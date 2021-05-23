<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
</head>

<body>
<form name="form1" method="post" action="">
<?php   
//电信---yang 20120801
$StartY=2008;
$NowY=date("Y");
$CID=$CID==""?"1002|ECHO":$CID;
$ClientA=explode("|",$CID);
$CID=$ClientA[0];
$Forshort=$ClientA[1];
for($i=$NowY;$i>$StartY;$i--){
	echo"<img src='desk_grossprofitsheet_m.php?Y=$i&CID=$CID&Forshort=$Forshort'><p>&nbsp;</p>";
	}
	
//echo"<img src='desk_grossprofitsheet_m.php><p>&nbsp;</p>";
?>
</form>
</body>
</html>
