<?php 
include "../basic/parameter.inc";
include "../basic/config.inc";


$today=date("Y-m-d");

$mySql="SELECT G.mStockId,S.Id,S.StockId,S.DeliveryDate AS OldDeliveryDate,SM.DeliveryDate,IFNULL(D.ReduceWeeks,0) AS ReduceWeeks 
		FROM $DataIn.cg1_semifinished G 
		LEFT JOIN $DataIn.cg1_stocksheet S ON G.StockId=S.StockId 
		LEFT JOIN $DataIn.cg1_stocksheet SM ON SM.StockId=G.mStockId
		LEFT JOIN $DataIn.semifinished_deliverydate D ON D.mStuffId=SM.StuffId
		WHERE S.mid>0 and S.DeliveryWeek=0 and S.level>1 AND S.rkSign>0 AND SM.DeliveryWeek>0 group by SM.StockId";
$myResult = mysql_query($mySql,$link_id);
while($myRow = mysql_fetch_array($myResult)){
   $mStockId    =$myRow['mStockId'];
   $DeliveryDate=$myRow['DeliveryDate'];
   $ReduceWeeks =$myRow['ReduceWeeks'];
   
   
   $jhDays=$ReduceWeeks*7;
   
   $NewDeliveryDate=date("Y-m-d",strtotime("$DeliveryDate  $jhDays  day"));
   
    if ($today>$NewDeliveryDate)
    {
	   $DeliveryDate=$today; 
    }
    
   $sId=$myRow['Id'];
   $StockId=$myRow['StockId'];
   $OldDeliveryDate=$myRow['OldDeliveryDate'];
   

   $DeliveryDateSql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate='$NewDeliveryDate' WHERE Id='$sId'";
   $DeliveryDateResult = mysql_query($DeliveryDateSql);
   
   /*
   if (mysql_affected_rows()>0){
	   echo "采购流水号( $StockId ）的交期（ $OldDeliveryDate ）已更改为：$NewDeliveryDate <br>";
   }
   */
}


$mySql="SELECT G.StockId,G.DeliveryDate AS OldDeliveryDate,IFNULL(PI.Leadtime,P.Leadtime) AS Leadtime,IFNULL(D.ReduceWeeks,1) AS ReduceWeeks 
		FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId 
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
		LEFT JOIN $DataIn.yw3_pileadtime P ON P.POrderId=S.POrderId 
		LEFT JOIN $DataIn.yw2_cgdeliverydate D ON D.POrderId=S.POrderId 
		WHERE G.mid>0 and G.DeliveryWeek=0 and G.level=1 AND IFNULL(PI.Leadtime,P.Leadtime) IS NOT NULL group by G.StockId";
$myResult = mysql_query($mySql,$link_id);
while($myRow = mysql_fetch_array($myResult)){

   $StockId    =$myRow['StockId'];
   $DeliveryDate=$myRow['Leadtime'];
   $ReduceWeeks =$myRow['ReduceWeeks'];
   
   
   $jhDays=$ReduceWeeks*7;
   
   $NewDeliveryDate=date("Y-m-d",strtotime("$DeliveryDate  $jhDays  day"));
   
    if ($today>$NewDeliveryDate)
    {
	   $DeliveryDate=$today; 
    }
    
   $StockId=$myRow['StockId'];
   $OldDeliveryDate=$myRow['OldDeliveryDate'];
   
   
   $DeliveryDateSql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate='$NewDeliveryDate' WHERE StockId='$StockId'";
   $DeliveryDateResult = mysql_query($DeliveryDateSql);
   /*
   if (mysql_affected_rows()>0){
	   echo "采购流水号( $StockId ）的交期（ $OldDeliveryDate ）已更改为：$NewDeliveryDate <br>";
   }
   */
}


?>