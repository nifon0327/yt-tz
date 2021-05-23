<?
//预收货款：抵付分列，结付分列 ewen 2012-11-21
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.PayDate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":
		$DataTSTR=$Parameters==0?"已抵付预收货款":"已结付记录"; $EstateSTR=$Parameters==0?" AND A.Mid>'0'":"";
		break;
	case "W":
		$DataTSTR=$Parameters==0?"未抵付预收货款":"未结付记录";$EstateSTR=$Parameters==0?" AND A.Mid='0'":"AND A.Mid='-1'";
		break;
	case "A":
		$DataTSTR="全部记录"; $EstateSTR="";
		break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==0?-1:1;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='150' class='A1001'>客户</td>
<td  class='A1001'>结付备注</td>
<td width='80' class='A1001'>结付日期</td>
<td width='80' class='A1001'>结付银行</td>
<td width='60' class='A1001'>结付货币</td>
<td width='80' class='A1001'>预收金额</td>
<td width='80' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='8' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT A.PayDate,A.Remark,A.Amount,(A.Amount*C.Rate) AS RmbAmount,
		B.Forshort,C.Symbol,D.Title
FROM $DataIn.cw6_advancesreceived A
LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
LEFT JOIN $DataPublic.my2_bankinfo D ON D.Id=A.BankId
WHERE 1 $MonthSTR $EstateSTR ORDER BY A.Id DESC
",$link_id);

$testSql = "SELECT A.PayDate,A.Remark,A.Amount,(A.Amount*C.Rate) AS RmbAmount,
		B.Forshort,C.Symbol,D.Title
FROM $DataIn.cw6_advancesreceived A
LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
LEFT JOIN $DataPublic.my2_bankinfo D ON D.Id=A.BankId
WHERE 1 $MonthSTR $EstateSTR ORDER BY A.Id DESC";

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		//add by cabbage 20141120 app採集單月紀錄
		$detailList[] = $checkRow;
	
		$PayDate=$checkRow["PayDate"];
		$BankName=$checkRow["Title"];
		$Forshort=$checkRow["Forshort"];
		$Remark=$checkRow["Remark"]==""?"&nbsp;":$checkRow["Remark"];
		$Symbol=$checkRow["Symbol"];
		$Amount=$checkRow["Amount"]*$Parameters;
		$RmbAmount=sprintf("%.2f",$checkRow["RmbAmount"]*$Parameters);
		$SumAmount+=$RmbAmount;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$Forshort</td>
			<td class='A0101'>$Remark</td>
			<td width='80' class='A0101' align='center'>$PayDate</td>
			<td width='80' class='A0101'>$BankName</td>
			<td width='80' class='A0101'  align='center' >$Symbol</td>
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='80' class='A0100' align='right'>$RmbAmount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>