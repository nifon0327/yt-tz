<?php
	
	include "../../basic/parameter.inc";
	include("../../model/subprogram/weightSet.php");
	
	$errorProductList = array();
	$weightOverSql = "Select A.ProductId,A.Weight,A.TimeStamp,A.Estate,A.Id,A.BoxId,A.Estate,B.cName, B.eCode, B.Weight as singleWeight, C.Forshort
					  From $DataIn.weightchangelist A
					  Left Join $DataIn.productdata B On B.productId = A.productId
					  Left Join $DataIn.trade_object C On C.CompanyId = B.CompanyId
					  Where A.Estate != 0
					  Order by A.Id Desc";
	//echo $weightOverSql;
	$weightOverResult = mysql_query($weightOverSql);
	while($weightOverRow = mysql_fetch_assoc($weightOverResult))
	{
		$productName = $weightOverRow["cName"];
		$realWeight = $weightOverRow["Weight"];
		$boxId = $weightOverRow["BoxId"];
		$productId = $weightOverRow["ProductId"];
		$group = substr($boxId, 0, 1);
		$productCode = $weightOverRow["eCode"];
		$singleWeight = $weightOverRow["singleWeight"];
		$companyName = $weightOverRow["Forshort"];
		$timeStamp = $weightOverRow["TimeStamp"];
		
		$pOrderIdCheckSql = mysql_query("Select sPOrderId From $DataIn.sc1_cjtj Where BoxId = '$boxId'");
		$pOrderIdCheckResult = mysql_fetch_assoc($pOrderIdCheckSql);
		$pOrderId = $pOrderIdCheckResult["sPOrderId"];
		
		include("../../model/subprogram/weightCalculate.php");
		
		$weightMaxStandard = 0.3;
		$weightMinStandard = 0.3;
	
		$pos = strpos($productName, "散装");
		if($pos !== false)
		{
			$weightMaxStandard = 0.3;
			$weightMinStandard = 0.2;
		}
	
		$maxWeight = round(($singleWeight * ($boxPcs + $weightMaxStandard) + $extraWeight)/1000, 2);
		$minWeight = round(($singleWeight * ($boxPcs - $weightMinStandard) + $extraWeight)/1000, 2);
		
		//获取最近20次的称重
		$weightCheck = array();
		$weightKey = array();
		$weightCheckSql = "Select Weight From $DataIn.sc1_cjtj Where sPOrderId = '$pOrderId' and Weight != 0 Order by Id Desc";
		//echo $weightCheckSql;
		$weightCheckResult = mysql_query($weightCheckSql);
		while($weightCheckRow = mysql_fetch_assoc($weightCheckResult))
		{
			$tmpWeight = $weightCheckRow["Weight"];
			if(array_key_exists($tmpWeight, $weightKey))
			{
				$weightKey[$tmpWeight]++;
				$position = $weightKey[$tmpWeight];
				$weightCheck[] = array("$tmpWeight", "$position");
			}
			else
			{
				$weightKey[$tmpWeight] = 0;
				$position = 0;
				$weightCheck[] = array("$tmpWeight", "$position");
			}
		}
		
		$productInfomation = array("weight"=>"$realWeight", "maxWeight"=>"$maxWeight", "minWeight"=>"$minWeight", "boxId"=>"$boxId", "productId"=>"$productId", "productName"=>"$productName", "group"=>"$group", "singleWeight"=>"$singleWeight", "companyName"=>"$companyName", "pOrderId"=>"$pOrderId", "boxCount"=>"$boxPcs"."PCS", "extraWeight"=>"$extraWeight", "productCode"=>"$productCode", "timeStamp"=>"$timeStamp");
		
		$errorProductList[] = array($productInfomation, $weightCheck);
		
	}
	
	echo json_encode($errorProductList);
	
?>