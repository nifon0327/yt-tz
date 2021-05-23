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
SELECT SUM(S.Amount*C.Rate) AS Amount,SUM((S.AddQty+S.FactualQty)*S.Price*C.Rate) AS OAmount,S.Month
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.CompanyId='$CompanyIdTemp' AND S.Estate!=2 
GROUP BY S.Month ORDER BY S.Month";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=910;
$subTableWidth=780;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Amount1=sprintf("%.0f",$myRow["Amount"]);
		$OAmount=sprintf("%.0f",$myRow["OAmount"]);
		$Month=$myRow["Month"];
		
		if ($Amount1!=$OAmount){
			echo "$Month:$Amount1!=$OAmount" ;
		}
		
		$DivNum=$predivNum.$RowId."b";
		$TempId="$CompanyIdTemp|$Month|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cw_fkcount_m_b\",\"public\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		//读取已付
		$paiedSql=mysql_fetch_array(mysql_query("
		SELECT SUM(S.Amount*C.Rate) AS Amount
		FROM $DataIn.cw1_fkoutsheet S
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
		WHERE S.CompanyId='$CompanyIdTemp' AND S.Month='$Month' AND S.Mid>0 AND S.Estate=0",$link_id));
		$Amount2=sprintf("%.0f",$paiedSql["Amount"]);
		$Amount3=$Amount1-$Amount2;
		$Amount1=number_format($Amount1);
		$Amount2=$Amount2==0?"&nbsp;":number_format($Amount2);
		$Amount3=$Amount3==0?"&nbsp;":number_format($Amount3);
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='30' height='20'></td><td width='644'>$showPurchaseorder $Month</td>
				<td width='80' align='right'>$Amount1</td>
				<td width='79' align='right'><div class='greenB'>$Amount2</div></td>
				<td width='76' align='right'><div class='redB'>$Amount3</div></td>
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