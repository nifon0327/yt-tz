<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$OperationResult = "Y";
$Log_Funtion="是否出货";
$Operator=$Login_P_Number;

$IdsArr = explode("|", $Ids);

for ($i= 0;$i< count($IdsArr); $i++){
    $Id= $IdsArr[$i];
    
    $upData = mysql_fetch_array(mysql_query("SELECT SP.Qty AS thisQty, S.POrderId,S.ProductId, SP.shipSign
            FROM $DataIn.ch1_shipsplit   SP
            LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
            WHERE SP.Id=$Id ",$link_id));

    $thisQty=$upData["thisQty"];
    $POrderId=$upData["POrderId"];
    $ProductId=$upData["ProductId"];
    
    $shipSign=$upData["shipSign"];
    
    $CheckrkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS rkQty
            FROM $DataIn.yw1_orderrk R
            WHERE R.POrderId='$POrderId' AND R.ProductId = '$ProductId' ",$link_id));
    $rkQty=$CheckrkQty["rkQty"];
    
    $CheckShipSignQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS shipQty
            FROM $DataIn.ch1_shipsplit
            WHERE POrderId='$POrderId'  AND shipSign = 1 AND Id !='$Id' ",$link_id));
    $ShipSignQty = $CheckShipSignQty["shipQty"]; //可以出，或者已经出货数量
    

    if(($rkQty-$ShipSignQty)>=$thisQty){
        //可以出

        //$CheckResult=mysql_fetch_array(mysql_query("SELECT shipSign FROM $DataIn.ch1_shipsplit
        //        WHERE Id=$Id",$link_id));
        //$shipSign=$CheckResult["shipSign"];
        
        if($shipSign ==1){
            $changeShipSign = 0 ;
        }else{
            $changeShipSign = 1 ;
        }
        $sql = "UPDATE $DataIn.ch1_shipsplit SET shipSign='$changeShipSign' WHERE  Id='$Id' ";
        //echo $sql;
        $result = mysql_query($sql,$link_id);
        if ($result){
            $OperationResult='Y';
        } else {
            $OperationResult='N';
        }
    }  
}

echo $OperationResult;
?>