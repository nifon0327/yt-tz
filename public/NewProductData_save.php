<?php 
//电信-ZX  2012-08-01
//步骤1：	$DataIn.productdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="产品资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//回传参数
$ALType="From=$From&CompanyId=$CompanyId&ProductType=$ProductType&Pagination=$Pagination";
//$LockSql=" LOCK TABLES $DataIn.newproductdata WRITE";$LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(ProductId) AS Mid FROM $DataIn.newproductdata",$link_id);
$ProductId=mysql_result($maxSql,0,"Mid");
if($ProductId){
	$ProductId=$ProductId+1;}
else{
	$ProductId=10001;}

//上传图片
$FilePath="../download/newproductdata";
if($TestStandard!=""){
	$OldFile=$TestStandard;
	$PreFileName="T".$ProductId.".jpg";
	$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	}
$PictureValue=$uploadInfo==""?0:2;

//
//上传高清图片
$uploadInfo="";

if($Img_H!=""){
	//$FilePath="../download/newproductdata";
	$OldFile=$Img_H;
	$PreFileName="T".$ProductId."_H.jpg";
	$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	}

$Img_HValue=$uploadInfo==""?0:2;

//上传微缩图片
$uploadInfo="";
if($Img_L!=""){
	//$FilePath="../download/newproductdata";
	$OldFile=$Img_L;
	$PreFileName="T".$ProductId."_L.jpg";
	$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	//echo ">>>>>$Img_L:$uploadInfo";
	}
$Img_LValue=$uploadInfo==""?0:2;

$cName=FormatSTR($cName);//去连续空格,去首尾空格
$eCode=FormatSTR($eCode);
$Price=FormatSTR($Price);
$Unit=FormatSTR($Unit);
$Remark=FormatSTR($Remark);
$pRemark=FormatSTR($pRemark);
$Description=FormatSTR($Description);
$Code=Chop(trim($Code));
$DefaultCompanyId=$CompanyId;
$Date=date("Y-m-d");
//$CompanyId=0;
$inRecode="INSERT INTO $DataIn.newproductdata
(Id,ProductId,cName,eCode,TypeId,Price,Unit,Moq,CompanyId,Description,Remark,pRemark,TestStandard,Img_H,Img_L,PackingUnit,Code,Date,Estate,Locks,Operator)
 VALUES (NULL,'$ProductId','$cName','$eCode','$TypeId','$Price','$Unit','0','$CompanyId','$Description','$Remark','$pRemark','$PictureValue','$Img_HValue','$Img_LValue','$PackingUnit','$Code','$Date','2','0','$Operator')"; 
 
 //echo "$inRecode";
$inAction=@mysql_query($inRecode);
if ($inAction){
	//$Log.= "$Img_H:$Img_L <br>";
	$Log.="新增名称为 $cName 的产品资料成功!<br>";
	} 
else{ 
	$Log.="<div class=redB>名称为 $cName 的产品资料新增失败! $inRecode </div><br>";
	$OperationResult="N";
	}
//复制
if($CopyTo==1){
	$Counts=count($_POST["CompanyIdCC"]);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$CompanyId=$_POST["CompanyIdCC"][$i];
		if($CompanyId!=$DefaultCompanyId){
			$ProductId=$ProductId+1;
			$inRecode="INSERT INTO $DataIn.newproductdata
(Id,ProductId,cName,eCode,TypeId,Price,Unit,Moq,CompanyId,Description,Remark,pRemark,TestStandard,Img_H,Img_L,PackingUnit,Code,Date,Estate,Locks,Operator)
 VALUES (NULL,'$ProductId','$cName','$eCode','$TypeId','$Price','$Unit','0','$CompanyId','$Description','$Remark','$pRemark','$PictureValue','$Img_HValue','$Img_LValue','$PackingUnit','$Code','$Date','1','0','$Operator')";

			$inAction=@mysql_query($inRecode);
			if ($inAction){ 
				$Log.="产品 $cName 的资料复制至客户 $CompanyId 成功!<br>";
				} 
			else{ 
				$Log.="<div class=redB>产品 $cName 的资料复制至客户 $CompanyId 失败!</div><br>";
				$OperationResult="N";
				}
			}
		}
	}
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
