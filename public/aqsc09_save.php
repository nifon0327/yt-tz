<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工安全生产知识考核";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//读取员工编号
$checStaffSql=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE Estate='1' AND cSign='$Login_cSign' AND Name='$Name' ",$link_id);
if($checkStaffRow=mysql_fetch_array($checStaffSql)){
	$Number=$checkStaffRow["Number"];
	//上传文件
	$PreFileName="";
	if($Attached!=""){
		$FileType=substr("$Attached_name", -4, 4);
		$OldFile=$Attached;
		$FilePath="../download/aqsc/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$datelist=newGetDateSTR();
		$PreFileName="9_".$datelist.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if ($Attached!=""){		
			$Log="附件上传成功.<br>";
			}
		else{
			$Log="<div class='redB'>附件上传失败！</div><br>";
			$OperationResult="N";
			}
		}	
	$Opinion=FormatSTR($Opinion);
	$Date=date("Y-m-d");
	$IN_recode="INSERT INTO $DataPublic.aqsc09 (Id,ExamDate,TypeId,ExamContent,Attached,Number,Results,Checker,Opinion,Date,Estate,Locks,Operator) VALUES (NULL,'$ExamDate','1','$ExamContent','$Attached','$Number','$Results','$Checker','$Opinion','$Date','1','0','$Operator')";
	$res=@mysql_query($IN_recode);
	if($res){
		$Log="$TitleSTR 成功. <br>";
		}
	else{
		$Log="<div class='redB'>$TitleSTR 失败(或记录已存在).</div><br>";
		}
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败(找不到员工资料).</div><br>";
	}
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
