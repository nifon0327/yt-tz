<?php
	
	include "../../basic/parameter.inc";
	include "../../model/modelfunction.php";
	
	$POrderId = $_POST["POrderId"];
	///$POrderId = "201303270850";
	$TypeId = $_POST["TypeId"];
	//$TypeId = "组装";
	$Qty = $_POST["Qty"];
	//$Qty = "1";
	$Remark = $_POST["Remark"];
	//$Remark = "拆2052";
	$Operator = $_POST["Operator"];
	//$Operator = "11008";
	$needFinish = $_POST["need"];
	
	/*
//获取登记的product Tyep
	$typeIdResult = mysql_query("Select Parameter From $DataPublic.sc4_funmodule Where ModuleName = '$TypeId' Limit 1");
	$stuffTypeRow = mysql_fetch_assoc($typeIdResult);
	$TypeId = $stuffTypeRow["Parameter"];
*/
	
	//获取groupId
	$groupIdSql = "Select GroupId From $DataPublic.staffmain Where Number = '$Operator'";
	$groupIdResult = mysql_query($groupIdSql);
	$groupIdRow = mysql_fetch_assoc($groupIdResult);
	$Login_GroupId = $groupIdRow["GroupId"];
	
	//步骤2：
	$Log_Item="生产记录";			//需处理
	$Log_Funtion="保存";
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="Y";
	
	/*
//步骤3：需处理
	$LockSql=" LOCK TABLES $DataIn.sc1_cjtj WRITE";
	$LockRes=@mysql_query($LockSql);
	//计算未登记的数量，新增的数量不可以大于该值????
*/

	$inRecode="INSERT INTO $DataIn.sc1_cjtj (Id,GroupId,TypeId,POrderId,Qty,Remark,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Login_GroupId','$TypeId','$POrderId','$Qty','$Remark','$DateTime','1','0','$Operator')";
	$inAction=@mysql_query($inRecode);
	//解锁表
	/*
$unLockSql="UNLOCK TABLES";
	$unLockRes=@mysql_query($unLockSql);
*/
	if ($inAction)
	{ 
		$Log="$TitleSTR 成功!\n";
		//只改变生产状态!!!!!!!!!!!!!!!!
		
		if($needFinish != "N")
		{
			//检查该订单是否已经生产完，是则更新订单状态Y.Estate=2,
			$UpdateSql="Update $DataIn.yw1_ordersheet Y
						LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
						LEFT JOIN (
						SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
						FROM $DataIn.cg1_stocksheet G 
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
						WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
						SET Y.scFrom=0 
						WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";//该订单中，如果生产的数量和需求的数量一致，则生产状态为0，出货状态是不变的1，由品检审核后出货状态才改为待出2
			$UpdateResult = mysql_query($UpdateSql);
			if($UpdateResult && mysql_affected_rows()>0)
			{
				$Log="$Operator"."生产登记".$POrderId."( $Tid )数量".$Qty.";订单生产完毕,生产状态更新成功.\n";
				$isReadyUpdate = "upDate $DataIn.sc1_mission Set Estate = '0' Where POrderId = '$POrderId'";
				mysql_query($isReadyUpdate);
			}
			else
			{
				$Log="$Operator"."生产登记".$POrderId."( $Tid )数量".$Qty.";订单生产未完成.\n";
			}
		}
	} 
	else
	{
		$Log=$Log."$TitleSTR 失败!\n";
		$OperationResult="N";
	} 
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	echo json_encode(array($OperationResult, $Log));
	
?>