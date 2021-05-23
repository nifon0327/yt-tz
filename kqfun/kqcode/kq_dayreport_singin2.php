<?php 
/*
情况2:上班签到在8点-12点,属于迟到

下班签退情况1:8点-12点签退,早上早退,下午缺勤;		情况2,12点-13点签退,早上正常上班,下午缺勤
    	情况3:在13点-17点签退,早退				情况4,在17点-18点签退,正常签退
	    情况5:在18点之后签退,有加班
*/
$ChickIn=rounding_in($AI);//计算参数以实际签到时间计算,但签到时间需向上取整(30整除)
$AOcolor="class='yellowB'";

//检查迟到的原因,是否有请假
if($DateType=="G"){
	include "kqcode/kq_dayreport_leave_e.php";//返回请假工时或早退次数
	}

//情况1://8点-12点签退,早上早退,下午缺勤,要检查请假情况
if($AO<$today_d_time[1]){
	$ChickOut=rounding_out($AO);
	//计算工作时间
	$today_WorkTime=(strtotime($ChickOut)-strtotime($ChickIn))/3600;
	$today_WorkTime=$today_WorkTime<=0?0:$today_WorkTime;
	//如果是工作日,则检查请假记录
	switch($DateType){
		case"G":
			include "kqcode/kq_dayreport_leave_s.php";//返回请假工时或早退次数
			$today_KGTime=8-$today_WorkTime-$qj_Hours;//如果早有上请假,则要扣除
			$Sum_KGTime=$Sum_KGTime+$today_KGTime;			//当月旷工总工时计算
			$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
		break;
		case "F":
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
	else{//12点之后的情况
//情况2://12点-13点,上午正常签退,下午缺勤,要检查请假情况
		if($AO<$today_d_time[2]){		
			$ChickOut=$toDay." 12:00:00";
			//计算工作时间
			$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;			
			switch($DateType){
				case "G"://工作日的计算方式
					include "kqcode/kq_dayreport_leave_s.php";//返回请假工时或早退次数
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
					$today_KGTime=8-$today_WorkTime-$qj_Hours;
					$KGTimeSUM=$KGTimeSUM+$today_KGTime;
					break;
				case "F"://法定假日
					$today_FJTime=$today_WorkTime;			//法定假日当天加班工时
					$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
					//$today_WorkTime=0;						//重置工作时间
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
					$today_WXTime=0;								//当天实际无薪工时
					$Sum_WXTime=$Sum_WXTime+$today_WXTime;			//当月无薪总工时计算
					$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
					break;			
					}
//情况2结束
			}
		else{//13点之后的情况
//情况3://在13点-17点,下午早退,要检查请假情况
			if($AO<$today_d_time[3]){	
				$ChickOut=rounding_out($AO);
				$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;
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
				}
			else{						//17点之后
				$AOcolor="";
				if($AO<=$today_d_time[4]){//在17点-18点,正常签退
					$ChickOut=$toDay." 17:00:00";
					switch($DateType){
						case "G":
							$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;	//当天工作的时间
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
							$today_QQTime=8-$today_WorkTime-$qj_Hours;		//当天缺勤工时
							$Sum_QQTime=$Sum_QQTime+$today_QQTime;			//当月缺勤总工时
						break;
						case "F";
							$today_WorkTime=8;													//应到工时
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
							$today_FJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;	//法定假日当天加班工时
							$Sum_FJTime=$Sum_FJTime+$today_FJTime;								//当月法定假日加班总工时计算
						break;
						case "X"://休息日
							$today_XJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;//休息日当天加班工时
							$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
						break;
						case "Y":
						break;
						case "W":
							$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-1;//休息日当天加班工时
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
							$today_WorkTime=abs(strtotime($ChickOut_temp1)-strtotime($ChickIn))/3600-1;	//当天工作的时间
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
							$today_GJTime=abs(strtotime($ChickOut)-strtotime($ChickOut_temp1))/3600-1;	//当天加班的时间
							$Sum_GJTime=$Sum_GJTime+$today_GJTime;			//当月加班总工时
							$today_QQTime=8-$today_WorkTime-$qj_Hours;		//当天缺勤工时
							$Sum_QQTime=$Sum_QQTime+$today_QQTime;			//当月缺勤总工时
						break;
						case "F";
							
							$today_FJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-2;//法定假日当天加班工时
							$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
						break;
						case "X"://休息日
							$today_XJTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600-2;//休息日当天加班工时
							$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
						break;
						case "Y":
						break;
						case "W":
							$today_WorkTime=abs(strtotime($ChickOut_temp1)-strtotime($ChickIn))/3600-2;//休息日当天加班工时
							//上班时间大于8小时，计加班费
							if($today_WorkTime>8){
								$today_GJTime=$today_WorkTime-8;	//多出的加班工时
								$today_WorkTime=8;
								}
							else{
								$today_GJTime=0;
								}
							$today_WXTime=8-$today_WorkTime;		//当天实际无薪工时
							$Sum_WXTime=$Sum_WXTime+$today_WXTime;	//当月无薪总工时计算
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
							$Sum_GJTime=$Sum_GJTime+$today_GJTime;	//当月实到总工时计算
						break;
						}
					}
				}
			}
		}
?>
