<?php
class RjLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $label = $originalLabel; 

        $noteContentSql = "SELECT PackRemark From $DataIn.yw1_ordersheet WHERE POrderId='$POrderId'";
        //echo $noteContentSql;
        $noteContentResult = mysql_query($noteContentSql);
        $noteContent = mysql_fetch_assoc($noteContentResult);
        $note = $noteContent['PackRemark'];

        $leftMark = strpos($note, '[');
        $rightMark = strpos($note, ']');
        $result = substr($note, intval($leftMark)+1,intval($rightMark)-intval($leftMark)-1);
        //echo $result;
        $label = str_replace('*oPo', $result, $label);
        return $label;
    }
}
?>