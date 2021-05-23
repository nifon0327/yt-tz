<?php
defined('IN_COMMON') || include '../basic/common.php';

/*
代码、数据库合并后共享-ZXQ 2012-08-08
加入血型字段 EWEN 2012-10-29
*/
if($ipadTag == "yes"){
	include "../basic/parameter.inc";
	include "../model/modelfunction.php";
	$Date=date("Y-m-d");
	$Log_Item="员工资料";			//需处理
	$Log_Funtion="保存";

	$Sex = ($Sex=="男")?1:0;
	//民族ID
	$NationResult = mysql_query("SELECT Id FROM $DataPublic.nationdata WHERE Estate=1 And Name='$Nation' ",$link_id);
	$NationRow = mysql_fetch_assoc($NationResult);
	$Nation = $NationRow["Id"];
	//籍贯ID
	$RprResult = mysql_query("SELECT Id FROM $DataPublic.rprdata WHERE Estate=1 And Name = '$Rpr'",$link_id);
	$RprRow = mysql_fetch_assoc($RprResult);
	$Rpr = $RprRow["Id"];
	//教育程度
	$EducationResult = mysql_query("SELECT Id FROM $DataPublic.education WHERE Estate=1 And Name = '$Education' ",$link_id);
	$EducatonRow = mysql_fetch_assoc($EducationResult);
	$Education = $EducatonRow["Id"];
	//婚姻状况
	$Married = ($Married == "未婚")?1:0;

	//血型
	if($BloodGroup == "未确定")
	{
		$BloodGroup = 0;
	}
	else
	{
		$checkBGSql=mysql_query("SELECT Id FROM $DataPublic.bloodgroup_type WHERE Name='$BloodGroup' And Estate=1 ORDER BY Id",$link_id);
		$bloodRow = mysql_fetch_assoc($checkBGSql);
		$BloodGroup = $bloodRow["Id"];
	}
	//员工类别
	$FormalSign = ($FormalSign == "正式工")?1:2;

	$cSign = ($cSign == "研砼")?"7":"3";
	//部门
	$BranchResult = mysql_query("SELECT Id FROM $DataPublic.branchdata WHERE Estate=1 And Name = '$BranchId' ",$link_id);
	$branchRow = mysql_fetch_assoc($BranchResult);
	$BranchId = $branchRow["Id"];

	//职位
	$JobResult = mysql_query("SELECT Id FROM $DataPublic.jobdata WHERE Estate=1 And Name = '$JobId' ",$link_id);
	$jobRow = mysql_fetch_assoc($JobResult);
	$JobId = $jobRow["Id"];
	//小组
	$dataTmp = ($cSign == "7")?"d7":"d3";
	$GroupResult = mysql_query("SELECT GroupId FROM $dataTmp.staffgroup WHERE Estate=1 And GroupName = '$GroupId' ",$link_id);
	$groupRow = mysql_fetch_assoc($GroupResult);
	$GroupId = $groupRow["GroupId"];
	//介绍人
	$Introducer = ($Introducer == "")?"":substr($Introducer, strcspn($Introducer,"-")+1,strlen($Introducer)-strcspn($Introducer,"-"));

	//工作地点
	$WorkAdd = intval($WorkAdd);
	$workAddResult = mysql_query("Select Id From $DataPublic.staffworkadd Where Name = '$WorkAdd'");
	$workAddRow = mysql_fetch_assoc($workAddResult);
	$WorkAdd = $workAddRow["Id"];

	$floorAddResult = mysql_query("Select Id From $DataPublic.attendance_floor Where Name = 'floorAdd'");
	$floorAddRow = mysql_fetch_assoc($floorAddResult);
	$floorAdd = $floorAddRow["Id"];

	$LogInfo = "";
}
else
{
//步骤1：
include "../model/modelhead.php";
include "public_appconfig.php";
//步骤2：
$Log_Item="员工资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";

//$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Operator=$Login_P_Number;
}

$DateTime=date("Y-m-d H:i:s");
$OperationResult="Y";
//步骤3：需处理
//回传参数
$ALType="From=$From&Pagination=$Pagination&cSign=$cSign";
//如果有照片，则检查上传的照片类型
$FileType=".jpg";

if(!file_exists($StaffPhotoPath)){
		makedir($StaffPhotoPath);
}
//$LockSql=" LOCK TABLES $DataPublic.staffmain,$DataPublic.gradedata WRITE";$LockRes=@mysql_query($LockSql);

$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.staffmain ORDER BY Number DESC",$link_id));
$MaxNumber=$checkNumRow["Number"];
$Number=$MaxNumber>1?$MaxNumber+1:"10001";
//echo "新员工ID号:$Number";

//上传图片
if($Photo!=""){

		if($ipadTag == "yes")
		{
			if(is_uploaded_file($_FILES['Photo']['tmp_name']))
			{
				$PreFileName1="P".$Number.".jpg";
				$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName1;
				if(move_uploaded_file($_FILES['Photo']['tmp_name'],$path))
				{
					$uploadInfo1 = 1;
				}
				else
				{
					$uploadInfo1 = "";
				}
			}
		}
		else
		{
			$OldFile1=$Photo;
			$PreFileName1="P".$Number.".jpg";
			$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
		}
	}
	$upValue1=$uploadInfo1==""?0:1;
	if($IdcardPhoto!=""){

		if($ipadTag == "yes")
		{
			if(is_uploaded_file($_FILES['IdcardPhoto']['tmp_name']))
			{
				$PreFileName2="C".$Number.".jpg";
				$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName2;
				if(move_uploaded_file($_FILES['IdcardPhoto']['tmp_name'],$path))
				{
					$uploadInfo2 = 1;
				}
				else
				{
					$uploadInfo2 = "";
				}
			}
		}else{
			$OldFile2=$IdcardPhoto;
			$PreFileName2="C".$Number.".jpg";
			$uploadInfo2=UploadPictures($OldFile2,$PreFileName2,$StaffPhotoPath);
		}
	}
	$upValue2=$uploadInfo2==""?0:1;

	if($HealthPhoto!=""){

		if($ipadTag == "yes")
		{
			if(is_uploaded_file($_FILES['HealthPhoto']['tmp_name']))
			{
				$PreFileName3="H".$Number.".jpg";
				$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName3;
				if(move_uploaded_file($_FILES['HealthPhoto']['tmp_name'],$path))
				{
					$uploadInfo3 = 1;
				}
				else
				{
					$uploadInfo3 = "";
				}
			}
		}
		else
		{
			$OldFile3=$HealthPhoto;
			$PreFileName3="H".$Number.".jpg";
			$uploadInfo3=UploadPictures($OldFile3,$PreFileName3,$StaffPhotoPath);
		}
	}
	$upValue3=$uploadInfo3==""?0:1;

	if($vocationHPhoto!=""){

		if($ipadTag == "yes")
		{
			if(is_uploaded_file($_FILES['vocationHPhoto']['tmp_name']))
			{
				$PreFileName4="V".$Number.".jpg";
				$path = $_SERVER['DOCUMENT_ROOT']."/download/staffPhoto/".$PreFileName4;
				if(move_uploaded_file($_FILES['vocationHPhoto']['tmp_name'],$path))
				{
					$uploadInfo4 = 1;
				}
				else
				{
					$uploadInfo4 = "";
				}
			}
		}
		else
		{
			$OldFile4=$vocationHPhoto;
			$PreFileName4="V".$Number.".jpg";
			$uploadInfo4=UploadPictures($OldFile4,$PreFileName4,$StaffPhotoPath);
		}
	}

	$upValue4=$uploadInfo4==""?0:1;

	$Name=FormatSTR($Name);//去连续空格,去首尾空格
	$Idcard=FormatSTR($Idcard);
	$Address=FormatSTR($Address);
	$Postalcode=FormatSTR($Postalcode);
	$Tel=FormatSTR($Tel);
	$Mobile=FormatSTR($Mobile);
	$Mail=FormatSTR($Mail);
	$Dh=FormatSTR($Dh);
	$Note=FormatSTR($Note);
	$Date=date("Y-m-d");
	//员工等级默认值设置：预设为该职位的最低等级
	$CheckGrade=mysql_query("SELECT Low FROM $DataPublic.gradedata WHERE Id='$JobId' order by Id LIMIT 1",$link_id);
	if($CheckGradeRow = mysql_fetch_array($CheckGrade)){
		$Grade=$CheckGradeRow["Low"];
		}
	else{
		$Grade=0;
		}
	$inRecode="INSERT INTO $DataPublic.staffmain (Id,cSign,WorkAdd,Number,IdNum,Name,Nickname,Grade,KqSign,BranchId,JobId,GroupId,Mail,GroupEmail,AppleID,ExtNo,ComeIn,ContractSDate,ContractEDate,Introducer,FormalSign,AttendanceFloor,Date,Estate,Locks,Operator) VALUES (NULL,'$cSign'," . intval($WorkAdd) . ",'$Number','$IdNum','$Name','$Nickname','$Grade','1','$BranchId','$JobId','$GroupId','$Mail','$GroupEmail','$AppleID','$ExtNo','$ComeIn','0000-00-00','0000-00-00','$Introducer','$FormalSign','$floorAdd','$Date','1','0','$Operator')";

	/*
echo json_encode(array($inRecode));
	exit ();
*/

	$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if($inAction){
	$Log.="&nbsp;&nbsp;1、员工 $Name 的入职资料新增成功! 员工ID号:$Number<br>";
	$LogInfo = $LogInfo."1、员工 $Name 的入职资料新增成功! 员工ID号:$Number.";
	//2-从表加入
	$inSheet="INSERT INTO $DataPublic.staffsheet (Id,Number,Sex,BloodGroup,Nation,Rpr,Education,Married,Birthday,Photo,IdcardPhoto,HealthPhoto,vocationHPhoto,DriverPhoto,Idcard,PassPort,PassTicket,Address,Postalcode,Tel,Mobile,Dh,Weixin,LinkedIn,Bank,Bank2,Bank3,ClothesSize,HouseSize,Note,`InFile`) VALUES (NULL,'$Number','$Sex','$BloodGroup','$Nation','$Rpr','$Education','$Married','$Birthday','$upValue1','$upValue2','$upValue3','$upValue4','0','$Idcard','0','0','$Address','$Postalcode','$Tel','$Mobile','$Dh','$Weixin','$LinkedIn','','','','$ClothesSize','','$Note','0')";
	$inAction2=@mysql_query($inSheet);
	if($inAction2){
		$Log.="&nbsp;&nbsp;&nbsp;&nbsp;2、员工 $Name 的从表信息加入成功!<br>";
		$LogInfo = $LogInfo."2、员工 $Name 的从表信息加入成功!";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;2、员工 $Name 的从表信息加入失败!</div><br>$inSheet</br>";
		$OperationResult="N";
		$LogInfo = $LogInfo."2、员工 $Name 的从表信息加入失败!";
		}

	//3-薪资基础初始化Id/Number/Jxjj/Locks/Date/Operator
	$baseinsql = "INSERT INTO $DataPublic.paybase (Id,Number,Jj,Jtbz,Sbkk,Taxkk,Locks,Date,Operator) VALUES (NULL,'$Number','0','0','0','0','0','$Date','$Operator')";
	$baseinresult = @mysql_query($baseinsql);
	if($baseinresult){
		$Log.="&nbsp;&nbsp;&nbsp;&nbsp;3、薪资初始化成功！<br>";
		$LogInfo = $LogInfo."3、薪资初始化成功！";
		}
	else{
		$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;3、薪资初始化失败! $baseinsql </div><br>";
		$OperationResult="N";
		$LogInfo = $LogInfo."3、薪资初始化失败!";
		}
	//短消息通知
	if($ipadTag != "yes")
	{
		$smsNote="新员工 $Name / $Number 的资料已加入,初始化员工等级: $Grade ;考勤状态:需考勤; 薪资基础的默认奖金:0，请核实。";	//短消息内容
		$smsfunId=3;																//短消息通知编号：权限，经理
		include "../admin/subprogram/tosmsdata.php";
	}
	}
else{
	$Log="<div class=redB>&nbsp;&nbsp;1、员工 $Name 的入职资料新增失败! $inRecode </div><br>";
	$LogInfo = $LogInfo."1、员工 $Name 的入职资料新增失败!";
	$OperationResult="N";
	}

	if($ipadTag == "yes")
	{
		$resultOperation = array($OperationResult, $LogInfo);
		echo json_encode($resultOperation);
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

if($ipadTag != "yes")
{
	include "../model/logpage.php";
}
?>