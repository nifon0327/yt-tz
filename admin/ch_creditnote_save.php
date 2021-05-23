<?php   
//电信-zxq 2012-08-01
//步骤1： $DataIn.ch6_creditnote 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="扣款资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$theCompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$nowYear=date("Y");
//步骤3：需处理
//锁定表
//$LockSql=" LOCK TABLES $DataIn.ch6_creditnote WRITE";$LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(Number) AS Mid FROM $DataIn.ch6_creditnote WHERE DATE_FORMAT(Date,'%Y')='$nowYear' ORDER BY Number DESC",$link_id);
$Number=mysql_result($maxSql,0,"Mid");
if($Number){
	$Number=$Number+1;
	}
else{
	$Number=$nowYear."10001";
	}
$inRecode="INSERT INTO $DataIn.ch6_creditnote (Id,Mid,PO,CompanyId,Number,Description,Qty,Price,Date,Estate,Locks,Operator) 
VALUES (NULL,'0','$PO','$theCompanyId','$Number','$Description','$Qty','$Price','$Date','1','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction && mysql_affected_rows()>0){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败 $inRecode !</div><br>";
	$OperationResult="N";
	} 
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
