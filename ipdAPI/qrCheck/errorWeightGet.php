<?php
	
	include "../../basic/parameter.inc";
	
	$boxId = $_POST["boxId"];
	$productId = $_POST["prodcutId"];
	
	$getErrorItemSql = "Select A.*, B.cName 
						From $DataIn.weightchangelist A
						Left Join $DataIn.productdata B On B.ProductId = A.ProductId
						Where A.BoxId = '$boxId' 
						Limit 1";
	$errorItemResult = mysql_query($getErrorItemSql);
	while($errorItem = mysql_fetch_assoc($errorItemResult))
	{
		$Id = $errorItem["Id"];
		$productId = $errorItem["ProductId"];
		$weight = $errorItem["Weight"];
		$time = $errorItem["TimeStamp"];
		$estate = $errorItem["Estate"];
		$productName = $errorItem["cName"];
	}
	
	echo json_encode(array("Id"=>"$Id", "productId"=>"$productId", "weight"=>"$weight", "time"=>"$time", "estate"=>"$estate", "productName"=>"$productName"));
	
?>