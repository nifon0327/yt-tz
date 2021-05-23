<?php
	
   include "../../model/stuffcombox_function.php";

	$sgRemark =$info[1];
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";
	$POrderId = $info[0];
	$Operator = $LoginNumber;
	$Log = "";
	
	$Log_Item="生产记录";			//需处理
	
	switch ($ActionId) {
		
		case "BULIAO": 
			$Log_Funtion="补料";
			$StockId = $info[0]; $BuQty = $info[1]; $Remark=$info[2];
             if($DataIn=="ac"){
			        $IN_recode="INSERT INTO $DataIn.ck13_replenish SELECT NULL,POrderId,StockId,StuffId,'$BuQty','$Remark','$Date','0','2','0','$Operator','$DateTime','0','$Operator','$DateTime','$Operator','$DateTime',''
FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId'
UNION ALL 
SELECT NULL,POrderId,StockId,StuffId,'$BuQty','$Remark','$Date','0','2','0','$Operator','$DateTime','0','$Operator','$DateTime','$Operator','$DateTime',''
FROM $DataIn.cg1_stuffcombox WHERE StockId='$StockId'";
            }else{
			        $IN_recode="INSERT INTO $DataIn.ck13_replenish SELECT NULL,POrderId,StockId,StuffId,'$BuQty','$Remark','$Date','0','2','0','$Operator','$DateTime' FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId' ";
            }
			$IN_res= mysql_query($IN_recode);
			if ($IN_res) {
				$Log="采购流水号为 $StockId 的补料申请保存成功.";
			} else {
				$Log="<div class='redB'>采购流水号为 $StockId 的补料申请保存失败.</div>";
				$OperationResult="N";
			}	
			
	    break;
		
		case "REMARK":{
			$Log_Funtion="更新生管备注";
	
	
	//步骤3：需处理
			$sql = "UPDATE $DataIn.yw1_ordersheet SET sgRemark='$sgRemark' WHERE POrderId='$POrderId'";
			$result = mysql_query($sql);
			if ($result) {
				$Log="订单流水号为 $POrderId 的生管备注更新成功.";
				$OperationResult="Y";
			} else {
				$Log="订单流水号为 $POrderId 的生管备注更新失败.";
				$OperationResult="N";
			}	
		}
		break;
		
		case "RELINE": {
		
			$Log_Funtion="更改生产拉线";
			$groupId = $sgRemark;
			$missionHandleSql = "replace into $DataIn.sc1_mission (Id, POrderId, Operator, DateTime, Estate, FinishTime) values (NULL, '$POrderId', '$groupId', '$DateTime', '1', NULL)";
			$result = mysql_query($missionHandleSql);
			if ($result) {
				$Log="订单流水号为 $POrderId 的拉线更改成功.";
				$OperationResult = "Y";
			} else {
				$Log="订单流水号为 $POrderId 的拉线更改失败.";
				$OperationResult="N";
			}
			 
		} break;
		
		case "QXSC": {
			$Log_Funtion="取消生产";
			
			$POrderId = $info[0];
				mysql_query("insert into $DataIn.sc_canceled (id,POrderId,Estate,OPdatetime) values 
(null,'$POrderId',1,'$DateTime') ;");
			$step1 = mysql_query( "DELETE FROM  $DataIn.yw9_blsheet   WHERE POrderId='$POrderId'");
		
			if ($step1) {
				$Log.="yw9_blsheet表删除成功，流水号:$POrderId";
				
				$OperationResult="Y";
			} else {
				$OperationResult="N";
				$Log.="yw9_blsheet表删除失败，流水号:$POrderId";
			}
			$step2 = mysql_query("DELETE FROM  $DataIn.sc1_mission where POrderId='$POrderId'");
			if ($step2) {
				$Log.="拉线任务表删除成功，流水号:$POrderId";
				$OperationResult="Y";
			} else {
				$OperationResult="N";
				$Log.="拉线任务表删除失败，流水号:$POrderId";
			}
			if ($step2 && $step1) {
				$OperationResult="Y";
				$checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId,G.StuffId
										FROM $DataIn.cg1_stocksheet G 
										LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
										LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
										WHERE G.POrderId='$POrderId' 
										AND ((T.mainType in (0,1)) )
										ORDER BY D.SendFloor",$link_id);
										//or (T.mainType = 5 and D.TypeId = 9124)
			
		
		while($checkStockRow=mysql_fetch_array($checkStockSql)) {
			$qty = $checkStockRow["OrderQty"];
			$stockId = $checkStockRow["StockId"];
			$StuffId = $checkStockRow["StuffId"];
			$midRow = mysql_fetch_assoc(mysql_query("select Mid from $DataIn.ck5_llsheet where StockId = '$stockId'"));
			$mid = $midRow["Mid"];
			$mainDel = mysql_query("delete from $DataIn.ck5_llmain where id=$mid");
			if ($mainDel ) {
				$Log.="领料主表删除成功stockId:$stockId";
			} else {
				$Log.="领料主表删除失败stockId:$stockId";
			}
			$llcancel2 = mysql_query("delete from  $DataIn.ck5_llsheet  Where StockId = '$stockId' ");
			if  ($llcancel2) {
				$Log.="领料表删除成功stockId:$stockId";
			} else {
				$Log.="领料表删除失败stockId:$stockId";
			}
			//更新在库
			$signUpSql=mysql_query("UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$qty  WHERE StuffId='$StuffId'");
			if  ($signUpSql) {
				$Log.="更新在库成功(＋$qty)，StuffId:$StuffId";
			} else {
				$Log.="更新在库失败(＋$qty)，StuffId:$StuffId";
			}
			
			//子母配件备料删除
			 stuffcombox_bl_delete($stockId,$qty,$DataIn,$link_id,$Operator,$Log);
		}
			
			} else {
				$OperationResult="N";
			}
		
			
		}
		break;
		
		default:
		$OperationResult="N";
		break;
		
	}
	
	
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$Log");
	
?>