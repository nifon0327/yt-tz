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
$theDay=$TempArray[1];	//天

//有未请款的年份
$mySql="SELECT M.CompanyId,P.Forshort,P.Letter,I.Tel,I.Fax,C.Symbol 
	FROM $DataIn.ck1_rkmain M
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
	WHERE 1 AND M.Date='$theDay' GROUP BY M.CompanyId ORDER BY P.Currency,P.Letter";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=990;
$subTableWidth=970;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Symbol=$myRow["Symbol"];
		$Letter=$myRow["Letter"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$DivNum=$predivNum."d".$i;
		$TempId="$theDay|$CompanyId";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgsh1_d1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#cccccc'><td>&nbsp;$showPurchaseorder $Symbol - $i - $Forshort</td><td width='73' align='right'>&nbsp;</td></tr></table>";
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