<?php 
//电信-EWEN
//**************检查 $CheckTime 是否超过20:00；如果超过，如何重置这个签退记录
$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 20:00:00");
$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
if($EndTimeTemp1>0){//超出时间:
	$EndMinute=$EndTimeTemp1*30;//超出部分的总分钟数
	$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
	$ioTime=date("H:i",strtotime($CheckTime));
	}
//*************

$dSetTime=$dTimeOut;
if($ChickIn!=""){
	//检查时间是否有直落
	if($ChickOut==""){
		$TimeGap=strtotime($CheckTime)-strtotime($dDateTimeOut);
		$TimeTemp1=intval($TimeGap/3600/0.5)*0.5-1;//实际加工工时(减休息时间1小时)
		
		if($TimeTemp1>=0.5){
			if($Kr_bgColor!=""){
//情况1:下班时间延迟90分钟以上,即晚上加班有30分钟以上并且标为跨日记录
				$CHECKSIGN="<div align='center' class='greenB'>下班签退</div>";
				$UnusualInfo="<div align='center' class='greenB'>跨日签退</div>";
				}
			else{
//情况2:下班时间延迟90分钟以上,即晚上加班有30分钟以上者
				$CHECKSIGN="<div align='center' class='greenB'>下班签退</div>";
				$UnusualInfo="<div align='center' class='greenB'>加班".$TimeTemp1."H</div>";
				}
			}
		else{//正常签退或早退
//情况3:早退
			if($ioTime<$dTimeOut && $KrSign==0){			
				$CHECKSIGN="<div align='center' class='greenB'>下班签退</div>";//非工作日早退允许
				if($DateType=="G"){//如果是工作日，检查是否有请假，无请假则为迟到
					$CHECKSIGN="<div align='center' class='yellowB'>下班签退</div>";
					$UnusualInfo="<div align='center' class='yellowB'>早退</div>";
					$UpdateInfo="<div class='yellowB'>不修改或临时排班或有请假</div>";
					}
				}
			else{
//情况4:正常签退
				$CHECKSIGN="<div align='center' class='greenB'>下班签退</div>";
				$UnusualInfo=$KrSign==1?"<div class='greenB'>跨日签退</div>":$UpdateInfo;
				if($TimeTemp1>=0.5 && $pbType==1){//如果是临时班，且相差30分钟以上，则算加班
					$UnusualInfo="<div class='greenB'>有加班</div>";
					}
				}
			}
			$ChickOut=$CheckTime;			
			}
	else{//如果ChickOut已经存在,则为重复记录
//情况5:重复记录	
		$CHECKSIGN="<div align='center' class='redB'>下班签退</div>";
		$UnusualInfo="<div align='center' class='redB'>重复记录</div>";
		$UpdateInfo="<div class='redB'>删除本次或上一个下班签退记录</div>";
		}
	}
else{//if($ChickIn!=""){不成立
//情况6:无上班签到
		$CHECKSIGN="<div align='center' class='redB'>下班签退</div>";
		$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
		$UpdateInfo="<div class='redB'>删除或补上班签到、跨日签退</div>";
		}
?>