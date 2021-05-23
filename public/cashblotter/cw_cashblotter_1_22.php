<?php
//22节日奖金			OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw11_jjmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='150' class='A1101'>奖金项目</td>
<td width='40' class='A1101'>部门</td>
<td width='40' class='A1101'>职位</td>
<td width='50' class='A1101'>姓名</td>
<td width='150' class='A1101'>计算月份</td>
<td width='70' class='A1101'>比率参数</td>
<td width='70' class='A1101'>金额</td>
<td width='70' class='A1101'>请款月份</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/cwjj/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/cwjj/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("
	SELECT 
	S.Id,S.Mid,S.ItemName,B.Name AS Branch,W.Name AS Job,S.Number,P.Name,P.Estate AS mEsate,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,S.Estate,S.Locks,S.Date,S.JfTime
 	FROM $DataIn.cw11_jjsheet S
	LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId 
	LEFT JOIN $DataPublic.jobdata W ON W.Id=S.JobId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
	WHERE  S.Mid='$Mid' ORDER BY S.Date DESC
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
			$JfTime=$checkSheetRow["JfTime"];
			$ItemName=$checkSheetRow["ItemName"]."--".$JfTime;
			$Branch=$checkSheetRow["Branch"];
			$Job=$checkSheetRow["Job"];
			$Number=$checkSheetRow["Number"];
			$Name=$checkSheetRow["mEsate"]==1?$checkSheetRow["Name"]:"<div class='yellowB'>".$checkSheetRow["Name"]."</div>";
			$Month=$checkSheetRow["Month"];
			$MonthS=$checkSheetRow["MonthS"];
			$MonthE=$checkSheetRow["MonthE"];
			$MonthSTR=$MonthS."~".$MonthE;
			$Divisor=$checkSheetRow["Divisor"];
			$Rate=$checkSheetRow["Rate"]*100/100;
			$Amount=$checkSheetRow["Amount"];
			$Locks=$checkSheetRow["Locks"];
			$Date=$checkSheetRow["Date"];
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101'>$ItemName</td>";
			echo"<td class='A0101' align='center'>$Branch</td>";
			echo"<td class='A0101' align='center'>$Job</td>";
			echo"<td class='A0101' align='center'>$Name</td>";
			echo"<td class='A0101' align='center'>$MonthSTR</td>";	
			echo"<td  class='A0101' align='center'>$Rate %</td>";
			echo"<td  class='A0101' align='right'>$Amount</td>";
			echo"<td  class='A0101' align='center'>$Month</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>