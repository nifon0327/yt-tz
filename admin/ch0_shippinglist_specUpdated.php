<?php
include "../model/modelhead.php";

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_specUpdated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="指定喷码内容";       //需处理
$Log_Funtion="更新指定喷码内容";
$TitleSTR=$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$codeUpdateSql = "REPLACE INTO $DataIn.ch13_othernote (Id, ShipId, type, note) VALUES (NULL, $ShipId, 'emu', '$spec')";
$codeUpdateResult = mysql_query($codeUpdateSql);

if($codeUpdateResult){
    $Log = '更新成功';
}
else{
    $Log = '更新失败:'.$codeUpdateSql;
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>