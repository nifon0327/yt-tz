<?php
class SmartsLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $labelType = strlen($labelInfomation['Code'])==12?'N':'T';
        $label = $originalLabel;
        $label = str_replace('*labeltype', $labelType, $label);
        $shpTypeContent = 'R';

       
        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];
        $noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='$type'";
        $noteResult = mysql_query($noteContentSql);
        $noteRow = mysql_fetch_assoc($noteResult);
        $spec = $noteRow['note'];
        if($spec != ''){
             $shpTypeContent = $spec;
        }

        $label = str_replace('*shipType', $shpTypeContent, $label);
        return $label;
    }
}
?>