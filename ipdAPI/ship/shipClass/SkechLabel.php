<?php
class SkechLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $label = $originalLabel;
        $innerName = explode('-', $labelInfomation['eCode']);
        //print_r($innerName);
        $innerName = $innerName[0].'-MC-'.$innerName[1].'-'.$innerName['2'];
        $label = str_replace('*innerCode', $innerName, $label);
        return $label;
    }
}
?>