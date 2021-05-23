<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
</head>
<body>
<form name="form1" method="post" action="">
<?php   
include "../basic/parameter.inc";
$ClientResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId=$CompanyId",$link_id));
$Forshort=$ClientResult["Forshort"];
$ChooseMonth=$ChooseMonth==""?12:$ChooseMonth;
/*for($i=$NowY;$i>$StartY;$i--){
	echo"<img src='charttopng_clientship_m.php?Y=$i&CID=$CID&Forshort=$Forshort&ChooseMonth=$ChooseMonth'><p>&nbsp;</p>";
	//include "charttopng_clientship_new.php";
	}*/
//	echo"<img src='charttopng_clientship_new.php?Y=$i&CID=$CompanyId&Forshort=$Forshort&ChooseMonth=$ChooseMonth'><p>&nbsp;</p>";
    echo"<img src='charttopng_clienttotal_data.php?CompanyId=$CompanyId&Forshort=$Forshort&M=$ChooseMonth'><p>&nbsp;</p>";
    echo"<img src='charttopng_clienttotalqty_data.php?CompanyId=$CompanyId&Forshort=$Forshort&M=$ChooseMonth'><p>&nbsp;</p>";
	echo"<img src='charttopng_clientship_type.php?Y=$i&CID=$CompanyId&Forshort=$Forshort&ChooseMonth=$ChooseMonth'><p>&nbsp;</p>";
?>
</form>
</body>
</html>
