<?php
	
	include "../../basic/parameter.inc";
	
	$productId = $_POST["productId"];
	$boxId = $_POST["boxId"];
	$Estate = $_POST["type"];
	$weight = $_POST["weight"];
	$timeStamp = date('Y-m-d H:i:s');
	
	$success = "N";
	$insertBoxWeightSql = "Insert Into $DataIn.weightchangelist (Id, ProductId, BoxId, Weight, TimeStamp, Estate) Values (NULL, '$productId', '$boxId', '$weight', '$timeStamp', '$Estate')";
	
	if(mysql_query($insertBoxWeightSql))
	{
		$success = "Y";
	}
	
	echo $success;
	
?>