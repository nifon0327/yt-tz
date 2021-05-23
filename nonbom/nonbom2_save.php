<?php 
//非BOM配件子分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
//步骤2：
$Log_Item="非BOM配件子分类";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Name=FormatSTR($Name);
//增加记录
$GetSign=$GetSign[0];

$inRecode="INSERT INTO $DataPublic.nonbom2_subtype(SendFloor,BuyerId,CheckerId,mainType,FirstId,TypeName,NameRule,Remark,GetSign,Date,Estate,Locks,Operator,creator,created) SELECT '$SendFloor','$BuyerId',Number,'$mainType','$FirstId','$TypeName','$NameRule','$Remark','$GetSign','$Date','1','0','$Operator', '$Operator', NOW() FROM $DataPublic.staffmain WHERE Name='$Name'";

$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
