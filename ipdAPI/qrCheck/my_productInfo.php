<?php
	
	include_once "../../basic/parameter.inc";
	
	$productId = $_GET["productId"];
	$pOrderId = $_GET["pOrderId"];
	$boxId = $_GET["boxId"];
	$operator = $_GET["operator"];
	
	$DateTime=date("Y-m-d H:i:s");
	
	$productId = "93558";
	$boxId = "F20131107042807000002";
	$pOrderId = "201309271309";
	$operator = "10125";
	//获取产品信息
	$productFindSql = "Select P.cName, P.eCode, P.TestStandard, P.Weight, P.TypeId, P.maxWeight, P.minWeight, U.Name as Unit, C.forshort
					   From $DataIn.productdata P
					   LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
					   LEFT JOIN $DataIn.clientdata C ON C.companyId = P.companyId
					   Where ProductId = '$productId' Limit 1";
	$productResult = mysql_query($productFindSql);
	$prodcutRow = mysql_fetch_assoc($productResult);
	
	$productName = $prodcutRow["cName"]." / ".$prodcutRow["eCode"];
	$testStandard = $prodcutRow["TestStandard"];
	$productWeight = $prodcutRow["Weight"];
	$unit = $prodcutRow["Unit"];
	$forShort = $prodcutRow["forshort"];
	$TypeId = $prodcutRow["TypeId"];
	
	$maxWeight = $prodcutRow["maxWeight"];
	$minWeight = $prodcutRow["minWeight"];
	
	$BoxResult = mysql_query("SELECT D.Spec,D.Weight,P.Relation 
									  FROM $DataIn.pands P 
									  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
									  LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
									  WHERE P.ProductId='$productId' 
									  AND P.ProductId>0 
									  and T.TypeId='9040'",$link_id);
	
	$BoxRows = mysql_fetch_assoc($BoxResult);
	$Relation=explode("/", $BoxRows["Relation"]);
	$productInBox = $Relation[1];
	
	$OrderQtyResult = mysql_query("SELECT A.Qty, SUM( B.Qty ) AS scQty
								   FROM $DataIn.yw1_ordersheet A
								   LEFT JOIN $DataIn.sc1_cjtj B ON B.POrderId = A.POrderId
								   WHERE A.POrderId =  '$pOrderId'
								   AND B.TypeId =  '7100'");
	
	$orderQtyRow = mysql_fetch_assoc($OrderQtyResult);
	$orderQty = $orderQtyRow["Qty"];
	$scQty = ($orderQtyRow["scQty"]=="")?0:$orderQtyRow["scQty"];
	
	//获取小组ID
	$getGroupIdResult = mysql_query("Select GroupId From $DataPublic.staffmain Where Number = '$operator'");
	$groupIdRow = mysql_fetch_assoc($getGroupIdResult);
	$Login_GroupId = $groupIdRow["GroupId"];
	
	//echo "pro:$productInBox   order:$orderQty    sc:$scQty";
	//开始分析
	if($productInBox == "" || $orderQty == "" )
	{
		//Alert some thing
	}
	else
	{
		$isLast = false;
		$lestQty = $orderQty - $scQty;
		if($lestQty >= $productInBox)
		{
			$logQty = $productInBox;
		}
		else
		{
			$logQty = $lestQty;
			$isLast = true;
		}
		
		$logQtySql = "INSERT INTO $DataIn.sc1_cjtj (Id,GroupId,TypeId,POrderId,Qty,Remark, boxId,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Login_GroupId','$TypeId','$pOrderId','$productInBox','$Remark', '$boxId','$DateTime','0','0','$operator')";
		//echo $logQtySql;
		mysql_query($logQtySql);		
	}
	
	//
	$isKickOut = "N";
	include("../../model/subprogram/weightCalculate.php");
	
	if($maxWeight == "0.00")
	{
		$maxWeight = round(($productWeight * ($boxPcs + 1) + $extraWeight)/1000, 2);
	}
	
	if($minWeight = "0.00")
	{
		$minWeight = round(($productWeight * ($boxPcs - 1) + $extraWeight)/1000, 2);
	}
	
	echo $productName."**".$testStandard."**".$productWeight."**".$boxPcs.$unit."**".$extraWeight."**".$forShort."**".$maxWeight."**".$minWeight."**".$isKickOut;
	
?>