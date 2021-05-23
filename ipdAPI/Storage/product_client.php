<?php
	
	include_once "../../basic/parameter.inc";
	
	/*
$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
*/
	
	$productItemType = $_POST["productType"];
	//$productItemType = "镭雕";
	//$productItemType = "皮套|车缝MC|镭雕|丝印|水贴|组装|袜加工";
	$productType = explode("|", $productItemType);
	
	$indexForProduct = array();
	for($i=0; $i<count($productType);$i++)
	{
		$TypeName = "$productType[$i]";
		$typeIdResult = mysql_query("Select Parameter From $DataPublic.sc4_funmodule Where ModuleName = '$TypeName' Limit 1");
		$typeIdRow = mysql_fetch_assoc($typeIdResult);
		$TypeId = $typeIdRow["Parameter"];
		$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
									FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
									LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
									LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
									LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
									WHERE
									S.scFrom>0 
									AND S.Estate>0 
									AND A.TypeId='$TypeId'
									GROUP BY M.CompanyId 
									order by M.CompanyId",$link_id);
		
		if(mysql_num_rows($ClientResult) == 0)
		{
			continue;
		}
		
		$comanyHolder = array();
		while($clientRow = mysql_fetch_assoc($ClientResult))
		{
			$companyId = $clientRow["CompanyId"];
			$forshot = $clientRow["Forshort"];
			$typeArray = array();
			
			$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName
									  FROM $DataIn.yw1_ordermain M 
									  LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
									  LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
									  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
									  LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
									  LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
									  LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
									  WHERE 1
									  AND A.TypeId='$TypeId'
									  AND S.scFrom>0 
									  AND S.Estate>0
									  AND M.CompanyId='$companyId'
									  GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);
						
			while($typeRow = mysql_fetch_assoc($TypeResult))
			{
				$productTypeName = $typeRow["TypeName"];
				$productTypeId = $typeRow["TypeId"];
				$typeArray[] = array("$productTypeName", "$productTypeId");
			}
			$comanyHolder[] = array("$companyId", "$forshot", $typeArray);
		}
		
		$indexForProduct["$TypeName"] = array("$TypeId",$comanyHolder);
		
	}
	
	echo json_encode($indexForProduct);
	/*
print_r($indexForProduct);
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo 'Page generated in '.$total_time.' seconds.';
*/
	
?>