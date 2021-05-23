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
echo"<table id='$TableId' width='610'  cellspacing='1' border='1' align='center'>
        <tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>&nbsp;</td>				
		<td width='70' align='center'>PO</td>
		<td width='150' align='center'>product code</td>
		<td width='280' align='center'>Description</td>
		<td width='80' align='center'>DeliveryQty</td>
		</tr>";

$sListResult = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.eCode,S.DeliveryQty,P.ProductId,P.TestStandard,P.Description
	FROM $DataIn.ch1_deliverysheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	WHERE S.Mid='$ShipId'",$link_id);
$i=1;
$sumDeliveryQty=0;
$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$Id=$StockRows["Id"];
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$POrderId=$StockRows["POrderId"];
		$ProductId=$StockRows["ProductId"];
		$TestStandard=$StockRows["TestStandard"];
		$Description=$StockRows["Description"]==""?"&nbsp;":$StockRows["Description"];
		$eCode=$StockRows["eCode"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
			}
  		$eCode=$eCode==""?"&nbsp;":$eCode;
		$DeliveryQty=$StockRows["DeliveryQty"];
		$sumDeliveryQty+=$DeliveryQty;
       $DeliveryStr="<span class='greenB'>$DeliveryQty</span>";
		echo"<tr bgcolor=#EAEAEA>";
		echo"<td align='center' height='20'>$i</td>";
		echo"<td  align='center'>$OrderPO</td>";	
		echo"<td  align='left'>$eCode</td>";
		echo"<td  align='left'>$Description</td>";
		echo"<td  align='right'>$DeliveryStr</td>";							
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
       $sumDeliveryQty="<span class='greenB'>$sumDeliveryQty</span>";
		echo"<tr bgcolor=#EAEAEA>";
		echo"<td align='center' height='20' colspan='4'>&nbsp;</td>";
		echo"<td  align='right'>$sumDeliveryQty</td>";						
		echo"</tr>";
	}
else{
	echo"<tr><td height='30' colspan='6'>no information</td></tr>";
	}
echo"</table>";
?>
