<?php 

	include "../../basic/parameter.inc";
	
	$transferArray = array();
	$no = 0;
	$mySql="SELECT M.Id,M.Number,M.Name,M.Grade,M.Estate,M.Locks,K.Name AS Kq,B.Name AS Branch,J.Name AS Job,G.GroupName,M.IdNum
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFT JOIN $DataPublic.kqtype K ON K.Id = M.KqSign
	WHERE 1 AND M.Estate=1  ORDER BY M.BranchId,M.GroupId,M.JobId,M.ComeIn,M.Number";
	
	$transferResult = mysql_query($mySql);
	while($transferRow = mysql_fetch_assoc($transferResult))
	{
		$no++;
		$Id = $transferRow["Id"];
		$staffNum = $transferRow["Number"];
		$staffName = $transferRow["Name"];
		$staffBranch = $transferRow["Branch"];
		$staffJob = $transferRow["Job"];
		$staffKq = $transferRow["Kq"];
		$staffGrade = $transferRow["Grade"];
		
		$transferArray[] = array("$no","$staffName","$staffNum","$staffBranch","$staffJob","$staffGrade","$staffKq","$Id");
	}
	
	echo json_encode($transferArray);

?>