<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="报价规则客户资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//回传参数
$ALType="From=$From&CompanyId=$CompanyId&Pagination=$Pagination";
$inRecode="INSERT INTO $DataIn.yw3_pirules (Id,CompanyId,Remark,Estate,Operator) VALUES (NULL,'$CompanyId','$Remark','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="&nbsp;&nbsp;&nbsp;&nbsp;新增报价规则客户资料成功!<br>";
	} 
else{ 
	$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;报价规则客户资料新增失败!</div><br>";
	$OperationResult="N";
	} 
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
