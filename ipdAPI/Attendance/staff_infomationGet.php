<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include "../../basic/parameter.inc";
	include_once("$path/ipdAPI/Attendance/AttendanceClass/StaffAvatar.php");

	$number = $_POST["Number"];
	//$number = "11008";
	$staff = new StaffAvatar($number, $DataIn, $DataPublic, $link_id);

	echo json_encode($staff->outputIntomation());

?>