<?php
class VITLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $ProductId = $labelInfomation['ProductId'];
        $label = $originalLabel;
        /*获取装箱数*/
        $codeResult = mysql_query("SELECT S.Relation,D.TypeId 
                                   FROM $DataIn.yw1_orderSheet P 
                                   LEFT JOIN $DataIn.pands S ON S.ProductId = P.ProductId
                                   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                                   WHERE P.POrderId = '$POrderId' AND D.TypeId in (9040, 9120)");

        $outBoxQty = '';
        $inBoxQty = '';
        while($codeRow = mysql_fetch_assoc($codeResult)){
            $typeId = $codeRow['TypeId'];
            $spec = explode('/', $codeRow['Relation']);
            $qty = $spec[1];
            switch ($typeId) {
                case '9040':
                    $outBoxQty = $qty;
                    break;
                case '9120':
                    $inBoxQty = $qty;
                    break;
            }
        }

        if($inBoxQty != '' && $inBoxQty != 0){
            $countBox = $outBoxQty/$inBoxQty;
        }else{
            $countBox = '';
        }
        $label = str_replace('*innerCarton', "$countBox × $inBoxQty", $label);

        //开箱条码
        $codeResult = mysql_query("SELECT D.StuffId, D.StuffCname
                                  FROM $DataIn.cg1_stocksheet P 
                                  INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
                                  WHERE P.POrderId = '$POrderId'
                                  AND D.TypeId in (9124, 9033)");
        while($codeRow = mysql_fetch_assoc($codeResult)){
             $stuffCname = explode('-', $codeRow['StuffCname']);
             if(strpos($stuffCname[0], '(外箱)') && count($stuffCname) ==3){
                $innerCode = $stuffCname[1];
             }
        }
        $label = str_replace('*labelCode',trim($innerCode), $label);

        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];
        $noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='$type'";
            $noteResult = mysql_query($noteContentSql);
            if($noteRow = mysql_fetch_assoc($noteResult)){
                $note = $noteRow['note'];
                $label = str_replace('*otherInvoiceNO', $note, $label);
            }


        return $label;
    }
}
?>