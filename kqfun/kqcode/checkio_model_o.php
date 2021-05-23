<?php 
//$CheckTime	原记录签卡时间 YYYY-MM-DD H:I:S
//$dDateTimeOut 设定的签退时间 YYYY-MM-DD H:I:S
$dSetTime=$dTimeOut;
if($ChickIn!=""){
	//检查时间是否有直落
	if($ChickOut==""){
		$TimeGap=strtotime($CheckTime)-strtotime($dDateTimeOut);
		$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;
		if($TimeTemp1>=1.5){
			if($Kr_bgColor!=""){
//情况1:下班时间延迟90分钟以上,即晚上加班有30分钟以上并且标为跨日记录
				$CHECKSIGN="<div align='center' class='greenB'>下班签退1</div>";
				$UnusualInfo="<div align='center' class='greenB'>跨日签退</div>";
				}
			else{
//情况2:下班时间延迟90分钟以上,即晚上加班有30分钟以上者
				$CHECKSIGN="<div align='center' class='greenB'>下班签退2</div>";
				$UnusualInfo="<div align='center' class='greenB'>有加班</div>";
				}
			}
		else{//正常签退或早退
//情况3:早退
			if($ioTime<$dTimeOut && $KrSign==0){			
				$CHECKSIGN="<div align='center' class='greenB'>下班签退3</div>";//非工作日早退允许
				if($DateType=="G"){//如果是工作日，检查是否有请假，无请假则为迟到
					$CHECKSIGN="<div align='center' class='yellowB'>下班签退3</div>";
					$UnusualInfo="<div align='center' class='yellowB'>早退</div>";
					$UpdateInfo="<div class='yellowB'>不修改或临时排班或有请假</div>";
					}
				}
			else{
//情况4:正常签退
				$CHECKSIGN="<div align='center' class='greenB'>下班签退4</div>";
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
		$CHECKSIGN="<div align='center' class='redB'>下班签退5</div>";
		$UnusualInfo="<div align='center' class='redB'>重复记录</div>";
		$UpdateInfo="<div class='redB'>删除本次或上一个下班签退记录</div>";
		}
	}
else{//if($ChickIn!=""){不成立
//情况6:无上班签到
		$CHECKSIGN="<div align='center' class='redB'>下班签退6</div>";
		$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
		$UpdateInfo="<div class='redB'>删除或补上班签到、跨日签退</div>";
		}
?>