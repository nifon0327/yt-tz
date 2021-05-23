<?php
	include_once "../../basic/parameter.inc";
	$signTips = "";
	$today = date("Y-m-d");
	//$staffKqId = "10410";
	$getAttendanceListSql = "SELECT A.ConfirmSign, A.month, B.Date
							 FROM $DataIn.kqdata A
							 LEFT JOIN $DataPublic.attendance_confirm_list B ON B.month = A.month
							 WHERE A.Number =  '$staffKqId'
							 And A.ConfirmSign = '1'
							 ORDER BY A.MONTH DESC 
							 LIMIT 1";
	//echo $getAttendanceListSql;					 
	$getAttendanceResult = mysql_query($getAttendanceListSql);
	if(mysql_num_rows($getAttendanceResult) == 1)
	{
		$result = mysql_fetch_assoc($getAttendanceResult);
		$month = $result["month"];
		$listDate = $result["Date"];
		$today = date("Y-m-d");
		
		if($month == "")
		{
			break;
		}
		
		if(strtotime($today) - strtotime($listDate) > 5*24*3600)
		{
			$insertSignSql = "Replace Into $DataPublic.attendance_confirm_sign (Id, Number, SignMonth, Sign, Date, Estate) Values (NULL, '$staffKqId',  '$month', '逾期确认,默认无误', '$today', '1')";
			if(mysql_query($insertSignSql))
			{
				$updateSignConfirmSql = "Update $DataIn.kqdata Set ConfirmSign = '0' Where Number = '$staffKqId' and Month = '$month'";	
				if(mysql_query($updateSignConfirmSql))
				{
					$signTips = " 逾期确认工时,默认无误.";
				}
			}
		}
		else
		{
			//未逾期，也未签
			$signTips = " 请尽快确认工时.";
		}
		
	}
	else
	{
		//check wage sign
		$getWageListSql = "SELECT A.month, C.sign, A.Date
						   FROM $DataPublic.wage_list A
						   LEFT JOIN $DataIn.cwxzsheet B ON B.month = A.month
						   LEFT JOIN $DataPublic.wage_list_sign C ON C.signMonth = A.month AND C.Number = B.Number
						   LEFT JOIN $DataPublic.staffmain D ON D.Number = B.Number
						   WHERE A.Estate =  '0'
						   AND B.Number =  '$staffKqId'
						   AND (B.Estate =  '0' OR B.Estate =  '3')
						   AND D.cSign = A.cSign
						   ORDER BY A.month DESC 
						   Limit 1";
		$wageResult = mysql_query($getWageListSql);					
		$wageListResult = mysql_fetch_assoc($wageResult);
		if(mysql_num_rows($wageResult) != 0 && $wageListResult["sign"] == "")
		{
			$createDate = $wageListResult["Date"];
			$today = date("Y-m-d");
			$overDay = intval((strtotime($today)-strtotime($createDate))/24/3600) - 5;
			if($overDay > 1)
			{
				$pay = 10*intval($overDay);
				$signTips = " 逾期确认薪资 $overDay 天,扣款 $pay 元.";	
			}
			else
			{
				$signTips = " 请尽快确认薪资.";
			}
			
		}
		
		
	}
	
	
?>