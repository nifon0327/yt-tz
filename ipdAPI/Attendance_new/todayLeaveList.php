<?php
	
	include "../../basic/parameter.inc";
	$ipadTag = "yes";
	include "../../model/kq_YearHolday.php";
		
	$leaveList = array();
	$today = date("Y-m-d");
	$checkLeaveSql = "SELECT B.Number, B.Name, A.StartDate, A.EndDate, B.cSign, A.bcType, A.Type, C.GroupName, D.Name as WorkAdd,E.Name as BranchName
				 	  FROM $DataPublic.kqqjsheet A
				 	  LEFT JOIN $DataPublic.staffmain B ON B.Number = A.Number
				 	  Left Join $DataIn.staffgroup C On C.GroupId = B.GroupId
				 	  Left Join $DataPublic.staffworkadd D On D.Id = B.WorkAdd
				 	  LEFT JOIN $DataIn.branchdata E On E.Id = B.BranchId
				 	  WHERE B.cSign = '7'
				 	  And DATE_FORMAT(A.EndDate,'%Y-%m-%d')>='$today'
				 	  AND DATE_FORMAT(A.StartDate,'%Y-%m-%d')<='$today'
				 	  ";
	//echo $checkLeaveSql;
	$checkLeaveResult = mysql_query($checkLeaveSql);
	while($checkLeaveRow = mysql_fetch_assoc($checkLeaveResult))
	{
		$number = $checkLeaveRow["Number"];
		$name = $checkLeaveRow["Name"];
		$startDate = $checkLeaveRow["StartDate"];
		$endDate = $checkLeaveRow["EndDate"];
		$cSign = $checkLeaveRow["cSign"];
		$bcType = $checkLeaveRow["bcType"];
		$type = $checkLeaveRow["Type"];
		$groupName = $checkLeaveRow["GroupName"];
		$workAdd = $checkLeaveRow["WorkAdd"];
		$BranchName = $checkLeaveRow["BranchName"];
		$leaveState = "";
		$leaveHour = GetBetweenDateDays($number,$startDate,$endDate,$bcType,$DataIn,$DataPublic,$link_id);
		if($leaveHour < 8)
		{
			$sTime = substr($startDate, 10,6);
			$eTime = substr($endDate, 10,6);
			$leaveState = "$sTime-$eTime(".$leaveHour."h)";
		}
		else
		{
			$leaveState = "8h";
		}
		
		$leaveList[] = array("Name"=>"$name", "Number"=>"$number", "State"=>"$leaveState", "Type"=>"$type", "GroupName"=>"$BranchName($workAdd)");
		
	}
	
	echo json_encode($leaveList);
	
?>