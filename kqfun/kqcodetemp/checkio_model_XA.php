<?php 
//分开已更新
/*正常签到的情况，签退有5种情况：早上签退；中午签退；下午签退；下常签退；加班签退（包括跨日加班签退）
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
$AOcolor="class='yellowB'";
if($AO<$dRestTime1){//情况1：早上签退（早退）;按签退取整后的时间计算
	//检查实到时间，签退向下取整
	$CTime=abs(strtotime(rounding_out($AO))-strtotime($dDateTimeIn))/3600;
	$test="XA-A";
	}
else{
	if($AO<=$dRestTime2){//情况：中午签退（早退）；按12点计算
		$CTime=abs(strtotime($dRestTime1)-strtotime($dDateTimeIn))/3600;
		$test="XA-B";
		}
	else{
		if($AO<$dDateTimeOut){//情况3：下午签退；按签退取整后的时间计算，需扣60分钟
			$CTime=abs(strtotime(rounding_out($AO))-strtotime($dDateTimeIn)-3600)/3600;
			$test="XA-C";
			}
		else{
			$AOcolor="";			//记录正常，不取色
			$CTime=8;			//情况4：正常签退17:00-18:00
			$test="XA-D";
			if(($AO>=$dRestTime3)){	//情况5：18:00之后签退，有加班
				$CTime=$CTime+abs(strtotime(rounding_out($AO))-strtotime($dRestTime3))/3600;
				$test="XA-E";
				if($KrSign==1){		//情况6：如果跨日签退
					$test="XA-F";
					$YBs=1;
					}
				}
			}
		}
	}
?>