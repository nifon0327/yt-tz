<?php 
/*
情况2:上班签到在12点-13点之间,属于早上缺勤

下班签退情况1:12点-13点签退,无效记录,整天缺勤
    	情况2:在13点-17点签退,早上缺勤,下午早退				情况3,在17点-18点签退,正常签退,早上缺勤
	    情况4:在18点之后签退,有加班,早上缺勤,其它正常
*/
$ChickIn=$today_d_time[2];//从13点开始计算,
$AOcolor="class='yellowB'";
//检查早上缺勤的原因,看是否有请假,没有则记缺勤
if($DateType=="G"){
	include "kqcode/kq_dayreport_leave_e.php";
	}

//情况1://12点-13点签退,上午缺勤，下午也缺勤,要检查请假情况,
if($AO<$today_d_time[2]){		
	$today_WorkTime=0;
	if($DateType=="G"){
		//检查下午是否有请假
		include "kqcode/kq_dayreport_leave_s.php";//返回请假工时或早退次数
		$today_KGTime=8-$qj_Hours;
		$Sum_KGTime=$Sum_KGTime+$today_KGTime;
		$today_OutEarlys=0;
		$Sum_OutEarlys=$Sum_OutEarlys-1;
		}	
//情况1结束
	}
else{//13点之后的情况
//情况2://在13点-17点,下午早退,要检查请假情况
	if($AO<$today_d_time[3]){	
		$ChickOut=rounding_out($AO);
		$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;
		
		switch($DateType){
			case "G"://工作日的计算方式
				//检查是否有请假,如果没有请假,则计算早退次数及缺勤时间
				include "kqcode/kq_dayreport_leave_s.php";//返回各类请假工时或早退次数以及各类请假统计
				$today_KGTime=8-$today_WorkTime-$qj_Hours;//8-当天工作工时-各类请假之和
				$Sum_KGTime=$Sum_KGTime+$today_KGTime;
				$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
				break;
			case "F"://法定假日
				$today_FJTime=$today_WorkTime;			//法定假日当天加班工时
				$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
				break;
			case "X"://休息日
				$today_XJTime=$today_WorkTime;			//休息日当天加班工时
				$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
				$today_WorkTime=0;						//重置工作时间
				break;
			case "Y"://公司有薪假日，如年假
				break;
			case "W"://无薪假日，上班当正常工作日计算
				//当天的无新工时=8-已上班工时
				$today_WXTime=8-$today_WorkTime;				//当天实际无薪工时
				$Sum_WXTime=$Sum_WXTime+$today_WXTime;			//当月无薪总工时计算
				$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
				break;			
				}
//情况2结束
		}
	else{						//17点之后
	$AOcolor="";
//情况3:在17点-18点,正常签退
		if($AO<=$today_d_time[4]){
			$ChickOut=$thisDay." 17:00:00";
			$today_WorkTime=4;		
			switch($DateType){
				case "G":
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
					$today_QQTime=8-$today_WorkTime-$qj_Hours;		//当天缺勤工时
					$Sum_QQTime=$Sum_QQTime+$today_QQTime;			//当月缺勤总工时
					break;
				case "F";
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
					$today_FJTime=$today_WorkTime;	//法定假日当天加班工时
					$Sum_FJTime=$Sum_FJTime+$today_FJTime;								//当月法定假日加班总工时计算
					$today_WorkTime=0;
					break;
				case "X"://休息日
					$today_XJTime=$today_WorkTime;//休息日当天加班工时
					$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
					$today_WorkTime=0;
					break;
				case "Y":
					break;
				case "W":
					$today_WXTime=8-$today_WorkTime;		//当天实际无薪工时
					$Sum_WXTime=$Sum_WXTime+$today_WXTime;	//当月无薪总工时计算
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
					$Sum_GJTime=$Sum_GJTime+$today_GJTime;	//当月实到总工时计算
					break;
				}
			}
		else{						//18点之后,有加班的情况
			$ChickOut=rounding_out($AO);			
			switch($DateType){
				case "G":
					$today_WorkTime=4;//工作的时间
					$today_GJTime=abs(strtotime($ChickOut)-strtotime($today_d_time[3]))/3600-1;//加班的工时
					$Sum_GJTime=$Sum_GJTime+$today_GJTime;
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;
					$today_KGTime=8-$today_WorkTime-$qj_Hours;//8-当天工作工时-各类请假之和
					$Sum_KGTime=$Sum_KGTime+$today_KGTime;
				break;
				case "F":
					$today_FJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;//法定假日当天加班工时
					$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
				break;
				case "X":
					$today_XJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;//法定假日当天加班工时
					$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月法定假日加班总工时计算
				break;
				case "Y":
				break;
				case "W":
					$today_WorkTime=4;
					$today_WXTime=8-$today_WorkTime;				//当天实际无薪工时
					$Sum_WXTime=$Sum_WXTime+$today_WXTime;			//当月无薪总工时计算
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
				break;
				}	
			
			}
		}
	}
?>
