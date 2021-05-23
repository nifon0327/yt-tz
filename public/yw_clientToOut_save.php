<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="客户出货指定转发对象";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$ToOutName=FormatSTR($ToOutName);
$Remark=FormatSTR($Remark);
$Date=date("Y-m-d");
$IN_recode="INSERT INTO $DataIn.yw7_clientToOut (Id,CompanyId,ToOutName,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$CompanyId','$ToOutName','$Remark','1','0','$DateTime','$Operator')";
$res=@mysql_query($IN_recode);
if($res){
	$Log="$TitleSTR 成功. <br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败(或更新无变化).</div><br>";
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
