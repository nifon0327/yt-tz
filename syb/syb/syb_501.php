<?php 
//电信
//代码共享-EWEN 2012-08-19
//三节奖金：资料不在同一个表？
$MonthSTR=$Month==""?"":" AND A.Month='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.TypeID IN($Parameters)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='100' class='A1001'>费用名称</td>
<td width='70' class='A1001'>部门</td>
<td width='60' class='A1001'>职位</td>
<td width='60' class='A1001'>员工姓名</td>
<td width='110' class='A1001'>计算月份</td>
<td width='60' class='A1001'>比率参数</td>
<td width='40' class='A1001'>状态</td>
<td width='80' class='A1001'>请款月份</td>
<td width='100' class='A1001'>请款金额</td>
</tr>
<tr>
<td colspan='10' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT A.Number,A.ItemName,B.Name AS Branch,C.Name AS Job,A.Number,D.Name,D.ComeIn,A.Month,A.MonthS,A.MonthE,A.Divisor,A.Rate,A.Amount,A.Estate,A.Locks,A.Date,D.Name AS Operator,E.Idcard 
FROM $DataIn.cw11_jjsheet A 
LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId 
LEFT JOIN $DataPublic.jobdata C ON C.Id=A.JobId 
LEFT JOIN $DataPublic.staffmain D ON D.Number=A.Number
LEFT JOIN $DataPublic.staffsheet E ON E.Number=A.Number 
WHERE 1 AND A.Month>='2008-07-01' 
$MonthSTR $Parameters
$EstateSTR ORDER BY A.Month DESC,B.SortId,D.JobId,D.Number",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		//add by cabbage 20141212 app採集單月紀錄
		$detailList[] = $checkRow;
	
		$Id=$checkRow["Id"];
		$ItemName=$checkRow["ItemName"];
		$Branch=$checkRow["Branch"];
		$Job=$checkRow["Job"];
		$Name=$checkRow["Name"];
		$Number=$checkRow["Number"];
		//include "../admin/subprogram/staff_model_gl.php";
		$MonthS=$checkRow["MonthS"];
		$MonthE=$checkRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Rate=$checkRow["Rate"]*100/100;
		
		//add by cabbage 20141212 app用，紀錄比率參數
		$detailList[$i - 1]["Rate"] = $Rate;
		
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
		$Month=$checkRow["Month"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$ItemName</td>
			<td width='70' class='A0101'>$Branch</td>
			<td width='60' class='A0101' align='center'>$Job</td>
			<td width='60' class='A0101' align='center'>$Name</td>
			<td width='110' class='A0101' align='center'>$MonthSTR</td>
			<td width='60' class='A0101' align='center'>$Rate %</td>
			<td width='40' class='A0101'  align='center'>$Estate</td>
			<td width='80' class='A0101' align='center'>$Month</td>
			<td width='100' class='A0100' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td width='70'  class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='110' class='A0101'>&nbsp;</td>
	<td width='60' class='A0100'>&nbsp;</td>
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