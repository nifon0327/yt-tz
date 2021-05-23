<?php
class Mco{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $invoice = $labelInfomation['POrderId'];
        //$shipId = $labelInfomation['shipId'];
        $label = $originalLabel;
        /*获取货运方式*/
        // $shipTypeResult = mysql_query("Select B.name From $DataIn.ch1_shipsplit A
        //             Left Join $DataPublic.ch_shiptype B On A.ShipType = B.Id
        //             Where A.POrderId = '$POrderId' And A.ShipId='$shipId'");

        $invoiceWiseSql = "SELECT Wise FROM $DataIn.ch1_shipmain Where InvoiceNO = '$invoice' Limit 1";
        $invoiceWiseResult = mysql_query($invoiceWiseSql);
        $invoiceWise = mysql_fetch_assoc($invoiceWiseResult);

        $shipType = (strpos(strtoupper($invoiceWise["Wise"]), "SEA") != '')?"SEA":"";  

        $label = str_replace('*shipType', $shipType, $label);
        return $label;
    }
}
?>