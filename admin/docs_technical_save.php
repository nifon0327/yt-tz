<?php   
//步骤1： 
include "../model/modelhead.php";
//步骤2：
$Log_Item="技术维护信息";			//需处理
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
$Title=FormatSTR($Title);
$inRecode="INSERT INTO $DataPublic.doc1_technical (Id,Title,Content,Type,Date,Operator) VALUES (NULL,'$Title','$Content','$Type','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction && mysql_affected_rows()>0){
	$Log="$TitleSTR 成功.<br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败. $inRecode </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
