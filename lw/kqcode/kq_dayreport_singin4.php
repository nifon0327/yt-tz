<?php 
//电信-EWEN
/*
情况4:上班签到在13点-17点,属于早上缺勤,下午迟到

下班签退情况1:在13点-17点签退,早上缺勤,下午迟到并早退				情况2:在17点-18点签退,正常签退,早上缺勤,下午迟到
	    情况3:在18点之后签退,有加班,早上缺勤,下午迟到,其它正常
*/
$ChickIn=rounding_in($AI);//以实际签到计算
//检查早上缺勤的原因,看是否有请假,没有则记缺勤
$AOcolor="class='yellowB'";
if($DateType=="G"){
	include "kqcode/kq_dayreport_leave_e.php";
	}

//情况1://在13点-17点签退,早上缺勤,下午迟到,下午早退,要检查请假情况
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
//情况1结束
	}
else{//17点之后
	$AOcolor="";
	//情况2:在17点-18点,正常签退
	if($AO<=$today_d_time[4]){
		$ChickOut=$toDay." 17:00:00";
		$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;	//需扣除中间休息时间			
		switch($DateType){
			case "G":
				$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;	//当天工作的时间
				$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
				$today_KGTime=8-$today_WorkTime-$qj_Hours;		//当天缺勤工时
				$Sum_KGTime=$Sum_KGTime+$today_KGTime;			//当月缺勤总工时
				break;
			case "F";
				$today_WorkTime=8;													//应到工时
				$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
				$today_FJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;	//法定假日当天加班工时
				$Sum_FJTime=$Sum_FJTime+$today_FJTime;								//当月法定假日加班总工时计算
				break;
			case "X"://休息日
				$today_XJTime=abs(strtotime($ChickOut_temp1)-strtotime($ChickIn))/3600;//休息日当天加班工时
				$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
				break;
			case "Y":
				break;
			case "W":
				$today_WorkTime=abs(strtotime($ChickOut_temp1)-strtotime($ChickIn))/3600-1;//休息日当天加班工时
				$today_WXTime=8-$today_WorkTime;		//当天实际无薪工时
				$Sum_WXTime=$Sum_WXTime+$today_WXTime;	//当月无薪总工时计算
				$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
				$Sum_GJTime=$Sum_GJTime+$today_GJTime;	//当月实到总工时计算
				break;
			}
		}
	else{						//18点之后,有加班的情况
		$ChickOut=rounding_out($AO);
		$ChickOut_temp1=$toDay." 17:00:00";
		
			switch($DateType){
				case "G":
					$today_WorkTime=4;//工作的时间
					$today_WorkTime=abs(strtotime($ChickOut_temp1)-strtotime($ChickIn))/3600;//工作的时间
					$today_GJTime=abs(strtotime($ChickOut)-strtotime($today_d_time[3]))/3600-1;//加班的工时
					$Sum_GJTime=$Sum_GJTime+$today_GJTime;
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;
					$today_KGTime=8-$today_WorkTime-$qj_Hours;//8-当天工作工时-各类请假之和
					$Sum_KGTime=$Sum_KGTime+$today_KGTime;
				break;
				case "F":
					$today_WorkTime=4;
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;
					$today_FJTime=abs(strtotime($ChickOut)-strtotime($today_d_time[3]))/3600-1+$today_WorkTime;//法定假日当天加班工时
					$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
				break;
				case "X":
					$today_XJTime=abs(strtotime($ChickOut)-strtotime($today_d_time[3]))/3600-1+$today_WorkTime;//法定假日当天加班工时
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
?>
