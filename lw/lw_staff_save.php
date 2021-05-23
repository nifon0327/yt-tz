<?php 
include "../model/modelhead.php";
$Log_Item="劳务工资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&cSign=$cSign";
//如果有照片，则检查上传的照片类型
$FileType=".jpg";
$StaffPhotoPath="../download/lw_staffPhoto/";
if(!file_exists($StaffPhotoPath)){
		makedir($StaffPhotoPath);
}

$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.lw_staffmain ORDER BY Number DESC",$link_id));
$MaxNumber=$checkNumRow["Number"];
$Number=$MaxNumber>1?$MaxNumber+1:"70001";
//echo "新员工ID号:$Number";

	//上传图片
	if($Photo!=""){
		$OldFile1=$Photo;
		$PreFileName1="P".$Number.".jpg";
		$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
	}
	$upValue1=$uploadInfo1==""?0:1;
	if($IdcardPhoto!=""){
		$OldFile2=$IdcardPhoto;
		$PreFileName2="C".$Number.".jpg";
		$uploadInfo2=UploadPictures($OldFile2,$PreFileName2,$StaffPhotoPath);
	}
	$upValue2=$uploadInfo2==""?0:1;
	
	if($HealthPhoto!=""){
			$OldFile3=$HealthPhoto;
			$PreFileName3="H".$Number.".jpg";
			$uploadInfo3=UploadPictures($OldFile3,$PreFileName3,$StaffPhotoPath);
	}
	$upValue3=$uploadInfo3==""?0:1;
	$Name=FormatSTR($Name);//去连续空格,去首尾空格			
	$Idcard=FormatSTR($Idcard);
	$Address=FormatSTR($Address);
	$Tel=FormatSTR($Tel);
	$Mobile=FormatSTR($Mobile);
	$eMail=FormatSTR($eMail);	
	$Date=date("Y-m-d");
	$Education = $Education==""?0:$Education;
	$inRecode="INSERT INTO $DataIn.lw_staffmain (Id,CompanyId,WorkAdd,Number,IdNum,Name,Nickname,KqSign,BranchId,JobId,GroupId,
	ComeIn,AttendanceFloor,Remark,Date,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$WorkAdd','$Number','$IdNum','$Name',
	'$Nickname','$KqSign','$BranchId','$JobId','$GroupId','$ComeIn','$floorAdd','$Remark','$Date','1','0','$Operator')";
	
	$inAction=@mysql_query($inRecode);
	if($inAction){	
		$Log.="&nbsp;&nbsp;1、劳务员工 $Name 的入职资料新增成功! 员工ID号:$Number<br>";
		//2-从表加入
		$inSheet="INSERT INTO $DataIn.lw_staffsheet (Id,Number,Sex,Nation,Rpr,Education,Married,Birthday,Photo,IdcardPhoto,
		HealthPhoto,Idcard,Address,Tel,eMail,Mobile,Weixin,Bank,Estate,Locks,Date,Operator) VALUES 
		(NULL,'$Number','$Sex','$Nation','$Rpr','$Education','$Married','$Birthday','$upValue1','$upValue2','$upValue3',
		'$Idcard','$Address','$Tel','$eMail','$Mobile','$Weixin','','1','0','$Date','$Operator')";
		$inAction2=@mysql_query($inSheet);
		if($inAction2){
			$Log.="&nbsp;&nbsp;&nbsp;&nbsp;2、劳务员工 $Name 的从表信息加入成功!<br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;2、劳务员工 $Name 的从表信息加入失败!</div><br>$inSheet</br>";
			$OperationResult="N";
			}	
		}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;1、劳务员工 $Name 的入职资料新增失败! $inRecode </div><br>";
		$OperationResult="N";
		}
	

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>