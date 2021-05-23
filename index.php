<?php
/*if (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
    Header("Location: index_ie.php");
}*/

include_once "model/modelfunction.php";        //读入函数
$IP = GetIP();
$th = "108";
$cssName = "application1.css";

/*if (preg_match('/iPhone/i', $_SERVER['HTTP_USER_AGENT'])) {
    $th = "92";
    $cssName = "iphone.css";
} else {
    if (preg_match('/iPad|Android/i', $_SERVER['HTTP_USER_AGENT'])) {
        $th = "93";
        $cssName = "ipad.css";
    }
}*/

$langSign = 0;
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
if (preg_match("/zh-c/i", $lang) || preg_match("/zh/i", $lang)) {
    $langSign = 1;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>研砼运营管理平台</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta property="og:image" content="/favicon.png">

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link href="login/images/mc-icon-57x57.png" rel="apple-touch-icon-precomposed">
    <link href="login/images/mc-icon-114x114.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <link href="login/<?php echo $cssName; ?>" media="screen" rel="stylesheet" type="text/css"/>
</head>


<script src="model/pagefun.js" type=text/javascript></script>
<script src="/plugins/js/jquery.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>

    $(document).ready(function() {
        //Init goes here
        $('body').on('keydown', keyDown);
        $('input#Name').focus();
    });

    function keyDown(e) {
        var ev = event || window.event;

        //回车键对应的ASCII是13，Tab对应的是9
        //判断按下回车建的控件类型，不能是reset，submit，textarea和空
        if (ev.keyCode == 13 && ev.srcElement.type != "submit" && ev.srcElement.type != "button" &&
            ev.srcElement.type != "reset" && ev.srcElement.type != "textarea" &&
            ev.srcElement.type != "") {
            ev.keyCode = 9;

            //document.loginForm.SignIn.onclick();
            //LoginCheck();
            $('#SignIn').trigger('click');
        }
    }

    function LoginCheck() {
        var tempU = $('input#Name').val(),
            tempP = $('input#Password').val();
        if (tempU === '' || tempP === '') {
            alert("未输入登录帐号或密码!");
        } else {
            $.ajax({
                url : 'LoginCheck.php',
                type : 'post',
                data : {
                    U : tempU,
                    P : tempP
                },
                dataType : 'json',
                beforeSend : function() {
                    //$('#LoginMsg').html('登入中，請稍後...').show();
                },
                success : function(result) {
                    if (result.rlt) {
                        $('form#LoginForm').attr('action', result.link).submit();
                    } else {
                        alert("帐号或密码有误!");
                    }
                }
            }).done(function() {
                //$('#LoginMsg').html('').hide();
            });
        }//eo if
    }
</script>
<body>

<div class="login-wrap">
    <form id="LoginForm" method="post" action="?">
        <input name="IP" type="hidden" id="IP" value="<?php echo $IP ?>"/>
        <input name="IPAdd" type="hidden" id="IPAdd" value=""/>
        <input name="Device" type="hidden" id="Device" value="<?php echo $th ?>"/>
        <div class="login-field">
			<div class="login-box">
				<input id="Name" type="text" name="Name" class="login-input" required="" placeholder="用户名" autocomplete="off">
			</div> 
			<div class="login-box">
				<input id="Password" type="password" name="Password" class="login-input" required="" placeholder="密码">
			</div> 
			<div style="margin-top:60px">
				<button id="LoginBtn" name="LoginBtn" class="login-button" onclick="LoginCheck();return false">登 录</button>
			</div>
        </div>
    </form>
</div>
<div class="login-footer">
	<div>
		<a target='_blank' href='#'>{{a1}}</a><a target='_blank' href='javascrip:;'>{{a2}}</a>
		<a target='_blank' href='#'>{{a3}}</a><a target='_blank' href='javascrip:;'>{{a4}}</a>
		<a target='_blank' href='#'>{{a5}}</a><a target='_blank' href='javascrip:;'>{{a6}}</a>
	</div>
	<P class="app">{{right}}</p>
	<p class="app">{{message}}</p>
</div>
</body>
</html>
<script>
  var app = new Vue({
      el:'.login-footer',
      data:{
          a1:'公司介绍',
          a2:'新闻动态',
          a3:'服务项目',
          a4:'下载中心',
          a5:'团队业绩',
          a6:'联系我们',
          message:'沪ICP备16053942号',
          right:'Copyright © 2017-2018 上海研砼治筑建筑科技有限公司 版权所有'
      }
  })
</script>
