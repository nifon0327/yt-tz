<?php 
	
	include "../../basic/parameter.inc";
	
	$number = $_POST["Number"];
	$signMonth = $_POST["Month"];
	
	$signStrSql = "Select Sign From $DataPublic.wage_list_sign Where Number = '$number' And SignMonth = '$signMonth'";
	
	
	$signStrResult = mysql_query($signStrSql);
	$signRow = mysql_fetch_assoc($signStrResult);
	
	$sign = $signRow["Sign"];
	if($sign == "")
	{
		$sign = "no";
	}
	
	echo json_encode(array($sign));
	
?>