<?php
	


	$groupId =$info[1];
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="N";
	$POrderId = $info[0];
	$Operator = $LoginNumber;
	$Log = "";
	
	$Log_Item="生产待分配";			//需处理
	
	switch ($ActionId) {
		
		case "LINE":{
			$Log_Funtion="分配生产拉线";
	
			$missionHandleSql = "replace into $DataIn.sc1_mission (Id, POrderId, Operator, DateTime, Estate, FinishTime) values (NULL, '$POrderId', '$groupId', '$DateTime', '1', NULL)";
			$result = mysql_query($missionHandleSql);
			if ($result) {
				$Log="订单流水号为 $POrderId 的拉线分配成功.";
				$OperationResult = "Y";
			} else {
				$Log="订单流水号为 $POrderId 的拉线分配失败.";
				$OperationResult="N";
			}
	
		}
		break;
		
		case "QXLINE": {
		
			$Log_Funtion="取消拉线分配";
		
			$missionHandleSql =  "delete From $DataIn.sc1_mission Where POrderId = '$POrderId'";
			$result = mysql_query($missionHandleSql);
			if ($result) {
				$Log="订单流水号为 $POrderId 的拉线分配取消成功.";
				$OperationResult = "Y";
			} else {
				$Log="订单流水号为 $POrderId 的拉线分配取消失败.";
				$OperationResult="N";
			}
			 
		}
		break;
		
		case "LINGQI": {
		
			$Log_Funtion="配件领齐确认";
		$POrderId = $info[0];
			$missionHandleSql =  "replace $DataIn.ck_dfp_pjlq (ID,POrderId,lqEstate,Operator,OPdatetime) values 
			(null,'$POrderId',1,'$LoginNumber','$DateTime')";
			$result = mysql_query($missionHandleSql);
			if ($result) {
				$Log="订单流水号为 $POrderId 的配件领齐确认成功.";
				$OperationResult = "Y";
			} else {
				$Log="订单流水号为 $POrderId 的配件领齐确认失败.";
				$OperationResult="N";
			}
			 
		}
		break;
		
	}
	
	
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$Log");
	
?>