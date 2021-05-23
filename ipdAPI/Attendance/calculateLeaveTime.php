<?php
	
	$ipadTag = "yes";
	include "../../model/kq_YearHolday.php";
	include "../../basic/parameter.inc";
	
	$number = $_POST["Number"];
	//$number = "11008";
	
	$startDate = $_POST["StartDate"];
	//$startDate = "2013-12-27 15:00";
	
	$endDate = $_POST["EndDate"];
	//$endDate = "2014-01-01 17:00";
	
	$leaveTime =  GetBetweenDateDays($number,$startDate,$endDate,"0",$DataIn,$DataPublic,$link_id);
	echo $leaveTime."h";
	
?>