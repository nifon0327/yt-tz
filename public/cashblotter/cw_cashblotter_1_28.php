<?php
//28 入仓费			OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT 
					  A.Id,A.PayDate,A.PayAmount,A.depotCharge AS PaydepotCharge,A.Payee,A.Remark AS PayRemark,A.declarationCharge AS Paydeclaration, A.checkCharge AS PaycheckCharge,B.Title
FROM $DataIn.cw4_freight_declaration A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付总额</td>
<td width='50' class='A1101'>入仓费<br>总额</td>
<td width='50' class='A1101'>报关费<br>总额</td>
<td width='50' class='A1101'>商检费<br>总额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='30' class='A1101'>序号</td>
<td width='70' class='A1101'>出货日期</td>
<td width='80' class='A1101'>Invoice<br>或提货单</td>
<td width='40' class='A1101'>货运<br>公司</td>
<td width='50' class='A1101'>目的地</td>
<td width='90' class='A1101'>提单号码</td>
<td width='30' class='A1101'>件数</td>
<td width='40' class='A1101'>公司<br>称重</td>
<td width='40' class='A1101'>上海<br>称重</td>
<td width='50' class='A1101'>单价<br>(元/KG)</td>
<td width='50' class='A1101'>运费<br>(RMB)</td>
<td width='50' class='A1101'>入仓费<br>(HKD)</td>
<td width='50' class='A1101'>报关费用<br>(RMB)</td>
<td width='50' class='A1101'>商检费用<br>(RMB)</td>
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
	$PaydepotCharge=$checkRow["PaydepotCharge"];		//总报关费用
	$PaycheckCharge=$checkRow["PaycheckCharge"];	//总商检费用
	$Paydeclaration=$checkRow["Paydeclaration"];		//总报关费用

	$BankName=$checkRow["Title"];
	$ImgDir="download/cwfreight/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("
	SELECT
	S.Id,S.Mid,S.Termini,S.ExpressNO,S.BoxQty,S.mcWG,S.Price,S.depotCharge,S.Remark,S.Estate,S.Locks,C.Date,C.InvoiceNO,C.InvoiceFile,D.Forshort,W.forwardWG,S.checkCharge,S.declarationCharge,S.PayType
 	FROM $DataIn.ch4_freight_declaration S
	LEFT JOIN $DataIn.ch1_shipmain C ON S.chId=C.Id
	LEFT JOIN $DataIn.ch3_forward W ON W.chId=S.Mid
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
	WHERE  S.Mid='$Mid' AND S.TypeId='1'
	UNION ALL
	SELECT
	S.Id,S.Mid,S.Termini,S.ExpressNO,S.BoxQty,S.mcWG,S.Price,S.depotCharge,S.Remark,S.Estate,S.Locks,C.DeliveryDate AS Date,C.DeliveryNumber AS InvoiceNO,'' AS InvoiceFile,D.Forshort,W.forwardWG,S.checkCharge,S.declarationCharge,S.PayType
 	FROM $DataIn.ch4_freight_declaration S
	LEFT JOIN $DataIn.ch1_deliverymain C ON S.chId=C.Id
	LEFT JOIN $DataIn.ch3_forward W ON W.chId=S.Mid
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
	WHERE  S.Mid='$Mid' AND S.TypeId='2'
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PaydepotCharge</td>";
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$Paydeclaration</td>";
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PaycheckCharge</td>";
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行
		$j=1;
		do{
			//结付明细数据
			$Id=$checkSheetRow["Id"];
			$Date=$checkSheetRow["Date"];
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
			$Operator=$checkSheetRow["Operator"];
			$Id=$checkSheetRow["Id"];
			$Termini=$checkSheetRow["Termini"]==""?"&nbsp;":$checkSheetRow["Termini"];
			$ExpressNO=$checkSheetRow["ExpressNO"];
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";

			$BoxQty=$checkSheetRow["BoxQty"];
			$mcWG=$checkSheetRow["mcWG"];$forwardWG=$myRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
			$Price=$checkSheetRow["Price"];
			$Amount=sprintf("%.2f",$mcWG*$Price);
			$depotCharge=$checkSheetRow["depotCharge"];
			$Paydeclaration=$checkSheetRow["Paydeclaration"];//总报关费用
			$PaycheckCharge=$checkSheetRow["PaycheckCharge"];//总商检费用
			$declarationCharge=$checkSheetRow["declarationCharge"];//报关费用
			$checkCharge=$checkSheetRow["checkCharge"];//商检费用
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";
			echo"<td class='A0101' align='center'>$Date</td>";				//出货日期
			echo"<td class='A0101'>$InvoiceFile</td>";						//研砼Invoice
			echo"<td class='A0101'>$Forshort</td>";							//货运公司
			echo"<td class='A0101'>$Termini</td>";							//目的地
			echo"<td  class='A0101' align='center'>$ExpressNO</td>";	//提单号码
			echo"<td  class='A0101' align='right'>&nbsp;$BoxQty</td>";//件数
			echo"<td  class='A0101' align='right'>$mcWG</td>";			//称重
			echo"<td  class='A0101' align='right'>$forwardWG</td>";	//称重
			echo"<td  class='A0101' align='right'>&nbsp;$Price</td>";		//单价
			echo"<td  class='A0101' align='right'>$Amount</td>";			//运货
			echo"<td  class='A0101' align='right'>$depotCharge</td>";	//入仓费
			echo"<td  class='A0101' align='right'>$declarationCharge</td>";//报关费
			echo"<td  class='A0101' align='right'>$checkCharge</td>";	//商检费
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{

	}
?>