<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.yw1_ordermain
$DataIn.cg1_stocksheet
$DataIn.stuffdata
$DataIn.stufftype
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
$TempCompanyId=$TempArray[0];
$TempProductId=$TempArray[1];
$TempMonth=$TempArray[2];
$tableWidth=780;
$TableId="ListTB".$RowId;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#B0F9B4'>
		<td width='30' align='center'>序号</td>
		<td width='100' align='center'>订单PO</td>
		<td width='100' align='center'>需求流水号</td>
		<td width='60' align='center'>配件分类</td>
		<td width='50' align='center'>配件ID</td>
		<td width='345' align='center'>配件名称</td>
		<td width='95' align='center'>未出数量</td>
		</tr>";
//订单列表
$sListResult = mysql_query("
SELECT S.OrderPO,G.StuffId,G.StockId,G.OrderQty,D.StuffCname,T.TypeName
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
WHERE S.Estate>0 
AND M.CompanyId='$TempCompanyId' 
AND S.ProductId='$TempProductId' 
AND T.TypeId IN (9032,9076,9083,9084,9069) 
AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$TempMonth' ORDER BY D.TypeId,D.StuffId,M.OrderDate
",$link_id);
$i=1;
$sumQty=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$StockId=$StockRows["StockId"];
		$OrderQty=$StockRows["OrderQty"];
		$StuffId=$StockRows["StuffId"];
		$StuffCname=$StockRows["StuffCname"];
		$TypeName=$StockRows["TypeName"];
		$sumQty=$sumQty+$OrderQty;
		echo"<tr bgcolor=#DAFCD6><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$OrderPO</td>";				//PO				
		echo"<td  align='center'>$StockId</td>";				//流水号
		echo"<td>$TypeName</td>";					//分类名称
		echo"<td align='center'>$StuffId</td>";					//配件ID
		echo"<td><DIV STYLE='width:280 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$StuffCname</NOBR></DIV></td>";//配件名称
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