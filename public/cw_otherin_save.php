<?php 
include "../model/modelhead.php";
$Log_Item="其它收入";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
//$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";//步骤3：需处理
$Remark=FormatSTR($Remark);
$inRecode="INSERT INTO $DataIn.cw4_otherin (Id,Mid,BankId,TypeId,Amount,Currency,Bill,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'0','0','$TypeId','$Amount','$Currency','0','$Remark','1','0','$getDate','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction && mysql_affected_rows()>0){ 
	$Log="其它收入记录添加成功.<br>";
	$Id=mysql_insert_id();
	//上传文件
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/otherin/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="O".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;凭证上传成功! <br>";
			$Attached=1;
			//更新刚才的记录
			$sql = "UPDATE $DataIn.cw4_otherin SET Bill='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;凭证上传失败! </div><br>";
			$OperationResult="N";			
			}
		}
	}
else{
	$Log="<div class='redB'>其它收入记录添加失败. $inRecode </div><br>";
	$OperationResult="N";
	}
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
