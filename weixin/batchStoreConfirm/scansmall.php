<?php
/* */
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
  <title>输入垛号</title>
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style type="text/css">
  header{font-size: 20px;padding:30px 10% 70px; text-shadow: 5px 0 7px rgba(0,0,0,.3);font-weight: 700;color: #5a5e66;}
  article{text-align: center;display: block;}
  #jump{font-size:14px; color: blue; text-decoration: underline;}
  button{border: solid 1px gray; display: inline-block; width: 70%;line-height: 45px; font-size:16px;
    border-radius: 3px; background-color: teal; color: white; margin-top: 50px;margin-bottom: 20px;}
  #searchSeat {margin-top:90px; margin-bottom:10px;}
  #searchProduct {margin-top:10px;}
  </style>
</head>
<body>
  <header>扫描垛牌</header>
  <article>
    <button id="scan">点击扫描</button>
    <div id="jump">若扫描不成功，请点击这里</div>

      <div class="btn-cfm">
          <button id="searchSeat">查询库存</button>
          <button id="searchProduct">查询构件</button>
      </div>

  </article>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var serviceUrl = '';
    var regexpat = RegExp('^[A-Z]+[0-9]+$');
    var module = {
      doms: {
        scan:$('#scan'),
        jump:$('#jump')
      },

      initServerUrl: function() {
        var _self = this
        $.getJSON("../project.json", function(json) {
           serviceUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.batchstoreUrl;
           console.log(serviceUrl)
        })
      },

      bind: function(list){
        var _self = this
        this.doms.jump.on('click',function(){
          window.location.href = './stackNo.php'
        })

          $('#searchSeat').on('click',function(){
              window.location.href = './searchSeat.php';
          })
              $('#searchProduct').on('click',function(){
                  window.location.href = './searchProduct.php';
              })
      },

      initWechat:function(){
        var _self = this;
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

      init: function(){
        var _self = this
        //SERVICE.sendSHR(serviceUrl,{action:'apiauth',tag:1}, function(oData){
        // if(oData.result == true){
              _self.initServerUrl()
              _self.bind()
              _self.initWechat()
        //  } else {
        //   POP.ShowAlert('您无此功能权限，如有疑问，请联系信息部人员，电话：15919701518')
        //  }
        //})
      },

      qrCodeScan: function(){

        var _self = this
        console.log(_self);
        wx.scanQRCode({
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            // POP.ShowAlert('扫描结果：' + result + ',openid:' + '<?php echo $_SESSION["openid"];?>')
            _self.addStackCode(result)
          }
        });
      },

      addStackCode: function(stackNo){
        if(regexpat.test(stackNo) == false) {
            POP.ShowAlert('垛号：'+  stackNo + ' 格式不正确' )
        } else {
          SERVICE.sendSHR(serviceUrl,{action:'getListByStackId',stackNo:stackNo}, function(oData){
            window.location.href = './stacksmall.php?stackId=' + oData.result.stackInfo.StackNo + '&stackNo=' +oData.result.stackInfo.StackNo + '&seatId=' + oData.result.stackInfo.SeatId
            window.event.returnValue = false;
          })
        }
      }




    }

    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>
