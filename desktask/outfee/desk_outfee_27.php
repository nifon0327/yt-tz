<?php   
//非BOM未付订金 ewen 2013-03-28  OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=900;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='80' align='center'>供应商</td>
		<td width='40' align='center'>货币</td>
		<td width='60' align='center'>预付金额</td>
		<td width='400' align='center'>预付说明</td>
		<td width='40' align='center'>状态</td>
		<td width='50' align='center'>请款人</td>
		<td width='70' align='center'>请款日期</td>
	</tr></table>";
$SearchRows=" AND S.Did=0 AND (S.Estate=0 OR S.Estate=3) AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthTemp'";
$mySql="
	SELECT S.Id,S.CompanyId,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,P.Forshort,C.Symbol
 	FROM $DataIn.nonbom11_djsheet S 
	LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE 1 $SearchRows order by S.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
        $Symbol=$myRow["Symbol"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Amount=$myRow["Amount"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate="<div align='center' class='redB'>未抵付</div>";
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='80' align='center'>$Forshort</td>
				<td width='40' align='center'>$Symbol</td>
				<td width='60' align='center'>-$Amount</td>
				<td width='400' >$Remark</td>
				<td width='40' align='center'>$Estate</td>
				<td width='50' align='center'>$Operator</td>
				<td width='70' align='center' >$Date</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>