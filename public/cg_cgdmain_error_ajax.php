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
$subTableWidth=600;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='100' >入库Id号</td>
		<td width='70' align='center'>送货单号</td>
		<td width='70' align='center'>送货日期</td>
		<td width='40' align='center'>配件ID</td>
		<td width='45' align='center'>数量</td>
		<td width='100' align='center'>采购流水号</td>		
		";

echo "</tr>";
$sListResult = mysql_query("SELECT S.Id,M.BillNumber,M.Date,S.StockId,S.StuffId,S.Qty FROM $DataIn.ck1_rksheet S
						   	LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id  
						    WHERE S.StockId='$StockId' order by S.Id",$link_id);
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$Id=$StockRows["Id"];
		$BillNumber=$StockRows["BillNumber"];
		$Date=$StockRows["Date"];
		$StockId=$StockRows["StockId"];
		$StuffId=$StockRows["StuffId"];
		$Qty=$StockRows["Qty"];
		
		echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='Left' >$Id</td>";//
		echo"<td  align='Left' >$BillNumber</td>";	
		echo"<td  align='Left' >$Date</td>";			
		echo"<td  align='Left' >$StuffId</td>";		
		echo"<td  align='Right'>$Qty</td>";
		echo"<td  align='center'>$StockId</td>";//
		echo"</tr>";
		$i=$i+1;
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的入库记录.</td></tr>";
	}
echo"</table>"."";

?>