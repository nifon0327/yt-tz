<?php 
//电信-EWEN
/*
情况5:上班签到在17点-18点,白天没上班

下班签退情况
	1:在17-18点签退,无效
	2:在18点后签退
*/
$ChickIn=$toDay." 18:00:00";//以实际签到计算
//检查早上缺勤的原因,看是否有请假,没有则记缺勤
if($DateType=="G"){
	include "kqcode/kq_dayreport_leave_e.php";
	}

//情况1://17-18点签退,早上缺勤,下午迟到,下午早退,要检查请假情况
if($AO<$today_d_time[4]){
	switch($DateType){
		case "G"://工作日的计算方式
			//检查是否有请假,如果没有请假,则计算早退次数及缺勤时间
			include "kqcode/kq_dayreport_leave_s.php";//返回各类请假工时或早退次数以及各类请假统计
			$today_KGTime=8-$qj_Hours;//8-当天工作工时-各类请假之和
			$Sum_KGTime=$Sum_KGTime+$today_KGTime;
			break;
			}
//情况1结束
	}
else{//18点之后
	$ChickOut=rounding_out($AO);		
	
	switch($DateType){
		case "G":
			$today_GJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;//加班的时间
			$Sum_GJTim=$Sum_GJTim+$today_GJTime;
			$today_KGTime=8-$today_WorkTime-$qj_Hours;		//当天缺勤工时
			$Sum_KGTime=$Sum_KGTime+$today_KGTime;			//当月缺勤总工时
			break;
		case "F":
			$today_FJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;	//法定假日当天加班工时
			$Sum_FJTime=$Sum_FJTime+$today_FJTime;								//当月法定假日加班总工时计算
			break;
		case "X":
			$today_XJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;//休息日当天加班工时
			$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
			break;
		case "Y":
		
			break;
		case "W":
			$today_GJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;//休息日当天加班工时
			$Sum_GJTime=$Sum_GJTime+$today_GJTime;	//当月休息日加班总工时计算
			break;
		}		
	}
?>
