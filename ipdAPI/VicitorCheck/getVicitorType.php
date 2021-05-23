<?php
	
	include_once "../../basic/parameter.inc";
	
	$typer = array();
	$today = date("Y-m-d");
	$getTypeSql = "Select * From $DataPublic.come_type Where Estate = 1";
	$typeResult = mysql_query($getTypeSql);
	while($typeRow = mysql_fetch_assoc($typeResult))
	{
		$typeId = $typeRow["Id"];
		$typeName = $typeRow["Name"];
		
		$getTodyVicitorListSql = "Select Count(*) as count From $DataPublic.come_data 
							 	  Where 
							 	  TypeId = '$typeId' 
							 	  and (Estate in (1, 2) or (Estate = 0 and ComeDate = '$today'))
							 	  and Id not in (Select Id From $DataPublic.come_data 
							 	  Where 
							 	  TypeId = '$typeId' 
							 	  and ComeDate = '$today'
							 	  and Estate = '0')
							 	  Order By Estate Desc";
		//echo $getTodyVicitorListSql;
		$badgeVicitorResult = mysql_query($getTodyVicitorListSql);
		$badgeRow = mysql_fetch_assoc($badgeVicitorResult);
		$badgeNumber = $badgeRow["count"];
		
		$typer[] = array("Id"=>$typeId, "Name"=>$typeName, "Count"=>$badgeNumber);
	}
	
	echo json_encode($typer);
	
?>