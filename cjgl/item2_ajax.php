<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
//步骤2：
$Log_Item="待出订单";			//需处理
$Log_Funtion="审核";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$UpdateSql="Update $DataIn.yw1_ordersheet SET Estate=2 WHERE POrderId='$POrderId' AND scFrom='0'";
$UpdateResult = mysql_query($UpdateSql);
if($UpdateResult){
	$Log="<div class=greenB>待出订单审核成功!</div><br>";
	} 
else{
	$Log="<div class=redB>待出订单审核失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>
