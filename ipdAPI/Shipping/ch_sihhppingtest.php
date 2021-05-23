<?php
    include_once "../../basic/parameter.inc";
    $POrder = '201410100401';
    $ProductId = '91025';

    $hasPrintParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' and Estate = 1 Order by Id Limit 1";
        $hasPrintParameterResult = mysql_query($hasPrintParameterSql);
        $hasPrintParameterRow = mysql_fetch_assoc($hasPrintParameterResult);
        if($hasPrintParameterRow){
            $lotto = $hasPrintParameterRow["Lotto"];
            $itf = $hasPrintParameterRow["itf"];
        }
        else{
            $hasProductParameterSql = "Select * From $DataIn.productprintparameter Where productId = '$ProductId' and Estate = 1 Order by Id Limit 1";
            $hasProductParameterResult = mysql_query($hasProductParameterSql);
            $hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
            if($hasProductParameterRow){
                $lotto = $hasProductParameterRow["Lotto"];
                $itf = $hasProductParameterRow["itf"];
            }
        }

        if($lotto == ''){
            if($CompanyId == '100024'){
                $lotto = "ART01";
            }
            else if($CompanyId == '2668'){
                $lotto = "LOP01";
            }
            else{
                $lotto = "ASH01";
            }
        }   

        if($itf == ''){
            $itf = "4";
        }
        echo 'itf:'.$itf.'  lotto:'.$lotto;
?>