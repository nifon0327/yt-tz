<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="安全生产培训计划";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$ItemName=FormatSTR($ItemName);
$Date=date("Y-m-d");
$IN_recode="INSERT INTO $DataPublic.aqsc07 (Id,DefaultDate,ItemName,ItemTime,Tutorial,Lecturer,Reviewer,TeachId,ExamId,OUId,ObjectId,TypeId,Date,Estate,Locks,Operator) VALUES 
(NULL,'$DefaultDate','$ItemName','$ItemTime','$Tutorial','$Lecturer','$Reviewer','$TeachId','$ExamId','$OUId','$ObjectId','$TypeId','$Date','0','0','$Operator')";
$res=@mysql_query($IN_recode);
if($res){
	$Log="$TitleSTR 成功. <br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败 $IN_recode</div><br>";
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
