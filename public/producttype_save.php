<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
$Log_Item="产品分类";			//需处理
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
$TypeName=FormatSTR($TypeName);
$chinese=new chinese;
$Letter=substr($chinese->c($TypeName),0,1);
$Date=date("Y-m-d");
//锁定数据库
//锁定表
//$LockSql=" LOCK TABLES $DataIn.producttype WRITE";$LockRes=@mysql_query($LockSql);
$Sql = mysql_query("SELECT MAX(TypeId) AS Mid FROM $DataIn.producttype",$link_id);
$TypeId=mysql_result($Sql,0,"Mid");
if($TypeId){
	$TypeId=$TypeId+1;}
else{
	$TypeId=8001;}
$inRecode="INSERT INTO $DataIn.producttype (Id,mainType,TypeId,TypeName,Letter,OrderId,SortId,scType,NameRule,Estate,Locks,Date,Operator) VALUES (NULL,'$mainType','$TypeId','$TypeName','$Letter','1','$SortId','$scType','$NameRule','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!$inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
