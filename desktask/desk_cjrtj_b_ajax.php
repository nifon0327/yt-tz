<?php   
/*
ewen 2014-01-08 更新:生产登记日期发稿为详细时间
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);//日期/层参数/统计分类
$TempDay=$TempArray[0];
$GroupId=$TempArray[2];
$SearchRows=" AND C.GroupId='$GroupId'";
//记录列表：日期和班长
$myResult = mysql_query("
	SELECT C.Qty,P.cName,S.POrderId,G.Price ,C.Leader 
	FROM $DataIn.sc1_cjtj C
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=C.POrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	WHERE C.Date LIKE '$TempDay%' $SearchRows AND D.TypeId=C.TypeId
	",$link_id);
$i=1;
$SumAmount=0;
$SumQty=0;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#00CC66'>
				<td width='50' height='20' align='center'>序号</td>
				<td width='70' align='center'>完成工价</td>
				<td width='70' align='center'>单品工价</td>
				<td width='70' align='center'>完成数量</td>
				<td width='106' align='center'>订单流水号</td>
				<td width='285' align='center'>产品名称</td>
				<td width='60' align='center'>登记人</td>
			</tr>";
if($myRow = mysql_fetch_array($myResult)){
		
	do{
		$cName=$myRow["cName"];
		$POrderId=$myRow["POrderId"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$Amount=sprintf("%.2f",$Price*$Qty);
		$SumAmount+=$Amount;
		$SumQty+=$Qty;
		$Operator=$myRow["Leader"];
		include "../model/subprogram/staffname.php";
		echo"
			<tr bgcolor='#BFFFBF' align='center'>
				<td height='20'>$i</td>
				<td align='right'>$Amount</td>
				<td align='right'>$Price</td>
				<td align='right'>$Qty</td>
				<td>$POrderId</td>
				<td align='left'>$cName</td>
				<td>$Operator</td>
			</tr>";
		$i++;
 		}while($myRow = mysql_fetch_array($myResult));
	//合计
	$SumAmount=sprintf("%.0f",$SumAmount);
	echo"<tr bgcolor='#00CC66' align='right'>
		<td height='20' align='center'>合计</td>
		<td>$SumAmount</td>
		<td>&nbsp;</td>
		<td>$SumQty</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
	}
else{
	echo"<tr bgcolor='#00CC66' align='right'><td height='20' align='center' colspan='7'>当天无生产登记</td></tr>";
	}
echo"</table>";
?>