<?php 

	


	$inRecode="INSERT INTO $DataIn.checkinout (Id,Number,CheckTime,CheckType,dFrom,Estate,Locks,ZlSign,KrSign,Operator,BranchId,JobId) 
VALUES (NULL,'$Number','$CheckTime','$CheckType','$dForm','1','1','0','$KrSign','0','$brandId','$jobId')";
	
	//echo $inRecode;
	$inAction=@mysql_query($inRecode);
	if ($inAction){
		$resultInfo = "$Name 签卡成功";		//返回提示信息和员工姓名
		$OperationResult="Y";
		$datee = substr($CheckTime, 0, 10);
		//$CheckTime=date("H:i",strtotime($CheckTime));
		//$CheckType=$CheckType=="I"?"签到":"签退";
		//$resultInfo=$Name."  ".$CheckTime.$CheckType;
		//$resultInfo = $Name."+".$CheckTime."+".$CheckType."+".$KqSign."+".$cSign."+".$datee."+".$staffKqId;
		
		$errorInfo = ($targetFloor != $AttendanceFloor)?"Y":"N";
		
		$resultInfo = $Name."+".$CheckTime."+".$CheckType."+".$dForm."+".$errorInfo;
	}
	else{
		$resultInfo .= ":签卡失败";				//返回提示信息和员工姓名
		$errorState = "yes";
	}

?>