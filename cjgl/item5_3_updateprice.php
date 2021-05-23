<?php
 $mStockIds=$Price_mStockId;
 $getPriceSql = "SELECT G.Price,S.mStockId FROM $DataIn.cg1_semifinished S 
                 LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId = S.StockId
                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
                 LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
                 WHERE S.mStockId IN ($mStockIds) AND T.mainType ='3'";  
                           
 $getPriceResult = $myPDO->query($getPriceSql);
 while($getPriceRow = $getPriceResult->fetch(PDO::FETCH_ASSOC)){
 
     $jgPrice  = $getPriceRow["Price"];
     $thismStockId = $getPriceRow["mStockId"];
	 
     $UpdatePriceSql = "UPDATE $DataIn.cg1_stocksheet 
                        SET Price = '$jgPrice' WHERE StockId = '$thismStockId'";
     $PriceResult = $myPDO->exec($UpdatePriceSql);   
  }
 	 $getPriceResult=null;
	 $getPriceRow=null;
?>