<?php 
//交货均期$DataIn.电信---yang 20120801
		$JAvgRow= mysql_fetch_array(mysql_query("SELECT IFNULL(AVG(TO_DAYS(SM.Date)-TO_DAYS(YM.OrderDate)),0) AS Days
			FROM $DataIn.yw1_ordersheet YS
			LEFT JOIN $DataIn.ch1_shipsheet SY ON SY.POrderId=YS.POrderId
			LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=YS.OrderNumber
			LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=SY.Mid
			WHERE YS.ProductId='$ProductId' AND SM.Date IS NOT NULL",$link_id));
		$JqAvg=$JAvgRow["Days"]==0?"&nbsp;":ceil($JAvgRow["Days"])."days";
?>