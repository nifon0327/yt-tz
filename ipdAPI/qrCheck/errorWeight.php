<?php
	
	/*
	* 错误处理列表
	*/
	include "../../basic/parameter.inc";
	include("../../model/subprogram/weightSet.php");
	
	$weightOverSql = "Select A.ProductId,A.Weight,A.TimeStamp,A.Estate,A.Id,A.BoxId,A.Estate,B.cName 
					  From $DataIn.weightchangelist A
					  Left Join $DataIn.productdata B On B.productId = A.productId
					  Where A.Estate != 0
					  Order by A.Id Desc";
	$errorList = array();
	$weightOverResult = mysql_query($weightOverSql);
	while($weightOverRow = mysql_fetch_assoc($weightOverResult))
	{
		$productName = $weightOverRow["cName"];
		$Id = $weightOverRow["Id"];
		$productId = $weightOverRow["ProductId"];
		$weight = $weightOverRow["Weight"];
		$Estate = $weightOverRow["Estate"];
		$timeStamp = $weightOverRow["TimeStamp"];
		$boxId = $weightOverRow["BoxId"];
		$errorList[] = array("$Id", "$productName", "$productId", "$weight", "$Estate", "$timeStamp", "$boxId");
	}
	
	echo json_encode($errorList);
	
?>