<?php 
//电信
//代码共享-EWEN 2012-08-19
//1 进项
$Sum_Y=$Sum_W=$Sum_A=0;
//########### 1、货款(inovice)
//:已结付正常出货货款=全部已结付款+部分结付款：以出货日期计算
//:已结付正常出货货款=全部已结付款+部分结付款：以出货日期计算
$HKIN_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount  
														 FROM $DataIn.cw6_orderinsheet S 
														 LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId 
														 LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
														 LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
														 WHERE M.Estate=0 AND (M.cwSign=0 OR M.cwSign=2)  AND M.Sign=1 AND M.ShipType!='debit'  AND M.Date>='2008-07-01' $TempDatetj",$link_id));
//全部正常出货款：以出货日期计算
$HKIN_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount 
														 FROM $DataIn.ch1_shipsheet S 
														 LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
														 LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
														 LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
														 WHERE 1 AND M.Estate=0 AND M.Sign=1 AND M.ShipType!='debit'  $TempDatetj",$link_id));

//订金
$checkSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*.C.Rate),0) AS Amount FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D,$DataPublic.currencydata C WHERE M.CompanyId=D.CompanyId AND D.Currency=C.Id  AND M.Mid>0  $TempPayDatetj",$link_id));
$DJ_YD=sprintf("%.0f",$checkSql["Amount"]);//已抵货款的定金
$checkSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*.C.Rate),0) AS Amount FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D,$DataPublic.currencydata C WHERE M.CompanyId=D.CompanyId AND D.Currency=C.Id   $TempPayDatetj",$link_id));
$DJ_YS=sprintf("%.0f",$checkSql["Amount"]);//预收定金总额
$DJ_WD=$DJ_YS-$DJ_YD;								//未抵定金＝已收定金－已抵货款定金

//1 货款
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$HKIN_Y["Amount"])-$DJ_YD;//=正常出货货款-预收已抵
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$HKIN_A["Amount"])-$DJ_YS;//全部正常出货款－预收货款
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$HKIN_A["Amount"])-sprintf("%.0f",$HKIN_Y["Amount"])-$DJ_WD;//货款部分2-3:正常出货未收款－预收未抵

//2 其他销货金额(含debit note)
$HKinDebit_Y=mysql_fetch_array(mysql_query("
	SELECT SUM(Amount) AS Amount FROM (
		SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount FROM $DataIn.cw6_orderinsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND M.cwSign=0 AND M.Sign=1 AND M.ShipType='debit' $TempDatetj 
		UNION ALL
		SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount FROM $DataIn.cw6_orderinsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND M.cwSign=2 AND M.Sign=1 AND M.ShipType='debit'  $TempDatetj 
	)A
	",$link_id));
$HKinDebit_A=mysql_fetch_array(mysql_query("
	SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 AND M.Estate=0 AND M.Sign=1 AND M.ShipType='debit'  $TempDatetj  ",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$HKinDebit_Y["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$HKinDebit_A["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$HKinDebit_A["Amount"])-sprintf("%.0f",$HKinDebit_Y["Amount"]);

//$Sql011=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*.C.Rate),0) AS Amount FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D,$DataPublic.currencydata C WHERE M.Mid=0 AND M.CompanyId=D.CompanyId AND D.Currency=C.Id AND M.PayDate>'2008-07-01'  $TempPayDatetj",$link_id));


/*
//3、已抵货款的客户定金和未抵付的客户定金
$Sum_Y+=$Value_Y[$Subscript][]=-$DJ_YD;			//已抵付定金：
$Sum_W+=$Value_W[$Subscript][]=-$DJ_WD;		//未抵付定金（实收＋手续费）
$Sum_A+=$Value_A[$Subscript][]=-$DJ_YS;		//定金总额（实收＋手续费）
*/

//########## 2
//4、预收定金总额
$Sum_Y+=$Value_Y[$Subscript][]=$DJ_YS;	//预收定金：已收总额
$Sum_W+=$Value_W[$Subscript][]=0;			//
$Sum_A+=$Value_A[$Subscript][]=$DJ_YS;	//预收定金：已收总额
//######### 3/4

//5 扣供应商货款
//未结付扣供应商货款
$checkGysTK_W=mysql_fetch_array(mysql_query("
											SELECT IFNULL(SUM(T.Amount*C.Rate),0) AS Amount 
			   FROM $DataIn.cw15_gyskksheet T
			   LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id 
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
			  WHERE 1 AND M.Estate=0 AND T.Kid=0 $TempDatetj",$link_id));
//已结付扣供应商货款
$checkGysTK_Y=mysql_fetch_array(mysql_query("
			    SELECT IFNULL(SUM(T.Amount*C.Rate),0) AS Amount 
			   FROM $DataIn.cw15_gyskksheet T
			   LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id 
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
			  WHERE 1 AND M.Estate=0 AND T.Kid>0 $TempDatetj",$link_id));


$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkGysTK_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkGysTK_W["Amount"]);							
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkGysTK_Y["Amount"])+sprintf("%.0f",$checkGysTK_W["Amount"]);


//***************供应商货款返利/*add by yang 2014-02-18
//未结付货款返利
$Return_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount 
			   FROM $DataIn.cw2_hksheet M
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
			  WHERE 1 AND M.Estate=3 AND M.Did=0 $TempDatetj",$link_id));
//已结付货款返利
$Return_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount 
			   FROM $DataIn.cw2_hksheet M
			   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			   LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
			  WHERE 1 AND M.Estate=0 AND M.Did>0 $TempDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$Return_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$Return_W["Amount"]);							
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$Return_Y["Amount"])+sprintf("%.0f",$Return_W["Amount"]);


//6	免抵退税收益
$checkMdtaxY=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Taxamount),0) AS Amount FROM $DataIn.cw14_mdtaxmain M WHERE M.Estate=0 AND M.TaxDate>='2008-07-01'  $TempDateTax",$link_id));
$checkMdtaxW=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Taxamount),0) AS Amount FROM $DataIn.cw14_mdtaxmain M WHERE M.Estate=3 AND M.TaxDate>='2008-07-01'  $TempDateTax",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkMdtaxY["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkMdtaxW["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkMdtaxY["Amount"])+sprintf("%.0f",$checkMdtaxW["Amount"]);

//7	汇兑损益+HZ642：以结付日期计算，没有未结付状态

$checkHDSY_Y=mysql_fetch_array(mysql_query("SELECT SUM(Amount) AS Amount FROM (
	SELECT IFNULL(SUM(-M.OutAmount*C.Rate),0) AS Amount FROM $DataIn.cw5_fbdh M,$DataPublic.currencydata C WHERE 1 and M.OutCurrency=C.Id AND M.PayDate>'2008-07-01' $TempPayDatetj
	UNION ALL 
	SELECT IFNULL(SUM(M.InAmount*C.Rate),0) AS Amount FROM $DataIn.cw5_fbdh M,$DataPublic.currencydata C WHERE 1 and M.InCurrency=C.Id AND M.PayDate>'2008-07-01' $TempPayDatetj
	)A",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=$checkHDSY_Y["Amount"]-$HZ642_Y;
$Sum_W+=$Value_W[$Subscript][]=-$HZ642_W;
$Sum_A+=$Value_A[$Subscript][]=$checkHDSY_Y["Amount"]-$HZ642_A;



//已结付代收货款
/*$DS_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Qty*M.Price*C.Rate),0) AS Amount FROM $DataIn.ch5_sampsheet M LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.TypeId=1 AND M.Date>='2008-07-01' $TempDatetj",$link_id));
$DFHK=0;//sprintf("%.0f",$DS_Y["Amount"]);*/
//A3-A15其他收入
//8	模具费退回
$checkModelfY=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.OutAmount),0) AS Amount FROM $DataIn.cw16_modelfee M WHERE M.Estate=0  $TempDateModelf",$link_id));
$checkModelfW=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.OutAmount),0) AS Amount FROM $DataIn.cw16_modelfee M WHERE M.Estate=3  $TempDateModelf",$link_id));
$modelfY=sprintf("%.0f",$checkModelfY["Amount"]);
$modelfW=sprintf("%.0f",$checkModelfW["Amount"]);

$checkOT_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw4_otherinsheet  M  ,$DataPublic.currencydata C
WHERE 1 and M.Currency=C.Id AND M.Estate='0'  $TempPayDatetj",$link_id));
$checkOT_W=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw4_otherinsheet  M,$DataPublic.currencydata C 
WHERE 1 and M.Currency=C.Id AND M.Estate='3'  $TempPayDatetj",$link_id));
$QTSR_Y=sprintf("%.0f",$checkOT_Y["Amount"]);
$QTSR_W=sprintf("%.0f",$checkOT_W["Amount"]);

$Sum_Y+=$Value_Y[$Subscript][]=$QTSR_Y+$modelfY;			
$Sum_W+=$Value_W[$Subscript][]=$QTSR_W+$modelfW;			
$Sum_A+=$Value_A[$Subscript][]=$QTSR_Y+$QTSR_W+$modelfY+$modelfW;	

$SumType_Y[$Subscript][]=$Sum_Y; 
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;

$SumCol_Y[$Subscript]+=$Sum_Y; 
$SumCol_W[$Subscript]+=$Sum_W; 
$SumCol_A[$Subscript]+=$Sum_A; 
?>