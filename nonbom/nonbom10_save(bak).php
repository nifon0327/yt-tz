<?php 
//ewen 2013-02-27 OK
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//保存记录
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$sheetInSql="INSERT INTO $DataIn.nonbom10_outsheet (Id,GoodsId,Qty,Remark,ReturnReasons,Estate,Locks,Date,Operator) VALUES (NULL,'$GoodsId','$Qty','$Remark','','2','0','$DateTime','$Operator') ";
$sheetInAction=@mysql_query($sheetInSql);
if($sheetInAction && mysql_affected_rows()>0){
	//为防止连续报废，引起库存读取出错，先将报废数量从库存中扣除
	$sql = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty-'$Qty',oStockQty=oStockQty-'$Qty' WHERE GoodsId='$GoodsId' AND wStockQty>='$Qty' AND oStockQty>='$Qty' ";
	$result = mysql_query($sql);
	/*
	如果oStockQty<mStockQty
	//如果因为报废引起最低库存不足，则自动生成申购单
	$sheetInSql="INSERT INTO $DataIn.nonbom6_cgsheet 
	SELECT (NULL,'0',A.GoodsId,B.CompanyId,'$Qty',A.Price,'因报废引起最低库存不足','1','1','2','1','$DateTime','$Operator') 
	FROM $DataPublic.nonbom4_goodsdata A
	LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId WHERE A.GoodsId='$GoodsId'";
	$sheetInAction=@mysql_query($sheetInSql);
	*/
	echo "1";
	}
?>