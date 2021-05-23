<?php 
//电信-EWEN
$today_WorkTime=8;//工作小时
$today_WXJTime=8;//无新花样工时为8小时,如有上班,则相应减去,多出来的仍按工作日加班计算
$today_sbTime=0;//上班时间
if($AI!="" && $AO!=""){
	$today_sbTime=intval(abs(strtotime($AIvalue)-strtotime($AOvalue))/3600/0.5)*0.5;
	}
if($BI!="" && $BO!=""){
	$today_sbTime=$today_sbTime+intval(abs(strtotime($BIvalue)-strtotime($BOvalue))/3600/0.5)*0.5;
	}
if($CI!="" && $CO!=""){//无薪假日加点时间
	$today_sbTime=$today_sbTime+intval(abs(strtotime($CIvalue)-strtotime($COvalue))/3600/0.5)*0.5;
	}
if($today_sbTime<=8){
	$today_WXJTime=8-$today_sbTime;//8-上班时间为无薪工时
	$today_WorkTime=$today_sbTime;//工作小时
	}
else{
	$today_WXJTime=0;			
	$today_GJTime=$today_sbTime-8;			//当天加点工时
	$Sum_GJTime=$Sum_GJTime+$today_GJTime;	//当月总加点工时
	}
$today_GJTime=$today_GJTime+$ZLTime;	//当天加点小时=多上的工时+中途直落的工时
$Sum_GJTime=$Sum_GJTime+$today_GJTime;	//当月加点工时
$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;//当天实到工时
$Sum_GTime=$Sum_GTime+$today_GTime;				//当月应到总工时
?>