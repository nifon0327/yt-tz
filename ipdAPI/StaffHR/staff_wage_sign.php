<?php 
	
	include "../../basic/parameter.inc";
	include "../Attendance/getStaffNumber.php";
	
	$type = $_POST["type"];
	//$type = 1;
	$targetTable = ($type == "1")?"wage_list_sign":"attendance_confirm_sign";
	
	$number = $_POST["number"];
	if(strlen($number) != 5)
	{
		$number = getStaffNumber($number, $DataPublic);
	}
	
	$strokes = $_POST["strokes"];
	$month = $_POST["month"];
	$date = date("Y-m-d");
	
	$state = "yes";
	$info = "签名保存成功!";
	
	
	
	$insertSignSql = "Replace Into $DataPublic.$targetTable (Id, Number, SignMonth, Sign, Date, Estate) Values (NULL, '$number',  '$month', '$strokes', '$date', '1')";
	if(!mysql_query($insertSignSql))
	{
		$state = "no";
		$info = "签名保存失败!";
	}
	else
	{
		$state = "yes";
		if($type == "0")
		{
			$updateSignConfirmSql = "Update $DataIn.kqdata Set ConfirmSign = '0' Where Number = '$number' and Month = '$month'";
			if(!mysql_query($updateSignConfirmSql))
			{
				$info .= "\n标记状态变更失败!";
			}
			
		}
	}
	
	echo json_encode(array($state, $info));
?>