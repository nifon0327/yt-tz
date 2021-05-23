<?php 
//外发备料 versionToNumber
$SumTotalValue=$overQty=$blCounts=0;
$curDate=date("Y-m-d");
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek",$link_id));
		$thisWeek = $dateResult["ThisWeek"];
	$sendOutStuffSql = "Select A.StuffId, C.ProductId, C.POrderId, B.StockId,B.OrderQty,
	YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks
						From $DataIn.yw1_ordersheet C
						INNER Join $DataIn.cg1_stuffunite A On C.ProductId = A.ProductId
						Left Join $DataIn.yw1_ordermain M ON M.OrderNumber=C.OrderNumber
						LEFT JOIN $DataIn.productdata PS ON PS.ProductId=C.ProductId
						Left Join $DataIn.cg1_stocksheet B On B.POrderId = C.POrderId
						Left Join $DataIn.stuffdata D On D.StuffId = A.StuffId
						Left Join $DataIn.stuffType E On E.TypeId = D.TypeId
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=C.Id			
		Left Join $DataIn.yw3_pileadtime PL On PL.POrderId = C.POrderId
						Left Join $DataIn.stuffproperty F On F.StuffId = A.uStuffId
						Where C.scFrom>0 And C.Estate >0 And E.mainType < 2  AND E.TypeId<>9033 AND E.TypeId<>9066  
						Group by C.POrderId,A.StuffId";//And F.property is NULL
	$sendOutStuffResult = mysql_query($sendOutStuffSql);
	$testArr = array();
	while($sendOutStuffRow = mysql_fetch_assoc($sendOutStuffResult))
	{$mainQty = $sendOutStuffRow["OrderQty"];
		$POrderId = $sendOutStuffRow["POrderId"];
		$Weeks = $sendOutStuffRow["Weeks"];
		$mainStuffId = $sendOutStuffRow["StuffId"];
		$ProductId = $sendOutStuffRow["ProductId"];
		$qtyCheckSql = "SELECT SUM( B.OrderQty ) AS OrderQty, IFNULL( SUM( L.Qty ) , 0 ) AS llQty, SUM( K.tStockQty ) AS tStockQty
						FROM $DataIn.cg1_stuffunite A
						LEFT JOIN $DataIn.cg1_stocksheet B ON B.StuffId = A.uStuffId 
						LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = B.StockId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = A.uStuffId
						WHERE A.POrderId = '$POrderId' AND A.ProductId =  '$ProductId' and  A.StuffId =  '$mainStuffId'
						";
		$qtyCheckResult = mysql_query($qtyCheckSql);
		$qtyCheckRow =mysql_fetch_assoc($qtyCheckResult);
		$totleLLQty = $qtyCheckRow["llQty"];
		$totleOrderQty = $qtyCheckRow["OrderQty"];
		$totleTStockQty = $qtyCheckRow["tStockQty"];
		if($totleLLQty == $totleOrderQty ||($totleLLQty != $totleOrderQty && $totleOrderQty > $totleTStockQty) ) {
			continue;
		}
		$count = 0;
		$getUnionStuffSql = "SELECT U.uStuffId, S.StuffCname, T.mainType, S.Picture,P.Property
							 FROM $DataIn.cg1_stuffunite U
							 LEFT JOIN $DataIn.stuffdata S ON S.StuffId = U.uStuffId
							 LEFT JOIN $DataIn.stufftype T ON T.TypeId = S.TypeId
							 LEFT JOIN  $DataIn.stuffproperty P ON P.StuffId=U.uStuffId
							 WHERE U.POrderId = '$POrderId' AND U.ProductId = '$ProductId'
							 AND U.StuffId = '$mainStuffId'
							 GROUP BY U.uStuffId
							 ORDER BY T.mainType";
		$unionStuffResult = mysql_query($getUnionStuffSql);
		while ($unionStuffRow = mysql_fetch_assoc($unionStuffResult)) {
			$uStuffId = $unionStuffRow["uStuffId"];
			    $orderQty="";
            $orderQtySql = mysql_fetch_assoc(mysql_query("Select OrderQty, StockId From $DataIn.cg1_stocksheet Where StuffId='$uStuffId' and POrderId = '$POrderId'"));
            $orderQty = $orderQtySql["OrderQty"];
            $uStockId = $orderQtySql["StockId"];
            
            $llqtyOne = 0;
			$checkLL = mysql_query("select sum(L.Qty) as Qty 
			from $DataIn.ck5_llsheet L 
			where L.StockId='$uStockId'");
			if ($checkLLRow = mysql_fetch_array($checkLL)) {
				$llqtyOne = $checkLLRow["Qty"];
			}
/*
			if ($LoginNumber == 11965 && $POrderId == "201507180401") {
				//echo("L:$llqtyOne O:$orderQty  S:$uStockId \n");
			}
*/
if($orderQty == ""){ continue; }

			if ($llqtyOne>=$orderQty)  continue;
			
			  $tStockQtySql = mysql_fetch_assoc(mysql_query("Select tStockQty From $DataIn.ck9_stocksheet Where StuffId = '$uStuffId'"));
			$tStockQty=$tStockQtySql["tStockQty"];
			if ($tStockQty<=0 || $tStockQty<$orderQty) continue;

            
			 $count ++;
		}
		
		if ($count <=0 ) {continue;}
// 		$testArr[]=array("$POrderId","$mainStuffId","$ProductId");
		$overQty +=  $thisWeek > $Weeks ? $mainQty:0;
		$SumTotalValue += $mainQty;
		$blCounts ++;
	}	
	$overQty = number_format($overQty);
	if ($overQty <= 0 ) {$overQty = "";}
	$SumTotalValue = number_format($SumTotalValue);
	
			if ($LoginNumber == 11965) {
				//$SumTotalValue  = "11111";
				//echo(json_encode($testArr));
				
				//echo("L:$llqtyOne O:$orderQty  S:$uStockId \n");
			}
?>