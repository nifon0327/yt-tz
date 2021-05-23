<?php 
include "../model/modelhead.php";
$Log_Item="长期股权投资记录";			//需处理
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
$inRecode="INSERT INTO $DataIn.cw22_investmentsheet (Id,Mid,Company,InvestName,Amount,Remark,Attached,Date,Estate,Locks,Operator) 
VALUES (NULL,'0','$Company','$InvestName', '$Amount','$Remark','0','$Date','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
	$Id=mysql_insert_id();
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/investment/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="C".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！<br>";
			$sql = "UPDATE $DataIn.cw22_investmentsheet SET Attached='$Attached' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$sql</div><br>";
			$OperationResult="N";			
			}
		}
	}
else{
	$Log.="<div class=redB>&nbsp;&nbsp; $TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
