<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="助学小孩信息记录";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

$inRecode="INSERT INTO $DataPublic.childinfo (Id, Number, ChildName, Sex, Amount, Remark, StartSchool, Date, Estate, Locks, Operator) 
VALUES (NULL,'$Number','$ChildName','$Sex','$Amount', '$Remark','$StartSchool','$Date','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
	$Id=mysql_insert_id();
	/*if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/childinfo/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="C".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;凭证上传成功！$inRecode <br>";
			$Attached=1;
			//更新刚才的记录
			$sql = "UPDATE $DataPublic.childinfo SET Attached='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;凭证上传失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}*/
	}
else{
	$Log.="<div class=redB>&nbsp;&nbsp; $TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
