<?php 
			if($AI!="" && $AO!=""){
				$today_FJTime=intval(abs(strtotime($AOvalue)-strtotime($AIvalue))/3600/0.5)*0.5;
				}
			if($BI!="" && $BO!=""){
				$today_FJTime=$today_FJTime+intval(abs(strtotime($BIvalue)-strtotime($BOvalue))/3600/0.5)*0.5;
				}
			if($CI!="" && $CO!=""){
				$today_FJTime=$today_FJTime+intval(abs(strtotime($CIvalue)-strtotime($COvalue))/3600/0.5)*0.5;
				}
			if($today_YBs==1){//跨日班休息时间
				$theLast=$kqDefaultTime_length-1;
				if($RestTimeRecord[$theLast]!=0){//如果有休息时间
					$MidwayRest=$RestTimeRecord[$theLast]/60;
					}
				else{
					$MidwayRest=0;//如果没有，则看跨日时间多少分：不扣，扣0.5或1小时
					}
				}
			$today_FJTime=$today_FJTime+$ZLTime-$MidwayRest;
			$today_GTime=8;//当天应到工时
			$today_WorkTime=8;//当天实到工时
			$Sum_GTime=$Sum_GTime+$today_GTime;//应到工时合计
			$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;//实到工时合计
			$Sum_FJtime=$Sum_FJtime+$today_FJTime;//法定假日加班工时合计
?>