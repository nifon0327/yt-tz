<?php
	$DateTime=date("Y-m-d H:i:s");
	$Time = date("H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";
	
	$Operator = $LoginNumber;
	$Log = "";
	
	$Log_Item="外发备料";			//需处理
	
	switch ($ActionId) {
		
		case "BL":{
			$stockId = $info[0];
			$StuffId = $info[1];
			$qty = $info[2];
			 //判断实物库存和备料数

			mysql_query('START TRANSACTION');
			//检查当天员工是否已有领料记录
			$checkResult = mysql_query("SELECT Id FROM $DataIn.ck5_llmain WHERE DATE='$Date' AND Materieler='$Operator' LIMIT 1",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)){
				$Mid=$checkRow["Id"];
			} else {
			//生成主领料单
				$llinRecode="INSERT INTO $DataIn.ck5_llmain (Id,Materieler,Remark,Locks,Date,Operator,Time) VALUES (NULL,'$Operator','','0','$Date','$Operator','$Time')";
				$llinAction=mysql_query($llinRecode);
				if($llinAction){
					$Mid=mysql_insert_id();
				} else {
					$Mid=0;
					$OperationResult = "N";
					mysql_query(' ROLLBACK ');
				}		
			}
		     
			if($Mid!=0 && $Mid!=""){
				$blinRecode="INSERT INTO $DataIn.yw9_blmain (Id,Estate,Locks,Date,Operator) VALUES (NULL,'1','0','$DateTime','$Operator')";
				$blinAction=@mysql_query($blinRecode);
				$Pid=mysql_insert_id();
			
				if($Pid!=0 && $Pid!=""){
					$faildStuff = "";
				//取得配件ID号
					$checkResult = mysql_query("SELECT StuffId FROM $DataIn.cg1_stocksheet WHERE StockId='$stockId' LIMIT 1",$link_id); 
					$StuffId=mysql_result($checkResult,0,"StuffId");
			//生成领料明细数据 
					$POrderId = substr($stockId, 0, 12);
					$llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,POrderId,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','$Mid', '$POrderId','$stockId','$StuffId','$qty','0','0')";
					if(mysql_query($llInSql)){
					  /*
						$signUpSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$qty  WHERE StuffId=$StuffId AND tStockQty>=$qty";
						if(!mysql_query($signUpSql)){
							mysql_query(' ROLLBACK ');
							$OperationResult = "N";
						}
					} else {
						$OperationResult = "N";
						mysql_query(' ROLLBACK ');
					}
					*/

				} else {
					$OperationResult = "N";
					mysql_query(' ROLLBACK ');
				}
			} else{
				$OperationResult = "N";
				mysql_query(' ROLLBACK ');
		}
	
			mysql_query('COMMIT');
	}
		break;
		
	}
	
	
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$Log");
	
?>