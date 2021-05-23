<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$Id = $_POST["Id"];
	$Operator = $_POST["Operator"];
	
	$Log_Item="入库";//需处理
	$Log_Funtion="删除";
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="Y";
	
	$CheckSql= mysql_query("SELECT $DataIn.ck1_rksheet.StuffId,$DataIn.ck1_rksheet.StockId,$DataIn.ck1_rksheet.Qty,$DataIn.ck1_rksheet.Mid,$DataIn.ck9_stocksheet.tStockQty
							FROM $DataIn.ck1_rksheet 
							LEFT JOIN $DataIn.ck9_stocksheet  
							ON $DataIn.ck1_rksheet.StuffId=$DataIn.ck9_stocksheet.StuffId
							WHERE $DataIn.ck1_rksheet.Id='$Id' 
							AND $DataIn.ck9_stocksheet.tStockQty>=$DataIn.ck1_rksheet.Qty",$link_id);
	
	if($CheckRow = mysql_fetch_array($CheckSql))
	{//可删除
		$StockId=$CheckRow["StockId"];				//需求单
		$Mid=$CheckRow["Mid"];						//入库主单
		$StuffId=$CheckRow["StuffId"];				//配件ID			
		$Qty=$CheckRow["Qty"];						//入库数量
		$delSql = "DELETE FROM $DataIn.ck1_rksheet WHERE Id='$Id'"; //删除些入库记录
		$delRresult = mysql_query($delSql);
		
		if($delRresult && mysql_affected_rows()>0)
		{
			$Log.="1.配件 $StuffId 的需求单 $StockId 入库记录删除成功!\n";
			////////////////////
			//2.更新需求单的收货状态
			$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE WHEN (SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId = '$StockId')>0 THEN 2 ELSE 1 END) WHERE StockId='$StockId'";
			$upRkAction=mysql_query($uprkSign);	
			if($upRkAction)
			{
				$Log.="2.需求单 $StockId 的入库标记更新成功.\n";
			}
			else
			{
				$Log.="2.需求单 $StockId 的入库标记更新失败.\n";
				$OperationResult="N";
			}
						
			//3.更新在库
			$Stockinsq = "UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$Qty WHERE StuffId='$StuffId' AND tStockQty>=$Qty LIMIT 1";
			$Stockinresult = mysql_query($Stockinsq);
			if($Stockinresult)
			{
				$Log.="3.配件 $StuffId 的在库扣除成功!\n";
			}
			else
			{
				$Log.="3.配件 $StuffId 的在库扣除失败!\n";
				$OperationResult="N";
			}
						
			//4.主入库单
			$delMainSql = "DELETE FROM $DataIn.ck1_rkmain WHERE Id=$Mid and Id NOT IN (SELECT Mid FROM $DataIn.ck1_rksheet WHERE Mid=$Mid)"; 
			$delMianRresult = mysql_query($delMainSql);
			if($delMianRresult && mysql_affected_rows()>0)
			{
				$Log.="4.主入库单已经没有内容，清除成功!\n";
			}
			else
			{
				$Log.="4.主入库单还有内容，不做处理!\n";
				$OperationResult="N";
			}
				////////////////////
		}
		else
		{//删除操作失败
			$Log.="1.配件 $StuffId 的需求单 $StockId 入库资料删除失败!\n";
			$OperationResult="N";
		}
	}
	else
	{
		$Log.="配件的在库不足或其它原因，删除失败!\n";
		$OperationResult="N";
	}
	
	echo json_encode(array("$OperationResult", $Log));
	
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
?>