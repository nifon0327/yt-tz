<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include "../../basic/parameter.inc";
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");

    if($_POST['Number'] != ""){
        $number = $_POST['Number'];
    }
    else{
        $number = $_GET['Number'];
    }

    //$number = '11008';
	
	$staff = new StaffAvatar($number, $DataIn, $DataPublic, $link_id);

    //print_r($staff->outputIntomation());
	echo json_encode($staff->outputIntomation());

?>