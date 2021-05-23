<?php   
//配件报废统计 zhongxq-2012/11/08 AND year(F.Date)='$Year'?
$Year=date("Y");
$Result110=mysql_fetch_array(mysql_query("SELECT  SUM(F.Qty) as Qty,SUM(D.Price*F.Qty*C.Rate) AS Amount
FROM $DataIn.ck8_bfsheet F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.bps B ON B.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=B.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
WHERE  year(F.Date)='$Year' ",$link_id));
$temp_C110=$Result110["Qty"]==""?0:round($Result110["Qty"]/1000,0);
$temp_M110=$Result110["Amount"]==""?0:round($Result110["Amount"]/10000,0);
$tmpTitle="<font color='red' >".number_format($temp_C110)."k</font>/<font color='red'>" . number_format($temp_M110) . "w</font>";
?>