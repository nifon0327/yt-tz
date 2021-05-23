<?php 
//电信-zxq 2012-08-01
//步骤1：
include "../model/modelhead.php";
//步骤2：
$Log_Item="报价配件资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Name=FormatSTR($Name);
$inRecode="INSERT INTO $DataIn.ywbj_stuffdata (Id,Name,Price,Estate,Locks,Date,Operator) VALUES (NULL,'$Name','$Price','1','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log.="名称为 $Name 的报价配件资料新增成功<br>";
	} 
else{
	$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $Name 的报价配件资料新增失败! $inRecode</div>";
	$OperationResult="N";
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
