<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$companyId = $_POST["CompanyId"];
	$productType = $_POST["Type"];
	$assblemState = $_POST["assblemState"];
	
	/*
$companyId = "1004";
	$productType = "8048";
	$assblemState = "1";
*/
	
	if($assblemState == "0")
	{
		$searchRow = "S.Estate= 2 ";
	}
	else
	{
		$searchRow = "S.scFrom > 0 AND S.Estate= 1 ";
	}
		
	$getProductListSql="SELECT 
S.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.ShipType,S.scFrom,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,U.Name AS Unit,M.OrderDate,S.dcRemark,S.sgRemark,M.OrderDate,S.PackRemark
						FROM $DataIn.yw1_ordersheet S
						LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
						LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
						WHERE
						$searchRow   
						AND P.TypeId='$productType'
						AND M.CompanyId='$companyId'
						ORDER BY M.OrderDate";
	//echo $getProductListSql;
	$productList = array();
	$productListResult = mysql_query($getProductListSql);
	while($productRow = mysql_fetch_assoc($productListResult))
	{
		$productName = $productRow["cName"];
		$productCode = $productRow["eCode"];
		$testStandard = $productRow["TestStandard"];
		$qty = $productRow["Qty"];
		$shipType = $productRow["ShipType"];
		$orderDate = $productRow["OrderDate"];
		$dcRemark = $productRow["dcRemark"];
		$packRemark = $productRow["PackRemark"];
		$sgRemark = $productRow["sgRemark"];
		$productId = $productRow["ProductId"];
		$orderPo = $productRow["OrderPO"];
		$pOrderId = $productRow["POrderId"];
		
		$checkSpliteSql = "Select * From $DataIn.yw10_ordersplit Where POrderId = '$pOrderId'";
		$checkSpliteResult = mysql_query($checkSpliteSql);
		if($checkSpliteRow = mysql_fetch_assoc($checkSpliteResult))
		{
			$isSplite = $checkSpliteRow["Estate"];
		}
		else
		{
			$isSplite = "1";
		}
		
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
		
		$overDay = (strtotime(date("Y-m-d")) - strtotime($orderDate))/24/3600;
		
		$canProducting = "yes";
		//判断配件状态
		/*
$getStuffStateSql = "Select A.OrderQty, A.StockId, B.tStockQty From $DataIn.cg1_stocksheet A
							 Left Join $DataIn.ck9_stocksheet B On B.StuffId = A.StuffId
							 Left Join $DataIn.stuffdata C On C.StuffId = A.StuffId
							 Left Join $DataIn.stufftype D On D.TypeId = C.TypeId
							 Where A.POrderId = '$pOrderId'
							 And D.mainType in (0,1)"; 
		//echo $getStuffStateSql;
		$stuffStateResult = mysql_query($getStuffStateSql);
		while($stuffStateRow = mysql_fetch_assoc($stuffStateResult))			 
		{
			$cgQty = $stuffStateRow["OrderQty"];
			$tQty = $stuffStateRow["tStockQty"];
			$stockId = $stuffStateRow["StockId"];
			
			$llQtyResult = mysql_query("SELECT SUM( Qty ) AS llQty FROM  $DataIn.ck5_llsheet WHERE StockId = '$stockId'");
			$llQtyRow = mysql_fetch_assoc($llQtyResult);
			$llQty = $llQtyRow["llQty"];
			
			//echo "StockId:$stockId cgQty:$cgQty tQty:$tQty llQty:$llQty <br>";
		}
*/

		//先比较领料数
		$llCheckStateSql = "Select SUM(A.OrderQty) as OrderQty, SUM(D.Qty) as llQty From $DataIn.cg1_stocksheet A
							Left Join $DataIn.stuffdata C On C.StuffId = A.StuffId
							Left Join $DataIn.ck5_llsheet D On D.StockId = A.StockId
							Left Join $DataIn.stufftype E On E.TypeId = C.TypeId
							Where A.POrderId = '$pOrderId'
							And E.mainType in (0,1)";
								 
		$llCheckStateResult = mysql_query($llCheckStateSql);
		$llCheckStateRow = mysql_fetch_assoc($llCheckStateResult);
		$orderQty = $llCheckStateRow["OrderQty"];
		$llQty = $llCheckStateRow["llQty"];
		if($orderQty != $llQty)
		{
			$canProducting = "no";
		}
		else
		{
			$tStockStateSql = "Select B.StuffId, B.OrderQty, C.tStockQty From $DataIn.cg1_stocksheet B
							   Left Join $DataIn.ck9_stocksheet C On C.StuffId = B.StuffId
							   Where B.POrderId = '$pOrderId'
							   And C.tStockQty < B.OrderQty";
			//echo $tStockStateSql;
			$tStockStateResult = mysql_query($tStockStateSql);
			if(mysql_num_rows($tStockStateResult) == 0)
			{
				$canProducting = "no";
			}
		}
		
		
		$productList[] = array("productName"=>"$productName", "productCode"=>"$productCode", "testStandard"=>"$testStandard", "qty"=>"$qty", "shipType"=>"$shipType", "orderDate"=>"$orderDate", "sgRemark"=>"$sgRemark", "packmark"=>"$packRemark", "productId"=>"$productId", "OrderPO"=>"$orderPo", "POderId"=>"$pOrderId", "OverDay" => "$overDay", "isSplite"=>"$isSplite", "ProductState" => "$canProducting", "ProductInBox" => "$productInBox");
	}
	
	echo json_encode($productList);
	
?>