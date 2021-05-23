<?php 
if($AI<=$InTime){	//正常签到
	$AIcolor="";
	$ChickIn=$InTime;
	}
else{
	$AIcolor="class='yellowB'";
	//
	$ChickIn=rounding_in($AI);
	//取整
	if($InLate>0){
		//计算迟到
		}	
	}

if($AO>=$OutTime){	//正常签退
	$AOcolor="";
	}
else{
	$AOcolor="class='yellowB'";
	if($OutEarly>0){
		//计算早退
		}	
	}
$ChickOut=rounding_out($AO);

//总的工作时间
$all_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn)-$RestTime*60)/3600;	
	switch($DateType){
		case "G"://工作日的计算方式
			if($all_WorkTime>=8){
				$today_WorkTime=8;
				$today_GJTime=$all_WorkTime-8;
				}
			else{
				$today_WorkTime=$all_WorkTime;
				//检查有没请假记录
				include "kqcode/kq_dayreport_leave_s.php";//返回请假工时
				$today_QQTime=8-$today_WorkTime-$qj_Hours;		//当天旷工时间=8-已上时间-已请假时间
				$Sum_QQTime=$Sum_QQTime+$today_QQTime;
				}
			$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;
			$Sum_GJTime=$Sum_GJTime+$today_GJTime;
		break;
		case "X":
		$today_XJTime=$all_WorkTime;
		$Sum_XJTime=$Sum_XJTime+$today_XJTime;
		break;
		case "F":
		$today_FJTime=$all_WorkTime;
		$Sum_FJTime=$Sum_FJTime+$today_FJTime;
		break;
		}
	if($TimeType==0){
		$AIcolor="class='greenB'";
		$AOcolor="class='greenB'";
		if(substr($ChickOut,0,10)!=substr($ChickIn,0,10) || substr($ChickIn,11,2)<5){//17点至4点上班为夜班
			$today_YBs=1;
			$Sum_YBs=$Sum_YBs+1;
			}
		}

?>