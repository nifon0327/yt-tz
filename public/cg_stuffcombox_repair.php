<?php 
//电信-zxq 2012-08-01
/*
	修复已下采购单母配件未生成子母配件关联表
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "../model/stuffcombox_function.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log="";

$mySql="SELECT G.*  
FROM $DataIn.cg1_stocksheet G 
WHERE G.rkSign=1 AND (G.AddQty+G.FactualQty)>0 
AND  EXISTS (SELECT A.StuffId FROM $DataIn.stuffcombox_bom A WHERE A.mStuffId=G.StuffId)
AND  NOT EXISTS(SELECT B.StuffId FROM $DataIn.cg1_stuffcombox B WHERE B.mStuffId=G.StuffId AND B.mStockId=G.StockId)";
$myResult = mysql_query($mySql,$link_id);

while($myRow = mysql_fetch_array($myResult)){
        $StockId=$myRow['StockId'];
        $StuffId=$myRow['StuffId'];
        
	    addCg_StuffComBox_data($StockId,$StuffId,$DataIn,$link_id,$Login_P_Number,$Log);
}
echo $Log;
?>