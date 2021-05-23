<?php   
//电信---yang 20120801
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
$TempProductId=$TempArray[0];
$TempMonth=$TempArray[1];
$tableWidth=780;
$TableId="ListTB".$RowId;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>PO</td>
		<td width='80' align='center'>流水号</td>				
		<td width='280' align='center'>中文名称</td>
		<td width='200' align='center'>Product Code</td>
		<td width='50' align='center'>数量</td>
		<td width='50' align='center'>单价</td>
		<td width='60' align='center'>金额</td>
		</tr>";
//订单列表
$sListResult = mysql_query("
SELECT S.OrderPO,S.POrderId,S.Qty,S.Price,D.cName,D.eCode
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
LEFT JOIN $DataIn.productdata D ON D.ProductId=S.ProductId
WHERE S.Estate>0 AND S.ProductId='$TempProductId' AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$TempMonth' ORDER BY M.OrderDate
",$link_id);
$i=1;
$sumQty=0;
$sumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$POrderId=$StockRows["POrderId"];
		$cName=$StockRows["cName"];
		$eCode=$StockRows["eCode"];
		$Qty=$StockRows["Qty"];
		$Price=$StockRows["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);	
		$sumQty=$sumQty+$Qty;
		$sumAmount=sprintf("%.2f",$sumAmount+$Amount);
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$OrderPO</td>";				//PO				
		echo"<td  align='center'>$POrderId</td>";				//流水号
		echo"<td><DIV STYLE='width:280 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$cName</NOBR></DIV></td>";//名称
		echo"<td>$eCode</td>";					//代码
		echo"<td align='right'>$Qty</td>";//订单需求数量
		echo"<td align='right'>$Price</td>";//使用库存数
		echo"<td align='right'>$Amount</td>";//采购数量
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='5'>合 计</td>";
		echo"<td align='right'>$sumQty</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumAmount</td>";
		echo"</tr>";
	}
echo"</table>";

?>