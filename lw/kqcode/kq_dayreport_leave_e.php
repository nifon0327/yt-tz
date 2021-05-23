<?php 
//请假分析代码,最后的请假段,即迟到现象
//电信-EWEN
$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  left(EndDate,10)='$toDay'",$link_id);
if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假		

	$qj_StartDate=$qj_Row1["StartDate"];	//请假的起始时间
	$qj_EndDate=$qj_Row1["EndDate"];		//请假结束的时间
	$qj_Type=$qj_Row1["Type"];				//请假的类别
	
	if(substr($qj_StartDate,0,10)!=$toDay){	//跨日请假
		$qj_EValue=$qj_EndDate;
		$qj_SValue=$toDay." 08:00:00";	//如果是跨日则只从8点起计算
		}
	else{//起始与结束为同一天,要看是不是跨休息时间
		if($qj_StartDate<$today_d_time[1] && $qj_EndDate>$today_d_time[2]){
			//不同时段
			$TimeOut=1;//中途休息的1小时需扣除
			}
		else{
			$TimeOut=0;
			}
		$qj_EValue=$qj_EndDate;
		$qj_SValue=$qj_StartDate;
		}
		$HoursTemp=ceil(abs(strtotime($qj_EValue)-strtotime($qj_SValue))/3600);	//向上取整
		$HoursTemp=$HoursTemp-$TimeOut;			//请假工时
		$qj_Hours=$HoursTemp;
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
	//计算迟到和缺勤
	$LateNumber=1;
	$LateNumberSUM=$LateNumberSUM+1;
		$today_InLates=1;	//当天迟到次数
	$Sum_InLates=$Sum_InLates+$today_InLates;

	}

?>