<?
//免抵退增值税款 ewen 2012-11-22
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='70' class='A1001'>请款日期</td>
<td width='80' class='A1001'>供应商</td>
<td width='90' class='A1001'>发票号</td>
<td width='70' class='A1001'>发票金额</td>
<td width='80' class='A1001'>收到发票日期</td>
<td class='A1001'>税款说明</td>
<td width='40' class='A1001'>状态</td>
<td width='40' class='A1001'>加税率</td>
<td width='60' class='A1001'>货币</td>
<td width='70' class='A1001'>税款金额</td>
<td width='70' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='12' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
SELECT A.Id,A.Mid,A.Forshort,A.InvoiceNUM,A.InvoiceFile,A.Amount,A.Remark,A.Date,A.Estate,
B.Symbol,A.Rate,A.Getdate,A.Fpamount,(A.Amount*B.Rate) AS AmountRMB
 	FROM $DataIn.cw2_gyssksheet A 
	LEFT JOIN $DataPublic.currencydata B ON B.Id=A.Currency
	WHERE A.Date>='2008-07-01' $MonthSTR $EstateSTR ORDER BY A.Date DESC,A.Id DESC ",$link_id);

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		//add by cabbage 20141128 app採集單月紀錄
		$detailList[] = $checkRow;	
				
		$Id=$checkRow["Id"];
		$Date=$checkRow["Date"];
		$Forshort=$checkRow["Forshort"];
		$InvoiceNUM=$checkRow["InvoiceNUM"];
		$InvoiceFile=$checkRow["InvoiceFile"];
		if($InvoiceFile==1){
			$InvoiceFile="S".$Id;
			
			//add by cabbage 20141128 app採集附件連結
			$fileLink = "/download/cwgyssk/".$InvoiceFile.".pdf";
			$linkList[$checkRow["Id"]] = $fileLink;
			
			$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			$InvoiceNUM="<a href=\"../admin/openorload.php?d=$Dir&f=$InvoiceFile&Type=&Action=7\" target=\"download\">$InvoiceNUM</a>";
		}
		$Fpamount=$checkRow["Fpamount"];
		$Getdate=$checkRow["Getdate"];
		$Remark=$checkRow["Remark"];
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
		$Symbol=$checkRow["Symbol"];
		$Rate=$checkRow["Rate"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$Amount=number_format($Amount);
		$AmountRMB=number_format($AmountRMB);
		
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='70' class='A0101'>$Date</td>
			<td width='80' class='A0101'>$Forshort</td>
			<td width='90' class='A0101' bgcolor='#ECEAED'>$InvoiceNUM</td>
			<td width='70' class='A0101' align='right'>$Fpamount</td>
			<td width='80' class='A0101' align='center'>$Getdate</td>
			<td class='A0101'>$Remark</td>
			<td width='40' class='A0101' align='center' bgcolor='#ECEAED'>$Estate</td>
			<td width='40' class='A0101' align='right'>$Rate</td>
			<td width='60' class='A0101' align='center'>$Symbol</td>		
			<td width='70' class='A0101' align='right'>$Amount</td>
			<td width='70' class='A0101' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='70'  class='A0101'>&nbsp;</td>
	<td width='80'  class='A0101'>&nbsp;</td>
	<td width='90' class='A0101' bgcolor='#ECEAED'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0100'>&nbsp;</td>
	</tr>";
	}
$SumAmount=number_format(sprintf("%.0f",$SumAmount));
echo"</table>
</div>
</td>
</tr>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td height='20' class='A0111' colspan='2'>合计</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>