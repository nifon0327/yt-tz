<?php
	
	include "../../basic/parameter.inc";
	include "../../model/modelfunction.php";

	$sgRemark = $_POST["sgRemark"];
	$Id = $_POST["Id"];
	$POrderId = $_POST["POrderId"];
	$Operator = $_POST["Operator"];

	//步骤2：
	$Log_Item="生产记录";			//需处理
	$Log_Funtion="更新生管备注";
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="Y";
	//步骤3：需处理
	$sql = "UPDATE $DataIn.yw1_ordersheet SET sgRemark='$sgRemark' WHERE POrderId='$POrderId'";
	$result = mysql_query($sql);
	if ($result)
	{
		$Log="订单流水号为 $POrderId 的生管备注更新成功.";
	}
	else
	{
		$Log="订单流水号为 $POrderId 的生管备注更新失败.";
		$OperationResult="N";
	}//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);	
	
	echo json_encode(array($OperationResult, $Log));
	
?>