<?php
//16 试用工薪资			OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cwxztempmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='70' class='A1101'>薪资<br>月份</td>
<td width='50' class='A1101'>部门</td>
<td width='50' class='A1101'>职位</td>
<td width='70' class='A1101'>试用期员工</td>
<td width='70' class='A1101'>1倍薪工资</td>
<td width='70' class='A1101'>1.5倍薪工资</td>
<td width='70' class='A1101'>2倍薪工资</td>
<td width='70' class='A1101'>3倍薪工资</td>
<td width='70' class='A1101'>夜宵补助</td>
<td width='70' class='A1101'>小计</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$djAmount=$checkRow["djAmount"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/cwxz/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("
	SELECT 
	S.Id,S.Mid,S.Month,S.Number,S.Axz,S.Bxz,S.Cxz,S.Dxz,S.YBbz,S.Amount,S.Estate,S.Locks,P.Name,B.Name AS Branch,J.Name AS Job
 	FROM $DataIn.cwxztempsheet S
	LEFT JOIN $DataIn.stafftempmain P ON S.Number=P.Number
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
			$Month=$checkSheetRow["Month"];
			$Number=$checkSheetRow["Number"];
			$Name=$checkSheetRow["Name"];
			$Branch=$checkSheetRow["Branch"];
			$Job=$checkSheetRow["Job"];
			$Axz=$checkSheetRow["Axz"];
			$Bxz=$checkSheetRow["Bxz"];
			$Cxz=$checkSheetRow["Cxz"];
			$Dxz=$checkSheetRow["Dxz"];
			$YBbz=$checkSheetRow["YBbz"];
			$Amount=sprintf("%.0f",$checkSheetRow["Amount"]);
			$Date=$checkSheetRow["Date"];
			
			$Axz=SpaceValue0($Axz);
			$Bxz=SpaceValue0($Bxz);
			$Cxz=SpaceValue0($Cxz);
			$Dxz=SpaceValue0($Dxz);
			$YBbz=SpaceValue0($YBbz);
		
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";
			echo"<td class='A0101' align='center'>$Month</td>";	
			echo"<td class='A0101' align='center'>$Branch</td>";
			echo"<td class='A0101' align='center'>$Job</td>";
			echo"<td class='A0101'>$Name</td>";
			echo"<td class='A0101' align='right'>$Axz</td>";
			echo"<td class='A0101' align='right'>$Bxz</td>";
			echo"<td class='A0101' align='right'>$Cxz</td>";
			echo"<td class='A0101' align='right'>$Dxz</td>";
			echo"<td class='A0101' align='right'>$YBbz</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>