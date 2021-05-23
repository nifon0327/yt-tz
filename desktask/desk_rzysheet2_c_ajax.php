<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.yw1_ordermain
$DataIn.cg1_stocksheet
$DataIn.productdata
$DataIn.trade_object
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$TempStuffId=$TempArray[0];
$TempMonth=$TempArray[1];
$tableWidth=780;
$TableId="ListTB".$RowId;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#B0F9B4'>
		<td width='30' align='center'>序号</td>
		<td width='60' align='center'>客户</td>
		<td width='100' align='center'>订单PO</td>
		<td width='55' align='center'>产品ID</td>
		<td width='330' align='center'>产品名称</td>
		<td width='101' align='center'>配件需求流水号</td>
		<td width='94' align='center'>未出数量</td>
		</tr>";
//订单列表
$sListResult = mysql_query("
SELECT S.OrderPO,G.OrderQty,G.StockId,G.OrderQty,S.ProductId,D.cName,C.Forshort
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata D ON D.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
WHERE S.Estate>0 AND G.StuffId='$TempStuffId' AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$TempMonth' ORDER BY S.ProductId,M.OrderDate
",$link_id);
$i=1;
$sumQty=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$StockId=$StockRows["StockId"];
		$OrderQty=$StockRows["OrderQty"];
		$ProductId=$StockRows["ProductId"];
		$cName=$StockRows["cName"];
		$Forshort=$StockRows["Forshort"];
		$sumQty=$sumQty+$OrderQty;
		echo"<tr bgcolor=#DAFCD6><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$Forshort</td>";				//客户				
		echo"<td  align='center'>$OrderPO</td>";				//PO
		echo"<td>$ProductId</td>";								//产品ID
		echo"<td><DIV STYLE='width:280 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$cName</NOBR></DIV></td>";//产品名称
		echo"<td align='center'>$StockId</td>";					//需求流水号
		echo"<td align='right'>$OrderQty</td>";					//订单需求数量
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
		echo"<tr bgcolor='#B0F9B4'><td colspan='6'>合 计</td>";
		echo"<td align='right'>$sumQty</td>";
		echo"</tr>";
	}
echo"</table><br>";
?>