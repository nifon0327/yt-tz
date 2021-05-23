<?php
	
	include_once "../basic/parameter.inc";
	include "../model/kq_YearHolday.php";
	
	
	$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Date,J.Operator,J.type,M.cSign,M.Number,J.Note,M.WorkAdd,M.Name,M.KqSign,M.JobId,M.BranchId
			FROM $DataPublic.bxSheet J 
			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number
			WHERE J.Number = '$number' Order by J.StartDate desc";	
	$totleHours = 0;
	$success = "N";
	$mysqlResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($mysqlResult))
	{
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$calculateType = $myRow["type"];

		$hours = ($calculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
		
		$totleHours += $hours;
		if($totleHours>0)
		{
			$resetSql = "update $DataPublic.bxtimecount set Hours = '$totleHours' where Number = '$number'";
			if(mysql_query($resetSql))
			{
				$success = "Y";
			}
		}
		
	}
	
	echo $success;
	
?>