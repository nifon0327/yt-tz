<?php   
//PI逾期  yang
 $Temptoday=date("Y-m-d");
$Result236=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS OutQty,SUM(S.Qty*S.Price*R.Rate) AS OutAmount
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.trade_object CD ON M.CompanyId=CD.CompanyId
LEFT JOIN $DataPublic.currencydata R ON R.Id=CD.Currency
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
WHERE 1 and S.Estate>0 AND PI.Leadtime<='$Temptoday' AND left(PI.Leadtime,3)='201'",$link_id));
//金额用于iPhone
$OutAmount=$Result236["OutAmount"]==""?0:round($Result236["OutAmount"]/10000,0);
$temp_C236=$Result236["OutQty"]==""?0:round($Result236["OutQty"]/1000,0);
$tmpTitle="<font color='red'>$temp_C236"."k</font>"."/"."<font color='red'>$OutAmount"."w</font>";
?>