<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 发票信息统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=1000;
$subTableWidth=1010;
$i=1;
?>
<body>
<script>
function PageTo(P){
	document.form1.action="cw_invoicecount_"+P+".php";
	document.form1.submit();
	}
</script>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" colspan="4">应付(供应商)货款发票统计</td>
    </tr>

	<tr>
		<td height="25" colspan="2" >款项类别：
      <input name="Type" type="radio" id="Type1" value="read" checked><LABEL for="Type1">应付</LABEL>
      <input type="radio" name="Type" id="Type2" value="income" onClick="PageTo(this.value)"><LABEL for="Type2">应收</LABEL>
      <input name="Type" type="radio" id="Type3" value="pay" onClick="PageTo(this.value)"><LABEL for="Type3">费用</LABEL>
      &nbsp;&nbsp;</td>
   	 <td colspan="2" align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>

<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="100" height="25"class="A1111" align="center" rowspan="2">月份</td>
    <td width="300" class="A1101" align="center" colspan="3">已开票</td>
	<td width="300" class="A1101" align="center" colspan="3">未开票</td>
	<td width="300" class="A1101" align="center" colspan="3">合计</td>
  </tr>
  <tr class='' align="center">
    <td width="100" height="25"class="A0101">A.不含税</td>
    <td width="100" class="A0101" align="center">B.税金</td>
	<td width="100" class="A0101" align="center">C=A+B</td>
	<td width="100" height="25"class="A0101">D.不含税</td>
    <td width="100" class="A0101" align="center">E.税金</td>
	<td width="100" class="A0101" align="center">F=D+E</td>
	<td width="100" height="25"class="A0101">不含税=A+D</td>
    <td width="100" class="A0101" align="center">税金=B+E</td>
	<td width="100" class="A0101" align="center">总计=C+F</td>
  </tr>
<?php 
//读取全部货款
$ShipResult = mysql_query("SELECT  S.Month, SUM(IF(S.InvoiceId>0,S.Amount*C.Rate,0)) AS Amount1,SUM(S.Amount*C.Rate) AS Amount,
		SUM(IF(S.InvoiceId>0,S.Amount*C.Rate,0)/(1+X.Value)) AS NoTaxAmount1,SUM(S.Amount*C.Rate/(1+X.Value)) AS NoTaxAmount
		FROM $DataIn.cw1_fkoutsheet S
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
		LEFT JOIN providersheet V ON V.CompanyId = S.CompanyId
		LEFT JOIN provider_addtax X ON X.Id = V.AddValueTax 		   
		WHERE  S.Estate IN(0,3) 
		GROUP BY S.Month ORDER BY S.Month 
  ",$link_id);
$Total1=0;$Total2=0; $Totals=0;
$TaxTotal1=0;$TaxTotal2=0; $TaxTotals=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Month=$ShipRow["Month"];
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		$Amount1=sprintf("%.0f",$ShipRow["Amount1"]);//已开票
		$Amount2 = $Amount-$Amount1;//未开票
		
		
		$NoTaxAmount = sprintf("%.0f",$ShipRow["NoTaxAmount"]);
		$NoTaxAmount1 = sprintf("%.0f",$ShipRow["NoTaxAmount1"]);
	    $NoTaxAmount2 = $NoTaxAmount-$NoTaxAmount1;
	    
	     $TaxAmount1 = $Amount1 - $NoTaxAmount1;
	     $TaxAmount2 = $Amount2 - $NoTaxAmount2;
		 $TaxAmount = $Amount - $NoTaxAmount;
?>
		<tr id='A'>
			<td class="A0111" width="100" height="25">&nbsp;<?php  echo $Month?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($NoTaxAmount1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($TaxAmount1);?></td>
			<td class="A0101" width="100" align="right"><div class="greenB"><?php  echo number_format($Amount1);?></div></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($NoTaxAmount2);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($TaxAmount2);?></td>
			<td class="A0101" width="100" align="right"><div class="redB"><?php  echo number_format($Amount2);?></div></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($NoTaxAmount);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($TaxAmount);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Amount);?></td>
		</tr>

<?php 
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
	</table>
<!--
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="200" height="25">&nbsp;<b>合计</b></td>
			<td class="A0101" width="100" align="right"><div class="greenB"><?php  echo number_format($Total1);?></div></td>
			<td class="A0101" width="100" align="right"><div class="redB"><?php  echo number_format($Total2);?></div></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Total3);?></td>
		</tr>
	</table>
-->
</form>
</body>
</html>