<?php

function checkCrash($number, $StartDate, $EndDate,  $targetTable,$link_id){
	$checkCrashSql = "SELECT COUNT(*) as count From $targetTable.kqqjsheet WHERE Number=$number and (StartDate between '$StartDate' and '$EndDate') and (EndDate between '$StartDate' and '$EndDate')";
	$checkCrashResult = mysql_query($checkCrashSql);
	$checkCRash = mysql_fetch_assoc($checkCrashResult);
	$result = true;
	if($checkCrash['count'] > 0){
		$result = false;
	}
	return $result;
}

?>