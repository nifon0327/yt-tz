<?php   
//已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="生产记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&TypeId=$TypeId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//$LockSql=" LOCK TABLES $DataIn.sc1_cjtj WRITE"; $LockRes=@mysql_query($LockSql);
$inRecode="INSERT INTO $DataIn.sc1_cjtj (Id,Tid,POrderId,Qty,Remark,Date,Estate,Locks,Operator) VALUES 
(NULL,'$Tid','$POrderId','$Qty','$Remark','$Date','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
//检查该订单是否已经生产完，是则更新订单状态
		$UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId 
			FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId') A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3) B ON B.POrderId=Y.POrderId 
			SET Y.Estate=2,Y.scFrom=0 
			WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";
		$UpdateResult = mysql_query($UpdateSql);
		$UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId') A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3) B ON B.POrderId=Y.POrderId 
			SET Y.Estate=1,Y.scFrom=2 
			WHERE Y.POrderId='$POrderId' AND A.Qty<B.Qty";
		$UpdateResult = mysql_query($UpdateSql);
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
