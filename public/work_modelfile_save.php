<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="模板文件";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
if($Attached!=""){
	//有上传文件
	$FileType=substr("$Attached_name", -2, 2);
	$OldFile=$Attached;
	$FileDir="modelfile";
	$FilePath="../download/modelfile/";
	if(!file_exists($FilePath)){//目录不存在则先创建目录
			makedir($FilePath);
			}
	$DateList=newGetDateSTR();
	$PreFileName=$DateList.".".$FileType;	//上传后文件名
	$Attached=UploadPictures($OldFile,$PreFileName,$FilePath);
	//$LockSql=" LOCK TABLES $DataPublic.workmodelfile WRITE";$LockRes=@mysql_query($LockSql);
	$Note=FormatSTR($Note);
	$IN_recode="INSERT INTO $DataPublic.workmodelfile (Id,Note,Attached,Date,Estate,Locks,Operator) VALUES (NULL,'$Note','$Attached','$DateTime','1','0','$Operator')";
	$res=@mysql_query($IN_recode);
	////////////////////////////////////////////
	if($res){
		$Log="$TitleSTR 成功！$IN_recode <br>";
		}
	else{
		$Log="<div class='redB'> $TitleSTR 失败！</div><br>";
		}
	////////////////////////////////////////////
	//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
	}
else{
	$Log="<div class='redB'>没有选取上传的模板，新增 $Log_Item 失败！</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
