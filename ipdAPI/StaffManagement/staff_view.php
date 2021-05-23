<?php 

	include "../../basic/parameter.inc";
	
	$staffNum = $_POST["staffNum"];
	//$staffNum = "11008";
	
	$Sql="
	SELECT M.Id,M.Number,M.Name,M.Nickname,M.Grade,M.KqSign,M.Introducer,M.Mail,M.ExtNo,M.FormalSign,M.ComeIn,M.GroupId,M.Introducer,
		S.Sex,S.Nation,S.Rpr,S.Education,S.Married,S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,
		S.Mobile,S.Dh,S.Bank,S.Note,S.HealthPhoto,
		B.Name AS Branch,B.Id As branchId,J.Id As jobId ,J.Name AS Job 
		FROM $DataPublic.staffmain M
		LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
		LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
		LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
		WHERE M.Number=$staffNum LIMIT 1
	";

	$staffDetailResult = mysql_query($Sql);
	if($staffDetailRow = mysql_fetch_assoc($staffDetailResult))
	{
		$staffName = $staffDetailRow["Name"];
		$staffNum = $staffDetailRow["Number"];
		$staffSex = ($staffDetailRow["Sex"] == "1")?"男":"女";
		
		$staffPeople = $staffDetailRow["Nation"];
		$peopleSql = mysql_query("SELECT Name FROM $DataPublic.nationdata WHERE 1  and Id=$staffPeople order by Id LIMIT 1",$link_id);
		$peopleResult = mysql_fetch_assoc($peopleSql);
		$staffPeople = $peopleResult["Name"];
		
		$staffRpr = $staffDetailRow["Rpr"];
		$rprSql = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE 1  and Id=$staffRpr order by Id LIMIT 1",$link_id);
		$rprResult = mysql_fetch_assoc($rprSql);
		$staffRpr = $rprResult["Name"];
		
		$staffEducation = $staffDetailRow["Education"];
		$educationSql = mysql_query("SELECT Name FROM $DataPublic.education WHERE 1  and Id=$staffEducation order by Id LIMIT 1",$link_id);
		$educationResult = mysql_fetch_assoc($educationSql);
		$staffEducation = $educationResult["Name"];
		
		$staffIntroducer = $staffDetailRow["Introducer"];
		if($staffIntroducer)
		{
			$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$staffIntroducer order by Id",$link_id);
			$iRow = mysql_fetch_assoc($iResult);
			$staffIntroducer = $iRow["Name"]."-".$staffIntroducer;
		}
		$staffMarried = "";
		switch($staffDetailRow["Married"])
		{
			case 0:
				$staffMarried = "已婚";
				break;
			case 1:
				$staffMarried = "未婚";
				break;
			case 2:
				$staffMarried = "离异";
				break;
			case 3:
				$staffMarried = "再婚";
				break;
		}
		
		$staffBirthday = $staffDetailRow["Birthday"];
		$staffIdCard = $staffDetailRow["Idcard"];
		$staffAddress = $staffDetailRow["Address"];
		$staffPostCode = $staffDetailRow["Postalcode"];
		$staffTel = $staffDetailRow["Tel"];
		
		
		$staffType = ($staffDetailRow["FormalSign"] == "1")?"正式工":"试用期";
		$staffBranch = $staffDetailRow["branchId"]."-".$staffDetailRow["Branch"];
		$staffJob = $staffDetailRow["jobId"]."-".$staffDetailRow["Job"];
		
		$staffGroupId = $staffDetailRow["GroupId"];
		$groupResult = mysql_query("Select GroupName From $DataIn.staffgroup Where GroupId = $staffGroupId");
		$groupRow = mysql_fetch_assoc($groupResult);
		$staffGroupId = $staffGroupId."-".$groupRow["GroupName"];
		
		$staffMobile = $staffDetailRow["Mobile"];
		$staffDh = $staffDetailRow["Dh"];
		$staffExtNo = $staffDetailRow["ExtNo"];
		$staffMail = $staffDetailRow["Mail"];
		$staffComeIn = $staffDetailRow["ComeIn"];
		$staffNote = $staffDetailRow["Note"];
		
	}
	
	$iconImage = "";
	$idImage = "";
	$healthImage = "";
	
	$iconImage = ($staffDetailRow["Photo"] == "1")?"1":"0";
	$idImage = ($staffDetailRow["IdcardPhoto"] == "1")?"1":"0";
	$healthImage = ($staffDetailRow["HealthPhoto"] == "1")?"1":"0";
	
	$imageRecord = $iconImage.":".$idImage.":".$healthImage.":".$staffName;
	
	$staffNormal = array("$staffSex","$staffPeople","$staffRpr","$staffEducation","$staffMarried","$staffBirthday","$staffIdCard","$staffAddress","$staffTel","$staffPostCode");
	
	$staffCompany = array("$staffNum","$staffType","$staffBranch","$staffJob","$staffGroupId","$staffIntroducer","$staffMobile","$staffDh","$staffExtNo","$staffMail","$staffComeIn","$staffNote");
	
	$staffImage = array($imageRecord);
	
	$staffDetail = array($staffNormal,$staffCompany,$staffImage);
	
	/*
	$staffDetail = array("$staffName","$staffNum","$staffSex","$staffPeople","$staffRpr","$staffEducation","$staffMarried","$staffBirthday","$staffIdCard","$staffAddress","$staffPostCode","$staffTel","$staffBranch","$staffJob","$staffMobile","$staffDh","$staffMail","$staffComeIn","$staffNote",$imageRecord);
	*/
	echo json_encode($staffDetail);

?>