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
$TypeId=$TempArray[0];		//统计分类：未请款、请款中、待结付、已结付
$CompanyId=$TempArray[1];	//供应商
$predivNum=$TempArray[2];	//a

/* 以请款日期为索引
$mySql="SELECT left(K.Month,4) AS theYear
	FROM $DataIn.cw1_fkoutsheet K
	WHERE 1 AND K.CompanyId='$CompanyId' AND K.Estate=0 GROUP BY left(K.Month,4) ORDER BY K.Month";*/

$mySql="SELECT DATE_FORMAT(M.PayDate,'%Y') AS theYear,SUM(S.Qty*S.Price) AS Amount
	FROM $DataIn.cw1_fkoutmain M
	LEFT JOIN $DataIn.cw1_fkoutsheet S ON S.Mid=M.Id
	WHERE 1 AND M.CompanyId='$CompanyId' GROUP BY DATE_FORMAT(M.PayDate,'%Y') ORDER BY M.PayDate DESC
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1030;
$subTableWidth=1010;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$theYear=$myRow["theYear"];
		$DivNum=$predivNum."c".$i;
		$TempId="$TypeId|$CompanyId|$DivNum|$theYear";
		$AmountTemp=sprintf("%.2f",$myRow["Amount"]);
		$AmountTemp=number_format($AmountTemp,2);
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgfk_c4\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#ffffff'><td>&nbsp;$showPurchaseorder $theYear 年</td><td width='80' align='right'>$AmountTemp</td><td width='74' align='right'>&nbsp;</td></tr></table>";
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