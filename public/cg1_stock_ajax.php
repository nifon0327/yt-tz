<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=550;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='20' height='20'></td>
		<td width='80' align='center'>PO</td>
		<td width='80' align='center'>采购流水号</td>		
		<td width='50' align='center'>单价</td>
		<td width='50' align='center'>订单数量</td>
		<td width='50' align='center'>使用库存</td>
		<td width='50' align='center'>需购数量</td>
		<td width='50' align='center'>增购数量</td>
		<td width='50' align='center'>实购数量</td>
		<td width='50' align='center'>金额</td>
     </tr>";

$sListResult = mysql_query("SELECT  S.StockId,S.POrderId,S.OrderQty,S.StockQty,S.AddQty, S.FactualQty,Y.OrderPO,S.Price
                FROM $DataIn.cg1_stocksheet S  
                LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
               WHERE S.StuffId='$StuffId' AND S.Mid=0 AND S.StockQty>0 and ( S.addqty>0 OR  S.FactualQty>0)",$link_id);
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$StockId=$StockRows["StockId"];
		$OrderPO=$StockRows["OrderPO"];
		$OrderQty=$StockRows["OrderQty"];
		$StockQty=$StockRows["StockQty"];
		$AddQty=$StockRows["AddQty"];
		$FactualQty=$StockRows["FactualQty"];
        $Qty=$AddQty+$FactualQty;
	    $Price=$StockRows["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计
	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td  align='center'>$OrderPO</td>";
		echo"<td  align='center'>$StockId</td>";
		echo"<td  align='center'>$Price</td>";
		echo"<td  align='center'>$OrderQty</td>";
		echo"<td  align='center'>$StockQty</td>";
		echo"<td  align='center'>$FactualQty</td>";
		echo"<td  align='center'>$AddQty</td>";
		echo"<td  align='center'>$Qty</td>";
		echo"<td  align='center'>$Amount</td>";
		echo"</tr>";
		$i=$i+1;		
	}while ($StockRows = mysql_fetch_array($sListResult));
  }
else{
	echo"<tr><td height='30' colspan='6'>无相关的订单.</td></tr>";
	}

echo"</table>"."";

?>