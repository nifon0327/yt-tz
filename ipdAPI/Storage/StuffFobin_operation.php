<?php
	
	include_once "../../basic/parameter.inc";
	
	$StuffId = $_POST["stuffId"];
	$Qty = $_POST["qty"];
	$Operator = $_POST["operator"];
	$Reason = $_POST["reason"];
	$DealResult = $_POST['DealResult'];
	
	$Log_Item="禁用类库存配件";			
	$Log_Funtion="报废";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";
	
	$BfResult="INSERT INTO $DataIn.ck8_bfsheet (Id, ProposerId, StuffId, Qty, Remark, DealResult, Type, Date, Estate, Locks, Operator)VALUES (NULL,'$Operator','$StuffId','$Qty','$Reason', '$DealResult','0','$Date','1','0','$Operator')";
	$BfAction=mysql_query($BfResult);
	if ($BfAction && mysql_affected_rows()>0)
	{
		$Log="$TitleSTR 成功!<br>";
		echo "Y";
	}
	else
	{
		$Log="<div class=redB>$TitleSTR 失败(库存不足或其它)!</div> $inRecode <br>";
		$OperationResult="N";
		echo "N";
	} 
	
	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
?>