<?php
//17 员工借支						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("
SELECT A.Id,A.Mid,A.Number,A.PayDate,A.Amount,A.InDate,A.Payee,A.Remark,A.Locks,A.Operator,B.Name,C.Title
FROM $DataIn.cwygjz A
LEFT JOIN $DataPublic.staffmain B ON A.Number=B.Number
LEFT JOIN $DataPublic.my2_bankinfo C ON C.Id=A.BankId
WHERE A.Id='$Id_Remark'
",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='40' class='A1111'>序号</td>
<td width='60' class='A1101'>借支员工</td>
<td width='80' class='A1101'>借支日期</td>
<td width='60' class='A1101'>借支金额</td>
<td width='150' class='A1101'>结付银行</td>
<td width='40' class='A1101'>借据</td>
<td width='60' class='A1101'>经手人</td>
<td width='80' class='A1101'>还款日期</td>
<td width='400' class='A1101'>备注</td>
</tr>";
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/cwygjz/",$SinkOrder,$motherSTR);
	do{
		
		$Id=$checkRow["Id"];
		$Name=$checkRow["Name"];
		$Number=$checkRow["Number"];
		$Amount=$checkRow["Amount"];
		$BankName=$checkRow["Title"];
		$Payee=$checkRow["Payee"];
		$PayDate=$checkRow["PayDate"];
		if($checkRow["InDate"]=="0000-00-00"){
			$LockRemark="";
			if($checkRow["Mid"]==0){
				$InDate="<div class='redB'>未还款</div>";
				}
			else{
				$InDate="<div class='yellowB'>准备还款</div>";
				}
			}
		else{
			$InDate="<div class='greenB'>".$checkRow["InDate"]."</div>";
			$LockRemark="已从薪资扣除，操作锁定！修改需取消结付。";
			}
		$Remark=$checkRow["Remark"]==""?"&nbsp;":$checkRow["Remark"];
		$Operator=$checkRow["Operator"];
		$Locks=$checkRow["Locks"];		
		
		if($Payee==1){
			$Payee="J".$Id.".jpg";
			$Payee=anmaIn($Payee,$SinkOrder,$motherSTR);
			$Payee="<span onClick='OpenOrLoad(\"$Dir\",\"$Payee\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Payee="-";
			}
				
		//输出首行前段
		echo"<tr><td class='A0111' align='center' height='20'>$i</td>";	
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101' align='center'>$PayDate</td>";
		echo"<td class='A0101' align='right'>$Amount</td>";
		echo"<td class='A0101'>$BankName</td>";
		echo"<td class='A0101'  align='center'>$Payee</td>";
		echo"<td class='A0101' align='center'>$Operator</td>";
		echo"<td class='A0101' align='center'>$InDate</td>";
		echo"<td class='A0101'>$Remark</td>";
		echo"</tr>";
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>