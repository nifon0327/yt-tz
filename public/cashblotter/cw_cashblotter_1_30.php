<?php
//货款返利			OK
//ewen 2013-09-04 OK
//读取记录
$checkSql=mysql_query("
					  SELECT B.Date AS PayDate,C.Forshort,B.Remark,B.Estate,
					  B.Did,D.Symbol,IFNULL(SUM(B.Amount),0) AS Amount,IFNULL(SUM(B.Amount*D.Rate),0) AS AmountRMB 
					  FROM $DataIn.cw2_hksheet B
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId 
					  LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
					  WHERE  B.Did='$Id_Remark' GROUP BY B.Id
					  ",$link_id);

echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>
<tr bgcolor='#CCC' align='center' >
<td width='50' height='20' class='A1111'>序号</td>
<td width='80' class='A1101'>返利日期</td>
<td width='300' class='A1101'>供应商</td>
<td class='A1101'>备注</td>
<td width='50' class='A1101'>状态</td>
<td width='60' class='A1101'>货币</td>
<td width='80' class='A1101'>金额</td>
<td width='80' class='A1101'>转RMB金额</td>
</tr>
";
if($checkRow=mysql_fetch_array($checkSql)){

	do{
		//add by cabbage 20141119 app採集單月紀錄
		$detailList[] = $checkRow;
		
		$Id=$checkRow["Id"];
		$PayDate=$checkRow["PayDate"];
		$Forshort=$checkRow["Forshort"];
		$Remark=$checkRow["Remark"];
		$Estate=$checkRow["Did"]==0?"<span class=\"redB\">未抵付</span>":"<span class=\"greenB\">已抵付</span>";
		$Symbol=$checkRow["Symbol"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#FFF' >
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80' class='A0101'>$PayDate</td>
			<td width='300' class='A0101'>$Forshort</td>
			<td class='A0101'>$Remark</td>
			<td width='50' class='A0101' align='center' >$Estate</td>
			<td width='60' class='A0101' align='center'>$Symbol</td>		
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='80' class='A0111' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
echo "</table>";
echo "</div>";
?>