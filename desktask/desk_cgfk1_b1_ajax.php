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
$predivNum=$TempArray[1];	//a
$CompanyId=$TempArray[2];	//供应商

//有未请款的年份

$mySql="
SELECT DATE_FORMAT(M.Date,'%Y') AS theYear,SUM(S.Qty*G.Price) AS Amount
	FROM $DataIn.ck1_rksheet S
	LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
	WHERE 1 AND M.CompanyId='$CompanyId' AND K.StockId IS NULL GROUP BY DATE_FORMAT(M.Date,'%Y') ORDER BY M.Date DESC
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1030;
$subTableWidth=1010;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$theYear=$myRow["theYear"];
		$DivNum=$predivNum."c".$i;
		$TempId="$TypeId|$DivNum|$CompanyId|$theYear";
		$AmountTemp=sprintf("%.2f",$myRow["Amount"]);
		$AmountTemp=number_format($AmountTemp,2);
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgfk1_c1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
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