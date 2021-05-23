<?php
	
	include_once "../../basic/parameter.inc";
	
	$staffListSql = "Select A.Name, A.Number, A.IdNum, A.KqSign, A.cSign, A.JobId, A.BranchId, B.Name as BranchName From $DataPublic.staffmain A
					 Left Join $DataPublic.branchdata B On B.Id = A.BranchId
					 Where A.Estate = 1
					 Order By A.BranchId, A.JobId, A.Number";
	
	$needCheck = array();
	$noNeedCheck = array();
	$staffResult = mysql_query($staffListSql);
	
	while($staffRow = mysql_fetch_assoc($staffResult))
	{
		$name = $staffRow["Name"];
		$number = $staffRow["Number"];
		$idNum = $staffRow["IdNum"];
		$kqSign = $staffRow["KqSign"];
		$cSign = $staffRow["cSign"];
		$jobId = $staffRow["JobId"];
		$branchId = $staffRow["BranchId"];
		$branchName = $staffRow["BranchName"];
		
		if($kqSign < 3)
		{
			$needCheck[] = array("$name", "$branchName", "$idNum", "$number", "$kqSign", "$cSign", "$jobId", "$branchId");
		}
		else
		{
			$noNeedCheck[] = array("$name", "$branchName", "$idNum", "$number", "$kqSign", "$cSign", "$jobId", "$branchId");
		}
	}
	
	echo json_encode(array($noNeedCheck, $needCheck));
	
?>