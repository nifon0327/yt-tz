<?php
/**
 * User: Elina
 * Date: 2018/11/27
 */
session_start();
error_reporting(E_ALL & ~E_NOTICE);

include_once('../../auth.php');
include_once('../../configure.php');
include_once('../../log.php');
include_once('../../jsapi.php');
include '../config/dbconnect.php';

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

$auth = new auth();
$query = "select u.Number, s.name from usertable u 
  left join wx_token w on w.openid = u.openid 
  left join staffmain s on u.number = s.number where w.openid='$_SESSION[openid]'";
$cursor = mysql_query($query);

if(true){
  $user_id = $row[0];
  $user_name = $row[1];
  $_SESSION['user_id'] = $user_id;
  $_SESSION['user_name'] = $user_name;
  $js_sdk = new jsapi();
  $sign = $js_sdk->get_sign();
  $hour = date('H');
  $grace = $hour<6?'凌晨':($hour<11?'早上':($hour<13?'中午':($hour<17?'下午':'晚上')));
}else{
  //usertable 的openid 绑定系统及微信用户，如没绑定，则转到信息页
  $result = 0;
  $title = '权限缺失';
  $msg = '您无此功能权限，如有疑问，请联系信息部人员</br>电话：15919701518';
  header("Location:msg.php?result=$result&title=$title&msg=$msg&onscan=1");
}
$db = new DbConnect();
$userArray = $db->get_user_name($_GET["stackId"],$_SESSION["openid"]);
$_SESSION["creator"] = $userArray["creator"];
$_SESSION["doubleCheckUser"] = $userArray["doubleCheckUser"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black"> 
  <title>盘点</title>
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 150px;}
  .wrapper-pop{padding-top: 40px;}
  header{line-height: 80px;text-align: right; font-size:14px}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 90px; border-top: solid 1px gray; width:100%; padding-top: 10px;}
  footer button{width:25%; margin: 5px auto; border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
  footer button.disabled{background-color: #A9A9A9;}
  #exception{text-decoration: underline;color:blue;}
  #back{display: block;position:absolute;}
  .fixed-table-container tbody .selected td { background-color: darkseagreen;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  tr.abnormal{background-color: gold !important}
  #seat{display:none;}
  #seat-option{display: none;}
  #seat-save,#note-save{display: none;border-radius:3px; border: solid 1px lightgray; padding: 5px 10px; background-color:teal; color:white;}
  #note-save{display: inline-block;}
  #pop-move{position:fixed; display: none;width: 100%;height: 100%;left: 0;top: 0;background-color: white;overflow-y: auto;}
  .move-btn-set{height: 60px;padding-top: 20px;text-align: center;}
  .move-btn-set button{padding: 8px 20px;margin: 0 10px;border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
  #note-ctn{margin-top:10px;}
  #note-ctn input{border: solid 1px lightgray; border-radius: 3px; padding: 5px 20px;margin-left: 4px; font-size:12px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back">< 返回</span>
      <span id="exception">点击查看所有异常构件</span>
    </header>
    <nav>
      <div class="criteria"><label>垛号</label><span id="stackId"></span></div>
      <div class="criteria"><label>盘点人</label><span id="pandian"></span></div>
      <div class="criteria"><label>复盘人</label><span id="fupan"></span></div>
      <div class="criteria">
        <label>库位号</label>
        <span id="seat"></span>
        <div class="select-inline select-area" type='stack' id="seat-option">
          <span class="select-value"></span>
          <select id="seat-selector"></select>
        </div>
        <button id="seat-save">保存</button>
      </div>
      <div class="criteria" id="note-ctn">
        <label>备注</label>
        <input type="text" name="" placeholder="请输入备注" id="note">
        <button id="note-save">保存</button>
      </div>
    </nav>
    <article></article>
    <div id="noData">正在查询</div>
  </div>
  <footer>
    <button id="scan" class="disabled" disabled="disabled">扫码添加</button>
    <button id="add" class="disabled" disabled="disabled">添加</button>
    <button id="btn_pandian" class="disabled" disabled="disabled">盘点</button>
    <button id="btn_fupan" class="disabled" disabled="disabled">复盘</button>
    <button id="delete" class="disabled" disabled="disabled">删除</button>
    <button id="btn-move" class="disabled" disabled="disabled">移垛</button>
  </footer>
  <div id="pop-move">
    <div class="wrapper wrapper-pop">
      <nav>
        <div class="criteria">
          <label>库位号</label>
          <span id="seat-move"></span>
          <div class="select-inline select-area" type='seat' id="seat-option-move">
            <span class="select-value"></span>
            <select id="seat-selector-move"></select>
          </div>
        </div>
        <div class="criteria">
          <label>垛号</label>
          <span id="stack-move"></span>
          <div class="select-inline select-area" type='stack-move' id="stack-option-move">
            <span class="select-value"></span>
            <select id="stack-selector-move"></select>
          </div>
        </div>
      </nav>
      <div class="move-btn-set">
        <button id="btn-move-confirm">确认移垛</button>
        <button id="btn-move-cancel">取消</button>
      </div>
      <div id="move-table"></div>
    </div>
  </div>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="./resource/bootstrap-table.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackId,
        stackNo,
        currentSeatId,
        seatSelectedId,
        seatSelectedIdMove,
        stackIdSelected,
        selectedRow = [],
        list = [],
        openid='<?php echo $_SESSION["openid"];?>', 
        serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/pandianconfirm/controller/index.php',
        pandianResult = {}
    var module = {
      doms: {
        table:$('table'),
        stackNoSelector:$('#stack-selector'),
        article:$('article'),
        noData:$('#noData'),
        btnAdd:$('#add'),
        btnScan:$('#scan'),
        btnPandian:$('#btn_pandian'),
        btnFupan:$('#btn_fupan'),
        btnDel:$('#delete'),
        seatSelector:$('#seat-selector'),
        seatOption:$('#seat-option'),
        btnSeatSave:$('#seat-save'),
        btnNoteSave:$('#note-save'),
        btnMove:$('#btn-move'),
        popMove:$('#pop-move'),
        seatSelectorMove:$('#seat-selector-move'),
        seatOptionMove:$('#seat-option-move'),
        stackSelectorMove:$('#stack-selector-move'),
        stackOptionMove:$('#stack-option-move'),
        moveTable:$('#move-table'),
        btnMoveCancel:$('#btn-move-cancel'),
        btnMoveConfirm:$('#btn-move-confirm')
      },
      init: function(){
        stackNo = this.getUrlParameter('stackNo')
        stackId = this.getUrlParameter('stackId')
        if(!stackNo) {
          POP.ShowAlert('垛号不存在，请确认后重试')
        } else {
          $('#stackId').html(stackNo)
          this.retrieveStackSeat()
          this.retrieveStackNote()
          this.retrieveStackData()
          this.initWechat()
          this.bind()
        }
      },
      setupFupanBtns:function(){
        this.doms.btnFupan.removeClass('disabled').removeAttr('disabled')
        // this.doms.btnMove.removeClass('disabled').removeAttr('disabled')

        // 盘点人功能呢
        this.doms.btnScan.removeClass('disabled').removeAttr('disabled')
        this.doms.btnAdd.removeClass('disabled').removeAttr('disabled')
        this.doms.btnPandian.removeClass('disabled').removeAttr('disabled')
        this.doms.btnDel.removeClass('disabled').removeAttr('disabled')
      },
      setupPandianBtns:function(){
        this.doms.btnScan.removeClass('disabled').removeAttr('disabled')
        this.doms.btnAdd.removeClass('disabled').removeAttr('disabled')
        this.doms.btnPandian.removeClass('disabled').removeAttr('disabled')
        this.doms.btnDel.removeClass('disabled').removeAttr('disabled')
        // this.doms.btnMove.removeClass('disabled').removeAttr('disabled')

        // 复盘人功能
        this.doms.btnFupan.removeClass('disabled').removeAttr('disabled')
      },
      retrieveStackOptions:function(seatId){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getStackBySeat',seatId:seatId}, function(oData){
          _self.generateStackSelector(oData.result)
        })
      },
      triggerSelectionClick: function(type,param){
        console.log('triggerSelectionClick',type)
        if (type === 'stack'){
          seatSelectedId = param
        } else if (type === 'seat'){
          seatSelectedIdMove = param
          this.retrieveStackOptions(param)
        } else if(type === 'stack-move'){
          stackIdSelected = param
        }
      },
      generateStackSelector: function(stacklist){
        var _html = '<option param="">请选择</option>', _self = this;
        if(stacklist && stacklist.length>0){
          for(var i=0;i<stacklist.length;i++){
            _html += '<option param="' + stacklist[i].ID +'">' + stacklist[i].StackNo + '</option>'
          }
        }
        this.doms.stackSelectorMove.empty().append(_html)
        this.doms.stackOptionMove.css('display','inline-block')
        this.initSelection()
      },
      initSelection: function(){
        var _self = this
        $(".select-area .select-value").each(function(){
          if( $(this).next("select").find("option:selected").length != 0 ){
            $(this).text( $(this).next("select").find("option:selected").text() );
          }
        });
        $(".select-area select").off('change').change(function(){
          var value = $(this).find("option:selected").text();
          $(this).parent(".select-area").find(".select-value").text(value);
          _self.triggerSelectionClick($(this).parent(".select-area").attr('type'),$(this).find("option:selected").attr('param'))
        });
      },
      saveStackChange: function(){
        var _self = this
        var oArray = []
        for(var i=0;i<selectedRow.length;i++){
          oArray.push({productId:list[selectedRow[i]].index})
        }

        SERVICE.sendSHR(serviceUrl,{action:'moveProduct',originStackId:stackId,stackId:stackIdSelected,productIds:JSON.stringify(oArray)}, function(oData){
            POP.ShowAlert('操作成功','确定',function(){
              _self.retrieveStackData()
              _self.doms.popMove.fadeOut()
            })
        })
      },
      updateSeatId:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'updateStackSeatId',seatId:seatSelectedId,stackId:stackId}, function(oData){
          POP.ShowAlert('保存成功', '确定', function(){
            // _self.doms.seatOption.hide()
            // _self.doms.btnSeatSave.hide()
            // $('#seat').html(seatSelectedId).show()
          })
        })
      },
      generateSeatSelector: function(seatlist,selectedSeatId){
        var _html = '<option param="">请选择</option>', _self = this;
        if(seatlist && seatlist.length>0){
          for(var i=0;i<seatlist.length;i++){
            if(seatlist[i].seatId === selectedSeatId){
              _html += '<option selected="true" param="' + seatlist[i].seatId +'">' + seatlist[i].seatId + '</option>'
            } else {
              _html += '<option param="' + seatlist[i].seatId +'">' + seatlist[i].seatId + '</option>'
            }
          }
        }
        
        this.doms.seatSelector.empty().append(_html)
        this.doms.seatSelectorMove.empty().append(_html)
        this.doms.seatOption.css('display','inline-block')
        this.doms.seatOptionMove.css('display','inline-block')
        this.initSelection()
        this.doms.btnSeatSave.show().off('click').on('click',function(){
          if(!seatSelectedId){
            POP.ShowAlert('请先选择库位')
          } else {
            _self.updateSeatId()
          }
        })
      },
      retrieveSeatList:function(seatId){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getStackSeatList'}, function(oData){
          _self.generateSeatSelector(oData.result,seatId)
        })
      },
      retrieveStackNote:function(){
        SERVICE.sendSHR(serviceUrl,{action:'getStackDesc',stackId:stackId}, function(oData){
          $('#note').val(oData.result)
        })
      },
      retrieveStackSeat:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getStackSeat',stackId:stackId}, function(oData){
          currentSeatId = oData.result
          _self.retrieveSeatList(oData.result)
        })
      },
      retrieveStackData:function(){
        var _self = this
        //console.log(serviceUrl)
        SERVICE.sendSHR(serviceUrl,{action:'getStackInfo',stackId:stackId}, function(oData){
          console.log(oData);
          if(oData.result && oData.result.length>0){
            _self.updateStackData(oData.result)
          } else {
            $('#pandian').html('<?php echo $_SESSION["creator"]?>')
            _self.setupPandianBtns()
            _self.doms.noData.show().html('暂无数据')
            _self.doms.article.hide()
          }
        })
      },
      retrievePandianName: function(status){
        var text = '未盘'
        if(!!status && status == 1){
          text = '已盘'
        } else if(!!status && status == 2){
          text = '复盘'
        }
        return text
      },
      retrieveStatusName: function(status){
        var text = '未知'
        if(!!status && status == 1){
          text = '正常'
        } else if(!!status && status == 2){
          text = '异常'
        } else if(!!status && status == 3){
          text = '系统';
        }
        return text
      },
      updateStackData: function(data){
        if(!data[0].Creator || (data[0].Creator && data[0].Creator == openid)) {
          this.setupPandianBtns()
        } else if(!data[0].DoubleCheckUser || (data[0].DoubleCheckUser && data[0].DoubleCheckUser == openid)){
          this.setupFupanBtns()
        }
        // $('#pandian').html(data[0].Creator)
        // $('#fupan').html(data[0].DoubleCheckUser)
        $('#pandian').html('<?php echo $_SESSION["creator"]?>')
        $('#fupan').html('<?php echo $_SESSION["doubleCheckUser"]?>')
        var temp = [], i
        for(i=0;i<data.length;i++){
          temp.push({
            indexId:i+1,
            index:data[i].ProductId,
            ProductId:data[i].ProductId,
            project:data[i].Forshort,
            name:data[i].cName,
            stackstate:this.retrievePandianName(data[i].Status),
            status:this.retrieveStatusName(data[i].Result),
            inventoryNum:data[i].inventoryNum,
            tstockqty:data[i].tstockqty,
            numShow:(data[i].tstockqty?data[i].tstockqty:'0') + '/' + (data[i].inventoryNum?data[i].inventoryNum:'0')
          })
        }
        list = temp;
        this.generateTableCell()
        this.doms.noData.hide()
        this.doms.article.show()
        //POP.ShowAlert(serviceUrl)
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      generateMoveTable: function(movelist){
        this.doms.moveTable.empty().append('<table id="table-move" class="table bootstrap-table table-striped">' +
          '<thead><tr>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目</th>' +
          '<th data-field="name">构件编号</th>' +
          '<th data-field="stackstate">盘点状态</th>' +
          '<th data-field="status">状态</th>' +
          '<th data-field="num">实物数/<br/>账面数</th>' +
          '</tr></thead></table>')

        $('#table-move').bootstrapTable({
          data: movelist
        });

        $('table td').each(function(){
          var _el = $(this)
          if(_el.text() === '异常')
            _el.parent().addClass('abnormal')
        })
      },
      generateTableCell: function(){
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目</th>' +
          '<th data-field="name">构件编号</th>' +
          '<th data-field="stackstate">盘点状态</th>' +
          '<th data-field="status">状态</th>' +
          '<th data-field="numShow">实物数/<br/>账面数</th>' +
          '</tr></thead></table>')

        // for(var i=0;i<list.length;i++){
        //   list[i].num = pandianResult && pandianResult[list[i].ProductId] ? pandianResult[list[i].ProductId] : ''
        // }
         console.log(list);
        $('#table').bootstrapTable({
          data: list
         
        });

        Array.prototype.remove = function(val) { 
          var index = this.indexOf(val); 
          if (index > -1) { 
          this.splice(index, 1); 
          }
        };

        $('#table').off('check.bs.table').on('check.bs.table', function (e, row, $el) {
          selectedRow.push($el.closest('tr').data('index'))
        });
        $('#table').off('uncheck.bs.table').on('uncheck.bs.table', function (e, row, $el) {
          selectedRow.remove($el.closest('tr').data('index'))
        });

        $($('input[name="btSelectAll"]')[0]).off('change').change(function(){
          if($('input[name="btSelectAll"]')[0].checked){
            selectedRow = []
            var i;
            for(i=0;i<list.length;i++){
              selectedRow.push(i)
            }
          } else {
            selectedRow = []
          }
        })

        $('table td').each(function(){
          var _el = $(this)
          if(_el.text() === '异常')
            _el.parent().addClass('abnormal')
        })
      },
      selectedRowCheck: function(){
        if(selectedRow.length<1){
          POP.ShowAlert('请先选择指定构件')
          return false
        } else {
          return true
        }
      },
      addProductCode: function(ProductId){
        var _self = this, oArray = [{productId:ProductId}]
        SERVICE.sendSHR(serviceUrl,{action:'addProductsToStack',stackId:stackId,productIds:JSON.stringify(oArray)}, function(oData){
          POP.ShowAlert('添加成功','确定',function(){
            _self.retrieveStackData()
          })
        })
      },
       addProductByName: function(productname){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'scanProductToStack',stackId:stackId,productName:productname}, function(oData){
          POP.ShowAlert('添加成功','确定',function(){
            _self.retrieveStackData()
          })
        })
      },
      qrCodeScan: function(){
        var _self = this
        wx.scanQRCode({
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            _self.addProductByName(result)
          }
        });
      },
      initWechat:function(){
        var _self = this
        wx.config({
            debug: false,
            appId: '<?php echo $sign["appId"];?>',
            timestamp: '<?php echo $sign["timestamp"];?>',
            nonceStr: '<?php echo $sign["nonceStr"];?>',
            signature: '<?php echo $sign["signature"];?>',
            jsApiList: ['scanQRCode']
        });

        wx.ready(function(){
          _self.doms.btnScan.on('click',function(){
            _self.qrCodeScan()
          })
        });
      },
      dealWithPandianData: function(list){
        if(!list && !list.length) return false

        pandianResult = {}
        for(var i=0; i<list.length;i++){
          pandianResult[list[i].productId] = '' + list[i].tstockqty + '/' + list[i].inventoryNum
        }
      },
      bind:function(){
        var _self = this

        $('#exception').on('click',function(){
          window.location.href='./exception.php?stackId=' + stackId + '&stackNo=' + stackNo
        })

        this.doms.btnAdd.on('click',function(){
          window.location.href = './add.php?stackId=' + stackId + '&stackNo=' + stackNo
        })

        this.doms.btnDel.on('click',function(){
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否删除已选构件', '确定', '取消', function(){
              var oArray = []
              
              for(var i=0;i<selectedRow.length;i++){
				        if(list[selectedRow[i]].stackstate == "未盘") {
                  console.log(list[selectedRow[i]])
					        oArray.push({productId:list[selectedRow[i]].index})
				        }
				        else
				        {
					          POP.ShowAlert("请只选择未盘点构件")
					          return
				        }
              }

              SERVICE.sendSHR(serviceUrl,{action:'removeProduct',stackId:stackId,productIds:JSON.stringify(oArray)}, function(oData){
                POP.ShowAlert('删除成功')
                selectedRow = []
                _self.retrieveStackData()
              })
            })
          }
        })

        this.doms.btnNoteSave.on('click',function(){
          var desc = $('#note').val()
          if(note){
            SERVICE.sendSHR(serviceUrl,{action:'updateStackDesc',desc:desc,stackId:stackId}, function(oData){
              POP.ShowAlert('保存成功')
            })
          } else {
            POP.ShowAlert('请输入备注')
          }
        })

        this.doms.btnPandian.on('click',function(){
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否盘点已选构件', '确定', '取消', function(){
              var oArray = []
              for(var i=0;i<selectedRow.length;i++){
                oArray.push({productId:list[selectedRow[i]].index})
              }
              console.log('oArray',oArray)

              SERVICE.sendSHR(serviceUrl,{action:'checkProductResult',stackId:stackId,productIds:JSON.stringify(oArray)}, function(oData){
                POP.ShowAlert('盘点成功')
                selectedRow = []
                if(oData.result && oData.result.length)
                  _self.dealWithPandianData(oData.result)
                _self.retrieveStackData()
              })
            })
          }
        })

        this.doms.btnFupan.on('click',function(){
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否复盘已选构件', '确定', '取消', function(){
              var oArray = []
              for(var i=0;i<selectedRow.length;i++){
                oArray.push({productId:list[selectedRow[i]].index})
              }
              console.log('oArray',oArray)

              SERVICE.sendSHR(serviceUrl,{action:'doubleCheck',stackId:stackId,productIds:JSON.stringify(oArray)}, function(oData){
                POP.ShowAlert('复盘成功')
                selectedRow = []
                if(oData.result && oData.result.length)
                  _self.dealWithPandianData(oData.result)
                _self.retrieveStackData()
              })
            })
          }
        })

        this.doms.btnMove.on('click',function(){
          if(_self.selectedRowCheck()){
            var movelist  = []
            for(var i=0;i<selectedRow.length;i++){
              movelist.push(list[selectedRow[i]])
            }
            console.log(movelist)
            _self.generateMoveTable(movelist)
            _self.retrieveStackOptions(seatSelectedId || currentSeatId)
            _self.doms.popMove.fadeIn()
          }
        })

        this.doms.btnMoveCancel.on('click',function(){
          _self.doms.popMove.fadeOut()
        })

        this.doms.btnMoveConfirm.on('click',function(){
          if(stackIdSelected){
            _self.saveStackChange()
          } else {
            POP.ShowAlert('请先选择目标垛')
          }
        })

        // this.doms.btnScan.on('click',function(){
        //   _self.addProductByName('13-26-YWQ-4L-2136')
        // })

        $('#back').on('click',function(){
          window.location.href = "javascript:history.go(-1)"; 
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>
