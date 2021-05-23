<?php 
//电信-joseph
//步骤1： 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="行政资料分类";			//需处理
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
//锁定表
//$LockSql=" LOCK TABLES $DataPublic.zw2_hzdoctype WRITE";$LockRes=@mysql_query($LockSql);
	$Remark=FormatSTR($Remark);
	$Name=FormatSTR($Name);
	$Date=date("Y-m-d");
	$inRecode="INSERT INTO $DataPublic.zw2_hzdoctype (Id,Name,SubName,Remark,SortId,Estate,Locks,Date,Operator) VALUES (NULL,'$Name','','$Remark','999','1','0','$Date','$Operator')";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log="$TitleSTR 成功!<br>";
		} 
	else{
		$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
		$OperationResult="N";
		}
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
