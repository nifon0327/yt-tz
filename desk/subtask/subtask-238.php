<?php   
//当月预出 	 yang
$ThisMonth=date("Y-m");
$Result238=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS monthQty,SUM(S.Qty*S.Price*R.Rate)  AS monthAmount
FROM $DataIn.yw1_ordermain M LEFT 
JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.trade_object CD ON M.CompanyId=CD.CompanyId
LEFT JOIN $DataPublic.currencydata R ON R.Id=CD.Currency
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
WHERE 1 and S.Estate>0 AND DATE_FORMAT(PI.Leadtime,'%Y-%m')<='$ThisMonth'",$link_id));
//金额用于iPhone
$Amount_C238=$Result238["monthAmount"]==""?0:round($Result238["monthAmount"]/10000,0);
$temp_C238=$Result238["monthQty"]==""?0:round($Result238["monthQty"]/1000,0);
$tmpTitle="<font color='red'>$temp_C238"."k</font>"."/"."<font color='red'>$Amount_C238"."w</font>";
?>