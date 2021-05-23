<?php
	
	include_once "../../basic/parameter.inc";
	
	$dsrcDoorSql = "Select Id, Address, Name from $DataIn.dsrc_door Where Estate = 1";
	$dsrcResult = mysql_query($dsrcDoorSql);
	$dsrcDoors = array();
	while($dsrcRow = mysql_fetch_assoc($dsrcResult))
	{
		$Id = $dsrcRow["Id"];
		$address = $dsrcRow["Address"];
		$name = $dsrcRow["Name"];
		
		$dsrcDoors[] = array("$Id", "$address", "$name");
		
	}
	
	echo json_encode($dsrcDoors);
	
?>