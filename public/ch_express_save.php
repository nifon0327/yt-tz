<?php 
//电信-zxq 2012-08-01
//步骤1：
include "../model/modelhead.php";
//步骤2：
$Log_Item="快递费用";			//需处理
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
$inRecode="INSERT INTO $DataIn.ch9_expsheet (Id,cSign,Mid,Date,ExpressNO,CompanyId,BoxQty,Weight,Amount,Type,HandledBy,Remark,Estate,Locks,Operator) VALUES (NULL,'$cSign','0','$SendDate','$ExpressNO','$CompanyId','$BoxQty','$Weight','$Amount','$Type','$HandledBy','$Remark','1','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction && mysql_affected_rows()>0){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>