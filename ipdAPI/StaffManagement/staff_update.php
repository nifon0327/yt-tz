<?php 

	include "../../basic/parameter.inc";
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	
	$staffName = $_POST["name"];
	$staffSex = $_POST["sex"];
	$staffSex = ($staffSex == "男")?"1":"0";
	
	$staffPeople = $_POST["people"];
	$peopleResult = mysql_query("Select Id From $DataPublic.nationdata Where Name = '$staffPeople'");
	$peopleRow = mysql_fetch_assoc($peopleResult);
    $staffPeople = $peopleRow["Id"];
	
	$staffRpr = $_POST["rpr"];
	$rprResult = mysql_query("Select Id From $DataPublic.rprdata Where Name = '$staffRpr'");
	$rprRow = mysql_fetch_assoc($rprResult);
	$staffRpr = $rprRow["Id"];
	
	$staffEducation = $_POST["education"];
	$educationResult = mysql_query("Select Id From $DataPublic.education Where Name = '$staffEducation'");
	$educationRow = mysql_fetch_assoc($educationResult);
	$staffEducation = $educationRow["Id"];
	
	$staffMarried = $_POST["married"];
	$staffMarried = ($staffMarried == "未婚")?"1":"0";
	
	$staffBirthday = $_POST["brithday"];
	$staffIdcard = $_POST["idcard"];
	$staffAddress = $_POST["address"];
	$staffTel = $_POST["tel"];
	$staffPostcode = $_POST["postcode"];
	
	$staffNum = $_POST["num"];
	$staffType = $_POST["type"];
	
	$staffType = ($staffType == "正式工")?"1":"2";
	
	/*
	$staffBranch = $_POST["branch"];
	$branchResult = mysql_query("Select Id From $DataPublic.branchdata Where Name = '$staffBranch'");
	$branchRow = mysql_fetch_assoc($branchResult);
	$staffBranch = $branchRow["Id"];
	*/
	
	$staffBranch = $_POST["branch"];
	$staffBranchBreak = explode("-",$staffBranch);
	$staffBranch = $staffBranchBreak[0];
	
	$staffJob = $_POST["job"];
	$staffJobBreak = explode("-",$staffJob);
	$staffJob = $staffJobBreak[0];
	
	$staffGroup = $_POST["group"];
	$staffTmpGroup = explode("-",$staffGroup);
	$staffGroup = $staffTmpGroup[0];
	
	$staffIntro = $_POST["intro"];
	$staffIntroTyp = explode("-",$staffIntro);
	$staffIntro = $staffIntroTyp[1];
	
	$staffMobile = $_POST["mobile"];
	$staffDh = $_POST["dh"];
	$staffExtno = $_POST["extno"];
	$staffMail = $_POST["mail"];
	$staffComeIn = $_POST["comein"];
	$staffNote = $_POST["note"];
	
	$operator = $_POST["Operator"];
	
	$ServerPath = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/";
	//$ServerPath = $_SERVER['DOCUMENT_ROOT']."/iPdAPI/";	
	
	//$ServerPath = "../../download/staffPhoto/";
	
	if($staffNum == "")
	{
		$actionType = "insert";
	}
	else
	{
		$actionType = "update";
	}
	
	
	if($actionType =="insert")
		{   
			$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.staffmain ORDER BY Number DESC",$link_id));
			$Local_MaxNumber=$checkNumRow["Number"];
			//echo "Local_MaxNumber:$Local_MaxNumber <br>";
			$MaxNumber=$Local_MaxNumber>$remote_MaxNumber?$Local_MaxNumber:$remote_MaxNumber;
	
			if($MaxNumber>1)
			{
				$staffNum=$MaxNumber+1;
			}
			else
			{
				$staffNum=10001;
			}
		}

	
	$hasPicSql = "Select Photo,IdcardPhoto,HealthPhoto From $DataPublic.staffsheet Where Number = '$staffNum'";
	$hasPicResult = mysql_query($hasPicSql);
	$hasPicRow = mysql_fetch_assoc($hasPicResult);
	$hasIcon = $hasPicRow["Photo"];
	$hasIdCard = $hasPicRow["IdcardPhoto"];
	$hasHealth = $hasPicRow["HealthPhoto"];
	
	if(is_uploaded_file($_FILES['icon']['tmp_name']))
	{
		$filePath = $ServerPath."P".$staffNum.".jpg";
		
		if(file_exists($filePath))
		{
			if(unlink($filePath))
			{
				copy($_FILES['icon']['tmp_name'],$filePath);
				$upValue1 =($staffNum == "")?"1":",Photo='1'";
			}
			else
			{
				//echo "failed";
				
			}
		}
		else
		{
			copy($_FILES['icon']['tmp_name'],$filePath);
			$upValue1 =($staffNum == "")?"1":",Photo='1'";
		}
	}
	else
	{
		if($actionType == "update" && $hasIcon)
		{
			$upValue1 = ",Photo='$hasIcon'";
		}
		else if($actionType == "insert")
		{
			$upValue1 = "0";
		}
		//$upValue1 =($staffNum == "")?"0":",Photo='0'";
	}
	
	if(is_uploaded_file($_FILES['id']['tmp_name']))
	{
		$filePath = $ServerPath."c".$staffNum.".jpg";
		
		if(file_exists($filePath))
		{
			if(unlink($filePath))
			{
				copy($_FILES['id']['tmp_name'],$filePath);
				$upValue2= ($staffNum == "")?"1":",IdcardPhoto='1'";
			}
			else
			{
				//echo "failed";
			}
		}
		else
		{
			copy($_FILES['id']['tmp_name'],$filePath);
			$upValue2= ($staffNum == "")?"1":",IdcardPhoto='1'";
		}
	}
	else
	{
		if($actionType == "update" && $hasIdCard)
		{
			$upValue2 = ",IdcardPhoto='$hasIdCard'";
		}
		else if($actionType == "insert")
		{
			$upValue2 = "0";
		}

		//$upValue2= ($staffNum == "")?"0":",IdcardPhoto='0'";
	}
	
	if(is_uploaded_file($_FILES['health']['tmp_name']))
	{
		$filePath = $ServerPath."H".$staffNum.".jpg";
		
		$hasHeal = "has Health";
		
		if(file_exists($filePath))
		{
			if(unlink($filePath))
			{
				copy($_FILES['health']['tmp_name'],$filePath);
				$upValue4= ($staffNum == "")?"1" :",HealthPhoto='1'";
			}
			else
			{
				//echo "failed";
			}
		}
		else
		{
			copy($_FILES['health']['tmp_name'],$filePath);
			$upValue4= ($staffNum == "")?"1" :",HealthPhoto='1'";
		}
	}
	else
	{
		if($actionType == "update" && $hasHealth)
		{
			$upValue4 = ",HealthPhoto='$hasHealth'";
		}
		else if($actionType == "insert")
		{
			$upValue4 = "0";
		}

		//$upValue4= ($staffNum == "")?"0" :",HealthPhoto='0'";
	}

	$updateInfo = "no";
	if($actionType == "update")
	{
		//更新
		$mainSql = "UPDATE $DataPublic.staffmain SET Name='$staffName',IdNum='',GroupId='$staffGroup',Mail='$staffMail',ExtNo='$staffExtno',ComeIn='$staffComeIn',Introducer='$staffIntro',FormalSign='$staffType',Date='$Date',Locks=0,Operator='$operator' WHERE Number='$staffNum'";
		$mainResult = mysql_query($mainSql);
		if($mainResult)
		{
			$info = "主表更新成功!";
		
			$sheetSql ="UPDATE $DataPublic.staffsheet SET 
		Sex='$staffSex',Nation='$staffPeople',Rpr='$staffRpr',Education='$staffEducation',Married='$staffMarried',Birthday='$staffBirthday',
		Idcard='$staffIdcard',Address='$staffAddress',Postalcode='$staffPostcode',Tel='$staffTel',Mobile='$staffMobile',Dh='$staffDh',
		Note='$staffNote' $upValue1 $upValue2 $upValue3 $upValue4 WHERE Number='$staffNum'";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult)
			{
				 $updateInfo = "yes";
				 $info = $info." 从表更新成功";
			}
			else
			{
				$info = $info." 从表更新失败";
			}
		}
		else
		{
			$info = "主表更新失败";
		}
	}
	else if($actionType == "insert")
	{
		//插入
		//$LockSql=" LOCK TABLES $DataPublic.staffmain WRITE"; $LockRes=@mysql_query($LockSql);
		/*
		if($staffNum=="")
		{   
			$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.staffmain ORDER BY Number DESC",$link_id));
			$Local_MaxNumber=$checkNumRow["Number"];
			//echo "Local_MaxNumber:$Local_MaxNumber <br>";
			$MaxNumber=$Local_MaxNumber>$remote_MaxNumber?$Local_MaxNumber:$remote_MaxNumber;
	
			if($MaxNumber>1)
			{
				$staffNum=$MaxNumber+1;
			}
			else
			{
				$staffNum=10001;
			}
		}
		*/
		//员工等级默认值设置：预设为该职位的最低等级
		$CheckGrade=mysql_query("SELECT Low FROM $DataPublic.gradedata WHERE Id='$staffJob' order by Id LIMIT 1",$link_id);
		if($CheckGradeRow = mysql_fetch_array($CheckGrade))
		{
			$Grade=$CheckGradeRow["Low"];
		}
		else
		{
			$Grade=0;
		}	
				
		$inRecode="INSERT INTO $DataPublic.staffmain (Id,cSign,Number,IdNum,Name,Nickname,Grade,KqSign,BranchId,JobId,GroupId,Mail,ExtNo,ComeIn,Introducer,FormalSign,Date,Estate,Locks,Operator) VALUES (NULL,'7','$staffNum','','$staffName','','$Grade','1','$staffBranch','$staffJob','$staffGroup','$staffMail','','$staffComeIn','$staffIntro','$staffType','$Date','1','0','$operator')";
		$inAction=@mysql_query($inRecode);
		
		//$unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
		if($inAction)
		{	
			$updateInfo = "yes";
			$info = "主表插入成功!";
			
	//2-从表加入	
	
		$inSheet="INSERT INTO $DataPublic.staffsheet (Id,Number,Sex,Nation,Rpr,Education,Married,Birthday,Photo,IdcardPhoto,HealthPhoto,Idcard,Address,Postalcode,Tel,Mobile,Dh,Bank,Note,InFile) VALUES (NULL,'$staffNum','$staffSex','$staffPeople','$staffRpr','$staffEducation','$staffMarried','$staffBirthday','$upValue1','$upValue2','$upValue4','$staffIdcard','$staffAddress','$staffPostcode','$staffTel','$staffMobile','$staffDh','','$staffNote','0')";
		
	$inAction2=@mysql_query($inSheet);
	
	if($inAction2)
	{
		$info = $info." 从表插入成功!";
		$baseinsql = "INSERT INTO $DataPublic.paybase (Id,Number,Jj,Jtbz,Locks,Date,Operator) VALUES (NULL,'$Number','0','0','0','$Date','$Operator')";
	//$baseinresult = @mysql_query($baseinsql);
	if($baseinresult){
		$info = $info." 薪资初始化成功";
		}
	else{
		
		$info = $info." 薪资初始化失败";
		}
	
	}
	else
	{
		$info = $info." 从表插入失败!";
	}
	
	$info = "主表插入失败!";
	
	}
}

	$staffUp = array($updateInfo,$inSheet);
	echo json_encode($staffUp);
	
?>