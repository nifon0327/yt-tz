<?php 
//电信-ZX  2012-08-01
//步骤1：$DataIn.errorcasedata 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="REACH法规图";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$Title=FormatSTR($Title);
$Date=date("Y-m-d");
if($Attached!=""){//有上传文件
	$FileType=substr("$Attached_name", -4, 4);
	$OldFile=$Attached;
	$FilePath="../download/stuffreach/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$PreFileName="R".$TypeId. $FileType;
	$ImageFile=$FilePath . $PreFileName;
	if (file_exists($ImageFile)){
	    $n=0;
	   do{
			$n+=1;
			$PreFileName="R".$TypeId. "_" . $n . $FileType;
		    $ImageFile=$FilePath . $PreFileName;
		}while(file_exists($ImageFile));
	}
	$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	if ($Attached!=""){		
		$Log="附件上传成功.<br>";
		}
	else{
		$Log="<div class='redB'>附件上传失败！</div><br>";
		$OperationResult="N";
		}
	if($OperationResult=="Y"){
		if ($IsType){$IsType=1;}else{$IsType=0;}
		$Caption=FormatSTR($Caption);
		$Date=date("Y-m-d");
		$IN_recode="INSERT INTO $DataIn.stuffreach (Id,TypeId,Title,Picture,IsType,Estate,Date,Locks,Operator) VALUES (NULL,'$TypeId','$Title','$PreFileName','$IsType','1','$Date','0','$Operator')";
		$res=@mysql_query($IN_recode);
		if($res){
			$Log="$TitleSTR 成功. <br>";
			}
		else{
			$Log="<div class='redB'>$TitleSTR 失败. $IN_recode </div><br>";
			}
		}
	}
else{
	$Log="<div class='redB'>未选择上传的附件.</div>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
