<?php
class MLineLable{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        //$customPO = 'ISY-0660';
        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];
        $noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='$type'";
        $noteCOntentResult = mysql_query($noteContentSql);
        $noteRow = mysql_fetch_assoc($noteCOntentResult);
        $customPO = $noteRow['note'];
        $label = str_replace('*POCustomer', $customPO, $originalLabel);
        return $label;
    }
}

?>