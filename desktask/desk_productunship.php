<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.productdata
$DataIn.producttype
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 未出产品数量统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$ColsNumber=3;
$tableWidth=850;
$subTableWidth=830;
$i=1;
?>
<body>
<form name="form1" method="post" action="">
  <input name="MergeRows" type="hidden" id="MergeRows">
  <input name="sumCols" type="hidden" id="sumCols">
  <table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">未出产品数量统计</td>
    </tr>

	<tr>
		<td height="24">&nbsp;</td>
   	 <td height="24" colspan="4" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
   <td width="40" class="A1111" height="25" align="center">序 号</td>
    <td width="710" class="A1101" align="center">分 类</td>
    <td width="100" class="A1101" align="center">未出数量</td>
  </tr>
</table>
<?php   
//读取未结付货款
$ShipResult = mysql_query("
SELECT SUM(S.Qty) AS Qty,T.Letter,T.TypeName,D.TypeId
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata D ON D.ProductId = S.ProductId
LEFT JOIN $DataIn.producttype T ON T.TypeId = D.TypeId
WHERE S.Estate>0 AND T.TypeId='8029' GROUP BY D.TypeId ORDER BY T.Letter",$link_id);
$Total=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$TypeId=$ShipRow["TypeId"];
		$Letter=$ShipRow["Letter"];
		$TypeName=$ShipRow["TypeName"];
		$Qty=$ShipRow["Qty"];
		$Total=$Total+$Qty;
		$Qty=number_format($Qty);
		//传递分类
		$DivNum="a".$i;
		$TempId="$TypeId|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_productunship_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
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
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<?php   
	echo"<tr id='A' bgcolor='$theDefaultColor'
	onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";?>
			<td width="40" class="A0111" height="25" align="center"><?php    echo $i?></td>
			<td class="A0101" width="710">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $Letter."-".$TypeName?></td>
			<td class="A0101" width="100" align="right"><?php    echo zerotospace($Qty)?>&nbsp;</td>
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
    <td class="A0111" height="25" width="750">合 计: </td>
    <td class="A0101" width="100" align="right"><?php    echo number_format(zerotospace($Total))?>&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
