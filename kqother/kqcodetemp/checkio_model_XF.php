<?php 
//电信-EWEN
/*早上迟到的情况，签退有5种情况：早上签退；中午签退；下午签退；下常签退；加班签退（包括跨日加班签退）
YYYY-MM-DD 8:00			$dDateTimeIn
YYYY-MM-DD 12:00		$dRestTime1
YYYY-MM-DD 13:00		$dRestTime2
YYYY-MM-DD 17:00		$dDateTimeOut
YYYY-MM-DD 18:00		$dRestTime3
$dRestTime=60;
$dKrSign=0;
$dInLate=0;
$dOutEarly=0;
*/
//签到时间在18点之后，分两种情况：1、未跨日 	2、已跨日

if(($AO>=$dRestTime3)){	//情况5：18:00之后签退，有加班
	if($KrSign==1){		//情况6：如果跨日签退
		$CTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI)))/3600;
		$AOcolor="class='greenB'";
		$test="FA";
		$YBs=1;
		}
	else{
		$CTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI)))/3600;
		$test="E";
		}
	}
?>