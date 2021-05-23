<?php   
//未付货款统计
$Result227=mysql_fetch_array(mysql_query("	SELECT SUM(A.Amount) AS Amount FROM (
              SELECT SUM(S.Amount*C.Rate) AS Amount 
              FROM $DataIn.cw1_fkoutsheet S
              LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
              LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
              WHERE S.Estate=3 AND Amount>0  
     UNION ALL
              SELECT SUM(S.Amount*C.Rate) AS Amount 
              FROM $DataIn.cw1_tkoutsheet S
              LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
              LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
              WHERE S.Estate=3 
       )A",$link_id));
$temp_C227=$Result227["Amount"]==""?0:number_format($Result227["Amount"]/10000);
$tmpTitle="<font color='red'>" .$temp_C227."w</font>";
?>