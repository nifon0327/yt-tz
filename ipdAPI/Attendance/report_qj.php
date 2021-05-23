<?php 

	include "../../basic/parameter.inc";
	$ipadTag = "yes";
	include "../../model/kq_YearHolday.php";
	include("getStaffNumber.php");
	
	$idNum = $_POST["idNum"];
	if(strlen($idNum) != 5)
	{
		$idNum = getStaffNumber($idNum, $DataPublic);
	}
	
	$qjArray = array();

	$mysql ="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.bcType,J.Estate,T.Name AS Type FROM $DataPublic.kqqjsheet J  
						LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
						WHERE 1 AND J.Number='$idNum'  ORDER BY J.StartDate Desc";
	
	$myResult = mysql_query($mysql);
	if(mysql_num_rows($myResult) != 0)
	{
		$no = 0;
		while($myRow = mysql_fetch_assoc($myResult))
		{
			$no++;
			$startTime = $myRow["StartDate"];
			$startTime = substr($startTime, 0, 16);
			$endTime = $myRow["EndDate"];
			$endTime = substr($endTime, 0, 16);
			$Reason=$myRow["Reason"];
			$Type=$myRow["Type"];
			$bcType=$myRow["bcType"];
			$Estate=$myRow["Estate"]==1?"未批准":"已批准";
			$time = GetBetweenDateDays($idNum,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
			
			$qjArray[] = array("$no",$startTime,$endTime,"$time",$Type,$Reason,$Estate);
		}
	}
		
	echo json_encode($qjArray);
	
?>