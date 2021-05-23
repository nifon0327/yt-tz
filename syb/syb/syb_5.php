<?php 
//电信
//代码共享-EWEN 2012-08-19
//行政费用
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.PayDate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate<=1"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" ";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.TypeId IN($Parameters)";
echo"<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='100' class='A1001'>借支员工</td>
<td  class='A1001'>备注</td>
<td width='80' class='A1001'>经手人</td>
<td width='80' class='A1001'>借支日期</td>
<td width='70' class='A1001'>借支凭证</td>
<td width='60' class='A1001'>借支货币</td>
<td width='90' class='A1001'>借支金额</td>
<td width='90' class='A1001'>还款日期</td>
</tr>
<tr>
<td colspan='9' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>";
//读取记录
$checkSql=mysql_query("SELECT A.Id,A.Mid,A.Number,A.PayDate,A.Amount,A.InDate,A.Payee,A.Remark,A.Locks,A.Operator,M.Name,B.Title
FROM $DataIn.cwygjz A
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId 
LEFT JOIN $DataPublic.staffmain M ON A.Number=M.Number 
 WHERE 1 $MonthSTR   $EstateSTR   ORDER BY A.Date DESC,A.Id DESC",$link_id);

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){

$tmpArray = array($checkSql, $ShowInfo);

	do{
		//add by cabbage 20141208 app採集單月紀錄
		$detailList[$i - 1] = $checkRow;
		
		$Id=$checkRow["Id"];
		$Content=$checkRow["Remark"];
		$Name=$checkRow["Name"];
		$Symbol="RMB";
		$Operator=$checkRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$checkRow["PayDate"];
		$InDate=$checkRow["InDate"];
		$Bill=$checkRow["Payee"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		//$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
		//$AmountRMB=number_format($AmountRMB);
		 $Dir=anmaIn("../download/cwygjz/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="J".$Id.".jpg";
			
			//add by cabbage 20141209 app紀錄附件的檔案路徑
			$detailList[$i - 1]["FilePath"] = "/download/cwygjz/".$Bill;
			
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			 }
		 else{$Bill="&nbsp;";}
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101' align='center'>$Name</td>
			<td class='A0101'>$Content&nbsp;</td>
			<td width='80' class='A0101' align='center'>$Operator</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='70' class='A0101'  align='center'  bgcolor='#ECEAED'>$Bill</td>
			<td width='60' class='A0101' align='right'>$Symbol</td>
			<td width='90' class='A0101' align='right'>$Amount</td>
			<td width='90' class='A0100' align='right'><span class='greenB'>$InDate</span></td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<25;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='90' class='A0101'>&nbsp;</td>
	<td width='90' class='A0100'>&nbsp;</td>
	</tr>";
	}
$SumAmount=number_format(sprintf("%.0f",$SumAmount));
echo"</table></div></td></tr>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td height='20' class='A0111' colspan='2'>合计</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr></table>";
?>