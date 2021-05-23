<?php
	
	include_once "../../basic/parameter.inc";
	
	$CodeType = $_POST["codeType"];
	$POrderId = $_POST["POrderId"];
	$Qty = $_POST["qty"];
	$Operator = $_POST["Operator"];
	
	$Log_Item="订单领料";			//需处理
	$Log_Funtion="数据更新";
	$Date=date("Y-m-d");
	$DateTime=date("Y-m-d H:i:s");
	
	$OperationResult = "Y";
    if($Qty>0 && $CodeType>0)
    {
	    $inRecode="INSERT INTO $DataIn.sc3_printtasks  (Id,CodeType,POrderId,Qty,Estate,Date,Operator) VALUES (NULL,'$CodeType','$POrderId','$Qty','1','$DateTime','$Operator')";
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
    }
	else
	{
		$Log="订单：$POrderId 添加标签打印任务失败!\n";
	    $OperationResult="N";   
	}
	
	echo json_encode(array($OperationResult, $Log));

	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES 			('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
?>