<?php
/**
 * User: Elina
 * Date: 2018/11/26
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
  <title>输入垛号</title>
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  header{font-size: 20px;padding:30px 10% 70px; text-shadow: 5px 0 7px rgba(0,0,0,.3);font-weight: 700;color: #5a5e66;}
  article{text-align: center;display: block;}
  #num{border: solid 1px gray; display: inline-block; width: 80%;line-height: 45px; border-radius: 3px;padding:0 10px;font-size:14px;}
  #search{border: solid 1px gray; display: inline-block; width: 70%;line-height: 45px; 
    border-radius: 3px; background-color: teal; color: white; letter-spacing: 10px; margin-top: 50px;font-size:16px;}
  </style>
</head>
<body>
  <header>请输入垛号</header>
  <article>
    <input type="text" name="num" id="num" placeholder="请输入垛号...">
    <button id='search'>保存</button>
  </article>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackId, serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/inventoryConfirm/controller/index.php'
    var module = {
      doms: {
        num:$('#num'),
        btn:$('#search')
      },
      init: function(){
        this.bind()
      },
      addStackCode: function(stackNo){
        SERVICE.sendSHR(serviceUrl,{action:'addStackCode',stackNo:stackNo,openId:'<?php echo $_SESSION["openid"];?>'}, function(oData){
          window.location.href = './stack.php?stackId=' + oData.result + '&stackNo=' + stackNo
        })
      },
      bind: function(list){
        var _self = this
        this.doms.btn.on('click',function(){
          var num = _self.doms.num.val()
          if(!num){
            POP.ShowAlert('请输入垛号')
          } else {
            _self.addStackCode(num)
          }
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>



