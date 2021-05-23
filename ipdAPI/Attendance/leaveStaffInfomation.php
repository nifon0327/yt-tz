<?php
	
	$ipadTag = "yes";
	include "../../basic/parameter.inc";
	include "../../model/kq_YearHolday.php";
	include("getStaffNumber.php");
	
	$Date = date("Y-m-d");
	$Number = $_POST["Number"];
	//$Number = "11008";
	if(strlen($Number) != 5)
	{
		$Number = getStaffNumber($Number, $DataPublic);
	}
	
	$chooseYear=$chooseYear==""?date("Y"):$chooseYear;
	$StartDate = $chooseYear;
	$LastYear = $chooseYear-1;
	
	//获取个人信息
	$staffInfomationSql = "Select A.Name, A.ComeIn, A.BranchId, A.JobId, A.GroupId, A.WorkAdd, A.cSign, B.Name as Branch, C.Name as Job ,D.Name as WorkAddress, E.GroupName
						   From $DataPublic.staffmain A
						   Left Join $DataPublic.branchdata B On B.Id = A.BranchId
						   Left Join $DataPublic.jobdata C On C.Id = A.JobId
						   Left Join $DataPublic.staffworkadd D On D.Id = A.WorkAdd
						   Left Join $DataIn.staffgroup E On E.GroupId = A.GroupId
						   Where A.Number = '$Number'";
	$staffInfomationResult = mysql_query($staffInfomationSql);
	if($staffInfomationRow = mysql_fetch_assoc($staffInfomationResult))
	{
		$name = $staffInfomationRow["Name"];
		$ComeIn = $staffInfomationRow["ComeIn"];
		$branchId = $staffInfomationRow["BranchId"];
		$jobId = $staffInfomationRow["JobId"];
		$groupId = $staffInfomationRow["GroupId"];
		$branch = $staffInfomationRow["Branch"];
		$job = $staffInfomationRow["Job"];
		$address = $staffInfomationRow["WorkAddress"];
		$groupName = $staffInfomationRow["GroupName"];
		$cSign = $staffInfomationRow["cSign"];
		
		//组织个人信息
		$infomation = "$branch-$job($address)";
		$ComeInYM=substr($ComeIn,0,7);
		include "../../public/subprogram/staff_model_gl.php";
		$jobTime = $glPad;
		
		//部门人数
		$getBranchCountResult = mysql_query("Select Count(*) as count From $DataPublic.staffmain Where BranchId='$branchId' and cSign='$cSign' and Estate = '1'");
		
		$getBranchCountRow = mysql_fetch_assoc($getBranchCountResult);
		$branchCount = $getBranchCountRow["count"];
		
		$checkQjSql=mysql_fetch_array(mysql_query("SELECT COUNT(*)  AS countLeave FROM $DataPublic.kqqjsheet S 
	         									   LEFT JOIN $DataPublic.staffmain M  ON S.Number=M.Number 
			 									   WHERE M.BranchId='$branchId' AND M.cSign='$cSign' AND S.StartDate<='$Date 08:00' AND S.EndDate>='$Date 17:00'",$link_id));				
	    $qjNums=$checkQjSql["Nums"]==""?0:$checkQjSql["Nums"];
	    $qjRate = ($qjNums/$branchCount)*100;
	    
	    //处理年假
	    $startTime = date("Y-m-d")." 08:00";
	    $usedAnnual = HaveYearHolDayDays($Number,$startTime,$endTime,$DataIn,$DataPublic,$link_id);
		$totleAnnual = GetYearHolDayDays($Number,$startTime,$endTime,$DataIn,$DataPublic,$link_id);
	    $usedAnnalDay = number_format($usedAnnual/8,1);
	    
	}
	
	if($name == "")
	{
		$success = "N";
	}
	else
	{
		$success = "Y";
	}
	
	$infomatin = array("$success", "$name", "$infomation", "$ComeIn", "$jobTime", "$branchCount", "$qjNums", "$qjRate%", "$usedAnnual", "$totleAnnual", "$usedAnnalDay", "$Number");
	echo json_encode($infomatin);
	
?>