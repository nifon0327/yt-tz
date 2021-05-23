<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

//取得文件所在目录
$FromDir=get_currentDir(1);

//参数拆分
$TempArray=explode("|",$TempId);
$DeliveryDate=$TempArray[0];	//交期
$predivNum=$TempArray[1];	//a

$mySql="SELECT S.BuyerId,M.Name FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 	
		WHERE 1  AND S.rkSign>0 AND S.Mid>0 AND S.DeliveryDate='$DeliveryDate'
		AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C
		WHERE 1 AND C.StockId=S.StockId) GROUP BY S.BuyerId
		";		
//echo "$mySql <br>";		
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=950;
$subTableWidth=930;
if($myRow = mysql_fetch_array($myResult)){
	$FK_1=0;$FK_2=0;$FK_3=0;
	do{
		
		/*
		$cgQty=$myRow["cgQty"];
		$StockId=$myRow["StockId"];
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$unQty=$cgQty-$rkQty;
		if($unQty>0){ */		
			$BuyerId=$myRow["BuyerId"];
			$Name=$myRow["Name"];
	
			$DivNum=$predivNum."b".$i;
			$TempId="$DeliveryDate|$BuyerId|$DivNum";
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_deliverydate_b\",\"$FromDir\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#CCCCCC'><td>&nbsp;$showPurchaseorder $Name</td></tr></table>";
			echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
					</td>
				</tr></table>
				";
			$i++;
		//}
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>