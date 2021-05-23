<?php 
	
	include "../../basic/parameter.inc";
	include("getStaffNumber.php");
	//
	//$sMonth = date("Y-m");
	$number = $_POST["number"];
	//$number = '08127959';
	
	if(strlen($number) != 5)
	{
		$number = getStaffNumber($number, $DataPublic);
	}
	
	//考勤部分，先确认考勤有没确认
	//$noSignMonth = "2013-05";
	//$signType = "attendance";
	$attendanceSql = "Select * From $DataIn.kqdata K Where Number = '$number' And ConfirmSign = '1'";
	
	$attendanceResult = mysql_query($attendanceSql);
	if(mysql_num_rows($attendanceResult) > 0)
	{
		$attendanceRows = mysql_fetch_assoc($attendanceResult);
		$noSignMonth = $attendanceRows["Month"];
		$signType = "attendance";
	}
	else
	{
	
		$monthGetSql = "Select distinct Month 
						From $DataPublic.wage_list
						Where cSign = '7' 
						and Estate = '0' 
						order by month desc Limit 1";
		$monthResult = mysql_query($monthGetSql);
		$monthRow = mysql_fetch_assoc($monthResult);
		$sMonth = $monthRow["Month"];
		
		
		if($sMonth != "")
		{
		//薪资确认部分		   
			$wageSql = "SELECT S.Month,S.Amount 
				   	   	FROM  $DataIn.cwxzsheet S 
				   	   	WHERE S.Number='$number' 
				   	   	AND S.Estate in (0) 
				   	   	AND S.Month>='$sMonth' 
				   	   	order by S.Month DESC Limit 1";
		    //echo $wageSql;
		    $wageResult = mysql_query($wageSql);
			if(mysql_num_rows($wageResult) > 0)
			{
				$wageRow = mysql_fetch_assoc($wageResult);
				$wageMonth = $wageRow["Month"];
				$checkSign=mysql_query("SELECT Id,sign FROM $DataPublic.wage_list_sign WHERE Number='$number' AND SignMonth='$wageMonth' LIMIT 1",$link_id);
			
				$checkSignResult = mysql_fetch_assoc($checkSign);
				$sign = $checkSignResult["sign"];
			
				if(mysql_num_rows($checkSign) == 0 || $sign=="")
				{
					$noSignMonth = $sMonth;
					$signType = "wage";
				}
			}
		}
		else
		{
			$signType = "wage";
		}	
	}
	
	$comeIn = "";
	$inquiryProfileResult = mysql_query("Select Name, kqSign, ComeIn 
						  				 From $DataPublic.staffmain 
						  				 Where Number = '$number'");
	$inquiryProfileRow = mysql_fetch_assoc($inquiryProfileResult);
	$name = $inquiryProfileRow["Name"];
	$kqSign = $inquiryProfileRow["kqSign"];
	$comeIn = $inquiryProfileRow["ComeIn"];
	//$noSignMonth = "";
	echo json_encode(array("$name", "$kqSign", "$comeIn", "$noSignMonth", "$signType"));
	
?>