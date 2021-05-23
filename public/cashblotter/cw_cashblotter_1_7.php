<?php
//7	客户货款收入				OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PreAmount,A.PayAmount,A.Handingfee,A.Remark AS PayRemark,B.Title,C.Forshort
FROM $DataIn.cw6_orderinmain A 
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId
WHERE A.PayDate='$PayDate' AND A.Remark LIKE '$Id_Remark%'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>收款日期</td>
<td width='60' class='A1101'>客户</td>
<td width='60' class='A1101'>手续费</td>
<td width='60' class='A1101'>收款总额</td>
<td width='60' class='A1101'>预收金额</td>
<td width='60' class='A1101'>本次实收</td>
<td width='100' class='A1101'>结付银行</td>
<td width='30' class='A1101'>出帐<br>凭证</td>
<td width='30' class='A1101'>TT<br>备注</td>

<td width='40' class='A1101'>序号</td>
<td width='70' class='A1101'>出货日期</td>
<td width='120' class='A1101'>Invoice</td>
<td width='60' class='A1101'>出货金额</td>
<td width='80' class='A1101'>本次收款</td>
</tr>";
$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
$d2=anmaIn("download/cwjzpz/",$SinkOrder,$motherSTR);
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
	
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$Forshort=$checkRow["Forshort"];
	$Handingfee=$checkRow["Handingfee"];
	$PayAmount=$checkRow["PayAmount"];
	$PreAmount=$checkRow["PreAmount"];
	$SumAmount=$PreAmount+$PayAmount;
	$BankName=$checkRow["Title"];
 	$jzpzFile="../download/cwjzpz/Z" . $Mid.".pdf";
    if(file_exists($jzpzFile)){
    	$f2=anmaIn("Z" . $Mid.".pdf",$SinkOrder,$motherSTR);
		$jzpzFile="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">查 看</a>";
        }
	else{
    	$jzpzFile="&nbsp;"; 
        }
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT S.Id,S.Mid,S.Amount,S.chId,C.InvoiceNO,C.InvoiceFile,C.Date
 	FROM $DataIn.cw6_orderinsheet S
	LEFT JOIN $DataIn.ch1_shipmain C ON C.Id=S.chId
	WHERE  S.Mid='$Mid' ORDER BY C.Date DESC
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//收款日期
		echo"<td rowspan='$Rowspan' class='A0101' valign='top'>$Forshort</td>";											//客户
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Handingfee</td>";					//手续费
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$SumAmount</td>";					//收款总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PreAmount</td>";					//预收金额
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//实收金额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";					//结付银行	
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$jzpzFile</td>";							//出帐凭证	
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//TT备注	
		$j=1;
		do{
			//结付明细数据
			$Id=$checkSheetRow["Id"];
			$Date=$checkSheetRow["Date"];					//出货日期
			$Amount=$checkSheetRow["Amount"];			//本次收款金额
			$InvoiceNO=$checkSheetRow["InvoiceNO"];
			$InvoiceFile=$checkSheetRow["InvoiceFile"];//Invoice文件
			//出货金额计算
			$chId=$checkSheetRow["chId"];
			$checkShipAmount=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*M.Sign) AS ShipAmount FROM $DataIn.ch1_shipsheet S,$DataIn.ch1_shipmain M WHERE S.YandN=1 AND M.Id='$chId' AND S.Mid=M.Id",$link_id));
			$ShipAmount=$checkShipAmount["ShipAmount"];
			$ShipAmount=$ShipAmount==""?0:round($ShipAmount,2);
			if($Amount==$ShipAmount){
				$Amount="<span class='greenB'>$Amount</span>";
				}
			else{
				$Amount="<span class='redB'>$Amount</span>";
				}
			//加密参数
			$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
			$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101'>$InvoiceFile</td>";
			echo"<td class='A0101' align='right'>$ShipAmount</td>";	
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>