<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 应付货款统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=940;
$subTableWidth=910;
$i=1;
?>
<body>
<script>
function PageTo(P){
	document.form1.action="cw_fkcount_"+P+".php";
	document.form1.submit();
	}
</script>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" colspan="4">应付货款统计</td>
    </tr>

	<tr>
		<td height="25" colspan="2" >统计方式：
		<input name="Type" type="radio" id="Type3" value="p" checked><LABEL for="Type3">结付月份</LABEL>
       <input name="Type" type="radio" id="Type1" value="m" onClick="PageTo(this.value)"><LABEL for="Type1">请款月份</LABEL>
       <input type="radio" name="Type" id="Type2" value="g" onClick="PageTo(this.value)"><LABEL for="Type2">供应商</LABEL>
       <input name="Type" type="radio" id="Type4" value="t" onClick="PageTo(this.value)"><LABEL for="Type4">年度报表</LABEL>
      &nbsp;&nbsp;</td>
   	 <td colspan="2" align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="700" height="25"class="A1111">&nbsp;月&nbsp;份</td>
    <td width="80" class="A1101" align="center">预付金额RMB</td>
	<td width="80" class="A1101" align="center">已付金额RMB</td>
	<td width="80" class="A1101" align="center">合计金额RMB</td>
  </tr>
</table>
<?php 
//读取全部货款
$ShipResult = mysql_query("SELECT SUM(M.payAmount*C.Rate) AS Amount,DATE_FORMAT(M.PayDate,'%Y-%m') AS Month
FROM $DataIn.cw1_fkoutmain M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
where  1 
GROUP BY DATE_FORMAT(M.PayDate,'%Y-%m') ORDER BY Month 
",$link_id);
$Total1=0;$Total2=0;$Total3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Month=$ShipRow["Month"];
		$Amount1=sprintf("%.2f",$ShipRow["Amount"]);
		
		//读出已付订金
		$paiedResult = mysql_fetch_array(mysql_query("SELECT SUM(S.PayAmount*S.Rate) AS Amount
		FROM (
			SELECT M.PayAmount,C.Rate 
			FROM $DataIn.cw2_fkdjmain M
			LEFT JOIN $DataIn.cw2_fkdjsheet  S ON S.Mid=M.Id 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
			WHERE DATE_FORMAT(M.PayDate,'%Y-%m')='$Month'  GROUP BY M.Id )S 
		",$link_id));

		$Amount2=sprintf("%.2f",$paiedResult["Amount"]);
		$Amount3=$Amount1+$Amount2;
		$Total1=$Total1+$Amount1;
		$Total2=$Total2+$Amount2;
		$Total3=$Total3+$Amount3;
		$Amount1=number_format($Amount1);
		$Amount2=$Amount2==0?"&nbsp;":number_format($Amount2);
		$Amount3=$Amount3==0?"&nbsp;":number_format($Amount3);
			//传递客户
			$DivNum="a";
			$TempId="$Month|$DivNum";			
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cw_fkcount_p_a\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0'name='HideTable_$DivNum$i'   id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="700" height="25">&nbsp;<?php  echo $showPurchaseorder?>&nbsp;<?php  echo $Month?></td>
			<td class="A0101" width="80" align="right"><div class="greenB"><?php  echo $Amount2?></div></td>
			<td class="A0101" width="80" align="right"><div class="greenB"><?php  echo $Amount1?></div></td>
			<td class="A0101" width="80" align="right"><div class="greenB"><?php  echo $Amount3?></div></td>
		</tr>
	</table>
<?php 
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr class=''>
    <td class="A0111" height="25" width="700">合 计:</td>
    <td class="A0101" width="80" align="right"><?php  echo number_format($Total2)?></td>
	<td class="A0101" width="80" align="right"><?php  echo number_format($Total1)?></td>
	<td class="A0101" width="80" align="right"><?php  echo number_format($Total3)?></td>
  </tr>
</table>
</form>
</body>
</html>