<?php
	
   include "../../model/stuffcombox_function.php";
   
	$Operator = $LoginNumber;
	
	$qty = $info[1];
	$stockId = $info[0];
	$Log_Item="仓库可占用(备料)";			//需处理
	$Log_Funtion="数据更新";
	$Date=date("Y-m-d");
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="Y";
	$Log = "";
	
	switch($ActionId){
	    /*
		case "BL":
		   blOperation($OperationResult,$qty,$stockId,$Operator,$Log,$DateTime,$Date,$DataIn,$link_id);
		   checkFinishedProduct($POrderId,$DataIn,$link_id,$Operator,$Log);
		   break;
	  case "ALLBL":
	       $POrderId = $info[0];
			$checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId
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
				blOperation($OperationResult,$qty,$stockId,$Operator,$Log,$DateTime,$Date,$DataIn,$link_id);
			}
			checkFinishedProduct($POrderId,$DataIn,$link_id,$Operator,$Log);
	     break;
	     */
	 case "PRINT":
		 	$Log_Item="订单领料－打印(条码)";
			$Qty = $info[4]; $POrderId = $info[0];
			if (strlen($POrderId)>12) {
				$POrderId = substr($POrderId,0,12);
			}
			$OperationResult = "Y";
			$CodeType =1;//PE 标签
		    if($Qty>0)
		    {
			    $inRecode="INSERT INTO $DataIn.sc3_printtasks  (Id,CodeType,POrderId,Qty,Estate,Date,Operator) VALUES (NULL,'1','$POrderId','$Qty','1','$DateTime','$Operator')";
				$inAction=@mysql_query($inRecode);
		        if ($inAction)
		        { 
			        $Log="订单：$POrderId 添加标签打印任务成功!\n";
			    } 
		        else
		        {
			         $Log="订单：$POrderId 添加标签打印任务失败!\n";
			         $OperationResult="N";
			    } 
		    } else {
				$OperationResult="N";
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
	
	//传出参数 OperationResult    Log
	function blOperation(&$OperationResult,$qty,$stockId,$Operator,&$Log,$DateTime,$Date,$DataIn,$link_id) {
			$stuffStateSql = "SELECT A.OrderQty, SUM(B.Qty) AS blQty FROM $DataIn.cg1_stocksheet AS A
					  INNER JOIN $DataIn.ck5_llsheet B On B.StockId = A.StockId
					  WHERE A.StockId='$stockId'";
			$stuffStateResult = mysql_query($stuffStateSql);
			$stuffState = mysql_fetch_assoc($stuffStateResult);
			$stateStuffOrderQty = $stuffState['OrderQty'];
			$stateStuffblQty = $stuffState['blQty'];
			if($stateStuffOrderQty < $stateStuffblQty + $qty){
		//echo json_encode(array("N", "备料失败", $faildLine))
				$OperationResult = "N";
				return ;
			}

	//生成主备料单
			$blinRecode="INSERT INTO $DataIn.yw9_blmain (Id,Estate,Locks,Date,Operator) VALUES (NULL,'1','0','$DateTime','$Operator')";
			$blinAction=@mysql_query($blinRecode);
			$Pid=mysql_insert_id();
			
			if($Pid!=0 && $Pid!=""){
				$faildStuff = "";
			//取得配件ID号
			  //$checkResult = mysql_query("SELECT StuffId FROM $DataIn.cg1_stocksheet WHERE StockId='$stockId' LIMIT 1"); 
			  
			  $checkResult = mysql_query("SELECT S.StuffId,S.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty   
																FROM $DataIn.cg1_stocksheet S 
																LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=S.StockId 
																WHERE S.StockId='$stockId'");
			  $StuffId=mysql_result($checkResult,0,"StuffId");
			  $OrderQty=mysql_result($checkResult,0,"OrderQty");
			  $llQty=mysql_result($checkResult,0,"llQty");
			  
	 if ($qty>=($OrderQty-$llQty)){
			//更新在库
			//$signUpSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$qty  WHERE StuffId=$StuffId AND tStockQty>=$qty";
			//$ocuppyLogSql = "Insert Into $DataIn.logocuppy (Id, SqlText, POrderId, Operator) Values (NULL, $signUpSql, '$POrderId', '$Operator')";
			//$ocuppyLogResult = mysql_query($ocuppyLogSql);
			//$signUpResult = mysql_query($signUpSql);
			//if ($signUpResult && mysql_affected_rows()>0){
			  //			$Log.="更新配件($StuffId)在库数量(-$qty)成功!\n";
			if (1){		
				    //生成领料明细数据 
					$POrderId = substr($stockId, 0, 12);
					$llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,POrderId,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','0', $POrderId,'$stockId','$StuffId','$qty','0','1')";
					$llInAction=@mysql_query($llInSql);
					
					if ($llInAction){
						$Log.="生成领料单成功!\n";
					}
					else{
						$Log.="生成领料单失败!\n";
					}
			     //子母配件备料
				 stuffcombox_bl_save($stockId,$Pid,$DataIn,$link_id,$Operator,$Log);
				 
				 /* 
				//查看是否成品类配件
					$hasAssembleSql = "SELECT A.StuffId
								  FROM $DataIn.pands A
								  LEFT JOIN $DataIn.yw1_orderSheet B ON B.ProductId = A.ProductId
								  LEFT JOIN $DataIn.stuffdata C ON C.StuffId = A.StuffId
								  WHERE B.POrderId =  '$POrderId'
								  AND C.TypeId =  '7100'";
				
					$hasAssembleResult = mysql_query($hasAssembleSql);
					if(mysql_num_rows($hasAssembleResult) == 0){
					//生成主领料单
						$Mid = '';
		    			$llinRecode="INSERT INTO $DataIn.ck5_llmain (Id,Materieler,Remark,Locks,Date,Operator,Time) VALUES (NULL,'$Operator','','0','$Date','$Operator','$Time')";
						$llinAction=@mysql_query($llinRecode);
						$Mid=mysql_insert_id();
					
						if($Mid!=0 && $Mid!=""){
							$upSql="UPDATE $DataIn.ck5_llsheet SET Estate=0,Mid='$Mid' WHERE StockId = '$StockId'";
							if(mysql_query($upSql)){
								$isFinishSql = "SELECT SUM( A.OrderQty ) AS orderQty, SUM( D.Qty ) AS Qty
											FROM $DataIn.cg1_stocksheet A
											LEFT JOIN $DataIn.stuffdata B ON B.StuffId = A.StuffId
											LEFT JOIN $DataIn.stufftype C ON C.TypeId = B.TypeId
											LEFT JOIN $DataIn.ck5_llsheet D ON D.StockId = A.StockId AND D.Estate =  '0'
											WHERE A.POrderId = '$POrderId'
											AND C.mainType IN ('0', '1')";
								$isFinishResult = mysql_query($isFinishSql);
							if($isFinishRow = mysql_fetch_assoc($isFinishResult)){
								$orderQty = ($isFinishRow["orderQty"]=="")?0:$isFinishRow["orderQty"];
								$qty = ($isFinishRow["Qty"]=="")?0:$isFinishRow["Qty"];
								if($orderQty != 0 && $qty != 0 &&$orderQty == $qty){
									$UpdateSql="Update $DataIn.yw1_ordersheet Set scFrom=0, Estate=2 Where POrderId='$POrderId'";
									mysql_query($UpdateSql);
								}
							}
						}
					}
				}
				*/
			}else{
				$Log.="更新配件($StuffId)在库数量(-$qty)失败!未生成领料单;\n";
				//$OperationResult="N";
				$faildStuff[] = $StuffId;
			}	
		}
		else{
				$Log.="配件($StuffId)的领料数量($qty)大于订单需求数量;\n";
				//$OperationResult="N";
				$faildStuff[] = $StuffId;
			}	
		 //解锁
        //  $unLockSql="UNLOCK TABLES"; $unLockRes=mysql_query($unLockSql);
    }
	else
	{
		$Log.="生成领料单失败!\n";
	    $OperationResult="N";
    }
}

function checkFinishedProduct($POrderId,$DataIn,$link_id,$Operator,&$Log)
{
	    //查看是否成品类配件
		$hasAssembleSql = "SELECT A.StuffId
					  FROM $DataIn.pands A
					  LEFT JOIN $DataIn.yw1_orderSheet B ON B.ProductId = A.ProductId
					  LEFT JOIN $DataIn.stuffdata C ON C.StuffId = A.StuffId
					  WHERE B.POrderId =  '$POrderId'
					  AND C.TypeId =  '7100'";
	
		$hasAssembleResult = mysql_query($hasAssembleSql);
		if(mysql_num_rows($hasAssembleResult) == 0){
		//生成主领料单
			$Mid = '';
			$Date=date("Y-m-d");
			$DateTime=date("Y-m-d H:i:s");
			$llinRecode="INSERT INTO $DataIn.ck5_llmain (Id,Materieler,Remark,Locks,Date,Operator,Time) VALUES (NULL,'$Operator','','0','$Date','$Operator','$DateTime')";
			$llinAction=@mysql_query($llinRecode);
			$Mid=mysql_insert_id();
		
			if($Mid!=0 && $Mid!=""){
				$upSql="UPDATE $DataIn.ck5_llsheet SET Estate=0,Mid='$Mid' WHERE POrderId = '$POrderId '";
				if(mysql_query($upSql)){
					$isFinishSql = "SELECT SUM( A.OrderQty ) AS orderQty, SUM( D.Qty ) AS Qty
								FROM $DataIn.cg1_stocksheet A
								LEFT JOIN $DataIn.stuffdata B ON B.StuffId = A.StuffId
								LEFT JOIN $DataIn.stufftype C ON C.TypeId = B.TypeId
								LEFT JOIN $DataIn.ck5_llsheet D ON D.StockId = A.StockId AND D.Estate =  '0'
								WHERE A.POrderId = '$POrderId'
								AND C.mainType IN ('0', '1')";
					$isFinishResult = mysql_query($isFinishSql);
				if($isFinishRow = mysql_fetch_assoc($isFinishResult)){
					$orderQty = ($isFinishRow["orderQty"]=="")?0:$isFinishRow["orderQty"];
					$qty = ($isFinishRow["Qty"]=="")?0:$isFinishRow["Qty"];
					if($orderQty != 0 && $qty != 0 &&$orderQty == $qty){
						$UpdateSql="Update $DataIn.yw1_ordersheet Set scFrom=0, Estate=2 Where POrderId='$POrderId'";
						mysql_query($UpdateSql);
						$Log.="更新成品类配件订单($POrderId)状态成功!\n";
					}
				}
			}
		}
	}
}
	
?>