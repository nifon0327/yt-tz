<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.yw1_ordermain
二合一已更新
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
$TempProductId=$TempArray[0];
$predivNum=$TempArray[1];
$mySql="
SELECT SUM(S.Qty) AS Qty,M.OrderDate
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
WHERE S.Estate>0 AND S.ProductId='$TempProductId' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m') ORDER BY M.OrderDate
";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=810;
$subTableWidth=780;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Qty=$myRow["Qty"];
		$OrderDate=date("Y年m月",strtotime($myRow["OrderDate"]));
		$Month=date("Y-m",strtotime($myRow["OrderDate"]));
		//Invoice查看
		$DivNum=$predivNum."c".$i;
		$TempId="$TempProductId|$Month";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_productunship_c\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='20' height='20'></td><td width='685'>$showPurchaseorder $OrderDate</td>
				<td width='94' align='right' $SignColor>$Qty&nbsp;</td>
			</tr></table>";
		echo"<table width='$tableWidth' cellspacing='1' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>