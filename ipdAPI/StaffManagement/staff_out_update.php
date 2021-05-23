<?php 

	include "../../basic/parameter.inc";
	$staffNum = $_POST["staffNum"];
		
	$staffOutInfoSql = "SELECT P.Name,P.Number,D.outDate,D.Type,D.Reason 
FROM $DataPublic.dimissiondata D,$DataPublic.staffmain P  WHERE D.Number='$staffNum' AND P.Number=D.Number LIMIT 1";

	$staffOutInfoResult = mysql_fetch_assoc(mysql_query($staffOutInfoSql));
	$staffName = $staffOutInfoResult["Name"];
	$staffOutdate = $staffOutInfoResult["outDate"];
	$staffOutType = $staffOutInfoResult["Type"];
	
	$outTypeResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.dimissiontype WHERE Estate='1' And Id = '$staffOutType'"));
	$staffOutType = $outTypeResult["Name"];
	$staffOutReason = $staffOutInfoResult["Reason"];
	
	$staffOutArray = array($staffName,$staffOutdate,$staffOutType,$staffOutReason);
	echo json_encode($staffOutArray);
	
?>