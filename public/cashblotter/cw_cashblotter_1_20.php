<?php
//20 快递费						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw9_expsheet A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='70' class='A1101'>寄件日期</td>
<td width='80' class='A1101'>快递公司</td>
<td width='120' class='A1101'>提单号码</td>
<td width='40' class='A1101'>件数</td>
<td width='40' class='A1101'>重量</td>
<td width='50' class='A1101'>金额</td>
<td width='40' class='A1101'>付款方式</td>
<td width='50' class='A1101'>经手人</td>
<td width='400' class='A1101'>备注</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/chexpress/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
	S.Id,S.Mid,S.Date,S.ExpressNO,S.BoxQty,S.Weight,S.Amount,S.Type,S.Operator,S.Remark,S.Estate,S.Locks,P.Name AS HandledBy,D.Forshort
 	FROM $DataIn.ch9_expsheet S
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
	WHERE S.Mid='$Mid' ORDER BY S.Date DESC
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
			$Date=$checkSheetRow["Date"];
			$Forshort=$checkSheetRow["Forshort"];
			$ExpressNO=$checkSheetRow["ExpressNO"];
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			$BoxQty=$checkSheetRow["BoxQty"];
			$Weight=$checkSheetRow["Weight"];
			$Amount=$checkSheetRow["Amount"];
			$Type=$checkSheetRow["Type"]==1?"到付":"寄付";		
			$HandledBy=$checkSheetRow["HandledBy"];
			$Remark=$checkSheetRow["Remark"]==""?"&nbsp;":$checkSheetRow["Remark"];
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101'>$Forshort</td>";
			echo"<td class='A0101' align='center'>$ExpressNO</td>";
			echo"<td class='A0101' align='right'>$BoxQty</td>";
			echo"<td class='A0101' align='right'>$Weight</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101'>$Type</td>";
			echo"<td class='A0101' align='center'>$HandledBy</td>";
			echo"<td  class='A0101'>$Remark</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>