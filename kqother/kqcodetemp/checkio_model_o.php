<?php 
//电信-EWEN
if($ChickIn!=""){
	//检查时间是否有直落
	if($ChickOut==""){
		$CHECKSIGN="<div align='center' class='greenB'>下班签退1</div>";
		$UnusualInfo="&nbsp;";
		if($KrSign==1){
			$CHECKSIGN="<div align='center' class='greenB'>下班签退2</div>";
			$UnusualInfo="<div align='center' class='greenB'>跨日签退</div>";
			}
		$ChickOut=$CheckTime;			
		}
	else{//如果ChickOut已经存在,则为重复记录
		$CHECKSIGN="<div align='center' class='redB'>下班签退3</div>";
		$UnusualInfo="<div align='center' class='redB'>重复记录</div>";
		$UpdateInfo="<div class='redB'>删除本次或上一个下班签退记录</div>";
		}
	}
else{
	$CHECKSIGN="<div align='center' class='redB'>下班签退4</div>";
	$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
	$UpdateInfo="<div class='redB'>删除或补上班签到、跨日签退</div>";
	}
?>