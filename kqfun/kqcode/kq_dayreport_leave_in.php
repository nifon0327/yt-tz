<?php 
//请假分析代码，请假的时间包含当天时间：即请假的起始时间早于当天的考勤起始时间；请假的结束时间迟于考勤的结束时间
//Number=》DefaultNumber******$DataIn.电信---yang 20120801

$qj_STemp=$today_d_time[0];
$qj_ETemp=$today_d_time[3];
$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qj_STemp' and EndDate>='$qj_ETemp'",$link_id);
if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假		

	$qj_Type=$qj_Row1["Type"];		//请假结束的时间
	$HoursTemp=8;	//向上取整
	$qj_Hours=$HoursTemp;
	//分请假类型
	switch($qj_Type){
		case "S":$today_SJTime=$qj_Hours;$Sum_SJTime=$Sum_SJTime+$today_SJTime;break;		//当天事假类加
		case "B";$today_BJTime=$qj_Hours;$Sum_BJTime=$Sum_BJTime+$today_BJTime;break;		//当天病假类加
		case "X":$today_BXTime=$qj_Hours;$Sum_BXTime=$Sum_BXTime+$today_BXTime;break;		//当天补休类加
		case "W":$today_WXJTime=$qj_Hours;$Sum_WXJTime=$Sum_WXJTime+$today_WXJTime;break;		//当天无薪请假类加
		case "L":$today_LJTime=$qj_Hours;$Sum_LJTime=$Sum_LJTime+$today_LJTime;break;		//当天年假类加
		}
	}
else{
	$qj_Hours=0;
	}
?>