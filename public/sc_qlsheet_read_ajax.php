<?php 
/*
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$searchBuyer=$$BuyerId==""?"":" AND S.BuyerId='$BuyerId'";
$subTableWidth=1350;
//来自于生产登记页面
	echo"<table id='$TableId'  cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
	<td width='10' height='20'></td>
	<td width='80' align='center'>供应商</td>
	<td width='80' align='center'>采购日期</td>
	<td width='90' align='center'>待购流水号</td>
	<td width='60' align='center'>需购数量</td>
	<td width='60' align='center'>增购数量</td>
	<td width='60' align='center'>实际采购</td>
	<td width='60' align='center'>已收货</td>
	<td width='60' align='center'>欠数</td>
	<td width='80' align='center'>预定交期</td>
	</tr>";
	$sListResult = mysql_query("SELECT 
		M.Date,S.StockId,S.AddQty,S.FactualQty,S.DeliveryDate,C.Forshort
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
		WHERE S.rkSign>0 AND S.Mid>0 AND S.StuffId='$StuffId' $searchBuyer ORDER BY S.StockId",$link_id);
	$i=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if($StockRows = mysql_fetch_array($sListResult)) {
		do{
/////////////////////////////
			//初始化
			$rkQty=0;$thQty=0;		$bcQty=0;		$llQty=0;	$scQty="-";
			$OnclickStr="";
			$StockId=$StockRows["StockId"];
			$StuffCname=$StockRows["StuffCname"];
			$Forshort=$StockRows["Forshort"];
			$FactualQty=$StockRows["FactualQty"];
			$AddQty=$StockRows["AddQty"];
			$cgQty=$FactualQty+$AddQty;
			$Date=$StockRows["Date"];
			$DeliveryDate=$StockRows["DeliveryDate"]=="0000-00-00"?"&nbsp;":$StockRows["DeliveryDate"];		
			//收货情况				
			$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$Mantissa=$FactualQty+$AddQty-$rkQty;
			if($Mantissa>0){
			echo"<tr bgcolor='$theDefaultColor'>
			<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//配件状态
			echo"<td  align='center'>$Forshort</td>";		//供应商
			echo"<td  align='center'>$Date</td>";		//采购日期
			echo"<td  align='center'>$StockId</td>";	//采购流水号
			echo"<td align='right'>$FactualQty</td>";	//订单需求数量
			echo"<td align='right'>$AddQty</div></td>";	//增购数量
			echo"<td  align='center'>&nbsp;</td>";		//实际采购数量
			echo"<td  align='center'>$rkQty</td>";		//已收数量
			echo"<td align='center'><span class='redB'>$Mantissa</span></td>";	//未收数量
			echo"<td  align='center'>$DeliveryDate</td>";		//预定交期
			echo"</tr>";
			$i++;
			}
/////////////////////////////////////
			}while ($StockRows = mysql_fetch_array($sListResult));	
		}
	echo"</table>";
?>