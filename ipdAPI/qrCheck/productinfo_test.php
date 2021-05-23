<?php
	
	include_once "../../basic/parameter.inc";
	
	$productId = $_GET["productId"];
	$pOrderId = $_GET["pOrderId"];
	$boxId = $_GET["boxId"];
	$operator = $_GET["operator"];
	
	$DateTime=date("Y-m-d H:i:s");
	
	/*
$productId = "86522";
	$boxId = "I20140418015733000011";
	$pOrderId = "201402071311";
	$operator = "11092";
*/
	//获取产品信息
	$productFindSql = "Select P.cName, P.eCode, P.TestStandard, P.Weight, P.TypeId, P.maxWeight, P.minWeight, U.Name as Unit, C.forshort
					   From $DataIn.productdata P
					   LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
					   LEFT JOIN $DataIn.clientdata C ON C.companyId = P.companyId
					   Where ProductId = '$productId' Limit 1";
	$productResult = mysql_query($productFindSql);
	$prodcutRow = mysql_fetch_assoc($productResult);
	
	$productName = $prodcutRow["cName"];
	$prodcutECode = $prodcutRow["eCode"];
	$testStandard = $prodcutRow["TestStandard"];
	$productWeight = $prodcutRow["Weight"];
	$unit = $prodcutRow["Unit"];
	$forShort = $prodcutRow["forshort"];
	$TypeId = $prodcutRow["TypeId"];
	
	$maxWeight = $prodcutRow["maxWeight"]."";
	$minWeight = $prodcutRow["minWeight"]."";
	
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
	
	$OrderQtyResult = mysql_query("SELECT A.Qty, SUM( B.Qty ) AS scQty,A.StockId,A.POrderId
								   FROM $DataIn.yw1_scsheet A
								   LEFT JOIN $DataIn.sc1_cjtj B ON B.sPOrderId = A.sPOrderId
								   WHERE A.sPOrderId =  '$pOrderId'");
	
	$orderQtyRow = mysql_fetch_assoc($OrderQtyResult);
	$orderQty = intval($orderQtyRow["Qty"]);
	$scQty = ($orderQtyRow["scQty"]=="")?0:$orderQtyRow["scQty"];
	$StockId = $orderQtyRow['StockId'];
	$opOrderId = $orderQtyRow['POrderId'];
	//获取小组ID
	$groupInital = strtoupper(substr($boxId, 0, 1));
	$getGroupIdResult = mysql_query("SELECT GroupId from $DataIn.staffgroup WHERE GroupName = '组装$groupInital' AND Estate=1");
	$groupIdRow = mysql_fetch_assoc($getGroupIdResult);
	$Login_GroupId = $groupIdRow["GroupId"];
	
	//echo "pro:$productInBox   order:$orderQty    sc:$scQty";
	//开始分析
	if($productInBox == "" || $orderQty == "" ){
		//Alert some thing
	}else{
		$isLast = false;
		$lestQty = $orderQty - $scQty;
		if($lestQty >= $productInBox){
			$logQty = $productInBox;
		}else{
			$logQty = $lestQty;
			$isLast = true;
		}
		
		$isKickOut = "N";
		//是否该天生产的第一箱
		$isFirstBoxSql = "Select * From $DataIn.sc1_cjtj Where sPOrderId = '$pOrderId'  and Estate = '1'";
		//echo $isFirstBoxSql;
		//$isFirstBoxSql = "Select * From $DataIn.sc1_cjtj Where POrderId = '$pOrderId' and TypeId = '7100'";
		$isFirstBoxResult = mysql_query($isFirstBoxSql);
		//echo $isFirstBoxSql;
		if(mysql_num_rows($isFirstBoxResult) == 0){
			$isKickOut = "Y";
		}

		$isRecord = "Y";
		//先判断是否有boxId，有则不插入
		$hasBoxIdSql = "Select * From $DataIn.sc1_cjtj Where boxId = '$boxId'";
		$hasBoxIdResult = mysql_query($hasBoxIdSql);
		if(mysql_num_rows($hasBoxIdResult) == 0){
			$logQtySql = "INSERT INTO $DataIn.sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark, boxId,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Login_GroupId','$opOrderId','$pOrderId', '$StockId','$logQty','$Remark', '$boxId','$DateTime','0','0','$operator')";
			//echo $logQtySql;
			if(@mysql_query($logQtySql)){
				$isRecord = "N";
			}else{
				$isRecord = "O";
			}
		}	
	}
	
	//	
	include("../../model/subprogram/weightCalculate.php");
	
	/*
if($maxWeight == "0.00")
	{
		$maxWeight = round(($productWeight * ($boxPcs + 0.5) + $extraWeight)/1000, 2);
	}
	
	if($minWeight == "0.00")
	{
		$minWeight = round(($productWeight * ($boxPcs - 0.5) + $extraWeight)/1000, 2);
	}
*/
	
	$weightMaxStandard = 0.3;
	$weightMinStandard = 0.3;
	
	$pos = strpos($productName, "散装");
	if($pos !== false){
		$weightMaxStandard = 0.3;
		$weightMinStandard = 0.2;
	}
	
	$maxWeight = round(($productWeight * ($boxPcs + $weightMaxStandard) + $extraWeight)/1000, 2);
	$minWeight = round(($productWeight * ($boxPcs - $weightMinStandard) + $extraWeight)/1000, 2);
	
	//echo $productName."**".$testStandard."**".$productWeight."**".$boxPcs.$unit."**".$extraWeight."**".$forShort."**".$maxWeight."**".$minWeight."**".$isKickOut."**".$isRecord;
	$isLastTag = $isLast?'0':'1';
	$productInfo = array("productName"=>"$productName", "testStandard"=>"$testStandard", "productWeight"=>"$productWeight", "boxPcs"=>"$boxPcs$unit", "extraWeight"=>"$extraWeight", "forShort"=>"$forShort", "maxWeight"=>"$maxWeight", "minWeight"=>"$minWeight", "isKickOut"=>"$isKickOut", "isRecord"=>"$isRecord", "eCode"=>"$prodcutECode", 'isLast'=>"$isLastTag", 'logQty'=>"$logQty");
	
	//获取最近20次的称重
	$weightCheck = array();
	$weightKey = array();
	$weightCheckSql = "Select Weight From $DataIn.sc1_cjtj Where sPOrderId = '$pOrderId' and Weight != 0 Order by Id Desc";
	//echo $weightCheckSql;
	$weightCheckResult = mysql_query($weightCheckSql);
	while($weightCheckRow = @mysql_fetch_assoc($weightCheckResult)){
		$tmpWeight = $weightCheckRow["Weight"];
		if(array_key_exists($tmpWeight, $weightKey)){
			$weightKey[$tmpWeight]++;
			$position = $weightKey[$tmpWeight];
			$weightCheck[] = array("$tmpWeight", "$position");
		}else{
			$weightKey[$tmpWeight] = 0;
			$position = 0;
			$weightCheck[] = array("$tmpWeight", "$position");
		}
	}
	
	echo json_encode(array($productInfo, $weightCheck));
	
?>