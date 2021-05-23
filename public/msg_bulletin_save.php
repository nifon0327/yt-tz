<?php 
//步骤1： $DataPublic.msg1_bulletin 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="电子公告";			//需处理
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
$inRecode="INSERT INTO $DataPublic.msg1_bulletin (Id,cSign,Title,Content,Type,Date,Operator) VALUES (NULL,'$cSign','$Title','$Content','$Type','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction && mysql_affected_rows()>0){
	$Log="$TitleSTR 成功.<br>";
	
	//考勤机推送用
	include "../ipdAPI/push_kq.php";
	
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败. $inRecode </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
