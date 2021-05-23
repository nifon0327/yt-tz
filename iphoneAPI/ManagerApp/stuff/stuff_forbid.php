<?php 
//步骤1：

$Log_Item="配件资料";			
$Log_Funtion="禁用";


$DateTime=date("Y-m-d H:i:s");
$Operator=$LoginNumber;
$OperationResult="N";
$stuffId = $info[0];

$Log = "";
if ($stuffId>0) {
$sql = "update $DataIn.stuffdata set Estate=0 where StuffId=$stuffId";
$in_sql = @mysql_query($sql);
if ($in_sql) {
	$Log = "配件 $stuffId 禁用成功";
	$OperationResult="Y";
}
else {
	$Log = "配件 $stuffId 禁用失败";
}
}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("Result"=>"$OperationResult");
?>
