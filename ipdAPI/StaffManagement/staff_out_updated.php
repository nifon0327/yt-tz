<?php 

	include "../../basic/parameter.inc";
	
	$outId = $_POST["id"];
	$outDate = $_POST["outdate"];
	$outType = $_POST["type"];
	
	$outTypeResult = mysql_query("Select Id From $DataPublic.dimissiontype Where Name = '$outType' And Estate = '1'");
	$outTypeRow = mysql_fetch_assoc($outTypeResult);
	$outType = $outTypeRow["Id"];
	
	$outReason = $_POST["reason"];
	$beOutDate = $Date=date("Y-m-d");
	$operator = $_POST["operator"];
	
	$outArray = array();
	
	$updateSQL = "UPDATE $DataPublic.dimissiondata SET Type='$outType',outDate='$outDate',Reason='$outReason',Date='$beOutDate',Operator='$operator',Locks='0' WHERE Id='$outId'";
	
	$succee = "";
	
	if(mysql_query($updateSQL))
	{
		$succee = "y";
	}
	else
	{
		$succee = "n";
	}
	
	$outArray[] = $succee;
	echo json_encode($outArray);
	
?>