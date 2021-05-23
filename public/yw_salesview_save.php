<?php  
//电信-zxq 2012-08-01
//步骤1： $DataIn.yw6_salesview 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="业务查询权限";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&UserId=$UserId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//先删后建
$delRecode = "DELETE FROM $DataIn.yw6_salesview WHERE SalesId='$SalesId'"; 
$delAction =@mysql_query($delRecode);
$Counts=count($checkid);
for($i=0;$i<$Counts;$i++){
	$ItemId=$checkid[$i];
	$Ids=$Ids==""?$ItemId:($Ids.",".$ItemId);
	}
$Date=date("Y-m-d");
if($Ids!=""){
	$inRecode="INSERT INTO $DataIn.yw6_salesview SELECT NULL,'$SalesId',CompanyId,'$TypeId','1','0','$Date','$Operator','0','$Operator','$DateTime',null,null FROM $DataIn.trade_object WHERE Id IN ($Ids)";
	$inAction=@mysql_query($inRecode);
	if($inAction && mysql_affected_rows()>0){ 
		$Log="$TitleSTR 成功!<br>";
		} 
	else{
		$Log="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
		$OperationResult="N";
		} 
	}
else{
	$Log="$TitleSTR 清空!<br>";
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>