<?php   
//ewen-2012-12-31
$SystemDataS="2016-01-01";
//取已结付财务数据条件
$Terms="WHERE 1 AND M.PayDate>='$SystemDataS'"; 
//RMB专有项目


///*2-试用工薪资*/	$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cwxztempmain M $Terms";//已取消
/*3-员工借支*/		$Result1.=" UNION ALL SELECT SUM(M.Amount)    *-1 AS Amount FROM $DataIn.cwygjz M $Terms";
/*4-社保缴费*/		$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.sbpaymain M $Terms";//多货币
/*5-假日加班费*/	$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.hdjbmain M $Terms";
/*6-快递费*/			$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw9_expsheet 	M $Terms";
/*7-寄样费*/			$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw10_samplemail M $Terms";
/*8-节日奖金*/		$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw11_jjmain M $Terms";
/*9-总务采购费用*/$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.zw3_purchasem 	M $Terms";
/*13 中港运费/报关/商检费用*/$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw4_freight_declaration M $Terms";
/*10退税收益*/		$Result1.=" UNION ALL SELECT SUM(M.Taxamount) AS Amount  FROM $DataIn.cw14_mdtaxmain M  WHERE 1 AND M.Taxdate>='$SystemDataS' and M.Estate='0'";  

/*11 模具退回费用*/  $Result1.=" UNION ALL SELECT SUM(M.OutAmount) AS Amount  FROM $DataIn.cw16_modelfee M  WHERE 1 AND M.Date>='$SystemDataS' and M.Estate='0'";  
/*12 体检费用*/  $Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount  FROM $DataIn.cw17_tjmain M  $Terms";  
/*15 助学费用*/  $Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount  FROM $DataIn.cw19_studyfeemain M  $Terms";  

//HKD专有项目
///*1-入仓费*/			$Result3=" UNION ALL SELECT SUM(M.depotCharge) *-1 AS Amount FROM $DataIn.cw4_freight_declaration M $Terms";
/*2-Forward杂费*/$Result3.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw3_forward M $Terms";

//共有项目
for($j=1;$j<4;$j++){//1RMB 2USD 3HKD 4TWD	//2014.6.9 加入台币,欧元
	$Currency=$j;
	$ResultSTR="Result".strval($Currency); 
	/*1-其它收款*/		$Result="SELECT SUM(S.Amount) 	AS Amount FROM $DataIn.cw4_otherinmain  M  
								LEFT JOIN $DataIn.cw4_otherinsheet S ON S.Mid=M.Id 
							   AND S.Currency='$Currency'";

/*1-员工薪资*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.cwxzmain M,$DataIn.cwxzsheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' ";//多货币
   
	/*2-汇兑转入*/		$Result.=" UNION ALL SELECT SUM(M.InAmount)  AS Amount FROM $DataIn.cw5_fbdh M $Terms AND M.InCurrency='$Currency'";
	/*3-汇兑转出*/		$Result.=" UNION ALL SELECT SUM(-M.OutAmount) AS Amount FROM $DataIn.cw5_fbdh M $Terms AND M.OutCurrency='$Currency'";
	/*4-开发费用*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.cwdyfmain M,$DataIn.cwdyfsheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency'";
	/*5-行政费用*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.hzqkmain M,$DataIn.hzqksheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' ";
	/*6-供应商税款*/	$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.cw2_gysskmain M,$DataIn.cw2_gyssksheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' ";
	/*7-客户货款*/		$Result.=" UNION ALL SELECT SUM(M.PayAmount-M.Handingfee) AS Amount FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId $Terms AND C.Currency=$Currency";
	/*8-预收客户货款*/$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw6_advancesreceived M LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId $Terms AND C.Currency=$Currency";
	/*9-BOM货款支出*/		$Result.=" UNION ALL SELECT SUM(-M.PayAmount) AS Amount FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C $Terms AND C.CompanyId=M.CompanyId AND C.Currency=$Currency";
	
	//ewen 2013-11-21 取消非BOM订金，结付主表名称有改变
	/*9-非BOM货款支出*/	$Result.=" UNION ALL SELECT SUM(-M.PayAmount) AS Amount FROM $DataIn.nonbom11_qkmain M,$DataPublic.nonbom3_retailermain C WHERE C.CompanyId=M.CompanyId AND C.Currency=$Currency";

	///*12-非BOM预付订金*/	$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.nonbom11_djmain M,$DataIn.nonbom11_djsheet S,$DataPublic.nonbom3_retailermain P $Terms AND S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency=$Currency";
	
	/*10客户回扣*/		$Result.=" UNION ALL SELECT SUM(-M.PayAmount) AS Amount FROM $DataIn.cw1_tkoutmain M,$DataIn.trade_object C $Terms AND C.CompanyId=M.CompanyId AND C.Currency=$Currency";
	/*11-扣供应商货款*/$Result.=" UNION ALL SELECT IFNULL(SUM(T.Amount),0) AS Amount FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND M.Estate=0 AND T.Kid>0 AND P.Currency=$Currency";
	/*12-预付订金*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P $Terms AND S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency=$Currency";

	/*13-供应商货款返利*/$Result.=" UNION ALL SELECT IFNULL(SUM(T.Amount),0) AS Amount FROM $DataIn.cw2_hksheet T   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=T.CompanyId WHERE 1 AND T.Estate=0 AND T.Did>0 AND P.Currency=$Currency";

/*车辆费用*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.carfeemain M,$DataIn.carfee S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' AND S.Date>='2016-01-01' ";

	/*员工离职补助*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.staff_outsubsidymain M,$DataIn.staff_outsubsidysheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' ";

/*其它奖金*/		$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.cw20_bonusmain M,$DataIn.cw20_bonussheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' ";

       $Result.=$$ResultSTR;
	
	$MyResult = mysql_query("SELECT SUM(Amount) AS SumAmount FROM ($Result) A",$link_id);
	if($MyResult  && $MyRow = mysql_fetch_array($MyResult)) {
		$ResultSTR="SumAmount".strval($Currency); 
		$$ResultSTR=sprintf("%.2f",$MyRow["SumAmount"]);
		
		 //echo "$ResultSTR: " .  $Result . "<br>" ;
		}
	}
//汇率
$checkCurrency=mysql_query("SELECT Symbol,Rate FROM $DataPublic.currencydata WHERE Estate=1 AND Id>1 ORDER BY Id",$link_id);
if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
	do{
		$TempRate=strval($checkCurrencyRow["Symbol"])."Rate"; 
		$$TempRate=$checkCurrencyRow["Rate"];	
		}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
	}
//echo "$SumAmount1+$SumAmount2*$USDRate+$SumAmount3*$HKDRate+$SumAmount4*$TWDRate+$SumAmount5*$EURRate";
$SumTotal=$SumAmount1+$SumAmount2*$USDRate+$SumAmount3*$HKDRate+$SumAmount4*$TWDRate+$SumAmount5*$EURRate;

$contentSTR="<li class=TitleBL>现金</li><li class=TitleBR><a href='$Extra' target='_blank'>¥".number_format($SumTotal)."</a></li>
<li class=DataA>$".number_format($SumAmount2)."</li> 
<li class=DataA>HK$".number_format($SumAmount3)."</li>
<li class=DataA>NT$".number_format($SumAmount4)."</li>	
<li class=DataA>€".number_format($SumAmount5)."</li>
<li class=DataA>¥".number_format($SumAmount1)."</li>";//2014.6.9 加入台币，欧元
?>