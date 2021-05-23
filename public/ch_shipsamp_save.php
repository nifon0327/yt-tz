<?php 
//电信-zxq 2012-08-01
//步骤1： $DataIn.ch5_sampsheet 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="随货样品资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理
//记录字段值
//$LockSql=" LOCK TABLES $DataIn.ch5_sampsheet WRITE";$LockRes=@mysql_query($LockSql);
$Samples_Temp=mysql_query("SELECT MAX(SampId) AS maxId FROM $DataIn.ch5_sampsheet",$link_id);; 
$Id_temp=mysql_result($Samples_Temp,0,"maxId");
if($Id_temp){
	$SampId=$Id_temp+1;}
else{
	$SampId=100001;
	}	
$inRecode="INSERT INTO $DataIn.ch5_sampsheet (Id,SampId,CompanyId,TypeId,SampPO,SampName,Description,Qty,Price,Weight,Type,Date,Estate,Locks,Operator) 
VALUES (NULL,'$SampId','$CompanyId','$TypeId','$SampPO','$SampName','$Description','$Qty','$Price','$Weight','$Type','$Date','1','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="随货样品添加成功!<br>";
 	} 
else{ 
	$Log="<div class=redB>随货样品添加失败!</div><br>"; 
	$OperationResult="N";
	}
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>