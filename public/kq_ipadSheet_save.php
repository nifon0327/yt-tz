<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
$Log_Item="考勤ipad";   
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
$inRecode="INSERT INTO $DataPublic.attendanceipadsheet (Id,Name,Floor,cSign,Identifier,Estate,Locks,PLocks, creator, created, modifier, modified,Date,Operator) VALUES (NULL, '$Name', '$Floor', '$cSign', '$Identifier', '1', '0', '0', '$Operator', '$DateTime', '$Operator', '$DateTime', '$DateTime', '$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
if ($inAction){ 
    $Log="$TitleSTR 成功!<br>";
    } 
else{
    $Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
    $OperationResult="N";
    } 
if($Manager!=""){
     $inRecode="INSERT INTO $upDataSheet (Id,BranchId,Manager)values(NULL,$Id,$Manager)";
     $inRes=mysql_query($inRecode); 
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
