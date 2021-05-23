<?php 
//步骤1： $DataPublic.msg3_notice  二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="人事通知";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Content=FormatSTR($Content);
$inRecode="INSERT INTO $DataPublic.msg3_notice (Id,cSign,Content,Date,Operator) VALUES (NULL,'$cSign','$Content','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction && mysql_affected_rows()>0){
	$Log="$TitleSTR 成功.<br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败.</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
