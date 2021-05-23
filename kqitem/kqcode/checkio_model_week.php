<?php 
/*
//电信-EWEN
计算：	
	1.当天的日期、签到时间、下一天
	2.当天是星期几
	3.是什么类型的日期(工作日、休息日还是法定假日、无薪假日、有薪假日)
*/
$weekInfo="";
//1
if($KrSign==0){//非跨日则计算考勤的日期
	$ToDay=date("Y-m-d",strtotime($CheckTime));							//签卡当天日期
	}
$ioTime=date("H:i",strtotime($CheckTime));
//2
$NextDay=date("Y-m-d",strtotime("$ToDay+1 days"));				//签卡下一天日期
$weekN=date("w",strtotime($ToDay));
$weekDay="星期".$Darray[$weekN];			//当天属于星期几 
//3
$DateType=($weekN==6 || $weekN==0)?"X":"G";
$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$ToDay'",$link_id);
if($holidayRow = mysql_fetch_array($holidayResult)){
	$jbTimes=$holidayRow["jbTimes"];
	switch($holidayRow["Type"]){
	case 0:
		$DateType="W";
		$weekInfo="<br><div class='yellowB'>$holidayRow[Name]</div>";
		break;
	case 1:
		$DateType="Y";
		$weekInfo="<br><div class='yellowB'>$holidayRow[Name]</div>";
		break;
	case 2:
		$DateType="F";
		$weekInfo="<br><div class='yellowB'>$holidayRow[Name]</div>";
		break;
		}
	}
$rqddResult = mysql_query("SELECT Id FROM $DataIn.kqrqdd WHERE (GDate='$ToDay' or XDate='$ToDay') and Number='$Number'",$link_id);
if($rqddRow = mysql_fetch_array($rqddResult)){			
	$weekInfo=$DateType=="X"?"<br><div class='yellowB'>调为工作日</div>":"<br><div class='yellowB'>调为休息日</div>";
	$DateType=$DateType=="X"?"G":"X";
	}
//星期输出
$weekInfo=$DateType."-".$weekDay.$weekInfo;
//4
?>