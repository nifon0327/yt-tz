<?php 
//ewen 2013-02-26 OK
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
$sheetInSql="INSERT INTO $DataIn.nonbom9_insheet (Id,GoodsId,Qty,Remark,Locks,Date,Operator) VALUES (NULL,'$GoodsId','$Qty','$Remark','0','$DateTime','$Operator') ";
$sheetInAction=@mysql_query($sheetInSql);
if($sheetInAction && mysql_affected_rows()>0){
	//库存增加
	$sql = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty+'$Qty',oStockQty=oStockQty+'$Qty' WHERE GoodsId='$GoodsId'";
	$result = mysql_query($sql);
	echo "1";
	}
?>