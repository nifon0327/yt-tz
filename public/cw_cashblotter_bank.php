<?php 
//电信-zxq 2012-08-01
if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 9.0"))
		$browser = "Internet Explorer 9.0";   
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 8.0"))
		$browser = "Internet Explorer 8.0";   
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 7.0"))      
		$browser = "Internet Explorer 7.0";      
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.0"))      
		$browser = "Internet Explorer 6.0";
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox/5"))      
		$browser = "Firefox 5";  
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox/4"))      
		$browser = "Firefox 4";  
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox/3"))      
		$browser = "Firefox 3";      
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox/2"))      
		$browser = "Firefox 2";
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Chrome"))
		$browser = "Google Chrome";      
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Safari"))      
		$browser = "Safari";      
	else if(strpos($_SERVER["HTTP_USER_AGENT"],"Opera"))      
		$browser = "Opera";      
	else 	$browser =  $_SERVER["HTTP_USER_AGENT"]; 
$i=1;
$TmpWidth=0;
//if($browser=="MSIE 6.0")
	$TmpWidth=4;
	
$w1=40-$TmpWidth;$w2=80-$TmpWidth;$w3=90-$TmpWidth;
$w4=250-$TmpWidth;$w5=100-$TmpWidth;$w6-$TmpWidth=160;
$j=($Page-1)*$Page_Size+1;
//List_Title($Th_Col,"1",0);
echo "<table width='1040' bgcolor='#9BCFE3' id='DataList' border='0' cellspacing='0'>";
echo "<tr align='center'>";
echo "<td width='$w1' class='A1111' style='height:25px;' rowspan='2'>序号</td>";
echo "<td width='$w2' class='A1101' rowspan='2'>日期</td>";
echo "<td width='$w3' class='A1101' rowspan='2'>费用类别</td>";
echo "<td width='$w4' class='A1101' rowspan='2'>备注</td>";
echo "<td width='$w5' class='A1101' rowspan='2'>结付银行</td>";

echo "<td width='$w6' class='A1101' colspan='2'>RMB</td>";
echo "<td width='$w6' class='A1101' colspan='2'>USD</td>";
echo "<td width='' class='A1101' colspan='2'>HKD</td>";
echo "</tr>";

echo "<tr align='center'>";
echo "<td width='$w2' class='A0101'>进账金额</td>";
echo "<td width='$w2' class='A0101'>出账金额</td>";
echo "<td width='$w2' class='A0101'>进账金额</td>";
echo "<td width='$w2' class='A0101'>出账金额</td>";
echo "<td width='$w2' class='A0101'>进账金额</td>";
echo "<td width='$w2' class='A0101'>出账金额</td>";
echo "</tr>";
echo "</table>";
/*
 1 供应商货款	多货币
 2 预付订金		多付币
 3 行政费用		多货币
 4 开发费用		多货币
 5 员工薪资		RMB
 6 员工借支		RMB
 7 社保缴费		RMB
 8 假日加班费	RMB
 
 9 FORWARD杂费	HKD
10 中港运费		RMB
11 快递费		RMB
12 其它支出		多货币
*/

//选定月份前的结余,条件 
$PayDateSTR=" and M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE'";
//选定月份的项目,条件 
$PayDateSTRNow=" and DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth'";

//初始化结余
//当月之前结余
		$mySqlP.="SELECT SUM(S.Amount) AS Amount,'-1' AS Sign,S.Currency,M.BankId FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign,S.Currency,M.BankId FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id  WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cwxzmain M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cwxztempmain M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cwygjz M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.sbpaymain M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.hdjbmain M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'1' AS Sign,M.Currency,M.BankId FROM $DataIn.cw4_otherin M WHERE 1 AND M.Estate=0 $PayDateSTR";
		//$mySqlP.="UNION ALL SELECT M.Amount AS Amount,'-1' AS Sign FROM cw4_otherout M WHERE 1 and M.Currency='1' $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.OutAmount AS Amount,'-1' AS Sign,M.OutCurrency AS Currency,M.OutBankId AS BankId FROM $DataIn.cw5_fbdh M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.InAmount AS Amount,'1' AS Sign,M.InCurrency AS Currency,M.InBankId AS BankId FROM $DataIn.cw5_fbdh M WHERE 1 $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,P.Currency,M.BankId FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P WHERE S.Mid=M.Id AND P.CompanyId=S.CompanyId $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.PayAmount-M.Handingfee AS Amount,'1' AS Sign,C.Currency,M.BankId FROM $DataIn.cw6_orderinmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,C.Currency,M.BankId FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId $PayDateSTR";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,C.Currency,M.BankId FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId $PayDateSTR GROUP BY M.Id";
		//$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw3_forward M,$DataIn.ch3_forward S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId AND C.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'1' AS Sign,D.Currency,M.BankId FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D WHERE M.CompanyId=D.CompanyId $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cw11_jjmain M WHERE 1 $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.zw3_purchasem M WHERE 1 $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT M.declarationCharge+M.checkCharge AS Amount,'-1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cw4_freight_declaration M WHERE 1 $PayDateSTR GROUP BY M.Id";			//报关费用 add by zx 20101229
		$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign,S.Currency,M.BankId FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id	WHERE 1 $PayDateSTR GROUP BY M.Id";
		$mySqlP.=" UNION ALL SELECT SUM(M.Taxamount)  AS Amount,'1' AS Sign,'1' AS Currency,M.BankId FROM $DataIn.cw14_mdtaxmain M WHERE 1 and M.Taxdate>='$PreMonthS' AND M.Taxdate<'$PreMonthE' and M.Estate='0'";			//退税
		$mySqlP.=" UNION ALL SELECT SUM(T.Amount) AS Amount,'1' AS Sign,P.Currency,M.BankId
		       FROM $DataIn.cw15_gyskksheet T
			   LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id 
			   LEFT JOIN $DataIn.cw15_gyskkmain G ON G.Id=T.Mid
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND T.Kid!=0
			   GROUP BY T.Kid";//供应商扣款

		$mySqlP.=" UNION ALL SELECT SUM(T.Amount) AS Amount,'1' AS Sign,P.Currency,M.BankId
		       FROM $DataIn.cw2_hksheet  T
			   LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
			   LEFT JOIN $DataIn.cw2_hkmain  G ON G.Id=T.Mid
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND T.Did!=0
			   GROUP BY  T.Did";//供应商货款返利
		
		//当月记录
		$mySqlN.="SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,concat('行政费用') AS Item,'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id WHERE 1 $PayDateSTRNow $BankStr GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,concat('开发费用') AS Item,'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id WHERE 1 $PayDateSTRNow $BankStr GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('员工薪资') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cwxzmain M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('临时工薪资') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cwxztempmain M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('员工借支') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cwygjz M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('社保缴费') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.sbpaymain M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('加班费') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.hdjbmain M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('其它收入') AS Item,'I' AS Sign,M.Remark AS Remark,M.Currency FROM $DataIn.cw4_otherin M WHERE 1 AND M.Estate=0 $PayDateSTRNow $BankStr";
		//$mySqlN.="UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('其它支出') AS Item,'O' AS Sign,M.Remark AS Remark FROM cw4_otherout M WHERE 1 and M.Currency='1' $PayDateSTRNow";
		$mySqlN.=" UNION ALL SELECT M.OutAmount AS Amount,M.PayDate AS PayDate,concat('转出') AS Item,'O' AS Sign,M.Remark AS Remark,M.OutCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 $PayDateSTRNow $OutBankStr";
		$mySqlN.=" UNION ALL SELECT M.InAmount AS Amount,M.PayDate AS PayDate,concat('转入') AS Item,'I' AS Sign,M.Remark AS Remark,M.InCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 $PayDateSTRNow $InBankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('预付订金') AS Item, 'O' AS Sign,M.Remark AS Remark,P.Currency FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P WHERE S.Mid=M.Id AND P.CompanyId=S.CompanyId $PayDateSTRNow $BankStr GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.PayAmount-M.Handingfee AS Amount,M.PayDate AS PayDate,concat('客户货款收入'),'I' AS Sign,M.Remark,C.Currency FROM $DataIn.cw6_orderinmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('供应商货款支付'),'O' AS Sign,M.Remark,C.Currency FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('中港运费'),'O' AS Sign,M.Remark,C.Currency FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId  $PayDateSTRNow $BankStr GROUP BY M.Id";
		//$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('Forward杂费'),'O' AS Sign,M.Remark FROM $DataIn.cw3_forward M,$DataIn.ch3_forward S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId AND C.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('快递费'),'O' AS Sign,M.Remark,'1' AS Currency FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $PayDateSTRNow $BankStr GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('寄样费'),'O' AS Sign,M.Remark,'1' AS Currency FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $PayDateSTRNow $BankStr GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('预收客户货款'),'I' AS Sign,M.Remark,D.Currency FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D WHERE M.CompanyId=D.CompanyId $PayDateSTRNow $BankStr GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('节日奖金') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw11_jjmain M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('总务采购费用') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.zw3_purchasem M WHERE 1 $PayDateSTRNow $BankStr";
		$mySqlN.=" UNION ALL SELECT M.declarationCharge +M.checkCharge  AS Amount,M.PayDate AS PayDate,concat('报关费用') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw4_freight_declaration  M WHERE 1 $PayDateSTRNow $BankStr";			//报关费用 add by zx 20101229
		$mySqlN.=" UNION ALL SELECT ifnull(M.Taxamount,0) AS Amount,M.Taxdate AS PayDate,concat('退税金额') AS Item,'I' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw14_mdtaxmain M WHERE 1 and DATE_FORMAT(M.Taxdate,'%Y-%m')='$ChooseMonth' and M.Estate='0' $BankStr";			//退税

		$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,concat('供应商税款') AS Item,'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id WHERE 1 $PayDateSTRNow $BankStr GROUP BY M.Id";
		
		$mySqlN.=" UNION ALL SELECT SUM(T.Amount) AS Amount,M.Date AS PayDate,concat('供应商扣款') AS Item,'I' AS Sign,M.Remark,P.Currency
		       FROM $DataIn.cw15_gyskksheet T
			   LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id 
			   LEFT JOIN $DataIn.cw15_gyskkmain G ON G.Id=T.Mid
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND T.Kid!=0 $BankStr
			   GROUP BY T.Kid";//供应商扣款

		$mySqlN.=" UNION ALL SELECT SUM(T.Amount) AS Amount,M.Date AS PayDate,concat('货款返利') AS Item,'I' AS Sign,M.Remark,P.Currency
		       FROM $DataIn.cw2_hksheet T
			   LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
			   LEFT JOIN $DataIn.cw2_hkmain G ON G.Id=T.Mid
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND T.Did!=0 $BankStr
			   GROUP BY T.Did";//供应商扣款

$mySqlPRMB="SELECT TRUNCATE(SUM(Amount*Sign),2) AS SUM_RMB FROM ( ".$mySqlP.") A WHERE Currency='1' $BankStr";
$mySqlPUSD="SELECT TRUNCATE(SUM(Amount*Sign),2) AS SUM_USD FROM ( ".$mySqlP.") A WHERE Currency='2' $BankStr";
$mySqlPHKD="SELECT TRUNCATE(SUM(Amount*Sign),2) AS SUM_HKD FROM ( ".$mySqlP.") A WHERE Currency='3' $BankStr";

$PreAmountRMB="&nbsp;";
$PreAmountUSD="&nbsp;";
$PreAmountHKD="&nbsp;";
$PreSqlRMB=mysql_fetch_array(mysql_query($mySqlPRMB,$link_id));
$PreAmountRMB=$PreSqlRMB["SUM_RMB"];
if($PreAmountRMB=="")
	$PreAmountRMB="&nbsp;";
$PreSqlUSD=mysql_fetch_array(mysql_query($mySqlPUSD,$link_id));
$PreAmountUSD=$PreSqlUSD["SUM_USD"];
if($PreAmountUSD=="")
	$PreAmountUSD="&nbsp;";
$PreSqlHKD=mysql_fetch_array(mysql_query($mySqlPHKD,$link_id));
$PreAmountHKD=$PreSqlHKD["SUM_HKD"];
if($PreAmountHKD=="")
	$PreAmountHKD="&nbsp;";
echo "<table width='1040' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' height='25'>
<tr bgcolor='$theDefaultColor' 
onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
<td class='A0111' width='40'>&nbsp;</td>
<td class='A0101' width='80'>&nbsp;</td>
<td class='A0101' width='90'>&nbsp;</td>
<td class='A0101' width='250' align='center'>上次结余</td>
<td class='A0101' width='100'>&nbsp;</td>
<td class='A0101' width='80' align='center'>$PreAmountRMB</td>
<td class='A0101' width='80' align='center'>&nbsp;</td>
<td class='A0101' width='80' align='center'>$PreAmountUSD</td>
<td class='A0101' width='80' align='center'>&nbsp;</td>
<td class='A0101' width='80' align='center'>$PreAmountHKD</td>
<td class='A0101' width='80' align='center'>&nbsp;</td>
</tr></table>";
/*
支出
	 1 供应商货款	多货币
	 2 预付订金		多付币
	 3 行政费用		多货币
	 4 开发费用		RMB
	 5 员工薪资		RMB
	 6 员工借支		RMB
	 7 社保缴费		RMB
	 8 假日加班费	RMB
	 
	 9 FORWARD杂费	RMB
	10 中港运费		RMB
	11 快递费		RMB
	12 其它支出		多货币
	13 货币转出		多货币
收入
	1 帐户转入		多货币
	2 货款进帐		多货币
	3 其它进帐		多货币
	
*/
$SumInRMB=0;
$SumOutRMB=0;
$SumInUSD=0;
$SumOutUSD=0;
$SumInHKD=0;
$SumOutHKD=0;
$ChooseOut="N";
$mySqlN.=" ORDER BY PayDate,Sign";
//echo $mySqlN;
$myResult = mysql_query($mySqlN,$link_id);
$i=2;
$num=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$PayDate=$myRow["PayDate"];
		$Item=$myRow["Item"];		
		$Remark=$myRow["Remark"];
		$Sign=$myRow["Sign"];
		$Currency=$myRow["Currency"];
		$bankResult=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Estate=1 AND Id<6 AND Id='$BankFromName' ORDER BY Id",$link_id);
		if($bankRow=mysql_fetch_array($bankResult))
		{
			$Title=$bankRow["Title"];
		}
		switch($Currency)
		{
			case 1:			//RMB
			switch($Sign){
				case "I":
					$outAmount="&nbsp;";
					$inAmount=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$inAmount);
					$SumInRMB+=$inAmount;
					break;
				case "O":				
					$outAmount=$Amount;
					$inAmount="&nbsp;";
					$PreAmount=sprintf("%.2f",$PreAmount-$outAmount);
					$SumOutRMB+=$outAmount;
					break;
			}
			echo "<table width='1040' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' height='25'>
				<tr bgcolor='$theDefaultColor' 
onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			//echo "<tr bgcolor='#FFFFFF' align='center'>";
			echo "<td width='40' class='A0111'>$num</td>";
			echo "<td width='80' class='A0101'>$PayDate</td>";
			echo "<td width='90' class='A0101'>$Item</td>";
			echo "<td width='250' class='A0101' align='left'><DIV title='$Remark'>$Remark</DIV></td>";
			echo "<td width='100' class='A0101'>$Title</td>";
			echo "<td width='80' class='A0101'>$inAmount</td>";
			echo "<td width='80' class='A0101'>$outAmount</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='' class='A0101'>&nbsp;</td>";
			echo "</tr>";
			echo "</table>";
			break;
			
			case 2:			//USD
			switch($Sign){
				case "I":
					$outAmount="&nbsp;";
					$inAmount=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$inAmount);
					$SumInUSD+=$inAmount;
					break;
				case "O":				
					$outAmount=$Amount;
					$inAmount="&nbsp;";
					$PreAmount=sprintf("%.2f",$PreAmount-$outAmount);
					$SumOutUSD+=$outAmount;
					break;
			}
			echo "<table width='1040' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' height='25'>
				<tr bgcolor='$theDefaultColor' 
				onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 			
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			//echo "<tr bgcolor='#FFFFFF' align='center'>";
			echo "<td width='40' class='A0111'>$num</td>";
			echo "<td width='80' class='A0101'>$PayDate</td>";
			echo "<td width='90' class='A0101'>$Item</td>";
			echo "<td width='250' class='A0101' align='left'><DIV title='$Remark'>$Remark</DIV></td>";
			echo "<td width='100' class='A0101'>$Title</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>$inAmount</td>";
			echo "<td width='80' class='A0101'>$outAmount</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='' class='A0101'>&nbsp;</td>";
			echo "</tr>";
			echo "</table>";
			break;
			
			case 3:			//HKD
			switch($Sign){
				case "I":
					$outAmount="&nbsp;";
					$inAmount=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$inAmount);
					$SumInHKD+=$inAmount;
					break;
				case "O":				
					$outAmount=$Amount;
					$inAmount="&nbsp;";
					$PreAmount=sprintf("%.2f",$PreAmount-$outAmount);
					$SumOutHKD+=$outAmount;
					break;
			}
			echo "<table width='1040' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5' height='25'>
				<tr bgcolor='$theDefaultColor' 
				onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			//echo "<tr bgcolor='#FFFFFF' align='center'>";
			echo "<td width='40' class='A0111'>$num</td>";
			echo "<td width='80' class='A0101'>$PayDate</td>";
			echo "<td width='90' class='A0101'>$Item</td>";
			echo "<td width='250' class='A0101' align='left'><DIV title='$Remark'>$Remark</DIV></td>";
			echo "<td width='100' class='A0101'>$Title</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>&nbsp;</td>";
			echo "<td width='80' class='A0101'>$inAmount</td>";
			echo "<td width='' class='A0101'>$outAmount</td>";
			echo "</tr>";
			echo "</table>";
			break;
		}
		$num++;
		/*$ValueArray=array(
			array(0=>$PayDate,	1=>"align='center'"),
			array(0=>$Item,		1=>"align='center'"),
			array(0=>$Remark,	3=>"..."),
			array(0=>$inAmount,	1=>"align='right'"),
			array(0=>$outAmount,1=>"align='right'"),
			array(0=>$PreAmount,1=>"align='right'")
			);*/
		//$checkidValue=$Id;
		//include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
	else{
	noRowInfo($tableWidth);
  	}
//计算总额
$SunRMB=$SumInRMB-$SumOutRMB+$PreAmountRMB;
$SunUSD=$SumInUSD-$SumOutUSD+$PreAmountUSD;
$SunHKD=$SumInHKD-$SumOutHKD+$PreAmountHKD;
//提取汇率
$rateResult = mysql_query("SELECT Rate,Id FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){do{
	$Id=$rateRow["Id"];$TempHZSTR="Rate_".strval($Id);$$TempHZSTR=$rateRow["Rate"];
	}while($rateRow = mysql_fetch_array($rateResult));}
$AmountTotal=$SunRMB+$SunUSD*$Rate_2+$SunHKD*$Rate_3;
$AmountTotal=number_format($AmountTotal);
//步骤7：
echo '</div>';
//List_Title($Th_Col,"0",0);
echo "<table width='1040' id='DataList' border='0' cellspacing='0' height='25'>";
echo "<tr bgcolor='#9BCFE3' align='center'>";
echo "<td width='556' class='A0111'>小计</td>";

echo "<td width='$w2' class='A0101'>$SumInRMB</td>";
echo "<td width='$w2' class='A0101'>$SumOutRMB</td>";
echo "<td width='$w2' class='A0101'>$SumInUSD</td>";
echo "<td width='$w2' class='A0101'>$SumOutUSD</td>";
echo "<td width='$w2' class='A0101'>$SumInHKD</td>";
echo "<td width='' class='A0101'>$SumOutHKD</td>";
echo "</tr>";
echo "</table>";

echo "<table width='1040' id='DataList' border='0' cellspacing='0' height='25'>";
echo "<tr bgcolor='#9BCFE3' align='center'>";
echo "<td width='556' class='A0111'>结余</td>";

echo "<td width='160' class='A0101'>$SunRMB</td>";
echo "<td width='160' class='A0101'>$SunUSD</td>";
echo "<td width='' class='A0101'>$SunHKD</td>";
echo "</tr>";
echo "</table>";

echo "<table width='1040' id='DataList' border='0' cellspacing='0' height='25'>";
echo "<tr bgcolor='#9BCFE3' align='center'>";
echo "<td width='556' class='A0111'>合计：转RMB总额约：</td>";
echo "<td width='' class='A0101'>$AmountTotal 元</td>";
echo "</tr>";
echo "</table>";
pBottom($i-2,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>