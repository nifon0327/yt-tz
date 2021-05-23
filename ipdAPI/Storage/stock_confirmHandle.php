<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$StockId = $_POST["StockId"];
	$Operator = $_POST["Operator"];
	$handleType = $_POST["type"];
	$llQty = $_POST["llQty"];
	$thisQty = $_POST["qty"];
	$StuffId = $_POST["stuffId"];
	
	$Log_Item="车间领料数据";			//需处理
	$Log_Funtion="数据更新";
	$DateTime=date("Y-m-d H:i:s");
	$Time = date("H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";
	
	
	switch($handleType)
	{
		case "pass":
		{
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
		           $Log = $Id . "领料单确认成功!\n";
		           
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
	           } 
	           else
	           { 
		           $Log = $Id . "领料单确认失败!\n";
		           $OperationResult = "N";
	           }
	       }
	       else
	       {
		       $OperationResult = "N"; 
		       $Log = $Id . "领料主单生成失败!\n";   
		   }
		}
		break;
		case "del":
		{
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
	  
			$delSql="DELETE FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' AND Estate='1'";
			$delResult=mysql_query($delSql);	
			if($delResult)
			{
				//更新在库
				$signUpSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$thisQty  WHERE StuffId='$StuffId'";
				$signUpResult = mysql_query($signUpSql);
				if($signUpResult)
				{
					$Log.= $Id . "更新在库成功!增加数量：(" . $llQty . " )\n";
				}
				else
				{
					$Log.= $Id . "更新在库失败!增加数量：(" . $llQty . " )\n";
				}
				$OperationResult = "Y";
				$Log.= $Id . "领料记录册除成功!\n";
			} 
			else
			{
		    	$Log.= $Id . "领料记录册除失败!\n";
		    	$OperationResult="N";
		    }
		break;		
		}
	}
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	echo json_encode(array($OperationResult, $Log));
	
?>