<?php
//供应商货款 ewen 2012-12-25
/*
分两部分显示
1、已结付部分：以主结付表的金额来计算
2、未结付部分：以请款明细计算
*/
$MonthSTR=$Month==""?"":" AND A.Month='$Month'";
$PayMonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录";break;
	case "W":$DataTSTR="未结付记录";break;
	case "A":$DataTSTR="全部记录";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==1?" AND B.GysPayMode=1":" AND B.GysPayMode!=1";//GysPayMode=1 现金，其余月结
//add by cabbage 20141124 在iphone上呈現時，是依據company做排序
$OrderInfo = ($device == "iphone" ? " A.CompanyId ASC, A.Month DESC, A.Id" : " A.Month DESC,A.Id");
$EstateSTR = ($device == "iphone" ? "AND A.Estate IN(0, 3)" : "AND A.Estate = 0");
if ($device == "iphone") {
	switch($DataT){
		case "Y":
			$EstateSTR = "AND A.Estate = 0";
			break;
		case "W":
			$EstateSTR = "AND A.Estate = 3";
			break;
	}
	
	//add by cabbage 20141230 為了可以進到app採集紀錄的if-else
	$DataT = "A";
}
//add by cabbage 20141216 app針對供應商做篩選
$CompanyInfo = ((($device == "iphone") && (strlen($companyId) > 0)) ? " AND A.CompanyId = '".$companyId."'" : "");

echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>";
if($DataT!="W"){
echo"
	<tr bgcolor='#FFFFFF'><td class='A1110' height='25'>一、已结付货款 </tr>
	<tr><td height='450px'>";
		//****************************************
		echo"<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td width='50' height='20' class='A1011'>序号</td>
		<td width='80' class='A1001'>请款日期</td>
		<td width='100' class='A1001'>需求流水号</td>
		<td width='100' class='A1001'>供应商</td>
		<td width='200' class='A1001'>配件名称</td>
		<td width='80' class='A1001'>采购数量</td>
		<td width='80' class='A1001'>单价</td>
		<td width='50' class='A1001'>状态</td>
		<td width='50' class='A1001'>货币</td>
		<td width='80' class='A1001'>金额</td>
		<td width='80' class='A1001'>转RMB金额</td>
		</tr>
		<tr>
		<td colspan='11' height='450px'>
		<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
		<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:450px' align='center'>
		";
		
		//modify by cabbage 20141126 加上 CompanyId
		//读取记录
		$checkSql=mysql_query("SELECT 
		A.Month,A.StockId,A.OrderQty,A.Price,(A.AddQty+A.FactualQty)*A.Price AS Amount,(A.AddQty+A.FactualQty)*A.Price*C.Rate AS AmountRMB,A.Estate,A.BuyerId,B.Forshort,C.Symbol,D.StuffCname,A.CompanyId
		FROM $DataIn.cw1_fkoutsheet A
		LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
		WHERE 1 $Parameters $MonthSTR $EstateSTR $CompanyInfo
		ORDER BY $OrderInfo",$link_id);
		
		$i=1;
		$SumAmount=0;
		if($checkRow=mysql_fetch_array($checkSql)){
			$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
			do{
				//add by cabbage 20141126 app採集單月紀錄
				$detailList[] = $checkRow;
			
				$Month=$checkRow["Month"];
				$StockId=$checkRow["StockId"];
				$Forshort=$checkRow["Forshort"];
				$StuffCname=$checkRow["StuffCname"];
				$OrderQty=$checkRow["OrderQty"];
				$Price=$checkRow["Price"];
				$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
				$Symbol=$checkRow["Symbol"];
				$Amount=sprintf("%.2f",$checkRow["Amount"]);
				$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
				$SumAmount+=$AmountRMB;
				$AmountRMB=number_format($AmountRMB);
				echo"
					<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
					<td width='50' height='20' class='A0111' align='center'>$i</td>
					<td width='80' class='A0101'>$Month</td>
					<td width='100' class='A0101'>$StockId</td>
					<td width='100' class='A0101'>$Forshort</td>
					<td width='200' class='A0101'>$StuffCname</td>
					<td width='80' class='A0101' align='right'>$OrderQty</td>
					<td width='80' class='A0101' align='right'>$Price</td>
					<td width='50' class='A0101' align='center'>$Estate</td>
					<td width='50' class='A0101' align='center'>$Symbol</td>		
					<td width='80' class='A0101' align='right'>$Amount</td>
					<td width='80' class='A0101' align='right'>$AmountRMB</td>
					</tr>";
				$i++;
				}while($checkRow=mysql_fetch_array($checkSql));
			}
		for($j=$i;$j<27;$j++){//补空行
			echo"
			<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111'>$j</td>
			<td width='80'  class='A0101'>&nbsp;</td>
			<td width='100'  class='A0101'>&nbsp;</td>
			<td width='100'  class='A0101'>&nbsp;</td>
			<td width='200' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='50' class='A0101'>&nbsp;</td>
			<td width='50' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='80' class='A0100'>&nbsp;</td>
			</tr>";
			}
					//订金计算
		$checkSql=mysql_fetch_array(mysql_query("SELECT SUM(A.Amount*C.Rate) AS DjAmount FROM $DataIn.cw2_fkdjsheet A LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency WHERE 1 AND A.Date>='2008-07-01' AND A.Estate=0  $Parameters $PayMonthSTR ORDER BY A.Date DESC,A.Id DESC",$link_id));
		$DjAmount=$checkSql["DjAmount"];
		$PayAmount=$SumAmount-$DjAmount;
				
		//add by cabbage 20141215 app紀錄已付、未付金額
		$appSumY = $PayAmount;
		
		$DjAmount=number_format(sprintf("%.0f",$DjAmount));
		$PayAmount=number_format(sprintf("%.0f",$PayAmount));
		$SumAmount=number_format(sprintf("%.0f",$SumAmount));
		echo"</table>
		</div>
		</td>
		</tr>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td height='20' class='A0111' colspan='5'>合计(已付货款总额：¥$SumAmount － 预付订金：¥$DjAmount)</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0101' align='right'>¥$PayAmount</td>
		</tr>
		</tr>
		</table>";
		//****************************************
	echo"</td></tr>";
}
if($DataT!="Y"){
//显示未出
	$ItemStr=$DataT=="A"?"二":"一";
echo"
<tr bgcolor='#FFFFFF'><td class='A0110' height='25'>$ItemStr 、未结付货款</td></tr>
<tr><td height='500px'>
		<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td width='50' height='20' class='A1011'>序号</td>
		<td width='80' class='A1001'>请款日期</td>
		<td width='100' class='A1001'>需求流水号</td>
		<td width='100' class='A1001'>供应商</td>
		<td width='200' class='A1001'>配件名称</td>
		<td width='80' class='A1001'>采购数量</td>
		<td width='80' class='A1001'>单价</td>
		<td width='50' class='A1001'>状态</td>
		<td width='50' class='A1001'>货币</td>
		<td width='80' class='A1001'>金额</td>
		<td width='80' class='A1001'>转RMB金额</td>
		</tr>
		<tr>
		<td colspan='11' height='450px'>
		<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
		<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:450px' align='center'>
		";
		//读取记录
		$checkSql=mysql_query("SELECT 
		A.Month,A.StockId,A.OrderQty,A.Price,(A.AddQty+A.FactualQty)*A.Price AS Amount,(A.AddQty+A.FactualQty)*A.Price*C.Rate  AS AmountRMB,A.Estate,A.BuyerId,B.Forshort,C.Symbol,D.StuffCname
		FROM $DataIn.cw1_fkoutsheet A
		LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
		WHERE 1 $Parameters $MonthSTR AND A.Estate=3
		ORDER BY A.Month DESC,A.Id",$link_id);
		
		$i=1;
		$SumAmount=0;
		if($checkRow=mysql_fetch_array($checkSql)){
			$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
			do{
				$Month=$checkRow["Month"];
				$StockId=$checkRow["StockId"];
				$Forshort=$checkRow["Forshort"];
				$StuffCname=$checkRow["StuffCname"];
				$OrderQty=$checkRow["OrderQty"];
				$Price=$checkRow["Price"];
				$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
				$Symbol=$checkRow["Symbol"];
				$Amount=sprintf("%.2f",$checkRow["Amount"]);
				$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
				$SumAmount+=$AmountRMB;
				$AmountRMB=number_format($AmountRMB);
				echo"
					<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
					<td width='50' height='20' class='A0111' align='center'>$i</td>
					<td width='80' class='A0101'>$Month</td>
					<td width='100' class='A0101'>$StockId</td>
					<td width='100' class='A0101'>$Forshort</td>
					<td width='200' class='A0101'>$StuffCname</td>
					<td width='80' class='A0101' align='right'>$OrderQty</td>
					<td width='80' class='A0101' align='right'>$Price</td>
					<td width='50' class='A0101' align='center'>$Estate</td>
					<td width='50' class='A0101' align='center'>$Symbol</td>		
					<td width='80' class='A0101' align='right'>$Amount</td>
					<td width='80' class='A0101' align='right'>$AmountRMB</td>
					</tr>";
				$i++;
				}while($checkRow=mysql_fetch_array($checkSql));
			}
		for($j=$i;$j<27;$j++){//补空行
			echo"
			<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111'>$j</td>
			<td width='80'  class='A0101'>&nbsp;</td>
			<td width='100'  class='A0101'>&nbsp;</td>
			<td width='100'  class='A0101'>&nbsp;</td>
			<td width='200' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='50' class='A0101'>&nbsp;</td>
			<td width='50' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='80' class='A0100'>&nbsp;</td>
			</tr>";
			}
		
		//订金计算
		$checkSql=mysql_fetch_array(mysql_query("SELECT SUM(A.Amount*C.Rate) AS DjAmount FROM $DataIn.cw2_fkdjsheet A LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency WHERE 1 AND A.Date>='2008-07-01' AND A.Estate=3  $Parameters $PayMonthSTR ORDER BY A.Date DESC,A.Id DESC",$link_id));
		$DjAmount=$checkSql["DjAmount"];
		$PayAmount=$SumAmount-$DjAmount;
				
		//add by cabbage 20141215 app紀錄已付、未付金額
		$appSumW = $PayAmount;
		
		$DjAmount=number_format(sprintf("%.0f",$DjAmount));
		$PayAmount=number_format(sprintf("%.0f",$PayAmount));
		$SumAmount=number_format(sprintf("%.0f",$SumAmount));
		echo"</table>
		</div>
		</td>
		</tr>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td height='20' class='A0111' colspan='5'>合计(未付货款总额：¥$SumAmount － 预付订金：¥$DjAmount)</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0101' align='right'>¥$PayAmount</td>
		</tr>
		</table></td></tr>";
		}
echo"</table>";
?>