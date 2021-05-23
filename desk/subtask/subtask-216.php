<?php   
//已送料配件 yang
$Result216=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS wQty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
 FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
WHERE 1 and S.Estate>=2 ",$link_id));
//金额用于iPhone
$iPhoneAmount_C216=$Result216["Amount"]==""?0:round($Result216["Amount"],0);

$Amount_C216=$Result216["Amount"]==""?0:round($Result216["Amount"]/10000,0);
$temp_C216=$Result216["wQty"]==""?0:round($Result216["wQty"]/1000,0);
$tmpTitle="<font color='red'>$temp_C216"."k</font>"."/"."<font color='red'>$Amount_C216"."w</font>";
?>