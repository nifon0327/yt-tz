<?php 

	include "../../basic/parameter.inc";
	
	$wageArray = array();
	
	$wageSql = "Select * From $DataIn.wage_list Order By Month Desc";
	$wageResult = mysql_query($wageSql);
	while($wageRow = mysql_fetch_assoc($wageResult))
	{
		$Id = $wageRow["Id"];
		$Month = $wageRow["Month"];
		$Estate = $wageRow["Estate"];
		
		$Operator = $wageRow["Operator"];
		$operatorResult = mysql_query("Select Name From $DataPublic.staffmain Where Number='$Operator'");
		$opreatorRow = mysql_fetch_assoc($operatorResult);
		$Operator = $opreatorRow["Name"];
		$fileName = $wageRow["FileName"];
		
		$wageArray[] = array("$Month","$Operator","$Estate","$Id","$fileName");
	}
	
	echo json_encode($wageArray);
?>