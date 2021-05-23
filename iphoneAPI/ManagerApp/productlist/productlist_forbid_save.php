<?php 
//步骤1：

$Log_Item="产品资料";			
$Log_Funtion="禁用";


$DateTime=date("Y-m-d H:i:s");
$Operator=$LoginNumber;
$OperationResult="N";
$productId = $info[0];
$stuffIds = $info[1];
$Log = "";
if ($productId>0) {

$sql = "update $DataIn.productdata set Estate=0 where ProductId=$productId";
$in_sql = @mysql_query($sql);
if ($in_sql) {
	$Log.= "产品ID $productId 禁用成功";
	$OperationResult="Y";
	if ($stuffIds != "") {
		$sqlstuff = "update $DataIn.stuffdata set Estate=0 where StuffId in ($stuffIds)";
		$in_sqlStuff = @mysql_query($sqlstuff);
		if ($in_sqlStuff) {
			$Log .= "配件 $stuffIds 禁用成功";
		}
		else {
			$Log .= "配件 $stuffIds 禁用失败";
		}
	
	}
}
else {
	$Log.= "产品ID $productId 禁用失败";
}


}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("Result"=>"$OperationResult");
?>
