<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.ck1_rksheet
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=660;
$TempArray=explode("|",$TempId);
$StuffId=$TempArray[0];
$BuyerId=$TempArray[1];
$CompanyId=$TempArray[2];
$DivNum="c".$StuffId.$RowId;
if($BuyerId!=""){
	$BuyerSTR="and S.BuyerId='$BuyerId'";
	}
if($CompanyId!=""){
	$CompanySTR=" and S.CompanyId='$CompanyId'";
	}

$i=1;
//$TempId:传递的参数包括：配件ID、供应商ID、收货状态

// 情况1 -检查是否有未收货的记录
$checkSql1=mysql_query("SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.FactualQty+S.AddQty) AS cgQty 
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId 
WHERE S.Mid>0 and S.rkSign='1' and S.StuffId='$StuffId' $BuyerSTR $CompanySTR and R.StockId IS NULL",$link_id);
$OrderQty1=mysql_result($checkSql1,0,"OrderQty");
$OrderQty1=$OrderQty1==""?0:$OrderQty1;
$cgQty1=mysql_result($checkSql1,0,"cgQty");
$cgQty1=$cgQty1==""?0:$cgQty1;
if($OrderQty1>0){
	$TempId1=$TempId."|1";
	$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId1\",\"cg_stuffqty_c\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ThisImg_$DivNum$i' width='13' height='13' style='CURSOR: pointer'>";
	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>$showPurchaseorder</td>
		<td width='290'>未收货</td>
		<td width='80' align='right'>$OrderQty1</td>				
		<td width='80' align='right'>$cgQty1</td>
		<td width='80' align='right'>&nbsp;</td>
		<td width='80' align='right'>$cgQty1</td>
		<td width='275' align='right'>&nbsp;</td>
		</tr>";
		echo"</table>";
		echo "
		<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>";
		echo"</div></td></tr></table>";
	$i++;
	}
//情况1结束
	
//情况2 检查是否有部分收货的记录
$checkSql2=mysql_query("SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.FactualQty+S.AddQty) AS cgQty
FROM $DataIn.cg1_stocksheet S WHERE S.Mid>0 and S.rkSign='2' and S.StuffId='$StuffId' $BuyerSTR $CompanySTR",$link_id);
$OrderQty2=mysql_result($checkSql2,0,"OrderQty");
$OrderQty2=$OrderQty2==""?0:$OrderQty2;
$cgQty2=mysql_result($checkSql2,0,"cgQty");
$cgQty2=$cgQty2==""?0:$cgQty2;
if($cgQty2>0){	
	$TempId2=$TempId."|2";
	$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId2\",\"cg_stuffqty_c\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ThisImg_$DivNum$i' width='13' height='13' style='CURSOR: pointer'>";
	
		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R,$DataIn.cg1_stocksheet S WHERE 
		S.StockId=R.StockId 
		$BuyerSTR $CompanySTR 
		and R.StuffId='$StuffId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
			
		$noQty=$cgQty-$rkQty;
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>$showPurchaseorder</td>
		<td width='290'>部分收货</td>
		<td width='80' align='right'>$OrderQty2</td>				
		<td width='80' align='right'>$cgQty2</td>
		<td width='80' align='right'>$rkQty2</td>
		<td width='80' align='right'>$noQty2</td>
		<td width='275' align='right'>&nbsp;</td>
		</tr>";
		echo"</table>";
		echo "
		<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr  bgcolor='#FFFFFF'>
				<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr>
		</table>";
	$i++;
	}
//情况2结束

//情况3 检查是否有全部收货的记录
$checkSql0=mysql_query("SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.FactualQty+S.AddQty) AS cgQty
FROM $DataIn.cg1_stocksheet S WHERE S.Mid>0 and S.rkSign='0' and S.StuffId='$StuffId' $BuyerSTR $CompanySTR",$link_id);
$OrderQty0=mysql_result($checkSql0,0,"OrderQty");
$OrderQty0=$OrderQty0==""?0:$OrderQty0;
$cgQty0=mysql_result($checkSql0,0,"cgQty");
$cgQty0=$cgQty0==""?0:$cgQty0;

if($cgQty0>0){
	$TempId0=$TempId."|0";
	$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId0\",\"cg_stuffqty_c\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ThisImg_$DivNum$i' width='13' height='13' style='CURSOR: pointer'>";
	
	//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty 
		FROM $DataIn.ck1_rksheet R,$DataIn.cg1_stocksheet S WHERE 
		S.StockId=R.StockId and S.rkSign='0' $BuyerSTR $CompanySTR and R.StuffId='$StuffId'",$link_id);
		$rkQty0=mysql_result($rkTemp,0,"Qty");
		$rkQty0=$rkQty0==""?0:$rkQty0;

		$noQty0=$cgQty0-$rkQty0;
		
	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>$showPurchaseorder</td>
		<td width='290'>已收货</td>
		<td width='80' align='right'>$OrderQty0</td>
		<td width='80' align='right'>$cgQty0</td>
		<td width='80' align='right'>$rkQty0</td>
		<td width='80' align='right'>$noQty0</td>
		<td width='275' align='right'>&nbsp;</td>
		</tr>";
		echo"</table>";
		echo "
		<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr>
		</table>";
	}
//情况3结束
?>