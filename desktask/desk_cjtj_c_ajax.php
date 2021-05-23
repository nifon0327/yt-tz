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
//参数拆分
$TempArray=explode("|",$TempId);
$TempDay=$TempArray[0];
$Tid=$TempArray[1];
$SearchRows=" AND C.Tid='$Tid'";

//订单列表
$myResult = mysql_query("
SELECT C.Qty,P.cName,S.POrderId,G.Price 
	FROM $DataIn.sc1_cjtj C
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=C.POrderId
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
	LEFT JOIN $DataIn.sc1_counttype T ON T.Id=C.Tid
	WHERE C.Date='$TempDay' $SearchRows AND A.TypeId=T.TypeId	
	",$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#99FF99'>
				<td width='89' height='20' align='center'>完成工价</td>
				<td width='89' align='center'>单品工价</td>
				<td width='89' align='center'>完成数量</td>
				<td width='89' align='center'>订单流水号</td>
				<td width='275' align='center'>产品名称</td>
			</tr>";
	do{
		$cName=$myRow["cName"];
		$POrderId=$myRow["POrderId"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];					
		$QtySum+=$Qty;
		$Amount=$Price*$Qty;
		echo"
			<tr bgcolor='#99FF99'>
				<td height='20' align='center'>$Amount</td>
				<td align='center'>$Price</td>
				<td align='center'>$Qty</td>
				<td align='center'>$POrderId</td>
				<td>$cName</td>
			</tr>";
		$i++;
 		}while($myRow = mysql_fetch_array($myResult));
	echo"</table>";
	}
?>