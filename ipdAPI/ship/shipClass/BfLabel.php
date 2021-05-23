<?php
class BfLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){

        $POrderId = $labelInfomation['POrderId'];
        $ProductId = $labelInfomation['ProductId'];
        $label = $originalLabel;

        $productTypeCode = mysql_query("select Description,pRemark From $DataIn.productdata Where ProductId = '$ProductId'");
        $pRemarkResult = mysql_fetch_assoc($productTypeCode);
        $productType =  $pRemarkResult["pRemark"];
        $pMark = explode('|', $productType);
        $label = str_replace("*producttype", $pMark[0], $label);
        $label = str_replace("*DeviceType", $pMark[1], $label);
        $label = str_replace("*material", $pMark[2], $label);

        return $label;
    }
}

?>