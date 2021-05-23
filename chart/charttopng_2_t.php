<?php   
//独立已更新
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 全部客户每月下单、出货金额条形图");
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td>
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="charttopng_2_m.php" method="post" target="mainFrame">
<select name="CheckYear" id="CheckYear">
<?php   
$sYear=2008;
$nYear=date("Y");
for($Y=$sYear;$Y<=$nYear;$Y++){
	if($Y==$nYear){
		echo"<option value='$Y' selected>$Y 年</option>";
		}
	else{
		echo"<option value='$Y'>$Y 年</option>";
		}
	}
?>
</select>&nbsp;  <input type="submit" name="Submit" value="查看" >
</form>
</td></tr>
</table>
</body>
</html>