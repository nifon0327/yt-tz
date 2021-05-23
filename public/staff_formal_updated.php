<?php

include "../model/modelhead.php";
$From=$From==""?"read":$From;

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$funName = $ActionId == '169'?"通过":"不通过";
$Log_Item="新员工评鉴$funName";     //需处理
$Log_Funtion="保存";

switch ($ActionId) {
    case '169': //通过试用
        $passFormalSql = "UPDATE $DataPublic.staffmain SET FormalSign=1,FormalManager=$ManagerId,FormalContent='$Remark' WHERE Number IN ($staffs)";
        break;
    case '170': //不通过试用
        $passFormalSql = "UPDATE $DataPublic.staffmain SET Estate=2,FormalManager=$ManagerId,FormalContent='$Remark' WHERE Number IN ($staffs)";
        break;
    default:
        break;
}

if(mysql_query($passFormalSql)){
    $Log = $Log_Item.$Log_Funtion.'成功';
}else{
    $Log = $Log_Item.$Log_Funtion.'失败';
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

?>