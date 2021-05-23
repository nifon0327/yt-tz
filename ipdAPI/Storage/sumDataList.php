<?php
	
	include_once "../../basic/parameter.inc";
	
	$sumtype = $_POST["sumtype"];
	$sumtype = "overDateQty";
	$sum = "";
	switch($sumtype)
	{
		case "overDateQty":
		{
			$overQtyResult= mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS Qty FROM(
                            							   SELECT S.Qty,Max(M.Date) AS LastBlDate 
                            							   FROM $DataIn.ck_bldatetime B 
                            							   LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=B.POrderId 
                            							   LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
                            							   LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
                            							   LEFT JOIN $DataIn.yw9_blmain M ON L.Pid=M.Id 
                            							   WHERE B.Estate=0 AND S.Estate=1 AND S.scFrom>0 GROUP BY B.POrderId) A WHERE  														   TIMESTAMPDIFF(minute,A.LastBlDate,Now())>2160",$link_id));
           
		}
		break;
	}
	
	echo $sum;
	
?>