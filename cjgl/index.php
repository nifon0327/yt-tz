<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>车间登录</title>
<link href="css/cjlogin.css" rel="stylesheet" type="text/css" />
</head>
<script>
function init(){ 
	document.onkeydown=keyDown ; 
	} 
function keyDown(e){  
	var ev = event || window.event;   
        //回车键对应的ASCII是13，Tab对应的是9   
        //判断按下回车建的控件类型，不能是reset，submit，textarea和空   
        if (ev.keyCode == 13 && ev.srcElement.type != "submit" && ev.srcElement.type != "button" &&   
            ev.srcElement.type != "reset" && ev.srcElement.type != "textarea" &&   
            ev.srcElement.type != "" ) {   
            ev.keyCode = 9;   
        }   

	}
function LoginCheck(){
	if(document.loginForm.UserName.value=="" || document.loginForm.Password.value==""){
		alert("未输入登录帐号或密码!");
		}
	else{
		document.loginForm.action="checklogin.php";
		document.loginForm.submit();
		}
	}
</script>
<body onload="init()">
<div id="header"></div>
<div id="center">
   	<DIV id="LoginD">
	<DIV id="Title">生产管理</DIV>
	<DIV id=login>
		<FORM name="loginForm" method="post" action="?">
		<INPUT id="UserName" name="UserName" onFocus="if(value==defaultValue){value='';this.style.background='#FFFFFF'}" onBlur="if(!value){value=defaultValue;this.style.background='url(UserName.gif) #fff no-repeat 3px 7px'}" >
		<INPUT type="password" id="Password" name="Password" onFocus="if(value==defaultValue){value='';this.style.background='#FFFFFF'}" onBlur="if(!value){value=defaultValue;this.style.background='url(Password.gif) #fff no-repeat 3px 7px'}">
		<INPUT type="button" id="SignIn" name="SignIn" onclick="LoginCheck()" value="">
		</FORM>
	</DIV>
	
	<!--<DIV id="Company" style="font-size:12px">上海研砼治筑建筑科技有限公司</DIV>-->
	</DIV> 
</div>
<div id="footer"></div>
</body>
</html>
