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
echo"<table id='$TableId' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='40' align='center'>Item</td>
		<td width='80' align='center'>PO</td>
		<td width='200' align='center'>Product Code</td>
		<td width='60' align='center'>QTY</td>
		<td width='70' align='center'>Unit price</td>
		<td width='70' align='center'>Amount</td>
		<td width='90' align='center'>Weight/unit(G)</td>
		<td width='90' align='center'>Weight/ctn(KG)</td>
		<td width='80' align='center'>Order Date</td>
		<td width='80' align='center'>Deliverydate In PI</td>
		<td width='80' align='center'>Shipping Date</td>
		<td width='80' align='center'>Days of Difference</td>
		<td width='80' align='center'>Running Days</td>
		</tr>";
//订单列表//LEFT JOIN yw1_ordermain M ON M.OrderNumber=O.OrderNumber 
$sListResult = mysql_query("
SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,
P.Weight AS Weight,P.TestStandard,M.Date,E.Leadtime,N.OrderDate AS orderDate 
	FROM ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
	LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	WHERE S.Mid='$ShipId' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,O.Weight AS Weight,'' AS TestStandard,M.Date,'' AS Leadtime,O.Date AS orderDate 
	FROM $DataIn.ch1_shipsheet S
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,'0' AS Weight ,'' AS TestStandard,M.Date,'' AS Leadtime,O.Date AS orderDate 
	FROM $DataIn.ch1_shipsheet S
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='3'
",$link_id);
$i=1;
$sumQty=0;
$sumAmount=0;
$sumWG=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$POrderId=$StockRows["POrderId"];
		$cName=$StockRows["cName"];
		$eCode=$StockRows["eCode"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/Client_getPOrderImage.php";
		$Qty=$StockRows["Qty"];
		$Price=$StockRows["Price"];
		$Type=$StockRows["Type"];
		$YandN=$StockRows["YandN"];
		$Amount=sprintf("%.2f",$Qty*$Price);	
		$sumQty=$sumQty+$Qty;
		$sumAmount=sprintf("%.2f",$sumAmount+$Amount);
		$Weight=$StockRows["Weight"];
		$WG=ceil(($Weight*$Qty)/1000);//整单重量
		$sumWG+=$WG;
		 $checkSplit=mysql_query("SELECT SPOrderId FROM $DataIn.yw1_ordersplit WHERE OPOrderId='$POrderId' LIMIT 1",$link_id);
			if($splitRow = mysql_fetch_array($checkSplit)){
		            $SPOrderId=$splitRow["SPOrderId"]; 
                            $Qty="<a href='client_order_split.php?Sid=$SPOrderId' target='_blank'><div style='color:#000000;Font-weight:bold;'>$Qty</div></a>";
                            
                        }
		$Date=$StockRows["Date"];
		$Leadtime=$StockRows["Leadtime"];
		if ($Leadtime!="" && strtotime($Leadtime)>0){
                   $diffday=(strtotime($Date)-strtotime($Leadtime))/3600/24;
                   if ($diffday<=0){
                       if ($diffday<-5) {
                           $diffday="<div class='greenB'>↑ " . abs($diffday) . "day</div>";
                          }
                      else{
                           $diffday="<span class='greenB'>↑ </span>" . abs($diffday) . "day";
                         }
                    }
                    else{
                        if ($diffday>5) { 
                               $diffday="<div class='redB'>↓ " . abs($diffday) . "day</div>";
                           }
                           else {
                               $diffday="<span class='redB'>↓ </span>" . abs($diffday) . "day";
                           }
                     }
                }          
                else{
                   $diffday="&nbsp;"; 
                   $Leadtime=$Leadtime==""?"&nbsp;":$Leadtime;
                }
		$orderDate=$StockRows["orderDate"];
                if ($orderDate!="" && strtotime($orderDate)>0){
                   $cycleDay=(strtotime($Date)-strtotime($orderDate))/3600/24;
                }          
                else{
                   $cycleDay="&nbsp;"; 
                   $orderDate=$orderDate==""?"&nbsp;":$orderDate;
                }
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$OrderPO</td>";				//PO				
		echo"<td>$TestStandard</td>";//名称
		//echo"<td>$eCode</td>";					//代码
		echo"<td align='right'>$Qty</td>";//订单需求数量
		echo"<td align='right'>$Price</td>";//使用库存数
		echo"<td align='right'>$Amount</td>";//金额
		echo"<td align='right'>$Weight</td>";//单品重量
		echo"<td align='right'>$WG</td>";	//整单重量
		echo"<td align='center'>$orderDate</td>";
        echo"<td align='center'>$Leadtime</td>";
		echo"<td align='center'>$Date</td>";
		echo"<td align='center'>$diffday</td>";
		echo"<td align='center'>$cycleDay</td>";
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='3'>Total amount</td>";
		echo"<td align='right'>$sumQty</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumAmount</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumWG</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td height='30'>Nothing</td></tr>";
	}
echo"</table>";
?>