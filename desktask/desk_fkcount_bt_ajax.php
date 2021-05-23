<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.stuffdata
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=780;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='95' align='center'>流水号</td>
		<td width='210' align='center'>配件名称</td>
		<td width='60' align='center'>需求数量</td>
		<td width='60' align='center'>增购数量</td>
		<td width='60' align='center'>实购数量</td>
		<td width='60' align='center'>单价</td>
		<td width='60' align='center'>金额</td>
		<td width='80' align='center'>应付金额RMB</td>
		<td width='79' align='center'>已付金额RMB</td>
		<td width='76' align='center'>未付金额RMB</td>
	</tr>";

//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$MonthTemp=$TempArray[1];
$predivNum=$TempArray[2];
$mySql="
SELECT S.Mid,S.StockId,S.FactualQty,S.AddQty,S.Price,D.StuffCname,C.Rate
FROM $DataIn.cw1_tkoutsheet S
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE S.Mid=0 AND S.Month='$MonthTemp' AND S.CompanyId='$CompanyId'
ORDER BY S.Mid,S.StockId";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$AmountSum=0;
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$StockId=$myRow["StockId"];
		$StuffCname=$myRow["StuffCname"];
		$Rate=$myRow["Rate"];
		$FactualQty=$myRow["FactualQty"];
		$AddQty=$myRow["AddQty"];
		$Qty=$FactualQty+$AddQty;
		$Price=$myRow["Price"];
		$Amount=$Qty*$Price;
		$Mid=$myRow["Mid"];
		$AmountRMB1=number_format($Amount*$Rate,2);
		if($Mid==0){
			$AmountRMB3=$AmountRMB1;
			}
		else{
			$AmountRMB2=$AmountRMB1;
			}
		
		$POrderId=$myRow["POrderId"];
		$OrderDate=$myRow["OrderDate"];
		echo"
			<tr bgcolor='#FFFFFF'>
				<td height='20' align='center'>$i</td>
				<td align='center'>$StockId</td>
				<td>$StuffCname</td>
				<td align='right'>$FactualQty</td>
				<td align='right'>$AddQty</td>
				<td align='right'>$Qty</td>
				<td align='right'>$Price</td>
				<td align='right'>$Amount</td>
				<td align='right'>$AmountRMB1</td>
				<td align='right'><div class='greenB'>$AmountRMB2</div></td>
				<td align='right'><div class='redB'>$AmountRMB3</div></td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
/*
echo"
	<tr bgcolor='#99FF99'>
		<td height='20' align='center' colspan='5'>合 计</td>
		<td align='right'>$QtySum</td>
		<td align='right'>$AmountSum</td>
		<td align='center'>&nbsp;</td>
	</tr>
</table>";*/
?>