<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="预结付取款记录";			//需处理
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
$Content=FormatSTR($Content);
$TypeIdArray=explode("|",$TypeId);
$TypeId=$TypeIdArray[0];
$inRecode=$DataIn !== 'ac' ? "INSERT INTO $DataIn.cw_advanced SELECT NULL,Number,'$BankId','$Amount','$Currency','$Remark','$DateTime','1','0','$Operator' FROM $DataPublic.staffmain WHERE Name='$Name'" : 
                             "INSERT INTO $DataIn.cw_advanced SELECT NULL,Number,'$BankId','$Amount','$Currency','$Remark','$DateTime','1','0','$Operator',0,'$Operator','$DateTime','$Operator','$DateTime' FROM $DataPublic.staffmain WHERE Name='$Name'";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
