<?php 
//电信-zxq 2012-08-01
//步骤1： $DataIn.ch10_samplemail  二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="客户样品寄送资料";			//需处理
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
//记录字段值
$ExpressNO=FormatSTR($ExpressNO);
$Description=FormatSTR($Description);
$Remark=FormatSTR($Remark);
$inRecode="INSERT INTO $DataIn.ch10_samplemail 
(Id,cSign,Mid,DataType,CompanyId,LinkMan,ExpressNO,Pieces,Weight,Qty,Price,Amount,
PayType,ServiceType,HandledBy,Description,Remark,Schedule,SendDate,ReceiveDate,Estate,Locks,Operator) 
VALUES (NULL,'$cSign','0','$DataType','$theCompanyId','$LinkMan','$ExpressNO','$Pieces','$Weight','$Qty','$Price','$Amount',
'$PayType','$ServiceType','$HandledBy','$Description','$Remark','0','$theDate','','1','1','$Operator'
)";
$inAction=@mysql_query($inRecode);
if ($inAction && mysql_affected_rows()>0){
	$Log="$TitleSTR 成功!<br>";
	}
else{
	$Log="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
