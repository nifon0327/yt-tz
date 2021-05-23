<?php 
//电信-EWEN
/*正常签到的情况，签退有5种情况：早上签退；中午签退；下午签退；下常签退；加班签退（包括跨日加班签退）
YYYY-MM-DD 8:00			$dDateTimeIn
YYYY-MM-DD 12:00		$dRestTime1
YYYY-MM-DD 13:00		$dRestTime2
YYYY-MM-DD 17:00		$dDateTimeOut
YYYY-MM-DD 18:00		$dRestTime3
$dRestTime=60;
$dKrSign=0;
*/
$AIcolor="class='yellowB'";
$AOcolor="class='yellowB'";
$InLates=1;
$OutEarlys=1;
if($AO<$dRestTime1){//情况1：早上签退（早退）;按签退取整后的时间计算
	//检查实到时间，签退向下取整
	$ATime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI)))/3600;
	$test="GB-A";
	}
else{
	if($AO<=$dRestTime2){//情况：中午签退（早退）；按12点计算
		$ATime=abs(strtotime($dRestTime1)-strtotime(rounding_in($AI)))/3600;
		$test="GB-B";
		}
	else{
		if($AO<$dDateTimeOut){//情况3：下午签退；按签退取整后的时间计算，需扣60分钟
			$ATime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI))-3600)/3600;
			$test="GB-C";
			}
		else{
			$AOcolor="";			//记录正常，不取色
			$OutEarlys=0;
			$ATime=abs(strtotime($dDateTimeOut)-strtotime(rounding_in($AI))-3600)/3600;			//情况4：正常签退17:00-18:00
			$test="GB-D";
			if(($AO>=$dRestTime3)){	//情况5：18:00之后签退，有加班
				$BTime=abs(strtotime(rounding_out($AO))-strtotime($dRestTime3))/3600;
				$test="GB-E";
				if($KrSign==1){		//情况6：如果跨日签退
					$test="GB-F";
					$YBs=1;
					$AOcolor="class='greenB'";
					}
				}
			}
		}
	}
?>