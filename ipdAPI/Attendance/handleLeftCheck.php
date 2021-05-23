<?php
	
	include "../../basic/parameter.inc";
	
	$needPrint = "no";
	$leftCheck = $_POST["leftCheck"];
	$leftCheck = "10973&2012-12-03 20:14:23&I+11706&2012-12-03 20:15:06&I+10572&2012-12-03 20:15:24&I";
	
	$errorNumber = array();
	$lefts = explode("+", $leftCheck);
	
	for($i=0;$i< count($lefts); $i++)
	{
		$tmpLefts = explode("&", $lefts[$i]);
		
		$staffKqId = $tmpLefts[0];
		$CheckTime = $tmpLefts[1];
		$CheckType = $tmpLefts[2];
		
		include "checkIO.php";
	}
	
	echo json_encode($errorNumber);
	
?>