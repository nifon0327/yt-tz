<?php   
//电信-zxq 2012-08-01
//步骤1：$DataIn.yw3_piatt 二合一已更新
include "../model/modelhead.php";
$delSql="DELETE FROM $DataIn.yw3_piatt WHERE Id='$Id'";
$result1 = mysql_query($delSql);
if($result1){
	//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw3_piatt");
	if($Id!="" && $PI!=""){
		include "yw_Pi_reset.php";
		}
	}
?>