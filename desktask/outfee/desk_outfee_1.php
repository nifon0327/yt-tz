<?php
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=950;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
$preDivNum=$TempArray[1];
$TableId="ListTB".$preDivNum.$RowId;
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp'";
$mySql="SELECT P.Forshort,P.CompanyId,C.Symbol,SUM((S.FactualQty+S.AddQty)*S.Price) AS Amount
 	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
    LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE 1 $SearchRows GROUP BY P.CompanyId";
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
        $CompanyId=$myRow["CompanyId"];
        $Forshort=$myRow["Forshort"];
        $Symbol=$myRow["Symbol"];
        $Amount=$myRow["Amount"];
		$DivNum=$preDivNum."d";
		$TempId="$CompanyId|$MonthTemp|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_outfee_1_ajax\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='60' height='20' align='center'>$showPurchaseorder</td>
				<td width='120' align='center'>$Forshort</td>
				<td width='100' align='center'>$Symbol</td>
				<td width='680' align='right'>$Amount</td>
			</tr></table>";
		echo"<table width='$tableWidth' cellspacing='1' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$tableWidth' align='center'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>