<?php
class SasLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $label = $originalLabel;
        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];

        if(strstr($label, "sas-doro")){
            $descriptionArray = explode('|', $labelInfomation['description']);
            $productName = $descriptionArray[0];
            $color = $descriptionArray[1];
            $cumCode = $descriptionArray[2];
            $cumPO = explode('(', $labelInfomation['OrderPO']);
            $cumPO = substr($cumPO[1], 0, strlen($cumPO[1])-1);  

            $label = str_replace('*customOrderPO', $cumPO, $label);
            $label = str_replace('*customECode', $productName, $label);
            $label = str_replace('*customProductCode', $cumCode, $label);
            $label = str_replace('*color', $color, $label);

        }else{
            $noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='$type'";
            $noteResult = mysql_query($noteContentSql);
            if($noteRow = mysql_fetch_assoc($noteResult)){
                $note = $noteRow['note'];
                $label = str_replace('*labeltype', $note, $label);
            }else{
                $type = strlen($labelInfomation['Code'])==12?'E':'N';
                $label = str_replace('*labeltype', $type, $label);
            }
        }
        

        return $label;
    }
}
?>