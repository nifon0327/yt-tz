<!-- 电信-zxq 2012-08-01-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>车间登录</title>
<link href="css/cjlogin.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="container">
   <div id="header"></div>
   <div id="center">
     <form name="form1" method="post" action="">
     <input name="CardId" type="password" id="CardId">
     <input type="button" name="Submit" value="按钮" onClick="javascript:loginSYS()">
     </form>
  </div>
   <div id="footer"></div>
</div>
</body>
</html>
<script>
function loginSYS(){
	if(document.form1.CardId.value==""){
		alert("未输入登录识别码!");
		}
	else{
		document.form1.action="cj_checklogin.php";
		document.form1.submit();
		}
	}
</script>