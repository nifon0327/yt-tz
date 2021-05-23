<?php
class BauLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $productId = $labelInfomation['ProductId'];
        
        $label = $originalLabel;
        $cNmaeSql = "SELECT cName From $DataIn.productdata Where ProductId='$productId'";
        $cNmaeResult = mysql_query($cNmaeSql);
        $cNameRow = mysql_fetch_assoc($cNmaeResult);
        $cName = $cNameRow['cName'];
        $type = substr($cName, strlen($cName)-2, 2);
        $labelType = strtoupper($type) == "/T"?'T':'K';

        $label = str_replace('*label', $labelType, $label);
        return $label;
    }
}