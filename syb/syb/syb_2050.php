<?php 
//电信
//代码共享-EWEN 2012-08-19
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
<td width='80' class='A1001'>分类</td>
<td width='40' class='A1001'>状态</td>
<td width='60' class='A1001'>请款人</td>
<td width='80' class='A1001'>请款日期</td>
<td width='40' class='A1001'>货币</td>
<td width='80' class='A1001'>预付金额</td>
<td width='100' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='11' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
					  SELECT A.Id,P.Forshort,B.PurchaseID,A.Remark,A.TypeId,A.Estate,M.Name AS Operator,A.Date,C.Symbol,A.Amount,(A.Amount*C.Rate) AS AmountRMB,P.Forshort,C.Symbol
 	FROM $DataIn.cw2_fkdjsheet A 
	LEFT JOIN $DataIn.cg1_stockmain B ON B.PurchaseID=A.PurchaseID 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
	WHERE 1 AND A.Date>='2008-07-01' $MonthSTR $EstateSTR   ORDER BY A.Date DESC,A.Id DESC
",$link_id);

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Id=$checkRow["Id"];
		$Forshort=$checkRow["Forshort"];
		$PurchaseID=$checkRow["PurchaseID"];
		$Remark=$checkRow["Remark"];
		$TypeId=$checkRow["TypeId"];
		$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
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
			<td width='60' class='A0101'>$PurchaseID</td>
			<td width='400' class='A0101'>$Remark</td>
			<td width='80' class='A0101'>$Type</td>
			<td width='40' class='A0101'>$Estate</td>
			<td width='60' class='A0101'>$Operator</td>
			<td width='80' class='A0101'>$Date</td>
			<td width='40' class='A0101'>$Symbol</td>
			<td width='80' class='A0101'>$Amount</td>
			<td width='100' class='A0101'>$AmountRMB</td>
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
	<td width='40' class='A0101'>&nbsp;</td>
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
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>