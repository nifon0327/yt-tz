<?php   
//电信-yang 20120801
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
$CompanyId=$TempArray[0];	//供应商
$predivNum=$TempArray[1];	//a
$theMonth=$TempArray[2];		//送货年份

//有未请款的年份
$mySql="SELECT M.Date AS theDay,DATE_FORMAT(M.Date,'%d') AS Day
	FROM $DataIn.ck1_rkmain M
	WHERE 1 AND M.CompanyId='$CompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.Date ORDER BY M.Date DESC
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1010;
$subTableWidth=990;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$theDay=$myRow["theDay"];
		$Day=$myRow["Day"];
		$DivNum=$predivNum."d".$i;
		$TempId="$CompanyId|$DivNum|$theDay";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgsh_d1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#cccccc'><td>&nbsp;$showPurchaseorder $Day 日</td><td width='73' align='right'>&nbsp;</td></tr></table>";
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