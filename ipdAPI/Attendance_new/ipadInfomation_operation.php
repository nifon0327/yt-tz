<?php
	
	include_once "../../basic/parameter.inc";
	
	$identifier = $_GET["identifier"];
	
	$name = "";
	$isAttendanceIpadSql = "Select * From $DataPublic.attendanceipadsheet Where Identifier = '$identifier' and Estate = '1'";
	$isAttendanceResult = mysql_query($isAttendanceIpadSql);
	if($isAttdanceIpadRow = mysql_fetch_assoc($isAttendanceResult))
	{
		$name = $isAttdanceIpadRow["Name"];
	}
	
	$result = ($name == "")?"非法机器":$name;
	
	echo json_encode(array($result));
?>