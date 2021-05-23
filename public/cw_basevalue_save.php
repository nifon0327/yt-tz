<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="财务基本参数";			//需处理
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
//$LockSql=" LOCK TABLES $DataPublic.cw3_basevalue WRITE";$LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(ValueCode) AS maxId FROM $DataPublic.cw3_basevalue ORDER BY Id DESC",$link_id);
$ValueCode=mysql_result($maxSql,0,"maxId");
if($ValueCode){
	$ValueCode=$ValueCode+1;
	}
else{
	$ValueCode=101;//默认
	}
$inRecode="INSERT INTO $DataPublic.cw3_basevalue (Id,ValueCode,Remark,Value,Estate,Locks,Date,Operator) VALUES (NULL,'$ValueCode','$Remark','$Value','1','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
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
