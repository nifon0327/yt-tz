<?php  
//佣金 OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=1220;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='100' align='center'>客户</td>
		<td width='100' align='center'>采购流水号</td>
		<td width='230' align='center'>配件名称</td>
		<td width='55' align='center'>订单数</td>
		<td width='55' align='center'>使用库存</td>
		<td width='55' align='center'>需求数</td>
		<td width='55' align='center'>增购数</td>
		<td width='55' align='center'>实购数</td>
		<td width='55' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='60' align='center'>金额</td>
		<td width='40' align='center'>货币</td>
		<td width='80' align='center'>出货日期</td>
		<td width='100' align='center'>Invoice</td>
		<td width='40' align='center'>状态</td>
		<td width='50' align='center'>采购员</td>
	</tr></table>";
$SearchRows=" AND S.Estate=3 AND S.Month='$MonthTemp'";
$mySql="SELECT 
	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,P.Forshort,M.Name,D.StuffCname,U.Name AS UnitName,H.Date as OutDate,D.TypeId,H.InvoiceNO,H.InvoiceFile,CD.Symbol,P.Forshort
 	FROM $DataIn.cw1_tkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
    LEFT JOIN $DataPublic.currencydata  CD ON CD.Id=P.Currency
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
    Left Join $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid	
	WHERE 1 $SearchRows ORDER BY H.InvoiceNO DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
     $d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);	
	do{
$m=1;
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];//采购流水号
		$OutStockId=$StockId;
		$StuffCname=$myRow["StuffCname"];//配件名称
		$Buyer=$myRow["Name"];//采购		
		$Forshort=$myRow["ForshortName"];//供应商
		$OrderQty=$myRow["OrderQty"];		//订单数量		
		$StockQty=$myRow["StockQty"];	//需求数量
		$FactualQty=$myRow["FactualQty"];	//需求数量
		$AddQty=$myRow["AddQty"];			//增购数量	
		$Qty=$FactualQty+$AddQty;	//采购总数
		$TypeId=$myRow["TypeId"];
		$Price=$myRow["Price"];	//采购价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"];    
        $InvoiceNO=$myRow["InvoiceNO"];
        $InvoiceFile=$myRow["InvoiceFile"];
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceNO=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target='_blank'>$InvoiceNO</a>";
		$Estate="<div class='redB'>未付</div>";
		//统计
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计	
		$Symbol=$myRow["Symbol"];
		$Forshort=$myRow["Forshort"];
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='100' align='center'>$Forshort</td>
				<td width='100' align='center'>$StockId</td>
				<td width='230' >$StuffCname</td>
				<td width='55' align='center'>$OrderQty</td>
                <td width='55' align='center'> $StockQty</td>
				<td width='55' align='center'>$FactualQty</td>
				<td width='55' align='center'>$AddQty</td>
				<td width='55' align='center' >$Qty</td>
				<td width='55' align='center'>$Price</td>
				<td width='45' align='center' >$UnitName</td>
				<td width='60' align='center'>$Amount</td>
				<td width='40' align='center'>$Symbol</td>
				<td width='80' align='center' >$OutDate</td>
				<td width='100' align='center'>$InvoiceNO</td>
				<td width='40' align='center' >$Estate</td>
				<td width='50' align='center' >$Buyer</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>