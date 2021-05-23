<?php   
/*
功能：获取出货标签的lotto
传入参数:$CompanyId,$POrderId,$targetBoxCode
*/


$lotto='';$itf='';
$initallottoSql = "Select * From $DataIn.ch_initallotto Where  CompanyId = '$CompanyId'  Limit 1";
$initallottoResult = mysql_query($initallottoSql);
if(mysql_num_rows($initallottoResult) > 0){
    $initallottoRow = mysql_fetch_assoc($initallottoResult);
    $lotto = $initallottoRow['lotto'];
    

	$hasPOrderPrintParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' Order by Id Limit 1";
	$hasPOrderPrintParameterResult = mysql_query($hasPOrderPrintParameterSql);
		if(mysql_num_rows($hasPOrderPrintParameterResult) > 0){
        $hasPOrderPrintParameterRow = mysql_fetch_assoc($hasPOrderPrintParameterResult);
        $lotto = $hasPOrderPrintParameterRow['Lotto'];
        $itf = $hasPOrderPrintParameterRow['itf'];
    }
    else{
        $productParameterSql = "Select * From $DataIn.productprintparameter Where   productId = '".$ProductId."' AND Estate = 1 Order by Id Limit 1";
        $productParameterResult = mysql_query($productParameterSql);
        if(mysql_num_rows($productParameterResult) > 0){
            $hasProductPrintParameterRow = mysql_fetch_assoc($productParameterResult);
            $lotto = $hasProductPrintParameterRow['Lotto'];
            $itf = $hasProductPrintParameterRow['itf'];
        }
    }
    
    if($itf == ''){
            $itf = '4';
    }
    
   $celCode = explode('|', $targetBoxCode);
   $BoxCode = $targetBoxCode.'|'.$lotto.'|'.$itf.$celCode[1];
}
?>