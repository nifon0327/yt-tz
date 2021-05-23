<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.cg1_stocksheet
$DataIn.stuffdata
$DataIn.stufftype
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TempArray=explode("|",$TempId);
$TempTypeId=$TempArray[0];
$predivNum=$TempArray[1];//a
//参数拆分:该客户下，满足条件的所有产品
$mySql="
SELECT SUM(G.OrderQty) AS Qty,G.StuffId,D.StuffCname
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
WHERE S.Estate>0 AND T.TypeId='$TempTypeId' GROUP BY G.StuffId ORDER BY D.StuffCname";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=825;
$subTableWidth=810;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Qty=$myRow["Qty"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$DivNum=$predivNum."b".$i;
		$TempId="$StuffId|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_rzysheet2_b\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='20' height='20' align='center'>$i</td><td width='710'>&nbsp;$showPurchaseorder $StuffId - $StuffCname</td>
				<td width='95' align='right'>$Qty&nbsp;</td>
			</tr></table>";
		echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>