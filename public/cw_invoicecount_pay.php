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
$tableWidth=600;
$subTableWidth=910;
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
		<td height="25" colspan="4">应付费用发票统计</td>
    </tr>

	<tr>
		<td height="25" colspan="2" >款项类别：
		<input name="Type" type="radio" id="Type3" value="pay" checked><LABEL for="Type3">费用</LABEL>
       <input name="Type" type="radio" id="Type1" value="read" onClick="PageTo(this.value)"><LABEL for="Type1">应付</LABEL>
       <input type="radio" name="Type" id="Type2" value="income" onClick="PageTo(this.value)"><LABEL for="Type2">应收</LABEL>
      &nbsp;&nbsp;</td>
   	 <td colspan="2" align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>

<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="200" height="25"class="A1111">&nbsp;月&nbsp;份</td>
    <td width="100" class="A1101" align="center">有票金额RMB</td>
	<td width="100" class="A1101" align="center">无票金额RMB</td>
	<td width="100" class="A1101" align="center">合计金额RMB</td>
  </tr>
</table>
<?php 
//读取行政费用
$ShipResult = mysql_query("
SELECT SUM(A.InoviceAmount) AS InoviceAmount,SUM(A.Amount) AS Amount,A.Month
FROM (
			SELECT SUM(IF(S.Bill=1,S.Amount*C.Rate,0)) AS InoviceAmount,SUM(S.Amount*C.Rate) AS Amount,DATE_FORMAT(S.Date,'%Y-%m') AS Month
			FROM  hzqksheet S  
			LEFT JOIN currencydata C ON C.Id=S.Currency
			WHERE  S.Estate=0 OR S.Estate=3
			GROUP BY DATE_FORMAT(S.Date,'%Y-%m') 
	UNION ALL 
			SELECT SUM(IF(I.Id>0,S.Amount*C.Rate,0)) AS InoviceAmount,SUM(S.Amount*C.Rate) AS Amount,S.Month
			FROM nonbom11_qksheet S
			LEFT JOIN nonbom6_invoice I ON I.cgMid=S.cgMid
							LEFT JOIN nonbom3_retailermain P ON P.CompanyId=S.CompanyId
							LEFT JOIN currencydata C ON C.Id=P.Currency
			GROUP BY S.Month 
)A GROUP BY A.Month ORDER BY Month
",$link_id);
$Total1=0;$Total2=0;$Total3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Month=$ShipRow["Month"];
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		$Amount1=sprintf("%.0f",$ShipRow["InoviceAmount"]);
		$Amount2 = $Amount-$Amount1;
	
		$Total1=$Total1+$Amount1;
		$Total2=$Total2+$Amount2;
		$Total3=$Total3+$Amount;
?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="200" height="25">&nbsp;<?php  echo $Month?></td>
			<td class="A0101" width="100" align="right"><div class="greenB"><?php  echo number_format($Amount1);?></div></td>
			<td class="A0101" width="100" align="right"><div class="redB"><?php  echo number_format($Amount2);?></div></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Amount);?></td>
		</tr>
	</table>
<?php 
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="200" height="25">&nbsp;<b>合计</b></td>
			<td class="A0101" width="100" align="right"><div class="greenB"><?php  echo number_format($Total1);?></div></td>
			<td class="A0101" width="100" align="right"><div class="redB"><?php  echo number_format($Total2);?></div></td>
			<td class="A0101" width="100" align="right"><?php  echo number_format($Total3);?></td>
		</tr>
	</table>
</form>
</body>
</html>