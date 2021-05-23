<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include "$path/basic/parameter.inc";
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
    include_once('../../FactoryCheck/FactoryClass/AttendanceTimeSetup.php');
    include_once('../../FactoryCheck/FactoryClass/AttendanceDatetype.php');
    include_once('../../FactoryCheck/FactoryClass/AttendanceInfo.php');
    include_once('../../FactoryCheck/FactoryClass/AttendanceCalculate.php');
	$nowMonth=date("Y-m");
	$number = $_POST["Number"];
	//$number = '10392';
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

	if($factoryCheck == 'on'){
		$titles = array("日期","星期","日期类别","签到记录","签退记录","应到工时","实到工时","1.5倍工时","2倍工时","3倍工时","迟到","早退","事假","病假","无薪假","年假","补休","婚假","丧假","产假", "工伤","缺勤工时","旷工工时","无效工时", "有薪工时");
		$widths = array("40","80","80","100","100","100","100","80","80","80","80","80","60","60","60","60","60","60","60","60","60","100","100","100", "100");
	}
	else{
		$titles = array("日期","星期","日期类别","签到记录","签退记录","应到工时","实到工时","1.5倍工时","2倍工时","3倍工时","迟到","早退","事假","病假","无薪假","年假","补休","婚假","丧假","产假", "工伤","缺勤工时","旷工工时","夜班","无效工时", "有薪工时");
		$widths = array("40","80","80","100","100","100","100","80","80","80","80","80","60","60","60","60","60","60","60","60","60","100","100","60","100", "100");
	}

	$attendanceInfomation = array();
	$attendanceStatistic = new AttendanceStatistic();
	$staff = new AttendanceAvatar($number, $DataIn, $DataPublic, $link_id);
	if($staff->getKqSign() == 3){
		echo json_encode(array(
			"titles" => $titles,
			"widths" => $widths,
			"inquerys" => $attendanceInfomation
		));
		return ;
	}

	if($factoryCheck == 'on'){
		$attendanceStatistic = new AttendanceInfo();
	    $timeSetup = new AttendanceTimeSetup('d7check', $DataPublic, $link_id);
	    $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
	    for($i=0;$i<$Days;$i++){
	        $j=$i+1;
	        $CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
	        $Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	        $weekDay=date("w",strtotime($CheckDate));    
	        $weekInfo="星期".$Darray[$weekDay];
	        $checkIsOutOfWorkResult=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A
	                         Left Join $DataPublic.dimissiondata B On B.Number = A.Number 
	                         WHERE A.Number='".$staff->getStaffNumber()."' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='".$staff->getStaffNumber()."' and B.OutDate<'$CheckDate'))",$link_id);
	        $attendanceTime = $timeSetup->setupTime($staff->getStaffNumber(), $CheckDate);
	        $sheet = new WorkScheduleSheet($staff->getStaffNumber(), $CheckDate, $attendanceTime['start'], $attendanceTime['end']);
	        $datetype = $datetypeModle->getDatetype($staff->getStaffNumber(), $CheckDate, $sheet);
	        if(!$checkDRow = mysql_fetch_array($checkIsOutOfWorkResult)){
                //获取时间
                $datetypeInfo = '';
                if(($datetype['morning'] !== 'G' && $datetype['afternoon'] !== 'G') && $attendanceTime['start'] != ''){
                    $attendanceTime['start'] = '';
                    $attendanceTime['end'] = '';
                }
                
                $infoCalculator = new AttendanceCalculate($DataIn, $DataPublic, $link_id);
                $attendanceResult = $infoCalculator->calculateTime($staff->getStaffNumber(), $attendanceTime['start'], $attendanceTime['end'], $sheet, $CheckDate, $datetype);
            }else{
                $infoCalculator = new AttendanceCalculate($DataIn, $DataPublic, $link_id);
                $attendanceResult = $infoCalculator->setOutOfWorkState($datetype);
                $datetypeInfo = '(离)';
            }

            if($attendanceResult['lackWorkHours'] < 0 && strtotime($attendanceTime['start']) <= strtotime($CheckDate.' '.$sheet->mCheckTime['start'])){
                $attendanceResult['workHours'] += $attendanceResult['lackWorkHours'];
                $attendanceTime['end'] = date('Y-m-d H:i', strtotime($attendanceTime['end'])+$attendanceResult['lackWorkHours']*3600);
                $attendanceResult['lackWorkHours'] = '';
            }
	        //保存数据
	        $startTime = substr($attendanceTime['start'], 11, 5)?substr($attendanceTime['start'], 11, 5):'';
	        $endTime = substr($attendanceTime['end'], 11, 5)?substr($attendanceTime['end'], 11, 5):'';
	        $attendanceInfomation[] = array(  substr($CheckDate, 8, 2),
	        								  $weekInfo,
	        							      $showDateType.$datetypeInfo,
	        							      $startTime,
	        							      $endTime,
	        							      $attendanceResult['defaultWorkHours'],
	        							      $attendanceResult['workHours'],
	        							      $attendanceResult['workdayOt'],
	        							      $attendanceResult['weekdayOt'],
	        							      $attendanceResult['holidayOt'],
	        							      $attendanceResult['beLate'],
	        							      $attendanceResult['beEarly'],
	        							      $attendanceResult['personalLeave'],
	        							      $attendanceResult['sickLeave'],
	        							      $attendanceResult['noPayLeave'],
	        							      $attendanceResult['annualLeave'],
	        							      $attendanceResult['bxLeave'],
	        							      $attendanceResult['marrayLeave'],
	        							      $attendanceResult['deadLeave'],
	        							      $attendanceResult['birthLeave'],
	        							      $attendanceResult['hurtLeave'],
	        							      $attendanceResult['lackWorkHours'],
	        							      $attendanceResult['kgHours'],
	        							      $attendanceResult['noPayHours'],
	        							      $attendanceResult['dkHours']);
	        //统计数据
	        $attendanceStatistic->statistic($attendanceResult);
	    }
	    $totleStatistic = $attendanceStatistic->outputByTag();
	    $attendanceInfomation[] = array("合计",
										"",
										"",
										"",
										"",
										$totleStatistic["defaultWorkHours"].'',
										$totleStatistic["workHours"].'',
										$totleStatistic['workdayOt'],
										$totleStatistic['weekdayOt'],
										$totleStatistic['holidayOt'],
										$totleStatistic["beLate"].'',
										$totleStatistic["beEarly"].'',
										$totleStatistic["personalLeave"],
										$totleStatistic["sickLeave"],
										$totleStatistic["noPayLeave"],
										$totleStatistic["annualLeave"],
										$totleStatistic["bxLeave"],
										$totleStatistic['marrayLeave'],
										$totleStatistic["deadLeave"],
										$totleStatistic["birthLeave"],
										$totleStatistic["hurtLeave"],
										$totleStatistic["lackWorkHours"],
										$totleStatistic["kgHours"],
										$totleStatistic["noPayHours"],
										$totleStatistic["dkHours"]
								  );
	}
	else{
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
										$statisticInfomation['defaultWorkHours'].'',
										$statisticInfomation["workHours"].'',
										$statisticWorkdayOverTime."",
										$statisticWeekdayOverTime."",
										$statisticHolidayOverTime."",
										$statisticInfomation["beLate"].'',
										$statisticInfomation["beEarly"].'',
										$statisticInfomation["personalLeave"].'',
										$statisticInfomation["sickLeave"]."",
										$statisticInfomation["noPayLeave"].'',
										$statisticInfomation["annualLeave"].'',
										$statisticInfomation["bxLeave"].'',
										$statisticInfomation["marrayLeave"].'',
										$statisticInfomation["deadLeave"].'',
										$statisticInfomation["birthLeave"].'',
										$statisticInfomation["hurtLeave"].'',
										$statisticInfomation["lackWorkHours"].'',
										$statisticInfomation["kgHours"].'',
										$statisticInfomation["nightShit"].'',
										$statisticInfomation["noPayHours"].'',
										$statisticInfomation["dkHours"].''
								  );
	}

	echo json_encode(array(
			"titles" => $titles,
			"widths" => $widths,
			"inquerys" => $attendanceInfomation
	 	));

?>