<?php   
//电信-EWEN
include "../model/modelhead.php";
$Log_Item="PI";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_pitopdf";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

include "subprogram/yw_model_pitopdf.php";
?>
