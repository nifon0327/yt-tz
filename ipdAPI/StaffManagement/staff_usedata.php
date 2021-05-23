<?php 
    //ДњТы branchdata by zx 2012-08-13

	include "../../basic/parameter.inc";
	//$branchSql = "SELECT Id, Name FROM $DataPublic.branchdata";
	$branchSql = "SELECT Id, Name FROM $DataPublic.branchdata where 1 AND (cSign=$Login_cSign OR cSign=0 )";
	$branchArray = array();
	$branchResult = mysql_query($branchSql);
	while($branchRow = mysql_fetch_assoc($branchResult))
	{
		$branchArray[] = $branchRow["Id"]."-".$branchRow["Name"];
	}
	
	$jobSql = "Select Id,Name From $DataPublic.jobdata";
	$jobArray = array();
	$jobResult = mysql_query($jobSql);
	while($jobRow = mysql_fetch_assoc($jobResult))
	{
		$jobArray[] = $jobRow["Id"]."-".$jobRow["Name"];
	}
	
	$staffListSql = "Select m.Name,m.Number from $DataPublic.staffmain m Left Join $DataPublic.branchdata b On b.Id = m.BranchId Where m.Estate = 1 Order by m.BranchId";
	$staffList = array();
	$staffListResult = mysql_query($staffListSql);
	while($staffListRow = mysql_fetch_assoc($staffListResult))
	{
		$staffList[] = $staffListRow["Name"]."-".$staffListRow["Number"];
	}
	
	$staffGroupSql = "Select GroupId,GroupName from $DataIn.staffgroup";
	$staffGroup = array();
	$staffGroupResult = mysql_query($staffGroupSql);
	while($staffGroupRow = mysql_fetch_assoc($staffGroupResult))
	{
		$staffGroup[] = $staffGroupRow["GroupId"]."-".$staffGroupRow["GroupName"];
	}
	
	$useDataArray = array($staffList,$branchArray,$jobArray,$staffGroup);
	echo json_encode($useDataArray);

?>