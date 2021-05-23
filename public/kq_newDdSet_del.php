<?php
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="上班日期对调";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
for($i=0;$i<count($checkid);$i++){
    $Id=$checkid[$i];
    if ($Id!=""){
        $Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
        }
    }


$deleteDdSql = "DELETE FROM $DataIn.kq_rqddnew WHERE Id in ($Ids)";
if(mysql_query($deleteDdSql) && mysql_affected_rows()>0){
    $Log.="&nbsp;&nbsp;记录删除成功!</br>";            
    }
else{
    $Log.="<div class='redB'>&nbsp;&nbsp;记录删除失败! $DelSql</div></br>";
    $OperationResult="N";
    }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

?>