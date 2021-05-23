<?php 
	
	include "../../basic/parameter.inc";
	$code = $_GET["code"];
	//$code = 8018080165573;
	
	$getProductInfoSql = "Select S.OrderPO, P.cName ,P.Code From $DataIn.productdata P
						  Left Join $DataIn.yw1_ordersheet S On S.ProductId = P.ProductId
						  Where P.Code Like '%$code'
						  And S.scFrom>0 
						  And S.Estate>0
						  Group By P.cName
						  Limit 1";
	$getProductInfoResult = mysql_query($getProductInfoSql);			  
	$productInfo = mysql_fetch_assoc($getProductInfoResult);
	
	$codeArray = explode("|", $productInfo["Code"]);
	
	$cName = $productInfo["cName"];
	$eName = $codeArray[0];
	$barCode = $codeArray[1];
	$PO = $productInfo["OrderPO"];
	
	echo json_encode(array("$cName","$eName","$barCode","$PO"));
	
?>