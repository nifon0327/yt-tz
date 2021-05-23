<?php
//6	BOM供应商税款
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw2_gysskmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='120' class='A1101'>供应商</td>
<td width='40' class='A1101'>货币</td>
<td width='60' class='A1101'>税款金额</td>
<td width='300' class='A1101'>说明</td>
<td width='80' class='A1101'>发票号</td>
<td width='60' class='A1101'>请款人</td>
<td width='70' class='A1101'>请款日期</td>
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
	$ImgDir="download/cwgyssk/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT S.Id,S.Mid,S.Forshort,S.InvoiceNUM,S.InvoiceFile,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency
 	FROM $DataIn.cw2_gyssksheet S
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
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
			$Forshort=$checkSheetRow["Forshort"];
			$Currency=$checkSheetRow["Currency"];
			$Amount=$checkSheetRow["Amount"];
			$Remark=$checkSheetRow["Remark"];
			$InvoiceNUM=$checkSheetRow["InvoiceNUM"];
			$InvoiceFile=$checkSheetRow["InvoiceFile"];
			if($InvoiceFile==1){
				$InvoiceFile="S".$Id;
				$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
				$InvoiceNUM="<a href=\"../public/openorload.php?d=$Dir&f=$InvoiceFile&Type=&Action=7\" target=\"download\">$InvoiceNUM</a>";
				}
			$Operator=$checkSheetRow["Operator"];
			include "../model/subprogram/staffname.php";
 			$Date=$checkSheetRow["Date"];	

			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101'>$Forshort</td>";
			echo"<td class='A0101' align='right'>$Currency</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101' >$Remark</td>";
			echo"<td class='A0101' align='center'>$InvoiceNUM</td>";	
			echo"<td  class='A0101' align='center'>$Operator</td>";
			echo"<td  class='A0101' align='center'>$Date</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>