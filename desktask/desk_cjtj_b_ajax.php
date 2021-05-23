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
$TempMonth=$TempArray[0];
$predivNum=$TempArray[1];
$dayObjQty=$TempArray[2];
$Tid=$TempArray[3];
$SearchRows=" AND C.Tid='$Tid'";
$myResult = mysql_query("SELECT SUM(C.Qty) AS Qty,C.Date FROM $DataIn.sc1_cjtj C 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=C.POrderId 
WHERE left(C.Date,7)='$TempMonth' $SearchRows GROUP BY C.Date ORDER BY C.Date DESC",$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Day=substr($myRow["Date"],8,2)."日";
		$Qty=$myRow["Qty"];					$QtySum+=$Qty;
		$DivNum=$predivNum."c".$i;
		$TempId=$myRow["Date"]."|".$Tid;
		$ObjQty=$dayObjQty;
		$bgColor=$Qty>=$ObjQty?"greenB":"redB";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cjtj_c\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='69' height='20' align='center'>$showPurchaseorder $Day</td>
				<td width='89' align='center'><div class='$bgColor'>$Qty</div></td>
				<td width='89' align='center'>$ObjQty</td>
				<td width='457' align='center'>&nbsp;</td>
			</tr></table>";
		echo"<table width='$tableWidth' cellspacing='1' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='25'>
				<div id='HideDiv_$DivNum$i' align='right'>&nbsp;</div>
				</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>