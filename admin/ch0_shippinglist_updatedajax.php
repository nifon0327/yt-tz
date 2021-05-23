<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($ActionId){
case 934:	//更新页面 取消出货项目
	             $Log_Funtion="出货项目退回";
				$DelSql="DELETE FROM $DataIn.ch0_shipsheet WHERE POrderId='$POrderId' AND Id=$sId";
				$delRresult = mysql_query($DelSql);

		break;

	case 935://价格更新
		$Log_Funtion="更新出货项目价格";
		$UpSql="UPDATE $DataIn.ch0_shipsheet C  SET C.Price='$NewPrice'  WHERE C.Id='$sId'";
		$UpResult = mysql_query($UpSql);$UpRows=mysql_affected_rows();
			break;
 }
?>