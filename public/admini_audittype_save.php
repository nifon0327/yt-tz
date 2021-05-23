<?php 
//代码共享、数据库共享-EWEN 2012-11-02
include "../model/modelhead.php";
$Log_Item="行政审核分类";	
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
$Name=FormatSTR($Name);
$inRecode= $DataIn !== 'ac' ? "INSERT INTO $DataPublic.admini_audittype SELECT NULL,'$Name',Number,'1','0','$DateTime','$Operator' FROM $Public.staffmain WHERE Name='$StaffName' AND Estate=1 LIMIT 1" : 
                              "INSERT INTO $DataPublic.admini_audittype SELECT NULL,'$Name',Number,'1','0','$DateTime','$Operator', 0, '$Operator', NOW(), '$Operator', NOW() FROM $Public.staffmain WHERE Name='$StaffName' AND Estate=1 LIMIT 1";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
