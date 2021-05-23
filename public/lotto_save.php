<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="新增lotto码记录";         //需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$insertSql = "INSERT INTO ch_initallotto (Id, companyId, lotto, date, operator) VALUES (NULL, '$CompanyId', '$lotto', '$DateTime', $Operator)";
//echo $insertSql;
$inResult = mysql_query($insertSql);

if($inResult){
    $Log.="新增lotto码成功!</br>";
    }
else{
    $Log.="新增lotto码失败!</div></br>";
    $OperationResult="N";
    }
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
