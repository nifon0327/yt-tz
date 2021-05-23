<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="安全管理制度文档";			//需处理
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

//分类处理
$checkSql = "SELECT A.Id AS TypeId FROM $DataPublic.aqsc01 A 
					   LEFT JOIN $DataPublic.aqsc01 B ON B.PreItem=A.Id
					   WHERE A.Name='$Name' AND B.Id IS NULL";
$checkResult=mysql_query($checkSql,$link_id);
if($checkRow=mysql_fetch_array($checkResult)){
	$TypeId=$checkRow["TypeId"];
	if($Attached!=""){//有上传文件
		$FileType=substr("$Attached_name", -4, 4);
		$OldFile=$Attached;
		$FilePath="../download/aqsc/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$datelist=newGetDateSTR();
		$PreFileName=$datelist.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if ($Attached!=""){		
			$Caption=FormatSTR($Caption);
			$IN_recode="INSERT INTO $DataPublic.aqsc02 (Id,Caption,Attached,TypeId,Date,Estate,Locks,Operator) VALUE (NULL,'$Caption','$PreFileName','$TypeId','$DateTime','1','0','$Operator')";
			$res=@mysql_query($IN_recode);
			if($res){
				$Log="$TitleSTR 成功. <br>";
				}
			else{
				$Log="<div class='redB'>$TitleSTR 失败(或更新无变化).</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log="<div class='redB'>附件上传失败！</div><br>";
			$OperationResult="N";
			}
		}
	}
else{//分类不适合
	$Log="<div class='redB'>分类错误！$checkSql</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
