<?php   
//备品转入统计 zhongxq-2012/11/08 
$Year=date("Y"); 
$Result220=mysql_fetch_array(mysql_query("SELECT  SUM(B.Qty) as Qty,SUM(D.Price*B.Qty*C.Rate) AS Amount
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
 WHERE  year(B.Date)='$Year' ",$link_id));
$temp_C220=$Result220["Qty"]==""?0:round($Result220["Qty"]/1000,0);
$temp_M220=$Result220["Amount"]==""?0:round($Result220["Amount"]/10000,0);
$tmpTitle="<font color='red'>" . number_format($temp_C220)."k</font>/<font color='red'>" . number_format($temp_M220) . "w</font>";
?>