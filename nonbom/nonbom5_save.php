<?php 
//ewen 2013-02-25 OK
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//保存记录
$DateTime=date("Y-m-d H:i:s");
$sgDate=$sgDate==""?$DateTime:$sgDate;
$Operator=$Login_P_Number;
$sheetInSql="INSERT INTO $DataIn.nonbom6_cgsheet (Id,Mid,ForshortId,fromMid,qkId,mainType,GoodsId,CompanyId,BuyerId,Qty,Price,AddTaxValue,Remark,ReturnReasons,rkSign,Estate,Locks,Date,Operator) VALUES (NULL,'0','$Forshort',0,'0','$mainType','$GoodsId','$CompanyId','$BuyerId','$Qty','$Price','$AddTaxValue','$Remark','','1','2','1','$sgDate','$Operator') ";
$sheetInAction=@mysql_query($sheetInSql);
if($sheetInAction && mysql_affected_rows()>0){
	echo "1";
	}
?>