<?php 
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="配件";			//需处理
$Log_Funtion="规格更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
        case "Spec":
                $sql = "UPDATE  $DataIn.stuffdata  SET Spec='$tempSpec' WHERE StuffId=$StuffId";
                $result = mysql_query($sql);
		        if($result){
                            echo "Y"; 
                }else{
                         echo "N"; 
                }
                break;
 }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);


?>