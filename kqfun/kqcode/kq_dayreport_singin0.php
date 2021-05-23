<?php 
//******$DataIn.电信---yang 20120801
$qj_Stime=$thisDay." 08:00:00";
$qj_Etime=$thisDay." 17:00:00";
$qj_Result1 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$Number and  StartDate<='$qj_Stime' and EndDate>='$qj_Etime'",$link_id);
if($qj_Row1=mysql_fetch_array($qj_Result1)){//有请假		
	$qj_Hours=8;			//请假工时
	$qj_HoursSUM=$qj_HoursSUM+$qj_Hours;	//请假工时合计
	}
else{//没有请假
	$KGTime=8;
	$KGTimeSUM=$KGTimeSUM+$KGTime;
	}

?>