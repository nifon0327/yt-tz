<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="报价产品资料";			//需处理
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
$Name=FormatSTR($Name);//去连续空格,去首尾空格
$inRecode="INSERT INTO $DataIn.ywbj_productdata (Id,CompanyId,TypeId,Remark,Date,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$TypeId','$Remark','$DateTime','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="&nbsp;&nbsp;&nbsp;&nbsp;新增名称为 $Name 的报价产品资料成功!<br>";
	} 
else{ 
	$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $Name 的报价产品资料新增失败!</div><br>";
	$OperationResult="N";
	} 
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
