<?php
//获取订单利润
$CostRow= mysql_fetch_array(mysql_query("SELECT getOrderProfit($POrderId) AS Profit",$link_id));
$CostValue=$CostRow['Profit'];
$CostArray=explode('|', $CostValue);
$profitRMB2=$CostArray[0];
$profitRMB2PC=$CostArray[1];
$GrossProfit=$CostArray[2];
$profitColor=$CostArray[3];
?>