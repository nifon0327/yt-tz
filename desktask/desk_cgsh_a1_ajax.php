<?php   
/*电信-yang 20120801
配件分类页面
已更新
*/
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
$mySql="SELECT DATE_FORMAT(M.Date,'%Y') AS theYear
	FROM $DataIn.ck1_rkmain M
	WHERE 1 AND M.CompanyId='$CompanyId' GROUP BY DATE_FORMAT(M.Date,'%Y') ORDER BY M.Date DESC
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1050;
$subTableWidth=1030;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$theYear=$myRow["theYear"];
		$DivNum=$predivNum."b".$i;
		$TempId="$CompanyId|$DivNum|$theYear";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgsh_b1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#cccccc'><td>&nbsp;$showPurchaseorder $theYear 年</td><td width='80' align='right'>&nbsp;</td><td width='74' align='right'>&nbsp;</td></tr></table>";
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