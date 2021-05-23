<?php
	
	include_once "../../basic/parameter.inc";
	
	$emuShippingDateSql = mysql_query("SELECT M.Date FROM $DataIn.ch0_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	
	$emuShipDate = array();
	while($emuShippingRow = mysql_fetch_assoc($emuShippingDateSql))
	{
		$emuShipDate[] = $dateValue=date("Y-m",strtotime($emuShippingRow["Date"]));
	}
	
	echo json_encode($emuShipDate);
	
?>