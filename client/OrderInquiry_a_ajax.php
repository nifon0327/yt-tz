<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TempArray=explode("|",$TempId);
$ProductId=$TempArray[0];
$predivName=$TempArray[1];
//已出货
$checkShipSql=mysql_query("SELECT S.Qty,M.InvoiceNO,M.InvoiceFile,M.Date,YS.OrderPO 
FROM $DataIn.ch1_shipsheet S,$DataIn.ch1_shipmain M,$DataIn.yw1_ordersheet YS
WHERE M.Estate=0 AND M.Id=S.Mid AND S.ProductId='$ProductId' AND S.POrderId=YS.POrderId  ORDER BY M.Date DESC",$link_id);
if($checkShipRow=mysql_fetch_array($checkShipSql)){
	$i=1;
	echo"
	<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' width='272'>
	<tr bgcolor='#FFCC99'>
	<td align='center'  height='20'>Shipment</td>
	</tr>
	</table>
	<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#FFFFFF'>
	<td align='center' width='40' height='20'>Item</td>
	<td align='center' width='80'>DeliveryDate</td>
	<td align='center' width='120'>PO</td>
	<td align='center' width='150'>InvoiceNO</td>
	<td align='center' width='60'>Qty</td>
	<td align='center' width='60'>&nbsp;</td>
	</tr>
	";
	do{
		$Date=date("d-M-y",strtotime($checkShipRow["Date"]));
		$OrderPO=$checkShipRow["OrderPO"];
		$InvoiceFile=$checkShipRow["InvoiceFile"];
		$InvoiceNO=$checkShipRow["InvoiceNO"];
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
		$InvoiceNO=$InvoiceFile==0?$InvoiceNO:"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNO</span>";
		$Qty=$checkShipRow["Qty"];
		echo"
		<tr bgcolor='#FFFFFF'>
		<td align='center' height='20'>$i</td>
		<td align='center'>$Date</td>
		<td>$OrderPO</td>
		<td>$InvoiceNO</td>
		<td align='right'>$Qty</td>
		<td align='center'>&nbsp;</td>
		</tr>
		";
		$i++;
		}while($checkShipRow=mysql_fetch_array($checkShipSql));
	echo "</table><p>";
	}


//未出货
$unSipSql=mysql_query("SELECT M.OrderDate,S.OrderPO,S.Qty
FROM $DataIn.yw1_ordersheet S,$DataIn.yw1_ordermain M  
WHERE S.Estate>0 AND M.OrderNumber=S.OrderNumber AND S.ProductId='$ProductId' ORDER BY M.OrderDate DESC",$link_id);
if($unSipRow=mysql_fetch_array($unSipSql)){
	$i=1;
	echo"
	<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' width='272'>
	<tr bgcolor='#FFCC99'>
	<td align='center'  height='20'>Order Status</td>
	</tr>
	</table>
	<table cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#FFFFFF'>
	<td align='center' width='40' height='20'>Item</td>
	<td align='center' width='80'>OrderDate</td>
	<td align='center' width='270'>PO</td>
	<td align='center' width='60'>&nbsp;</td>
	<td align='center' width='60'>Qty</td>
	</tr>
	";
	do{
		$OrderDate=date("d-M-y",strtotime($unSipRow["OrderDate"]));
		$OrderPO=$unSipRow["OrderPO"];
		$Qty=$unSipRow["Qty"];
		echo"
		<tr bgcolor='#FFFFFF'>
		<td align='center' height='20'>$i</td>
		<td align='center'>$OrderDate</td>
		<td>$OrderPO</td>
		<td align='center'>&nbsp;</td>
		<td align='right'>$Qty</td>
		</tr>
		";
		$i++;
		}while($unSipRow=mysql_fetch_array($unSipSql));
	echo "</table>";
	}

?>
