<?php 
//电信-joseph
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="外部人员";			//需处理
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
//$LockSql=" LOCK TABLES $DataIn.ot_staff WRITE";$LockRes=@mysql_query($LockSql);
$checkNumRow=mysql_fetch_array(mysql_query("SELECT IFNULL(MAX(Number),0) AS Number FROM $DataIn.ot_staff ORDER BY Number DESC",$link_id));
$MaxNumber=$checkNumRow["Number"];
$Number=$MaxNumber==0?50001:$MaxNumber+1;
$Name=FormatSTR($Name);
$inRecode="INSERT INTO $DataIn.ot_staff (Id,Number,Name,Forshort,Estate,Locks,Date,Operator,PLocks, creator, created, modifier, modified) VALUES (NULL,'$Number','$Name','$Forshort','1','0','$DateTime','$Operator', '0', '$Operator', '$DateTime', '$Operator', '$DateTime')";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log="$TitleSTR 成功!<br>";
		} 
	else{
		$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
		$OperationResult="N";
		}
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
