<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
$Log_Item="门禁设备";			//需处理
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
$DoorName=FormatSTR($PostValues[0]);
$DoorAdd=$PostValues[1];
$IP=$PostValues[2];
$OrderId=$PostValues[3];
//$LockSql=" LOCK TABLES $DataPublic.accessguard_door WRITE";$LockRes=@mysql_query($LockSql);
$inRecode="INSERT INTO $DataPublic.accessguard_door (Id,DoorName,DoorAdd,IP,OrderId,Date,Estate,Locks,Operator) VALUES (NULL,'$DoorName','$DoorAdd','$IP','$OrderId','$DateTime','1','0','$Operator')";
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
