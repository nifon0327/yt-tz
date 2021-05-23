<?php  
//电信-zxq 2012-08-01
/*
$DataIn.ck7_bprk
$DataIn.ck9_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="备品入库";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Remark=FormatSTR($Remark);
//锁定表		
$inRecode="INSERT INTO $DataIn.ck7_bprk (Id,StuffId,CompanyId,Qty,Price,Remark,RkId,LocationId,Date,Locks,Operator) VALUES (NULL,'$StuffId','$CompanyId','$Qty','$Price','$Remark','0','$LocationId','$Date','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
		} 
else{
	$Log="<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
