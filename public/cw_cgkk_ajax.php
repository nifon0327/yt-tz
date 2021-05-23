<?php 
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$tableWidth=1030;
echo"<table id='$TableId'  cellspacing='1' border='1' align='right'>
	<tr bgcolor='#CCCCCC'>
	<td width='20' height='20'></td>
	<td width='80' align='center'>采购单号</td>
	<td width='90' align='center'>采购单流水号</td>
	<td width='280' align='center'>配件名称</td>
	<td width='60' align='center'>扣款数量</td>
	<td width='60' align='center'>单价</td>
	<td width='60' align='center'>扣款金额</td>
	<td width='120' align='center'>扣款原因</td>
	</tr>";
$i=1;
$mySql="SELECT S.Id,S.PurchaseID,S.StockId,S.Qty,S.Price,S.Amount,S.Remark,S.StuffName
		          FROM $DataIn.cw15_gyskksheet S
		          LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
				  WHERE S.Mid='$Id'";
//echo $mySql;
$TotalQty=0;
$TotalAmount=0;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	   $Id=$myRow["Id"];
	   $PurchaseID=$myRow["PurchaseID"]==0?"&nbsp;":$myRow["PurchaseID"];
	   $StockId=$myRow["StockId"]==0?"&nbsp;":$myRow["StockId"];
	   $Qty=$myRow["Qty"];
	   $TotalQty+=$Qty;
	   $Price=$myRow["Price"];
	   $Amount=$myRow["Amount"];
	   $TotalAmount+=$Amount;
	   $Remark=$myRow["Remark"]=="&nbsp;"?:$myRow["Remark"];
	   $StuffName=$myRow["StuffName"];
		echo "<tr bgcolor='#FFFFFF'>
		      <td align='center'>$i</td>
			  <td align='center'>$PurchaseID</td>
			  <td align='center'>$StockId</td>
			  <td align='center'>$StuffName</td>
			  <td align='right'>$Qty</td>
			  <td align='right'>$Price</td>
			  <td align='right'>$Amount</td>
			  <td align='center'>$Remark</td>
		      </tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
		echo "<tr bgcolor='#FFFFFF'>
		      <td colspan=4 align='right'>Total</td>
		      <td align='right'>$TotalQty</td>
			  <td>&nbsp;</td>
			  <td align='right'>$TotalAmount</td>
			  <td>&nbsp;</td></tr>";
		echo "</table>";
	}
?>
