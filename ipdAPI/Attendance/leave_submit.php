<?php 
	
	$ipadTag = "yes";
	include "../../model/kq_YearHolday.php";
	include "../../basic/parameter.inc";
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
	
	$chkRepeatLeaveSql = "Select * From $DataPublic.kqqjsheet Where StartDate = '$startTime' And EndDate = '$endTime' And Number = '$staffNumber' And Type = '$qjType'";
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
		
		$insertQjSql = sprintf("INSERT INTO $DataPublic.kqqjsheet SELECT NULL,Number,'$startTime','$endTime','$reason','0','$qjType','0','$Estate','$date','0','$staffNumber','$DateTime' FROM $DataPublic.staffmain WHERE Number='$staffNumber' AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Id)");
		
		//echo $insertQjSql;
		
		if(mysql_query($insertQjSql))
		{
			$info = "$name $startTime 到 $endTime 请假申请成功";
		}
		else
		{
			$err = "yes";
			$info = "申请失败";
		}
	}
	
	echo json_encode(array($err, $info));
	
?>Time','0' FROM $DataPublic.staffmain WHERE Number='$staffNumber' AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Id)");
		}
		//echo $insertQjSql;
		
		if(mysql_query($insertQjSql))
		{
			$info = "$name $startTime 到 $endTime 请假申请成功";
		}
		else
		{
			$err = "yes";
			$info = "申请失败";
		}
	}
	
	echo json_encode(array($err, $info));
	
?>