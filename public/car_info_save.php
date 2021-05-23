<?php
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="车辆新增记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$BuyDate = $BuyDate==''?'0000-00-00':$BuyDate;
$InsuranceDate = $InsuranceDate==''?'0000-00-00':$InsuranceDate;
//步骤3：需处理
$inRecode="INSERT INTO $DataPublic.cardata (Id,OldId,cSign,TypeId,BrandId,CarListNo,CarNo,BuyDate,BuyAddress,BuyContact, User,StoreNum,BuyStore,Maintainer,DriveLic,Enrollment,Insurance,YueTong,OilCard,CheckTime,InsuranceDate,
Estate,Locks,Date,Operator)values 
(NULL,'0','0','$TypeId','$BrandId','$CarListNo','$CarNo','$BuyDate','$BuyAddress' ,'$BuyContact','$User','$StoreNum','$BuyStore', '$Maintainer','','','','','',
 '0000-00-00' ,'$InsuranceDate','1','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log="$TitleSTR 成功! $inRecode<br>";
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
			$OilCardResult = mysql_query($OilCardSql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;加油上传失败！ </div><br>";
			$OperationResult="N";
			}
		}
	}
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>