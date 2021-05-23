<?php
	
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_cancelBl";
	$_SESSION["nowWebPage"]=$nowWebPage; 

	//步骤2：
	$Log_Item="产品订单";//需处理
	$Log_Funtion="取消备料设置";
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
	
		$deleteBlRecordSql = "Delete From $DataIn.yw9_blsheet Where POrderId = '$POrderId'";
		if(mysql_query($deleteBlRecordSql))
		{
			$Log.="订单($POrderId)取消备料成功<br>";
		}
		else
		{
			$OperationResult="N";
			$Log.="订单($POrderId)取消备料失败<br>";
		}
	}
	//步骤4：操作日志
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES 	('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
	
?>