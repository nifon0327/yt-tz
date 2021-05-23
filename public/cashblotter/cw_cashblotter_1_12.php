<?php
//12汇兑转入
//13汇兑转出
//ewen 2013-09-04 OK
$checkSql=mysql_query("
					  SELECT D.Id,D.PayDate,D.OutCurrency,D.OutAmount,D.Rate,D.InCurrency,D.InAmount,D.BillNumber,D.Bill,D.Remark,D.Locks,D.Operator,C.Symbol AS OutCurrency,I.Title AS InTitle,O.Title AS OutTitle
	FROM $DataIn.cw5_fbdh D
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.OutCurrency
	LEFT JOIN $DataPublic.my2_bankinfo I ON I.Id=D.InBankId
	LEFT JOIN $DataPublic.my2_bankinfo O ON O.Id=D.OutBankId
	WHERE D.Id='$Id_Remark' ORDER BY D.Id DESC
	",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='30' class='A1111'>序号</td>
<td width='70' class='A1101'>日期</td>
<td width='80' class='A1101'>转出银行</td>
<td width='30' class='A1101'>转出<br>货币</td>
<td width='80' class='A1101'>转出金额</td>
<td width='80' class='A1101'>汇率</td>
<td width='80' class='A1101'>转入银行</td>
<td width='30' class='A1101'>转入<br>货币</td>
<td width='80' class='A1101'>转入金额</td>
<td width='60' class='A1101'>结汇凭证</td>
<td class='A1101'>备注</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/fbdh/",$SinkOrder,$motherSTR);
	$i=1;
	do{			
		//**********
		$Id=$checkRow["Id"];
		$PayDate=$checkRow["PayDate"];
		$OutCurrency=$checkRow["OutCurrency"];
		$OutAmount=$checkRow["OutAmount"];
		$OutBankName=$checkRow["OutTitle"]==""?"&nbsp;":$checkRow["OutTitle"];
		$InBankName=$checkRow["InTitle"]==""?"&nbsp;":$checkRow["InTitle"];
		$Rate=$checkRow["Rate"];
		$InCurrency=$checkRow["InCurrency"];
		$checkCurrency=mysql_fetch_array(mysql_query("SELECT Symbol FROM $DataPublic.currencydata WHERE Id='$InCurrency' LIMIT 1",$link_id));
		$InCurrency=$checkCurrency["Symbol"]==""?"&nbsp;":$checkCurrency["Symbol"];
		$InAmount=$checkRow["InAmount"];
		$Remark=$checkRow["Remark"]==""?"&nbsp;":$checkRow["Remark"];		
		$BillNumber=$checkRow["BillNumber"];
		$Bill=$checkRow["Bill"];
		if($Bill==1){
			$Bill="DH".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$BillNumber="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$BillNumber</span>";
			}
		//*********		
		echo"<tr>
		<td align='center' class='A0111' height='25'>$i</td>
		<td align='center' class='A0101'>$PayDate</td>
		<td class='A0101'>$OutBankName</td>
		<td align='center' class='A0101'>$OutCurrency</td>
		<td align='right' class='A0101'>$OutAmount</td>
		<td align='right' class='A0101'>$Rate</td>
		<td class='A0101'>$InBankName</td>
		<td align='center' class='A0101'>$InCurrency</td>
		<td align='right' class='A0101'>$InAmount</td>
		<td align='center' class='A0101'>$BillNumber</td>
		<td class='A0101'>$Remark</td>
		";
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>