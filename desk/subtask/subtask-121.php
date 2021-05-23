<?php   
//ewen-2012-12-31

$Terms="WHERE 1 AND M.Estate=3"; //取数据条件：处于审核通过但未结付的状态
//$Result="SELECT SUM(M.Amount) AS Amount FROM $DataIn.cwxztempsheet M $Terms";						//试用期薪资
$Result ="SELECT  SUM(M.Amount) AS Amount FROM $DataIn.hdjbsheet M $Terms";				//假日加班费
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cwxzsheet M  LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";				//员工薪资：加入支付货币
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";				//开发费用
$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw11_jjsheet M $Terms";			//节日奖金
$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch9_expsheet M $Terms";			//快递费用
$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch10_samplemail M $Terms";			//寄样费用
$Result.=" UNION ALL SELECT SUM(M.Qty*M.Price) AS Amount FROM $DataIn.zw3_purchases M $Terms";		//总务采购费用
$Result.=" UNION ALL SELECT SUM(M.mAmount+M.cAmount) AS Amount FROM $DataIn.sbpaysheet M $Terms";	//社保费用+住房公积金
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.ch3_forward M,$DataPublic.currencydata C $Terms AND C.Id=3";							//Forward费用
$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw17_tjsheet M $Terms";//体检费用
$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw19_studyfeesheet  M $Terms";//助学费用
$Result.=" UNION ALL SELECT SUM(M.Amount+M.declarationCharge+M.checkCharge+M.depotCharge+M.carryCharge+M.xyCharge+M.wfqgCharge+M.ccCharge+M.djCharge+M.stopcarCharge+M.expressCharge+M.otherCharge) AS Amount FROM $DataIn.ch4_freight_declaration M  $Terms ";		//中港运费、报关、商检费用
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate*-1) AS Amount FROM $DataIn.cw4_otherinsheet  M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Estate=3";	//其它收入
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//行政费用
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.carfee M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//车辆费用
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.staff_outsubsidysheet  M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//员工离职补助
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw20_bonussheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//其它奖金
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//供应商税款
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw2_fkdjsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms";						//未结付订金
//BOM供应商货款
$Result.=" UNION ALL SELECT SUM((M.AddQty+M.FactualQty)*M.Price*C.Rate) AS Amount FROM $DataIn.cw1_fkoutsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms";
//非BOM供应商货款 ewen 2013-11-21 请款数据表名称改变，并且取消原订金功能和数据表 OK
$Result.=" UNION ALL SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.nonbom11_qksheet M LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms";
//客户扣款
$Result.=" UNION ALL SELECT SUM((M.OrderQty)*M.Price*C.Rate) AS Amount FROM $DataIn.cw1_tkoutsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms";

//扣供应商款
$Result.=" UNION ALL SELECT IFNULL(SUM(T.Amount*C.Rate*-1),0) AS Amount  FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1 AND M.Estate=0 AND T.Kid=0";
//模具退回费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.OutAmount*-1),0) AS Amount  FROM $DataIn.cw16_modelfee M  $Terms ";
//未抵付订金
$Result.=" UNION ALL SELECT SUM(-S.Amount*C.Rate) AS Amount
           FROM $DataIn.cw2_fkdjsheet S 
           LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
           LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1  AND S.Did=0 AND S.Estate IN(0,3)";

$Result.=" UNION ALL SELECT SUM(-M.Taxamount ) AS Amount FROM $DataIn.cw14_mdtaxmain M WHERE 1 AND M.Estate=3";//未结付退税

  $Result.=" UNION ALL 
				SELECT IFNULL(SUM(M.Amount*D.Rate),0)*-1 AS Amount 
                 FROM $DataIn.cw2_hksheet M 
                 LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId 
                 LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency WHERE  M.Estate=3 AND M.Did=0 ";//供应商货款返利   

//if ($Login_P_Number==10868) echo "$Result";
$noPaySql = mysql_query("SELECT SUM(IFNULL(Amount,0)) AS sumAmount FROM ($Result) A",$link_id);
	if($noPaySql && $noPayRow = mysql_fetch_array($noPaySql)) {
		$noPayRMB=sprintf("%.2f",$noPayRow["sumAmount"]);
		}

$contentSTR="<li class=TitleBL>未付</li><li class=TitleBR><a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>¥".number_format($noPayRMB)."</a></li>";
//$contentSTR.="<li class=DataA><a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>¥".number_format($noPayRMB)."</a></li>";
?>