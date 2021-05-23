<?php 
//检查当前签卡时间属于哪个签卡段*******电信---yang 20120801
for($k=1;$k<$kqtime_length;$k=$k+2){
	if($CHECKTIME<$kqTime[$k]){//签卡时间与时间段对比
		$timePart=$k;//时间段序号
		$SetTime=$kqTime[$k-1];
		break;
		}
	}
if($Ci==""){
	//检查是否有对应记录:两种情况，1、当日	2、隔日
	$CheckNext= mysql_query("SELECT CHECKTIME,CHECKTYPE FROM $DataIn.checkinout WHERE 1 and NUMBER=$Number and CHECKTIME>'$CHECKTIME' and left(CHECKTIME,10)='$DateTemp' order by CHECKTIME limit 0,1",$link_id);
	if($CheckNextRow = mysql_fetch_array($CheckNext)){//如果后面有签卡记录
		$CHECKSIGN="<div align='center' class='greenB'>加班签到1</div>";
		}
	else{
		//是否有隔日签退记录
		$CheckNext= mysql_query("SELECT CHECKTIME,CHECKTYPE FROM $DataIn.checkinout WHERE 1 and NUMBER=$Number and left(CHECKTIME,10)='$NextDateTemp' and KRSign=1 order by CHECKTIME limit 0,1",$link_id);
		if($CheckNextRow = mysql_fetch_array($CheckNext)){//如果有隔日签退记录
			$CHECKSIGN="<div align='center' class='greenB'>加班签到2</div>";
			}
		else{//如果无隔日签退
			$CHECKSIGN="<div align='center' class='redB'>加班签到3</div>";
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>删除或补加班签退</div>";
			}
		}
		$Ci=$CHECKTIME;
	}
else{
	if($Co==""){
		$CHECKSIGN="<div align='center' class='redB'>加班签到4</div>";
		$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
		$RevisionPrompt="<div class='redB'>改加班签退或删除</div>";
		}
	else{
		$CHECKSIGN="<div align='center' class='redB'>加班签到4</div>";
		$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
		$RevisionPrompt="<div class='redB'>删除</div>";
		}
	}

?>