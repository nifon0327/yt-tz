<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$updateSQL = "UPDATE $DataIn.sc1_memberset SET GroupId='$GroupId' WHERE Number='$Num' AND Date='$checkDay'";
$updateResult = mysql_query($updateSQL);
if ($updateResult && mysql_affected_rows()>0){
	$Log="员工".$Num.$checkDay."调至小组 $GroupId 成功.<br>";
	$OperationResult="Y";
	}
else{
	$Log.="<div class='redB'>员工".$Num.$checkDay."调至小组 $GroupId 失败.</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>