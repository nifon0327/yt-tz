<?php   
//MC、DP共享代码电信---yang 20120801
$checkLen=strlen($ChooseTime);
//月循环
$NowMonth=$ChooseTime."-12-01";
if($ChooseTime==2010){
	$MonthCount=3;
	}
else{
	if($ChooseTime==date("Y")){
		$NowMonth=date("Y-m-d");
		$MonthCount=date("n");
		}
	else{
		$MonthCount=12;
		}
	}
echo"<table cellspacing='0' border='0' cellpadding='0'><tr><td>&nbsp;</td>";
for($k=0;$k<$MonthCount;$k++){
	$bgColor=$k%2==0?"bgcolor='#B9E3C1'":"bgcolor='#D9F0DD'";
	$CheckTime=date("Y-m",strtotime("$NowMonth -$k month"));//echo $M."<br>";
	echo "<td valign='top' $bgColor>";
	include"desk_cjtj_count_data.php";
	echo"</td>";
	}
echo"<td>&nbsp;</td></tr></table>";
?>