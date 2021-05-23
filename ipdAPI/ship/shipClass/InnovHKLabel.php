<?php
class InnovHKLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $ProductId = $labelInfomation['ProductId'];
        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];
        $innerCodeLen = strlen($labelInfomation['Code']);
        $labeltype = $innerCodeLen == 12 ? 'E' : 'N';

        $label = $originalLabel;

        $noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='$type'";
        
        $noteResult = mysql_query($noteContentSql);
        $noteRow = mysql_fetch_assoc($noteResult);
        $spec = $noteRow['note'];

        $label = str_replace('*specInvoiceNO', $spec, $label);
        $label = str_replace('*labeltype', $labeltype, $label);
        return $label;
    }
}
?>