<?php 
//代码、数据共享-EWEN 2012-09-17
include "../model/modelhead.php";
//步骤2：
$Log_Item="门禁指令";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Name=FormatSTR($PostValues[0]);
$OpenKeys=$PostValues[1];
$CloseKeys=$PostValues[2];
//$LockSql=" LOCK TABLES $DataPublic.accessguard_order WRITE";$LockRes=@mysql_query($LockSql);
$inRecode="INSERT INTO $DataPublic.accessguard_order (Id,Name,OpenKeys,CloseKeys,Date,Estate,Locks,Operator) VALUES (NULL,'$Name','$OpenKeys','$CloseKeys','$DateTime','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
