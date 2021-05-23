<?php 
//电信
// ewen 2013-03-29 OK
//订金
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
<td width='80' class='A1001'>供应商</td>
<td width='60' class='A1001'>采购单号</td>
<td width='400' class='A1001'>预付说明</td>
<td width='40' class='A1001'>状态</td>
<td width='60' class='A1001'>请款人</td>
<td width='80' class='A1001'>请款日期</td>
<td width='40' class='A1001'>货币</td>
<td width='80' class='A1001'>预付金额</td>
<td width='100' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='10' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
SELECT A.Date,A.Remark,A.PurchaseID,A.Amount,(A.Amount*C.Rate) AS AmountRMB,A.Did,A.Estate,B.Forshort,C.Symbol,D.Name AS Operator
FROM $DataIn.nonbom11_djsheet A
LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=A.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
LEFT JOIN $DataPublic.staffmain D ON D.Number=A.Operator
WHERE 1 $MonthSTR $EstateSTR
ORDER BY A.Date DESC,A.Id
",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Id=$checkRow["Id"];
		$Forshort=$checkRow["Forshort"];
		$PurchaseID=$checkRow["PurchaseID"];
		$Remark=$checkRow["Remark"];
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
		$Operator=$checkRow["Operator"];
		$Date=$checkRow["Date"];
		$Symbol=$checkRow["Symbol"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80' class='A0101'>$Forshort</td>
			<td width='60' class='A0101' align='center'>$PurchaseID</td>
			<td width='400' class='A0101'>$Remark</td>
			<td width='40' class='A0101' align='center'>$Estate</td>
			<td width='60' class='A0101' align='center'>$Operator</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='40' class='A0101' align='center'>$Symbol</td>
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='100' class='A0101' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='80'  class='A0101'>&nbsp;</td>
	<td width='60'  class='A0101'>&nbsp;</td>
	<td width='400' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='100' class='A0100'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>