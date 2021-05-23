<?php
//10	 其他收入
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Receipt,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw4_otherinmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='60' class='A1101'>类别</td>
<td width='70' class='A1101'>收款日期</td>
<td width='100' class='A1101'>收款单号</td>
<td width='60' class='A1101'>收款金额</td>
<td width='40' class='A1101'>币别</td>
<td width='300' class='A1101'>收款备注</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	//$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/otherin/",$SinkOrder,$motherSTR);	
	$i=1;
	$ImgDir="download/otherin/";
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
//	$ImgDir="download/cwadminicost/";
	$Payee=$checkRow["Payee"];
	$Receipt=$checkRow["Receipt"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
	S.Amount,S.getmoneyNO,S.Remark AS sheeRemark, C.Symbol AS Currency,S.payDate AS sheeDate,S.Operator,T.Name AS TypeName
 	FROM $DataIn.cw4_otherinsheet S
	LEFT JOIN $DataPublic.cw4_otherintype T ON S.TypeId=T.Id
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE  S.Mid='$Mid' ORDER BY S.payDate DESC
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
			$TypeName=$checkSheetRow["TypeName"];
			$Date=$checkSheetRow["sheeDate"];
			$getmoneyNO=$checkSheetRow["getmoneyNO"];
			$f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
			$getmoneyNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">收款单$getmoneyNO</a>";
			$Amount=$checkSheetRow["Amount"];
			$Currency=$checkSheetRow["Currency"];
			$Remark=$checkSheetRow["sheeRemark"]==""?"&nbsp;":$checkSheetRow["sheeRemark"];
			
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";
			echo"<td  class='A0101'>$TypeName</td>";
			echo"<td class='A0101' align='center'>$sheeDate</td>";
			echo"<td class='A0101' align='center'>$getmoneyNO</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101' align='center'>$Currency</td>";	
			echo"<td class='A0101' >$Remark</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>