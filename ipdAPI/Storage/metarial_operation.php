<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$changeQty = $_POST["changeQty"];
	$Operators = $_POST["operator"];
	$Id = $_POST["Id"];
	
	$upDataSheet="$DataIn.ck1_rksheet";	
	
	$rkSTR="";
	$succed = "Y";
	if($Operators<0)
	{	//减少入库数量的条件 在库>=减少的数量
		$rkSTR=" and K.tStockQty>=$changeQty";
	}
	//$LockSql=" LOCK TABLES $upDataSheet R WRITE,$DataIn.ck9_stocksheet K WRITE";$LockRes=@mysql_query($LockSql);
	$upSql = "UPDATE $upDataSheet R LEFT JOIN $DataIn.ck9_stocksheet K ON R.StuffId=K.StuffId SET R.Qty=R.Qty+$changeQty*$Operators,K.tStockQty=K.tStockQty+$changeQty*$Operators WHERE R.Id=$Id $rkSTR";
	$upResult = mysql_query($upSql);		
	if($upResult && mysql_affected_rows()>0)
	{
		$Log="入库单:" . $Id . "更新入库数量成功!\n";
		//更新需求单的入库状态:2部分入库，1未入库，0已全部入库
		$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE WHEN (SELECT SUM(Qty) AS Qty FROM $upDataSheet WHERE StockId = '$StockId')>0 THEN 2 ELSE 1 END) WHERE StockId='$StockId'";
		$upRkAction=mysql_query($uprkSign);
	}
	else
	{
		$succed = "N";
		$Log="入库单:" . $Id . "更新入库数量失败!\n";
	    $OperationResult="N";
	}

	echo json_encode(array($succed, $Log));
	
?>