<?php 
//OK
$ipadTag = $_GET["ipadTag"];
if($ipadTag == "yes")
{
	$POrderId = $_GET["POrderId"];
	$Qty = $_GET["Qty"];
	$Operator = $_GET["Operator"];
}
else
{
	include "cj_chksession.php";
}
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//步骤2：
$Log_Item="生产记录";			//需处理
$Log_Funtion="保存";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$POrderId=substr($StockId,0,12);
$inRecode="INSERT INTO $DataIn.sc1_gxtj (Id,GroupId,sPOrderId,ProcessId,POrderId,StockId,Qty,Remark,LastPos,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Operator','$sPOrderId','$thisProcessId','$POrderId','$StockId','$Qty','$Remark','$LastPos','$Date','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>
