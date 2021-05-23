<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="客户授权书";			//需处理
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
if($Attached!=""){//有上传文件
	$FileType=substr("$Attached_name", -4, 4);
	$OldFile=$Attached;
	$FilePath="../download/clientproxy/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$datelist=newGetDateSTR();
	$PreFileName=$datelist.$FileType;
	$UpFileName=UploadFiles($OldFile,$PreFileName,$FilePath);
	if ($UpFileName!=""){		
		$Log="附件上传成功.<br>";
		}
	else{
		$Log="<div class='redB'>附件上传失败！</div><br>";
		$OperationResult="N";
		}
	}
$TimeLimit=$TimeLimit==""?"0000-00-00":$TimeLimit;
$Caption=FormatSTR($Caption);
$Date=date("Y-m-d");
$IN_recode="INSERT INTO $DataIn.yw7_clientproxy (Id,CompanyId,Caption,TimeLimit,Attached,Estate,Locks,Date,Operator) VALUES (NULL,'$CompanyId','$Caption','$TimeLimit','$UpFileName','1','0','$DateTime','$Operator')";
$res=@mysql_query($IN_recode);
if($res){
	$Log="$TitleSTR 成功. <br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败(或更新无变化).</div><br>";
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
