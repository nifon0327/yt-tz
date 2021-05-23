<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车辆记录";		//需处理
$upDataSheet="$DataPublic.cardata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		$EstateStr=" AND Estate<2"; include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		$EstateStr=" AND Estate<2"; include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$SetStr=
        "TypeId='$TypeId',BrandId='$BrandId',UserSign='$UserSign',CarListNo='$CarListNo',CarNo='$CarNo',Maintainer='$Maintainer',User='$User',
        BuyStore='$BuyStore',StoreNum='$StoreNum',InsuranceDate='$InsuranceDate',CheckTime='0000-00-00',BuyDate='$BuyDate',
         BuyAddress='$BuyAddress',BuyContact='$BuyContact',Date='$DateTime',Operator='$Operator'";
	//文件上传
	$FileType=".jpg";
	$FilePath="../download/cardata/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
	}
	
	if($DriveLic!=""){//行驶证上传
		$OldDrive=$DriveLic;
		$DriveLic="DriveLic_".$CarListNo.$FileType;
		$DriveLic=UploadFiles($OldDrive,$DriveLic,$FilePath);
		if($DriveLic){
			$Log.="&nbsp;&nbsp;行驶证上传成功! <br>";
			//更新刚才的记录
			$DriveLicSql = "UPDATE $DataPublic.cardata SET DriveLic='$DriveLic' WHERE CarNo='$CarNo'";
			$DriveLicResult = mysql_query($DriveLicSql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;行驶证上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
		
	if($Enrollment!=""){//登记证书上传
		$OldEnrollment=$Enrollment;
		$Enrollment="Enrollment_".$CarListNo.$FileType;
		$Enrollment=UploadFiles($OldEnrollment,$Enrollment,$FilePath);
		if($Enrollment){
			$Log.="&nbsp;&nbsp;登记证书上传成功！<br>";
			//更新刚才的记录
			$EnrollmentSql = "UPDATE $DataPublic.cardata SET Enrollment='$Enrollment' WHERE CarNo='$CarNo'";
			$EnrollmentResult = mysql_query($EnrollmentSql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;行驶证上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
		
	if($Insurance!=""){//保险单上传
		$OldInsurance=$Insurance;
		$Insurance="Insurance_".$CarListNo.$FileType;
		$Insurance=UploadFiles($OldInsurance,$Insurance,$FilePath);
		if($Insurance){
			$Log.="&nbsp;&nbsp;保险单上传成功！<br>";
			//更新刚才的记录
			$InsuranceSql = "UPDATE $DataPublic.cardata SET Insurance='$Insurance' WHERE CarNo='$CarNo'";
			$InsuranceResult = mysql_query($InsuranceSql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;保险单上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
		
		if($YueTong!=""){//粤通上传
		$OldYueTong=$YueTong;
		$YueTong="YueTong_".$CarListNo.$FileType;
		$YueTong=UploadFiles($OldYueTong,$YueTong,$FilePath);
		if($YueTong){
			$Log.="&nbsp;&nbsp;保险单上传成功！<br>";
			//更新刚才的记录
			$YueTongSql = "UPDATE $DataPublic.cardata SET YueTong='$YueTong' WHERE CarNo='$CarNo'";
			$YueTongResult = mysql_query($YueTongSql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;保险单上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
		
		if($OilCard!=""){//加油上传
		$OldOilCard=$OilCard;
		$OilCard="OilCard_".$CarListNo.$FileType;
		$OilCard=UploadFiles($OldOilCard,$OilCard,$FilePath);
		if($OilCard){
			$Log.="&nbsp;&nbsp;加油上传成功！<br>";
			//更新刚才的记录
			$OilCardSql = "UPDATE $DataPublic.cardata SET OilCard='$OilCard' WHERE CarNo='$CarNo'";
			//echo "$OilCardSql";
			$OilCardResult = mysql_query($OilCardSql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;加油上传上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}

	$updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE $upDataSheet.Id='$Id' $OtherWhere";
	$updateResult = mysql_query($updateSQL);
	if ($updateResult){
		$Log=$Log."&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 成功!<br>";
	}
	else{
		$Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 失败! $updateSQL </div><br>";
		$OperationResult="N";
	break;
	}
}
//include "../model/subprogram/updated_model_3a.php";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>