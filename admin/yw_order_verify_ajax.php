<?php   
//电信-zxq 2012-08-01
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
 $sql = "INSERT INTO $DataIn.stockdelverify(Id, StockId, Operator)VALUES(NULL,'$StockId','$Operator')";

		$result = mysql_query($sql);
		if($result){
                   $Log="<p>产品 $StockId 的配件需求单删除初审成功.";
                   echo "Y"; 
                 }else{
                   $Log="<p>产品 $StockId 的配件需求单删除初审失败."; 
                   echo "N"; 
                }
?>