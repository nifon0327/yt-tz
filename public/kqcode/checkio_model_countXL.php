<?php 
/*
YYYY-MM-DD 8:00			$dDateTimeIn
YYYY-MM-DD 17:00		$dDateTimeOut
休息时间				$dRestTime;
跨日标志				$dKrSign=0;
许可迟到				$dInLate=0;
许可早退				$dOutEarly=0;
*/
//休息日临时班
$DateTypeColor="class='yellowB'";
$ddInfo="(临)";
if($AI!="" || $AO!=""){//如果有签到签退，计算加班工时
	$jbTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI))-($dRestTime*60))/3600;
	if($KrSign==1){//跨日签退
		$AOcolor="class='greenB'";
		$YBs=1;
		}
	}
else{//任何一个为空，皆为无效
	$jbTime=0;

	//$aiTime="";
	//$aoTime="";
	}
//假日类型
switch($DateType){
	case "X":
		$XJTime=$jbTime;
	break;
	case "F":
	if($jbTimes==3){
		$FJTime=$jbTime;}
	else{
		$XJTime=$jbTime;
		}
	break;
	case "W":
		$GJTime=$jbTime;
	break;
	case "Y":
       if($jbTimes==2){
			$XJTime=$jbTime;
			}
		else{		
		$GJTime=$jbTime;	
		}
	break;
	}
?>