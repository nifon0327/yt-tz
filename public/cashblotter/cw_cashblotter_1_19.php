<?php
//19 假日加班费						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.hdjbmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='60' class='A1101'>员工ID</td>
<td width='60' class='A1101'>员工姓名</td>
<td width='60' class='A1101'>部门</td>
<td width='60' class='A1101'>职位</td>
<td width='70' class='A1101'>月份</td>
<td width='60' class='A1101'>2倍时薪</td>
<td width='60' class='A1101'>2倍工时</td>
<td width='60' class='A1101'>3倍时薪</td>
<td width='60' class='A1101'>3倍工时</td>
<td width='70' class='A1101'>加班费</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/cwjrjb/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT
	S.Id,S.Mid,S.Number,S.Month,S.xHours,S.xWage,S.fHours,S.fWage,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,P.Name,J.Name AS Job,B.Name AS Branch
 	FROM $DataIn.hdjbsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
	WHERE S.Mid='$Mid' ORDER BY S.Id
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
		$j=1;
		do{
			//结付明细数据
			$Id=$checkSheetRow["Id"];	
			$Number=$checkSheetRow["Number"];
			$Name=$checkSheetRow["Name"];
			$Branch=$checkSheetRow["Branch"];
			$Job=$checkSheetRow["Job"];
			$Month=$checkSheetRow["Month"];
			$xHours=$checkSheetRow["xHours"];
			$fHours=$checkSheetRow["fHours"];
			$xWage=$checkSheetRow["xWage"];
			$fWage=$checkSheetRow["fWage"];
			$Amount=sprintf("%.0f",$checkSheetRow["Amount"]);
			$Date=$checkSheetRow["Date"];
		
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$Number</td>";
			echo"<td class='A0101'>$Name</td>";
			echo"<td class='A0101'>$Branch</td>";
			echo"<td class='A0101'>$Job</td>";
			echo"<td class='A0101'  align='center'>$Month</td>";
			echo"<td class='A0101' align='right'>$xWage</td>";
			echo"<td class='A0101' align='right'>$xHours</td>";
			echo"<td class='A0101' align='right'>$fWage</td>";
			echo"<td  class='A0101' align='right'>$fHours</td>";
			echo"<td  class='A0101' align='right'>$Amount</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>