<?php
	
	include_once "../../basic/parameter.inc";
	
	$totleStatistic = 0;
	
	$sendOutStuffSql = "Select A.StuffId, C.ProductId, C.POrderId, D.StuffCName, S.CompanyId, P.Forshort, B.OrderQty, Y.OrderPO, B.StockId, D.Picture
						From $DataIn.yw1_ordersheet C
						Left Join $DataIn.cg1_stocksheet B On B.POrderId = C.POrderId
						Left Join $DataIn.pands_unite A On C.ProductId = A.ProductId
						Left Join $DataIn.stuffdata D On D.StuffId = A.StuffId
						Left Join $DataIn.stuffType E On E.TypeId = D.TypeId
						Left Join $DataIn.stuffproperty F On F.StuffId = A.uStuffId
						Left Join $DataIn.bps S On S.StuffId = A.StuffId
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
						Left Join $DataIn.yw1_ordersheet Y On Y.POrderId = C.POrderId 
						Where C.scFrom != '0'
						And C.Estate != '0'
						And E.mainType < 2
						And F.property is NULL
						Group by C.POrderId";
	$sendOutCount = 0;
	$sendOutStuffResult = mysql_query($sendOutStuffSql);
	while($sendOutStuffRow = mysql_fetch_assoc($sendOutStuffResult))
	{
		$mainStuffId = $sendOutStuffRow["StuffId"];
		$ProductId = $sendOutStuffRow["ProductId"];
		$pOrderId = $sendOutStuffRow["POrderId"];
		
		$qtyCheckSql = "SELECT SUM( B.OrderQty ) AS OrderQty, IFNULL( SUM( L.Qty ) , 0 ) AS llQty, SUM( K.tStockQty ) AS tStockQty
						FROM $DataIn.pands_unite A
						LEFT JOIN $DataIn.cg1_stocksheet B ON B.StuffId = A.uStuffId 
						LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = B.StockId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = A.uStuffId
						WHERE A.ProductId =  '$ProductId' and B.POrderId = '$pOrderId'";
		$qtyCheckResult = mysql_query($qtyCheckSql);
		$qtyCheckRow =mysql_fetch_assoc($qtyCheckResult);
		$totleLLQty = $qtyCheckRow["llQty"];
		$totleOrderQty = $qtyCheckRow["OrderQty"];
		$totleTStockQty = $qtyCheckRow["tStockQty"];
		
		//echo "ll:$totleLLQty  order:$totleOrderQty   tStock:$totleTStockQty   $pOrderId  $ProductId<br>";
		
		if($totleLLQty == $totleOrderQty ||($totleLLQty != $totleOrderQty && $totleOrderQty > $totleTStockQty) )
		{
			continue;
		}

		$sendOutCount++;
	}
	
	$totleStatistic += $sendOutCount;
	
	echo json_encode(array("sendOut"=>"$sendOutCount", "totle"=>"$totleStatistic"));
	
?>