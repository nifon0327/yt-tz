<?php
class RobiLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $ProductId = $labelInfomation['ProductId'];
        //$shipId = $labelInfomation['shipId'];
        $label = $originalLabel;
        
        $productTypeCode = mysql_query("select cName From $DataIn.productdata Where ProductId = '$ProductId'");
        //echo "select Description,pRemark From $DataIn.productdata Where ProductId = '$ProductId' <br>";
        $pRemarkResult = mysql_fetch_assoc($productTypeCode);
        $productType =  $pRemarkResult["cName"];
        $pMark = explode(' ', $productType);
        $productType = $pMark[0];
        //echo $productType.'<br>';
        $label = str_replace('*productType', $productType, $label);
        return $label;
    }
}

?>