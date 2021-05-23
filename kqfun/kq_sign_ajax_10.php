<?php 
//电信-ZX  2012-08-01
$inRecode="INSERT INTO $DataIn.checkinout (Id,BranchId,JobId,Number,CheckTime,CheckType,dFrom,Estate,Locks,ZlSign,KrSign,Operator) 
VALUES (NULL,'$BranchId','$JobId','$Number','$CheckTime','$CheckType','0','1','1','0','$KrSign','0')";
$inAction=@mysql_query($inRecode);
$newID = mysql_insert_id();
if ($inAction){
	$ReInfo="$Name<br>签卡成功";		//返回提示信息和员工姓名
	$OperationResult="Y";
	$CheckTime=date("H:i",strtotime($CheckTime));
	$CheckType=$CheckType=="I"?"签到":"签退";
	$Record=$Name."~".$CheckTime.$CheckType."~".$newID."_".$Number;
	}
else{
	$ReInfo="$Name<br>签卡失败";				//返回提示信息和员工姓名
	}
?>