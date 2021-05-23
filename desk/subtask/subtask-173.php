<?php
$checkDay=date("Y-m-d");
$Result173=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
WHERE 1 and S.Estate>0 AND M.OrderDate='$checkDay'",$link_id));
  $temp_C173=$Result173["Qty"]==""?0:round($Result173["Qty"]/1000,0);
  $tmpTitle="<font color='red'>$temp_C173"."k</font>";
?>