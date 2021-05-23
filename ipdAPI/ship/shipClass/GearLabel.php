<?php
class GearLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $label = $originalLabel;
        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];
        $POrderId = $labelInfomation['POrderId'];
        $ProductId = $labelInfomation['ProductId'];

        //外箱尺寸
        $boxOriginalSize = $labelInfomation['boxSize'];
        $boxsizeArray = explode('×', $boxOriginalSize);
        $boxsizeType = "L ".$boxsizeArray[0]."× W ".$boxsizeArray[1]."× H".$boxsizeArray[2];
        //echo $boxsizeType.'<br>';
        $label = str_replace('*boxll', $boxsizeType, $label);

        //ean13_条码
        $codeResult = mysql_query("SELECT D.StuffId, D.StuffCname
                                  FROM $DataIn.cg1_stocksheet P 
                                  INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
                                  INNER JOIN $DataIn.stufftype T On T.TypeId = D.TypeId
                                  WHERE P.POrderId = '$POrderId'
                                  AND D.TypeId in (9124, 9033)");

        $targetCode = '';
        $innCode = '';
        $code128 = '';
        while($codeRow = mysql_fetch_assoc($codeResult)){
            //echo $codeRow['StuffCname'];
            $stuffnameArray = explode('-', $codeRow['StuffCname']);
            $tmpCode = $stuffnameArray[count($stuffnameArray)-1];
            if(strlen($tmpCode) == 13){
                $targetCode = $tmpCode;
            }else if(strlen($tmpCode) == 14){
                $innCode = $tmpCode;
            }else{
                $code128 = $tmpCode;
            }
        }

        $label = str_replace('*eanCode', $targetCode, $label);
        $label = str_replace('*in_itf', $innCode, $label);
        $label = str_replace('*Co_128', $code128, $label);

        //净重
        //$mainWeightSql = "Select MainWeight From $DataIn.productdata Where ProductId=$ProductId"; 

        return $label;
    }
}
?>