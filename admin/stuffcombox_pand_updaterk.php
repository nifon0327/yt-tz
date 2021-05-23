<?php
 if ($mStockId>0 && $mStuffId>0){

	 $CheckComboxRow = mysql_fetch_array(mysql_query("SELECT MIN(rkQty) AS rkQty,A.StockId,A.StuffId,A.Price
	     FROM (
		 SELECT S.StockId,S.StuffId,G.Price, IFNULL(SUM(K.Qty),0) AS rkQty 
		 FROM $DataIn.cg1_stuffcombox  S 
		 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
		 LEFT JOIN $DataIn.ck1_rksheet  K ON K.StockId = S.StockId
		 WHERE S.mStuffId='$mStuffId' AND S.mStockId='$mStockId' GROUP BY S.StockId) A",$link_id)); 
    $minRkQty  =  $CheckComboxRow["rkQty"];
    $comboxStockId = $CheckComboxRow["StockId"];
    $comboxStuffId = $CheckComboxRow["StuffId"];
    $comboxPrice   = $CheckComboxRow["Price"];
	 // 子配件的入库数量大于 母配件的入库数量
	 if($minRkQty>0){
		 $mRkRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$mStockId' order by StockId",$link_id));
		 $mRkQty = $mRkRow["Qty"];
		 if($minRkQty!=$mRkQty){
			 
			 if($minRkQty>$mRkQty && $mRkQty==0){
				 
				 $InRkSql = "INSERT INTO $DataIn.ck1_rksheet SELECT NULL, Mid, sPOrderId,'$mStockId', '$mStuffId', '$comboxPrice', '$minRkQty', '0', '1', '0', '1', '0', '母配件自动入库', '0', '1', '0', '$Operator', NOW(), '$Operator', NOW(), NOW(), '$Operator' FROM $DataIn.ck1_rksheet WHERE StockId ='$comboxStockId' AND StuffId = '$comboxStuffId'";
				 $InRkResult = mysql_query($InRkSql);
				 
			  } 
			  if($minRkQty>$mRkQty && $mRkQty!=0){
			      $thisRkQty = $minRkQty-$mRkQty;
				  $UpRkSql = "UPDATE $DataIn.ck1_rksheet SET Qty= Qty + $thisRkQty,llSign = 1  WHERE StockId = '$mStockId' AND StuffId = '$mStuffId' ";
				  $upRkResult = mysql_query($UpRkSql);  
			  }
		  } 
	  }
 }

?>