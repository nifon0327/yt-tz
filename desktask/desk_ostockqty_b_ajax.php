<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$TempArray=explode("|",$TempId);
$StuffId=$TempArray[0];
$subTableWidth=820;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='20' height='20'></td>
		<td width='100' align='center'>客户</td>
		<td width='50' align='center'>产品ID</td>
		<td width='180' align='center'>中文名</td>		
		<td width='150' align='center'>Product Code</td>
		<td width='180' align='center'>条码</td>";
echo "</tr>";

$sListResult = mysql_query("SELECT C.Forshort,A.ProductId,P.cName,P.eCode,P.TestStandard,P.Code
FROM $DataIn.pands A
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata D ON  D.StuffId=A.StuffId
where 1 AND D.StuffId='$StuffId' order by A.ProductId DESC",$link_id);
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$Forshort=$StockRows["Forshort"];
		$ProductId=$StockRows["ProductId"];
		$cName=$StockRows["cName"];
		$eCode=$StockRows["eCode"];
		$Code=$StockRows["Code"];
		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getProductImage.php";	
	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td  align='Left' >$Forshort</td>";	
		echo"<td  align='center'>$ProductId</td>";//
		echo"<td  align='Left' >$TestStandard</td>";		
		echo"<td  align='Left'>$eCode</td>";
		echo"<td  align='Left'>$Code</td>";
		echo"</tr>";
		$i=$i+1;
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的产品.</td></tr>";
	}

echo"</table>"."";

?>