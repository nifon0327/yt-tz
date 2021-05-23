<?php 
	
	include "../../basic/parameter.inc";
	
	$printIpSql = "Select Ip,Line From $DataIn.printerip";
	$printArray = array();
	$printResult = mysql_query($printIpSql);
	while($printRow = mysql_fetch_assoc($printResult))
	{
		$printArray[] = array($printRow["Line"],$printRow["Ip"]);
	}
	
	echo json_encode($printArray);
?>