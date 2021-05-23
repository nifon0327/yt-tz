<?php 
//电信
//代码共享-EWEN 2012-08-19
//模具费退回
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR="AND ( A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='380' class='A1001'>模具项目</td>
<td width='150' class='A1001'>供应商</td>
<td width='100' class='A1001'>扫描附件</td>
<td class='A1001'>备注</td>
<td width='80' class='A1001'>请款日期</td>
<td width='50' class='A1001'>状态</td>
<td width='80' class='A1001'>退回金额</td>
</tr>
<tr>
<td colspan='8' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT A.Id,A.Mid,A.Moq,A.ItemName ,A.OutAmount ,A.Remark,A.Operator,A.Date,A.Locks,A.Estate,S.Provider  
FROM $DataIn.cw16_modelfee  A 
LEFT JOIN cwdyfsheet S  ON A.Mid=S.Id
WHERE 1  $MonthSTR $EstateSTR  ORDER BY A.Date DESC",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141120 app採集單月紀錄
		$detailList[] = $checkRow;
	
		$Id=$checkRow["Id"];
		$Mid=$myRow["Mid"];
		$Moq=$checkRow["Moq"];
		$ItemName=$checkRow["ItemName"];
		$Remark=$checkRow["Remark"]==""?"&nbsp":$checkRow["Remark"];
		$Operator=$checkRow["Operator"];
        $Provider =$checkRow["Provider"];	
		include"../model/subprogram/staffname.php";
		$Date=$checkRow["Date"];
		$Locks=$checkRow["Locks"];			
		$Bill=$checkRow["Bill"];
		$Dir=anmaIn("download/modelfee/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="M".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
  $Estate=$checkRow["Estate"];		
	switch($Estate){
				case "3":
					$Estate="<div align='center' class='yellowB' title='审核通过'>√.</div>";
               case "0":
					$Estate="<div align='center' class='greenB' title='已结付'>√.</div>";
					break;
				}
		$Amount=sprintf("%.2f",$checkRow["OutAmount"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='380' class='A0101' >$ItemName</td>
			<td width='150' class='A0101'>$Provider</td>
			<td width='100' class='A0101' bgcolor='#ECEAED' align='center'>$Bill</td>
			<td class='A0101'>$Remark</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='50' class='A0101' align='center' bgcolor='#ECEAED'>$Estate</td>		
			<td width='80' class='A0101' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='380'  class='A0101'>&nbsp;</td>
	<td width='150'  class='A0101'>&nbsp;</td>
	<td width='100' class='A0101' bgcolor='#ECEAED'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='50' class='A0101' bgcolor='#ECEAED'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>