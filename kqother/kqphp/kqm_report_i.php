<?php 
//电信-EWEN
//首先比较此签卡时间属于那个时间段
if($nowCHECKTIME<=$kqDefaultTime[0]){	//时段1-签到:正常，条件是签卡时间少于第一个设定的签到时间
	$AI=$nowCHECKTIME;					//用于显示于页面的值（签卡的实际时间）
	$AIvalue=$kqDefaultTime[0];			//用于计算工时的值
	$todayTimeSetp++;					//已处理签卡总数（-1）
	}
else{//时段1-签到：迟到现象，
	if($nowCHECKTIME<$kqDefaultTime[1]){				//时间段1-签到:迟到
		$AI=$nowCHECKTIME;								//实际签卡时间
		$AIvalue=$AI.":00";	
		//取整处理:向上取整
		$minuteTemp=date("i",strtotime("$AIvalue"))*1;
		if($minuteTemp<30){			
			if($minuteTemp!=0){
				$minuteTemp=30-$minuteTemp;
				}
			}
		else{
			$minuteTemp=60-$minuteTemp;
			}
	
		//取整后的时间
		$AIvalue=date("H:i:s ",strtotime("$AIvalue")+$minuteTemp*60);
		
		if($DateType=="G"){								//如果是工作日,则计算迟到资料：缺勤时间、迟到次数
			$qjS=$toDay." ".$kqDefaultTime[0].":00";	//签卡正常时间
			//$AI要取整
			$qjE=$toDay." ".$AIvalue;					//迟到的签卡时间
			$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qjS' and EndDate>='$qjE'",$link_id);
			if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假，计算请假的工时
				$qj_Type1=$qj_Row1["Type"];
				$qj_Hours1=ceil(abs(strtotime($qjS)-strtotime($qjE))/3600);//向上取整
				switch($qj_Type1){
					case "S":$today_SJTime=$qj_Hours1;break;				//事假
					case "B";$today_BJTime=$qj_Hours1;break;				//病假
					case "X":$today_BXTime=$qj_Hours1;break;				//补休
					case "W":$today_WXJTime=$qj_Hours1;break;				//无薪事、病假
					}
				}//结束有请假
			else{//没有请假，计算迟到的次数，及缺勤工时
				$AIcolor="class='yellowB'";
				$today_InLates=$today_InLates+1;											//当天迟到次数
				$Sum_InLates=$Sum_InLates+1;												//当月迟到次数
				$TimeGap=abs(strtotime($AI)-strtotime($kqDefaultTime[0]))/60;				//迟到分钟
				$This_QQTime=intval($TimeGap/60/0.5)*0.5;									//迟到小时
				$today_QQTime=$today_QQTime+$This_QQTime;									//当天缺勤工时							
				$Sum_QQTime=$Sum_QQTime+$This_QQTime;										//当月缺勤工时统计
//迟到、早退罚扣计算
				if($TimeGap>10){//早退10分钟以上开始罚扣工时
					if($TimeGap<30){//10-30分钟，扣1小时
						$today_BKTime=$today_BKTime+1;$Sum_BKTime=$Sum_BKTime+1;}
					else{
						if($TimeGap<180){//30-180分钟，扣4
							$today_BKTime=$today_BKTime+4;$Sum_BKTime=$Sum_BKTime+4;}
						else{//180分钟以上扣8小时，3小时以上计一天
							$today_BKTime=$today_BKTime+8;$Sum_BKTime=$Sum_BKTime+8;}
						}
					}
//迟到、早退罚扣结束
				}//结束没有请假
			}//结束if($DateType=="G")
			$todayTimeSetp++;//标记第一个上班签卡已登记
		}
	else{//其它时间段检查
		if($nowCHECKTIME<=$kqDefaultTime[2]){		//时间段2-签到:正常
			$BI=$nowCHECKTIME;						//用于显示
			$BIvalue=$kqDefaultTime[2];				//用于计算的时间：默认的签卡时间
			$todayTimeSetp++;
			}
		else{										//时间段2-签到：迟到
			$BI=$nowCHECKTIME;						//用于显示
			$BIvalue=$BI;							//用于计算：实际签卡时间
			$todayTimeSetp++;
			//是工作日则做请假检查
			if($DateType=="G"){
				$qjS=$toDay." ".$kqDefaultTime[2].":00";	
				$qjE=$toDay." ".$BI.":00";
				$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qjS' and EndDate>='$qjE'",$link_id);
				if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假
					$qj_Type1=$qj_Row1["Type"];
					$qj_Hours1=ceil(abs(strtotime($qjS)-strtotime($qjE))/3600);//向上取整
					switch($qj_Type1){
						case "S":$today_SJTime=$today_SJTime+$qj_Hours1;break;				//事假
						case "B";$today_BJTime=$today_BJTime+$qj_Hours1;break;				//病假
						case "X":$today_BXTime=$today_BXTime+$qj_Hours1;break;				//补休
						case "W":$today_WXJTime=$today_WXJTime+$qj_Hours1;break;			//无薪事、病假
						}
					}//结束有请假
				else{//没有请假，计算时间段2的缺勤工时和罚扣工时
					$BIcolor="class='yellowB'";
					$today_InLates=$today_InLates+1;//当天迟到次数
					$Sum_InLates=$Sum_InLates+1;//当月迟到次数
					$TimeGap=abs(strtotime($BI)-strtotime($kqDefaultTime[2]))/60;				//迟到分钟
					$This_QQTime=intval($TimeGap/60/0.5)*0.5;									//迟到小时
					$today_QQTime=$today_QQTime+$This_QQTime;									//当天缺勤工时							
					$Sum_QQTime=$Sum_QQTime+$This_QQTime;										//当月缺勤工时统计
//迟到、早退罚扣计算
					if($TimeGap>10){//早退10分钟以上开始罚扣工时
						if($TimeGap<30){//10-30分钟，扣1小时
							$today_BKTime=$today_BKTime+1;$Sum_BKTime=$Sum_BKTime+1;}
						else{
							if($TimeGap<180){//30-180分钟，扣4
								$today_BKTime=$today_BKTime+4;$Sum_BKTime=$Sum_BKTime+4;}
							else{//180分钟以上扣8小时，3小时以上计一天
								$today_BKTime=$today_BKTime+8;$Sum_BKTime=$Sum_BKTime+8;}
							}
						}
//迟到、早退罚扣结束
					}//结束没有请假
				}//结束 if($DateType=="G")
			}//end 时间段2迟到
		}//end //时间段1，迟到
	}

?>