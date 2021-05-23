<?php   
/*//
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";

$tableWidth=970;
$subTableWidth=950;
$i=1;
//总额
//未出货订单总额
$noshipResult = mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 and S.Estate>'0'",$link_id);
if($noshipRow = mysql_fetch_array($noshipResult)) {
	$AllOrderAmount=sprintf("%.0f",$noshipRow["Amount"]);
	}

$noProfitResult = mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*IFNULL(C.Rate,1)) AS oTheCost
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>'0'",$link_id);
if($noProfitRow = mysql_fetch_array($noProfitResult)) {
	$AllProfitAmount=sprintf("%.0f",($AllOrderAmount-$noProfitRow["oTheCost"]));
	}
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">未出订单毛利统计 <?php    echo $AllProfitAmount?></td>
    </tr>

	<tr>
		<td height="24">&nbsp;</td>
   	 <td height="24" colspan="4" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="620" class="A1111" height="25">&nbsp;</td>
	<td width="80"  class="A1101" align="center">毛利</td>
	<td width="55"  class="A1101" align="center">毛利比例</td>
    <td width="80" align="center" bgcolor="#CCCCCC" class="A1101">订单金额</td>
	<td width="55" align="center" bgcolor="#CCCCCC"  class="A1101">订单比例</td>
    <td width="80" class="A1101" align="center">毛利率</td>
  </tr>
</table>
<?php   
//读取未出货订单金额
$ShipResult = mysql_query("
SELECT Amount,CompanyId,Forshort,oTheCost FROM(
	SELECT Amount,CompanyId,Forshort,oTheCost,(Amount-oTheCost) AS OrderByAmount FROM(
		SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount,M.CompanyId,C.Forshort,A.oTheCost
		FROM $DataIn.yw1_ordermain M
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		LEFT JOIN (
			SELECT SUM((A.AddQty+A.FactualQty)*A.Price*IFNULL(C.Rate,1)) AS oTheCost,M.CompanyId
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>'0' GROUP BY M.CompanyId
			) A ON A.CompanyId=M.CompanyId
		WHERE S.Estate>'0' GROUP BY M.CompanyId ORDER BY C.OrderBy DESC
		) B 
	)C ORDER BY OrderByAmount DESC
",$link_id);
$Total=0;$Total_1=0;$Total_2=0;$Total_3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$cbAmount=0;
		$TempRMB=0;
		$TempPC=0;
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		$TempRMB=sprintf("%.0f",$ShipRow["Amount"]);
		$TempRMBAmount+=$TempRMB;
		$cbAmount=sprintf("%.0f",$ShipRow["oTheCost"]);
		
		$ddbl=sprintf("%.1f",($TempRMB/$AllOrderAmount)*100);//=订单金额/总订单金额
		//毛利
		$TempProfit=sprintf("%.0f",$TempRMB-$cbAmount);
		$mlbl=sprintf("%.1f",($TempProfit/$AllProfitAmount)*100);//=毛利/总毛利
		//毛利率
		$TempPC=sprintf("%.0f",($TempProfit/$TempRMB)*100);
		//毛利总额
		$Total=$Total+$TempProfit;
			//传递客户
			$DivNum="a";
			$TempId="$CompanyId|$Currency|$DivNum";			
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_goodsunsold_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
		//<?php    echo $showPurchaseorder
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="620" height="25">&nbsp;&nbsp;<?php    echo $Forshort?></td>
			<td class="A0101" width="80" align="right"><?php    echo $TempProfit?></td>
			<td class="A0101" width="55" align="right"><?php    echo $mlbl?>%</td>
			<td width="80" align="right" bgcolor="#CCCCCC" class="A0101"><?php    echo zerotospace($TempRMB)?></td>
			<td width="55" align="right" bgcolor="#CCCCCC" class="A0101"><?php    echo $ddbl?>%</td>
			<td class="A0101" width="80" align="right"><?php    echo $TempPC?>%</td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
$AllPC=sprintf("%.0f",($Total/$TempRMBAmount)*100);
?>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr class=''>
    <td class="A0111" height="25" width="620">合 计: </td>
    <td class="A0101" width="80" align="right"><?php    echo zerotospace(number_format(sprintf("%.2f",$Total)))?></td>
	<td class="A0101" width="55" align="right">&nbsp;</td>
	<td width="80" align="right" bgcolor="#CCCCCC" class="A0101"><?php    echo zerotospace(number_format(sprintf("%.2f",$TempRMBAmount)))?></td>
	 <td width="55" align="right" bgcolor="#CCCCCC" class="A0101">&nbsp;</td>
    <td class="A0101" width="80" align="right">&nbsp;<?php    echo $AllPC?>%</td>
   
  </tr>
</table>
<?php   
 //echo "<img src='desk_grossprofitsheet_m.php'><p>&nbsp;</p>";

?>
</form>
</body>
</html>
