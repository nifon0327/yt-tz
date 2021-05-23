<?php 
//ewen 2013-01-14 加入体检费用
$i=1;
$TmpWidth=0;
$TmpWidth=4;
$j=($Page-1)*$Page_Size+1;
echo "<table width='$tableWidth' id='DataList' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
echo "<tr align='center' bgcolor='#CCCCCC'>";
echo "<td width='40' class='A1111' style='height:25px;' rowspan='2'>序号</td>";
echo "<td width='80' class='A1101' rowspan='2'>日期$BankId</td>";
echo "<td width='90' class='A1101' rowspan='2'>费用类别</td>";
echo "<td width='250' class='A1101' rowspan='2'>备注</td>";
echo "<td width='200' class='A1101' colspan='2'>RMB</td>";
echo "<td width='200' class='A1101' colspan='2'>USD</td>";
echo "<td width='200' class='A1101' colspan='2'>HKD</td>";
echo "<td width='200' class='A1101' colspan='2'>NTD</td>";
echo "</tr>";
echo "<tr align='center' bgcolor='#CCCCCC'>";
echo "<td width='100' class='A0101'>进账金额</td>";
echo "<td width='100' class='A0101'>出账金额</td>";
echo "<td width='100' class='A0101'>进账金额</td>";
echo "<td width='100' class='A0101'>出账金额</td>";
echo "<td width='100' class='A0101'>进账金额</td>";
echo "<td width='100' class='A0101'>出账金额</td>";
echo "<td width='100' class='A0101'>进账金额</td>";
echo "<td width='100' class='A0101'>出账金额</td>";
echo "</tr>";
//以银行分类明细列出：与银行统计不同之处只是条件不同；另外把当前月的数据单独做处理
$PreMonth=" WHERE 1 AND M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE' AND M.BankId='$BankId'  GROUP BY M.Id";//之前
//一、多货币，可使用统一条件的项目
		/*1-BOM采购货款*/	$mySqlP.=" SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw1_fkoutmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId ".$PreMonth;
		/*2-客户回扣*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw1_tkoutmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId ".$PreMonth;
		/*3-BOM采购预付订金*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw2_fkdjmain M LEFT JOIN $DataIn.cw2_fkdjsheet A ON A.Mid=M.Id LEFT JOIN $DataIn.trade_object S ON S.CompanyId=A.CompanyId ".$PreMonth;

		//ewen 2013-03-28
		/*非BOM采购货款支出*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.nonbom11_qkmain M LEFT JOIN $DataPublic.nonbom3_retailermain S ON S.CompanyId=M.CompanyId ".$PreMonth;
		
		/*4-供应商税款*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id ".$PreMonth;
		/*5-客户货款*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount-M.Handingfee) AS Amount,S.Currency FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId ".$PreMonth;
		/*6-预收货款*/			$mySqlP.=" UNION ALL SELECT (M.Amount) AS Amount,S.Currency FROM $DataIn.cw6_advancesreceived M LEFT JOIN $DataIn.trade_object S ON M.CompanyId=S.CompanyId ".$PreMonth;
		/*7-开发费用*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id  ".$PreMonth;
		/*8-行政费用*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id ".$PreMonth;

		/*9-车辆费用*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.carfeemain M LEFT JOIN $DataIn.carfee S ON S.Mid=M.Id ".$PreMonth;

		/*33-员工离职补助*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.staff_outsubsidymain M LEFT JOIN $DataIn.staff_outsubsidysheet S ON S.Mid=M.Id ".$PreMonth;

		/*34-其它奖金*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw20_bonusmain M LEFT JOIN $DataIn.cw20_bonussheet S ON S.Mid=M.Id ".$PreMonth;

//二、多货币，条件需单独处理的项目
		/*9-其它收入*/			$mySqlP.=" UNION ALL SELECT M.PayAmount,S.Currency FROM $DataIn.cw4_otherinmain M  LEFT JOIN $DataIn.cw4_otherinsheet S ON S.Mid=M.Id 
   WHERE 1 AND M.BankId='$BankId'  AND M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE' GROUP BY M.Id";		
		/*10-汇兑转入*/			$mySqlP.=" UNION ALL SELECT (M.InAmount) AS Amount,M.InCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 AND M.InBankId='$BankId'  AND M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE' GROUP BY M.Id";
		/*11-汇兑转出*/			$mySqlP.=" UNION ALL SELECT (M.OutAmount*-1) AS Amount,M.OutCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 AND M.OutBankId='$BankId'  AND M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE' GROUP BY M.Id";
		/*12扣供应商货款*/	$mySqlP.=" UNION ALL SELECT (T.Amount) AS Amount,S.Currency FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND M.BankId='$BankId' AND T.Kid!=0";//扣供应商款
		/*13货款返利*/	$mySqlP.=" UNION ALL SELECT (T.Amount) AS Amount,S.Currency FROM $DataIn.cw2_hksheet T 
                                             LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
                                             LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId 
                                             WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND M.BankId='$BankId' AND T.Did!=0";//货款返利
	
//三、HKD项目，可使用统一条件的项目
		/*13入仓费*/ 			$mySqlP.=" UNION ALL SELECT (M.depotCharge)*-1 AS Amount,'3' AS Currency FROM $DataIn.cw4_freight_declaration M LEFT JOIN $DataIn.ch4_freight_declaration S ON M.Id=S.Mid ".$PreMonth;
		/*14-Forward杂费*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'3' AS Currency FROM $DataIn.cw3_forward M LEFT JOIN $DataIn.ch3_forward S ON M.Id=S.Mid ".$PreMonth;
		
//四、RMB项目，可使用统一条件的项目
		/*5－员工薪资*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cwxzmain M ".$PreMonth;
		///*16－试用工薪资*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cwxztempmain M ".$PreMonth;
		/*17－员工借支*/		$mySqlP.=" UNION ALL SELECT (M.Amount*-1) AS Amount,'1' AS Currency FROM $DataIn.cwygjz M ".$PreMonth;
		/*18－社保缴费*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.sbpaymain M ".$PreMonth;
		/*19－假日加班费*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.hdjbmain M ".$PreMonth;
		/*20－快递费*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw9_expsheet M LEFT JOIN $DataIn.ch9_expsheet S ON M.Id=S.Mid ".$PreMonth;
		/*21－寄样费*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw10_samplemail M LEFT JOIN $DataIn.ch10_samplemail S ON M.Id=S.Mid ".$PreMonth;
		/*22－节日奖金*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw11_jjmain M ".$PreMonth;
		/*23－总务费用*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.zw3_purchasem M ".$PreMonth;
		/*24中港运费/报关/商检费用*/ 
										$mySqlP.=" UNION ALL SELECT (M.PayAmount+M.declarationCharge+M.checkCharge)*-1 AS Amount,'1' AS Currency FROM $DataIn.cw4_freight_declaration M LEFT JOIN $DataIn.ch4_freight_declaration S ON M.Id=S.Mid ".$PreMonth;
		//五、RMB项目，条件需单独处理的项目

		/*25－退税收益*/		//$mySqlP.=" UNION ALL SELECT (M.Taxamount)  AS Amount,'1' AS Currency FROM $DataIn.cw14_mdtaxmain M WHERE 1 AND M.Taxdate>='$PreMonthS' AND M.Taxdate<'$PreMonthE'  AND M.BankId='$BankId'  AND M.Estate='0' GROUP BY M.Id";	

		/*25－退税收益OK, 按结付日期来显示*/	$mySqlP.=" UNION ALL SELECT SUM(M.Taxamount)  AS Amount,'1' AS Currency  FROM $DataIn.cw14_mdtaxmain M WHERE 1 and M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE' AND M.BankId='$BankId'  and M.Estate='0' GROUP BY M.Id";		//M.Taxdate>='$PreMonthS' AND 

		/*模具费退回*/			$mySqlP.=" UNION ALL SELECT (M.OutAmount)  AS Amount,'1' AS Currency FROM $DataIn.cw16_modelfee M WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE'  AND M.BankId='$BankId'  AND M.Estate='0' GROUP BY M.Id";	
		/*体检费用*/				$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw17_tjmain  M ".$PreMonth;
		/*助学费用*/				$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw19_studyfeemain  M ".$PreMonth;
$NowMonth=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth' AND M.BankId='$BankId'";	//当前月,当前选定银行
//一、多货币，可使用统一条件的项目
		/*1-BOM货款支出*/			$mySqlN="SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('BOM采购货款支付') AS Item,'O' AS Sign,M.Remark,S.Currency FROM $DataIn.cw1_fkoutmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId WHERE 1 $NowMonth GROUP BY M.Id";
		/*2-客户回扣*/					$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('客户退款支出') AS Item,'O' AS Sign,M.Remark,S.Currency FROM $DataIn.cw1_tkoutmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId WHERE 1 $NowMonth GROUP BY M.Id";
		/*3-BOM预付订金*/			$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('BOM采购预付订金') AS Item, 'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.cw2_fkdjmain M LEFT JOIN $DataIn.cw2_fkdjsheet A ON A.Mid=M.Id LEFT JOIN $DataIn.trade_object S ON S.CompanyId=A.CompanyId WHERE 1 $NowMonth GROUP BY M.Id";
		
		//ewen 2013-03-28
		/*1-非BOM货款支出*/		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('非BOM货款支付') AS Item,'O' AS Sign,M.Remark,S.Currency FROM $DataIn.nonbom11_qkmain M LEFT JOIN $DataPublic.nonbom3_retailermain S ON S.CompanyId=M.CompanyId WHERE 1 $NowMonth GROUP BY M.Id";
		/*3-非BOM预付订金*/		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('非BOM预付订金') AS Item, 'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.nonbom11_djmain M LEFT JOIN $DataIn.nonbom11_djsheet A ON A.Mid=M.Id LEFT JOIN $DataPublic.nonbom3_retailermain S ON S.CompanyId=A.CompanyId WHERE 1 $NowMonth GROUP BY M.Id";
		
		/*4-供应商税款*/		$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('供应商税款') AS Item,	'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id	WHERE 1 $NowMonth GROUP BY M.Id";
		/*5-客户货款*/			$mySqlN.=" UNION ALL SELECT (M.PayAmount-M.Handingfee) AS Amount,M.PayDate AS PayDate,concat('客户货款收入') AS Item,'I' AS Sign,M.Remark,S.Currency FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId WHERE 1 $NowMonth";
		/*6-预收货款*/			$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('预收客户货款') AS Item,'I' AS Sign,M.Remark,S.Currency FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object S WHERE M.CompanyId=S.CompanyId $NowMonth GROUP BY M.Id";
		/*7-开发费用*/			$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,concat('开发费用') AS Item,'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";
		/*8-行政费用*/			$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('行政费用') AS Item,	'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";

		/*9-车辆费用*/			$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('车辆费用') AS Item,	'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.carfeemain M LEFT JOIN $DataIn.carfee S ON S.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";

		/*33-员工离职补助*/			$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('员工离职补助') AS Item,	'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.staff_outsubsidymain  M LEFT JOIN $DataIn.staff_outsubsidysheet S ON S.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";


		/*34-其它奖金*/			$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('其它奖金') AS Item,	'O' AS Sign,M.Remark AS Remark,S.Currency FROM $DataIn.cw20_bonusmain M LEFT JOIN $DataIn.cw20_bonussheet S ON S.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";

//二、多货币，条件需单独处理的项目
		/*9-其它收入*/			$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('其它收入') AS Item,'I' AS Sign,M.Remark AS Remark,S.Currency 
FROM $DataIn.cw4_otherinmain  M  LEFT JOIN $DataIn.cw4_otherinsheet S ON S.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";

		/*10-汇兑转入*/			$mySqlN.=" UNION ALL SELECT M.InAmount AS Amount,M.PayDate AS PayDate,concat('汇兑转入') AS Item,'I' AS Sign,M.Remark AS Remark,M.InCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 AND DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth' AND M.InBankId='$BankId'";
		/*11-汇兑转出*/			$mySqlN.=" UNION ALL SELECT M.OutAmount AS Amount,M.PayDate AS PayDate,concat('汇兑转出') AS Item,'O' AS Sign,M.Remark AS Remark,M.OutCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 AND DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth' AND M.OutBankId='$BankId'";
		/*12扣供应商货款*/	$mySqlN.=" UNION ALL SELECT SUM(T.Amount) AS Amount,M.Date AS PayDate,concat('供应商扣款') AS Item,'I' AS Sign,M.Remark,P.Currency FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND M.BankId='$BankId' AND T.Kid!=0 GROUP BY T.Kid";//供应商扣款

		/*13货款返利*/	$mySqlN.=" UNION ALL SELECT SUM(T.Amount) AS Amount,M.Date AS PayDate,concat('货款返利') AS Item,'I' AS Sign,M.Remark,P.Currency 
       FROM $DataIn.cw2_hksheet T 
        LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
       LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
       WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND M.BankId='$BankId' AND T.Did!=0 GROUP BY T.Did";//货款返利


//三、HKD项目，可使用统一条件的项目
		/*13入仓费*/ 			$mySqlN.=" UNION ALL SELECT M.depotCharge AS Amount,M.PayDate AS PayDate,concat('入仓费') AS Item,'O' AS Sign,M.Remark,'3' AS Currency FROM $DataIn.cw4_freight_declaration M LEFT JOIN $DataIn.ch4_freight_declaration S ON M.Id=S.Mid WHERE 1 $NowMonth GROUP BY M.Id";//只用主表计算也可以，但为防止空记录出错就连上明细表
		/*14-Forward杂费*/	$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('Forward杂费') AS Item,'O' AS Sign,M.Remark,'3' AS Currency FROM $DataIn.cw3_forward M LEFT JOIN $DataIn.ch3_forward S ON M.Id=S.Mid WHERE 1 $NowMonth GROUP BY M.Id";//只用主表计算也可以，但为防止空记录出错就连上明细表
										
//四、RMB项目，可使用统一条件的项目
		/*15－员工薪资*/		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('员工薪资') AS Item,'O' AS Sign,M.Remark AS Remark,A.Currency FROM $DataIn.cwxzmain M  LEFT JOIN $DataIn.cwxzsheet A ON A.Mid=M.Id WHERE 1 $NowMonth GROUP BY M.Id";
		///*16－试用工薪资*/	$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('试用工薪资') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cwxztempmain M WHERE 1 $NowMonth";
		/*17－员工借支*/		$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('员工借支') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cwygjz M WHERE 1 $NowMonth";
		/*18－社保缴费*/		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('社保缴费') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.sbpaymain M WHERE 1 $NowMonth";
		/*19－假日加班费*/	$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('假日加班费') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.hdjbmain M WHERE 1 $NowMonth";
		/*20－快递费*/			$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('快递费'),'O' AS Sign,M.Remark,'1' AS Currency FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $NowMonth GROUP BY M.Id";
		/*21－寄样费*/			$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('寄样费'),'O' AS Sign,M.Remark,'1' AS Currency FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $NowMonth GROUP BY M.Id";
		/*22－节日奖金*/		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('节日奖金') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw11_jjmain M WHERE 1 $NowMonth";
		/*23－总务费用*/		$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('总务采购费用') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.zw3_purchasem M WHERE 1 $NowMonth";
		/*24中港运费/报关/商检费用*/	$mySqlN.=" UNION ALL SELECT (M.PayAmount+M.declarationCharge+M.checkCharge) AS Amount,M.PayDate AS PayDate,concat('中港运费/报关/商检费用') AS Item,'O' AS Sign,M.Remark,'1' AS Currency 
		FROM $DataIn.cw4_freight_declaration M
		LEFT JOIN $DataIn.ch4_freight_declaration S ON M.Id=S.Mid WHERE 1 $NowMonth GROUP BY M.Id";
		//五、RMB项目，条件需单独处理的项目
		/*25－退税收益*/		//$mySqlN.=" UNION ALL SELECT ifnull(M.Taxamount,0) AS Amount,M.Taxdate AS PayDate,concat('退税金额') AS Item,'I' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw14_mdtaxmain  M WHERE 1 AND DATE_FORMAT(M.Taxdate,'%Y-%m')='$ChooseMonth' AND M.Estate='0'";
		$mySqlN.=" UNION ALL SELECT ifnull(M.Taxamount,0) AS Amount,M.PayDate,concat('退税金额') AS Item,'I' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw14_mdtaxmain  M WHERE 1 AND DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth' AND M.Estate='0' AND M.BankId='$BankId'";
		
		/*模具费退回*/			$mySqlN.=" UNION ALL SELECT ifnull(M.OutAmount,0) AS Amount,M.Date AS PayDate,concat('模具费退回') AS Item,'I' AS Sign,M.ItemName AS Remark,'1' AS Currency FROM $DataIn.cw16_modelfee  M WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND M.Estate='0'";
		/*体检费用*/				$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('体检费用') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw17_tjmain M WHERE 1 $NowMonth";

		/*助学费用*/				$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('助学费用') AS Item,'O' AS Sign,M.Remark AS Remark,'1' AS Currency FROM $DataIn.cw19_studyfeemain M WHERE 1 $NowMonth";

$PreJY1=$PreJY2=$PreJY3=$PreJY4="&nbsp;";	
$checkPreSumSql=mysql_query("SELECT SUM(Amount) AS Amount,Currency FROM ( ".$mySqlP.") A WHERE 1 GROUP BY Currency",$link_id);
//echo $mySqlN;
//echo "SELECT SUM(Amount) AS Amount,Currency FROM ( ".$mySqlP.") A WHERE 1 GROUP BY Currency";
if($checkPreSumRow=mysql_fetch_array($checkPreSumSql)){
	do{//读取此银行之前备货币的结余
		$PreAmount=sprintf("%.2f",$checkPreSumRow["Amount"]);
		$PreCurrency=$checkPreSumRow["Currency"];
		$TempEstateSTR="PreJY".strval($PreCurrency); 
		$$TempEstateSTR=$PreAmount;
		}while($checkPreSumRow=mysql_fetch_array($checkPreSumSql));
	}
//前期结余
/*if($PreJY1=="")$PreJY1=0;
if($PreJY2=="")$PreJY2=0;
if($PreJY3=="")$PreJY3=0;*/
//if(!$PreJY4)$PreJY4=0;
$PreJY1=(float)$PreJY1;
$PreJY2=(float)$PreJY2;
$PreJY3=(float)$PreJY3;
$PreJY4=(float)$PreJY4;
echo "<tr align='right'>
	<td class='A0111' height='25'>&nbsp;</td><td class='A0101'>&nbsp;</td><td class='A0101' align='left'>上次结余</td><td class='A0101'>&nbsp;</td>
	<td class='A0101'>".number_format($PreJY1,2)."</td><td class='A0101'>&nbsp;</td>
	<td class='A0101' bgcolor='#CCFFCC'>".number_format($PreJY2,2)."</td><td class='A0101' bgcolor='#CCFFCC'>&nbsp;</td>
	<td class='A0101'>".number_format($PreJY3,2)."</td><td class='A0101'>&nbsp;</td>
	<td class='A0101' bgcolor='#CCFFCC'>".number_format($PreJY4,2)."</td><td class='A0101' bgcolor='#CCFFCC'>&nbsp;</td>
</tr>";

$SumInRMB=0;	$SumOutRMB=0;
$SumInUSD=0;	$SumOutUSD=0;
$SumInHKD=0;	$SumOutHKD=0;
$SumInNTD=0;	$SumOutNTD=0;

$mySqlN.=" ORDER BY PayDate,Sign";//echo $mySqlN;
$myResult = mysql_query($mySqlN,$link_id);
$i=2;
$num=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$RMBout=$RMBin=$USDout=$USDin=$HKDout=$HKDin=$NTDout=$NTDin="&nbsp;";
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$PayDate=$myRow["PayDate"];
		$Item=$myRow["Item"];		
		$Remark=$myRow["Remark"];
		if($Remark=="")	$Remark="&nbsp;";
		$Sign=$myRow["Sign"];
		$Currency=$myRow["Currency"];
		switch($Currency){
			case 1:			//RMB
				if($Sign=="I"){
					$RMBin=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$RMBin);
					$SumInRMB+=$RMBin;
					}
				else{
					$RMBout=$Amount*-1;
					$PreAmount=sprintf("%.2f",$PreAmount-$RMBout);
					$SumOutRMB+=$RMBout;
					}
			break;
			case 2:			//USD
				if($Sign=="I"){
					$USDin=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$USDin);
					$SumInUSD+=$USDin;
					}
				else{
					$USDout=$Amount*-1;
					$PreAmount=sprintf("%.2f",$PreAmount-$USDout);
					$SumOutUSD+=$USDout;
					}
			break;
			case 3:			//HKD
				if($Sign=="I"){
					$HKDin=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$HKDin);
					$SumInHKD+=$HKDin;
					}
				else{
					$HKDout=$Amount*-1;
					$PreAmount=sprintf("%.2f",$PreAmount-$HKDout);
					$SumOutHKD+=$HKDout;
					}
				break;
			case 4:
				if($Sign=="I"){
					$NTDin=$Amount;				
					$PreAmount=sprintf("%.2f",$PreAmount+$NTDin);
					$SumInNTD+=$NTDin;
					}
				else{
					$NTDout=$Amount*-1;
					$PreAmount=sprintf("%.2f",$PreAmount-$NTDout);
					$SumOutNTD+=$NTDout;
					}
				break;
			}
		//输出行记录
		echo "<tr align='right'>";
			echo "<td class='A0111' height='25' align='center'>$num</td>";
			echo "<td class='A0101' align='center'>$PayDate</td>";
			echo "<td class='A0101' align='left'>$Item</td>";
			echo "<td class='A0101' align='left'><DIV title='$Remark'>$Remark</DIV></td>";
			echo "<td class='A0101'>$RMBin</td>";
			echo "<td class='A0101'>$RMBout</td>";
			echo "<td class='A0101' bgcolor='#CCFFCC'>$USDin</td>";
			echo "<td class='A0101' bgcolor='#CCFFCC'>$USDout</td>";
			echo "<td class='A0101'>$HKDin</td>";
			echo "<td class='A0101'>$HKDout</td>";
			echo "<td class='A0101' bgcolor='#CCFFCC'>$NTDin</td>";
			echo "<td class='A0101' bgcolor='#CCFFCC'>$NTDout</td>";
		echo "</tr>";
		$num++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo "<tr align='right'><td class='A0111' colspan='12' height='25'  align='center'>本月没有资料</td></tr>";
  	}
//小计
$SumInRMB+=$PreJY1;
$SumInUSD+=$PreJY2;
$SumInHKD+=$PreJY3;
$SumInNTD+=$PreJY4;

//货币合计
$SumRMB=$SumInRMB+$SumOutRMB;
$SumUSD=$SumInUSD+$SumOutUSD;
$SumHKD=$SumInHKD+$SumOutHKD;
$SumNTD=$SumInNTD+$SumOutNTD;


//提取汇率
$rateResult = mysql_query("SELECT Rate,Id FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Id=$rateRow["Id"];$TempHZSTR="Rate_".strval($Id);$$TempHZSTR=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
//全部合计
$AmountTotal=$SumRMB+$SumUSD*$Rate_2+$SumHKD*$Rate_3+$SumNTD*$Rate_4;
$AmountTotal=number_format($AmountTotal);
//步骤7：
echo '</div>';
/*$SumInRMB=$SumInRMB==0?"&nbsp;":$SumInRMB;$SumOutRMB=$SumOutRMB==0?"&nbsp;":$SumOutRMB;
$SumInUSD=$SumInUSD==0?"&nbsp;":$SumInUSD;$SumOutUSD=$SumOutUSD==0?"&nbsp;":$SumOutUSD;
$SumInHKD=$SumInHKD==0?"&nbsp;":$SumInHKD;$SumOutHKD=$SumOutHKD==0?"&nbsp;":$SumOutHKD;
$SumInNTD=$SumInNTD==0?"&nbsp;":$SumInNTD;$SumOutNTD=$SumOutNTD==0?"&nbsp;":$SumOutNTD;
*/
//$SumInNTD=(float)$SumInNTD;
echo "<tr align='right'>";
echo "<td colspan='4' class='A0111' height='25' bgcolor='#CCCCCC' align='center'>小计</td>";
echo "<td class='A0101'>".number_format($SumInRMB,2)."</td>";
echo "<td class='A0101'>".number_format($SumOutRMB,2)."</td>";
echo "<td class='A0101' bgcolor='#CCFFCC'>".number_format($SumInUSD,2)."</td>";
echo "<td class='A0101' bgcolor='#CCFFCC'>".number_format($SumOutUSD,2)."</td>";
echo "<td class='A0101'>".number_format($SumInHKD,2)."</td>";
echo "<td class='A0101'>".number_format($SumOutHKD,2)."</td>";
echo "<td class='A0101' bgcolor='#CCFFCC'>".number_format($SumInNTD,2)."</td>";
echo "<td class='A0101' bgcolor='#CCFFCC'>".number_format($SumOutNTD,2)."</td>";
echo "</tr>";
//$SumRMB=$SumRMB==0?"&nbsp;":$SumRMB;
//$SumUSD=$SumUSD==0?"&nbsp;":$SumUSD;
//$SumHKD=$SumHKD==0?"&nbsp;":$SumHKD;
//$SumNTD=$SumNTD==0?"&nbsp;":$SumNTD;


echo "<tr align='right'>";
echo "<td class='A0111' colspan='4' height='25'  bgcolor='#CCCCCC' align='center'>结余</td>";
echo "<td class='A0101' colspan='2'>".number_format($SumRMB,2)."</td>";
echo "<td class='A0101' colspan='2' bgcolor='#CCFFCC'>".number_format($SumUSD,2)."</td>";
echo "<td class='A0101' colspan='2'>".number_format($SumHKD,2)."</td>";
echo "<td class='A0101' colspan='2' bgcolor='#CCFFCC'>".number_format($SumNTD,2)."</td>";
echo "</tr>";

echo "<tr align='center'>";
echo "<td class='A0111' colspan='4' height='25'  bgcolor='#CCCCCC'>合计：转RMB总额约：</td>";
echo "<td class='A0101' colspan='8' align='right'>$AmountTotal</td>";
echo "</tr></table>";
?>