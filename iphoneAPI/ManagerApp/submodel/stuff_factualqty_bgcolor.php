<?php
//配件采购周期与订单周期为同一周时显示背景颜色
$FactualQty_Color="";
if ($POrderId=="") $POrderId=substr($StockId,0,12);
$checkCgWeeks=mysql_query("SELECT ReduceWeeks FROM $DataIn.yw2_cgdeliverydate WHERE POrderId='$POrderId'",$link_id);
 if($checkCgWeeksRow = mysql_fetch_array($checkCgWeeks)){
     $FactualQty_Color=$checkCgWeeksRow["ReduceWeeks"]==0?"#CCE3F0":"";
 }
?>