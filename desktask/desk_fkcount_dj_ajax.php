<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.stuffdata
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=780;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='95' align='center'>请款日期</td>
		<td width='305' align='center'>说明</td>
		<td width='95' align='center'>付款日期</td>
		<td width='60' align='center'>金额</td>
		<td width='79' align='center'>已付金额RMB</td>
	</tr>";

//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$predivNum=$TempArray[1];

$mySql="
SELECT M.PayDate,S.Remark,S.Date,S.Amount,C.Rate
FROM $DataIn.cw2_fkdjsheet S 
LEFT JOIN $DataIn.cw2_fkdjmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE  S.Did='0' and S.Estate=0  AND S.CompanyId='$CompanyId' ORDER BY S.Date";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$AmountSum=0;
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$PayDate=$myRow["PayDate"];
		$Date=$myRow["Date"];
		$Rate=$myRow["Rate"];
		$Amount=$myRow["Amount"];
		$AmountRMB1=number_format($Amount*$Rate,2);	
		$Remark=$myRow["Remark"];
		echo"
			<tr bgcolor='#FFFFFF'>
				<td height='20' align='center'>$i</td>
				<td align='center'>$Date</td>
				<td>$Remark</td>
				<td align='right'>$PayDate</td>
				<td align='right'>$Amount</td>
				<td align='right'>$AmountRMB1</td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}

?>