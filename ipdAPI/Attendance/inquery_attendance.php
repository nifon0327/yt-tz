<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include "$path/basic/parameter.inc";
	include_once("$path/ipdAPI/Attendance/AttendanceClass/AttendanceDecorator.php");
	include_once("$path/ipdAPI/Attendance/AttendanceClass/AttendanceStatistic.php");
	
	$nowMonth=date("Y-m");
	$number = $_POST["Number"];
	//$number = "11100";
	if($number == ""){
		return "";
	}

	$CheckMonth = $_POST["CheckMonth"];
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

	$attendanceInfomation = array();
	$attendanceStatistic = new AttendanceStatistic();
	$staff = new AttendanceAvatar($number, $DataIn, $DataPublic, $link_id);
	for($i=0;$i<$Days;$i++){
		$checkDate=date("Y-m-d",strtotime("$FristDay + $i days"));
		$cloneLeaveAvatar = clone $staff;
		$cloneLeaveAvatar->setupAttendanceData($number, $checkDate, $DataIn, $DataPublic, $link_id);
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
										$dayAttendanceInfomation["hurtLeave"], 
										$dayAttendanceInfomation["lackWorkHours"], 
										$dayAttendanceInfomation["kgHours"], 
										$dayAttendanceInfomation["nightShit"], 
										$dayAttendanceInfomation["noPayHours"], 
										$dayAttendanceInfomation["dkHours"]);
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
									"",
									"",
									"",
									"",
									$statisticInfomation["defaultWorkHours"],
									$statisticInfomation["workHours"],
									$statisticWorkdayOverTime,
									$statisticWeekdayOverTime,
									$statisticHolidayOverTime,
									$statisticInfomation["beLate"],
									$statisticInfomation["beEarly"],
									$statisticInfomation["personalLeave"],
									$statisticInfomation["sickLeave"],
									$statisticInfomation["noPayLeave"],
									$statisticInfomation["annualLeave"],
									$statisticInfomation["bxLeave"],
									$statisticInfomation["marrayLeave"],
									$statisticInfomation["deadLeave"],
									$statisticInfomation["birthLeave"],
									$statisticInfomation["hurtLeave"],
									$statisticInfomation["lackWorkHours"],
									$statisticInfomation["kgHours"],
									$statisticInfomation["nightShit"],
									$statisticInfomation["noPayHours"],
									$statisticInfomation["dkHours"]
							  );


	$titles = array("日期","星期","日期类别","签到记录","签退记录","应到工时","实到工时","1.5倍工时","2倍工时","3倍工时","迟到","早退","事假","病假","无薪假","年假","补休","婚假","丧假","产假", "工伤","缺勤工时","旷工工时","夜班","无效工时", "有薪工时");
	$widths = array("40","80","80","100","100","100","100","80","80","80","80","80","60","60","60","60","60","60","60","60","60","100","100","60","100", "100");

	echo json_encode(array(

			"titles" => $titles,
			"widths" => $widths,
			"inquerys" => $attendanceInfomation
		));

?>