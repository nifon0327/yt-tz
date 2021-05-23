<?php   
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);
$predivNum=$TempArray[0];
$Number=$TempArray[1];
$theYear=$TempArray[2];

//有未请款的年份
$mySql="
	SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,DATE_FORMAT(M.Date,'%m') AS theMonth,SUM(M.Amount) AS Amount
	FROM $DataIn.cwdyfsheet M
	WHERE 1 AND M.Operator='$Number' AND DATE_FORMAT(M.Date,'%Y')='$theYear' GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1030;
$subTableWidth=1010;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$theMonth=$myRow["theMonth"];
		$Amount=$myRow["Amount"];
		$Month=$myRow["Month"];
		$DivNum=$predivNum."c".$i;
		$TempId="$Number|$Month";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_development2_c1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#ffffff'><td>&nbsp;$showPurchaseorder $theMonth 月</td><td width='63' align='right'>$Amount</td></tr></table>";
		echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#ffffff'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>