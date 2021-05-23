<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="QC不良原因";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="Type=$TypeId";
$Title=FormatSTR($Title);
$Date=date("Y-m-d");
if($Attached!=""){//有上传文件
	$FileType=substr("$Attached_name", -4, 4);
	$OldFile=$Attached;
	$FilePath="../download/qccause/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$PreFileName="Type_".$TypeId. $FileType;
	$ImageFile=$FilePath . $PreFileName;
	if (file_exists($ImageFile)){
	    $n=0;
	   do{
			$n+=1;
			$PreFileName="Type".$TypeId. "_" . $n . $FileType;
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
  }
		$Cause=FormatSTR($Cause);
		$Date=date("Y-m-d");
		$IN_recode="INSERT INTO $DataIn.qc_causetype (Id, Cause, Type, Picture, Estate, Date, Operator) VALUES (NULL,'$Cause','$TypeId','$PreFileName','1','$Date','$Operator')";
		$res=@mysql_query($IN_recode);
		if($res){
			$Log="$TitleSTR 成功. <br>";
			}
		else{
			$Log="<div class='redB'>$TitleSTR 失败. $IN_recode </div><br>";
                        $OperationResult="N";
			}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
