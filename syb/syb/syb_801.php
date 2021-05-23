<?php
//电信
//代码共享-EWEN 2012-08-19
//中港运费、入仓费
$MonthSTR=$Month==""?"":" AND  DATE_FORMAT(B.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='80' class='A1001'>出货日期</td>
<td width='80' class='A1001'>货运公司</td>
<td width='100' class='A1001'>目的地</td>
<td width='80' class='A1001'>提单号码</td>
<td width='120' class='A1001'>Invoice</td>
<td width='40' class='A1001'>状态</td>
<td width='40' class='A1001'>件数</td>
<td width='60' class='A1001'>公司称重</td>
<td width='60' class='A1001'>上海称重</td>
<td width='80' class='A1001'>单价(元/KG)</td>
<td width='80' class='A1001'>运费(RMB)</td>
<td width='80' class='A1001'>入仓费(HKD)</td>
<td width='80' class='A1001'>小计(RMB)</td>
</tr>
<tr>
<td colspan='14' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT B.Date,D.Forshort, A.Termini,A.ExpressNO,B.InvoiceNO,B.InvoiceFile,A.Estate,A.BoxQty,A.mcWG,C.forwardWG,A.Price,A.Amount AS Amount ,A.depotCharge,(A.Amount+A.depotCharge*$HKD_Rate) AS AmountRMB 
	FROM $DataIn.ch4_freight_declaration A LEFT JOIN $DataIn.ch1_shipmain B ON B.Id=A.chId LEFT JOIN $DataIn.ch3_forward C ON C.chId=A.chId LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=A.CompanyId WHERE 1 AND B.Date>='2008-07-01' $MonthSTR $EstateSTR ORDER BY B.Date DESC",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141222 app採集單月紀錄
		$detailList[] = $checkRow;
		//cabbage 20141222 紀錄檔案的路徑
		$detailList[count($detailList) - 1]["invoiceFilePath"] = "/download/invoice/".$checkRow["InvoiceNO"].".pdf";
		$detailList[count($detailList) - 1]["billFilePath"] = "/download/expressbill/".$checkRow["ExpressNO"].".jpg";

		$Date=$checkRow["Date"];
		$Forshort=$checkRow["Forshort"];
		$Termini=$checkRow["Termini"];
		$ExpressNO=$checkRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		$InvoiceNO=$checkRow["InvoiceNO"];
		$InvoiceFile=$checkRow["InvoiceFile"];
		$f=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未结付</span>":"<span class=\"greenB\">已结付</span>";
		$BoxQty=$checkRow["BoxQty"];
		$mcWG=$checkRow["mcWG"];
		$forwardWG=$checkRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
		$Price=$checkRow["Price"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$depotCharge=sprintf("%.2f",$checkRow["depotCharge"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$Amount=number_format($Amount);
		$depotCharge=$depotCharge==0?"&nbsp;":number_format($depotCharge);
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80'  class='A0101' align='center'>$Date</td>
			<td width='80' class='A0101'>$Forshort</td>
			<td width='100' class='A0101'>$Termini</td>
			<td width='80' class='A0101' align='center'>$ExpressNO</td>
			<td width='120' class='A0101'>$InvoiceFile</td>
			<td width='40' class='A0101' align='center'>$Estate</td>
			<td width='40' class='A0101' align='right'>$BoxQty</td>
			<td width='60' class='A0101'  align='right'>$mcWG</td>
			<td width='60' class='A0101' align='right'>$forwardWG</td>
			<td width='80' class='A0101' align='right'>$Price</td>
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='80' class='A0101' align='right'>$depotCharge</td>
			<td width='80' class='A0101' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='80'  class='A0101'>&nbsp;</td>
	<td width='80'  class='A0101'>&nbsp;</td>
	<td width='100' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='120' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0100'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='80' class='A0100'>&nbsp;</td>
	<td width='80' class='A0100'>&nbsp;</td>
	<td width='80' class='A0100'>&nbsp;</td>
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
<td  class='A0100'>&nbsp;</td>
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