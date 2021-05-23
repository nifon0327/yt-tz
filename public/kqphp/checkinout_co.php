<?php 
//电信-EWEN
//检查当前签卡时间属于哪个签卡段
$timePart=$kqtime_length;//时间段序号
$SetTime=$kqTime[$kqtime_length-1];
if($CHECKTIME<$kqTime[0]){//如果签卡时间少于第一个签卡时间，则应为隔日签退记录
	$CHECKSIGN="<div align='center' class='redB'>加班签退1</div>";
	$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
	$RevisionPrompt="<div class='redB'>删除或改跨日签退</div>";
	$Co=$CHECKTIME;
	}
else{
	if($Co==""){
		if($Ci==""){
			$CHECKSIGN="<div align='center' class='redB'>加班签退2</div>";
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>删除或补加班签到</div>";
			$Co=$CHECKTIME;
			}
		else{
			//检查是否跨日签退记录/和直落
			$CHECKSIGN="<div align='center' class='greenB'>加班签退3</div>";
			if($Class!=""){
				//检查
				$UnusualRecord="<div align='center' class='greenB'>跨日记录</div>";
				}
				$Co=$CHECKTIME;
			}
		}
	else{
		$CHECKSIGN="<div align='center' class='redB'>加班签退4</div>";
		$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
		$RevisionPrompt="<div class='redB'>删除</div>";
		}
	}
?>