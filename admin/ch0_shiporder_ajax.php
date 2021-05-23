<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
echo"<table id='$TableId' width='830' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>PO</td>
		<td width='80' align='center'>流水号</td>				
		<td width='280' align='center'>中文名称</td>
		<td width='200' align='center'>Product Code</td>
		<td width='50' align='center'>数量</td>
		<td width='50' align='center'>单价</td>
		<td width='60' align='center'>金额</td>
		</tr>";
//订单列表//LEFT JOIN yw1_ordermain M ON M.OrderNumber=O.OrderNumber 
$sListResult = mysql_query("
SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN 
	FROM $DataIn.ch0_shipsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 	
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$ShipId' AND S.Type='1'
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
		$Type=$StockRows["Type"];
		$YandN=$StockRows["YandN"];
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
else{
	echo"<tr><td height='30'>没有出货明细资料,请检查.</td></tr>";
	}
echo"</table>";
?>