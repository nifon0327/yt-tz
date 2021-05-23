<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="PI";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
//$_SESSION["nowWebPage"]=$nowWebPage;
$_SEESION["nowWebPage"] = $nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$dataArray=explode("|",$SIdList);
$Count=count($dataArray);
for($i=0;$i<$Count;$i++){
	$Ids=$Ids==""?$dataArray[$i]:($Ids.",".$dataArray[$i]);
	}

/*
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$delSql="DELETE FROM $DataIn.yw3_pisheet WHERE oId IN ($Ids)";
$delRresult = mysql_query($delSql);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw3_pisheet");
$InsertSql="INSERT INTO $DataIn.yw3_pisheet SELECT NULL,'$CompanyId',Id,'$PI','$Leadtime','$Paymentterm','$Date','$Operator' FROM $DataIn.yw1_ordersheet WHERE Id IN ($Ids)";
$inRes=@mysql_query($InsertSql);

$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
*/
include "subprogram/yw_model_pitopdf.php";
?>
