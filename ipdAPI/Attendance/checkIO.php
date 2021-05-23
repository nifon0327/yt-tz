<?php

	include_once "../../basic/parameter.inc";
	include_once("getStaffNumber.php");

	$staffKqId = $_POST["Number"];
	//处理从二维码读出的号码
	if(strlen($staffKqId) != 5)
	{
		$staffKqId = getStaffNumber($staffKqId, $DataPublic);
	}

	$CheckTime = $_POST["CheckTime"];
	//$CheckTime = date("Y-m-d H:i:s");

	$CheckType = $_POST["CheckType"];
	//$CheckType = "I";
	//$staffKqId = '10228';
	$dForm = "";
	$identifier = $_POST["identifier"];
	$isAttendanceIpadSql = "Select * From $DataPublic.attendanceipadsheet Where Identifier = '$identifier' and Estate = '1'";
	$isAttendanceIpadResult = mysql_query($isAttendanceIpadSql);
	if($isAttendanceIpadRows = mysql_fetch_assoc($isAttendanceIpadResult))
	{
		$dForm = $isAttendanceIpadRows["Name"];
		$targetFloor = $isAttendanceIpadRows["Floor"];
	}


	if($CheckType == "" || $staffKqId == "" || $CheckTime == "" || $dForm == "")
	{
		if($dForm == "")
		{
			$resultInfo = "非法机器打卡";
		}
		else
		{
			$resultInfo = "读卡错误，请重试";
		}

		$errorState = "yes";
	}
	else
	{
		$OperationResult="N";
		$today = date("Y-m-d");
		$errorState = "no";

		$staffInfoStr = sprintf("SELECT st.Number,st.Name,st.KqSign,st.BranchId,st.JobId,st.cSign,af.Floor 
								 FROM $DataPublic.staffmain st
								 Left Join $DataPublic.attendance_floor as af On af.Id = st.AttendanceFloor
								 WHERE Number='%s'  
								 AND st.Estate='1' LIMIT 1",$staffKqId);
		//echo $staffInfoStr;
		$staffResult = mysql_query($staffInfoStr);
		if($staffInfo = mysql_fetch_assoc($staffResult))
		{
			$KqSign=$staffInfo["KqSign"];//分三种情况：
			$Number=$staffInfo["Number"];
			$Name=$staffInfo["Name"];
			$brandId = $staffInfo["BranchId"];
			$jobId = $staffInfo["JobId"];
			$cSign = $staffInfo["cSign"];
			$AttendanceFloor = ($staffInfo["Floor"] == "")?" ":$staffInfo["Floor"];

			//皮套员工在研砼打卡
			if($cSign == "3")
			{
				$DataIn = $DataOut;
			}

			$resultInfo = $Name;

			if($KqSign<3)
			{//需考勤或考勤参考
			//签卡分析
				include "staffChkIO.php";
				//$resultInfo = $Number." ".$Name." ".$CheckTime."  ".$CheckType;
				//工时确认
				$staffKqId = $Number;
				include("check_kqData.php");

			}
			else
			{
				$resultInfo = "无需考勤";					//返回提示信息和员工姓名
				$errorState = "yes";
			}

		}
	}

	echo json_encode(array("$errorState","$resultInfo","$signTips"));

?>