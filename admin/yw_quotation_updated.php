<?php   
//电信-zxq 2012-08-01
//$DataIn.yw4_quotationsheet 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//2
$Log_Item="Quotation Sheet";
$upDataSheet="$DataIn.yw4_quotationsheet";
$Log_Funtion="UPDATE";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
$FilePath="../download/quotation/";
switch($ActionId){
	case 7:
		$Log_Funtion="Lock";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="unLock";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		//上传文件
		$PreFileName1=$Number."-01.jpg";
		if($Image1!=""){
			$OldFile1=$Image1;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);				
			$Image1STR=$uploadInfo1==""?",Image1='0'":",Image1='1'";
			}
		if($Image1STR=="" && $oldImage1==1){
			$Image1Path=$FilePath.$PreFileName1;
			if(file_exists($Image1Path)){
				unlink($Image1Path);
				}
			$Image1STR=",Image1='0'";
			}
		$PreFileName2=$Number."-02.jpg";
		if($Image2!=""){
			$OldFile2=$Image2;
			$uploadInfo2=UploadFiles($OldFile2,$PreFileName2,$FilePath);				
			$Image2STR=$uploadInfo2==""?",Image2='0'":",Image2='1'";
			}
		if($Image2STR=="" && $oldImage2==1){
			$Image2Path=$FilePath.$PreFileName2;
			if(file_exists($Image2Path)){
				unlink($Image2Path);
				}
			$Image2STR=",Image2='0'";
			}
		$PreFileName3=$Number."-03.jpg";
		if($Image3!=""){
			$OldFile3=$Image3;
			$uploadInfo3=UploadFiles($OldFile3,$PreFileName3,$FilePath);				
			$Image3STR=$uploadInfo3==""?",Image3='0'":",Image3='1'";
			}
		if($Image3STR=="" && $oldImage3==1){
			$Image3Path=$FilePath.$PreFileName3;
			if(file_exists($Image3Path)){
				unlink($Image3Path);
				}
			$Image3STR=",Image3='0'";
			}
		$SetStr="ProductCode='$ProductCode',CompanyId='$thisCompanyId',Currency='$Currency',Price='$Price',Rate='$Rate',Moq='$Moq',Priceterm='$Priceterm',Paymentterm='$Paymentterm',Leadtime='$Leadtime',Remark='$Remark',Sales='$Sales',ApprovedBy='$ApprovedBy',Model='$Model',Date='$Date',Locks='0' $Image1STR $Image2STR $Image3STR";
		include "../model/subprogram/updated_model_3a.php";
		include "yw_quotation_topdf.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>