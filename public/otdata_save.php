<?php 
//步骤1：$DataPublic.otdata 二合一已更新
//电信-joseph
include "../model/modelhead.php";
//步骤2：
$Log_Item="开发文档";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$PreFileName="";
$Log="";
 if ($ShowCompany=="on") {  //表示直接输入客户信息
	$sheetInSql="INSERT INTO $DataPublic.otdata_kfinfo (Id,ListName,Company,Name,Tel,Fax,Address,Remark) VALUES (NULL,'$ListCompany','$NewCompany','$NewName','$NewTel','$NewFax','$NewAddress','$NewRemark')";
	$sheetInAction=@mysql_query($sheetInSql);
	$CompanyId=@mysql_insert_id(); 
	if($sheetInAction && mysql_affected_rows()>0){
		$Log.="加入客户信息成功.<br>";
	 }
	 else{
		$CompanyId="1";
		$Log.="<div class='redB'>加入客户信息失败,客户ID默认为[1]</div><br>";
		}
  }
if($Attached!=""){//有上传文件
	$FileType=substr("$Attached_name", -4, 4);
	$Ohycfile=$Attached;
	$FilePath="../download/otfile/doc/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$datelist=newGetDateSTR();
	$PreFileName=$datelist.$FileType;
	$Attached=UploadFiles($Ohycfile,$PreFileName,$FilePath);
	if ($Attached!=""){		
		$Log.="文档附件上传成功.<br>";
		}
	else{
		$Log.="<div class='redB'>文档附件上传失败！</div><br>";
		$OperationResult="N";
		}
	}
$ImageFlag=0;
if($upImageFile!=""){//上传图片文件
	$FileType=substr("$upImageFile_name", -4, 4);
	$Ohycfile=$upImageFile;
	$FilePath="../download/otfile/Image/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$datelist=newGetDateSTR();
	$ImageFileName=$datelist.$FileType;
	$upImageFile=UploadFiles($Ohycfile,$ImageFileName,$FilePath);
	if ($upImageFile!=""){		
		$Log.="图片附件上传成功.<br>";
		$ImageFlag=1;
		}
	else{
		$Log.="<div class='redB'>图片附件上传失败！</div><br>";
		$OperationResult="N";
		$ImageFlag=0;
		}
	}	
$Caption=FormatSTR($Caption);
$Date=date("Y-m-d");
$IN_recode="INSERT INTO $DataPublic.otdata (Id,Name,TypeId,FileName,ImageFlag,ImageName,CompanyId,Estate,Locks,Date,Operator) VALUES (NULL,'$Name','$TypeId','$PreFileName','$ImageFlag','$ImageFileName','$CompanyId','1','0','$Date','$Operator')";
$res=@mysql_query($IN_recode);
if($res){
	$Log.="$TitleSTR 成功. <br>";
	}
else{
	$Log.="<div class='redB'>$TitleSTR 失败(或更新无变化).</div><br>";
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
