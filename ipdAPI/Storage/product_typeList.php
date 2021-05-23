<?php
	
	
	/*
$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
*/
	
	include_once "../../basic/parameter.inc";
	
	$productItemType = $_POST["productType"];
	//$productItemType = "皮套|车缝MC|镭雕|丝印|水贴|组装|袜加工";
	//$productItemType = "镭雕";
	$productType = explode("|", $productItemType);
	
	$indexForProduct = array();
	
	for($i=0; $i<count($productType);$i++)
	{
	
		$TypeName = "$productType[$i]";
		$typeIdResult = mysql_query("Select Parameter From $DataPublic.sc4_funmodule Where ModuleName = '$TypeName' Limit 1");
		$typeIdRow = mysql_fetch_assoc($typeIdResult);
		$TypeId = $typeIdRow["Parameter"];
	
		$TypeSql = "SELECT P.TypeId, T.TypeName, M.CompanyId, C.Forshort 
									  FROM $DataIn.yw1_ordersheet S
									  LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
									  LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
									  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
									  LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
									  LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
									  LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
									  WHERE 1
									  AND A.TypeId='$TypeId'
									  AND S.scFrom>0 
									  AND S.Estate>0
									  GROUP BY P.TypeId, M.CompanyId 
									  ORDER BY M.CompanyId,T.TypeId";
		//echo $TypeSql."<br/>";
		$TypeResult= mysql_query($TypeSql,$link_id);		
			
		$comanyHolder = array();
		$companyJudger = "";	
		$j = -1;	
			  
	    while($typeRow = mysql_fetch_assoc($TypeResult))
		{
			$productTypeName = $typeRow["TypeName"];
			$productTypeId = $typeRow["TypeId"];
			$companyId = $typeRow["CompanyId"];
			$companyShort = $typeRow["Forshort"];
			
			if($companyJudger != $companyId)
			{
				$companyJudger = $companyId;
				$j++;
				$comanyHolder[] = array($companyId, $companyShort, array());
				$comanyHolder[$j][2][] = array("$productTypeName", "$productTypeId");
			}
			else
			{
				$comanyHolder[$j][2][] = array("$productTypeName", "$productTypeId");
			}
			
		}
		
		if(count($comanyHolder[1]) != 0)
		{
			$indexForProduct["$TypeName"] = array("$TypeId",$comanyHolder);
	    }								  
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