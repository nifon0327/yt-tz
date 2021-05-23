<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="员工资料";		//需处理
$upDataSheet="$DataPublic.lw_staffmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$StaffPhotoPath="../download/lw_staffPhoto/";
if(!file_exists($StaffPhotoPath)){
		makedir($StaffPhotoPath);
}
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:$Log_Funtion="锁定";	$SetStr="Locks=0";				
	       include "../admin/subprogram/updated_model_3d.php";		break;
	case 8:$Log_Funtion="解锁";	$SetStr="Locks=1";				
	       include "../admin/subprogram/updated_model_3d.php";		break;
	case 32:$Log_Funtion="离职";	$SetStr="Estate=0,Locks=0";
	        $updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE $upDataSheet.Id='$Id' ";
			$updateResult = mysql_query($updateSQL);
			if ($updateResult && mysql_affected_rows()>0){
			    $Log=$Log."&nbsp;&nbsp;ID号为 $Id 的 劳务工离职成功!<br>";
			    $inRrecode="INSERT INTO $DataPublic.lw_dimissiondata(Number,outDate,Type,Reason,LeaveType,Estate,
			    Locks,Date,Operator,creator,created) SELECT Number,'$theDate','$LeavedType','$Reason','$LeavedType',
			    1, '0','$Date','$Operator', '$Operator', NOW() FROM $DataPublic.lw_staffmain WHERE Id=$Id";
				$res=@mysql_query($inRrecode);
				if($res){
					$Log.="&nbsp;&nbsp;离职资料存档成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;离职资料存档失败.</div><br>";
					$OperationResult="N";
				}
			
			}else{
				$Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 $Id 的 劳务工离职失败! $updateSQL </div><br>";
	            $OperationResult="N";
			}
      break;

	default:
	    if ($Name=="") break;
		$Log_Funtion="更新";
		$FilePath=$StaffPhotoPath;  //../download/staffPhoto/
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		//照片处理
		$PreFileName1="P".$Number.".jpg";
		if($Photo!=""){
		 
				$OldFile1=$Photo;
				$PreFileName1="P".$Number.".jpg";
				$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$StaffPhotoPath);
		}
		
		if($PngPhoto!=""){
		      $PreFileName2="P".$Number.".png";
		      $uploadInfo2=UploadPictures($PngPhoto,$PreFileName2,$StaffPhotoPath);
		}

		$upValue1=$uploadInfo1==""?"":",Photo='1'";		
		if($upValue1=="" && $oldPhoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName1;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue1=",Photo='0'";
				}			
			}
		
		//身份证扫描档处理
		$PreFileName2="C".$Number.".jpg";
		if($IdcardPhoto!="")
		{
			$OldFile2=$IdcardPhoto;
			$PreFileName2="C".$Number.".jpg";
			$uploadInfo2=UploadPictures($OldFile2,$PreFileName2,$StaffPhotoPath);
		}

		$upValue2=$uploadInfo2==""?"":",IdcardPhoto='1'";
		if($upValue2=="" && $oldIphoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName2;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue2=",IdcardPhoto='0'";
				}			
			}
			
		
		$PreFileName4="H".$Number.".jpg";
		if($HealthPhoto!=""){
			$OldFile4=$HealthPhoto;			
			$uploadInfo4=UploadPictures($OldFile4,$PreFileName4,$FilePath);
			}
		$upValue4=$uploadInfo4==""?"":",HealthPhoto='1'";
		if($upValue4=="" && $oldHPhoto==1){//没有上传文件并且已选取删除原文件
			$delFilePath=$FilePath.$PreFileName4;
			if(file_exists($delFilePath)){
				unlink($delFilePath);
				$upValue4=",HealthPhoto='0'";
				}			
			}
		$Name=FormatSTR($Name);//去连续空格,去首尾空格			
		$Idcard=FormatSTR($Idcard);
		$IdNum=FormatSTR($IdNum);
		$Address=FormatSTR($Address);
		$Tel=FormatSTR($Tel);
		$Mobile=FormatSTR($Mobile);
		$eMail=FormatSTR($eMail);
		$Remark=FormatSTR($Remark);
		$mainSql = "UPDATE $upDataSheet SET CompanyId='$CompanyId',WorkAdd='$WorkAdd',Name='$Name',IdNum='$IdNum',
		Nickname='$Nickname',BranchId='$BranchId',GroupId='$GroupId',JobId='$JobId',ComeIn='$ComeIn',KqSign='$KqSign',
		AttendanceFloor='$floorAdd',Remark='$Remark',Date='$Date',Locks=0,Operator='$Operator' WHERE Id='$Id'";
		$mainResult = mysql_query($mainSql);
		if ($mainResult){
			$Log.="&nbsp;&nbsp; $Name 的入职资料更新成功!</br>";
			$sheetSql ="UPDATE $DataPublic.lw_staffsheet SET 
			Sex='$Sex',Nation='$Nation',Rpr='$Rpr',Married='$Married',Birthday='$Birthday',
			Idcard='$Idcard',Address='$Address',Tel='$Tel',Mobile='$Mobile',eMaile='$eMaile',Weixin='$Weixin',
			$upValue1 $upValue2  $upValue4  WHERE Number=$Number";
			$sheetResult = mysql_query($sheetSql);
			if($sheetResult){
				$Log.="&nbsp;&nbsp; $Name 的从表信息更新成功!</br>";
				}
			else{
				$Log.="&nbsp;&nbsp; $Name 的从表信息更新失败! $sheetSql </br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp; $Name 的入职资料更新失败! $mainSql </div></br>";
			$OperationResult="N";
			}
		break;
	}
	

$ALType="&BranchId=$BranchId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>