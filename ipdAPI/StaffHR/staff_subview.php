<?php

	include "../../basic/parameter.inc";

	$staffNum = $_POST["Number"];
	//$staffNum = "11008";

	$staffInfoSql = "SELECT M.Id,M.Number,M.Name,M.Nickname,M.Grade,M.KqSign,M.Introducer,M.Mail,M.ExtNo,M.FormalSign,M.workAdd,W.Name As workAddName,M.cSign,M.ComeIn,M.GroupId,M.Introducer,S.Sex,S.Nation,N.Name AS NationName,S.Rpr,R.Name As RprName,S.Education,E.Name As EducationName,S.Married,S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,S.Mobile,S.Dh,S.Bank,S.Note,S.HealthPhoto,B.Name AS Branch,B.Id As branchId,J.Id As jobId ,J.Name AS Job ,L.Name As BloodName
		FROM $DataPublic.staffmain M
		LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
		LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
		LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
		LEFT JOIN $DataPublic.nationdata N ON N.Id = S.Nation
		LEFT JOIN $DataPublic.rprdata R ON R.Id = S.Rpr
		LEFT JOIN $DataPublic.education E ON E.Id = S.Education
		LEFT JOIN $DataPublic.staffworkadd W ON W.Id = M.workAdd
		Left Join $DataPublic.bloodgroup_type L On L.Id = S.BloodGroup
		WHERE M.Number=$staffNum LIMIT 1";

	$staffSubInfoResult = mysql_query($staffInfoSql);
	$staffSubInfo = mysql_fetch_assoc($staffSubInfoResult);

	//第一部分
	$name = $staffSubInfo["Name"];

	//第二部分
	$nickName = $staffSubInfo["Nickname"];
	$sex = ($staffSubInfo["Sex"] == 1)?"男":"女";
	$nation = $staffSubInfo["NationName"];
	$rpr = $staffSubInfo["RprName"];
	$education = $staffSubInfo["EducationName"];
	$married = ($staffSubInfo["Married"]==1)?"未婚":"已婚";
	$birth = $staffSubInfo["Birthday"];
	$idCard = $staffSubInfo["Idcard"];
	$telephone = $staffSubInfo["Tel"];
	$postalCode = $staffSubInfo["Postalcode"];
	$address = $staffSubInfo["Address"];
	$bloodType = $staffSubInfo["BloodName"];
	if(!$bloodType)
	{
		$bloodType = "未确定";
	}

	$basic = array($nickName,$sex,$nation,$rpr,$education,$married,$birth,$bloodType,$idCard,$telephone,$postalCode,$address);

	//第三部分
	$companySign = ($staffSubInfo["cSign"]==7)?"研砼":"鼠宝";
	$staffType = ($staffSubInfo["FormalSign"]==1)?"正式工":"试用期";
	$staffNum = $staffSubInfo["Number"];
	$workAddress = $staffSubInfo["workAddName"];
	$mobile = $staffSubInfo["Mobile"];
	$dh = $staffSubInfo["Dh"];
	$extNo = $staffSubInfo["ExtNo"];
	$ComeIn = $staffSubInfo["ComeIn"];
	$email = $staffSubInfo["Mail"];
	$note = $staffSubInfo["Note"];

	$company = array($companySign,$staffType,$staffNum,$workAddress,$mobile,$dh,$extNo,$ComeIn,$email,$note);

	//第四部分
	$photoTag = $staffSubInfo["Photo"];
	$idCardPhotoTag = $staffSubInfo["IdcardPhoto"];
	$healthPhotoTag = $staffSubInfo["HealthPhoto"];

	$photo = array($photoTag, $idCardPhotoTag, $healthPhotoTag);

	$staffSub = array($name, $basic, $company, $photo);

	echo json_encode($staffSub);

?>