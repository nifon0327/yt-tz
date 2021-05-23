<?php 
//电信-EWEN
	$RecordsTemp=$rowRecords%2;//跨日记录
	if($RecordsTemp==1){//有跨日记录	
		$today_YBs=1;
		//下班跨日还是加班跨日
		//读取跨日记录,下一班第一个记录并且为不是I的记录
		$nextDay=date("Y-m-d",strtotime("$StartDateTemp"));
		$nextStartDateTemp=date("Y-m-d H:i:s",strtotime("$nextDay+1 days"));//当天有效签卡记录起始时间
		$nextEndDateTemp=date("Y-m-d H:i:s",strtotime("$nextDay+2 days"));//当天有效记录结束时间
		$next_Result = mysql_query("SELECT * FROM $DataIn.checkinout WHERE NUMBER=$DefaultNumber and CHECKTIME>='$nextStartDateTemp' and CHECKTIME<'$nextEndDateTemp'  order by CHECKTIME ASC",$link_id);//and ESTATE=0
		if($next_Row=mysql_fetch_array($next_Result)){
			$nextCHICKTIME=$next_Row["CHECKTIME"];
			if($CIvalue!=""){//加班跨日
				$CO=date("H:i",strtotime($nextCHICKTIME));
				$COcolor="class='greenB'";
				$COvalue=$nextCHICKTIME;
				$CIvalue=$nextDay." ".$CIvalue;
				//取整处理
				$minuteTemp=date("i",strtotime("$CIvalue"))*1;
				if($minuteTemp!=0 && $minuteTemp!=30){
					if($minuteTemp<30){
						$minuteTemp=(30-$minuteTemp);}
					else{
						$minuteTemp=(60-$minuteTemp);}
					}
				else{
					$minuteTemp=0;}
				//取整后的时间
				
				$CIvalue=date("Y-m-d H:i:s",strtotime("$CIvalue")+$minuteTemp*60);
				}
			else{
				if($BIvalue!=""){//第2时间段跨日
					//判断跨日几小时，1小时以内扣暂不扣休息时间；1小时以上扣一个小时的休息时间
					$BO=date("H:i",strtotime($nextCHICKTIME));
					$BOcolor="class='greenB'";
					$BOvalue=$nextCHICKTIME;
					$BIvalue=$nextDay." ".$BIvalue.":00";
					//取整处理
					$minuteTemp=date("i",strtotime("$BIvalue"));
					if($minuteTemp!=0 && $minuteTemp!=30){
						if($minuteTemp<30){
							$minuteTemp=(30-$minuteTemp);}
						else{
							$minuteTemp=(60-$minuteTemp);}
						}
					else{
						$minuteTemp=0;}
					//取整后的时间
					$BIvalue=date("Y-m-d H:i:s",strtotime("$BIvalue")+$minuteTemp*60);
					}
				else{//第1时间段跨日
					$AO=date("H:i",strtotime($nextCHICKTIME));
					$AOcolor="class='greenB'";
					$AOvalue=$nextCHICKTIME;
					$AIvalue=$nextDay." ".$AIvalue.":00";
					//取整处理
					$minuteTemp=date("i",strtotime("$AO"));
					if($minuteTemp<30){
						$AOvalue=date("Y-m-d H:00:00",strtotime("$AOvalue"));
						}
					else{
						$AOvalue=date("Y-m-d H:30:00",strtotime("$AOvalue"));}
					}
				}
//计算跨日班的休息时间
			$jg_Hours=date("G",strtotime($nextCHICKTIME))*1;
			if($today_YBs==1){//跨日班休息时间
				$theLast=$kqDefaultTime_length-1;
				if($RestTimeRecord[$theLast]!=0){//如果有休息时间
					$MidwayRest=$RestTimeRecord[$theLast]/60;
					}
				else{
					//$MidwayRest=0;//如果没有，则看跨日时间多少分：不扣，扣0.5或1小时
					//下班签卡时间与 00:00:00相差小时数,如果在1小时内不扣休息时间，否则仍以1小时的休息时间计算
					if($jg_Hours>=2){
						$MidwayRest=1;}
					else{
						$MidwayRest=$jg_Hours<1?0:0.5;}
					}
				if($MidwayRest>0){//如果需要扣休息时间的则补夜宵
					$today_YBs=1;
					$Sum_YBs=$Sum_YBs+$today_YBs;
					}
				else{
					$today_YBs=0;
					}
				}
			}
		}
?>