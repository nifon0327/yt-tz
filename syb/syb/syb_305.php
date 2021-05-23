<?php 
//住房公积金+行政公积金费用 ewen 2013-11-06
$MonthSTR=$Month==""?"":" AND  A.Month='$Month'";
$MonthSTRHZ=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.TypeId IN($Parameters)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='60' height='20' class='A1011'>序号</td>
<td class='A1001'>请款月份</td>
<td class='A1001'>员工姓名</td>
<td class='A1001'>部门</td>
<td class='A1001'>职位</td>
<td class='A1001'>状态</td>
<td class='A1001'>个人缴费</td>
<td class='A1001'>公司缴费</td>
<td class='A1001'>小计</td>
</tr>
<tr>
<td colspan='9' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
SELECT Month, mAmount,cAmount,Amount,Estate, Name,Branch,Job FROM(
		SELECT A.Date AS Month,(A.Amount*B.Rate) AS mAmount,'0' AS cAmount,(A.Amount*B.Rate) AS Amount,A.Estate,A.Content AS Name,'' AS Branch,'' AS Job
			FROM $DataIn.hzqksheet A
			LEFT JOIN $DataPublic.currencydata B ON B.Id=A.Currency 
			WHERE 1 AND A.Date>='2008-07-01' 
			$MonthSTRHZ $Parameters
			$EstateSTR
	UNION ALL
		SELECT A.Month,A.mAmount,A.cAmount,(A.mAmount+A.cAmount) AS Amount,A.Estate,B.Name,C.Name AS Branch,D.Name AS Job
		FROM $DataIn.sbpaysheet A
		LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
		LEFT JOIN $DataPublic.branchdata C ON C.Id=A.BranchId 
		LEFT JOIN $DataPublic.jobdata D ON D.Id=A.JobId 
		WHERE  A.Month>='2008-07' $MonthSTR $EstateSTR AND A.TypeId=2
		) Z
",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		//add by cabbage 20141202 app採集單月紀錄
		$detailList[] = $checkRow;
	
		$Month=$checkRow["Month"];
		$Name=$checkRow["Name"];
		$Branch=$checkRow["Branch"];
		$Job=$checkRow["Job"];
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";	
		$mAmount=sprintf("%.2f",$checkRow["mAmount"]);
		$cAmount=$checkRow["cAmount"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);		
		$SumAmount+=$Amount;
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \" align='right'>
			<td width='60' height='20' class='A0111' align='center'>$i</td>
			<td class='A0101' align='center'>$Month</td>";
			if($Branch==""){
				echo "<td class='A0101' colspan='3' align='left'><span class='redB'>行政费用:</span> $Name</td>";
				}
			else{
				echo "<td class='A0101' align='center'>$Name</td>
				<td class='A0101' align='center'>$Branch</td>
				<td class='A0101' align='center'>$Job</td>";
				}
			echo"
			<td class='A0101' align='center'>$Estate</td>
			<td class='A0101'>$mAmount</td>
			<td class='A0101' >$cAmount</td>
			<td class='A0101'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='60' height='20' class='A0111'>$j</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
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
<td  class='A0101' align='right'>$SumAmount</td>
</tr>
</table>
";
?>