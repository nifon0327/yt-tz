<?php
	
    include "../../model/stuffcombox_function.php";

	$StockId = $info[0];
	$Operator = $LoginNumber;
	
	$llQty = $info[2];
	$thisQty = $info[1];
	$StuffId = $info[3];
	
	$Log_Item="车间领料数据";			//需处理
	$Log_Funtion="数据更新";
	$DateTime=date("Y-m-d H:i:s");
	$Time = date("H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";
			//需处理
	 $Log = "";
	switch($ActionId)
	{
		case "QUEREN": {
			$POrderId = $info[0];
			$Log_Item="已备料确认";	
			$confirmSql = "replace into $DataIn.ck5_llconfirm (Id,POrderId,Estate) values (null,'$POrderId',0)";
			if (mysql_query($confirmSql)) {
				 $Log =   "已备料确认成功!(POrderId:$POrderId)\n";
			} else {
				$OperationResult="N";
				$Log =   "已备料确认失败!(POrderId:$POrderId)\n";
			}
		} break;
		
		case "CJLL":
		{
			$Log_Item="车间领料";	
			//检查当天员工是否已有领料记录
			
			//生成主领料单
	    	$llinRecode="INSERT INTO $DataIn.ck5_llmain (Id,Materieler,Remark,Locks,Date,Operator,Time) VALUES (NULL,'$Operator','','0','$Date','$Operator','$Time')";
	    	$llinAction=@mysql_query($llinRecode);
	    	$Mid=mysql_insert_id();
		    
		   
		    if($Mid!=0 && $Mid!=""){
			   $upSql="UPDATE $DataIn.ck5_llsheet SET Estate=0,Mid='$Mid' WHERE StockId='$StockId'";
			   $upResult=mysql_query($upSql);	
			   if($upResult){
		           $OperationResult = "Y";
		           $Log =   "领料单确认成功!\n";
		           
		           $checkStuffcom=mysql_query("SELECT A.mStockId,SUM(A.OrderQty) AS OrderQty,SUM(A.llQty) AS llQty,SUM(A.llEstate) AS llEstate
								FROM (
								SELECT B.mStockId,SUM(S.OrderQty) AS OrderQty,0 AS llQty,0 AS llEstate 
								FROM $DataIn.cg1_stuffcombox B
								LEFT JOIN $DataIn.cg1_stuffcombox S ON S.mStockId=B.mStockId 
								WHERE S.StockId='$StockId'
						UNION ALL
								SELECT B.mStockId,0 AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,sum(case  when L.Estate=1 then 1  else 0 end)  AS llEstate 
								FROM $DataIn.cg1_stuffcombox B
								LEFT JOIN $DataIn.cg1_stuffcombox S ON S.mStockId=B.mStockId 
								LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=S.StockId 
								WHERE S.StockId='$StockId') A ");
		           if($checkRows = mysql_fetch_assoc($checkStuffcom)){
			             $com_OrderQty=$checkRows['OrderQty'];
			             $com_llQty=$checkRows['llQty'];
			             $com_llEstate=$checkRows['llEstate'];
			             
			             if ($com_OrderQty==$com_llQty && $com_llEstate==0){
				              $com_mStockId=$checkRows['mStockId'];
				              $upSql2="UPDATE $DataIn.ck5_llsheet SET Estate=0,Mid='$Mid' WHERE StockId='$com_mStockId'";
			                  $upResult2=mysql_query($upSql2);	
			             }
		           }
		           
		           $POrderId = substr($StockId, 0, 12);
		           //配料完之后
		           $isReadyAll = mysql_query("Select SUM(Estate) as llEstate From $DataIn.ck5_llsheet Where Left(StockId, 12) = '$POrderId'");
		           $isReadyAllResult = mysql_fetch_assoc($isReadyAll);
		           $llEstate = $isReadyAllResult["llEstate"];
		           if($llEstate == 0){
			           $isReadyUpdate = "upDate $DataIn.sc1_mission Set Estate = '2' Where POrderId = '$POrderId'";
			           mysql_query($isReadyUpdate);
		           }
		           
		           //查询是否配件成品
		           $hasAssembleSql = "SELECT A.StuffId 
							  FROM $DataIn.pands A
							  LEFT JOIN $DataIn.yw1_orderSheet B ON B.ProductId = A.ProductId
							  LEFT JOIN $DataIn.stuffdata C ON C.StuffId = A.StuffId
							  WHERE B.POrderId =  '$POrderId'
							  AND C.TypeId =  '7100'";
			
				   $hasAssembleResult = mysql_query($hasAssembleSql);
				   if(mysql_num_rows($hasAssembleResult) == 0)
				   {
		           		$isFinishSql = "SELECT SUM( A.OrderQty ) AS orderQty, SUM( D.Qty ) AS Qty
										FROM $DataIn.cg1_stocksheet A
										LEFT JOIN $DataIn.stuffdata B ON B.StuffId = A.StuffId
										LEFT JOIN $DataIn.stufftype C ON C.TypeId = B.TypeId
										LEFT JOIN $DataIn.ck5_llsheet D ON D.StockId = A.StockId AND D.Estate =  '0'
										WHERE A.POrderId = '$POrderId'
										AND C.mainType IN ('0', '1')";
						$isFinishResult = mysql_query($isFinishSql);
						if($isFinishRow = mysql_fetch_assoc($isFinishResult))
						{
							$orderQty = ($isFinishRow["orderQty"]=="")?0:$isFinishRow["orderQty"];
							$qty = ($isFinishRow["Qty"]=="")?0:$isFinishRow["Qty"];
							if($orderQty != 0 && $qty != 0 &&$orderQty == $qty)
							{
								$UpdateSql="Update $DataIn.yw1_ordersheet Set scFrom=0,Estate=2 Where POrderId='$POrderId'";
								mysql_query($UpdateSql);
							}
						}
		           }
				    $OperationResult = "Y";
	           } 
	           else
	           { 
		           $Log =   "领料单确认失败!\n";
		           $OperationResult = "N";
	           }
			   $OperationResult = "Y";
	       }
	       else
	       {
		       $OperationResult = "N"; 
		       $Log =   "领料主单生成失败!\n";   
		   }
		}
		break;
		case "QXZY":
		{
			
			if (count($info)<=2) {
				$Log_Item="产品下全部配件取消占用";
				$POrderId = $info[0];	
				$checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId,G.StuffId
										FROM $DataIn.cg1_stocksheet G 
										LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
										LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
										WHERE G.POrderId='$POrderId' 
										AND ((T.mainType in (0,1)) )
										ORDER BY D.SendFloor",$link_id);
										//or (T.mainType = 5 and D.TypeId = 9124)
			
		
				while($checkStockRow=mysql_fetch_array($checkStockSql)) {
					$thisQty = $checkStockRow["OrderQty"];
					$stockId = $checkStockRow["StockId"];
					$StuffId = $checkStockRow["StuffId"];
					cancelBL($OperationResult,$stockId,$StuffId,$thisQty,$Log,$DataIn,$link_id);
				}
				
			} else {
				$Log_Item="单个配件取消占用";
				cancelBL($OperationResult,$StockId,$StuffId,$thisQty,$Log,$DataIn,$link_id);
			}
			
					break;		
		}
		default: {
			$OperationResult = "N";
		}
		break;
	}

	
	
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$Log");
	
	function cancelBL(&$OperationResult,$StockId,$StuffId,$thisQty,&$Log,$DataIn,$link_id) {
		
		//检查备料单状态
			$checkBlState=mysql_query("SELECT G.PorderId,B.Estate FROM $DataIn.ck5_llsheet K 
									   LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=K.StockId 
									   LEFT JOIN $DataIn.yw9_blsheet B ON B.PorderId=G.PorderId 
									   WHERE K.StockId='$StockId'",$link_id);
									   
			$Estate=mysql_result($checkBlState,0,"Estate");
			if($Estate!="")
			{
				//删除表yw9_blsheet中数据，表示要领料
				$PorderId=mysql_result($checkBlState,0,"PorderId");
				$UpdateSql="DELETE FROM  $DataIn.yw9_blsheet   WHERE POrderId='$POrderId'";
				$UpdateResult = mysql_query($UpdateSql);
			}
	  
			$delSql="DELETE FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' ";
			$delResult=mysql_query($delSql);	
			if($delResult)
			{
				//更新在库
				/*
				$signUpSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$thisQty  WHERE StuffId='$StuffId'";
				$signUpResult = mysql_query($signUpSql);
				if($signUpResult)
				{
					$Log.=   "更新在库成功!增加数量：(" . $thisQty . " )\n";
				}
				else
				{
					$Log.=   "更新在库失败!增加数量：(" . $thisQty . " )\n";
				}
				*/
				$OperationResult = "Y";
				$Log.=   "领料记录册除成功!\n";
				
				//子母配件备料删除
			   stuffcombox_bl_delete($StockId,$thisQty,$DataIn,$link_id,$Operator,$Log);
			} 
			else
			{
		    	$Log.=   "领料记录册除失败!\n";
		    	$OperationResult="N";
		    }

		
	}
	
?>