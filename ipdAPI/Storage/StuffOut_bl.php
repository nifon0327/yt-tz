<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$stuffOutArray = array();
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
	//echo $sendOutStuffSql;
	$sendOutStuffResult = mysql_query($sendOutStuffSql);
	while($sendOutStuffRow = mysql_fetch_assoc($sendOutStuffResult))
	{
		$mainStuffId = $sendOutStuffRow["StuffId"];
		$ProductId = $sendOutStuffRow["ProductId"];
		$pOrderId = $sendOutStuffRow["POrderId"];
		$mainStuffName = $sendOutStuffRow["StuffCName"];
		$companyId = $sendOutStuffRow["CompanyId"];
		$cmpanyName = $sendOutStuffRow["Forshort"];
		$mainQty = $sendOutStuffRow["OrderQty"];
		$orderPO = $sendOutStuffRow["OrderPO"];
		$picture = $sendOutStuffRow["Picture"];
		
		$qtyCheckSql = "SELECT SUM( B.OrderQty ) AS OrderQty, IFNULL( SUM( L.Qty ) , 0 ) AS llQty, SUM( K.tStockQty ) AS tStockQty
						FROM $DataIn.pands_unite A
						LEFT JOIN $DataIn.cg1_stocksheet B ON B.StuffId = A.uStuffId 
						LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = B.StockId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = A.uStuffId
						WHERE A.ProductId =  '$ProductId' and B.POrderId = '$pOrderId' AND A.StuffId =  '$mainStuffId'";
		$qtyCheckResult = mysql_query($qtyCheckSql);
		$qtyCheckRow =mysql_fetch_assoc($qtyCheckResult);
		$totleLLQty = $qtyCheckRow["llQty"];
		$totleOrderQty = $qtyCheckRow["OrderQty"];
		$totleTStockQty = $qtyCheckRow["tStockQty"];
		
		//$orderQtySql = mysql_query("Select Sum(OrderQty) as")
		
		
		//echo "ll:$totleLLQty  order:$totleOrderQty   tStock:$totleTStockQty   $pOrderId  $ProductId<br>";
		
		if($totleLLQty == $totleOrderQty ||($totleLLQty != $totleOrderQty && $totleOrderQty > $totleTStockQty) )
		{
			continue;
		}
		
		
		//获取关联的配件
		$unionStuffArray = array();
		$getUnionStuffSql = "SELECT U.uStuffId, S.StuffCname, T.mainType, S.Picture
							 FROM $DataIn.pands_unite U
							 LEFT JOIN $DataIn.stuffdata S ON S.StuffId = U.uStuffId
							 LEFT JOIN $DataIn.stufftype T ON T.TypeId = S.TypeId
							 WHERE U.ProductId = '$ProductId'
							 AND U.StuffId = '$mainStuffId'
							 GROUP BY U.uStuffId
							 ORDER BY T.mainType";
		//echo $getUnionStuffSql."<br>";
		$unionStuffResult = mysql_query($getUnionStuffSql);
		while($unionStuffRow = mysql_fetch_assoc($unionStuffResult))
		{
			$uStuffId = $unionStuffRow["uStuffId"];
			$uStuffName = $unionStuffRow["StuffCname"];
			$uPicture = $unionStuffRow["Picture"];
            $StockId = $unionStuffRow["StockId"];

            //获取数量
            $orderQtySql = mysql_fetch_assoc(mysql_query("Select OrderQty, StockId From $DataIn.cg1_stocksheet Where StuffId='$uStuffId' and POrderId = '$pOrderId'"));
            $orderQty = $orderQtySql["OrderQty"];

            if($orderQty == ""){
            	continue;
            }
            $uStockId = $orderQtySql["StockId"];

            $tStockQtySql = mysql_fetch_assoc(mysql_query("Select tStockQty From $DataIn.ck9_stocksheet Where StuffId = '$uStuffId'"));
			$tStockQty=$tStockQtySql["tStockQty"];


			$llStockQtySql = mysql_fetch_assoc(mysql_query("Select Sum(Qty) as llQty From $DataIn.ck5_llsheet Where StuffId = '$uStuffId' and StockId = '$uStockId' and Estate=0"));
			$llQty = $llStockQtySql["llQty"] == ""?"0":$llStockQtySql["llQty"];

            //是否可备料
            $unionStuffArray[] = array("uStuffName"=>"$uStuffName", "uStuffId"=>"$uStuffId", "orderQty"=>"$orderQty", "blQty"=>"$llQty", "tStockQty"=>"$tStockQty", "uPicture"=>"$uPicture", "StockId"=>"$uStockId");			
		}
		
		
		if(count($unionStuffArray)  == 0 ){
			continue;
		}

		$stuffOutArray[] = array("mainStuffName"=>"$mainStuffName", "mainStuffId"=>"$mainStuffId", "ProductId"=>"$ProductId", "POrderId"=>"$pOrderId", "CompanyId"=>"$companyId", "CompanyName"=>"$cmpanyName", "Qty"=>"$mainQty", "OrderPO"=>"$orderPO", "Picture"=>"$picture","unionStuffs"=>$unionStuffArray);

	}
	
	echo json_encode($stuffOutArray);
	//print_r($stuffOutArray);
	//echo count($stuffOutArray);
	
?>