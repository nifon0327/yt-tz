<?php
class MELabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $eCode = $labelInfomation['eCode'];
        $label = $originalLabel;
        
        $names = explode('(', $eCode);
        $mainName = $names[0];
        $subName = str_replace(')', '', $names[1]);

        $label = str_replace('*mainName', $mainName, $label);
        $label = str_replace('*subName', $subName, $label);
        return $label;
    }
}
?>