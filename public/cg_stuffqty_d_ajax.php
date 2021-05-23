<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.ck1_rksheet
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//需求单列表
$TempArray=explode("|",$TempId);
$StuffId=$TempArray[0];
$BuyerId=$TempArray[1];

		
echo"<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
echo"<tr bgcolor='timeTop'>
<td align='center' class='A1111' width='38' height='20'>序号</td>
<td align='center' class='A1101' width='80'>采购日期</td>
<td align='center' class='A1101' width='80'>收货记录</td>
<td align='center' class='A1101' width='80'>流水号</td>
<td align='center' class='A1101' width='60'>使用库存</td>
<td align='center' class='A1101' width='80'>订单数量</td>
<td align='center' class='A1101' width='80'>采购总数</td>
<td align='center' class='A1101' width='80'>已收数量</td>
<td align='center' class='A1101' width='80'>未收数量</td>
<td align='center' class='A1101' width='80'>供应商</td>
<td align='center' class='A1101' width='80'>交货日期</td>
</tr>";

$mySql="SELECT M.Date,S.StockId,S.POrderId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,P.Forshort,S.DeliveryDate 
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.cg1_stockmain M  ON M.Id=S.Mid
LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
WHERE 1 and S.Mid>0 and S.StuffId='$StuffId'  AND S.BuyerId='$BuyerId' and S.rkSign>0 GROUP BY S.Id ORDER BY S.Id";
$myResult = mysql_query($mySql."",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$j=1;$sumOrderQty=0;$sumStockQty=0;$sumAddQty=0;$sumRkQty=0;$sumNoQty=0;$oldStockId="";
	do{
		$Date=$myRow["Date"];
		$StockId=$myRow["StockId"];
		//$oldStockId=$oldStockId==""?$StockId:$oldStockId;
		if($oldStockId!=$StockId)  //相同的不显示，会重得2011-11-05
		{
			$oldStockId=$oldStockId; 
			$POrderId=$myRow["POrderId"];
			$OrderQty=$myRow["OrderQty"];		$sumOrderQty+=$OrderQty;
			$StockQty=$myRow["StockQty"];		$sumStockQty+=$StockQty;
			$AddQty=$myRow["AddQty"];			
			$FactualQty=$myRow["FactualQty"];	
			$Forshort=$myRow["Forshort"];
			$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"未设置":$myRow["DeliveryDate"];
			$Qty=$FactualQty+$AddQty;			$sumQty+=$Qty;
			//已收货总数
			$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R,$DataIn.cg1_stocksheet S WHERE 
			S.StockId=R.StockId 
			$BuyerSTR $CompanySTR 
			and R.StockId='$StockId'",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			$noQty=$Qty-$rkQty;
			$sumRkQty+=$rkQty;
			$sumNoQty+=$noQty;
			
			//入库记录
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$rkList="<a href='../public/ck_rk_list.php?Sid=$Sid' target='_black'>查看</a>";
			echo"<tr>
					<td align='center' class='A0111' height='20'>$j</td>
					<td align='center' class='A0101'>$Date</td>
					<td align='center' class='A0101'>$rkList</td>
					<td align='center' class='A0101'>$StockId</td>
					<td align='right' class='A0101'>$StockQty</td>
					<td align='right' class='A0101'>$OrderQty</td>
					<td align='right' class='A0101'>$Qty</td>
					<td align='right' class='A0101'>$rkQty</td>
					<td align='right' class='A0101'>$noQty</td>
					<td align='center' class='A0101'>$Forshort</td>
					<td align='center' class='A0101'>$DeliveryDate</td>
					</tr>";
					$j++;
					
		}
	}while ($myRow = mysql_fetch_array($myResult));
echo"<tr>
		<td align='center' class='A0111' height='20' colspan='4'>合计</td>
		<td align='right' class='A0101'>$sumStockQty</td>
		<td align='right' class='A0101'>$sumOrderQty</td>
		<td align='right' class='A0101'>$sumQty</td>
		<td align='right' class='A0101'>$sumRkQty</td>
		<td align='right' class='A0101'>$sumNoQty</td>
		<td align='center' class='A0101'>&nbsp;</td>
		<td align='center' class='A0101'>&nbsp;</td>
		</tr>";
	}
echo"<table>";
?>