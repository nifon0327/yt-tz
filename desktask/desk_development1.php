<?php   
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 开发费用统计明细");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1070;
$subTableWidth=1050;
$i=1;
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">开发费用统计明细</td>
    </tr>
	<tr>
	  <td height="24" colspan="5"><input name="Action" type="radio" value="1" id="Action1" checked><label for="Action1">以请款日期为索引进行统计</label><input type="radio" name="Action" value="0" id="Action0" onClick="javascript:document.form1.action='desk_development2.php';document.form1.submit()"><label for="Action0">以开发人员为索引进行统计</label>
		 </td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="25" align="center">年份</td>
    <td class="A1101" width="210" align="center">费用</td>
  </tr>
</table>
<?php   
//条件：有下单给供应商 未传图片 配件可用 分类9000以上 采购在职？AND M.Estate>0
$SumAmount=0;
$ShipResult = mysql_query("SELECT SUM(M.Amount) AS Amount,DATE_FORMAT(M.Date,'%Y') AS theYear
	FROM $DataIn.cwdyfsheet M
	WHERE 1 GROUP BY DATE_FORMAT(M.Date,'%Y') ORDER BY M.Date DESC",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Amount=$ShipRow["Amount"];$SumAmount+=$Amount;
		$theYear=$ShipRow["theYear"];
		//传递交货日期
		$DivNum="a".$i;
		$TempId="$DivNum|$theYear";			
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_development1_a1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏下级资料. ' width='13' height='13' style='CURSOR: pointer'>";
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
		<tr id='A'>
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder."&nbsp;$theYear"?>年</td>
			<td class="A0101" width="70" align="right"><?php    echo number_format($Amount,2)?></td>
			<td class="A0101" width="70">&nbsp;</td>
			<td class="A0101" width="70">&nbsp;</td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" height="25">&nbsp;合计</td>
			<td class="A0101" width="70" align="right"><?php    echo number_format($SumAmount,2)?></td>
			<td class="A0101" width="70">&nbsp;</td>
			<td class="A0101" width="70">&nbsp;</td>
		</tr>
	</table>

</form>
</body>
</html>