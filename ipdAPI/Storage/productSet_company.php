<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$assblemState = $_GET["assblemState"];
	//$assblemState = 0;
	
	if($assblemState == "0")
	{
		$searchRow = "S.Estate= 2 ";
	}
	else
	{
		$searchRow = "S.scFrom > 0 AND S.Estate= 1 ";
	}

	
	$getProductSetCompany = "SELECT M.CompanyId,C.Forshort 
							 FROM $DataIn.yw1_ordermain M 
							 LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
							 LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
							 WHERE 
							 $searchRow
							 GROUP BY M.CompanyId 
							 order by M.CompanyId ,M.OrderDate desc";
	$companis = array();				
	$prodcutSetResult = mysql_query($getProductSetCompany);
	while($prodcutSetRow = mysql_fetch_assoc($prodcutSetResult))
	{
		$companyId = $prodcutSetRow["CompanyId"];
		$companyName = $prodcutSetRow["Forshort"];
		
		$getTypeSql = "SELECT P.TypeId,T.TypeName,C.Color 
					   FROM $DataIn.yw1_ordermain M
					   LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
					   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
					   LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
					   LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
					   WHERE  
					   $searchRow 
					   And M.CompanyId = '$companyId' 
					   GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId";
					   
		$productType = array();
		$typeResult = mysql_query($getTypeSql);
		while($typeRow = mysql_fetch_assoc($typeResult))
		{
			$productType[] = array($typeRow["TypeId"], $typeRow["TypeName"]);
		}
		
		$companis[] = array("companyId"=>"$companyId", "companyName"=>"$companyName", "type"=>$productType);
	}
	
	echo json_encode($companis);
	
?>