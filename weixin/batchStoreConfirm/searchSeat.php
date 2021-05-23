<?php
/**
 * User: Elina
 * Date: 2018/11/27
*/
session_start();
error_reporting(E_ALL & ~E_NOTICE);

include_once('../auth.php');
include_once('../configure.php');
include_once('../log.php');
include_once('../jsapi.php');

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
  <title>查询库号垛号</title>
  <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <link rel="stylesheet" type="text/css" href="../static/css/mobileSelect.css">
  <style type="text/css">
  .wrapper{padding:0 5% 50px;}
  .head { position:fixed; top: 0; right: 0; bottom: 0; left: 0; width: 100%; height: 250px; background:#fff; z-index:999;}
  .selectiongroup { display: block; margin-top: 40px; margin-left:30px;}
  header{height: 40px;font-size:14px;width: 100%;padding-top: 10px; position: fixed; top: 0;}
  table{font-size:13px;}
  table tr td:nth-child(3){width: 150px;}
  table tr td:nth-child(2){width: 150px;}
  article { position: absolute; top: 260px; height: 100%;}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 40px;width:100%;margin-bottom:5px;}
  footer button{width:45%; height:100%; margin: 5px auto; font-size:16px; background-color:teal; color:white;}
  p{width: 80%; text-align: left; margin: 10px auto;}
  p label{width: 80px;vertical-align: middle; display: inline-block; font-size:15px;}
  p input, #forshort, #building, #floor, #ptype, #pname{font-size: 15px; padding:3px 10px; border: solid 1px lightgray; vertical-align: middle;border-radius: 3px;}
  #forshort, #building, #floor, #ptype, #pname{display: inline-block;}
  /* #search{border-radius:3px; border: solid 1px lightgray; padding: 5px 10px; background-color:teal; color:white;} */
  #base{margin-bottom: 15px}
  #base span{display: inline-block; text-align: right; margin-right: 20px;}
  #base span:last-child{margin-right: 0;}
  #back{display: block; position: absolute;padding-left: 8px;}
  #noData{position: absolute; top: 260px; text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; width:90%;}
  .selections { margin-top:30px}
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="head">
    <header>
      <span id="back"> <返回 </span>
      <div class="selections">
      <p>
        <label>项目：</label>
        <span id="forshort">请选择</span>
      </p>
      <p>
         <label>楼栋：</label>
         <span id="building">请选择</span>
      </p>
      <p>
         <label>楼层：</label>
         <span id="floor">请选择</span>
      </p>
      <p>
         <label>构件类型：</label>
         <span id="ptype">请选择</span>
      </p>
      <p>
         <label>构件名：</label>
         <span id="pname">请选择</span>
      </p>
      <div>
    </header>
</div>
    <article></article>
    <div id="noData">未查到内容</div>
  </div>
  <footer>
    <button id="search">筛选查询</button>
    <button id="qrcode">扫码查询</button>
  </footer>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="../static/js/bootstrap-table.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script src="../static/js/mobileSelect.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var  serviceUrl='<?php $_SERVER['HTTP_HOST']?>',searchUrl='<?php $_SERVER['HTTP_HOST']?>',selectedRow=[];
    var regexpat = RegExp('^[A-Z]+[0-9]+$');
    var companySelector, buildnoSelector,floorSelector,TypeSelector,CnameSelector;
    var tradeId,BuildNo,FloorNo,TypeId,cname;
    var module = {
      doms: {
        table:$('table'),
        article: $('article')
      },

      init: function(){
      
          this.initServerUrl()
          this.initWechat()
          this.bind()

          $('#noData').hide()
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
          $('#qrcode').on('click',function(){
            _self.qrCodeScan()
          })
        });
      },

      qrCodeScan: function(){
        var _self = this
        wx.scanQRCode({
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            _self.search(result)
          }
        });
      },

      initServerUrl: function() {
        var _self = this
        $.getJSON("../project.json", function(json) {
           serviceUrl =  serviceUrl + json.commonUrl;
           searchUrl  =  searchUrl + json.batchstoreUrl; 
           console.log(serviceUrl)
           _self.getCompanyForShort()
        })
      },

      initCompanySel: function(list) {
        var _self = this
        companySelector = new MobileSelect({
          trigger: '#forshort',
          title: '项目',
          wheels: [{data:list}],
          keyMap: {
            id:'TradeId',
            value: 'Forshort',
          },
          callback:function(indexArr, data){
            tradeId = data[0].TradeId
            $('#building').text("请选择")
            _self.clearResult()
            _self.getBuildNo()
          }
        });

        $('#forshort').css('display','inline-block')
      },

      initBuildnoSel: function(list) {
        var _self = this
        if(buildnoSelector) {
            buildnoSelector.updateWheel(0,list);
            return;
        }
        buildnoSelector = new MobileSelect({
          trigger: '#building',
          title: '楼栋',
          wheels: [{data:list}],
          keyMap: {
            id:'BuildingNo',
            value: 'BuildingNo',
          },
          callback:function(indexArr, data){
            BuildNo = data[0].BuildingNo
            $('#floor').text("请选择")
            _self.clearResult()
            _self.getFloorNo()
          }
        });
        $('#building').css('display','inline-block')
      },

      initFoorSel: function(list) {
        var _self = this
        if(floorSelector) {
            floorSelector.updateWheel(0,list);
            return;
        }
        floorSelector = new MobileSelect({
          trigger: '#floor',
          title: '楼层',
          wheels: [{data:list}],
          keyMap: {
            id:'FloorNo',
            value: 'FloorNo',
          },
          callback:function(indexArr, data){
            FloorNo = data[0].FloorNo
            $('#ptype').text("请选择")
            _self.clearResult()
            _self.getType()
          }
        });
        $('#floor').css('display','inline-block')
      },

      initTypeSel: function(list) {
        var _self = this
        if(TypeSelector) {
            TypeSelector.updateWheel(0,list);
            return;
        }
        TypeSelector = new MobileSelect({
          trigger: '#ptype',
          title: '构件类型',
          wheels: [{data:list}],
          keyMap: {
            id:'TypeId',
            value: 'CmptType',
          },
          callback:function(indexArr, data){
            TypeId = data[0].TypeId
            $('#pname').text("请选择")
            _self.clearResult()
            _self.searchCName()
          }
        });
        $('#ptype').css('display','inline-block')
      },

      initCnameSel: function(list) {
        var _self = this
        if(CnameSelector) {
            CnameSelector.updateWheel(0,list);
            return;
        }
        CnameSelector = new MobileSelect({
          trigger: '#pname',
          title: '构件名称',
          wheels: [{data:list}],
          keyMap: {
            id:'Id',
            value: 'cName',
          },
          callback:function(indexArr, data){
            cname = data[0].cName
          }
        });
        $('#pname').css('display','inline-block')
      },

      getCompanyForShort: function() {
        var _self = this

        SERVICE.sendSHR(serviceUrl,{action:'getCompanyForShort'}, function(oData){
          if(oData && oData.result && oData.result.length>0){
            console.log('oData.result',oData.result)
            _self.initCompanySel(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到公司名称信息')
          }
        })
      },

      getBuildNo: function() {
        var _self = this

        SERVICE.sendSHR(serviceUrl,{action:'getCompanyBuilding',tradeId:tradeId}, function(oData){
          if(oData && oData.result && oData.result.length>0){
            console.log('oData.result',oData.result)
            _self.initBuildnoSel(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到楼栋信息')
          }
        })
      },

      getFloorNo: function() {
        var _self = this

        SERVICE.sendSHR(serviceUrl,{action:'getBuildingFloor', tradeId:tradeId, buildingNo:BuildNo}, function(oData){
            if(oData && oData.result && oData.result.length>0){
                console.log('oData.result',oData.result)
                _self.initFoorSel(oData.result)
            } else {
                POP.ShowAlert('出错了，未获取楼层信息')
            }
        })
      },

      getType: function() {
        var _self = this

        SERVICE.sendSHR(serviceUrl,{action: 'getCmptType', tradeId:tradeId, buildingNo:BuildNo, floorNo:FloorNo}, function(oData){
            if(oData && oData.result && oData.result.length>0){
                console.log('oData.result',oData.result)
                _self.initTypeSel(oData.result)
            } else {
                POP.ShowAlert('出错了，未获取构件类型信息')
            }
        })
      },

      searchCName: function() {
        var _self = this

        SERVICE.sendSHR(searchUrl,{action:'searchCName', tradeId:tradeId, buildNo:BuildNo, floorNo:FloorNo, typeId: TypeId}, function(oData){
            if(oData && oData.result && oData.result.length>0){
                console.log('oData.result',oData.result)
                _self.initCnameSel(oData.result)
            } else {
                POP.ShowAlert('未获取构件名称信息')
                CnameSelector.updateWheel(0,[]);
                cname = ''
                $('#pname').text("请选择")
            }
        })
      },

      search:function(name){
        var _self = this
        POP.ShowNotify("正在查询，请稍后...")
        SERVICE.sendSHR(searchUrl,{
          action:'getStackIdAndSeatByProduct',
          cName: name
        }, function(oData){
          POP.dialog.close()
          if(oData.result && oData.result.length>0){
            console.log(oData.result)
            _self.updateList(oData.result)
          } else {
            POP.ShowAlert("未查询到结果")
            _self.doms.article.hide()
            $('#noData').show()
          }
        })
      },

      clearResult: function() {
        var _self = this
        _self.doms.article.hide()
        // $('#noData').show()
      },

      updateList: function(list){
        var temp = []
        searchResult = list
        for(var i=0; i<list.length;i++){
          temp.push({
            StackId: list[i].StackId,
            SeatId: list[i].SeatId
          })
        }
        this.generateTableCell(temp)
        $('#noData').hide()
        this.doms.article.show()
      },
     
      retrieveOptionData:function(oSend,cb){
        var _self = this
        SERVICE.sendSHR(searchUrl,oSend, function(oData){
          if(oData.result && oData.result.length>0){
            _self[cb](oData.result)
          }
        })
      },

      generateTableCell: function(list){
        // console.log(list)
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="indexId">序号</th>' +
          '<th data-field="StackId">垛号</th><th data-field="SeatId">库号</th></tr></thead></table>')

        for(var i=0;i<list.length;i++){
          list[i].indexId = i+1
        }

        $('#table').bootstrapTable({
          data: list
        });
      },

      bind:function(){
        var _self = this
        $('#search').on('click',function(){
            if(cname){
                _self.search(cname)
            } else {
                POP.ShowAlert("请选择构件名称")
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
