<?php
class AvenirLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $systemCode = $labelInfomation['systemCode'];
        $label = $originalLabel; 

        $label = str_replace('*systemCode', $systemCode, $label);
        return $label;
    }
}
?>