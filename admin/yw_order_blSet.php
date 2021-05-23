<?php
	
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_blSet";
	$_SESSION["nowWebPage"]=$nowWebPage; 

	//步骤2：
	$Log_Item="产品订单";//需处理
	$Log_Funtion="备料设置";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	$DateTime=date("Y-m-d H:i:s");
	$Date = date("Y-m-d");

	$Operator=$Login_P_Number;
	$OperationResult="Y";
	ChangeWtitle($TitleSTR);
	for($i=0; $i<count($checkid); $i++)
	{
		$Id = $checkid[$i];
		//获取订单了POrderId
		$pOrderIdSql = "Select * From $DataIn.yw1_ordersheet Where Id = '$Id'";
		$pOrderIdResult = mysql_query($pOrderIdSql);
		$pOrderIdRow = mysql_fetch_assoc($pOrderIdResult);
		$POrderId = $pOrderIdRow["POrderId"];
		$ProductId = $pOrderIdRow["ProductId"];
	
		$orderCountSql = "Select * From $DataIn.yw1_ordersheet WHERE ProductId =  '$ProductId' AND scFrom =  '1'";
		$orderCountResult = mysql_query($orderCountSql);
		if(mysql_num_rows($orderCountResult) == 1)
		{
			$OperationResult="N";
			$Log.="该订单($POrderId)产品只有一张订单，不需备料设置<br>";
		}
		else
		{
			$hasSamePOrderIdSql = "Select * From $DataIn.yw9_blsheet Where POrderId = '$POrderId'";
			$hasSamePOrderIdResult = mysql_query($hasSamePOrderIdSql);
			if(mysql_num_rows($hasSamePOrderIdResult) == 0)
			{
				$recordBlSql = "Insert Into $DataIn.yw9_blsheet (Id, Num, POrderId, blDate, Estate, Date, Operator) Values (NULL, '1', '$POrderId', '$Date', '1', '$Date', '$Operator')";
				if(mysql_query($recordBlSql))
				{
					$Log.="订单($POrderId)设置备料成功<br>";
				}
				else
				{
					$OperationResult="N";
					$Log.="订单($POrderId)设置备料失败<br>";
				}
			}
			else
			{
				$OperationResult="N";
				$Log.="订单($POrderId)设置备料重复<br>";
			}
		}
	}
	//步骤4：操作日志
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES 	('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
	
?>