<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;  //产品属性|70|
echo"<table id='$TableId' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>PO</td>
		<td width='70' align='center'>产品属性</td>
		<td width='100' align='center'>流水号</td>				
		<td width='220' align='center'>中文名称</td>
		<td width='150' align='center'>Product Code</td>
		<td width='50' align='center'>数量</td>
		<td width='50' align='center'>单价</td>
		<td width='60' align='center'>金额</td>
		<td width='55' align='center'>单品重(g)</td>
		<td width='55' align='center'>成品重(g)</td>
		<td width='60' align='center'>整单重(kg)</td>
        <td width='70' align='center'>下单日期</td>
        <td width='70' align='center'>PI交期</td>
        <td width='50' align='center'>物料</td>
        <td width='50' align='center'>备料</td>
        <td width='50' align='center'>组装</td>
        <td width='50' align='center'>待出</td>
        <td width='50' align='center'>交货周期</td>
		</tr>";
//订单列表//LEFT JOIN yw1_ordermain M ON M.OrderNumber=O.OrderNumber
$sListSql = "
SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,E.Leadtime,P.TestStandard,P.MainWeight,N.OrderDate AS OrderDate ,P.ProductId,N.ClientOrder,P.buySign
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
    LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$ShipId' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,O.Weight AS Weight,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight,O.Date AS OrderDate  ,'' AS ProductId,'' AS ClientOrder,-1 as buySign
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,'0' AS Weight ,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight,O.Date AS OrderDate,'' AS ProductId,'' AS ClientOrder,-1 as buySign
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='3'
";
//echo $sListSql;
$sListResult = mysql_query($sListSql,$link_id);

$i=1;
$sumQty=0;
$sumAmount=0.00;
$sumWG=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$POrderId=$StockRows["POrderId"];
		$cName=$StockRows["cName"];
		$eCode=$StockRows["eCode"];
		$ProductId=$StockRows["ProductId"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
        $ClientOrder=$StockRows["ClientOrder"];
        if($ClientOrder!=""){//原单在序号列显示
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);
			$OrderPO="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$OrderPO</a>";
			}
		$Qty=$StockRows["Qty"];
		$Price=$StockRows["Price"];
		$Type=$StockRows["Type"];
		$YandN=$StockRows["YandN"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		//$Amount=round($Qty*$Price,2);
		$sumQty=$sumQty+$Qty;
		//$sumAmount=sprintf("%.2f",$sumAmount+$Amount);
		$sumAmount=$sumAmount+$Amount;
		$Weight=$StockRows["Weight"];
		$WG=ceil(($Weight*$Qty)/1000);//整单重量
		$sumWG+=$WG;
		$MainWeight=$StockRows["MainWeight"]==0?"&nbsp;":$StockRows["MainWeight"];


        //交货日期、PI交期、差值
        $Date=$StockRows["Date"];
		$Leadtime=$StockRows["Leadtime"];

        /*if ($Leadtime!="" && strtotime($Leadtime)>0){
           $diffday=(strtotime($Date)-strtotime($Leadtime))/3600/24;
           if ($diffday<=0){
               if ($diffday<-5) {
                   $diffday="<div class='greenB'>↑ " . abs($diffday) . "天</div>";
                  }
              else{
                   $diffday="<span class='greenB'>↑ </span>" . abs($diffday) . "天";
                 }
            }
            else{
                if ($diffday>5) {
                       $diffday="<div class='redB'>↓ " . abs($diffday) . "天</div>";
                   }
                   else {
                       $diffday="<span class='redB'>↓ </span>" . abs($diffday) . "天";
                   }
             }
        }
        else{
           $diffday="&nbsp;";
           $Leadtime=$Leadtime==""?"&nbsp;":$Leadtime;
        }*/
        //下单日期、交货周期
        $OrderDate=$StockRows["OrderDate"];
        include "order_date.php";//备料周期，组装周期，待出周期
        if ($OrderDate!="" && strtotime($OrderDate)>0){
           $cycleDay=((strtotime($Date)-strtotime($OrderDate))/3600/24)."天";
        }
        else{
           $cycleDay="&nbsp;";
           $OrderDate=$OrderDate==""?"&nbsp;":$OrderDate;
        }
        include "../model/subprogram/PI_Leadtime.php";

		$buySign=$StockRows["buySign"];
        $checkProperty = mysql_fetch_array(mysql_query("SELECT Name FROM $DataIn.product_property WHERE Id = '$buySign'",$link_id));	    $buySignName = $checkProperty["Name"];

		$POrderId="<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'>$POrderId</a>";

		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$OrderPO</td>";				//PO
		echo"<td  align='center'>$buySignName</td>";
		echo"<td  align='center'>$POrderId</td>";				//流水号
		echo"<td><DIV STYLE='width:280 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$TestStandard</NOBR></DIV></td>";//名称
		echo"<td>$eCode</td>";					//代码
		echo"<td align='right'>$Qty</td>";//订单需求数量
		echo"<td align='right'>$Price</td>";//使用库存数
		echo"<td align='right'>$Amount</td>";//金额
		echo"<td align='right'>$MainWeight</td>";//主产品重量
		echo"<td align='right'>$Weight</td>";//成品重量
		echo"<td align='right'>$WG</td>";	//整单重量
        echo"<td align='center'>$OrderDate</td>";
        echo"<td align='center'>$Leadtime</td>";
        echo"<td align='center'>$wl_cycle</td>";
        echo"<td align='center'>$bl_cycle</td>";
        echo"<td align='center'>$sc_cycle</td>";
        echo"<td align='center'>$sctj_date</td>";
        echo"<td align='center'>$cycleDay</td>";
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
	    $sumAmount=sprintf("%.2f",$sumAmount);
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='6'>Total amount</td>";
		echo"<td align='right'>$sumQty</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumAmount</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumWG</td>";
         echo"<td colspan='7'>&nbsp;</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td height='30'>Nothing</td></tr>";
	}
echo"</table>";
?>