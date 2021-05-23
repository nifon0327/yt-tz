<?php

	session_start();
	
	error_reporting(E_ALL & ~E_NOTICE);
	
	include_once('../jsapi.php');

	$js_sdk = new jsapi();

	$sign = $js_sdk->get_sign();
	
	$result = $_GET['result']==1?'success':'warn';
	
	$title = urldecode($_GET['title']);
	
	$msg = urldecode($_GET['msg']);
	
	if($_GET['onscan']){//是否有扫码按钮
		
		$scan = '<a class="weui-btn weui-btn_primary" id="btn-close" onclick="wx.closeWindow();">关闭</a>';
		
	}else{
		
		$scan = '<button class="weui-btn weui-btn_primary" id="btn-scan">继续扫码</button>
			<a class="weui-btn weui-btn_default" id="btn-close" onclick="wx.closeWindow();">关闭</a>';	
		
	}
	
	
	
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<title>研砼治筑运营平台</title>
<link rel="stylesheet" type="text/css" href="http://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css" >
</head>
<body>
	<div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-<?php echo $result;?> weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title"><?php echo $title;?></h2>
            <p class="weui-msg__desc"><?php echo $msg;?></p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <?php echo $scan;?>
                
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text">Copyright © 2017-2018 上海研砼治筑建筑科技有限公司 版权所有</p>
				<p class="weui-footer__text">沪ICP备16053942号</p>
            </div>
        </div>
    </div>
	<form action='op.php' method='post' id=form1 >
		<input type=hidden name=p_name id=p_name />
	</form>
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
		document.getElementById('btn-scan').onclick = function(){			
			wx.scanQRCode({
				needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
				scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
				success: function (res) {
					var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
					document.getElementById('p_name').value = result;
					document.getElementById('form1').submit();
				}
			});
		}
	});	
</script>