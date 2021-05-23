<?php   
//电信-zxq 2012-08-01
/*
功能：统计用户负责的未出订单总额
独立已更新:EWEN 2009-12-18 17:00
*/
$MynoshipResult = mysql_query("
SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount,C.Forshort 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
LEFT JOIN $DataIn.yw6_salesview V ON V.CompanyId=M.CompanyId
WHERE 1 AND S.Estate>'0' AND V.SalesId='$Login_P_Number' GROUP BY M.CompanyId",$link_id);
if($MynoshipRow = mysql_fetch_array($MynoshipResult)){
	$MySumAmount=0;
	do{
		$myAmount=sprintf("%.2f",$MynoshipRow["Amount"]);
		$TempPC=($myAmount/$noshipAmount)*100;
		$TempPC=$TempPC>=1?(round($TempPC)."%"):(sprintf("%.2f",$TempPC)."%");
		$Forshort=$MynoshipRow["Forshort"];
		$MySumAmount=$MySumAmount+$myAmount;
		$MyRemark=$MyRemark==""?($Forshort.":".number_format($myAmount)."RMB(占$TempPC)"):($MyRemark."<br>".$Forshort.":".number_format($myAmount)."RMB(占$TempPC)");
		}while ($MynoshipRow = mysql_fetch_array($MynoshipResult));
	$OutputInfo.="<tr $TR_bgcolor><td colspan='3' width='200'>我负责的未出订单总额：<span class='yellowN'>".number_format($MySumAmount)."</span>RMB.包括<br>$MyRemark</td></tr>";
	}
?>