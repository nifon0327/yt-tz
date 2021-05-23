<?php 
	
	include "../../basic/parameter.inc";
	
	$doors = array();
	
	$getDoorSql = "Select Id,DoorName,DoorAdd From $DataPublic.accessguard_door Order By Id";
	$doorResult = mysql_query($getDoorSql);
	while($doorRow = mysql_fetch_assoc($doorResult))
	{
		$doors[]= array($doorRow["Id"], $doorRow["DoorName"], $doorRow["DoorAdd"]);
	}
	
	echo json_encode($doors);
	
?>