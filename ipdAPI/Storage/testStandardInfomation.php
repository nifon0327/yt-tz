<?php
	
	include_once "../../basic/parameter.inc";
	
	$productId = $_POST["productId"];
	$productId = "91081";
	$pOrderId = $_POST["pOrderId"];
	
	$productInfomationSql = "SELECT P.cName,P.eCode,P.Code,P.TestStandard,P.Weight,U.Name AS Unit,TY.mainType
	     					 FROM $DataIn.productdata P 
	     					 LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
	     					 LEFT JOIN $DataIn.producttype TY ON TY.TypeId=P.TypeId 
	     					 WHERE P.ProductId='$productId'
	     					 Limit 1";
	//echo $productInfomationSql;
	$productInfomationResult = mysql_query($productInfomationSql);
	$productInfomationRow = mysql_fetch_assoc($productInfomationResult);
	
	$cName=$productInfomationRow["cName"]."/".$productInfomationRow["eCode"];
	$Unit=$productInfomationRow["Unit"];
	$Weight=$productInfomationRow["Weight"];	
	$TypeId=$productInfomationRow["TypeId"];
	
	$boxSql = "SELECT D.Spec,A.Relation   
				FROM $DataIn.pands A,$DataIn.stuffdata D where A.ProductId='$productId' AND D.TypeId = '9040' and D.StuffId=A.StuffId ORDER BY A.Id";
	
	$boxResult = mysql_query($boxSql);
	$boxRow = mysql_fetch_assoc($boxResult);
	$relation = explode("/", $boxRow["Relation"]);
	$boxPcs = ($relation[1] == "")?"-":$relation[1];
	
	echo json_encode(array("name"=>"$cName", "weight"=>"$Weight", "typeId"=>"$TypeId", "boxPcs"=>"$boxPcs ".$Unit));
	
?>