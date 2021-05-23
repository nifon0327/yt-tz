<?php 
//电信
//代码共享-EWEN 2012-08-19
//报关/商检费
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
<td width='80' class='A1001'>报关公司</td>
<td width='120' class='A1001'>研砼Invoice</td>
<td width='80' class='A1001'>目的地</td>
<td width='80' class='A1001'>报关单号码</td>
<td width='60' class='A1001'>状态</td>
<td width='80' class='A1001'>报关费</td>
<td width='80' class='A1001'>商检费</td>
<td width='80' class='A1001'>其它费用</td>
<td width='80' class='A1001'>合计</td>
</tr>
<tr>
<td colspan='10' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT 
					  B.Date,B.InvoiceNO,B.InvoiceFile,
					  C.Forshort,A.Termini,A.ExpressNO,A.declarationCharge,A.checkCharge,A.Remark,A.Estate,
					  (A.carryCharge+A.xyCharge+A.wfqgCharge+A.ccCharge+A.djCharge+A.stopcarCharge+A.expressCharge+A.otherCharge) AS OtherAmount 
	FROM $DataIn.ch4_freight_declaration A
	LEFT JOIN $DataIn.ch1_shipmain B ON A.chId=B.Id
	LEFT JOIN $DataPublic.freightdata C ON C.CompanyId=A.CompanyId 
	WHERE 1  $MonthSTR $EstateSTR ORDER BY B.Date DESC",$link_id);

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/declarationbill/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141222 app採集單月紀錄
		$detailList[] = $checkRow;
		//cabbage 20141222 紀錄檔案的路徑
		$detailList[count($detailList) - 1]["invoiceFilePath"] = "/download/invoice/".$checkRow["InvoiceNO"].".pdf";
		$detailList[count($detailList) - 1]["billFilePath"] = "/download/declarationbill/".$checkRow["ExpressNO"].".jpg";
		
		$Date=$checkRow["Date"];
		$Forshort=$checkRow["Forshort"];
		$InvoiceNO=$checkRow["InvoiceNO"];
		$InvoiceFile=$checkRow["InvoiceFile"];
		$f=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
		$Termini=$checkRow["Termini"];
		$ExpressNO=$checkRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未结付</span>":"<span class=\"greenB\">已结付</span>";
		
		$declarationCharge=$checkRow["declarationCharge"];
		$checkCharge=$checkRow["checkCharge"];
		$declarationCharge=sprintf("%.2f",$checkRow["declarationCharge"]);
		$checkCharge=sprintf("%.2f",$checkRow["checkCharge"]);
		$OtherAmount=sprintf("%.2f",$checkRow["OtherAmount"]);
		
		$AmountRMB=sprintf("%.2f",$declarationCharge+$checkCharge+$OtherAmount);
		if ($AmountRMB<=0) continue;
		
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80'  class='A0101' align='center'>$Date</td>
			<td width='80' class='A0101'>$Forshort</td>
			<td width='120' class='A0101'>$InvoiceFile</td>
			<td width='80' class='A0101'>$Termini</td>
			<td width='80' class='A0101'>$ExpressNO</td>
			<td width='60' class='A0101' align='center'>$Estate</td>
			<td width='80' class='A0101' align='right'>$declarationCharge</td>
			<td width='80' class='A0101'  align='right'>$checkCharge</td>
			<td width='80' class='A0101'  align='right'>$OtherAmount</td>
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
	<td width='120' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>