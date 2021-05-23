<?php   
//电信-zxq 2012-08-01
//步骤1： $DataIn.yw4_quotationsheet 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="报价记录";			//需处理
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
//步骤3：需处理
$YearTemp=date("Y");
//编号计算
//$LockSql=" LOCK TABLES $DataIn.yw4_quotationsheet WRITE";$LockRes=@mysql_query($LockSql);
$checkMax=mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.yw4_quotationsheet WHERE $YearTemp=DATE_FORMAT(Date,'%Y')",$link_id);
$Number=mysql_result($checkMax,0,"Number");
if($Number){
	$Number=$Number+1;
	}
else{
	$Number=$YearTemp."0001";
	}
$FilePath="../download/quotation/";
if(!file_exists($FilePath)){
	makedir($FilePath);
	}
$inRecode="INSERT INTO $DataIn.yw4_quotationsheet 
(Id,Number,ProductCode,CompanyId,Currency,Price,Rate,Moq,Priceterm,Paymentterm,Leadtime,Remark,Image1,Image2,Image3,Sales,ApprovedBy,Model,Date,Locks,Operator) VALUES 
(NULL,'$Number','$ProductCode','$thisCompanyId','$Currency','$Price','$Rate','$Moq','$Priceterm','$Paymentterm','$Leadtime','$Remark','1','1','1','$Sales','$ApprovedBy','$Model','$Date','0','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
if ($inAction && mysql_affected_rows()>0){ 
	$Log="$TitleSTR 成功!<br>";
	//上传文件
	if($Image1!=""){
		$PreFileName1=$Number."-01.jpg";
		$OldFile1=$Image1;
		$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);				
		}$Image1STR=$uploadInfo1==""?",Image1='0'":",Image1='1'";
	if($Image2!=""){
		$PreFileName2=$Number."-02.jpg";
		$OldFile2=$Image2;
		$uploadInfo2=UploadFiles($OldFile2,$PreFileName2,$FilePath);				
		}$Image2STR=$uploadInfo2==""?",Image2='0'":",Image2='1'";
	if($Image3!=""){
		$PreFileName3=$Number."-03.jpg";
		$OldFile3=$Image3;
		$uploadInfo3=UploadFiles($OldFile3,$PreFileName3,$FilePath);				
		}$Image3STR=$uploadInfo3==""?",Image3='0'":",Image3='1'";
	//更新
	$upSql="UPDATE $DataIn.yw4_quotationsheet SET Locks='0' $Image1STR $Image2STR $Image3STR WHERE Number='$Number' LIMIT 1";
	$Result = mysql_query($upSql);
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if($OperationResult=="Y"){
	//生成PDF
	include "yw_quotation_topdf.php";
	}
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
