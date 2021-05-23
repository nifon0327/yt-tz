<?php   
/*电信---yang 20120801
$DataIn.ck9_stocksheet
$DataIn.stuffdata
$DataIn.stufftype
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 配件可用库存统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$ColsNumber=4;
$tableWidth=1000;
$subTableWidth=980;
$i=1;
?>
<body>
<form name="form1" method="post" action=""><input name="MergeRows" type="hidden" id="MergeRows">
  <input name="sumCols" type="hidden" id="sumCols">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="6">配件分类可用库存统计</td>
    </tr>

	<tr>
		<td height="24">&nbsp;</td>
   	 <td height="24" colspan="5" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
   <td width="40" class="A1111" height="25" align="center">序 号</td>
    <td width="580" class="A1101" align="center">分类名称</td>
    <td width="150" class="A1101" align="center">可用库存</td>
    <td width="150" class="A1101" align="center">金额小计</td>
	 <td width="80" class="A1101" align="center">库存分析</td>
  </tr>
</table>
<?php   
//读取未结付货款
$ShipResult = mysql_query("
SELECT SUM(oStockQty) AS oStockQty,T.Letter,T.TypeName,D.TypeId
FROM $DataIn.ck9_stocksheet K
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
WHERE K.oStockQty>0  GROUP BY D.TypeId ORDER BY T.Letter",$link_id);
$Total=0;
$SumAmount=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$TypeId=$ShipRow["TypeId"];
		
		$Letter=$ShipRow["Letter"];
		$TypeName=$ShipRow["TypeName"];
		$oStockQty=$ShipRow["oStockQty"];
		$SumTemp=mysql_query("SELECT SUM(IFNULL(K.oStockQty,0)*IFNULL(D.Price,0))  As Amount
				FROM $DataIn.ck9_stocksheet K
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
				WHERE K.oStockQty>0  AND D.TypeId=$TypeId",$link_id);
		$Amount=mysql_result($SumTemp,0,"Amount");
		$SumAmount=$SumAmount+$Amount;
		$Total=$Total+$oStockQty;
		$oStockQty=number_format($oStockQty);
		//传递分类
		$DivNum="a";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TypeId\",\"desk_ostockqty_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='center'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<?php   
	echo"<tr id='A' bgcolor='$theDefaultColor'
	onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";?>
			<td width="40" class="A0111" height="25" align="center"><?php    echo $i?></td>
			<td class="A0101" width="580">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $Letter."-".$TypeName?></td>
			<td class="A0101" width="150" align="right"><?php    echo zerotospace($oStockQty)?>&nbsp;</td>
            <td class="A0101" width="150" align="right"><?php    echo zerotospace($Amount)?>&nbsp;</td>
			<td class="A0101" width="80" align="center"><a href='desk_stufftype_count.php?Idtemp=<?php    echo $TypeId?>' target='_blank'>查看</a></td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr class=''>
    <td class="A0111" height="25" width="620">合 计: </td>
    <td class="A0101" width="150" align="right"><?php    echo number_format(zerotospace($Total))?>&nbsp;</td>
    <td class="A0101" width="150" align="right"><?php    echo sprintf("%.4f",$SumAmount)?>&nbsp;</td>
	<td class="A0101" width="80" align="right">&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
