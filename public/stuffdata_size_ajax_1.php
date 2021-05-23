<?php 
/*$DataIn.电信---yang 20120801
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=780;
/*
echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='80' align='center'>订单PO</td>
		<td width='90' align='center'>内部单号</td>
		<td width='330' align='center'>产品名称</td>				
		<td width='55' align='center'>订单数</td>
		<td width='55' align='center'>本次完成</td>
		<td width='55' align='center'>总完成进度（%）</td>
		<td width='55' align='center'>组装总时间(分)</td>
		<td width='55' align='center'>人数</td>
		<td width='55' align='center'>人力(RMB)/单品</td>
		<td width='55' align='center'>人力总计(RMB)</td>	
		";
*/
//width='$subTableWidth'
echo"<table id='$TableId' cellspacing='1' border='1' align='center' tyle='table-layout:fixed;word-break:break-all; word-wrap:break-word;'><tr bgcolor='#CCCCCC'>
		<td width='30' height='20'>序号</td>
		<td width='80' align='center'>客户</td>
		<td width='50'  align='center'>产品ID</td>
		<td width='250'  align='center'>中文名</td>		
		<td width='150' align='center'>Product Code</td>
		<td width='200' align='center'>外箱配件名称</td>
		";

echo "</tr>";

//$Th_Col="选项|45|序号|45|日期|70|订单PO|100|内部单号|80|产品名称|300|订单数|50|本次完成|50|总完成（%）|50|组装时间(分)|50|人数|50|人力(RMB)/单品|60|人力总计(RMB)|60|备注|50|登记|60";

/*
echo "SELECT M.OrderPO,S.Estate,S.ProductId,P.cName,D.Id,D.POrderId,S.Qty,D.FQty,D.AllMins,D.Workers,D.Remark,D.Date,D.Locks,D.Operator
FROM $DataIn.sc2_Pfinish D
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
WHERE 1 And D.Date=$Date ORDER BY D.ID Desc,M.OrderPO ";
*/
//echo "StuffId:$StuffId";
//echo $cName;
$sListResult = mysql_query("SELECT C.Forshort,A.ProductId,P.cName,P.eCode,P.TestStandard,P.Code,D.StuffCname 
FROM $DataIn.pands A
LEFT JOIN $DataIn.stuffdata D ON  D.StuffId=A.StuffId 
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
where 1 AND P.Estate>0 AND D.StuffCname like '$cName%' order by C.CompanyId,A.ProductId DESC",$link_id);
/*
echo "SELECT D.Mid,D.StuffId,D.ReQty,D.Remark,D.Date,D.Estate,D.Locks,D.Operator FROM $DataIn.ck10_tfsheet D
WHERE 1 And D.Mid=$Mid ORDER BY D.Date Desc ";
//echo "";
*/
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$Forshort=$StockRows["Forshort"];
		$ProductId=$StockRows["ProductId"];
		$cName=$StockRows["cName"];
	
		$eCode=$StockRows["eCode"];
		$Code=$StockRows["Code"];
        $StuffCname =$StockRows["StuffCname"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
	
	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td >$Forshort</td>";	
		echo"<td  align='center'>$ProductId</td>";//
		echo"<td>$TestStandard</td>";
		echo"<td>$eCode</td>";
		echo"<td>$StuffCname</td>";
		echo"</tr>";
		$i=$i+1;
		
		//echo "<td width='55' align='center'>$Date</td>";
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的产品.</td></tr>";
	}

echo"</table>"."";

?>