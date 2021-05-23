<?php

	include_once "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance/AttendanceClass/CheckAvatar.php");

	$number = $_POST["number"];
	$attendanceTime = $_POST["attendanceTime"];
	$attendanceType = $_POST["attendanceType"];
	$ipadIdentify = $_POST["identifier"];

	//$number = "11008";
	//$attendanceTime = date("Y-m-d H:i:s");
	//$attendanceType = "I";
	//$ipadIdentify = "6929FFA0-AA02-46D2-8528-0FBCA9B75FBC";

	$checkAvatar = new CheckAvatar();

	if($checkAvatar->getCsign()== "3"){
		$DataIn = $DataOut;
	}

	$checkAvatar->setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id);
	$checkAvatar->checkLegalIpad($ipadIdentify, $DataIn, $DataPublic, $link_id);
	if($checkAvatar->targetFloor == "")
	{
		 $result = array("state"=>falese, "infomation"=>"非法考勤iPad");
	}
	else
	{

		$checkAvatar->checkAttendanceFloor($DataIn, $DataPublic, $link_id);
		$checkAvatar->setupCheckTime($attendanceType, $attendanceTime);

		$checkLegal = "";
		$checkLegal = ($attendanceType == "I")?$checkAvatar->checkIn($DataIn, $DataPublic, $link_id):$checkAvatar->checkOut($DataIn, $DataPublic, $link_id);

		$result = ($checkLegal["state"])?$checkAvatar->insertCheckTime($attendanceType, $DataIn, $DataPublic, $link_id):$checkLegal;
	}

	echo json_encode($result);

