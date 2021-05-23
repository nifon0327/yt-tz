<?php
	
	include_once "../../basic/parameter.inc";
	
	$id = $_POST["Id"];
	$cardNumber = $_POST["cardNumber"];
	$carNum = $_POST["carNum"];
	$cardHolder = $_POST["cardHolder"];
	$operator = $_POST["operator"];
	
	$Date=date("Y-m-d");
	
	$succed = "N";
	$whiteListSql = "Update $DataIn.dsrc_list Set CardNumber='$cardNumber', CarNum='$carNum', CardHolder='$cardHolder', Date='$Date', Operator = '$operator' Where Id = '$id'";
	if(mysql_query($whiteListSql))
	{
		$succed = "Y";
	}
	
	echo $succed;
	
?>