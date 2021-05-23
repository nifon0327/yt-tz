<?php 
//电信-EWEN
			if($AI!="" && $AO!=""){
				$today_XJTime=intval(abs(strtotime($AIvalue)-strtotime($AOvalue))/3600/0.5)*0.5;
				}
			if($BI!="" && $BO!=""){
				$today_XJTime=$today_XJTime+intval(abs(strtotime($BIvalue)-strtotime($BOvalue))/3600/0.5)*0.5;
				}
			if($CI!="" && $CO!=""){
				$today_XJTime=$today_XJTime+intval(abs(strtotime($CIvalue)-strtotime($COvalue))/3600/0.5)*0.5;
				}
			if($AI!="" && $AO=="" && $BI=="" && $BO!=""){
				$today_XJTime=$today_XJTime+intval(abs(strtotime($AIvalue)-strtotime($BOvalue))/3600/0.5)*0.5;
				}			
			$today_XJTime=$today_XJTime+$ZLTime-$MidwayRest;
			$today_GTime=0;//当天应到工时
			$today_WorkTime=0;//当天实到工时
			$Sum_XJTime=$Sum_XJTime+$today_XJTime;//休息日加班工时合计

?>