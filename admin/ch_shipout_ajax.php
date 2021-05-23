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
$subTableWidth=800;
echo"<table id='$TableId' cellspacing='1' border='1' align='center' width='$subTableWidth'>
        <tr bgcolor='#CCCCCC'>
        <td width='30' align='center'>&nbsp;</td>
		<td width='40' align='center'>序号</td>
		<td width='120' align='center'>出货流水号</td>				
		<td width='160' align='center'>Invoice名称</td>
		<td width='150' align='center'>Invoice文档</td>
		<td width='100' align='center'>出货数量</td>
		<td width='100' align='center'>本次提货</td>
		<td width='100' align='center'>提货金额</td>
		</tr>";
$sListResult = mysql_query("SELECT  SUM(S.DeliveryQty) AS DeliveryQty,S.ShipId,SUM(S.DeliveryQty*S.Price) AS DeliveryAmount
FROM  $DataIn.ch1_deliverymain M
LEFT JOIN $DataIn.ch1_deliverysheet S ON S.Mid=M.Id  			 			 
WHERE M.Id='$Id' GROUP BY S.ShipId",$link_id);
$i=1; 
$TotalQty=0;
$TotalDelivery=0;
$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$ShipId=$StockRows["ShipId"];
		$DeliveryQty=$StockRows["DeliveryQty"];
		$DeliveryAmount=sprintf("%.2f",$StockRows["DeliveryAmount"]);
		$TotalDelivery+=$DeliveryQty;
		$MainResult=mysql_query("SELECT M.Number,M.InvoiceNO,M.InvoiceFile,SUM(S.Qty) AS SumQty
		    FROM $DataIn.ch1_shipmain M
			LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id
			WHERE M.Id='$ShipId' GROUP BY M.Id",$link_id);
		if($MainRow=mysql_fetch_array($MainResult)){
		  $Number=$MainRow["Number"];
		  $InvoiceNO=$MainRow["InvoiceNO"];
		  $InvoiceFile=$MainRow["InvoiceFile"];
		  $SumQty=$MainRow["SumQty"];
		  }
        $TotalQty+=$SumQty; 
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">view</a>";   
		$OrderList="OrderList".$RowId.$i;
		$shoucut="shoucut".$RowId.$i;
		$HideDiv="HideDiv".$RowId.$i;
	    $showPurchaseorder="<img name='$shoucut' id= '$shoucut' onClick='ShowOrderHidden($OrderList,$shoucut,$OrderList,$RowId,$i,\"$Id\")' src='../images/showtable.gif' alt='显示订单' width='13' height='13' style='CURSOR: pointer'>";
	 
	   $HideTableHTML="<tr id='$OrderList' style='display:none' bgcolor='#EAEAEA'>
				     <td colspan='8' align='left'>
			          <table width='$subTableWidth' border='0' cellspacing='0'>
				       <tr bgcolor='#B7B7B7'>
					       <td class='A0000' height='20'>
						       <br>
							       <div id='$HideDiv' width='200'>&nbsp;</div>
						       <br>
					       </td>
				         </tr>
			           </table></td></tr>";
		
		echo"<tr bgcolor=#EAEAEA>";
		echo"<td  align='center' height='20'>$showPurchaseorder</td>";
		echo"<td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$Number</td>";	
		echo"<td  align='center'>$InvoiceNO</td>";	
		echo"<td  align='center'>$InvoiceFile</td>";	
		echo"<td  align='right'>$SumQty</td>";	
		echo"<td  align='right'>$DeliveryQty</td>";	
		echo"<td  align='right'>$DeliveryAmount</td>";							
		echo"</tr>";
		$i++;
		echo $HideTableHTML;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	
	}
else{
	echo"<tr><td height='30'>没有相关记录</td></tr>";
	}
echo"</table>";
?>
