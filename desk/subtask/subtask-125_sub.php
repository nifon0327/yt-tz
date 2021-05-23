<?
//此文件取消
$NowMonthTemp=date("Y-m");

$TempPayDatetj=" AND M.PayDate<'".$NowMonthTemp."-01'";
$TempDatetj=" AND M.Date<'".$NowMonthTemp."-01'";
$TempMonthtj="  AND M.Month<'$NowMonthTemp'";
$TempSendDatetj=" AND M.SendDate<'".$NowMonthTemp."-01'";
$TempqkDatetj=" AND M.qkDate<'".$NowMonthTemp."-01'";
$TempDateTax=" AND M.TaxDate<'".$NowMonthTemp."-01'";
$TempDateModelf=" AND M.Date<'".$NowMonthTemp."-01'";
$TempDeliveryDate=" AND M.DeliveryDate<'".$NowMonthTemp."-01'";

//汇率参数
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
//1 进项
$Sum_A=0;
//全部正常出货款：以出货日期计算
$HKIN_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 AND M.Estate=0 AND M.Sign=1 AND M.ShipType!='debit' AND M.Date>='2008-07-01' $TempDatetj",$link_id));
$Sum_A+=sprintf("%.0f",$HKIN_A["Amount"]);//全部正常出货款
//2其他销货金额(含debit note)
$HKinDebit_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 AND M.Estate=0 AND M.Sign=1 AND M.ShipType='debit' AND M.Date>='2008-07-01' $TempDatetj  ",$link_id));
$Sum_A+=sprintf("%.0f",$HKinDebit_A["Amount"]);
//3已结付其他收入
$checkOTSql=mysql_fetch_array(mysql_query("
SELECT SUM(M.Amount*C.Rate) AS Amount
FROM $DataIn.cw4_otherinsheet  M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND (M.Estate=0 OR M.Estate=3) AND M.PayDate>='2008-07-01' $TempPayDatetj ",$link_id));
$Sum_A+=sprintf("%.0f",$checkOTSql["Amount"]);
//4免抵退税收益
$checkMdtaxA=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Taxamount),0) AS Amount FROM $DataIn.cw14_mdtaxmain M WHERE (M.Estate=0 OR M.Estate=3 )AND M.TaxDate>='2008-07-01'  $TempDateTax",$link_id));
$Sum_A+=sprintf("%.0f",$checkMdtaxA["Amount"]);
//5汇兑损益+HZ642：以结付日期计算，没有未结付状态
$checkHDSY_Y=mysql_fetch_array(mysql_query("SELECT SUM(Amount) AS Amount FROM (
	SELECT IFNULL(SUM(-M.OutAmount*C.Rate),0) AS Amount FROM $DataIn.cw5_fbdh M,$DataPublic.currencydata C WHERE 1 and M.OutCurrency=C.Id AND M.PayDate>'2008-07-01' $TempPayDatetj
	UNION ALL 
	SELECT IFNULL(SUM(M.InAmount*C.Rate),0) AS Amount FROM $DataIn.cw5_fbdh M,$DataPublic.currencydata C WHERE 1 and M.InCurrency=C.Id AND M.PayDate>'2008-07-01' $TempPayDatetj
	)A",$link_id));
$Sum_A+=$checkHDSY_Y["Amount"];
//6扣供应商货款
$checkGysTK_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(T.Amount*C.Rate),0) AS Amount FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  WHERE 1 AND M.Estate=0 $TempDatetj",$link_id));
$Sum_A+=sprintf("%.0f",$checkGysTK_A["Amount"]);
//7模具费退回
$checkModelfA=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.OutAmount),0) AS Amount FROM $DataIn.cw16_modelfee M WHERE (M.Estate=0 OR M.Estate=3) $TempDateModelf",$link_id));
$Sum_A+=sprintf("%.0f",$checkModelfA["Amount"]);
/////////222222
			//未结付供应商货款：以请款月份计算，分现金和月结统计
			$gysWFSql=mysql_query("
								  SELECT SUM(Amount) AS Amount,Name FROM(
								  SELECT (M.Amount*C.Rate) AS Amount,concat('FKout_W',P.GysPayMode) AS Name
				FROM $DataIn.cw1_fkoutsheet M 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
				WHERE 1 AND M.Estate=3  $TempMonthtj 
				UNION ALL
				 SELECT (M.Amount*C.Rate) AS Amount,concat('FKout_W',P.GysPayMode) AS Name
				FROM $DataIn.cw1_tkoutsheet M 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
				WHERE 1 AND M.Estate=3  $TempMonthtj 
				) A GROUP BY Name ORDER BY Name
				",$link_id);
						
			if($gysWFRow=mysql_fetch_array($gysWFSql)){
				do{
					$Amount=sprintf("%.0f",$gysWFRow["Amount"]);
					$Name=$gysWFRow["Name"];
					$TempFKout=strval($Name); 
					$$TempFKout=$Amount;
					}while  ($gysWFRow=mysql_fetch_array($gysWFSql));
				}
			//已付供应商货款：以请款月份计算，分现金和月结统计
			$gysYFSql=mysql_query("
								  SELECT SUM(Amount) AS Amount,Name FROM( 
								   SELECT (M.Amount*C.Rate) AS Amount,concat('FKout_Y',P.GysPayMode) AS Name
				FROM $DataIn.cw1_fkoutsheet M 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
				WHERE 1 AND M.Estate=0  $TempMonthtj 
				UNION ALL
				SELECT (M.Amount*C.Rate) AS Amount,concat('FKout_Y',P.GysPayMode) AS Name
				FROM $DataIn.cw1_tkoutsheet M 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
				WHERE 1 AND M.Estate=0  $TempMonthtj 
				) A GROUP BY Name ORDER BY Name
				",$link_id);
			if($gysYFRow=mysql_fetch_array($gysYFSql)){
				do{
					$Amount=sprintf("%.0f",$gysYFRow["Amount"]);
					$Name=$gysYFRow["Name"];
					$TempFKout=strval($Name); 
					$$TempFKout=$Amount;
					}while  ($gysYFRow=mysql_fetch_array($gysYFSql));
				}
//*************供应商订金处理
		for($p=0;$p<2;$p++){
			//未结付订金：以请款日期计算Estate=3
			$gysDj_WSql=mysql_query("
					SELECT SUM(M.Amount*C.Rate) AS Amount,concat('FKoutDJ_W',$p) AS Name 
					FROM $DataIn.cw2_fkdjsheet M 
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
					LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
					WHERE 1 AND M.Mid=0 AND M.Did=0 AND M.Estate=3 AND M.Date>='2008-07-01' $TempDatetj AND P.GysPayMode='$p'",$link_id);
			if($gysDj_WRow=mysql_fetch_array($gysDj_WSql)){
					$Amount=sprintf("%.0f",$gysDj_WRow["Amount"]);
					$Name=$gysDj_WRow["Name"];
					$TempFKoutDj=strval($Name); 
					$$TempFKoutDj=$Amount;
				}
		
			//已结付订金，已抵付
			$gysDj_YDSql=mysql_query("
						SELECT SUM(M.Amount*C.Rate) AS Amount,concat('FKoutDJ_YD',$p) AS Name
						FROM $DataIn.cw2_fkdjsheet M 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT
						JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
						WHERE 1 AND M.Mid>0 AND M.Did>0 AND M.Estate=0 AND M.Date>='2008-07-01' $TempDatetj AND P.GysPayMode='$p'",$link_id);
			if($gysDj_YDRow=mysql_fetch_array($gysDj_YDSql)){
					$Amount=sprintf("%.0f",$gysDj_YDRow["Amount"]);
					$Name=$gysDj_YDRow["Name"];
					$TempFKoutDj=strval($Name); 
					$$TempFKoutDj=$Amount;
				}
			//已结付订金，未抵付
			$gysDj_WDSql=mysql_query("
						SELECT SUM(M.Amount*C.Rate) AS Amount,concat('FKoutDJ_WD',$p) AS Name 
						FROM $DataIn.cw2_fkdjsheet M 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT
						JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
						WHERE 1 AND M.Mid>0 AND M.Did=0 AND M.Estate=0 AND M.Date>='2008-07-01' $TempDatetj AND P.GysPayMode='$p'",$link_id);
			if($gysDj_WDRow=mysql_fetch_array($gysDj_WDSql)){
					$Amount=sprintf("%.0f",$gysDj_WDRow["Amount"]);
					$Name=$gysDj_WDRow["Name"];
					$TempFKoutDj=strval($Name); 
					$$TempFKoutDj=$Amount;
				}
			}//end for
//处理货款
//B19 现金货款
$Sum_A-=$FKout_Y1+$FKout_W1-$FKoutDJ_W1-$FKoutDJ_WD1-$FKoutDJ_YD1;

$Sum_A-=$FKout_Y0+$FKout_W0-$FKoutDJ_W0-$FKoutDJ_WD0-$FKoutDJ_YD0;

$Sum_A-=$FKout_Y2+$FKout_W2-$FKoutDJ_W2-$FKoutDJ_WD2-$FKoutDJ_YD2;
//货款部分4-1:已结付扣款：以出货日期计算
$ClientKK_Y=mysql_fetch_array(mysql_query("
	SELECT SUM(Amount*-1) AS Amount FROM (
		SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount FROM $DataIn.cw6_orderinsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND M.cwSign=0 AND M.Sign=-1 AND M.Date>='2008-07-01' $TempDatetj
		UNION ALL
		SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount FROM $DataIn.cw6_orderinsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND M.cwSign=2 AND M.Sign=-1 AND M.Date>='2008-07-01' $TempDatetj
	)A
	",$link_id));
//货款部分4-2:扣款总金额：以出货日期计算
$ClientKK_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 AND M.Estate=0 AND M.Sign=-1 AND M.Date>='2008-07-01' $TempDatetj ",$link_id));
//B21 客户退款
$Sum_A-=sprintf("%.0f",$ClientKK_A["Amount"]);

//需分开计算，否则与月份有冲突
$gysSK_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Mid>0 AND M.Estate=0 AND M.Date>='2008-07-01' $TempDatetj",$link_id));
$gysSK_W=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Mid=0 AND M.Estate=3 AND M.Date>='2008-07-01' $TempDatetj",$link_id));

$Sum_A-=$Value_A[$Subscript][]=sprintf("%.0f",$gysSK_Y["Amount"])+sprintf("%.0f",$gysSK_W["Amount"]);


$Sum_A-=$FKoutDJ_YD1+$FKoutDJ_WD1+$FKoutDJ_YD0+$FKoutDJ_WD0+$FKoutDJ_W1+$FKoutDJ_W0;
////////////////333333333333
//薪资
$checkXZ_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM  $DataIn.cwxzsheet M WHERE M.Month>='2008-07' AND (M.Estate='0' OR M.Estate='3') $TempMonthtj",$link_id));
$Sum_A-=sprintf("%.0f",$checkXZ_A["Amount"]);		
//假日加班费
$checkJBF_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.hdjbsheet M WHERE M.Month>='2008-07' AND (M.Estate='0' OR M.Estate='3') $TempMonthtj",$link_id));
$Sum_A-=sprintf("%.0f",$checkJBF_A["Amount"]);		
//社保费用
$checkSBF_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE M.Month>='2008-07' AND (M.Estate='0' OR M.Estate='3') $TempMonthtj",$link_id));
$Sum_A-=sprintf("%.0f",$checkSBF_A["Amount"]);
//其他薪资奖金
$checkLSGXZ_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cwxztempsheet M WHERE M.Month>='2008-07' AND (M.Estate='0' OR M.Estate='3') $TempMonthtj",$link_id));
$Sum_A-=sprintf("%.0f",$checkLSGXZ_A["Amount"]);
/////////////////////666666666666
//开发费用OK
$checkKF_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount 
															FROM $DataIn.cwdyfsheet M 
															LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
															WHERE 1  AND M.Date>='2008-07-01' AND (M.Estate=0 OR M.Estate=3) $TempDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$checkKF_A["Amount"]);
///////////////////5555555555555
//E69-三节奖金OK
$checkJJ_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw11_jjsheet M WHERE 1 AND M.Month>='2008-07' AND (M.Estate='0' OR M.Estate='3') $TempMonthtj",$link_id));
$Sum_A-=sprintf("%.0f",$checkJJ_A["Amount"]);
//E74-员工借支OK
$checkYGJZ_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.cwygjz M WHERE 1 AND M.PayDate>='2008-07-01' $TempPayDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$checkYGJZ_Y["Amount"]);

///////////44444
//D45-618+国内快递费(包括月结和现金)
$checkKDF1_A=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch9_expsheet M WHERE 1 AND M.Date>='2008-07-01'  AND (M.Estate='0' OR M.Estate='3')  $TempDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$checkKDF1_A["Amount"]);			
//D46-国外快递费:寄样费
$checkKDF2_A=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch10_samplemail M WHERE 1 AND M.SendDate>='2008-07-01' AND (M.Estate='0' OR M.Estate='3')  $TempSendDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$checkKDF2_A["Amount"]);
									
$Sql51=mysql_fetch_array(mysql_query("SELECT SUM(M.Handingfee*C.Rate) AS Amount FROM $DataIn.cw6_orderinmain M, $DataIn.trade_object K, $DataPublic.currencydata C WHERE K.CompanyId=M.CompanyId AND K.Currency=C.Id AND M.PayDate>='2008-07-01' $TempPayDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$Sql51["Amount"]);
							  
$checkTJF_A=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw17_tjsheet M WHERE 1 AND (M.Estate='0' OR M.Estate='3') $TempDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$checkTJF_A["Amount"]);	
//中港代付运费
$freightY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											)A
											",$link_id));
$freightW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											 )A
											 ",$link_id));
$Sum_A-=sprintf("%.0f",$freightY["Amount"])+sprintf("%.0f",$freightW["Amount"]);

//H87-货代杂费Forward费用：以出货日期计算
$forwardY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM (
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											 ) A
											 ",$link_id));
$forwardW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											 ) A
											 ",$link_id));
$Sum_A-=sprintf("%.0f",$forwardY["Amount"]*$HKD_Rate)+sprintf("%.0f",$forwardW["Amount"]*$HKD_Rate);

//全部行政费用
$checkHZSql=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount 
									FROM $DataIn.hzqksheet M 
									LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
									WHERE 1 AND M.Date>='2008-07-01' $TempDatetj AND (M.Estate=3 OR M.Estate=0) ",$link_id));
$Sum_A-=sprintf("%.0f",$checkHZSql["Amount"]);
//独立申购的总务费用：按主分类分开统计
$checkZWYSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Qty*M.Price),0) AS Amount  FROM $DataIn.zw3_purchases M LEFT JOIN $DataIn.zw3_purchaset S ON S.Id=M.TypeId WHERE M.qkDate>='2008-07-01' AND (M.Estate='0' OR M.Estate='3') $TempqkDatetj",$link_id));
$Sum_A-=sprintf("%.0f",$checkZWYSql["Amount"]);

//非BOM采购费用
$checkKF_YSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.nonbom12_cwsheet M LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency  WHERE (M.Estate='0' OR M.Estate='3')  $TempMonthtj ",$link_id));
$Sum_A-=sprintf("%.0f",$checkKF_YSql["Amount"]);



//////////////////////77777777777777
//H86-中港运费、入仓费:以出货日期计算
$checkFreightY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=0 $TempDeliveryDate
											)A
											",$link_id));
$checkFreightW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=0 $TempDeliveryDate
											 )A
											 ",$link_id));
$Sum_A-=sprintf("%.0f",$checkFreightY["Amount"])+sprintf("%.0f",$checkFreightW["Amount"]);

//H87-货代杂费Forward费用：以出货日期计算
$checkForwardY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM (
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=0 $TempDeliveryDate
											 ) A
											 ",$link_id));
$checkForwardW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=0 $TempDeliveryDate
											 ) A
											 ",$link_id));
$Sum_A-=sprintf("%.0f",$checkForwardY["Amount"]*$HKD_Rate)+sprintf("%.0f",$checkForwardW["Amount"]*$HKD_Rate);
//H88-报关费商检费:以出货日期计算
$checkBgY=mysql_fetch_array(mysql_query("
										SELECT SUM(Amount) AS Amount FROM (
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01'  $TempDatetj
										UNION ALL
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01'  $TempDeliveryDate)A
										",$link_id));
$checkBgW=mysql_fetch_array(mysql_query("
										SELECT SUM(Amount) AS Amount FROM (
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01'  $TempDatetj
										UNION ALL
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01'  $TempDeliveryDate)A
										",$link_id));
$Sum_A-=sprintf("%.0f",$checkBgY["Amount"])+sprintf("%.0f",$checkBgW["Amount"]);

//2008-07-01后结付2008-07-01前的金额，因为桌面现金流水帐有计算，所以损益需要扣除这部分金额
$PreRow = mysql_fetch_array(mysql_query("
SELECT SUM(Amount) AS Amount FROM(
SELECT SUM(S.Amount) AS Amount FROM $DataIn.cwxzmain M,$DataIn.cwxzsheet S WHERE 1 AND M.PayDate>='2008-07-01' AND S.Mid=M.Id AND S.Month<'2008-07'
UNION ALL
SELECT SUM(S.Amount) AS Amount FROM $DataIn.hdjbmain M,$DataIn.hdjbsheet S WHERE 1 AND M.PayDate>='2008-07-01' AND S.Mid=M.Id AND S.Month<'2008-07'
UNION ALL
SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.cwdyfmain M,$DataIn.cwdyfsheet S,$DataPublic.currencydata C WHERE 1 AND M.PayDate>='2008-07-01' AND S.Date<'2008-07-01' AND S.Mid=M.Id AND C.Id=S.Currency
UNION ALL
SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet S LEFT JOIN $DataIn.hzqkmain M ON S.Mid=M.Id LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency WHERE M.PayDate>='2008-07-01' AND S.Date<'2008-07-01'
) A
",$link_id));
$Sum_A-=$PreRow["Amount"]+3000*$USD_Rate;
?>