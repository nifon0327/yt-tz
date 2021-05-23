<?php
	
	include "../../basic/parameter.inc";
	include "../model/modelfunction.php";
	
	$companyId = $_POST["companyId"];
	$modelId = $_POST["modelId"];
	$bankId = $_POST["bankId"];
	$invoiceNumber = $_POST["invoiceNumber"];
	$wise = $_POST["wise"];
	$note = $_POST["note"];
	$terms = $_POST["terms"];
	$paymentTerms = $_POST["paymentTerms"];
	$shipDate = $_POST["shipDate"];
	$shipType = ($_POST["shipType"] == "1")?"replen":"";
	$operator = $_POST["operator"];
	$shipItems = $_POST["shipItems"];
	$sampleItems = $_POST["sampleItems"];
	
	$OperationResult="Y";
	$DateTime=date("Y-m-d H:i:s");
	$Log_Item="出货资料";
	$Log_Funtion="保存";
	
	//获取最大Number
	$checkNumber = mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch1_shipmain",$link_id));
	$Number = $checkNumber["Number"]+1;
	
	$mainInSql="INSERT INTO $DataIn.ch1_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,Ship,ShipType,cwSign,Remark,Operator) 
VALUES (NULL,'$companyId','$modelId','$bankId','$Number','$invoiceNumber','0','$wise','$note','$terms','$paymentTerms','','$shipDate','1','1','1','-1','$shipType','1','','$operator')";
	
	$mainInAction = mysql_query($mainInSql);
	if($mainInAction)
	{
		$Log.="出货主单($Mid)创建成功.\n";
		$Mid=mysql_insert_id();
		if ($DataIn=='ac'){
		    $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,Qty,Price,'1','1','1','1','1','0','$operator',NOW(),'$operator',NOW(),CURDATE(),'$operator' FROM $DataIn.yw1_ordersheet WHERE Id IN ($shipItems)";
		}else{
			$sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,Qty,Price,'1','1','1' FROM $DataIn.yw1_ordersheet WHERE Id IN ($shipItems)";
		}
		if($sampleItems != "")
		{
		    if ($DataIn=='ac'){
			   $sheetInSql.=" UNION SELECT NULL,'$Mid',SampId,'0',Qty,Price,'2','1','1','1','1','0','$operator',NOW(),'$operator',NOW(),CURDATE(),'$operator' FROM $DataIn.ch5_sampsheet WHERE Id IN ($sampleItems)";
			}
			else{
				  $sheetInSql.=" UNION SELECT NULL,'$Mid',SampId,'0',Qty,Price,'2','1','1' FROM $DataIn.ch5_sampsheet WHERE Id IN ($sampleItems)";
			}
		}
		
		$sheetInAction = mysql_query($sheetInSql);
		if($sheetInAction && mysql_affected_rows()>0)
		{
			$Log.="出货的订单和随货项目加入出货明细表成功.\n";
			//更新状态
			$pUpSql="UPDATE $DataIn.yw1_ordersheet SET Estate='4' WHERE Id IN ($shipItems)";
			$pUpResult = mysql_query($pUpSql);
			if($pUpResult && mysql_affected_rows()>0)
			{
				$Log.="订单的状态更新成功.\n";
			}
			else
			{
				$Log.= "订单的将出状态更新失败.\n";
				$OperationResult="N";
			}
			
			if($sampleItems != "")
			{
				$sUpSql = "UPDATE $DataIn.ch5_sampsheet SET Estate='2' WHERE Id IN ($sampleItems)";
				$sUpResult = mysql_query($sUpSql);
				if($sUpResult && mysql_affected_rows()>0)
				{
					$Log.="随货项目的状态更新成功.\n";
				}	
				else
				{
					$Log.="随货项目的将出状态更新失败.\n";
					$OperationResult="N";
				}
		}
		else
		{
			$Log.="出货的订单和随货项目加入出货明细表失败.\n";
			$OperationResult="N";
		}
	}
	else
	{
		$Log.="出货主单创建失败.\n";
	}
	
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion',\"$Log\",'$OperationResult','$operator')";
	$IN_res=@mysql_query($IN_recode);
	
	echo json_encode(array("$OperationResult", "$Log"));
	
?>