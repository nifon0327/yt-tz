<?php   
//PI未传  yang
$Result237=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS noNum
FROM $DataIn.yw1_ordermain M LEFT 
JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
WHERE 1 and S.Estate>0 AND ( left(PI.Leadtime,3)!='201'  OR PI.Leadtime IS NULL )",$link_id));
//金额用于iPhone
//$Amount_C237=$Result237["Amount"]==""?0:sprintf("%.0f",$Result237["Amount"]);
$temp_C237=$Result237["noNum"]==""?0:round($Result237["noNum"],0);
$tmpTitle="<font color='red'>$temp_C237</font>";
?>