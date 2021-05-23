<?php 

	include "../../basic/parameter.inc";
	
	$staffNm = $_POST["staffNum"];
	//$staffNm = "10172";
	//取个人部门调动信息
	$branchTransfer = array();
	$branchTransferSql = "SELECT J.Id,J.Number,J.ActionIn,J.ActionOut,J.Month,J.Remark,J.Date,J.Locks,J.Operator,M.Name
	FROM $DataPublic.redeployb J 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
	WHERE  J.Number = '$staffNm' ORDER BY J.Id DESC,M.Estate DESC,J.Month DESC,J.Number";
	$branchResutl = mysql_query($branchTransferSql);
	while($branchRow = mysql_fetch_assoc($branchResutl))
	{
		$tmpActionIn = $branchRow["ActionIn"];
		$inResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE Id=$tmpActionIn Limit 1",$link_id));
		$tmpActionIn = $tmpActionIn."-".$inResult["Name"];
		
		$tmpActionOut = $branchRow["ActionOut"];
		$outResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE Id=$tmpActionOut Limit 1",$link_id));
		$tmpActionOut = $tmpActionOut."-".$outResult["Name"];
		
		$tmpMonth = $branchRow["Month"];
		$tmpRemark = $branchRow["Remark"];
		
		$tmpOeration = $branchRow["Operator"];
		$operationResult = mysql_fetch_assoc(mysql_query("Select Name From $DataPublic.staffmain Where Number = '$tmpOeration'"));
		$tmpOeration = $operationResult["Name"];
		
		$branchTransfer[] = array("$tmpActionOut","$tmpActionIn","$tmpMonth","$tmpOeration","$tmpRemark");
	}
	
	$tmpActionIn = "";
	$tmpActionOut = "";
	$tmpMonth = "";
	$tmpRemark = "";
	$tmpOeration = "";
	
	
	
	//取个人职位调动信息
	$jobTransfer = array();
	$jobTransferSql = "SELECT J.Id,J.Number,J.ActionIn,J.ActionOut,J.Month,J.Remark,J.Date,J.Locks,J.Operator,M.Name
	FROM $DataPublic.redeployj J 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
	WHERE J.Number = '$staffNm' ORDER BY J.Id DESC,M.Estate DESC,J.Month DESC,J.Number";
	
	$jobResult = mysql_query($jobTransferSql);
	while($jobRow = mysql_fetch_assoc($jobResult))
	{
		$tmpActionIn = $jobRow["ActionIn"];
		$inResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE Id=$tmpActionIn Limit 1",$link_id));
		$tmpActionIn = $tmpActionIn."-".$inResult["Name"];
		
		$tmpActionOut = $jobRow["ActionOut"];
		$outResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE Id=$tmpActionOut Limit 1",$link_id));
		$tmpActionOut = $tmpActionOut."-".$outResult["Name"];
		
		$tmpMonth = $jobRow["Month"];
		$tmpRemark = $jobRow["Remark"];
		
		$tmpOeration = $jobRow["Operator"];
		$operationResult = mysql_fetch_assoc(mysql_query("Select Name From $DataPublic.staffmain Where Number = '$tmpOeration'"));
		$tmpOeration = $operationResult["Name"];
		
		$jobTransfer[] = array("$tmpActionOut","$tmpActionIn","$tmpMonth","$tmpOeration","$tmpRemark");
	}
	$tmpActionIn = "";
	$tmpActionOut = "";
	$tmpMonth = "";
	$tmpRemark = "";
	$tmpOeration = "";
	//print_r($jobTransfer);
	//取个人考勤调动信息
	$kqTransfer = array();
	$kqTransferSql = "SELECT K.Id,K.Number,K.ActionIn,K.ActionOut,K.Month,K.Remark,K.Date,K.Locks,K.Operator,M.Name
	FROM $DataPublic.redeployk K 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
	WHERE K.Number = '$staffNm' ORDER BY K.Id DESC,M.Estate DESC,K.Month DESC,K.Number";
	$kqResult  = mysql_query($kqTransferSql);
	while($kqRow = mysql_fetch_assoc($kqResult))
	{
		$tmpActionIn = $kqRow["ActionIn"];
		$inResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.kqtype WHERE Id=$tmpActionIn Limit 1",$link_id));
		//echo "SELECT Name FROM $DataPublic.kqtype WHERE Id=$tmpActionIn Limit 1";
		$tmpActionIn = $tmpActionIn."-".$inResult["Name"];
		
		$tmpActionOut = $kqRow["ActionOut"];
		$outResult = mysql_fetch_assoc(mysql_query("SELECT Name FROM $DataPublic.kqtype WHERE Id=$tmpActionOut Limit 1",$link_id));
		$tmpActionOut = $tmpActionOut."-".$outResult["Name"];
		
		$tmpMonth = $kqRow["Month"];
		$tmpRemark = $kqRow["Remark"];
		
		$tmpOeration = $kqRow["Operator"];
		$operationResult = mysql_fetch_assoc(mysql_query("Select Name From $DataPublic.staffmain Where Number = '$tmpOeration'"));
		$tmpOeration = $operationResult["Name"];
		
		$kqTransfer[] = array("$tmpActionOut","$tmpActionIn","$tmpMonth","$tmpOeration","$tmpRemark");
	}
	$tmpActionIn = "";
	$tmpActionOut = "";
	$tmpMonth = "";
	$tmpRemark = "";
	$tmpOeration = "";
	//取个人等级调动信息
	$gradeTransfer = array();
	$gradeTransferSql = "SELECT G.Id,G.Number,G.ActionIn,G.ActionOut,G.Month,G.Remark,
	G.Date,G.Locks,G.Operator,M.Name
	FROM $DataPublic.redeployg G 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=G.Number 
	WHERE G.Number = '$staffNm' order by G.Id DESC,M.Estate DESC,G.Month DESC,G.Number";
	//echo $gradeTransferSql;
	$gradeResult = mysql_query($gradeTransferSql);
	while($gradeRow = mysql_fetch_assoc($gradeResult))
	{
		$tmpActionIn = $gradeRow["ActionIn"];		
		$tmpActionOut = $gradeRow["ActionOut"];
		$tmpMonth = $gradeRow["Month"];
		$tmpRemark = $gradeRow["Remark"];
		$tmpOeration = $gradeRow["Operator"];
		
		$operationResult = mysql_fetch_assoc(mysql_query("Select Name From $DataPublic.staffmain Where Number = '$tmpOeration'"));
		$tmpOeration = $operationResult["Name"];
		
		$gradeTransfer[] = array("$tmpActionOut","$tmpActionIn","$tmpMonth","$tmpOeration","$tmpRemark");
	}
	$tmpActionIn = "";
	$tmpActionOut = "";
	$tmpMonth = "";
	$tmpRemark = "";
	$tmpOeration = "";
	
	$detailTransfer = array($branchTransfer,$jobTransfer,$kqTransfer,$gradeTransfer);
	
	echo json_encode($detailTransfer);
	
?>