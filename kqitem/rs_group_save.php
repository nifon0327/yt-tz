<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
$Log_Item="小组资料";			//需处理
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
//提取组长编号
$checkSql=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE Name LIKE '$StaffName'",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	$GroupLeader=$checkRow["Number"];
	//计算小组编号
	//$LockSql=" LOCK TABLES $DataIn.staffgroup WRITE";$LockRes=@mysql_query($LockSql);
	$Sql = mysql_query("SELECT MAX(GroupId) AS Mid FROM $DataIn.staffgroup WHERE BranchId='$BranchId'",$link_id);
	$GroupId=mysql_result($Sql,0,"Mid");
	if($GroupId){
		$GroupId=$GroupId+1;}
	else{
		$GroupId=$BranchId*100+1;}
	$GroupName=FormatSTR($GroupName);	
	$inRecode="INSERT INTO $DataIn.staffgroup (Id,BranchId,GroupId,GroupName,GroupLeader,TypeId,Date,Estate,Locks,Operator) VALUES (NULL,'$BranchId','$GroupId','$GroupName','$GroupLeader','$TypeId','$DateTime','1','0','$Operator')";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log="$TitleSTR 成功!<br>";
		} 
	else{
		$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
		$OperationResult="N";
		} 
	//解锁表
	//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
	}
else{
	$Log="<div class='redB'>组长($StaffName)资料提取失败!</div>";
	$OperationResult="N";
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
