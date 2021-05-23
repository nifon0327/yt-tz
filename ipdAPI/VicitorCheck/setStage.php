<?php
	
	include_once "../../basic/parameter.inc";
	
	$id = $_POST["Id"];
	$stage = $_POST["stage"];
	$operator = $_POST["operator"];
	$time = date("Y-m-d H:i:s");
	
	$updateLine = "";
	$result = "";
	//echo $stage;
	if($stage == "1")
	{
		$updateLine = "Set InTime = '$time', InOperator = '$operator', Estate = '2'";
	}
	else if($stage == "2")
	{
		$updateLine = "Set OutTime = '$time', OutOperator = '$operator', Estate = '0'";
	}
	
	if($updateLine != "")
	{
		$updateVicitorSql = "Update $DataPublic.come_data $updateLine Where Id = '$id'";
		//echo $updateVicitorSql;
		if(mysql_query($updateVicitorSql))
		{
			$result = substr($time, 11, 5);
		}
	}
	
	echo($result);
	
?>