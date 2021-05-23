<?php 
//电信-EWEN
if($nowCHECKTIME<$kqDefaultTime[1]){				//时间段1-下班签退：早退现象
	$AO=$nowCHECKTIME;								//用于显示：=实际签退时间
	$AOvalue=$AO;									//用于计算的时间=实际签退时间
	if($DateType=="G"){								//计算早退数据或请假
		$qjS=$toDay." ".$AOvalue.":00";				//实际签退时间
		$qjE=$toDay." ".$kqDefaultTime[1].":00";	//至默认签退这段时间是否有请假
		$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qjS' and EndDate>='$qjE'",$link_id);
		if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假		
			$qj_Type1=$qj_Row1["Type"];
			$qj_Hours1=ceil(abs(strtotime($qjS)-strtotime($qjE))/3600);	//向上取整
			switch($qj_Type1){
				case "S":$today_SJTime=$today_SJTime+$qj_Hours1;break;		//当天事假类加
				case "B";$today_BJTime=$today_BJTime+$qj_Hours1;break;		//当天病假类加
				case "X":$today_BXTime=$today_BXTime+$qj_Hours1;break;		//当天补休类加
				case "W":$today_WXJTime=$today_WXJTime+$qj_Hours1;break;	//当天无薪请假类加
				}
			}
//结束有请假
//没有请假
		else{
			$AOcolor="class='yellowB'";
			$today_OutEarlys=$today_OutEarlys+1;		//当天早退次数累加
			$Sum_OutEarlys=$Sum_OutEarlys+1;			//当月早退次数累加
			$TimeGap=abs(strtotime($AO)-strtotime($kqDefaultTime[1]))/60;				//早退分钟
			$This_QQTime=intval($TimeGap/60/0.5)*0.5;									//早退小时
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
			}//
//结束没有请假的处理
		}//if($DateType=="G")
	$todayTimeSetp++;
	}
	else{//其它时间段签退检查
		if($kqDefaultTime_length==2){				//只有一个时间段时,正常签退
			$AO=$nowCHECKTIME;						//实际签卡时间			
			$TimeGap=intval(abs(strtotime($AO)-strtotime($kqDefaultTime[1]))/60/30);//签卡时间与设定的签卡时间间隔
			if($TimeGap>0 && $nowZLSign==0){		//直落有效
				$ZLTime=$ZLTime+$TimeGap*0.5;		//计算当天直落加班时间
				$AOvalue=$AO;}						//用于计算的时间：以有效的直落签退时间计算
			else{									//直落无效时
				$AOvalue=$kqDefaultTime[1];}		//用于计算的时间：以默认的签退时间计算
			$todayTimeSetp++;
			}
		else{
			if($nowCHECKTIME<$kqDefaultTime[2]){	//时间段1-正常签退
				$AO=$nowCHECKTIME;					//实际签卡时间
				$AOvalue=$kqDefaultTime[1];			//用于计算的时间
				$TimeGap=intval(abs(strtotime($AO)-strtotime($kqDefaultTime[1]))/60/30);//相差时间间隔，判断直落是否有效
				if($TimeGap>0 && $nowZLSign==0){//直落有效
					$ZLTime=$ZLTime+$TimeGap*0.5;//计算当天直落加班时间
					}
				$todayTimeSetp++;
				}
			else{
				
				$Temp1=(strtotime($nowCHECKTIME)-strtotime($kqDefaultTime[3]))/60;	//早退分钟数
				
				if($nowCHECKTIME<$kqDefaultTime[3]){				//时间段2-签退：早退
					$AO=$nowCHECKTIME;								//用于显示=实际签卡时间
					$AOvalue=$AO;									//用于计算=早退的签卡时间
					if($DateType=="G"){								//如果是工作日，则计算早退或请假工时
						$qjS=$toDay." ".$AOvalue.":00";				//实际签退时间
						$qjE=$toDay." ".$kqDefaultTime[3].":00";	//至默认签退这段时间是否有请假
						$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qjS' and EndDate>='$qjE'",$link_id);
						if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假		
							$qj_Type1=$qj_Row1["Type"];
							$qj_HoursTemp1=ceil(abs(strtotime($qjS)-strtotime($qjE))/3600);	//向上取整
							switch($qj_Type1){
								case "S":$today_SJTime=$today_SJTime+$qj_Hours1;break;		//当天事假类加
								case "B";$today_BJTime=$today_BJTime+$qj_Hours1;break;		//当天病假类加
								case "X":$today_BXTime=$today_BXTime+$qj_Hours1;break;		//当天补休类加
								case "W":$today_WXJTime=$today_WXJTime+$qj_Hours1;break;	//当天无薪请假类加
								}
							}
				//结束有请假
				//没有请假
						else{
							$AOcolor="class='yellowB'";
							$today_OutEarlys=$today_OutEarlys+1;							//当天早退次数累加
							$Sum_OutEarlys=$Sum_OutEarlys+1;								//当月早退次数累加
							//早退的时间向下取整:如15:36 取15:30
							$TimeGap=abs(strtotime($AO)-strtotime($kqDefaultTime[3]))/60;	//早退分钟
							$This_QQTime=intval($TimeGap/60/0.5)*0.5;						//早退小时
							$today_QQTime=$today_QQTime+$This_QQTime;						//当天缺勤工时							
							$Sum_QQTime=$Sum_QQTime+$This_QQTime;							//当月缺勤工时统计
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
							else{
								$today_QQTime=$today_QQTime+0.5;
								$Sum_QQTime=$Sum_QQTime+0.5;
								}
				//迟到、早退罚扣结束
							}//
				//结束没有请假的处理
						}
					$todayTimeSetp++;
					}
				else{								//时段2-签退：正常
					$AO=$nowCHECKTIME;				//用于显示=实际签卡时间
					$AOvalue=$kqDefaultTime[3];		//用于计算=默认时段2的下班签卡时间
					//判断是否有直落时间
					//$AO向上取整，如7:29 读做7:00
					
					$TimeGap=intval(abs(strtotime($AO)-strtotime($kqDefaultTime[3]))/60/30);
					
					if($TimeGap>0 && $nowZLSign==0){
						$ZLTime=$ZLTime+$TimeGap*0.5;
						}
					$todayTimeSetp++;
					}
				}
			}
		}

?>