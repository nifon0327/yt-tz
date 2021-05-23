<?php
	
	include_once "../../basic/parameter.inc";
	
	$id = $_POST["Id"];
	
	$state = 'N';
	$deleteVicitorSql = "Delete From $DataPublic.come_data Where Id = '$id'";
	if(mysql_query($deleteVicitorSql))
	{
		$state = 'Y';
	}
	
	echo $state;
	
?>  