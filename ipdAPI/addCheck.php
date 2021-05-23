<?php
	
	
	include_once("../model/modelhead.php");
	
// 		$number= '11710';
// 		$CheckDate = "2014-05-30";
// 		$CheckIn = $CheckDate." 07:5".mt_rand(0,9);
// 		$inRecode="INSERT INTO $DataIn.checkinout (Id,Number,CheckTime,CheckType,dFrom,Estate,Locks,ZlSign,KrSign,Operator,BranchId,JobId) 
// VALUES (NULL,'$number','$CheckIn','I','1','1','1','0','0','0','1','12')";
// 		mysql_query($inRecode);
	
// 		$CheckOut = $CheckDate." 20:0".mt_rand(0,9);
// 		$outRecode="INSERT INTO $DataIn.checkinout (Id,Number,CheckTime,CheckType,dFrom,Estate,Locks,ZlSign,KrSign,Operator,BranchId,JobId) 
// VALUES (NULL,'$number','$CheckOut','O','1','1','1','0','0','0','1','1')";
// 		mysql_query($outRecode);
	

	$startMonth = date('Y-m', strtotime($startTime));
	$endMonth = date('Y-m', strtotime($endTime));
	$tempMonth = $startMonth;
	$tempTime = $startTime;
	while($tempMonth <= $endMonth){
		if($tempMonth == $endMonth){
			//echo $tempTime.'   '.$endTime.'<br>';
		}else if($tempMonth < $endMonth){
			$lastDay = date("Y-m-t",strtotime($tempMonth.'-01'));
			//echo $tempTime.'   '.$lastDay.' 17:00:00'.'<br>';
		}

		$tempMonth = date("Y-m",strtotime("+1months",strtotime($tempMonth)));
		$tempTime = $tempMonth.'-01 08:00:00';
	}
	
	
?>