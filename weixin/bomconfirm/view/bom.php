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
  $msg = '您无此功能权限，如有疑问，请联系信息部人员</br>电话：13775147477';
  header("Location:msg.php?result=$result&title=$title&msg=$msg&onscan=1");
}

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
  <title>布模拆模</title>
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
  #seat-save{display: none;border-radius:3px; border: solid 1px lightgray; padding: 5px 10px; background-color:teal; color:white;}
  #pop-move{position:fixed; display: none;width: 100%;height: 100%;left: 0;top: 0;background-color: white;overflow-y: auto;}
  .move-btn-set{height: 60px;padding-top: 20px;text-align: center;}
  .move-btn-set button{padding: 8px 20px;margin: 0 10px;border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
  #trolleyId{vertical-align: middle;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back">< 返回</span>
      <span>操作人：<span id="openid"></span></span>
    </header>
    <nav>
      <div class="criteria"><label>台车号</label><span id="trolleyId"></span></div>
    </nav>
    <article></article>
    <div id="noData">正在查询</div>
  </div>
  <footer>
    <button id="add">添加</button>
    <button id="bumu">布模</button>
    <button id="tuomu">脱模</button>
    <button id="delete">删除</button>
    <button id="qingmu">清模</button>
    <button id="chaimu">拆模</button>
  </footer>
  <script type="text/javascript" src="./resource/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="./resource/bootstrap-table.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var trolleyId,
        list = [],
        selectedRow = [],
        openid='<?php echo $_SESSION["openid"];?>', 
        serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/bomconfirm/controller/index.php'
    var module = {
      doms: {
        table:$('table'),
        article:$('article'),
        noData:$('#noData'),
        btnAdd:$('#add'),
        btnBumu:$('#bumu'),
        btnTuomu:$('#tuomu'),
        btnQingmu:$('#qingmu'),
        btnChaimu:$('#chaimu'),
        btnDel:$('#delete')
      },
      init: function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'apiauth',tag:1}, function(oData){
          if(oData.result == true){
            trolleyId = _self.getUrlParameter('trolleyId')
            if(!trolleyId) {
              POP.ShowAlert('台车号不存在，请确认后重试')
            } else {
              $('#trolleyId').html(trolleyId)
              _self.retrievePageData()
              _self.bind()
              _self.getUserName()
            }
          } else {
            POP.ShowAlert('您无此功能权限，如有疑问，请联系信息部人员，电话：13775147477')
          }
        })
      },
      getUserName:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getUserName'}, function(oData){
          if(oData.result && oData.result.length>0)
            $('#openid').html(oData.result[0].uName)
        })
      },
      retrievePageData:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getBomByTrolley',trolleyId:trolleyId}, function(oData){
          if(oData.result && oData.result.length>0){
            _self.updatePageData(oData.result)
          } else {
            _self.doms.noData.show().html('暂无数据')
            _self.doms.article.hide()
          }
        })
      },
      retrieveStatusName: function(status){
        var text = '未知'
        switch (status) {
          case 0: case '0': text = '初始'; break;
          case 1: case '1': text = '布模完成'; break;
          case 2: case '2': text = '脱模完成'; break;
          case 3: case '3': text = '清模完成'; break;
          case 4: case '4': text = '拆模完成'; break;
          default: text = '未知'
        }
        return text
      },
      updatePageData: function(data){
        var tablelist = [], i
        for(i=0;i<data.length;i++){
          tablelist.push({
            indexId:i+1,
            project:data[i].Forshort,
            bomId:data[i].BomId,
            module:data[i].MouldCat,
            num:data[i].MouldNo,
            id:data[i].Id,
            status:this.retrieveStatusName(data[i].Status)
          })
        }
        list = tablelist;
        this.generateTableCell()
        this.doms.noData.hide()
        this.doms.article.show()
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      generateTableCell: function(){
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目名称</th>' +
          '<th data-field="module">模具类别</th>' +
          '<th data-field="num">模具编号</th>' +
          '<th data-field="status">模具状态</th>' +
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
      },
      selectedRowCheck: function(type){
        if(selectedRow.length<1){
          POP.ShowAlert('请先选择指定模具')
          return false
        } else if (type !== 'delete'){
          return true
        }

        var ifAllowed = 1
        for(var i = 0;i<selectedRow.length;i++){
          if(list[selectedRow[i]].status !== '初始'){
            ifAllowed = 2
          }
        }

        if(ifAllowed == 2){
          POP.ShowAlert('该模具已使用')
          return false
        }
        return true
      },
      setMouldState: function(msg,actionName,msgSuccess,status){
        var _self = this
        POP.ShowConfirm(msg, '确定', '取消', function(){
          var oArray = [], oSend
          for(var i=0;i<selectedRow.length;i++){
            oArray.push({
              bomId:list[selectedRow[i]].bomId,
              Id:list[selectedRow[i]].id
            })
          }

          oSend = {
            action:actionName,
            mouldArray:JSON.stringify(oArray)
          }
          if(status){
            oSend.status = status
          }

          SERVICE.sendSHR(serviceUrl, oSend, function(oData){
            POP.ShowAlert(msgSuccess)
            selectedRow = []
            _self.retrievePageData()
          })
        })
      },
      bind:function(){
        var _self = this

        this.doms.btnAdd.on('click',function(){
          window.location.href = './add.php?trolleyId=' + trolleyId
        })

        this.doms.btnDel.on('click',function(){
          if(_self.selectedRowCheck('delete')){
            _self.setMouldState('请确认是否删除已选模具','deleteMouldTrolley','删除成功')
          }
        })

        this.doms.btnBumu.on('click',function(){
          if(_self.selectedRowCheck()){
            _self.setMouldState('请确认是否更新已选模具状态为布模','updateMouldStatus','布模成功',1)
          }
        })

        this.doms.btnTuomu.on('click',function(){
          if(_self.selectedRowCheck()){
            _self.setMouldState('请确认是否更新已选模具状态为脱模','updateMouldStatus','脱模成功',2)
          }
        })
        this.doms.btnQingmu.on('click',function(){
          if(_self.selectedRowCheck()){
            _self.setMouldState('请确认是否更新已选模具状态为清模','updateMouldStatus','清模成功',3)
          }
        })
        this.doms.btnChaimu.on('click',function(){
          if(_self.selectedRowCheck()){
            _self.setMouldState('请确认是否更新已选模具状态为拆模','updateMouldStatus','拆模成功',4)
          }
        })

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

