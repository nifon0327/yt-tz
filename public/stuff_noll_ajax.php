<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=780;
$StuffId=$StuffId==""?$args:$StuffId;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='20' height='20'></td>
		<td width='60' align='center'>产品ID</td>
		<td width='100' align='center'>订单流水号</td>	
		<td width='180' align='center'>中文名</td>	
		<td width='70' align='center'>订单数量</td>	
		<td width='100' align='center'>采购流水号</td>
		<td width='70' align='center'>需备料数</td>
		<td width='70' align='center'>已备料数</td>
		</tr>";



$sListResult = mysql_query("SELECT * FROM (
SELECT Y.POrderId,P.cName,P.ProductId,P.TestStandard,Y.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
FROM $DataIn.cg1_stocksheet G 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
LEFT JOIN $DataIn.productdata P ON P.ProductId = Y.ProductId 
LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = G.StockId 
WHERE G.StuffId = '$StuffId' AND G.OrderQty>0 AND Y.Estate=0 GROUP BY G.StockId 
UNION ALL
SELECT Y.POrderId,P.cName,P.ProductId,P.TestStandard,Y.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
FROM $DataIn.cg1_stuffcombox G 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
LEFT JOIN $DataIn.productdata P ON P.ProductId = Y.ProductId 
LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = G.StockId 
WHERE G.StuffId = '$StuffId'  AND G.OrderQty>0 AND Y.Estate=0 GROUP BY G.StockId ) A WHERE A.llQty<A.OrderQty ORDER BY A.OrderQty ASC
",$link_id);
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
         
		$POrderId=$StockRows["POrderId"];
		$ProductId=$StockRows["ProductId"];
		$cName=$StockRows["cName"];
		$Qty=$StockRows["Qty"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$StockId=$StockRows["StockId"];
		$OrderQty=$StockRows["OrderQty"];
		$llQty=$StockRows["llQty"];
		
    	echo"<tr bgcolor='$theDefaultColor'>
		<td align='right' height='20'>$i</td>";//
		echo"<td  align='center' >$ProductId</td>";	
		echo"<td  align='center'>$POrderId</td>";//
		echo"<td  align='Left' >$TestStandard</td>";		
		echo"<td  align='center'>$Qty</td>";
		echo"<td  align='center'>$StockId</td>";
		echo"<td  align='center'>$OrderQty</td>";
		echo"<td  align='center'>$llQty</td>";
		echo"</tr>";
		$i=$i+1;
		
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的备料资料.</td></tr>";
	}

echo"</table>"."";

?>