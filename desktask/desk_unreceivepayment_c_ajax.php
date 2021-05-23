<?php   
/*
已更新电信---yang 20120801
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
$CompanyId=$TempArray[0];
$Currency=$TempArray[1];
$ShipId=$TempArray[2];
$tableWidth=910;
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
SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.TestStandard,P.eCode,S.Qty,S.Price,S.Type,S.YandN 
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$ShipId' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.SampName AS cName,'' AS TestStandard,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN 
	FROM $DataIn.ch1_shipsheet S
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,'' AS TestStandard,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN 
	FROM $DataIn.ch1_shipsheet S
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='3'
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
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$OrderPO</td>";				//PO				
		echo"<td  align='center'>$POrderId</td>";				//流水号
		echo"<td><DIV STYLE='width:280 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$TestStandard</NOBR></DIV></td>";//名称
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