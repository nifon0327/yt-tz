<?php
/**
*
*  Modified by Aitch.Zung (aitch.zung@icloud.com) 2014-06-25
*
*/
include_once "model/modelfunction.php";		//读入函数
$IP = GetIP();
$th = "108";
//if (strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") ||
//	strpos($_SERVER['HTTP_USER_AGENT'],"iPad") ||
//	strpos($_SERVER['HTTP_USER_AGENT'],"Android")){
if(preg_match('/[iPhone|iPad|Android]/i', $_SERVER['HTTP_USER_AGENT'])){
  $th = "93";
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<link href='/favicon.ico' type='image/x-icon' rel='icon' />
<link href='/favicon.ico' type='image/x-icon' rel='shortcut icon' />
<link href="login/images/mc-icon-57x57.png" rel="apple-touch-icon-precomposed">
<link href="login/images/mc-icon-114x114.png" sizes="114x114" rel="apple-touch-icon-precomposed">
<meta property="og:image" content="/favicon.png">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ASH CLOUD</title>


<script src="model/pagefun.js" type=text/javascript></script>
<script src="/plugins/js/jquery.min.js" type="text/javascript"></script>

</head>

<style>
body{
	margin: 0;
	padding: 0;
}

#main{
	height:455px;
	width:1066px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 10%;
	background:#ffffff url(images/logo1.jpg)  no-repeat 0px 0px;
}

#LoginTB{
    margin:0;
	width:1066px;
	height:455px;
	/*background:#ffffff url(images/logo_a1.png)  no-repeat 0px 0px;*/
	}

.byte {
	width: 260px;
	height:80px;
	font-size: 26pt;
	line-height:80px;
	text-align: left;
	vertical-align: middle;
	border: none;
	color: #ffffff;
	background:  none transparent scroll repeat 0% 0%;
	}

.SignCSS {
	position:  relative;
	border: 0;
	float:left;
	width: 180px;
	height:80px;
	line-height:80px;
	color: #ffffff;
	font-size: 20pt;
	font-family: Arial, Helvetica, sans-serif;
	cursor:  pointer;
	background:  none transparent scroll repeat 0% 0%;
	}

#gov{
	position:relative;
	margin-top:108px;
	margin-left: 700px;
	line-height: 20px;
	height: 20px;
	/*border: 2px solid #f00;*/
	width: 200px;
}
#gov ul{
	list-style:none;
	margin: 0px;
	padding:0px;
	}
#gov ul li{
	float:none;
	POSITION: relative;
	FONT-SIZE: 12pt;
	font-family: Arial, Helvetica,Times sans-serif;
	}

#links{
	position:relative;
	width:920px;
	height:100px;
	background: #ffffff url(images/logo_a2.png)  no-repeat center center;
}

#links{
   position: relative;
   margin: 0px;
   padding:0px;
    display: inline;
	list-style:none;
}

#links  li{
	float:right;
	/*margin-top:14px;*/
	background: none;
	margin-left:5px;
	}

#LinkDBS {
	cursor: pointer;
	position: relative;
	background: none transparent scroll repeat 0% 0%;
	}

#LinkEBS {
	cursor: pointer;
	color: #ffffff;
	position: relative;
	background: none transparent scroll repeat 0% 0%;
}
</style>

<script>

$(document).ready(function(){
//Init goes here
	$('body').on('keydown', keyDown);
	$('input#Name').focus();
});

/*function init(){
	document.onkeydown=keyDown ;
	document.loginForm.Name.focus();
} */
function keyDown(e){
	var ev = event || window.event;
        //回车键对应的ASCII是13，Tab对应的是9
        //判断按下回车建的控件类型，不能是reset，submit，textarea和空
        if (ev.keyCode == 13 && ev.srcElement.type != "submit" && ev.srcElement.type != "button" &&
            ev.srcElement.type != "reset" && ev.srcElement.type != "textarea" &&
            ev.srcElement.type != "" ) {
            ev.keyCode = 9;
            //document.loginForm.SignIn.onclick();
            //LoginCheck();
            $('#SignIn').trigger('click');
        }

	}
function LoginCheck(){
	var tempU = $('input#Name').val(),
	    tempP = $('input#Password').val();

	if(tempU === '' || tempP === ''){
		alert("未输入登录帐号或密码!");
    }else{
		//后台检测
		//Do not use GET!
		/*
		myurl="LoginCheck.php?U="+tempU+"&P="+tempP;
		var ajax=InitAjax();
		ajax.open("GET",myurl,true);
		ajax.onreadystatechange =function(){
			if(ajax.readyState==4){
				//根据返回结果处理
				var BackLink=ajax.responseText;
				if(BackLink!="0"){
					document.loginForm.action=BackLink;
					document.loginForm.submit();
				}else{
					alert("帐号或密码有误!");
					}
				}
			}
		ajax.send(null);
		*/
		$.ajax({
			url: 'LoginCheck.php',
			type: 'post',
			data:{
				U:tempU,
				P:tempP
			},
			dataType:'json',
			beforeSend: function(){
				$('#LoginMsg').html('登入中，請稍後...').show();
			},
			success:function(result){
				if(result.rlt){
					$('form#LoginForm').attr('action', result.link).submit();
				}else{
					alert("帐号或密码有误!");
				}
			}
		}).done(function(){
			$('#LoginMsg').html('').hide();
		});
	}//eo if
}
</script>
<body>
<FORM id="LoginForm" name="loginForm" method="post" action="?">
<input name="IP" type="hidden" id="IP" value="<?php echo $IP?>" />

<div id="main">
<table width="1066" align="left" cellspacing="5" id="LoginTB">
<tr valign="middle">
	<td width="443" height="0" align="right" height="130">
		<input  id="Name" name="Name" class="byte" title="Enter the user name" placeholder="username..." maxlength="30">
	</td>
	<td width="293" style="TEXT-ALIGN:center">
		<input type="password" id="Password" name="Password" class="byte" title="Enter the password" place="password..." maxlength="30">
	</td>
	<td width="190" style="TEXT-ALIGN:center;">
		<input  type="button" id="SignIn" name="SignIn" onclick="LoginCheck()" value="Sign" class="SignCSS">
	</td>
	<td width="140">&nbsp;</td>
</tr>
<tr><td align="center" colspan="4"><span id="LoginMsg"></span></td></tr>
</tr style="display: none;">
	<td colspan="4" height="<?php echo $th?>">&nbsp;</td>
</tr>
<tr height="58">
<td>&nbsp;</td>
<td>&nbsp;</td>
<td align="center"><a href="http://www.miitbeian.gov.cn" target="_blank" width="150"   border="0"  class="govCSS">粤ICP备12040715号</a>
<td>&nbsp;</td>
</td>
</tr>
<tr height="65" valign='top'>
<td colspan="4" >
    <table width='520' align='right'>
    <tr>
    	  <td width="120"><a href="http://www.ebs.gov.cn/EntCertificate.aspx?domainId=6bf67e6e-a8ae-4cec-83d3-0f14035a8d4b" target="_blank" id="LinkEBS" name="LinkEBS" class="LinkEBS" ><img src="images/ebs.png" title="上海市市场监督管理局企业主体身份公示" alt="上海市市场监督管理局企业主体身份公示" width="120" height="40" border="0" style='margin-top:-3px;'/></a></td>
	  	  <td width="143" >&nbsp;</td>
          <td width="120" ><iframe id="LinkDBS" name="LinkDBS" class="LinkDBS" src="http://www.middlecloud.com/desk/dbslogo.php" width="120" height="40"  marginwidth="0" marginheight="0" topmargin=0 frameborder="0" scrolling="no"></iframe></td>
          <td width="137" >&nbsp;</td>
    </tr>
    </table>
</td>
</tr>
</tr><td colspan="4" height="135">&nbsp;</td></tr>
</table>
</div>
</body>
</html>

