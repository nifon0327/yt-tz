<?php
	
	$ipadTag = "yes";
	include "../../basic/parameter.inc";
	include "../../model/kq_YearHolday.php";
	include("getStaffNumber.php");
	
	$number = $_POST["Number"];
	if(strlen($Number) != 5)
	{
		$number = getStaffNumber($number, $DataPublic);
	}

	
	$leaveList = array();
	$leavelistSql = "Select A.StartDate, A.EndDate, A.Reason, A.Estate, A.Operator, B.Name as TypeName , C.Name, A.bcType,  A.Type
					 From $DataPublic.kqqjsheet A
					 Left Join $DataPublic.qjtype B On B.Id = A.Type
					 Left Join $DataPublic.staffmain C On C.Number = A.Operator
					 Where A.Number = '$number' Order By A.Id Desc";
	//echo $leavelistSql;
	$i = 1;
	$leavelistResult = mysql_query($leavelistSql);
	while($leavelistRow = mysql_fetch_assoc($leavelistResult))
	{
		$startDate = substr($leavelistRow["StartDate"], 0, 16);
		$endDate = substr($leavelistRow["EndDate"], 0, 16);
		$reason = $leavelistRow["Reason"];
		$estate = $leavelistRow["Estate"];
		$typeName = $leavelistRow["TypeName"];
		$name = $leavelistRow["Name"];
		$bcType = $leavelistRow["bcType"];
		$operator = $leavelistRow["Operator"];
		$type = $leavelistRow["Type"];
		
		if($operator == $number)
		{
			$name = "系统";
		}
		
		$leaveTime =  GetBetweenDateDays($number,$startDate,$endDate,$bcType,$DataIn,$DataPublic,$link_id);
		$leave = "";
		
		$leaveDay = intval($leaveTime/8);
		if($leaveDay != 0)
		{
			$leave = $leaveDay."天";
		}
		
		$leaveHour = $leaveTime%8;
		if($leaveHour != 0)
		{
			$leave = $leave.$leaveHour."天";
		}
			
		$leave = "";
		if($leaveDay != 0)
		{
			$leave = "$leaveDay"."天";
		}
		
		if($leaveHour != 0)
		{
			$leave = $leave."$leaveHour"."小时";
		}
		
		$time = "$startDate ~ $endDate";
		
		$leaveList[] = array("$i", "$time", "$leave", "$reason", "$name", "$type", "$estate",);
		$i++;
	}
	
	echo json_encode($leaveList);
	
?>