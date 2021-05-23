<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="部门资料";       //需处理
$upDataSheet="$DataPublic.branchdata";  //需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作

$updateSql = "UPDATE $DataPublic.attendanceipadsheet Set Name='$Name', Floor='$Floor', cSign='$cSign', Identifier='$Identifier' Where Id='$Id'";
$UpResult=mysql_query($updateSql);
        if($UpResult && mysql_affected_rows()>0){
            $Log="更新成功. $UpSql";
            }
        else{
            $Log="<div class='redB'>更新失败. $UpSql </div>";
            $OperationResult="N";
            }       


$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>