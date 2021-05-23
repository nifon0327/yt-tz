<?php
	
	include_once "../../basic/parameter.inc";
	
	$name = $_POST["name"];
	$typeId = $_POST["typeId"];
	$vicitTime = $_POST["time"];
	$personCount = $_POST["person"];
	$remark = $_POST["remark"];
	$isArrive = $_POST["isArrive"];
	$operator = $_POST["operator"];
	$date = date('Y-m-d');
	
	$personCount = ($personCount == "")?"0":$personCount;
	
	$result = "N";
	
	$insertVicitorSql = "Insert Into $DataPublic.come_data (Id, Name, TypeId, Persons, ComeDate, Remark, Date, Operator, Estate) Values (NULL, '$name', '$typeId', '$personCount', '$vicitTime', '$remark', '$date', '$operator', '$isArrive')";
	//echo $insertVicitorSql;
	if(mysql_query($insertVicitorSql))
	{
		$result = "Y"; 
	}
	
	echo $result;
	
?>