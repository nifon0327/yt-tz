<?php

	include_once "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/CheckAvatar.php");

	$number = $_POST["number"];
	$attendanceTime = $_POST["attendanceTime"];
	$attendanceType = $_POST["attendanceType"];
	$ipadIdentify = $_POST["identifier"];

	// $number = '12047';
	// $attendanceTime = '2015-03-11 07:43:10';
	// $attendanceType = 'I';
	// $ipadIdentify = 'ABEA2F75-19FE-4DAB-A52F-C5A3B874290F';

	$checkAvatar = new CheckAvatar();
	$checkAvatar->setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id, 1);
	if($checkAvatar->getCsign() == "3"){
		$DataIn = $DataOut;
	}

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
	
	//print_r($result);
	echo json_encode($result);

