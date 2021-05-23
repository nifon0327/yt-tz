<?php
/**/
session_start();
error_reporting(E_ALL & ~E_NOTICE);

include_once('../auth.php');
include_once('../configure.php');
include_once('../log.php');
include_once('../jsapi.php');


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
// $db = new DbConnect();
// $userArray = $db->get_user_name($_GET["stackId"],$_SESSION["openid"]);
// $_SESSION["creator"] = $userArray["creator"];
// $_SESSION["doubleCheckUser"] = $userArray["doubleCheckUser"];

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
  <title>批量入库</title>
  <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 150px;}
  .wrapper-pop{padding-top: 40px;}
  header{line-height: 80px;text-align: right; font-size:14px; height: 80px;}
  #operator{display: block; text-align: right;}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 125px; border-top: solid 1px gray; width:100%; padding-top: 10px;}
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
  #apop-move{position:fixed; display: none;width: 100%;height: 100%;left: 0;top: 0;background-color: white;overflow-y: auto;}
  .move-btn-set{height: 60px;padding-top: 20px;text-align: center;}
  .move-btn-set button{padding: 8px 20px;margin: 0 10px;border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
  #note-ctn{margin-top:10px;}
  #note-ctn input{border: solid 1px lightgray; border-radius: 3px; padding: 5px 20px;margin-left: 4px; font-size:12px;}
  #noData{position: absolute; top: 140px; text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; width:90%;}
  #newseat {font-size: 14px; padding: 3px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back">< 返回</span>
    </header>
    <div class="baseinfo">
      <span id="operator"></span>
      <span id="stackno"></span>
      <span id="seatid"></span>
    </div>
    <article></article>
    <div id="noData">正在查询构件信息</div>
  </div>
  <footer>
    <button id="scan" class="disabled" disabled="disabled">扫码添加</button>
    <button id="nameadd" class="disabled" disabled="disabled">构件名添加</button>
    <button id="addnew" class="disabled" disabled="disabled">添加</button>
    <button id="delete" class="disabled" disabled="disabled">删除</button>
    <button id="seatconfirm" class="disabled" disabled="disabled">入库确定</button>
    <button id="seatchose" class="disabled" disabled="disabled">库位选择</button>
    <button id="stackmove" class="disabled" disabled="disabled">移垛</button>
    <button id="rollback" class="disabled" disabled="disabled">入库回退</button>
  </footer>
  <div id="pop-move">
  <div class="wrapper wrapper-pop">
  <label>入库编号</label>
  <input id="storageNO" type="text" name="storageNO" >

  <div class="move-btn-set">
        <button id="btn-addseat-confirm">确认入库</button>
        <button id="btn-addseat-cancel">取消</button>
  </div>
  </div>
  </div>

  <div id="apop-move">
  <div class="wrapper wrapper-pop">
  <label>入库编号</label>
  <input id="newstorageNO" type="text" name="storageNO" >
  <br><br>
  <label>库位选择</label>
  <div class="select-inline " style="width:60%" type='seat'>
        <select id="newseat" style="width: 72%" > </select>
  </div>
  <div class="move-btn-set">
        <button id="n-btn-addseat-confirm">确认入库</button>
        <button id="n-btn-addseat-cancel">取消</button>
  </div>
  </div>
  </div>

  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
  <script type="text/javascript" src="../static/js/bootstrap-table.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackNo, stackId, seatId, serviceUrl ='', batchStoreUrl='', list=[], selectedRow = [], isallAdded=false;

    var module = {
      doms: {
        scan: $('#scan'),
        addnew: $('#addnew'),
        delete: $('#delete'),
        seatconfirm: $('#seatconfirm'),
        seatchose: $('#seatchose'),
        stackmove: $('#stackmove'),
        article: $('article'),
        seatpop: $('#pop-move'),
        seatpopnew: $('#apop-move'),
        storageNO: $('#storageNO'),
        newseat: $('#newseat'),
        newstorageNO: $('#newstorageNO'),
        btnAddseatConfirm: $('#btn-addseat-confirm'),
        btnAddseatCancel: $('#btn-addseat-cancel'),
        nbtnAddseatConfirm: $('#n-btn-addseat-confirm'),
        nbtnAddseatCancel: $('#n-btn-addseat-cancel'),
        nodata: $('#noData'),
        rollback: $('#rollback'),
        nameadd: $('#nameadd')
      },
      init: function(){
        stackNo = this.getUrlParameter('stackNo')
        stackId = this.getUrlParameter('stackId')
        seatId  = this.getUrlParameter('seatId')
        if(!stackNo) {
          POP.ShowAlert('垛号不存在，请确认后重试')
        } else {
          $('#stackno').html('垛号:' + stackNo)
          if(seatId) {
            $('#seatid').html('库位号:' + seatId)
          }
          console.log(stackNo, stackId, seatId)
          this.initServerUrl()
          this.initWechat()
          this.bind()
          this.enablebtns()
          this.doms.article.hide()
        }
      },
      enablebtns: function() {
        this.doms.scan.removeClass('disabled').removeAttr('disabled')
        this.doms.addnew.removeClass('disabled').removeAttr('disabled')
        this.doms.nameadd.removeClass('disabled').removeAttr('disabled')
        this.doms.delete.removeClass('disabled').removeAttr('disabled')
        this.doms.seatconfirm.removeClass('disabled').removeAttr('disabled')
        this.doms.seatchose.removeClass('disabled').removeAttr('disabled')
        this.doms.stackmove.removeClass('disabled').removeAttr('disabled')
        this.doms.rollback.removeClass('disabled').removeAttr('disabled')

      },
      getUserName: function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getOperatorName'}, function(oData){
          if(oData.result && oData.result.length>0)
            $('#operator').html(oData.result[0].uName)
        })
      },

      initServerUrl: function() {
        var _self = this
        $.getJSON("../project.json", function(json) {
           batchStoreUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.batchstoreUrl;
           serviceUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.commonUrl;
           console.log(serviceUrl)
           _self.getStackInfo()
           _self.getUserName()
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      getStackInfo: function() {
        var _self = this
        console.log(stackNo)
        SERVICE.sendSHR(batchStoreUrl,{action:'getListByStackId', stackNo: stackNo}, function(oData){
          if(oData.result) {
              console.log(oData.result.list)
              if(oData.result.list) {
                  _self.makeupListData(oData.result)
              } else {
                 _self.doms.article.empty()
                 _self.doms.nodata.empty().html("暂无构件信息")
                 _self.doms.nodata.show()
                 _self.doms.article.hide()
              }
            }
        }, function(oData) {
          POP.ShowAlert(oData.msg)
          _self.doms.article.empty()
          _self.doms.nodata.empty().html("暂无构件信息")
          _self.doms.nodata.show()
          _self.doms.article.hide()
        })
      },
      retrieveSeatName: function(status){
        var text = '待入库'
        if(!!status && status == 0){
          text = '待入库'
          isallAdded = false
        } else if(!!status && status == 1){
          text = '已入库'
          isallAdded = true
        } else if(!!status && status == 2){
          text = '出货'
          isallAdded = false
		    } else if (!!status && status == 3) {
			    text = '异常构件'
			    isallAdded = true
		    }
        return text
      },
      makeupListData: function(result) {
        var data=result.list
        var stackInfo = result.stackInfo

        var temp = [], i
        for(i=0;i<data.length;i++){
          temp.push({
            indexId:i+1,
            index:data[i].ProductId,
            ProductId:data[i].ProductId,
            project:data[i].ForShort,
            name:data[i].cName,
            status: this.retrieveSeatName(data[i].Status),
            stackno: stackInfo.StackNo,
            seatid: stackInfo.SeatId,
          })
        }

        list = temp
        this.generateTableCell()
        this.doms.nodata.hide()
        this.doms.article.show()
      },

      generateTableCell: function(){
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目</th>' +
          '<th data-field="name">构件编号</th>' +
          '<th data-field="status">状态</th>' +
          '<th data-field="stackno">垛号</th>' +
          '<th data-field="seatid">库位</th>' +
          '</tr></thead></table>')

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
      addNewProductByQR: function(cname) {
        var _self = this
        SERVICE.sendSHR(batchStoreUrl,{action:'addFinishedProductByProductName',stackId:stackId,cname:cname}, function(oData){
          _self.getStackInfo()
          POP.ShowAlert('添加成功')
        })
      },
      qrCodeScan: function(){
        var _self = this
        wx.scanQRCode({
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            _self.addNewProductByQR(result)
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
          _self.doms.scan.on('click',function(){
            _self.qrCodeScan()
          })
        });
      },
      getSeatInfo: function() {
        var _self = this

        SERVICE.sendSHR(batchStoreUrl,{action:'getSeats'}, function(oData){
          if(oData.result && oData.result.length>0)
            _self.generateSeatOptions(oData.result)
        })
      },
      generateSelectorOptions: function(selector,options){
        this.doms[selector].empty().append(options)
      },
      generateSeatOptions:function(list){
        var _html = '<option param="">请选择</option>'

        if(list){
          for(var i=0;i<list.length;i++){
              _html += '<option param="' + list[i].SeatId +'">' + list[i].SeatId + '</option>'
            }
        }
        this.generateSelectorOptions('newseat', _html)
      },
      bind:function(){
        var _self = this

        $('#seatchose').on('click',function(){
          if(isallAdded == true) {
            window.location.href='./warehouse.php?stackId=' + stackId + '&stackNo=' + stackNo
          } else {
            POP.ShowAlert("请确认所有构件都已入库")
            return
          }
        })

        // _self.doms.scan.on('click',function(){
        //     // _self.qrCodeScan()
        //     result = "8-13-YLT-01-1122"
        //     _self.addNewProductByQR(result)
        // })

        this.doms.seatconfirm.on('click', function(){
             if(_self.selectedRowCheck()) {
               if(list[selectedRow[0]].seatid && list[selectedRow[0]].seatid != "请选择" ){
                  _self.doms.seatpop.fadeIn()
               } else {
                  _self.doms.seatpopnew.fadeIn()
                  _self.getSeatInfo()
               }

             }
        })

        this.doms.btnAddseatConfirm.on('click', function() {
            var sseatno = _self.doms.storageNO.val()
            if(sseatno == '') {
              POP.ShowAlert("请填写入库编号")
            } else {
              if(_self.selectedRowCheck()){
                POP.ShowConfirm('请确认是否入库已选构件', '确定', '取消', function(){
                  var oArray = []

                  for(var i=0;i<selectedRow.length;i++){
                      console.log(list[selectedRow[i]])
                      oArray.push({productId:list[selectedRow[i]].ProductId, storageNO:sseatno, SeatId:list[selectedRow[i]].seatid})
                  }

                  console.log('oArray',oArray)

                  SERVICE.sendSHR(batchStoreUrl,{action:'storageInConfirm',products:JSON.stringify(oArray)}, function(oData){
                    selectedRow = []
                    POP.ShowConfirm('入库成功，是否打印入库单','打印','暂不打印',function(){
                       let printURL='/pages/stockprint/stockprint?storageno=' + sseatno + '&stackid=' + stackId + '&seatId=' + seatid;
                       wx.miniProgram.navigateTo({url: printURL});
                    },function(){
                        _self.doms.seatpop.fadeOut();
                        _self.getStackInfo();
                    })             
                   
                  
                  })
                })
              }
            }
        })

        this.doms.nbtnAddseatConfirm.on('click', function() {
            var sseatno = _self.doms.newstorageNO.val()
            var sseatid = _self.doms.newseat.val()
            console.log(sseatid)

            if(sseatid=="请选择"){
              POP.ShowAlert("请选择库位")
              return
            }

            if(sseatno == '') {
              POP.ShowAlert("请填写入库编号")
            } else {
              if(_self.selectedRowCheck()){
                POP.ShowConfirm('请确认是否入库已选构件', '确定', '取消', function(){
                  var oArray = []

                  for(var i=0;i<selectedRow.length;i++){
                      console.log(list[selectedRow[i]])
                      oArray.push({productId:list[selectedRow[i]].ProductId, storageNO:sseatno, SeatId:sseatid})
                  }

                  console.log('oArray',oArray)

                  SERVICE.sendSHR(batchStoreUrl,{action:'storageInConfirm',products:JSON.stringify(oArray)}, function(oData){
                    selectedRow = []
                    POP.ShowConfirm('入库成功，是否打印入库单','打印','暂不打印',function(){
                      let printURL='/pages/stockprint/stockprint?storageno=' + sseatno + '&stackid=' + stackId + '&seatId=' + sseatid
                      wx.miniProgram.navigateTo({url: printURL})
                    },function(){
                      _self.doms.seatpopnew.fadeOut()
                      _self.getStackInfo()
                    })                   
                  })
                })
              }
            }
        })

        this.doms.btnAddseatCancel.on('click',function(){
          _self.doms.seatpop.fadeOut()
        })

        this.doms.nbtnAddseatCancel.on('click',function(){
          _self.doms.seatpopnew.fadeOut()
        })


        this.doms.addnew.on('click', function(){
          window.location.href = './add.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatId
        })

        this.doms.nameadd.on('click', function() {
          window.location.href = './productName.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatId
        })

        this.doms.stackmove.on('click', function() {
            if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否移垛', '确定', '取消', function(){
              var oArray = []

              for(var i=0;i<selectedRow.length;i++){
                if(list[selectedRow[i]].status == "已入库") {
                  console.log(list[selectedRow[i]])
                  oArray.push({productId:list[selectedRow[i]].index})
                } else {
                  POP.ShowAlert("请只选择已入库的构件")
					        return
                }
              }
              console.log('oArray',oArray)

              var storage=window.localStorage;
              storage.setItem('products', JSON.stringify(oArray))

              window.location.href = './stackmove.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatId
              })
            }
        })


        $('#back').on('click',function(){
          // window.location.href = "javascript:history.go(-1)";
          window.location.href = "./scan.php";

        })

        $('#btnprint').on('click',function(){
           wx.miniProgram.navigateTo({url: '/pages/stockprint/stockprint?storageno=6310&stackid=LB11169&seatid=A064'})
        });

        this.doms.rollback.on('click', function() {
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否回退已选构件', '确定', '取消', function(){
              var oArray = []

              for(var i=0;i<selectedRow.length;i++){
				        if(list[selectedRow[i]].status == "已入库") {
                  console.log(list[selectedRow[i]])
					        oArray.push({productId:list[selectedRow[i]].index})
				        }
				        else
				        {
					          POP.ShowAlert("请只选择已入库的构件")
					          return
				        }
              }
              console.log('oArray',oArray)

              SERVICE.sendSHR(batchStoreUrl,{action:'cancelFinishedProducts',products:JSON.stringify(oArray)}, function(oData){
                POP.ShowAlert('回退成功')
                selectedRow = []
                _self.getStackInfo()
              })
            })
          }
        })


        this.doms.delete.on('click',function(){
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否删除已选构件', '确定', '取消', function(){
              var oArray = []

              for(var i=0;i<selectedRow.length;i++){
                  console.log(list[selectedRow[i]])
					        oArray.push({productId:list[selectedRow[i]].index})   
              }
              console.log('oArray',oArray)

              SERVICE.sendSHR(batchStoreUrl,{action:'deleteProductByIds',inventoryDataIds:JSON.stringify(oArray)}, function(oData){
                if(oData.result == true) {
                  POP.ShowAlert('删除成功')
                  selectedRow = []
                  _self.getStackInfo()
                } else {
                  POP.ShowAlert('删除失败')
                }
              })
            })
          }
        })


      } // bind function

    } // module
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>
