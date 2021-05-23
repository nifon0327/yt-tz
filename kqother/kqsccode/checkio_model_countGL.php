<?php 
//电信-EWEN
/*
YYYY-MM-DD 8:00			$dDateTimeIn
YYYY-MM-DD 17:00		$dDateTimeOut
休息时间				$dRestTime;
跨日标志				$dKrSign=0;
许可迟到				$dInLate=0;
许可早退				$dOutEarly=0;
*/
if($AI=="" || $AO==""){//任何一个为空，皆为缺勤
	$WorkTime=0;
	}
else{
	//工作工时
	$WorkTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI))-($dRestTime*60))/3600;
	}
?>