<?php 
	
	include_once("../../basic/parameter.inc");
	
	$nameInfo = array();
	$nameAndNumberSql = "Select Name, Number, KqSign, cSign From $DataPublic.staffmain Where Estate = 1";
	$result = mysql_query($nameAndNumberSql);
	while($rows = mysql_fetch_row($result))
	{
		$nameInfo[$rows[1]] = $rows;
	}	
	
	echo json_encode($nameInfo);
?>