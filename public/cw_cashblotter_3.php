<?php 
//ewen 2013-01-14 加入体检费用
$i=1;
$TmpWidth=0;
$TmpWidth=4;
//提取汇率
$rateResult = mysql_query("SELECT Rate,Id FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Id=$rateRow["Id"];$TempHZSTR="Rate_".strval($Id);$$TempHZSTR=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
$j=($Page-1)*$Page_Size+1;
echo "<table width='800' id='DataList' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
echo "<tr align='center' bgcolor='#CCCCCC'>";
echo "<td width='60' class='A1111' style='height:25px;'>序号</td>";
echo "<td width='260' class='A1101'>银行</td>";
echo "<td width='130' class='A1101'>RMB结余</td>";
echo "<td width='130' class='A1101'>USD结余</td>";
echo "<td width='130' class='A1101'>HKD结余</td>";
echo "<td width='130' class='A1101'>转RMB小计</td>";
echo "</tr>";
//选定月份前的结余,条件 

$SumJY1=$SumJY2=$SumJY3=$SumJY4=0;
$checkBankSql=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Estate=1 ORDER BY Id",$link_id);//AND cSign='$Login_cSign' 
if($checkBankRow=mysql_fetch_array($checkBankSql)){
	$i=1;
	do{
		$RMBout=$RMBin=$USDout=$USDin=$HKDout=$HKDin=$NTDout=$NTDin="&nbsp;";
		$BankId=$checkBankRow["Id"];
		$BankName=$checkBankRow["Title"];
		$WhereBank=" WHERE 1 AND M.PayDate>='$PreMonthS'  AND M.BankId='$BankId'  GROUP BY M.Id";
		$WhereBank2=" WHERE 1 AND M.Date>='$PreMonthS'  AND M.BankId='$BankId'  GROUP BY M.Id";
		//一、多货币，可使用统一条件的项目
		/*1-BOM货款支出*/		$mySqlP="SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw1_fkoutmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId ".$WhereBank;
		/*2-客户回扣*/				$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw1_tkoutmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId ".$WhereBank;
		/*3-BOM预付订金*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw2_fkdjmain M LEFT JOIN $DataIn.cw2_fkdjsheet A ON A.Mid=M.Id LEFT JOIN $DataIn.trade_object S ON S.CompanyId=A.CompanyId ".$WhereBank;
		
		//ewen 2013-03-28
		/*非BOM货款支出*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.nonbom11_qkmain M LEFT JOIN $DataPublic.nonbom3_retailermain S ON S.CompanyId=M.CompanyId ".$WhereBank;
		
		/*4-供应商税款*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id ".$WhereBank;
		/*5-客户货款*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount-M.Handingfee) AS Amount,S.Currency FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId ".$WhereBank;
		/*6-预收货款*/			$mySqlP.=" UNION ALL SELECT (M.Amount) AS Amount,S.Currency FROM $DataIn.cw6_advancesreceived M LEFT JOIN $DataIn.trade_object S ON M.CompanyId=S.CompanyId ".$WhereBank;
		/*7-开发费用*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id  ".$WhereBank;
		/*8-行政费用*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id ".$WhereBank;

		/*车辆费用*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.carfeemain  M LEFT JOIN $DataIn.carfee S ON S.Mid=M.Id ".$WhereBank;


		/*33-员工离职补助*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.staff_outsubsidymain M LEFT JOIN $DataIn.staff_outsubsidysheet S ON S.Mid=M.Id ".$WhereBank;

		/*34-其它奖金*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,S.Currency FROM $DataIn.cw20_bonusmain  M LEFT JOIN $DataIn.cw20_bonussheet S ON S.Mid=M.Id ".$WhereBank;

//二、多货币，条件需单独处理的项目
		/*9-其它收入*/				$mySqlP.=" UNION ALL SELECT M.PayAmount,S.Currency FROM $DataIn.cw4_otherinmain M  LEFT JOIN $DataIn.cw4_otherinsheet S ON S.Mid=M.Id".$WhereBank;
		/*10-汇兑转入*/			$mySqlP.=" UNION ALL SELECT (M.InAmount) AS Amount,M.InCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 AND M.InBankId='$BankId'  AND M.PayDate>='$PreMonthS'  GROUP BY M.Id";
		/*11-汇兑转出*/			$mySqlP.=" UNION ALL SELECT (M.OutAmount*-1) AS Amount,M.OutCurrency AS Currency FROM $DataIn.cw5_fbdh M WHERE 1 AND M.OutBankId='$BankId'  AND M.PayDate>='$PreMonthS'  GROUP BY M.Id";
		/*12扣供应商货款*/	$mySqlP.=" UNION ALL SELECT (T.Amount) AS Amount,S.Currency FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId WHERE 1 AND M.Date>='$PreMonthS' AND M.BankId='$BankId' AND T.Kid!=0";//扣供应商款

		/*12供应商货款返利*/	$mySqlP.=" UNION ALL SELECT (T.Amount) AS Amount,S.Currency 
          FROM $DataIn.cw2_hksheet T 
          LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
          LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId WHERE 1 AND M.Date>='$PreMonthS' AND M.BankId='$BankId' AND T.Did!=0";//货款返利

//三、HKD项目，可使用统一条件的项目
		/*13入仓费*/ 			$mySqlP.=" UNION ALL SELECT (M.depotCharge)*-1 AS Amount,'3' AS Currency FROM $DataIn.cw4_freight_declaration M LEFT JOIN $DataIn.ch4_freight_declaration S ON M.Id=S.Mid ".$WhereBank;
		/*14-Forward杂费*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'3' AS Currency FROM $DataIn.cw3_forward M LEFT JOIN $DataIn.ch3_forward S ON M.Id=S.Mid ".$WhereBank;
//四、RMB项目，可使用统一条件的项目
		/*5－员工薪资*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cwxzmain M ".$WhereBank;
		///*16－试用工薪资*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cwxztempmain M ".$WhereBank;
		/*17－员工借支*/		$mySqlP.=" UNION ALL SELECT (M.Amount*-1) AS Amount,'1' AS Currency FROM $DataIn.cwygjz M ".$WhereBank;
		/*18－社保缴费*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.sbpaymain M ".$WhereBank;
		/*19－假日加班费*/	$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.hdjbmain M ".$WhereBank;
		/*20－快递费*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw9_expsheet M LEFT JOIN $DataIn.ch9_expsheet S ON M.Id=S.Mid ".$WhereBank;
		/*21－寄样费*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw10_samplemail M LEFT JOIN $DataIn.ch10_samplemail S ON M.Id=S.Mid ".$WhereBank;
		/*22－节日奖金*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw11_jjmain M ".$WhereBank;
		/*23－总务费用*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.zw3_purchasem M ".$WhereBank;
		/*24中港运费/报关/商检费用*/ $mySqlP.=" UNION ALL SELECT (M.PayAmount+M.declarationCharge+M.checkCharge)*-1 AS Amount,'1' AS Currency FROM $DataIn.cw4_freight_declaration M LEFT JOIN $DataIn.ch4_freight_declaration S ON M.Id=S.Mid ".$WhereBank;
		/*26－体检费用*/		$mySqlP.=" UNION ALL SELECT (M.PayAmount*-1) AS Amount,'1' AS Currency FROM $DataIn.cw17_tjmain M ".$WhereBank;
//五、RMB项目，条件需单独处理的项目
		/*25－退税收益*/		$mySqlP.=" UNION ALL SELECT (M.Taxamount)  AS Amount,'1' AS Currency FROM $DataIn.cw14_mdtaxmain M ".$WhereBank;
		/*助学费用*/   $mySqlP.=" UNION ALL SELECT (M.PayAmount*-1)  AS Amount,'1' AS Currency FROM $DataIn.cw19_studyfeemain M ".$WhereBank;
		/*模具费退回*/	 $mySqlP.=" UNION ALL SELECT  IFNULL(M.OutAmount,0)  AS Amount,'1' AS Currency FROM $DataIn.cw16_modelfee M ".$WhereBank2;



	$JY1=$JY2=$JY3=$JY4="&nbsp;";	$ToRMB=0;
	//echo $mySqlP;
	$checkPreSumSql=mysql_query("SELECT IFNULL(SUM(Amount),0) AS Amount,Currency FROM ( ".$mySqlP.") A WHERE 1 GROUP BY Currency",$link_id);
	if($checkPreSumRow=mysql_fetch_array($checkPreSumSql)){
		do{
			$PreAmount=sprintf("%.2f",$checkPreSumRow["Amount"]);
			$PreCurrency=$checkPreSumRow["Currency"];
			$TempEstateSTR="JY".strval($PreCurrency); 
			$$TempEstateSTR=$PreAmount;
			$TempSTR="SumJY".strval($PreCurrency);
			$$TempSTR+=$PreAmount;
			$HBTemp="Rate_".strval($PreCurrency);
			$ToRMB+=$PreAmount*$$HBTemp;
			}while ($checkPreSumRow=mysql_fetch_array($checkPreSumSql));
		}
    /* if($JY1=="&nbsp;")$JY1=0;
     if($JY2=="&nbsp;")$JY3=0;
     if($JY3=="&nbsp;")$JY3=0;*/
     //if($ToRMB=="")$ToRMB=0;
       $JY1= (float)$JY1;
       $JY2= (float)$JY2;
       $JY3= (float)$JY3;
       $ToRMB= (float)$ToRMB;
		echo "<tr align='right'><td class='A0111' height='25' align='center'>$i</td><td class='A0101'>$BankId $BankName</td><td class='A0101'>".number_format($JY1,2)."</td><td class='A0101' bgcolor='#CCFFCC'>".number_format($JY2,2)."</td><td class='A0101'>".number_format($JY3,2)."</td><td class='A0101' bgcolor='#CCFFCC'>".number_format($ToRMB,2)."</td></tr>";
	$i++;
	}while($checkBankRow=mysql_fetch_array($checkBankSql));
}
//全部合计
$AmountTotal=$SumJY1+$SumJY2*$Rate_2+$SumJY3*$Rate_3+$SumJY4*$Rate_4;
$SumJY1=sprintf("%.2f",$SumJY1);
$SumJY2=sprintf("%.2f",$SumJY2);
$SumJY3=sprintf("%.2f",$SumJY3);
$SumJY4=sprintf("%.2f",$SumJY4);
echo "<tr align='right'>";
echo "<td class='A0111' colspan='2' height='25'  bgcolor='#CCCCCC' align='center'>结余</td>";
echo "<td class='A0101'>".number_format($SumJY1)."</td>";
echo "<td class='A0101' bgcolor='#CCFFCC'>".number_format($SumJY2)."</td>";
echo "<td class='A0101'>".number_format($SumJY3)."</td>";
echo "<td class='A0101' bgcolor='#CCFFCC'>".number_format($AmountTotal)."</td>";
echo "</tr>";
echo"</tabe>";
?>