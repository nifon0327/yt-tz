<?php

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

	$msg = '您无此功能权限，如有疑问，请联系信息部人员</br>电话：13775147477';

	header("Location:msg.php?result=$result&title=$title&msg=$msg&onscan=1");

}



?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<title>研砼治筑运营平台</title>
<link rel="stylesheet" type="text/css" href="http://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css" >
<script src="../../public/js/jquery.min.js" ></script>

</head>
<body>
	<div class="weui-cells__title">
		<img width=56 height=56 style="border-radius:28px" src="<?php echo $_SESSION['headimgurl'];?>" />&nbsp;&nbsp;&nbsp;<?php echo $user_name ." (".$_SESSION['nickname'].")"; ?>
		<hr style="background-color: #d5d5d6; height:1px; border:none;">
		<p style="float: right; margin-top:10px"><?php echo $grace;?>好，欢迎使用扫码登记！</p>
	</div>
	<div class="weui-content">
        <div id="loadingToast">
			<div class="weui-mask_transparent"></div>
			<div class="weui-toast">
				<i class="weui-loading weui-icon_toast"></i>
				<p class="weui-toast__content">扫一扫启动中</p>
			</div>
		</div>
    </div>
	<form action='op.php' method='post' id=form >
		<input type=hidden name=p_name id=p_name />
	</form>
	<div class="weui-footer weui-footer_fixed-bottom">
		<p class="weui-footer__text">Copyright © 2017-<?php echo date("Y")?> 上海电气研砼建筑科技集团有限公司</p>
		<p class="weui-footer__text">沪ICP备16053942号</p>
	</div>

</body>
</html>
<script src=http://res.wx.qq.com/open/js/jweixin-1.2.0.js ></script>
<script>
    wx.config({
        debug: false,
        appId: '<?php echo $sign["appId"];?>',
        timestamp: '<?php echo $sign["timestamp"];?>',
        nonceStr: '<?php echo $sign["nonceStr"];?>',
        signature: '<?php echo $sign["signature"];?>',
        jsApiList: [
            'scanQRCode'
          ]
    });
	wx.ready(function(){
		wx.scanQRCode({
			needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
			scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
			success: function (res) {
				var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
				document.getElementById('p_name').value = result;
				document.getElementById('form').submit();
			}
		});
	});
</script>