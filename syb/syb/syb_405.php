<?php
//电信
//代码共享-EWEN 2012-08-19
//条件
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.TypeId IN($Parameters)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='100' class='A1001'>费用名称</td>
<td  class='A1001'>请款说明405</td>
<td width='80' class='A1001'>请款人</td>
<td width='80' class='A1001'>请款日期</td>
<td width='70' class='A1001'>请款凭证</td>
<td width='70' class='A1001'>收回凭证</td>
<td width='60' class='A1001'>请款货币</td>
<td width='90' class='A1001'>请款金额</td>
<td width='90' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='9' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT A.Id,A.Content,A.Operator,A.Bill,A.Date,C.Symbol,A.Amount,(A.Amount*C.Rate) AS AmountRMB,B.Name AS ItemName ,A.OtherId,O.Estate AS OtherEstate,O.getmoneyNO,A.Property,M.InvoiceNO,M.cwSign,M.InvoiceFile
										  FROM $DataIn.hzqksheet A
										  LEFT JOIN $DataPublic.adminitype B ON B.TypeId=A.TypeId
										  LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
										  LEFT JOIN $DataIn.cw4_otherinsheet O ON O.Id=A.OtherId AND A.Property=1
										  LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=A.OtherId AND A.Property=2 
										  WHERE 1 AND A.Date>='2008-07-01' 
										  $MonthSTR $Parameters
										  $EstateSTR   ORDER BY A.Date DESC,A.Id DESC",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Id=$checkRow["Id"];
		$ItemName=$checkRow["ItemName"];
		$Content=$checkRow["Content"];
		$Symbol=$checkRow["Symbol"];
		$Operator=$checkRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$checkRow["Date"];
		$Bill=$checkRow["Bill"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$Amount=number_format($Amount);
		$AmountRMB=number_format($AmountRMB);

		//add by cabbage 20141210 app用，附件路徑
		$appFileLink = "";

		 $Dir=anmaIn("../download/cwadminicost/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="H".$Id.".jpg";

			//add by cabbage 20141210 app用，附件路徑
			$appFileLink = "/download/cwadminicost/".$Bill;

			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			 }
		 else{$Bill="&nbsp;";}

        $OtherId=$checkRow["OtherId"];
        $OtherEstate=$checkRow["OtherEstate"];
        $getmoneyNO=$checkRow["getmoneyNO"];
        if($OtherId!=0){
                if($OtherEstate==0)$FontColor="style='color:#FF6633'";
                else if($OtherEstate==3) $FontColor="style='color:#0000CC'";
	            $d1=anmaIn("../download/otherin/",$SinkOrder,$motherSTR);
		        $f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
		        $OtherEstate="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\" $FontColor>$getmoneyNO</a>";
              }
        $InvoiceNO=$checkRow["InvoiceNO"];
        $cwSign=$checkRow["cwSign"];
        $InvoiceFile=$checkRow["InvoiceFile"];
         if($cwSign!=""){
				$d2=anmaIn("../download/invoice/",$SinkOrder,$motherSTR);
		         $f2=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		         $OtherEstate=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
             }

		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$ItemName</td>
			<td class='A0101'>$Content&nbsp;</td>
			<td width='80' class='A0101' align='center'>$Operator</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='70' class='A0101'  align='center'  bgcolor='#ECEAED'>$Bill</td>
			<td width='70' class='A0101'  align='center'  bgcolor='#ECEAED'>$OtherEstate</td>
			<td width='60' class='A0101' align='right'>$Symbol</td>
			<td width='90' class='A0101' align='right'>$Amount</td>
			<td width='90' class='A0100' align='right'>$AmountRMB</td>
			</tr>";

		//add by cabbage 20141210 app採集單月紀錄
		$detailList[$i - 1] = array(
			"Date" => $Date,
			"AmountRMB" => $AmountRMB,
			"ItemName" => $ItemName,
			"Content" => $Content,
			"Operator" => $Operator
		);
		if (strlen($appFileLink) > 0) {
			$detailList[$i - 1]["FileLink"] = $appFileLink;
		}

		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='90' class='A0101'>&nbsp;</td>
	<td width='90' class='A0100'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>

<?php
//中港运费、入仓费
$MonthSTR=$Month==""?"":" AND  DATE_FORMAT(B.Date,'%Y-%m')='$Month'";
$MonthSTR2=$Month==""?"":" AND  DATE_FORMAT(B.DeliveryDate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo="代付中港运费、入仓费";
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
$checkSql=mysql_query("
	SELECT Date,Forshort,Termini,ExpressNO,InvoiceNO,InvoiceFile,Estate,BoxQty,mcWG,forwardWG,Price,Amount ,depotCharge,AmountRMB  FROM(
	SELECT B.Date,D.Forshort, A.Termini,A.ExpressNO,B.InvoiceNO,B.InvoiceFile,A.Estate,A.BoxQty,A.mcWG,C.forwardWG,A.Price,(A.mcWG*A.Price) AS Amount ,A.depotCharge,(A.mcWG*A.Price+A.depotCharge*$HKD_Rate) AS AmountRMB 
	FROM $DataIn.ch4_freight_declaration A 
	LEFT JOIN $DataIn.ch1_shipmain B ON B.Id=A.chId 
	LEFT JOIN $DataIn.ch3_forward C ON C.chId=A.chId AND C.PayType=1
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=A.CompanyId 
	WHERE 1 AND B.Date>='2008-07-01' $MonthSTR $EstateSTR AND A.PayType=1 AND A.TypeId='1'
	UNION ALL
	SELECT B.DeliveryDate AS Date,D.Forshort, A.Termini,A.ExpressNO,B.DeliveryNumber AS InvoiceNO,B.DeliveryNumber  AS InvoiceFile,A.Estate,A.BoxQty,A.mcWG,C.forwardWG,A.Price,(A.mcWG*A.Price) AS Amount ,A.depotCharge,(A.mcWG*A.Price+A.depotCharge*$HKD_Rate) AS AmountRMB 
	FROM $DataIn.ch4_freight_declaration A 
	LEFT JOIN $DataIn.ch1_deliverymain B ON B.Id=A.chId 
	LEFT JOIN $DataIn.ch3_forward C ON C.chId=A.chId  AND C.PayType=1
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=A.CompanyId 
	WHERE 1 AND B.DeliveryDate>='2008-07-01' $MonthSTR2 $EstateSTR AND A.PayType=1 AND A.TypeId='2') Z ORDER BY Date DESC
	",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
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
<?php
//货代杂费
$ShowInfo="代付Forward杂费";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='80' class='A1001'>出货日期</td>
<td width='80' class='A1001'>Forward公司</td>
<td width='90' class='A1001'>入仓号</td>
<td width='90' class='A1001'>Forward Invoice</td>
<td width='120' class='A1001'>研砼Invoice</td>
<td width='40' class='A1001'>状态</td>
<td width='40' class='A1001'>件数</td>
<td width='60' class='A1001'>公司称重</td>
<td width='60' class='A1001'>上海称重</td>
<td width='80' class='A1001'>发票日期</td>
<td class='A1001'>备注</td>
<td width='80' class='A1001'>金额(HKD)</td>
<td width='80' class='A1001'>金额(RMB)</td>
</tr>
<tr>
<td colspan='14' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
					  SELECT Date,InvoiceNO,InvoiceFile,Forshort,HoldNO,ForwardNO,Estate,BoxQty,mcWG,forwardWG,Amount,AmountRMB,InvoiceDate,Remark,ETD  FROM(
					  SELECT B.Date,B.InvoiceNO,B.InvoiceFile,
					  D.Forshort, 
					  A.HoldNO,A.ForwardNO,A.Estate,A.BoxQty,A.mcWG,A.forwardWG,A.Amount,(A.Amount*$HKD_Rate) AS AmountRMB,A.InvoiceDate,A.Remark,A.ETD 
					  FROM $DataIn.ch3_forward A 
					  LEFT JOIN $DataIn.ch1_shipmain B ON B.Id=A.chId 
					  LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=A.CompanyId WHERE 1 AND B.Date>='2008-07-01' $MonthSTR $EstateSTR AND A.PayType=1  AND A.TypeId='1'
					  UNION ALL
					 SELECT B.DeliveryDate AS Date,B.DeliveryNumber AS InvoiceNO,B.DeliveryNumber  AS InvoiceFile,
					  D.Forshort, 
					  A.HoldNO,A.ForwardNO,A.Estate,A.BoxQty,A.mcWG,A.forwardWG,A.Amount,(A.Amount*$HKD_Rate) AS AmountRMB,A.InvoiceDate,A.Remark,A.ETD 
					  FROM $DataIn.ch3_forward A 
					  LEFT JOIN $DataIn.ch1_deliverymain B ON B.Id=A.chId 
					  LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=A.CompanyId WHERE 1 AND B.DeliveryDate>='2008-07-01' $MonthSTR2 $EstateSTR AND A.PayType=1 AND A.TypeId='2') Z ORDER BY Date DESC
					  ",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$Date=$checkRow["Date"];
		$Forshort=$checkRow["Forshort"];
		$HoldNO=$checkRow["HoldNO"];
		$ForwardNO=$checkRow["ForwardNO"];
		//提单
		$Lading="../download/expressbill/".$ForwardNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ForwardNO.".jpg",$SinkOrder,$motherSTR);
			$ForwardNO="<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ForwardNO</span>";
			}
		$InvoiceNO=$checkRow["InvoiceNO"];
		$InvoiceFile=$checkRow["InvoiceFile"];
		$f=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";

		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
		$BoxQty=$checkRow["BoxQty"];
		$mcWG=$checkRow["mcWG"];
		$forwardWG=$checkRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
		$InvoiceDate=$checkRow["InvoiceDate"];
		$Remark=$checkRow["Remark"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$Amount=number_format($Amount);
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80'  class='A0101' align='center'>$Date</td>
			<td width='80' class='A0101'>$Forshort</td>
			<td width='90' class='A0101'>$HoldNO</td>
			<td width='90' class='A0101' align='center'>$ForwardNO</td>
			<td width='120' class='A0101'>$InvoiceFile</td>
			<td width='40' class='A0101' align='center'>$Estate</td>
			<td width='40' class='A0101' align='right'>$BoxQty</td>
			<td width='60' class='A0101'  align='right'>$mcWG</td>
			<td width='60' class='A0101' align='right'>$forwardWG</td>
			<td width='80' class='A0101' align='center'>$InvoiceDate</td>
			<td class='A0101'>$Remark</td>
			<td width='80' class='A0101' align='right'>$Amount</td>
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
	<td width='90' class='A0101'>&nbsp;</td>
	<td width='90' class='A0101'>&nbsp;</td>
	<td width='120' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
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
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>