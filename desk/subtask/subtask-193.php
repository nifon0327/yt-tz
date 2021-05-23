<?php   
//电信-zxq 2012-08-01
/*
功能：统计未确定订单数量
*/
$Result193=mysql_fetch_array(mysql_query("SELECT count(*) AS orderNumber 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId 
	LEFT JOIN (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G WHERE GL.Locks=0 AND GL.StockId=G.StockId GROUP BY POrderId) K ON K.POrderId=S.POrderId 
	WHERE S.Estate>0  AND (T.Type='2' OR K.Locks=0) ",$link_id));
  $temp_C193=$Result193['orderNumber']==""?0:$Result193['orderNumber'];
  $tmpTitle="<font color='red'>$temp_C193</font>";
?> 