
<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include "$path/basic/parameter.inc";
	include_once("$path/ipdAPI/Attendance/AttendanceClass/AttendanceDecorator.php");
	include_once("$path/ipdAPI/Attendance/AttendanceClass/AttendanceStatistic.php");
	
	$nowMonth=date("Y-m");
	$number = $_POST["idNum"];
	if($number == ""){
		return "";
	}
	if(strlen($number) > 8){
		$number = substr($number, strlen($number)-8, 8);
	}
	$CheckMonth = $_POST["targetDate"];
	//$CheckMonth = '2014-08';
	if($CheckMonth==""){			//如果没有填月份，则为默认的现在月份
		$CheckMonth=$nowMonth;
	}

	$FristDay=$CheckMonth."-01";
	$EndDay=date("Y-m-t",strtotime($FristDay));
	if($CheckMonth==$nowMonth){
		$Days=date("d")-1;
	}
	else{
		$Days=date("t",strtotime($FristDay));
	}
	//echo $Days;
	$attendanceInfomation = array();
	$attendanceStatistic = new AttendanceStatistic();
	$staff = new AttendanceAvatar($number, $DataIn, $DataPublic, $link_id);
	for($i=0;$i<$Days;$i++){
		//echo "here";
		$checkDate=date("Y-m-d",strtotime("$FristDay + $i days"));
		$cloneLeaveAvatar = clone $staff;
		$cloneLeaveAvatar->setupAttendanceData($staff->getStaffNumber(), $checkDate, $DataIn, $DataPublic, $link_id);
		$cloneLeaveAvatar->attendanceSetup($DataIn, $DataPublic, $link_id);
		//计算当天的数据
		$dayAttendanceInfomation =  $cloneLeaveAvatar->getInfomationByTag();

		//统计数据
		$attendanceStatistic->statistic($dayAttendanceInfomation);

		//按需求格式化输出
		$workDayOverTime = ($dayAttendanceInfomation["workOtHours"]+$dayAttendanceInfomation["workZlHours"] == "0")?"":$dayAttendanceInfomation["workOtHours"]+$dayAttendanceInfomation["workZlHours"];

		$weekDayOverTime = ($dayAttendanceInfomation["weekOtTime"]+$dayAttendanceInfomation["weekZlHours"] == "0")?"":$dayAttendanceInfomation["weekOtTime"]+$dayAttendanceInfomation["weekZlHours"];

		$holidayOverTime = ($dayAttendanceInfomation["holidayOtHours"]+$dayAttendanceInfomation["holidayZlHours"] == "0")?"":$dayAttendanceInfomation["holidayOtHours"]+$dayAttendanceInfomation["holidayZlHours"];

		$attendanceInfomation[] = array(substr($dayAttendanceInfomation["checkDay"], 8, 2), 
										$dayAttendanceInfomation["weekDay"]."", 
										$dayAttendanceInfomation["state"]."", 
										$dayAttendanceInfomation["startTime"]."", 
										$dayAttendanceInfomation["endTime"]."", 
										$dayAttendanceInfomation["defaultWorkHours"], 
										$dayAttendanceInfomation["workHours"], 
										$workDayOverTime."", 
										$weekDayOverTime."", 
										$holidayOverTime."", 
										$dayAttendanceInfomation["beLate"], 
										$dayAttendanceInfomation["beEarly"], 
										$dayAttendanceInfomation["personalLeave"], 
										$dayAttendanceInfomation["sickLeave"], 
										$dayAttendanceInfomation["noPayLeave"], 
										$dayAttendanceInfomation["annualLeave"], 
										$dayAttendanceInfomation["bxLeave"], 
										$dayAttendanceInfomation["marrayLeave"], 
										$dayAttendanceInfomation["deadLeave"], 
										$dayAttendanceInfomation["birthLeave"], 
										$dayAttendanceInfomation["lackWorkHours"], 
										$dayAttendanceInfomation["kgHours"], 
										$dayAttendanceInfomation["nightShit"], 
										$dayAttendanceInfomation["noPayHours"]);
	}

	//格式化统计数据
	$statisticInfomation = $attendanceStatistic->getStatisticByTag();
	$statisticWorkdayOverTime = $statisticInfomation["workOtHours"] + $statisticInfomation["workZlHours"];
	$statisticWorkdayOverTime = $statisticWorkdayOverTime==0?"":$statisticWorkdayOverTime."";
	$statisticWeekdayOverTime = $statisticInfomation["weekOtTime"] + $statisticInfomation["weekZlHours"];
	$statisticWeekdayOverTime = $statisticWeekdayOverTime==0?"":$statisticWeekdayOverTime."";
	$statisticHolidayOverTime = $statisticInfomation["holidayOtHours"] + $statisticInfomation["holidayZlHours"];
	$statisticHolidayOverTime = $statisticHolidayOverTime==0?"":$statisticHolidayOverTime."";
	$attendanceInfomation[] = array("合计",
									" ",
									" ",
									" ",
									" ",
									$statisticInfomation["defaultWorkHours"]."",
									$statisticInfomation["workHours"]."",
									$statisticWorkdayOverTime."",
									$statisticWeekdayOverTime."",
									$statisticHolidayOverTime."",
									$statisticInfomation["beLate"]."",
									$statisticInfomation["beEarly"]."",
									$statisticInfomation["personalLeave"]."",
									$statisticInfomation["sickLeave"]."",
									$statisticInfomation["noPayLeave"]."",
									$statisticInfomation["annualLeave"]."",
									$statisticInfomation["bxLeave"]."",
									$statisticInfomation["marrayLeave"]."",
									$statisticInfomation["deadLeave"]."",
									$statisticInfomation["birthLeave"]."",
									$statisticInfomation["lackWorkHours"]."",
									$statisticInfomation["kgHours"]."",
									$statisticInfomation["nightShit"]."",
									$statisticInfomation["noPayHours"].""
									);

	echo json_encode($attendanceInfomation);

?>