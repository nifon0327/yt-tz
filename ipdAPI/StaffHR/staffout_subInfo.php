<?php 
	
	include "../../basic/parameter.inc";
	
	$Id = $_GET["Id"];
	//$Id = "1529";
		
	$outSubInfo = "SELECT T.Name AS TypeName, D.outDate, D.Reason, P.Name, P.Number
				   FROM $DataPublic.dimissiondata D
				   LEFT JOIN $DataPublic.staffmain P ON P.Number = D.Number
				   LEFT JOIN $DataPublic.dimissiontype T ON T.Id = D.Type
				   WHERE D.Id = '$Id'";
	  
	
	$outSubInfoResult = mysql_query($outSubInfo);
	$outSubInfoRow = mysql_fetch_assoc($outSubInfoResult);
	
	echo json_encode(array($outSubInfoRow["Name"],$outSubInfoRow["TypeName"],$outSubInfoRow["outDate"],$outSubInfoRow["Reason"],$outSubInfoRow["Number"]));
	
?>