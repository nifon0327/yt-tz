<?php 
//电信
//代码共享-EWEN 2012-08-19
//其他收入
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.PayDate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0";break;
	case "W":$DataTSTR="未结付记录";$EstateSTR=" AND A.Estate=2";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND A.Estate!=1";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.TypeId IN($Parameters)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='100' class='A1001'>类别</td>
<td width='100' class='A1001'>日期</td>
<td  class='A1001'>备注</td>
<td width='40' class='A1001'>货币</td>
<td width='80' class='A1001'>原金额</td>
<td width='80' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='7' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$i=1;
	$checkSql=mysql_query("SELECT A.Remark,A.PayDate,A.Amount,(A.Amount*C.Rate) AS AmountRMB,B.Name AS ItemName,C.Symbol 
            FROM $DataIn.cw4_otherin A 
            LEFT JOIN $DataPublic.cw4_otherintype B ON B.Id=A.TypeId 
            LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
           WHERE 1 AND A.PayDate>='2008-07-01' $MonthSTR $Parameters $EstateSTR ORDER BY A.PayDate DESC,A.Id DESC",$link_id);
	$SumAmount=0;
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$PayDate=$checkRow["PayDate"];
			$ItemName=$checkRow["ItemName"];
			$Remark=$checkRow["Remark"];
			$Symbol=$checkRow["Symbol"];
			$Amount=sprintf("%.2f",$checkRow["Amount"]);
			$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
			$SumAmount+=$AmountRMB;
			$Amount=number_format($Amount);
			$AmountRMB=number_format($AmountRMB);
			echo"
				<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
				<td width='50' height='20' class='A0111' align='center'>$i</td>
				<td width='100'  class='A0101'>$ItemName</td>
				<td width='100' class='A0101' align='center'>$PayDate</td>
				<td class='A0101'>$Remark</td>
				<td width='40' class='A0101' align='center'>$Symbol</td>
				<td width='80' class='A0101' align='right'>$Amount</td>
				<td width='80' class='A0100' align='right'>$AmountRMB</td>
				</tr>";
			$i++;
			}while($checkRow=mysql_fetch_array($checkSql));
		}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td width='100' class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>