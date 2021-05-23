<?php
//货款(invoice) ewen 2012-11-21
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
$TempPayDatetj=$Month==""?"":" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$Month'";

//订金
$checkSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*.C.Rate),0) AS Amount 
														   FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D,$DataPublic.currencydata C 
														   WHERE M.CompanyId=D.CompanyId AND D.Currency=C.Id  AND M.Mid>0 AND M.PayDate>'2008-07-01' $TempPayDatetj",$link_id));
$DJ_YD=sprintf("%.0f",$checkSql["Amount"]);//已抵货款的定金
$checkSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*.C.Rate),0) AS Amount 
														   FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D,$DataPublic.currencydata C 
														   WHERE M.CompanyId=D.CompanyId AND D.Currency=C.Id AND M.PayDate>'2008-07-01' $TempPayDatetj",$link_id));
$DJ_All=sprintf("%.0f",$checkSql["Amount"]);//预收定金总额
$DJ_WD=$DJ_All-$DJ_YD;						//未抵定金＝已收定金－已抵货款定金

switch($DataT){
	case "Y":
		$DataTSTR="已结付记录"; $EstateSTR=" AND (A.cwSign=0 OR A.cwSign=2)"; 
		//要扣除已抵付的
		$ValueAmount=$DJ_YD;
		break;
	case "W":
		$DataTSTR="未结付记录"; $EstateSTR=" AND (A.cwSign=1 OR A.cwSign=2)";
		$ValueAmount=$DJ_WD;
		break;
	case "A":
		$DataTSTR="全部记录"; $EstateSTR="";
		$ValueAmount=$DJ_All;
		break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：$Parameters ".$Remark;
$ParametersSTR=$Parameters==""?"AND A.ShipType!='debit'":" AND A.ShipType='debit'";
//add by cabbage 20141124 在iphone上呈現時，是依據company做排序
$OrderInfo = ($device == "iphone" ? " ORDER BY CompanyId ASC, A.Date DESC" : " ORDER BY A.Date DESC");
//add by cabbage 20141216 app針對供應商做篩選
$CompanyInfo = ((($device == "iphone") && (strlen($companyId) > 0)) ? " AND A.CompanyId = '".$companyId."'" : "");

echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='80' class='A1001'>出货日期</td>
<td width='100' class='A1001'>客户</td>
<td width='200' class='A1001'>Invoice</td>
<td class='A1001'>备注</td>
<td width='50' class='A1001'>状态</td>
<td width='60' class='A1001'>货币</td>
<td width='80' class='A1001'>金额</td>
<td width='80' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='9' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//modify by cabbage 20141119 
//1. 多傳Esate和CompanyId欄位
//2. 原本只撈出「已結付」資料，加上顯示「審核過未結付」的資料 >> A.Estate=0 >> A.Estate IN (0, 3)
//读取记录：正常出货或其他收款项目
//modify by cabbage 20141216 加上 CompanyId的篩選
$checkSql=mysql_query("SELECT A.Id,A.Date,C.Forshort,A.InvoiceNO,A.InvoiceFile,A.Remark,A.cwSign,D.Symbol,SUM(B.Qty*B.Price) AS Amount,SUM(B.Qty*B.Price*D.Rate) AS AmountRMB, A.Estate, A.CompanyId 
FROM $DataIn.ch1_shipmain A
LEFT JOIN $DataIn.ch1_shipsheet B ON B.Mid=A.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
WHERE A.Estate IN (0, 3) AND A.Sign=1 $ParametersSTR $MonthSTR $EstateSTR $CompanyInfo
GROUP BY A.Id $OrderInfo",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141120 app採集單月紀錄
		$detailList[] = $checkRow;	
		
		$fileLink = "/download/invoice/".$checkRow["InvoiceNO"].".pdf";
		$linkList[$checkRow["Id"]] = $fileLink;
		
		$Id=$checkRow["Id"];
		$Date=$checkRow["Date"];
		$Forshort=$checkRow["Forshort"];
		$InvoiceNO=$checkRow["InvoiceNO"];
		$InvoiceFile=$checkRow["InvoiceFile"];
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
		$Remark=$checkRow["Remark"];
		$cwSign=$checkRow["cwSign"];
		$Symbol=$checkRow["Symbol"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		switch($cwSign){
			case 1:
				$cwSign="<span class=\"redB\">未付</span>";
				break;
			case 2://部分结付
				$cwSign="<span class=\"blueB\">部分</span>";
				if($DataT!="A"){

					$checkPaySql=mysql_fetch_array(mysql_query("SELECT 
						IFNULL(SUM(S.Amount),0) AS Amount,IFNULL(SUM(S.Amount*C.Rate),0) AS AmountRMB
						FROM $DataIn.cw6_orderinsheet S 
						LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId 
						LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId  
						LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
						WHERE M.Id='$Id'",$link_id));
					$AmountY=$checkPaySql["Amount"];
					$AmountRMBY=$checkPaySql["AmountRMB"];
					if($DataT=="Y"){
						$Amount=$AmountY;
						$AmountRMB=$AmountRMBY;
						}
					else{
						$Amount-=$AmountY;
						$AmountRMB-=$AmountRMBY;
						}
				}
				break;
			default:
				$cwSign="<span class=\"greenB\">已付</span>";
				break;
			}
			
		//add by cabbage 20141216 app紀錄當月的已付/未付金額
		$appSumA += $checkRow["AmountRMB"];
		if ($checkRow["cwSign"] != 0) {
			$appSumW += $AmountRMB;
		}
			
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80' class='A0101'>$Date</td>
			<td width='100' class='A0101'>$Forshort</td>
			<td width='200' class='A0101' bgcolor='#ECEAED'>$InvoiceFile</td>
			<td class='A0101'>$Remark</td>
			<td width='50' class='A0101' align='center' bgcolor='#ECEAED'>$cwSign</td>
			<td width='60' class='A0101' align='center'>$Symbol</td>		
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
	<td width='100'  class='A0101'>&nbsp;</td>
	<td width='200' class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='50' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0100'>&nbsp;</td>
	</tr>";
	}
$LastSumAmount=number_format(sprintf("%.0f",$SumAmount)-$ValueAmount);
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
</tr>";
if($Parameters==""){
	echo"
	</tr>
	<tr bgcolor='#36C' align='center' style='Color:#FFF'>
	<td height='20' class='A0111' colspan='2'>预收款</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0101' align='right'>- ¥$ValueAmount</td>
	</tr>
	</tr>
	<tr bgcolor='#36C' align='center' style='Color:#FFF'>
	<td height='20' class='A0111' colspan='2'>实际</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0100'>&nbsp;</td>
	<td  class='A0101' align='right'>¥$LastSumAmount</td>
	</tr>";
	}
echo"
</table>";
?>