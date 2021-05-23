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
$sheetInSql="INSERT INTO $DataIn.nonbom8_outsheet (Id,GoodsId,WorkAdd,Qty,Remark,ReturnReasons,GetNumber,OutOperator,OutDate,Estate,Confirm,Locks,Date,Operator) VALUES (NULL,'$GoodsId','$WorkAdd','$Qty','$Remark','','$GetNumber','0','0000-00-00 00:00:00','2','1','0','$slDate','$Operator') ";
$sheetInAction=@mysql_query($sheetInSql);
if($sheetInAction && mysql_affected_rows()>0){
	echo "1";
	}
?>