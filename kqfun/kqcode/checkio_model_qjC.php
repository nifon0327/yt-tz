<?php 
//二合一已更新
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

$StartDate	请假起始时间
$EndDate	请假结束时间
请假起始时间在12-13
*/
if($EndDate<$dDateTimeOut){//情况3：请假结束在17点前
	$qjHours=abs(strtotime($EndDate)-strtotime($dRestTime2))/3600;
	}
else{//情况4：请假结束在17点后
	$qjHours=abs(strtotime($dDateTimeOut)-strtotime($dRestTime2))/3600;
	}
?>