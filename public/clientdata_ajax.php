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
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>PO</td>
		<td width='80' align='center'>出货日期</td>
		<td width='80' align='center'>Invoice名称</td>
		<td width='80' align='center'>Invoice文档</td>
		<td width='50' align='center'>外箱标签</td>
		<td width='100' align='center'>流水号</td>				
		<td width='260' align='center'>中文名称</td>
		<td width='200' align='center'>Product Code</td>
		<td width='50' align='center'>数量</td>
		<td width='50' align='center'>单价</td>
		<td width='60' align='center'>金额</td>
		<td width='80' align='center'>单品重(g)</td>
		<td width='80' align='center'>成品重(g)</td>
		<td width='80' align='center'>整单重量(kg)</td>
                <td width='70' align='center'>下单日期</td>
                <td width='100' align='center'>PI交期</td>
                <td width='60' align='center'>交期差值</td>
                <td width='60' align='center'>交货周期</td>
                <td width='50' align='center'>生产商</td>
		</tr>";
//订单列表//LEFT JOIN yw1_ordermain M ON M.OrderNumber=O.OrderNumber

$sListResult = mysql_query("
SELECT S.Id,M.Id AS boxId,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,M.InvoiceNO,M.InvoiceFile,M.Sign,E.Leadtime,P.TestStandard,P.MainWeight,N.OrderDate AS orderDate,L.InvoiceModel,C.CompanyId
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
    LEFT JOIN $DataIn.ch8_shipmodel L ON L.Id=M.ModelId
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
    LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	WHERE C.CompanyId='$ShipId' AND S.Type='1' AND O.Estate='0'
	ORDER BY M.Date DESC
",$link_id);

$i=1;
$sumQty=0;
$sumAmount=0;
$sumWG=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
    $d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
		$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		$POrderId=$StockRows["POrderId"];
		$InvoiceNO=$StockRows["InvoiceNO"];
		$InvoiceFile=$StockRows["InvoiceFile"];
		$boxId=$StockRows["boxId"];
		$CompanyId=$StockRows["CompanyId"];
		$BoxLable="<div class='redB'>未装箱</div>";
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$boxId' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			//$bId=$PackingRow["Id"];
			$Parame1=anmaIn($boxId,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		$Sign=$StockRows["Sign"];//收支标记
		//echo "InvoiceNO=$InvoiceNO ";
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">查看</a>";
		if($CompanyId==1001  && $Sign!=-1){
			$d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
			$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
			}
        $InvoiceModel=$StockRows["InvoiceModel"];

		//if ($InvoiceModel==5){ //出MCA
		if ($InvoiceModel==5 || $CompanyId==1064){ //出MCA
                    $d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
                    $InvoiceFile.="&nbsp;&nbsp;<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
                }


		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$boxId'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;
		if($Amount<0){
			$Amount="<div class='redB'>$Amount</div>";
			}
		//检查收款情况
		 $checkShipAmount=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS ShipAmount
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$boxId' GROUP BY S.chId",$link_id));
		$ShipAmount=$checkShipAmount["ShipAmount"];
		$ShipAmount=$ShipAmount==""?0:round($ShipAmount,2);
		if(sprintf("%.2f",$Amount)-sprintf("%.2f",$ShipAmount)>0){//出货金额与收款金额一致，则为已收款
			 if($myRow["PayType"]==1){
				$BoxLable="<span class=\"redB\">未收款</span>";
				$OrderSignColor="bgColor='#F00'";
				}
			}

		$cName=$StockRows["cName"];
		$eCode=$StockRows["eCode"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
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
		$MainWeight=$StockRows["MainWeight"]==0?"&nbsp;":$StockRows["MainWeight"];

                //按产品类型区分生产商：鼠宝、研砼
                $scType=1;
                $checkResult = mysql_query("SELECT T.scType  
                      FROM yw1_ordersheet O 
                      LEFT JOIN productdata P ON P.ProductId=O.ProductId 
                      LEFT JOIN producttype T ON T.TypeId=P.TypeId WHERE O.POrderId='$POrderId'",$link_id);
                if($scTypeRow = mysql_fetch_array($checkResult)){
	              $scType=$scTypeRow["scType"];
	        }
                  $scType=$scType==2?"<div style='color:#F00;'>鼠 宝</div>":"黑 云";

                //交货日期、PI交期、差值
                $Date=$StockRows["Date"];
		$Leadtime=$StockRows["Leadtime"];
                if ($Leadtime!="" && strtotime($Leadtime)>0){
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
                }
                //下单日期、交货周期
                $orderDate=$StockRows["orderDate"];
                if ($orderDate!="" && strtotime($orderDate)>0){
                   $cycleDay=(strtotime($Date)-strtotime($orderDate))/3600/24;
                }
                else{
                   $cycleDay="&nbsp;";
                   $orderDate=$orderDate==""?"&nbsp;":$orderDate;
                }

                 //拆分订单
            $checkSplit=mysql_query("SELECT SPOrderId FROM $DataIn.yw1_ordersplit WHERE OPOrderId='$POrderId' LIMIT 1",$link_id);
			if($checkSplit && $splitRow = mysql_fetch_array($checkSplit)){
		            $SPOrderId=$splitRow["SPOrderId"];
                            $Qty="<a href='yw_order_split.php?Sid=$SPOrderId' target='_blank'><div style='color:#000000;Font-weight:bold;'>$Qty</div></a>";
                   }
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$OrderPO</td>";				//PO
		echo"<td  align='center'>$Date</td>";                   //出货日期
		echo"<td  align='center'>$InvoiceNO</td>";              //Invoice名称
		echo"<td  align='center'>$InvoiceFile</td>";            // InvoiceFile文档
		echo"<td  align='center'>$BoxLable</td>";				//外箱标签
		echo"<td  align='center'>$POrderId</td>";				//流水号
		echo"<td><DIV STYLE='width:280 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$TestStandard</NOBR></DIV></td>";//名称
		echo"<td>$eCode</td>";					//代码
		echo"<td align='right'>$Qty</td>";//订单需求数量
		echo"<td align='right'>$Price</td>";//使用库存数
		echo"<td align='right'>$Amount</td>";//金额
		echo"<td align='right'>$MainWeight</td>";//主产品重量
		echo"<td align='right'>$Weight</td>";//成品重量
		echo"<td align='right'>$WG</td>";	//整单重量
                echo"<td align='center'>$orderDate</td>";
                echo"<td align='center'>$Leadtime</td>";
                echo"<td align='center'>$diffday</td>";
                 echo"<td align='center'>$cycleDay 天</td>";
                echo"<td align='center'>$scType</td>";
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='9'>Total amount</td>";
		echo"<td align='right'>$sumQty</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumAmount</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>&nbsp;</td>";
		echo"<td align='right'>$sumWG</td>";
                echo"<td colspan='9'>&nbsp;</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td height='30'>Nothing</td></tr>";
	}
echo"</table>";
?>