<?php
/*
$CheckOrderResult=mysql_query("SELECT  S.POrderId,S.Qty,S.ShipType FROM $DataIn.yw1_ordersheet  S
 LEFT JOIN $DataIn.ch1_shipsplit  P ON P.POrderId=S.POrderId
 WHERE S.Estate=2 AND S.scFrom=0  AND P.POrderId IS NULL",$link_id);
while($CheckOrderRow=mysql_fetch_array($CheckOrderResult)){
               $checkPOrderId=$CheckOrderRow["POrderId"];
               $checkQty=$CheckOrderRow["Qty"];
               $checkShipType=$CheckOrderRow["ShipType"];
               $In_Sql="INSERT INTO $DataIn.ch1_shipsplit(Id,POrderId,ShipId,Qty,ShipType,Estate,OrderSign)VALUES(NULL,'$checkPOrderId','0','$checkQty','$checkShipType','1','0')";
               $In_Result=@mysql_query($In_Sql);
}
*/
/*
$CheckOrderSign="UPDATE  $DataIn.ch1_shipsplit  SP
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SP.POrderId
SET SP.ShipType=S.ShipType
WHERE S.Estate=2 AND S.scFrom=0  AND SP.OrderSign=0 AND SP.ShipType!=S.ShipType  AND S.ShipType>=0 AND S.ShipType<=1000";
*/
if ($DataIn == 'ac') {
    $In_Sql = "INSERT INTO $DataIn.ch1_shipsplit SELECT NULL,POrderId,'0',Qty,'7','1','0','0','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator'  FROM $DataIn.yw1_ordersheet  S WHERE S.Estate=2 AND S.scFrom=0 AND NOT EXISTS(SELECT P.POrderId FROM $DataIn.ch1_shipsplit  P WHERE P.POrderId=S.POrderId)";
    //echo $In_Sql;
}
else {
    $In_Sql = "INSERT INTO $DataIn.ch1_shipsplit SELECT NULL,POrderId,'0',Qty,'7','1','0' FROM $DataIn.yw1_ordersheet  S WHERE S.Estate=2 AND S.scFrom=0 AND NOT EXISTS(SELECT P.POrderId FROM $DataIn.ch1_shipsplit  P WHERE P.POrderId=S.POrderId)";
}
$In_Result = @mysql_query($In_Sql);
//echo $In_Sql;
$CheckOrderSign = "UPDATE  $DataIn.ch1_shipsplit  SP  
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SP.POrderId
SET SP.ShipType='7'
WHERE S.Estate=2 AND S.scFrom=0  AND SP.OrderSign=0 AND (SP.ShipType is Null or SP.ShipType='')  AND S.ShipType>=0 AND S.ShipType<=1000";
$CheckOrderSignResult = @mysql_query($CheckOrderSign);
?>