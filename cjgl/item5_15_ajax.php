<?php
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="配件转成品入成品仓";			//需处理
$Log_Funtion="更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
switch($ActionId){
       case 1:
        $myResult=$myPDO->query("CALL proc_ck5_llsheet_save_finished('$POrderId','$ProductId','$canRkQty',$Operator);");
        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
        $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
	    $myResult=null;
	    $myRow=null;  
	    echo $OperationResult;
       break;
   }

?>