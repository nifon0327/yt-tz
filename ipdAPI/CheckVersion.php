<?php
	
	include_once "../basic/parameter.inc";
	$appName = $_POST["appName"]?$_POST['appName']:$_GET['appName'];

	//$appName = "AttendanceSystem";
	$checkVersionSql = "Select version,updateItem From $DataPublic.app_sheet Where appName = '$appName'";
	$checkVersionResult = mysql_query($checkVersionSql);
	$checkVersionRow = mysql_fetch_assoc($checkVersionResult);
	
	$version = $checkVersionRow["version"];
	$update = $checkVersionRow["updateItem"];
	
	$updateItem = explode("|", $update);
	$update = implode(" \n ", $updateItem);
	
	$updateArray = array("$version","$update");
	
	echo json_encode($updateArray);
	
?>