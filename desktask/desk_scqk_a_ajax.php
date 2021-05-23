<?php   
/*
已更新电信---yang 20120801
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
$scFrom=$TempId;
$predivNum="a".$scFrom;
//列出客户
$mySql="
SELECT count(*) AS Num,M.CompanyId,C.Forshort FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
WHERE S.Estate>0 AND S.scFrom='$scFrom' GROUP BY M.CompanyId ORDER BY M.CompanyId";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1030;
$subTableWidth=1010;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Num=$myRow["Num"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$DivNum=$predivNum."b".$i;
		$TempId="$scFrom|$CompanyId|$DivNum";		
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_scqk_b\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#CCCCCC'><td>&nbsp;$showPurchaseorder $i-$Forshort 订单数：$Num</td></tr></table>";
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