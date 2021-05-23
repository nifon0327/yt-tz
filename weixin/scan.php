<?php

// session_start();

// include_once('auth.php');

// include_once('configure.php');

// include_once('log.php');

// include_once('jsapi.php');

// $auth = new auth();

// $query = "select s.name from wx_token w 
	// left join usertable u on w.openid = u.openid 
	// left join staffmain s on u.number = s.number where w.openid='$_SESSION[openid]'";

// log::e($query, 'xxx');
	
// $cursor = mysql_query($query);

// $row = mysql_fetch_row($cursor);

// // echo $row[0];

// $js_sdk = new jsapi();

// $sign = $js_sdk->get_sign();

//获取构件



?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<title>扫码测试</title>
<link rel="stylesheet" type="text/css" href="http://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css" >
<script src="../public/js/jquery.min.js" ></script>
<style>
	.weui-form-preview__bd{
		display:none
	}
</style>
</head>
<body>
	<!-- 
	<p>
		<img src='<?php echo $_SESSION['headimgurl'];?>' />你好，<b><?php echo $_SESSION['nickname'];?></b>；3秒后启动扫码
	<p/>
	-->
	<div class="weui-cells">
		<a class="weui-cell weui-cell_access" href="javascript:;" onclick="open_detail(this)">
			<div class="weui-cell__bd">
				<p>cell standard</p>
			</div>
			<div class="weui-cell__ft">说明文字</div>
		</a>
		<div class="weui-form-preview__bd">
			<div class="weui-form-preview__item">
				<label class="weui-form-preview__label">商品</label>
				<span class="weui-form-preview__value">电动打蛋机</span>
			</div>
			<div class="weui-form-preview__item">
				<label class="weui-form-preview__label">标题标题</label>
				<span class="weui-form-preview__value">名字名字名字</span>
			</div>
			<div class="weui-form-preview__item">
				<label class="weui-form-preview__label">标题标题</label>
				<span class="weui-form-preview__value">很长很长的名字很长很长的名字很长很长的名字很长很长的名字很长很长的名字</span>
			</div>
		</div>
		<a class="weui-cell weui-cell_access" href="javascript:;" onclick="open_detail(this)">
			<div class="weui-cell__bd">
				<p>cell standard</p>
			</div>
			<div class="weui-cell__ft">说明文字</div>
		</a>

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
		// setTimeout(function(){
			// wx.scanQRCode({
				// needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
				// scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
				// success: function (res) {
					// var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
					// alert('您扫出来的东西是：' + result);
				// }
			// });
		// }, 3000);		
	});
	function open_detail(host){
		// alert($);
		// alert($(host).html());
		$(host).next().toggle();
	}
</script>