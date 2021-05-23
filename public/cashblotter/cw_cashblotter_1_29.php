<?php
//29 Forward杂费			OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw3_forward A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付总额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='30' class='A1101'>序号</td>
<td width='70' class='A1101'>发票日期</td>
<td width='80' class='A1101'>Invoice<br>或提货单</td>
<td width='40' class='A1101'>Forward<br>公司</td>
<td width='50' class='A1101'>入仓号</td>
<td width='90' class='A1101'>Forward<br>Invoice</td>
<td width='30' class='A1101'>件数</td>
<td width='40' class='A1101'>研砼<br>称重</td>
<td width='40' class='A1101'>上海<br>称重</td>
<td width='50' class='A1101'>金额<br>(HKD)</td>
<td width='70' class='A1101'>出货日期</td>
<td width='50' class='A1101'>ETD/ETA</td>
<td width='180' class='A1101'>备注</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	$d2=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);//提货单
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/cwforward/";
	$Checksheet=$checkRow["Checksheet"];
	$Payee=$checkRow["Payee"];
	$Receipt=$checkRow["Receipt"];
	include "../model/subprogram/cw0_imgview.php";
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("
	SELECT 
	S.PayType,S.Id,S.Mid,S.chId,S.HoldNO,S.ForwardNO,S.BoxQty,S.mcWG,S.forwardWG,S.Volume,S.Amount,S.InvoiceDate,S.ETD,S.Remark,S.Estate,S.Locks,S.Date,
	C.Date AS ShipDate,C.InvoiceNO,C.InvoiceFile,D.Forshort 
 	FROM $DataIn.ch3_forward S
	LEFT JOIN $DataIn.ch1_shipmain C ON S.chId=C.Id
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataIn.ch3_forward F ON F.CompanyId=S.CompanyId 
	WHERE  S.Mid='$Mid' AND S.TypeId='1' GROUP BY C.Id
	UNION ALL
	SELECT 
	S.PayType,S.Id,S.Mid,S.chId,S.HoldNO,S.ForwardNO,S.BoxQty,S.mcWG,S.forwardWG,S.Volume,S.Amount,S.InvoiceDate,S.ETD,S.Remark,S.Estate,S.Locks,S.Date,
	C.DeliveryDate AS ShipDate,C.DeliveryNumber AS InvoiceNO,'' AS InvoiceFile,D.Forshort 
 	FROM $DataIn.ch3_forward S
	LEFT JOIN $DataIn.ch1_deliverymain C ON S.chId=C.Id
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataIn.ch3_forward F ON F.CompanyId=S.CompanyId 
	WHERE  S.Mid='$Mid' AND S.TypeId='2' GROUP BY C.Id
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
			$ShipDate=$checkSheetRow["ShipDate"];
			$InvoiceNO=$checkSheetRow["InvoiceNO"];
			$InvoiceFile=$checkSheetRow["InvoiceFile"];
			//加密参数
			$f=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
			if($InvoiceFile!=""){//invoice
				$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
				 }
			else {
				$filename="../download/DeliveryNumber/$InvoiceNO.pdf";
				if(file_exists($filename)){
				   $InvoiceFile="<a href=\"openorload.php?d=$d2&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";}
				 else $InvoiceFile="$InvoiceNO";
				 }
			$Forshort=$checkSheetRow["Forshort"];
			$HoldNO=$checkSheetRow["HoldNO"]==""?"&nbsp;":$checkSheetRow["HoldNO"];

			$ForwardNO=$checkSheetRow["ForwardNO"];
			//提单
			$Lading="../download/expressbill/".$ForwardNO.".jpg";
			if(file_exists($Lading)){
				$f1=anmaIn($ForwardNO.".jpg",$SinkOrder,$motherSTR);
				$ForwardNO="<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ForwardNO</span>";
				}

			$BoxQty=$checkSheetRow["BoxQty"];
			$mcWG=$checkSheetRow["mcWG"];
			$forwardWG=$checkSheetRow["forwardWG"];
			$Amount=$checkSheetRow["Amount"];
			$InvoiceDate=$checkSheetRow["InvoiceDate"];
			$ETD=$checkSheetRow["ETD"]==""?"&nbsp;":$checkSheetRow["ETD"];
			$Remark=$checkSheetRow["Remark"]==""?"&nbsp;":$checkSheetRow["Remark"];
			$Locks=$checkSheetRow["Locks"];
			$Estate=$checkSheetRow["Estate"];
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";
			echo"<td class='A0101' align='center'>$InvoiceDate</td>";//出货日期
			echo"<td class='A0101'>$InvoiceFile</td>";//研砼Invoice
			echo"<td class='A0101'>$Forshort</td>";//Forward公司
			echo"<td class='A0101'>$HoldNO</td>";//入仓号
			echo"<td  class='A0101'>$ForwardNO</td>";//Forward Invoice
			echo"<td  class='A0101' align='right'>$BoxQty</td>";//件数
			echo"<td  class='A0101' align='right'>$mcWG</td>";//MC称重
			echo"<td  class='A0101' align='right'>$forwardWG</td>";//HK称重
			echo"<td  class='A0101' align='right'>$Amount</td>";//金额
			echo"<td  class='A0101' align='center'>$ShipDate</td>";//发票日期
			echo"<td  class='A0101'>$ETD</td>";//TT
			echo"<td  class='A0101' align='center'>$Remark</td>";//备注
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{

	}
?>