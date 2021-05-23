<?php 
//$DataIn.电信---yang 20120801
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="配件图档";			//需处理
$Log_Funtion="业务初审";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
        case "ts":
                $Log_Funtion="配件图档业务初审";
                $sql = "INSERT INTO $DataIn.stuffverify(Id, Mid, StuffId, Estate, Locks, Date, Operator)VALUES(NULL,'$Id','$StuffId','1','0','$Date','$Operator')";
				//echo $sql;
		$result = mysql_query($sql);
		if($result){
                   $Log="<p>产品 $StuffId 的配件图档业务初审成功.";
                   echo "Y"; 
                }else{
                   $Log="<p>产品 $StuffId 的配件图档业务初审失败."; 
                   echo "N"; 
                }
                break;
 }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>