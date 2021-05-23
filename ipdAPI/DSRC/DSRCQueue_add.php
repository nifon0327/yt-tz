<?php
	
	include_once "../../basic/parameter.inc";
	$queues= $_POST["queues"];
	$address = $_POST["address"];
	
	$Date=date("Y-m-d");
	
	$eachQueue = explode("|", $queues);
	$insertQueue = array();
	for($i=0; $i<count($eachQueue); $i++)
	{
		$info = explode("*", $eachQueue[$i]);
		$cardNumber = $info[0];
		$checkTime = $info[1];
		$insertQueue[] = "(NULL, '$cardNumber', '$checkTime', '$address', '$Date')";
	}
	
	$realQueue = implode(",", $insertQueue);
	$uploadQuery = "Insert Into $DataIn.dsrc_queue (id, CardNumber, CheckTime, Door, Date) Values $realQueue"; 
	
	if(mysql_query($uploadQuery))
	{
		$upLoadSucceed = "Y";
	}
	
	echo $upLoadSucceed;
	
?>