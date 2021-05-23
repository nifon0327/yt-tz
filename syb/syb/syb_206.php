<?
//客户扣款(credit note) ewen 2012-11-22
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.cwSign=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND (A.cwSign=1 OR A.cwSign=2)";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR="";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='80' class='A1001'>出货日期</td>
<td width='100' class='A1001'>客户</td>
<td width='200' class='A1001'>Invoice</td>
<td class='A1001'>备注</td>
<td width='50' class='A1001'>状态</td>
<td width='60' class='A1001'>货币</td>
<td width='80' class='A1001'>金额</td>
<td width='80' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='9' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT 
A.Id,A.Date,C.Forshort,A.InvoiceNO,A.InvoiceFile,A.Remark,A.cwSign,D.Symbol,SUM(B.Qty*B.Price) AS Amount,SUM(B.Qty*B.Price*D.Rate) AS AmountRMB
FROM $DataIn.ch1_shipmain A
LEFT JOIN $DataIn.ch1_shipsheet B ON B.Mid=A.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
WHERE A.Estate=0 AND A.Sign=-1 $MonthSTR $EstateSTR
GROUP BY A.Id ORDER BY A.Date DESC",$link_id);

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141128 app採集單月紀錄
		$detailList[] = $checkRow;			
	
		$Id=$checkRow["Id"];
		$Date=$checkRow["Date"];
		$Forshort=$checkRow["Forshort"];
		$InvoiceNO=$checkRow["InvoiceNO"];
		$InvoiceFile=$checkRow["InvoiceFile"];
		if ($InvoiceFile != 0) {
			//add by cabbage 20141128 app採集單附件資料	
			$fileLink = "/download/invoice/".$checkRow["InvoiceNO"].".pdf";
			$linkList[$checkRow["Id"]] = $fileLink;
		}
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
		$Remark=$checkRow["Remark"];
		$cwSign=$checkRow["cwSign"];
		switch($cwSign){
			case 1:
			$cwSign="<span class=\"redB\">未付</span>";
			break;
			case 2:
			$cwSign="<span class=\"yellowB\">部分</span>";
			break;
			case 0:
			$cwSign="<span class=\"greenB\">已付</span>";
			break;
			default:
			$cwSign="出错";
			break;
			}
		$Symbol=$checkRow["Symbol"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80' class='A0101'>$Date</td>
			<td width='100' class='A0101'>$Forshort</td>
			<td width='200' class='A0101' bgcolor='#ECEAED'>$InvoiceFile</td>
			<td class='A0101'>$Remark</td>
			<td width='50' class='A0101' align='center' bgcolor='#ECEAED'>$cwSign</td>
			<td width='60' class='A0101' align='center'>$Symbol</td>		
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='80' class='A0101' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='80'  class='A0101'>&nbsp;</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td width='200' class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='50' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0100'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>