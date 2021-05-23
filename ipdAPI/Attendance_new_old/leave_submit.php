<?php 
	
	$ipadTag = "yes";
	include "../../model/kq_YearHolday.php";
	include "../../basic/parameter.inc";
	include "../../model/subprogram/qjCalculate.php";
	include("getStaffNumber.php");
	
	$staffNumber = $_POST["number"];
	if(strlen($staffNumber) != 5)
	{
		$staffNumber = getStaffNumber($staffNumber, $DataPublic);
	}	
	$DateTime=date("Y-m-d H:i:s");
	$startTime = $_POST["startTime"];
	$startTime = $startTime.":00";
	$endTime = $_POST["endTime"];
	$endTime = $endTime.":00";
	
	$reason = $_POST["reason"];
	$qjType = $_POST["type"];

	// $staffNumber = '11008';
	// $startTime = '2015-06-04 08:00';
	// $endTime = '2015-08-04 17:00';
	// $reason = '跨月请假测试';
	// $qjType = '1';

	mysql_query('START TRANSACTION');
	$inAction = true;
	
	$err = "no";
	$info = "";
	$MonthTemp=substr($startTime,0,7);
	$date = date("Y-m-d");
	
	$chkNjSqlStr = "Select M.Name, M.ComeIn, M.KqSign From $DataPublic.staffmain M Where  M.Estate = '1' And M.Number=$staffNumber Order by M.Number";
	
	$chkNjResult = mysql_query($chkNjSqlStr);
	$chkNjRows = mysql_fetch_assoc($chkNjResult);
	
	$name = $chkNjRows["Name"];
	$comeIn = $chkNjRows["ComeIn"];
	
	$submitHours = GetBetweenDateDays($staffNumber,$startTime,$endTime, '0',$DataIn,$DataPublic,$link_id);
	
	if($qjType == 4)
	{
		$usedAnnual = HaveYearHolDayDays($staffNumber,$startTime,$endTime,$DataIn,$DataPublic,$link_id);
		$totleAnnual = GetYearHolDayDays($staffNumber,$startTime,$endTime,$DataIn,$DataPublic,$link_id);
		
		if($usedAnnual + $submitHours > $totleAnnual*8 )
		{
			$err = "yes";
			$info = sprintf("申请失败！申请天数为%s,还有年假天数为%s,",$submitHours/8,($totleAnnual*8-$usedAnnual)/8);
			
			echo json_encode(array($err, $info));
			exit();
		}
		
	}
	
	$chkRepeatLeaveSql = "Select * From $DataPublic.kqqjsheet Where ((StartDate Between '$startTime' And '$endTime') OR (EndDate Between '$startTime' And '$endTime')) And Number = '$staffNumber'";
	$chkRepeatLeaveResult = mysql_query($chkRepeatLeaveSql);
	if(mysql_num_rows($chkRepeatLeaveResult) > 0)
	{
		$err = "yes";
		$info = "重复申请";
		echo json_encode(array($err, $info));
		exit();
	}
	
	if($err == "no")
	{
		$Estate = "1";
		//$MonthTemp=substr($StartDate,0,7);
		if($qjType == 4)
		{
			$Estate = IsAuditHolDayDays($staffNumber,$startTime,$endTime,$DataIn,$DataPublic,$link_id);
		}
		$qjArray = qjCalculate($startTime, $endTime);
		for($i=0; $i<count($qjArray); $i++){
			$tempQj = $qjArray[$i];
			$start = $tempQj[0];
			$end = $tempQj[1];
			$insertQjSql = sprintf("INSERT INTO $DataPublic.kqqjsheet SELECT NULL,Number,'$start','$end','$reason','0','$qjType','0','$Estate','$date','0','$staffNumber','$DateTime','0','0','$staffNumber',NOW(),'$staffNumber',NOW() FROM $DataPublic.staffmain WHERE Number='$staffNumber' AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Id)");
			echo $insertQjSql.'<br>';
			if(!mysql_query($insertQjSql)){
				$inAction = false;
				mysql_query('ROLLBACK ');
				break;
			}
		}
		
		//echo $insertQjSql;
		
		if($inAction){
			mysql_query('COMMIT');
			$info = "$name $startTime 到 $endTime 请假申请成功";
		}else{
			$err = "yes";
			$info = "申请失败";
		}
	}
	
	//echo $insertQjSql;
	echo json_encode(array($err, $info));
	
?>