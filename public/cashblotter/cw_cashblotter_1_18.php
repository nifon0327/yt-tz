<?php
//18 社保缴费						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.sbpaymain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='100' class='A1101'>类型</td>
<td width='60' class='A1101'>员工姓名</td>
<td width='60' class='A1101'>部门</td>
<td width='60' class='A1101'>职位</td>
<td width='70' class='A1101'>缴费月份</td>
<td width='70' class='A1101'>个人缴费</td>
<td width='70' class='A1101'>公司缴费</td>
<td width='80' class='A1101'>小计</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/sbjf/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
	S.Id,S.Mid,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Date,S.Estate,S.Locks,
	P.Name,S.TypeId
	FROM $DataIn.sbpaysheet S
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
	WHERE S.Mid='$Mid' 
	ORDER BY S.Month DESC,S.BranchId,S.JobId,P.Number
	
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
			$Name=$checkSheetRow["Name"];
			$BranchId=$checkSheetRow["BranchId"];
			$JobId=$checkSheetRow["JobId"];
			$Month=$checkSheetRow["Month"];
			$mAmount=$checkSheetRow["mAmount"];
			$cAmount=$checkSheetRow["cAmount"];
			$Amount=sprintf("%.2f",$mAmount+$cAmount);
			$EstateSTR=$Estate==0?"<div align='center' class='greenB'>已付</div>":"<div align='center' class='redB'>状态错</div>";
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
		   switch($checkSheetRow["TypeId"]){
						case 1: $TypeName="社保";break;
						case 2: $TypeName="公积金";break;
						case 3: $TypeName="意外险";break;
				}
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$TypeName</td>";
			echo"<td class='A0101'>$Name</td>";
			echo"<td class='A0101'>$Branch</td>";
			echo"<td class='A0101'>$Job</td>";
			echo"<td class='A0101'  align='center'>$Month</td>";
			echo"<td class='A0101' align='right'>$mAmount</td>";
			echo"<td class='A0101' align='right'>$cAmount</td>";
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