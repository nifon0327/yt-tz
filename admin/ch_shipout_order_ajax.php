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
echo"<table id='$TableId' cellspacing='1' border='1' align='center'>
        <tr bgcolor='#CCCCCC'>
		<td width='40' align='center'>序号</td>
		<td width='80' align='center'>订单流水号</td>				
		<td width='80' align='center'>PO</td>
		<td width='180' align='center'>中文名</td>
		<td width='100' align='center'>product code</td>
		<td width='60' align='center'>需提货数量</td>
		<td width='60' align='center'>已提货数量</td>
		<td width='60' align='center'>单价</td>
		<td width='60' align='center'>已提货金额</td>
		</tr>";
$sListSql ="SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.DeliveryQty,S.Price,S.Type,
    P.TestStandard
	FROM $DataIn.ch1_deliverysheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	WHERE S.Mid='$Mid' AND S.Type='1'
    UNION ALL
	SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description 
	AS eCode,S.DeliveryQty,S.Price,S.Type,'' AS TestStandard
	FROM $DataIn.ch1_deliverysheet S 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$Mid' AND S.Type='2'
    UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.DeliveryQty,S.Price,
	S.Type,'' AS TestStandard
	FROM $DataIn.ch1_deliverysheet S 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$Mid' AND S.Type='3'";
	
//if ($Login_P_Number==10868) echo $sListSql;
$sListResult = mysql_query($sListSql,$link_id);
$i=1;

$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$Id=$StockRows["Id"];
		$DeliveryQty=$StockRows["DeliveryQty"];
		$Price=$StockRows["Price"];
		$DeliveryAmount=$DeliveryQty*$Price;
		$DeliveryAmount=sprintf("%.2f",$DeliveryAmount)==0?"&nbsp;":sprintf("%.2f",$DeliveryAmount);
		$Price=sprintf("%.2f",$Price)==0?"&nbsp;":sprintf("%.2f",$Price);
		
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$POrderId=$StockRows["POrderId"];
		$cName=$StockRows["cName"];
		$eCode=$StockRows["eCode"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		
		$TotalResult=mysql_query("SELECT  Qty AS TotalQty FROM $DataIn.ch1_shipsheet WHERE  POrderId='$POrderId' ",$link_id);
		$TotalQty =mysql_result($TotalResult,0,"TotalQty");

		echo"<tr bgcolor=#EAEAEA>";
		echo"<td align='center' height='20'>$i</td>";	//序号
		echo"<td  align='center'>$POrderId</td>";	
		echo"<td  align='center'>$OrderPO</td>";	
		echo"<td  align='left'>$TestStandard</td>";	
		echo"<td  align='left'>$eCode</td>";
		echo"<td  align='right'>$TotalQty</td>";	
		echo"<td  align='right'>$DeliveryQty</td>";	
		echo"<td  align='right'>$Price</td>";	
		echo"<td  align='right'>$DeliveryAmount</td>";							
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));	
	}
else{
	echo"<tr><td height='30' colspan='6'>没有相关记录</td></tr>";
	}
echo"</table>";
?>
