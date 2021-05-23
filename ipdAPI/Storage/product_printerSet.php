<?php
	
	include_once "../../basic/parameter.inc";
	
	$printerSetSql = "Select LineNumber, IP From $DataIn.printerset Where Estate = 1";
	$printerSetResult = mysql_query($printerSetSql);
	$printers = array();
	while($printerSerRow = mysql_fetch_assoc($printerSetResult))
	{
		$line = $printerSerRow["LineNumber"];
		$ip = $printerSerRow["IP"];
		
		$printers[] = array("$line", "$ip");
		
	}
	
	echo json_encode($printers);
	
?>