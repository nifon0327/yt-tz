<?php

//用于获取branch、job、group
	include "../../basic/parameter.inc";
	//部门分类
	$branchData = array();
	$branchDataSql = "Select Id,Name,cSign From $DataPublic.branchdata Where Estate = '1'";
	$branchResult = mysql_query($branchDataSql);
	while($branchRow = mysql_fetch_row($branchResult))
	{
		$branchData[] = $branchRow;
	}
	//职位分类
	$jobData = array();
	$jobDataSql = "Select Id,Name,cSign From $DataPublic.jobdata Where Estate = '1'";
	$jobResult = mysql_query($jobDataSql);
	while($jobRow = mysql_fetch_row($jobResult))
	{
		$jobData[] = $jobRow;
	}

	//研砼小组分类
	$groupD7 = array();
	$groupD7Sql = "Select Id,GroupId,GroupName From $DataIn.staffgroup Where Estate = '1'";
	$groupD7Result = mysql_query($groupD7Sql);
	while($groupD7Row = mysql_fetch_row($groupD7Result))
	{
		$groupD7[] = $groupD7Row;
	}
	//鼠宝小组
	$groupPt = array();
	$otherDataIn = ($DataIn == "d7")?"d3":"d7";
	$groupPtSql = "Select Id,GroupId,GroupName From $otherDataIn.staffgroup Where Estate = '1'";
	$groupPtResult = mysql_query($groupPtSql);
	while($groupPtRow = mysql_fetch_row($groupPtResult))
	{
		$groupPt[] = $groupPtRow;
	}
	//教育资料
	$education = array();
	$educationSql = "Select Id,Name From $DataPublic.education Where Estate = '1' Order By Id";
	$educationResult = mysql_query($educationSql);
	while($educationRow = mysql_fetch_row($educationResult))
	{
		$education[] = $educationRow;
	}
	//工作楼层
	$workAddress = array();
	$workAddressSql = "Select Id,Name From $DataPublic.staffworkadd Where Estate = '1' Order By Id";
	$workAddressResult = mysql_query($workAddressSql);
	while($workAddressRow = mysql_fetch_row($workAddressResult))
	{
		$workAddress[] = $workAddressRow;
	}

	$initData = array($branchData, $jobData, $groupD7, $groupPt, $education, $workAddress);
	echo json_encode($initData);

?>