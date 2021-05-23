<?php
$Result="SELECT IFNULL(SUM((M.AddQty+M.FactualQty)*M.Price),0) AS Amount,'1' AS Name,P.Currency,'供应商货款' AS ItemName 
               FROM $DataIn.cw1_fkoutsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
               LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms GROUP BY P.Currency";//供应商货款
$Result.=" UNION ALL SELECT IFNULL(SUM(M.FactualQty*M.Price),0) AS Amount,	'2' AS Name,P.Currency,'佣金' AS ItemName 
               FROM $DataIn.cw1_tkoutsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
               LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms GROUP BY P.Currency";//客户回扣
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'3' AS Name,P.Currency,'未付订金' AS ItemName 
               FROM $DataIn.cw2_fkdjsheet M LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
               WHERE 1 AND M.Estate=3 AND M.Did=0  GROUP BY P.Currency";
$Result.=" UNION ALL SELECT IFNULL(SUM(-M.Amount),0) AS Amount,'4' AS Name,P.Currency,'未抵货款的订金' AS ItemName  
               FROM $DataIn.cw2_fkdjsheet M LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
               WHERE 1 AND M.Did=0 AND (M.Estate=0 OR M.Estate=3) GROUP BY P.Currency";//未抵货款的订金(需扣除此金额)
$Result.=" UNION ALL SELECT IFNULL(SUM(T.Amount*-1),0) AS Amount ,'5' AS Name, P.Currency ,'扣供应商货款' AS ItemName 
               FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id 
               LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND M.Estate=0 AND T.Kid=0 GROUP BY P.Currency";			//扣供应商货款
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'6' AS Name,M.Currency,'供应商税款' AS ItemName 
               FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//供应商税款
$Result.=" UNION ALL SELECT IFNULL(SUM(-M.Taxamount ),0) AS Amount,	'7' AS Name,'1' AS Currency,'未结付退税收益' AS ItemName 
               FROM $DataIn.cw14_mdtaxmain M WHERE 1 AND M.Estate=3";//未结付退税
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'8' AS Name,M.Currency,'开发费用' AS ItemName 
               FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms GROUP BY M.Currency";				//开发费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Qty*M.Price),0) AS Amount,'9' AS Name,'1' AS Currency,'总务采购费用' AS ItemName 
               FROM $DataIn.zw3_purchases M $Terms";		//总务采购费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'10' AS Name,M.Currency,'行政费用' AS ItemName 
               FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms GROUP BY M.Currency";	//行政费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'11' AS Name,'1' AS Currency,'员工薪资' AS ItemName 
               FROM $DataIn.cwxzsheet M $Terms";				//员工薪资
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'12' AS Name,'1' AS Currency,'假日加班费' AS ItemName 
               FROM $DataIn.hdjbsheet M $Terms";				//假日加班费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'13' AS Name,'1' AS Currency,'社保费用' AS ItemName 
               FROM $DataIn.sbpaysheet M $Terms AND M.TypeId=1";	//社保费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'14' AS Name,'1' AS Currency,'节日奖金' AS ItemName 
               FROM $DataIn.cw11_jjsheet M $Terms";			//节日奖金
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'15' AS Name,'3' AS Currency,'Forward费用' AS ItemName 
               FROM $DataIn.ch3_forward M LEFT JOIN $DataPublic.freightdata B ON B.CompanyId=M.CompanyId  $Terms";//Forward费用:不分货币？
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'16' AS Name,'1' AS Currency,'中港运费' AS ItemName 
               FROM $DataIn.ch4_freight_declaration M $Terms";	//中港运费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.depotCharge),0) AS Amount,	'17' AS Name,'3' AS Currency,'入仓费' AS ItemName 
               FROM $DataIn.ch4_freight_declaration M $Terms";	//中港运费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.declarationCharge+M.checkCharge+M.carryCharge+M.xyCharge+M.wfqgCharge+M.ccCharge+M.djCharge+M.stopcarCharge+M.expressCharge+M.otherCharge),0) AS Amount,'18' AS Name,'1' AS Currency,'报关/商检费用' AS ItemName 
               FROM $DataIn.ch4_freight_declaration M $Terms";			//报关费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'19' AS Name,'1' AS Currency,'快递费用' AS ItemName 
               FROM $DataIn.ch9_expsheet M $Terms";			//快递费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'20' AS Name,'1' AS Currency,'寄样费用' AS ItemName 
               FROM $DataIn.ch10_samplemail M $Terms";			//寄样费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount*-1),0) AS Amount,	'21' AS Name,M.Currency,'其他收入' AS ItemName 
               FROM $DataIn.cw4_otherinsheet M WHERE M.Estate=3 GROUP BY M.Currency";			//其他收入
$Result.=" UNION ALL SELECT IFNULL(SUM(M.OutAmount*-1),0) AS Amount,	'22' AS Name,'1' AS Currency,'模具退回费' AS ItemName 
               FROM $DataIn.cw16_modelfee M $Terms ";			//模具退回费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,	'23' AS Name,'1' AS Currency,'体检费用' AS ItemName 
               FROM $DataIn.cw17_tjsheet M $Terms ";			//体检费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'24' AS Name,'1' AS Currency,'住房公积金' AS ItemName 
               FROM $DataIn.sbpaysheet M $Terms AND M.TypeId=2";			//住房公积金
$Result.=" UNION ALL SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'25' AS Name,'1' AS Currency,'意外险' AS ItemName 
               FROM $DataIn.sbpaysheet M $Terms AND M.TypeId=3";			//意外险
$Result.=" UNION ALL 
				SELECT IFNULL(SUM(M.Amount),0) AS Amount,'26' AS Name,P.Currency,'非BOM货款' AS ItemName 
               FROM $DataIn.nonbom11_qksheet M 
			   LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=M.CompanyId 
               LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms GROUP BY P.Currency";//非BOM货款
 $Result.=" UNION ALL 
				SELECT IFNULL(SUM(M.Amount),0) AS Amount,'27' AS Name,M.Currency,'车辆费用' AS ItemName 
                 FROM $DataIn.carfee M 
                 LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms GROUP BY M.Currency";//车辆费用                 
  $Result.=" UNION ALL 
				SELECT IFNULL(SUM(M.Amount),0) AS Amount,'28' AS Name,M.Currency,'员工离职补助' AS ItemName 
                 FROM $DataIn.staff_outsubsidysheet M 
                 LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms GROUP BY M.Currency";//员工离职补助  
  $Result.=" UNION ALL 
				SELECT IFNULL(SUM(M.Amount),0)*-1 AS Amount,'29' AS Name,C.Currency,'供应商货款返利' AS ItemName 
                 FROM $DataIn.cw2_hksheet M 
                 LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId 
                 LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency $Terms AND M.Did=0 GROUP BY C.Currency";//供应商货款返利       
                           
	$SumAmount_1=$SumAmount_2=$SumAmount_3=$SumAmount_4=0;
	$noPaySql = mysql_query("$Result",$link_id);
	if($noPayRow = mysql_fetch_array($noPaySql)) {
		$Num=1;
		do{
			$noPayRMB=sprintf("%.2f",$noPayRow["Amount"]);
			$Currency=$noPayRow["Currency"];
			$Name=$noPayRow["Name"];
			$TempName="A".$Name."_".strval($Currency);
			$$TempName=$noPayRMB;
			$TempSumAmount="SumAmount_".strval($Currency);
			$$TempSumAmount+=$noPayRMB;
			}while ($noPayRow = mysql_fetch_array($noPaySql));
		}
	//总RMB
	$rateResult = mysql_query("SELECT Rate,Id FROM $DataPublic.currencydata WHERE 1 and Estate=1 OR Id=4",$link_id);
	if($rateRow = mysql_fetch_array($rateResult)){
		do{
			$Id=$rateRow["Id"];
			$TempCurrency="SumAmount_".strval($Id);
			$SumRMB+=$$TempCurrency*$rateRow["Rate"];
			$TempRate="Rate_".strval($Id);
			$$TempRate=$rateRow["Rate"];
			}while($rateRow = mysql_fetch_array($rateResult));
		}
//echo $SumRMB;
?>