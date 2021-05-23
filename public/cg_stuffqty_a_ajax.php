<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.ck1_rksheet
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
$StuffId=$TempArray[0];
$BuyerId=$TempArray[1];
$CompanyId=$TempArray[2];
if($BuyerId!=""){
	$BuyerSTR="and S.BuyerId='$BuyerId'";
	}
if($CompanyId!=""){
	$CompanySTR=" and S.CompanyId='$CompanyId'";
	}

$mySql="SELECT SUM(S.OrderQty) AS OrderQty,S.CompanyId,S.StuffId,P.Forshort
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
WHERE 1 and S.Mid>0 and S.StuffId='$StuffId' $BuyerSTR $CompanySTR GROUP BY S.CompanyId  ORDER BY S.CompanyId DESC";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=710;
$subTableWidth=670;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Forshort=$myRow["Forshort"];
		$OrderQty=$myRow["OrderQty"];
		$CompanyId=$myRow["CompanyId"];
		$BuyerId=$myRow["BuyerId"];
		$StuffId=$myRow["StuffId"];
		
		//此供应商的采购单信息
		//
		//已下单总数
		$cgTemp=mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S WHERE 1 and S.Mid>0 and S.StuffId='$StuffId' $BuyerSTR $CompanySTR",$link_id);
		$cgQty=mysql_result($cgTemp,0,"Qty");
		$cgQty=$cgQty==""?0:$cgQty;
		
		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R,$DataIn.cg1_stocksheet S WHERE 
		S.StockId=R.StockId 
		$BuyerSTR $CompanySTR 
		and R.StuffId='$StuffId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
			
		$noQty=$cgQty-$rkQty;

		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$DivNum="b".$StuffId;
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_stuffqty_b\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ThisImg_$DivNum$i' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='#CCCCCC'>
		<td width='30'></td>
		<td width='30' align='center'>$showPurchaseorder</td>
		<td width='305'>供应商 $Forshort</td>
		<td width='80' align='right'>$OrderQty</td>				
		<td width='80' align='right'>$cgQty</td>
		<td width='80' align='right'>$rkQty</td>
		<td width='80' align='right'>$noQty</td>
		<td width='275' align='right'>&nbsp;</td>
		</tr>";
		echo"</table>";		
		echo "
		<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
				<td height='20'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr>
		</table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>