<?php
	
	include "../../basic/parameter.inc";
	
	$month = $_POST["month"];
	//$month = "2013-06";
	$cSign = $_POST["cSign"];
	//$cSign = "7";
	
	$signList = array();
	
	$database = ($cSign == "7")?$DataIn:$DataSub;
	
	$getDetailAttendanceListSql = "Select B.Id, A.Number, C.Name, B.Sign, D.Name as Branch, E.GroupName From $database.kqdata A
								   Left Join $DataPublic.attendance_confirm_sign B On A.Number = B.Number AND B.SignMonth = A.Month
								   Left Join $DataPublic.staffmain C On C.Number = A.Number
								   Left Join $DataPublic.branchdata D On D.Id = C.BranchId
								   Left Join $database.staffgroup E On E.GroupId = C.GroupId
								   Where 
								   A.Month = '$month'
								   And C.KqSign = '1'
								   And C.Estate = '1'
								   Order By D.Id, E.GroupId";
	
	$detailAttendanceListResult = mysql_query($getDetailAttendanceListSql);
	while($detailAttendanceRow = mysql_fetch_assoc($detailAttendanceListResult))
	{
		$id = $detailAttendanceRow["Id"];
		$number = ($detailAttendanceRow["Number"])?$detailAttendanceRow["Number"]:"";
		$name = $detailAttendanceRow["Name"];
		$sign = $detailAttendanceRow["Sign"];
		$branch = $detailAttendanceRow["Branch"];
		$group = $detailAttendanceRow["GroupName"];
		
		$state = ($sign == "")?"no":"yes";
		
		$signList[] = array("id"=>"$id", "number"=>"$number", "name"=>"$name", "branch"=>"$branch", "group"=>"$group", "sign"=>"$sign", "state"=>"$state");	
	}
	
	echo json_encode($signList);
	
?>