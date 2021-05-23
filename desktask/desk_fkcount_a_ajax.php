<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
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
$TempArray=explode("|",$TempId);
$CompanyIdTemp=$TempArray[0];
$predivName=$TempArray[1];//a
$mySql="
SELECT SUM(S.Amount*C.Rate) AS Amount,S.Month
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.CompanyId='$CompanyIdTemp' AND S.Estate=3  
GROUP BY S.Month ORDER BY S.Month";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=910;
$subTableWidth=780;
if($myRow = mysql_fetch_array($myResult)){
	do{
	
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$Month=$myRow["Month"];
		$DivNum=$predivNum.$RowId."b";
		$TempId="$CompanyIdTemp|$Month|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_fkcount_b\",\"desktask\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$Amount=number_format($Amount);
	
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='30' height='20'></td><td width='644'>$showPurchaseorder $Month</td>
				<td width='80' align='right'>$Amount</td>
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