<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/

include "../model/modelhead.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
$predivName=$TempArray[1];//a

$mySql="
		SELECT '1' AS Sign,M.PayDate,M.Id,SUM(M.PayAmount*C.Rate) AS Amount,M.PreAmount,M.CompanyId,P.Forshort,P.Letter,C.PreChar 
		FROM $DataIn.cw6_orderinmain M 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
		WHERE DATE_FORMAT(M.PayDate,'%Y-%m') ='$MonthTemp' 
		GROUP BY M.Id 
	 UNION ALL 
	     SELECT '2' AS Sign,S.PayDate,S.Id,(S.Amount*C.Rate) AS Amount,'' AS PreAmount,S.CompanyId,P.Forshort,P.Letter,C.PreChar 
		FROM $DataIn.cw6_advancesreceived S  
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
		WHERE DATE_FORMAT(S.PayDate,'%Y-%m') ='$MonthTemp' 
		ORDER BY PayDate,Sign";
//	echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=910;
$subTableWidth=780;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Amount1=number_format($myRow["Amount"],2);
		$Forshort=$myRow["Letter"]."-".$myRow["Forshort"];
		$Mid=$myRow["Id"];
		$PayDate=$myRow["PayDate"];
		$CompanyId=$myRow["CompanyId"];
		$Sign=$myRow["Sign"];
		$SignSTR = $Sign==1?"货款":"<div class='greenB'>预收</div>";
		$PreChar=$myRow["PreChar"];
		
		$PreAmount=$myRow["PreAmount"]>0?"<span style='float:right;color:#FF8000;'>抵扣预收款:   $PreChar" . $myRow["PreAmount"] . "</span>":"";
		$DivNum=$predivNum.$RowId."b";
		$TempId="$Mid|$Sign|$DivNum";
		
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cw_clientfkcount_b\",\"public\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";


		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='91'>$showPurchaseorder $PayDate</td>
				<td width='80' align='center'>$SignSTR</td>
				<td width='630'>$Forshort $PreAmount</td>
				<td width='100' align='right'><div class='greenB'>$Amount1</div></td>
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