<?php 
//参照默认值，对比，如果签卡时间<默认值，以默认值计算
$CI=$nowCHECKTIME;									//用于显示=实际签卡时间
$DefaultITime=$kqDefaultTime_length-2;
if($nowCHECKTIME<=$kqDefaultTime[$DefaultITime]){	//如果签卡时间在默认签卡时间之前，即正常加班签到
	$CIvalue=$kqDefaultTime[$DefaultITime];			//用于计算=加班默认签卡时间
	}
else{												//否则，要做取整处理
	$CIvalue=$CI;									//用于计算的时间=实际签卡时间，但要做取整
	$CIvalue=$CIvalue.":00";
	//取整处理
	$minuteTemp=date("i",strtotime("$CIvalue"))*1;
	if($minuteTemp!=0 && $minuteTemp!=30){
		if($minuteTemp<30){
			$minuteTemp=(30-$minuteTemp);}
		else{
			$minuteTemp=(60-$minuteTemp);}
		}
	else{
		$minuteTemp=0;}
	//取整后的时间
	$CIvalue=date("H:i:s ",strtotime("$CIvalue")+$minuteTemp*60);
	}
$todayTimeSetp++;
?>