<?php
	

	
	$printerSetSql = "Select LineNumber, IP From $DataIn.printerset Where Estate = 1";
	$printerSetResult = mysql_query($printerSetSql);
	$jsonArray = array();
	while($printerSerRow = mysql_fetch_assoc($printerSetResult))
	{
		$line = $printerSerRow["LineNumber"];
		$ip = $printerSerRow["IP"];
		$line = substr($line,-1);
		$jsonArray[] = array("$line", "$ip");
		
	}

?>