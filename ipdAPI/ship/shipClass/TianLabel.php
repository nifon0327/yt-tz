<?php
class TianLabel{
    private $defaultLotto = 'TIAN01';
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        //~JS1|cellular3|1|code|*eCode|endPlace|*EndPlace|shipDate|*Date|invoiceNo|*InvoiceNO|currentBox|%i|totleBox|%i|gwValue|%@|nwValue|*WG|PO|*OrderPO|boxPcs|%i|productCode|*Code|lotto|*lotto|itfInit|*itfInit|

        $lotto = '';
        $itf = '';
        $POrderId = $labelInfomation['POrderId'];
        $hasPOrderPrintParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' Order by Id Limit 1";
        $hasPOrderPrintParameterResult = mysql_query($hasPOrderPrintParameterSql);
        if(mysql_num_rows($hasPOrderPrintParameterResult) > 0){
            $hasPOrderPrintParameterRow = mysql_fetch_assoc($hasPOrderPrintParameterResult);
            $lotto = $hasPOrderPrintParameterRow['Lotto'];
            $itf = $hasPOrderPrintParameterRow['itf'];
        }else{
            $productParameterSql = "Select * From $DataIn.productprintparameter Where   productId = '".$labelInfomation['ProductId']."' AND Estate = 1 Order by Id Limit 1";
            $productParameterResult = mysql_query($productParameterSql);
            if(mysql_num_rows($productParameterResult) > 0){
                $hasProductPrintParameterRow = mysql_fetch_assoc($productParameterResult);
                $lotto = $hasProductPrintParameterRow['Lotto'];
                $itf = $hasProductPrintParameterRow['itf'];
            }else{
                $lotto = $this->defaultLotto;
                $itf = '4';
            }
        }

        if($lotto == ''){
            $lotto = $this->defaultLotto;
        }

        if($itf == ''){
            $itf = '4';
        }
        
        $labelInfomation['lotto'] = $lotto;
        $labelInfomation['itfInit'] = $itf;

        $label = $originalLabel;
        foreach($labelInfomation as $key=>$value){
            if(strpos($label, '*'.$key)){
                $label = str_replace('*'.$key, $value , $label);
            }
        }

        return $label;
    }

    public function addCharacter($item,$DataIn, $DataPublic){
        $POrderId = $item['POrderId'];
        $hasPOrderPrintParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' Order by Id Limit 1";
        $hasPOrderPrintParameterResult = mysql_query($hasPOrderPrintParameterSql);
        if(mysql_num_rows($hasPOrderPrintParameterResult) > 0){
            $hasPOrderPrintParameterRow = mysql_fetch_assoc($hasPOrderPrintParameterResult);
            $lotto = $hasPOrderPrintParameterRow['Lotto'];
            $itf = $hasPOrderPrintParameterRow['itf'];
        }else{
            $productParameterSql = "Select * From $DataIn.productprintparameter Where   productId = '".$item['ProductId']."' AND Estate = 1 Order by Id Limit 1";
            $productParameterResult = mysql_query($productParameterSql);
            if(mysql_num_rows($productParameterResult) > 0){
                $hasProductPrintParameterRow = mysql_fetch_assoc($productParameterResult);
                $lotto = $hasProductPrintParameterRow['Lotto'];
                $itf = $hasProductPrintParameterRow['itf'];
            }else{
                $lotto = $this->defaultLotto;
                $itf = '4';
            }
        }

        if($lotto == ''){
            $lotto = $this->defaultLotto;
        }

        if($itf == ''){
            $itf = '4';
        }

        return array('lotto'=>"$lotto", 'itf'=>"$itf");
    }
}
?>