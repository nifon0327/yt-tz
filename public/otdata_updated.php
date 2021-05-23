<?php 
//电信-joseph
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="加工文档";		//需处理
$upDataSheet="$DataPublic.otdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
	if ($ShowCompany=="on") {  //表示修改客户信息
	  $sheetInSql="UPDATE $DataPublic.otdata_kfinfo SET  Name='$NewName',Tel='$NewTel',Fax='$NewFax',Address='$NewAddress',Remark ='$NewRemark' WHERE Id='$CompanyId'";
	$sheetInAction=@mysql_query($sheetInSql);
	if($sheetInAction && mysql_affected_rows()>0){
		$Log.="    修改客户信息成功.<br>";
	 }
	 else{
		$Log.="<div class='redB'>修改客户信息失败</div><br>";
	}
   }
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$QueryFile=$Attached;
			$FilePath="../download/otfile/doc/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($Ohycfile!=""){
				$PreFileName=$Ohycfile;
				}
			else{
				$datelist=newGetDateSTR();
				$PreFileName=$datelist.$FileType;
				}
			$upAttached=UploadPictures($QueryFile,$PreFileName,$FilePath);
			if($upAttached!=""){
				$AttachedSTR=",FileName='$PreFileName'";
				}
			}
		 if($upImageFile!=""){//有上传图片文件
			$FileType=substr("$upImageFile_name", -4, 4);
			$QueryFile=$upImageFile;
			$FilePath="../download/otfile/image/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($ImageFile!=""){
				$PreFileName=$ImageFile;
				}
			else{
				$datelist=newGetDateSTR();
				$PreFileName=$datelist.$FileType;
				}
			$upImage=UploadPictures($QueryFile,$PreFileName,$FilePath);
			if($upImage!=""){
				$AttachedSTR=",ImageFlag='1',ImageName='$PreFileName'";
				}
			}
		$Date=date("Y-m-d");
		$Name=FormatSTR($Name);
		$SetStr="Name='$Name',TypeId='$TypeId',Date='$Date',CompanyId='$CompanyId',Operator='$Operator',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
