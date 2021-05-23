<?php 
//读取客户货币
 $currency_Temp = mysql_query("SELECT A.PreChar,A.Rate,A.Symbol FROM $DataPublic.currencydata A LEFT JOIN $DataIn.trade_object B ON A.Id=B.Currency WHERE  B.CompanyId=$CompanyId ORDER BY B.CompanyId LIMIT 1",$link_id);
if($RowTemp = mysql_fetch_array($currency_Temp)){
        $PreChar=$RowTemp["PreChar"];
        $Rate=$RowTemp["Rate"];//汇率
        $Symbol=$RowTemp["Symbol"];//货币符号
        }
?>