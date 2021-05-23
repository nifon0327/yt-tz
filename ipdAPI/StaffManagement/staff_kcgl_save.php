<?php 

	include "../../basic/parameter.inc";
	
	$branchId = $_POST["branch"];
	$jobId = $_POST["job"];
	$Ids = $_POST["ids"];
	$Months = $_POST["Months"];
	$Month = $_POST["Month"]; 
	$Remark = $_POST["Remark"];
	$Operator = $_POST["operator"];
	$DateTime=date("Y-m-d H:i:s");

	$NumberSTR="AND Number IN ($Ids)";
	$err = "no";
if($DataIn =="ac"){
	$inRecode="INSERT INTO $DataPublic.rs_kcgl
	SELECT NULL,Number,'$Month','$Months','$Remark','$DateTime','1','0','$Operator' ,'0','$Operator','$DateTime','$Operator','$DateTime'
	FROM $DataPublic.staffmain 
	WHERE 1 $NumberSTR AND Estate='1'";
    }
else{
	$inRecode="INSERT INTO $DataPublic.rs_kcgl
	SELECT NULL,Number,'$Month','$Months','$Remark','$DateTime','1','0','$Operator' 
	FROM $DataPublic.staffmain 
	WHERE 1 $NumberSTR AND Estate='1'";
}
	$inResult=@mysql_query($inRecode);
	if($inResult){
		$Log = "新增扣工龄记录成功!";
	}
	else{
		$Log = "新增扣工龄记录失败!";
		$err = "yes";
		$OperationResult="N";
	}
	
	$succeeArray = array($err,$Log);
	
	echo json_encode($succeeArray);
?>