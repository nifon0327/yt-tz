<?php
class DcAsiaLabel{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];
        $ProductId = $labelInfomation['ProductId'];
        $Id = $labelInfomation['shipId'];
        $type = $labelInfomation['printType'];
        $label = $originalLabel;

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

        $noteContentSql = "SELECT note From $DataIn.ch13_othernote WHERE shipId='$Id' and type='$type'";
        
        $noteResult = mysql_query($noteContentSql);
        $noteRow = mysql_fetch_assoc($noteResult);
        $spec = $noteRow['note'];

        $label = str_replace('*innerQty', "$inBoxQty", $label);
        $label = str_replace('*invoiceNospe', $spec, $label);
        return $label;
    }
}
?>