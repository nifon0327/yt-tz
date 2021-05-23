<?php 
//电信
//代码共享-EWEN 2012-08-19
//扣供应商货款
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND B.Kid>0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND B.Kid=0";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR="";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='80' class='A1001'>日期</td>
<td width='100' class='A1001'>供应商</td>
<td width='200' class='A1001'>退款单号</td>
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
//modify by cabbage 20141119 
//1. 多傳一個Esate欄位
//2. 原本只撈出「已結付」資料，加上顯示「審核過未結付」的資料 >> A.Estate=0 >> A.Estate IN (0, 3)
//读取记录
$checkSql=mysql_query("
					  SELECT A.Date AS PayDate,C.Forshort,
					  A.BillNumber,A.BillFile,A.Remark,A.Estate,
					  B.Kid,D.Symbol,IFNULL(SUM(B.Amount),0) AS Amount,IFNULL(SUM(B.Amount*D.Rate),0) AS AmountRMB 
					  FROM $DataIn.cw15_gyskksheet B
					  LEFT JOIN $DataIn.cw15_gyskkmain A ON B.Mid=A.Id 
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
					  LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
					  WHERE 1 AND A.Date>='2008-07-01' AND A.Estate IN (0, 3) $EstateSTR $MonthSTR GROUP BY A.Id
					  ",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
	
		//add by cabbage 20141120 app採集單月紀錄
		$detailList[] = $checkRow;
	
		$Id=$checkRow["Id"];
		$PayDate=$checkRow["PayDate"];
		$Forshort=$checkRow["Forshort"];
		$BillNumber=$checkRow["BillNumber"];
		$BillFile=$checkRow["BillFile"];
		//$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
	//	$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
		$Remark=$checkRow["Remark"];
		$Estate=$checkRow["Kid"]==0?"<span class=\"redB\">未抵付</span>":"<span class=\"greenB\">已抵付</span>";
		$Symbol=$checkRow["Symbol"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80' class='A0101'>$PayDate</td>
			<td width='100' class='A0101'>$Forshort</td>
			<td width='200' class='A0101' bgcolor='#ECEAED'>$BillNumber</td>
			<td class='A0101'>$Remark</td>
			<td width='50' class='A0101' align='center' bgcolor='#ECEAED'>$Estate</td>
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