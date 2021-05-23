<?php 
	include "../../basic/parameter.inc";
	$cSign = $_Post["cSign"];
	$date = date("Y-m-d");
	$overTimeStr = sprintf("Select Content,Date From $DataPublic.msg2_overtime Where (cSign='$cSign' or cSign ='0') and Date='%s' Order By Date Desc Limit 1",$date);
	$overTimeResult = mysql_query($overTimeStr);
	$msg = "no";
	while($overTimeMsg = mysql_fetch_assoc($overTimeResult))
	{
		$date = $overTimeMsg["Date"];
		$content = $overTimeMsg["Content"];
		$msg = "($date)$content";
	}
	$overTimeInfo = array("overTime"=>$msg);
	echo json_encode($overTimeInfo);

?>