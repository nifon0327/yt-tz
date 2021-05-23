<?php
	
	include "../basic/parameter.inc";
	include "../model/modelfunction.php";
	
	$POrderId = $_POST["POrderId"];
	$Qty = $_POST["Qty"];
	$Qty1 = $_POST["Qty1"];
	$Qty2 = $_POST["Qty2"];
	$Operator = $_POST["Operator"];
	$SpiltRemark =$_POST["remark"];
	
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$llSign=1;
	if($llSign==1)
	{//有备料的订单拆分 先不做拆分处理，需业务主管审核方可拆单。不审核做不处理状态
		$OrderSplit="INSERT $DataIn.yw10_ordersplit(Id, POrderId, Qty, Qty1, Qty2,Remark,Estate, Date, Operator)VALUES(NULL,'$POrderId','$Qty','$Qty1','$Qty2','$SpiltRemark','0','$Date','$Operator')";
		$OrderResult=mysql_query($OrderSplit);
		if($OrderResult)
		{
			$Log = "订单流水号为 $POrderId 添加表中成功,等待业务主管审核才能拆分!";
			$OperationResult = "Y";
		}
		else
		{
			$Log = "订单流水号为 $POrderId 添加表中失败,拆分失败!";
			$OperationResult="N";
		}
	}
	
	echo json_encode(array($OrderResult, $Log));
	
?>