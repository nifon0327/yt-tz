<?php
	
	include_once "../../basic/parameter.inc";
	
	$productTypeReader = "SELECT P.TypeId,T.TypeName,T.Letter,C.Color 
						  FROM $DataIn.productdata P
						  LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
						  LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
						  WHERE T.Estate=1
						  GROUP BY P.TypeId";
	
	echo $productTypeReader;
	
?>