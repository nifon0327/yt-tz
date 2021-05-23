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
$tableWidth=700;
$subTableWidth=710;
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
		<td height="25" colspan="4">应收(客户)货款发票统计</td>
    </tr>

	<tr>
		<td height="25" colspan="2" >款项类别：
      <input name="Type" type="radio" id="Type1" value="income" checked><LABEL for="Type1">应收</LABEL>
      <input type="radio" name="Type" id="Type2" value="read" onClick="PageTo(this.value)"><LABEL for="Type2">应付</LABEL>
      <input name="Type" type="radio" id="Type3" value="pay" onClick="PageTo(this.value)"><LABEL for="Type3">费用</LABEL>
      &nbsp;&nbsp;</td>
   	 <td colspan="2" align="right">统计日期:<?php  echo date("Y年m月d日");?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth;?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
     <td width="100" height="25"class="A1111" align="center" rowspan="2">月份</td>
     <td width="100" height="25"class="A1101" align="center" rowspan="2">&nbsp;</td>
    <td width="300" class="A1101" align="center" colspan="3">内销</td>
	<td width="100" class="A1101" align="center">外销</td>
	<td width="100" class="A1101" align="center">合计</td>
  </tr>
  <tr class='' align="center">
    <td width="100" height="25"class="A0101">A.不含税</td>
    <td width="100" class="A0101" align="center">B.税金</td>
	<td width="100" class="A0101" align="center">C=A+B</td>
	<td width="100" height="25"class="A0101">D</td>
    <td width="100" class="A0101" align="center">E=A+D</td>
  </tr>

<?php 

//读取全部已收货款
$ShipResult = mysql_query("SELECT SUM(A.InvoiceAmount) AS InvoiceAmount,SUM(A.Amount) AS Amount,A.Month,A.SaleMode 
FROM (
		SELECT SUM(IF(F.Id>0,S.Qty*S.Price*C.Rate,0)) AS InvoiceAmount,SUM(S.Qty*S.Price*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%Y-%m') AS Month,IF(P.SaleMode!=1,2,P.SaleMode) AS SaleMode 
		FROM ch1_shipmain M
		INNER JOIN ch1_shipsheet S  ON M.Id=S.Mid
		INNER JOIN trade_object P ON P.CompanyId=M.CompanyId
		INNER JOIN currencydata C ON C.Id=P.Currency
		LEFT JOIN ch1_shipfile F ON F.ShipId = M.Id 
		WHERE   (M.Estate=0  OR M.Estate=3) AND M.Sign=1 AND M.ShipType!='debit' 
		GROUP BY DATE_FORMAT(M.Date,'%Y-%m'),P.SaleMode
) A GROUP BY A.Month,A.SaleMode ORDER BY Month,SaleMode
",$link_id);

$Total1=0;$Total2=0;$Total3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	$Amount_1=$Amount_2=$NoTaxAmount_1=$NoTaxAmount_2=$TaxAmount_1=$TaxAmount_2=0;
	$oldMonth=$ShipRow["Month"];
	do{
		$Month=$ShipRow["Month"];
		
		if ($oldMonth!=$Month){
			   $NoTaxAmount_1= $InvoiceAmount_1/(1+0.17);
			   $TaxAmount_1  = $InvoiceAmount_1 - $NoTaxAmount_1;
			   
			   $NoInvoiceAmount = $Amount_1 - $NoTaxAmount_1;
			   $NoInvoiceNoTaxAmount = $NoInvoiceAmount/(1+0.17);
			   $NoInvoiceTaxAmount = $NoInvoiceAmount - $NoInvoiceNoTaxAmount;
			   
			   $Total1 = $NoTaxAmount_1 + $Amount_2;

?>
		<tr id='A' height="25" >
			<td class="A0111" width="200" rowspan="2">&nbsp;<?php  echo $oldMonth?></td>
			<td class="A0101" width="100" align="center">有开票</td>
			<td class="A0101" width="100" align="right"><?php  echo $NoTaxAmount_1==0?"&nbsp;":number_format($NoTaxAmount_1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $TaxAmount_1==0?"&nbsp;":number_format($TaxAmount_1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $InvoiceAmount_1==0?"&nbsp;":number_format($InvoiceAmount_1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Amount_2);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Total1);?></td>
	
		<tr id='A'>
		    <td class="A0101" width="100" height="25"  align="center">未开票</td>
			<td class="A0101" width="100" align="right"><?php  echo $NoInvoiceNoTaxAmount==0?"&nbsp;":number_format($NoInvoiceNoTaxAmount);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $NoInvoiceTaxAmount==0?"&nbsp;":number_format($NoInvoiceTaxAmount);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $NoInvoiceAmount==0?"&nbsp;":number_format($NoInvoiceAmount);?></td>
			<td class="A0101" width="100" align="right">&nbsp;</td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($NoInvoiceNoTaxAmount);?></td>
		</tr>

<?php 
         $oldMonth = $Month;
         $Amount_1=$Amount_2=$NoTaxAmount_1=$NoTaxAmount_2=$TaxAmount_1=$TaxAmount_2=0;
         $Total1=0;$Total2=0;$Total3=0;
      }
      
        $SaleMode = $ShipRow["SaleMode"];

		$AmountSTR= "Amount_" . $SaleMode;
		$NoTaxAmountSTR= "NoTaxAmount_" . $SaleMode;
		$TaxAmountSTR= "TaxAmount_" . $SaleMode;
		$InvoiceAmountSTR= "InvoiceAmount_" . $SaleMode;
		$$AmountSTR=sprintf("%.0f",$ShipRow["Amount"]);
		$$InvoiceAmountSTR = sprintf("%.0f",$ShipRow["InvoiceAmount"]);
        
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
		       $oldMonth = $Month;
	      	   $NoTaxAmount_1= $InvoiceAmount__1/(1+0.17);
			   $TaxAmount_1  = $InvoiceAmount__1 - $NoTaxAmount_1;
			   
			   $NoInvoiceAmount = $Amount_1 - $NoTaxAmount_1;
			   $NoInvoiceNoTaxAmount = $NoInvoiceAmount/(1+0.17);
			   $NoInvoiceTaxAmount = $NoInvoiceAmount - $NoInvoiceNoTaxAmount;
			   
			   $Total1 = $NoTaxAmount_1 + $Amount_2;
		?>
		<tr id='A' height="25" >
			<td class="A0111" width="200" rowspan="2">&nbsp;<?php  echo $oldMonth?></td>
			<td class="A0101" width="100" align="center">有开票</td>
			<td class="A0101" width="100" align="right"><?php  echo $NoTaxAmount_1==0?"&nbsp;":number_format($NoTaxAmount_1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $TaxAmount_1==0?"&nbsp;":number_format($TaxAmount_1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $InvoiceAmount_1==0?"&nbsp;":number_format($InvoiceAmount_1);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Amount_2);?></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Total1);?></td>
	
		<tr id='A'>
		    <td class="A0101" width="100" height="25"  align="center">未开票</td>
			<td class="A0101" width="100" align="right"><?php  echo $NoInvoiceNoTaxAmount==0?"&nbsp;":number_format($NoInvoiceNoTaxAmount);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $NoInvoiceTaxAmount==0?"&nbsp;":number_format($NoInvoiceTaxAmount);?></td>
			<td class="A0101" width="100" align="right"><?php  echo $NoInvoiceAmount==0?"&nbsp;":number_format($NoInvoiceAmount);?></td>
			<td class="A0101" width="100" align="right">&nbsp;</td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($NoInvoiceNoTaxAmount);?></td>
		</tr>


<?php 
	}
?>
	</table>
</form>
</body>
</html>