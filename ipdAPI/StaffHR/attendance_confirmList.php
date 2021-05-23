<?php
	
	include "../../basic/parameter.inc";
	
	$getAttendanceList = "Select A.Id, A.Month, A.FileName, A.CSign, B.Name From $DataPublic.attendance_confirm_list A
						  Left Join $DataPublic.staffmain B On A.Operator = B.Number
						  Order By Month Desc";
	
	$attendanceListResult = mysql_query($getAttendanceList);
	
	$attendanceLists = array();
	while($attendanceListRow = mysql_fetch_assoc($attendanceListResult))
	{
		$id = $attendanceListRow["Id"];
		$month = $attendanceListRow["Month"];
		$filename = $attendanceListRow["FileName"];
		$cSign = $attendanceListRow["CSign"];
		$operator = $attendanceListRow["Name"];
		
		$attendanceLists[] = array("Id"=>"$id", "Month"=>"$month", "Filename" => "$filename", "Csign"=>"$cSign", "Operator"=>"$operator");
		
	}
	
	echo json_encode($attendanceLists);
	
?>