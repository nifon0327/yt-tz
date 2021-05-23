<?php
if($Id>0){
    //出货给研砼上海,研砼贸易自动入库
    $DateYmd = date("Ymd");
    $Date = date("Y-m-d");
    $RkMid = 0 ;
    $MaxBillNumberRow = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS MaxBillNumber FROM $DataIn.sale_rkmain 
    WHERE  BillNumber LIKE'$DateTemp%'",$link_id));
    $MaxBillNumber  = $MaxBillNumberRow["MaxBillNumber"];
    if($MaxBillNumber){
        $MaxBillNumber = $MaxBillNumber+1;
    }else{
        $MaxBillNumber = $DateTemp."0001";
    }
	$CheckResult = mysql_query("SELECT S.POrderId,Y.StockId,S.CompanyId,S.ProductId,S.Price
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN $DataIn.sale_stocksheet S ON S.StockId = Y.StockId
	WHERE M.Id ='$Id'  AND Y.StockId>0",$link_id);
	while($CheckRow = mysql_fetch_array($CheckResult)){
		$thisPOrderId = $CheckRow["POrderId"];
		$thisStockId  = $CheckRow["StockId"];
		$thisPrice    = $CheckRow["Price"];
		$thisCompanyId = $CheckRow["CompanyId"];
		$ProductId= $CheckRow["ProductId"];
		$CheckShipRow = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS ShipQty FROM $DataIn.ch1_shipsheet 
		WHERE POrderId ='$thisPOrderId'",$link_id));
		$thisShipQty  = $CheckShipRow["ShipQty"];
		//入库数量 $thisStockId
		$CheckRkRow = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS rkQty FROM $DataIn.sale_rksheet 
		WHERE StockId ='$thisStockId'",$link_id));
		$thisRkQty= $CheckRkRow["rkQty"];
		$thisQty = $thisShipQty -$thisRkQty;
		if($thisShipQty>0 && $thisQty>0){

			 if($RkMid==0){
				 $inRecode="INSERT INTO $DataIn.sale_rkmain (Id,BillNumber,GysNumber,CompanyId,Remark,Estate,Locks,Date,Operator) 
				 VALUES (NULL,'$MaxBillNumber','$MaxBillNumber','$thisCompanyId','','1','0','$Date','$Operator')";
				 $inAction = mysql_query($inRecode);
			     $RkMid    = mysql_insert_id();
			 }
			 if($RkMid>0){
					$addRecodes="INSERT INTO $DataIn.sale_rksheet(Id,Mid,StockId,ProductId,Price,Qty,gys_Id,Type,Locks,Estate,Date,
					Operator,PLocks,creator,created,modifier,modified)VALUES(NULL,'$RkMid','$thisStockId','$ProductId','$thisPrice',
	                '$thisQty','0','1','0','1','$Date','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
					$addAction = mysql_query($addRecodes);
			 }
		}
	}
}
?>