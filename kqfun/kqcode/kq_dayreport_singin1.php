<?php 
/******$DataIn.电信---yang 20120801
情况2:上班签到在8点前,正常签到:
下班签退情况1:8点-12点签退,早上早退,下午缺勤;		情况2,12点-13点签退,早上正常上班,下午缺勤
	    情况3:在13点-17点签退,早退					情况4,在17点-18点签退,正常签退
		情况5:在18点之后签退,有加班
*/

$ChickIn=$today_d_time[0];//按"当天 08:00:00"计算签到时间
$AOcolor="class='yellowB'";
if($AO<$today_d_time[0]){//如果没上班就签卡，原则上不允许
	switch($DateType){
		case "G":
		include "kqcode/kq_dayreport_leave_s.php";//返回请假工时
		$today_KGTime=8-$qj_Hours;		//当天旷工时间=8-已上时间-已请假时间
		$Sum_KGTime=$Sum_KGTime+$today_KGTime;			//当月旷工总工时计算
		break;
		case "F":
		$today_WorkTime=8;
		break;
		}
	}
else{
//情况1://8点-12点签退,早上早退,下午缺勤,要检查请假情况
if($AO<$today_d_time[1]){
	$ChickOut=rounding_out($AO);					//要取整处理
	//计算工作时间
	$today_WorkTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;	
	switch($DateType){
		case "G"://工作日的计算方式
			//查找当天的请假记录,因是早退,所以为起始请假日,请假的日期与考勤的日期一致即是
			//调入请假分析
			include "kqcode/kq_dayreport_leave_s.php";//返回请假工时
			//如果缺勤超过30分钟，则做旷工，否则只做缺勤处理
			if($QQ_Minute<30){				//缺勤处理
				$today_QQTime=8-$today_WorkTime-$qj_Hours;
				$Sum_QQTime=$Sum_QQTime+$today_QQTime;
				}
			else{							//旷工处理
				$today_KGTime=8-$today_WorkTime-$qj_Hours;//8-当天工作工时-各类请假之和
				$Sum_KGTime=$Sum_KGTime+$today_KGTime;
				}
			$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
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
			$today_WXTime=8-$today_WorkTime;				//当天实际无薪工时
			$Sum_WXTime=$Sum_WXTime+$today_WXTime;			//当月无薪总工时计算
			$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
			break;			
		}
//情况1结束
	}
	else{//12点之后的情况
//OK- 情况2://12点-13点,上午正常签退,下午缺勤,要检查请假情况
		if($AO<$today_d_time[2]){		
			$today_WorkTime=4;
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
					$today_WorkTime=0;						//重置工作时间
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
//OK-情况2结束
			}
		else{//13点之后的情况
				//  情况3://在13点-17点,下午早退,要检查请假情况
			if($AO<$today_d_time[3]){	
				$ChickOut=rounding_out($AO);
				$allTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;
				$today_WorkTime=$allTime-1;
				switch($DateType){
					case "G"://工作日的计算方式
						//检查是否有请假,如果没有请假,则计算早退次数及缺勤时间
						include "kqcode/kq_dayreport_leave_s.php";//返回各类请假工时或早退次数以及各类请假统计
						
						//如果缺勤超过30分钟，则做旷工，否则只做缺勤处理
						if($QQ_Minute<30){				//缺勤处理
							$today_QQTime=8-$today_WorkTime-$qj_Hours;
							$Sum_QQTime=$Sum_QQTime+$today_QQTime;
							}
						else{							//旷工处理
							$today_KGTime=8-$today_WorkTime-$qj_Hours;//8-当天工作工时-各类请假之和
							$Sum_KGTime=$Sum_KGTime+$today_KGTime;
							}
						$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
						break;
					case "F"://法定假日
						$today_FJTime=$today_WorkTime;			//法定假日当天加班工时
						$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
						$today_WorkTime=0;						//重置工作时间
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
//OK -在17点-18点,正常签退
				$AOcolor="";
				if($AO<=$today_d_time[4]){
					//分工作日或休息日
					//分工作日和休息日处理
					$today_WorkTime=8;				//当天实到工时
					switch($DateType){
						case "G"://工作日的计算方式
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
						break;
						case "F"://法定假日
							$today_FJTime=$today_WorkTime;			//法定假日当天加班工时
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
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
//OK- 17-18点签退结束
					}
				else{						
//OK-18点之后,有加班的情况;上班时间为8小时，加班时间=总时间-10小时（除正常工时和休息工时）
					$ChickOut=rounding_out($AO);
					$allTime=abs(strtotime($ChickOut)-strtotime($ChickIn))/3600;	//总工时
					
					//分工作日和休息日处理
					switch($DateType){
						case "G"://工作日的计算方式
							
							$today_WorkTime=8;								//当天实到工时
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
							$today_GJTime=$allTime-10;						//当天加点工时
							$Sum_GJTime=$Sum_GJTime+$today_GJTime;			//当月加点总工时计算
						break;
						case "F"://法定假日
							$today_WorkTime=8;
							$today_FJTime=$allTime-$today_WorkTime-2;			//法定假日当天加班工时
							$Sum_FJTime=$Sum_FJTime+$today_FJTime;	//当月法定假日加班总工时计算
							$today_WorkTime=0;						//重置工作时间
							break;
						case "X"://休息日
							$today_WorkTime=8;
							$today_XJTime=$allTime-today_WorkTime-2;			//休息日当天加班工时
							$Sum_XJTime=$Sum_XJTime+$today_XJTime;	//当月休息日加班总工时计算
							$today_WorkTime=0;						//重置工作时间
							break;
						case "Y"://公司有薪假日，如年假
							
							break;
						case "W"://无薪假日，上班当正常工作日计算
							//当天的无新工时=8-已上班工时
							$today_WorkTime=8;	
							$today_WXTime=0;								//当天实际无薪工时
							$Sum_WXTime=$Sum_WXTime+$today_WXTime;			//当月无薪总工时计算
							$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;	//当月实到总工时计算
							$today_GJTime=$allTime-10;						//当天加点工时
							$Sum_GJTime=$Sum_GJTime+$today_GJTime;			//当月加点总工时计算
							$today_WorkTime=0;
							break;			
						}
//OK-结束18点之后						
					}
				}
			}
		}
	}
//分工作日还是休息日进行统计


?>