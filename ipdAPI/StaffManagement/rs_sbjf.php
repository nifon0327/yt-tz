<?php 

	include "../../basic/parameter.inc";
	
	$targetDate = $_POST["date"];
	
	if(!$targetDate)
	{
		$date_Result = mysql_query("SELECT Month FROM $DataIn.sbpaysheet WHERE 1 GROUP BY Month order by Month DESC limit 1",$link_id);
		$date_row = mysql_fetch_assoc($date_Result);
		$targetDate = $date_row["Month"];
	}
	
	$sbjfArray = array();
	$no = 0;
	$mySql="SELECT 
	S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,S.Date,S.Operator,S.Estate,
	P.Name
	 FROM $DataIn.sbpaysheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
	WHERE 1 and S.Month = '$targetDate'
	ORDER BY S.BranchId,S.JobId,P.Number";
	
	$sbResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($sbResult))
	{
		$no++;
		$m=1;
		$Id=$myRow["Id"];			
		$Name=$myRow["Name"];
		$Number = $myRow["Number"];
		$Month =$myRow["Month"];
		$mAmount =$myRow["mAmount"];
		$cAmount =$myRow["cAmount"];
		$Amount=sprintf("%.2f",$mAmount +$cAmount);
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Operator=$myRow["Operator"];
		include "../../admin/subprogram/staffname.php";
		$Estate=$myRow["Estate"];
		switch($Estate)
		{
			case 1:
				$Estate="未处理";
				$LockRemark="";
			break;
			case 2:
				$Estate="请款中";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
			break;
			case 3:
				$Estate="请款通过";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
			break;
			case 0:
				$Estate="已结付";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
			break;
		}
		
		$sbjfArray[] = array("$no","$Name","$Number","$Branch","$Job","$Month","$mAmount","$cAmount","$Amount","$Estate","$Date","$Operator","$Id");

	}
	
	echo json_encode($sbjfArray);

?>