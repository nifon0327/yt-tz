<?php   
//步骤1电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 热转印明细");
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
		<td height="24" colspan="2">未出热转印类需求明细</td>
    </tr>
	<tr>
		<td height="24"><input name="ShowType" type="radio" id="ShowType2" value="2" checked><LABEL for="ShowType2">以配件类型优先分类</LABEL>&nbsp;&nbsp;
	    <input type="radio" name="ShowType" id="ShowType1" value="1" onClick="javascript:document.form1.action='desk_rzysheet1.php';document.form1.submit()"><LABEL for="ShowType1">以客户优先分类</LABEL></td>
   	 <td align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
   <td width="40" class="A1111" height="25" align="center">序 号</td>
    <td width="710" class="A1101" align="center">分 列</td>
	 <td width="100" class="A1101" align="center">未出数量</td>
  </tr>
</table>
<?php   
//读取客户
$ShipResult = mysql_query("
SELECT SUM(G.OrderQty) AS Qty,T.TypeId,T.TypeName
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
WHERE S.Estate>0 AND T.TypeId IN (9032,9076,9083,9084,9069) GROUP BY T.TypeId ORDER BY T.TypeId
",$link_id);
$SumQty=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Qty=$ShipRow["Qty"];
		$SumQty=$SumQty+$Qty;
		$TypeId=$ShipRow["TypeId"];
		$TypeName=$ShipRow["TypeName"];
		//传递分类
		$DivNum="a".$i;
		$TempId="$TypeId|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_rzysheet2_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
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
			<td class="A0101" width="710">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $TypeId."-".$TypeName?></td>
			<td class="A0101" width="100" align="right"><?php    echo $Qty?>&nbsp;</td>
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
   <td width="750" class="A0111" height="25" align="center">合 计</td>
	 <td width="100" class="A0101" align="right"><?php    echo $SumQty?>&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
