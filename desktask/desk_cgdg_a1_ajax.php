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
$predivNum=$TempArray[0];	//a
$SearchRows=" AND T.mainType<2";//需采购的配件需求单
//有未请款的年份
$mySql="SELECT count(*) AS Nums,S.BuyerId,M.Name
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
		LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
		WHERE 1 AND S.Mid=0 AND E.Type='2' AND (S.FactualQty>0 OR S.AddQty>0) $SearchRows
		GROUP BY S.BuyerId ORDER BY S.BuyerId";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1050;
$subTableWidth=1030;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Nums=$myRow["Nums"];
		$BuyerId=$myRow["BuyerId"];
		$Name=$myRow["Name"];
		$TempId=$BuyerId;
		$DivNum=$predivNum."b".$i;
		$TempId="$DivNum|$BuyerId";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgdg_b1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr bgcolor='#cccccc'>
		<td>&nbsp;$showPurchaseorder $Name</td>
		<td width='82' align='right'>$Nums</td>
		<td width='73' align='right'>&nbsp;</td>
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