<?php 
//电信-EWEN
$lsbResult = mysql_query("SELECT InTime,OutTime,InLate,OutEarly,TimeType,RestTime FROM $DataIn.kqlspbb WHERE Number=$Number and left(InTime,10)='$ToDay' order by Id",$link_id);
if($lsbResult  && $lsbRow = mysql_fetch_array($lsbResult)){
	do{
		$pbType=1;
		$dDateTimeIn=$lsbRow["InTime"];
		$dTimeIn=date("H:i",strtotime($dDateTimeIn));					
		$dDateTimeOut=$lsbRow["OutTime"];
		$dTimeOut=date("H:i",strtotime($dDateTimeOut));
		$dRestTime=$lsbRow["RestTime"];
		$dInLate=$lsbRow["InLate"];
		$dOutEarly=$lsbRow["OutEarly"];
		$TimeType=$lsbRow["TimeType"];
		//$dKrSign=$TimeType==0?1:0;
		//判断是否跨日班次，如果上下班时间不是同一天为跨日班
		if(substr($lsbRow["InTime"],10)==substr($lsbRow["OutTime"],10)){
			$dKrSign=0;
			}
		else{
			$dKrSign=1;
			}
		}while($lsbRow = mysql_fetch_array($lsbResult));
	}
else{
	$pbType=0;
	$dDateTimeIn=$ToDay." 08:00:00";
	$dTimeIn=date("H:i",strtotime($dDateTimeIn));
	$dDateTimeOut=$ToDay." 17:00:00";
	$dTimeOut=date("H:i",strtotime($dDateTimeOut));
	$dRestTime1=$ToDay." 12:00:00";
	$dRestTime2=$ToDay." 13:00:00";
	$dRestTime3=$ToDay." 18:00:00";
	$dRestTime=60;
	$dInLate=0;
	$dOutEarly=0;
	$dKrSign=0;
	}
//AAAAAAAAAAAAAAAAAAAAAAAAA
?>