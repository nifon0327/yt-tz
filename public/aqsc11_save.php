<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="安全生产证书";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

if($Attached!=""){//有上传文件
	$FileType=substr("$Attached_name", -4, 4);
	$OldFile=$Attached;
	$FilePath="../download/aqsc/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$datelist=newGetDateSTR();
	$PreFileName="11_".$datelist.$FileType;
	$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	if ($Attached!=""){//附件上传成功	
		$Caption=FormatSTR($Caption);
		$IN_recode="INSERT INTO $DataPublic.aqsc11 (Id,TypeId,Caption,Attached,EndDate,Date,Estate,Locks,Operator) VALUE (NULL,'$TypeId','$Caption','$Attached','$EndDate','$DateTime','1','0','$Operator')";
		$res=@mysql_query($IN_recode);
		if($res){
			$Log="$TitleSTR 成功. <br>";
			}
		else{
			$Log="<div class='redB'>$TitleSTR 失败(或更新无变化).$IN_recode</div><br>";
			$OperationResult="N";
			//删除附件
			$FilePath="../download/aqsc/".$PreFileName;
			if(file_exists($FilePath)){
				unlink($FilePath);
				}
			}
		}
	else{//附件上传失败
		$Log="<div class='redB'>附件上传失败！</div><br>";
		$OperationResult="N";
		}
	}
else{//没有上传 文件
	$Log="<div class='redB'>没有附件!</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
