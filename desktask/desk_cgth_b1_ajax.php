<?php   
/*电信-yang 20120801
已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$StuffId=$TempArray[1];

$tableWidth=1030;
$TableId=$predivNum;
echo"<table width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#99FF99'>
		<td width='40' height='25' align='center'>序号</td>
		<td width='80' align='center'>日期</td>
		<td align='center'>&nbsp;</td>
		<td width='110' align='center'>退换数量</td>
		<td width='110' align='center'>补仓数量</td>
		<td width='103' align='center'>未补数量</td>
		</tr>";
//订单列表
$mySql="
SELECT thQty,Date,bcQty FROM(
SELECT SUM(S.Qty) AS thQty,M.Date,'0' AS bcQty
	FROM $DataIn.ck2_thmain M
	LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid=M.Id
	WHERE M.CompanyId='$CompanyId' AND S.StuffId='$StuffId' GROUP BY M.Date
	UNION ALL
SELECT '0' AS thQty,M.Date,SUM(S.Qty) AS bcQty
	FROM $DataIn.ck3_bcmain M
	LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid=M.Id
	WHERE M.CompanyId='$CompanyId' AND S.StuffId='$StuffId' GROUP BY M.Date
) A ORDER BY Date
	";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRows = mysql_fetch_array($myResult)){
	$i=1;
	$SUMthQty=0;$SUMbcQty=0;
	do{
		$thQty=$myRows["thQty"];$SUMthQty+=$thQty;
		$bcQty=$myRows["bcQty"];$SUMbcQty+=$bcQty;
		
		$thQty=$thQty==0?"&nbsp;":$thQty;
		$bcQty=$bcQty==0?"&nbsp;":$bcQty;
		$Date=$myRows["Date"];
		echo"<tr bgcolor='#BBFFBB'>";
		echo"<td width='40' height='20' align='center'>$i</td>";
		echo"<td align='center'>$Date</td>";
		echo"<td align='center'>&nbsp;</td>";
		echo"<td align='right'>$thQty</td>";
		echo"<td align='right'>$bcQty</td>";
		echo"<td align='right'>$wbQty</td>";
		echo"</tr>";
		$i++;
		}while($myRows = mysql_fetch_array($myResult));
	//小计
	$SUMwbQty=$SUMthQty-$SUMbcQty;
	echo"<tr bgcolor='#99FF99'>";
		echo"<td height='20' colspan='3' align='center'>小计</td>";
		echo"<td align='right'>$SUMthQty</td>";
		echo"<td align='right'>$SUMbcQty</td>";
		echo"<td align='right'>$SUMwbQty</td>";
		echo"</tr>";
	echo"</table><br>";
	}
else{
	echo"<tr><td height='30' colspan='6'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr></table>";
	}
?>
