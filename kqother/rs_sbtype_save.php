<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="社保参数设定";			//需处理
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
$inRecode="INSERT INTO $DataIn.rs_sbtype (Id, Name, cAmount, mAmount, Remark, Date, Operator) VALUES (NULL, '$Name', '$cAmount', '$mAmount', '$Remark', '$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);

if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div> $inRecode<br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
