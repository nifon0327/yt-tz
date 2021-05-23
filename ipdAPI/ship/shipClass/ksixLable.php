<?php
class ksixLable{
    public function getLabel($originalLabel, $labelInfomation, $DataIn, $DataPublic){
        
        $POrderId = $labelInfomation['POrderId'];


        $codeResult = mysql_query("SELECT D.StuffId, D.StuffCname
                                  FROM $DataIn.cg1_stocksheet P 
                                  INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
                                  INNER JOIN $DataIn.stufftype T On T.TypeId = D.TypeId
                                  WHERE P.POrderId = '$POrderId'
                                  AND T.mainType= 5
                                  AND D.TypeId in (9124, 9033)");

        switch(mysql_num_rows($codeResult)){
            case 1: //只有一个的默认为外箱条码
                $codeRow = mysql_fetch_assoc($codeResult);
                $stuffCname = explode('-', $codeRow['StuffCname']);
                $oCode = $stuffCname[count($stuffCname)-1];
            break;
            default :
                while($codeRow = mysql_fetch_assoc($codeResult)){
                    if(strpos($codeRow['StuffCname'], '(ITF)')){
                        continue;
                    }
                    //echo $codeRow['StuffCname'].'<br>';
                    $stuffCname = explode('-', $codeRow['StuffCname']);
                    if(strpos($codeRow['StuffCname'], '(外箱)')){
                        $oCode = $stuffCname[count($stuffCname)-1];
                    }
                }
            break;
        }

        $code = explode('-', $oCode);
        $code = explode('(', $code[count($code)-1]);
        $label = $originalLabel;
        $label = str_replace('*specCode', $code[0], $label);
        return $label;
    }
}
?>