<?php 

	include "../../basic/parameter.inc";
	
	$Log_Funtion="更新";
	
	$Months = $_POST["Months"];
	$Month = $_POST["Month"];
	$Remark = $_POST["Remark"];
	$Operator = $_POST["operator"];
	$Id = $_POST["id"];
	
	$DateTime=date("Y-m-d H:i:s");
	
	$SetStr="Remark='$Remark',Month='$Month',Months='$Months',Date='$DateTime',Locks='0',Operator='$Operator'";
	include "../../admin/subprogram/updated_model_3a.php";
	
	$succ = array($err,$LogIpad);
	echo json_encode($succ);
?>