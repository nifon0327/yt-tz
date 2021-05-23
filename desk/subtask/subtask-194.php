<?php   
//电信-zxq 2012-08-01
/*
功能：统计未确定订单数量
*/
$Result194=mysql_fetch_array(mysql_query("SELECT count(*) AS orderNumber 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId 
	WHERE S.Estate>0  AND T.Type='7'",$link_id));
  $temp_C194=$Result194['orderNumber']==""?0:$Result194['orderNumber'];
$tmpTitle="<font color='red'>($temp_C194)</font>";
?> 