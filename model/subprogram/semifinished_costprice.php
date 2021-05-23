<?php
//取得半成品实际价格

$semiResult = mysql_query("SELECT A.Relation,S.Price
FROM  $DataIn.cg1_semifinished   A 
LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId= A.StockId
WHERE  A.mStockId='$StockId' ",$link_id);

?>