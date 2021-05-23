<?php

	include_once "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/CheckAvatar.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/CheckAvatar_lw.php");
	include_once("getWageSignState.php");

	$number = $_POST["number"];
	$attendanceTime = $_POST["attendanceTime"];
	$attendanceType = $_POST["attendanceType"];
	$ipadIdentify = $_POST["identifier"];

	// $number = '11008';
	// $attendanceTime = '2015-10-21 10:02:29';
	// $attendanceType = 'I';
	// $ipadIdentify = 'F14A88E2-72D0-462B-BF62-1120092818DC';

	$isLwStaffSql = "SELECT * From $DataIn.lw_staffmain A Where (A.Number = $number or A.IdNum = $number) and Estate=1";
	$isLwStaffResult = mysql_query($isLwStaffSql);
	if(mysql_num_rows($isLwStaffResult) == 1){
		$checkAvatar = new CheckAvatar_lw();
	}else{
		$checkAvatar = new CheckAvatar();
	}

	$checkAvatar->setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id, 1);
	// if($checkAvatar->getCsign() == "3"){
	// 	$DataIn = $DataOut;
	// }

	$checkAvatar->checkLegalIpad($ipadIdentify, $DataIn, $DataPublic, $link_id);
	if($checkAvatar->targetFloor == ""){
		 $result = array("state"=>falese, "infomation"=>"非法考勤iPad");
	}
	else{

		$checkAvatar->checkAttendanceFloor($DataIn, $DataPublic, $link_id);
		$checkAvatar->setupCheckTime($attendanceType, $attendanceTime);

		$checkLegal = "";
		$checkLegal = ($attendanceType == "I")?$checkAvatar->checkIn($DataIn, $DataPublic, $link_id):$checkAvatar->checkOut($DataIn, $DataPublic, $link_id);

		$result = ($checkLegal["state"])?$checkAvatar->insertCheckTime($attendanceType, $DataIn, $DataPublic, $link_id):$checkLegal;
		if ($result['state']) {
			$signState = checkWageSign($checkAvatar->getStaffNumber(), $DataIn, $DataPublic, $link_id);
			$result['infomation'] = $result['infomation'].'+'.$signState;
		}
	}

	echo json_encode($result);

