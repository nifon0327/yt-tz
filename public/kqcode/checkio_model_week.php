<?php 
/*
//电信-EWEN
计算：	
	1.当天的日期、签到时间、下一天
	2.当天是星期几
	3.是什么类型的日期(工作日、休息日还是法定假日、无薪假日、有薪假日)
*/
$weekInfo="";
$Info="";
$ddSign="";
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
//对调情况落实到个人：因为有时是部分员工对调
$rqddResult = mysql_query("SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE Number='$Number' AND (GDate='$ToDay' or XDate='$ToDay') LIMIT 1",$link_id);
if($rqddRow = mysql_fetch_array($rqddResult)){
	$ddSign=1;//有对调记录
	$weekDayTempX=date("w",strtotime($rqddRow["XDate"]));		//调动的休息日
	$weekDayTempG=date("w",strtotime($rqddRow["GDate"]));	//调动的工作日
	if($DateType=="G"){
		$Info=$weekDayTempX==0?"(调为周日)":"(调为周六)";
		}
	else{
		$Info="(调为周".$Darray[$weekDayTempG].")";
		}
	$DateType=$DateType=="X"?"G":"X";
	}
//星期输出

$weekInfo=$DateType."-".$weekDay.$Info;
//4		
?>