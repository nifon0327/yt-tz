<?php
include "../model/modelhead.php";

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_codeUpdated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品资料";       //需处理
$Log_Funtion="更新喷码code";
$TitleSTR=$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$codeUpdateSql = "REPLACE INTO $DataIn.productprintparameter (Id, productId, Lotto, itf, Estate, Operator) VALUES (NULL, $ProductId, '$lotto', '$itf', 1, $Operator)";
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
echo "<input type = 'hidden' name='CompanyId' id='CompanyId' value = '$CompanyId'>";
?>