<?php   
/*电信---yang 20120801
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$Currency=$TempArray[1];
$predivName=$TempArray[2];//a
$mySql="SELECT SUM(S.Price*S.Qty*M.Sign) AS Amount,M.Date
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
WHERE M.Estate=0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=950;
$subTableWidth=930;
if($myRow = mysql_fetch_array($myResult)){
	$FK_1=0;$FK_2=0;$FK_3=0;
	do{
		$Date=substr($myRow["Date"],0,7);
		$CheckPart=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		FROM $DataIn.cw6_orderinsheet P
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		WHERE M.cwSign='2' AND M.CompanyId='$CompanyId' AND  DATE_FORMAT(M.Date,'%Y-%m')='$Date'",$link_id));
		$PayedAmount=$CheckPart["GatheringSUM"];
		$Amount=$myRow["Amount"];
		$TempFKSTR="FK_".strval($Currency);
		$$TempFKSTR=sprintf("%.2f",$myRow["Amount"]+$PayedAmount);
		
		$DivNum=$predivNum.$RowId."b".$i;
		$TempId="$CompanyId|$Currency|$Date|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_unreceivepayment_b\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$FK_1=zerotospace($FK_1);
		$FK_2=zerotospace($FK_2);
		$FK_3=zerotospace($FK_3);
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='30' height='20'></td><td width='620'>$showPurchaseorder $Date</td>
				<td width='75' align='right'>$cgQty</td>
				<td width='75' align='right'>$FK_3</td>
				<td width='75' align='right'>$FK_2</td>
				<td width='70' align='right'>$FK_1</td>
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