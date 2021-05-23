<?php 
//ewen 2013-01-14 加入体检费用;2013-09-04加入明细连接，退税收益数据表与皮套不一样
$PayDateSTR="AND M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE'";//之前
$PayDateSTRNow=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth'";//当月
//一、多货币，可使用统一条件的项目

/*1-货款支出OK*/			$mySqlP.="SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTR";
$mySqlN.="SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'1' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow";

//********同一笔支出抵多笔结付记录，以备注和日期为索引
/*2-客户回扣OK*/			$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw1_tkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTR";
$mySqlN.=" UNION ALL SELECT '0' AS Id,'0' AS Payee,SUM(M.PayAmount) AS Amount,M.PayDate AS PayDate,'2' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw1_tkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow GROUP BY M.Remark,M.PayDate";

/*3预付订金*/				$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P WHERE S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency='$Currency' $PayDateSTR  GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'3' AS Item, 'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P WHERE S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency='$Currency' $PayDateSTRNow  GROUP BY M.Id";									

/*4非BOM货款支出OK*/			$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.nonbom11_qkmain M,$DataPublic.nonbom3_retailermain C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTR";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'4' AS Item,'O' AS Sign,M.Remark FROM $DataIn.nonbom11_qkmain M,$DataPublic.nonbom3_retailermain C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow";

/*6-供应商税款OK*/		$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'6' AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
									
//*******同一笔入帐抵多笔结付记录，以备注和日期为索引
/*7-客户货款OK*/			$mySqlP.=" UNION ALL SELECT (M.PayAmount-M.Handingfee) AS Amount,'1' AS Sign FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId WHERE 1 AND C.Currency='$Currency' $PayDateSTR";
$mySqlN.=" UNION ALL SELECT '0' AS Id,'0' AS Payee,SUM(M.PayAmount-M.Handingfee) AS Amount,M.PayDate AS PayDate,'7' AS Item,'I' AS Sign,M.Remark FROM $DataIn.cw6_orderinmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow GROUP BY M.Remark,M.PayDate";// 

/*8-预收货款OK*/			$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'1' AS Sign FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D WHERE M.CompanyId=D.CompanyId AND D.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Attached AS Payee,M.Amount AS Amount,M.PayDate AS PayDate,'8' AS Item,'I' AS Sign,M.Remark FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D WHERE M.CompanyId=D.CompanyId AND D.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

/*9-开发费用OK*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id  WHERE 1 AND S.Currency='$Currency' $PayDateSTR";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'9' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id WHERE 1 AND S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

/*10-行政费用OK*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'10' AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";


/*15－员工薪资OK  ewen 2014-06-11*/	
$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.cwxzmain M LEFT JOIN $DataIn.cwxzsheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'15' AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.cwxzmain M LEFT JOIN $DataIn.cwxzsheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

//二、多货币，条件需单独处理的项目
/*11-其它收款OK*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'1' AS Sign  FROM $DataIn.cw4_otherinmain  M LEFT JOIN $DataIn.cw4_otherinsheet  S ON S.Mid=M.Id WHERE 1  AND  S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL  SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'11' AS Item,'I' AS Sign,M.Remark AS Remark FROM $DataIn.cw4_otherinmain  M  LEFT JOIN $DataIn.cw4_otherinsheet  S ON S.Mid=M.Id WHERE 1  AND  S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

/*12-汇兑转入OK*/		$mySqlP.=" UNION ALL SELECT M.InAmount AS Amount,'1' AS Sign FROM $DataIn.cw5_fbdh M WHERE 1 and M.InCurrency='$Currency' $PayDateSTR";
$mySqlN.=" UNION ALL SELECT M.Id,M.Bill AS Payee,M.InAmount AS Amount,M.PayDate AS PayDate,'12' AS Item,'I' AS Sign,M.Remark AS Remark FROM $DataIn.cw5_fbdh M WHERE 1 and M.InCurrency='$Currency' $PayDateSTRNow";

/*13-汇兑转出OK*/		$mySqlP.=" UNION ALL SELECT M.OutAmount AS Amount,'-1' AS Sign FROM $DataIn.cw5_fbdh M WHERE 1 and M.OutCurrency='$Currency' $PayDateSTR";
$mySqlN.=" UNION ALL SELECT M.Id,M.Bill AS Payee,M.OutAmount AS Amount,M.PayDate AS PayDate,'13' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw5_fbdh M WHERE 1 and M.OutCurrency='$Currency' $PayDateSTRNow";

/*14扣供应商货款*/		$mySqlP.=" UNION ALL SELECT SUM(T.Amount) AS Amount,'1' AS Sign FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.cw15_gyskkmain G ON G.Id=T.Mid LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND T.Kid!=0 AND P.Currency='$Currency' GROUP BY T.Kid";//扣供应商款
//?????????
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(T.Amount) AS Amount,M.PayDate,'14' AS Item,'I' AS Sign,M.Remark FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND T.Kid!=0 AND P.Currency='$Currency' GROUP BY T.Kid";//供应商扣款

/*14货款返利*/		$mySqlP.=" UNION ALL SELECT SUM(T.Amount) AS Amount,'1' AS Sign 
        FROM $DataIn.cw2_hksheet T 
        LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
        WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND T.Did!=0 AND P.Currency='$Currency' GROUP BY T.Did";//货款返利

$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(T.Amount) AS Amount,M.PayDate,'30' AS Item,'I' AS Sign,M.Remark 
       FROM $DataIn.cw2_hksheet T 
       LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Did=M.Id 
       LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
       WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND T.Did!=0 AND P.Currency='$Currency' GROUP BY T.Did";//货款返利


/*32-车辆费用OK*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.carfeemain M LEFT JOIN $DataIn.carfee S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'32' AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.carfeemain M LEFT JOIN $DataIn.carfee S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

/*33-员工离职补助*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.staff_outsubsidymain M LEFT JOIN $DataIn.staff_outsubsidysheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'33' AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.staff_outsubsidymain M LEFT JOIN $DataIn.staff_outsubsidysheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

/*34-其它奖金OK*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.cw20_bonusmain M LEFT JOIN $DataIn.cw20_bonussheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,SUM(S.Amount) AS Amount,M.PayDate AS PayDate,'34' AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.cw20_bonusmain M LEFT JOIN $DataIn.cw20_bonussheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";


switch($Currency){
	case 1:	
		//四、RMB项目，可使用统一条件的项目
		/*15－员工薪资OK  改为多货币
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cwxzmain M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'15' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwxzmain M WHERE 1 $PayDateSTRNow";
			*/	
		/*16－试用工薪资OK*/
		/*$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cwxztempmain M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'16' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwxztempmain M WHERE 1 $PayDateSTRNow";
		*/

		/*17－员工借支OK*/	
		$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'-1' AS Sign FROM $DataIn.cwygjz M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.Amount AS Amount,M.PayDate AS PayDate,'17' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwygjz M WHERE 1 $PayDateSTRNow";
	
		/*18－社保缴费OK*/	
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.sbpaymain M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'18' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.sbpaymain M WHERE 1 $PayDateSTRNow";

		/*19－假日加班费OK*/
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.hdjbmain M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'19' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.hdjbmain M WHERE 1 $PayDateSTRNow";

		/*20－快递费OK*/		
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'20' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $PayDateSTRNow GROUP BY M.Id";
		
		/*21－寄样费OK*/		
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'21' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $PayDateSTRNow GROUP BY M.Id";

		/*22－节日奖金OK*/	
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw11_jjmain M WHERE 1 $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'22' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw11_jjmain M WHERE 1 $PayDateSTRNow";

		/*23－总务费用OK*/	
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.zw3_purchasem M WHERE 1 $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'23' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.zw3_purchasem M WHERE 1 $PayDateSTRNow";
		
		/*24中港运费/报关/商检费用*/ 
		$mySqlP.=" UNION ALL SELECT (M.PayAmount+M.declarationCharge+M.checkCharge) AS Amount,'-1' AS Sign FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId AND C.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,(M.PayAmount+M.declarationCharge+M.checkCharge) AS Amount,M.PayDate AS PayDate,'24' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId AND C.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";

		/*25－退税收益OK, 按结付日期来显示*/	
		$mySqlP.=" UNION ALL SELECT SUM(M.Taxamount)  AS Amount,'1' AS Sign  FROM $DataIn.cw14_mdtaxmain M WHERE 1 and M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE' and M.Estate='0'";		//M.Taxdate>='$PreMonthS' AND M.PayDate<'$PreMonthE'
		$mySqlN.=" UNION ALL SELECT M.Id,M.Attached AS Payee,ifnull(M.Taxamount,0) AS Amount,M.PayDate AS PayDate,'25' AS Item,'I' AS Sign,M.Remark AS Remark   FROM $DataIn.cw14_mdtaxmain  M WHERE 1  and DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth' and M.Estate='0'";

		/* 26模具退回费用*/		
		$mySqlP.=" UNION ALL SELECT SUM(M.OutAmount)  AS Amount,'1' AS Sign  FROM $DataIn.cw16_modelfee  M WHERE 1 and M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' and M.Estate='0'";	
		$mySqlN.=" UNION ALL SELECT M.Id,M.Bill AS Payee,ifnull(M.OutAmount,0) AS Amount,M.Date AS PayDate,'26' AS Item,'I' AS Sign,M.ItemName AS Remark   FROM $DataIn.cw16_modelfee  M WHERE DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' and M.Estate='0'";
		
		/*27体检费用*/				
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign  FROM $DataIn.cw17_tjmain  M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'27' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw17_tjmain M WHERE 1 $PayDateSTRNow";

		//*31助学费用*/				
		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign  FROM $DataIn.cw19_studyfeemain  M WHERE 1 $PayDateSTR";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'31' AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw19_studyfeemain M WHERE 1 $PayDateSTRNow";


		break;
	case 3:
		//三、HKD项目，可使用统一条件的项目
		/*28入仓费*/ 				$mySqlP.=" UNION ALL SELECT M.depotCharge AS Amount,'-1' AS Sign FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.depotCharge AS Amount,M.PayDate AS PayDate,'28' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw4_freight_declaration M WHERE 1 AND M.depotCharge>0 $PayDateSTRNow";
		
		/*29-Forward杂费OK*/	$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw3_forward M,$DataIn.ch3_forward S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
		$mySqlN.=" UNION ALL SELECT M.Id,M.Payee,M.PayAmount AS Amount,M.PayDate AS PayDate,'29' AS Item,'O' AS Sign,M.Remark FROM $DataIn.cw3_forward M,$DataIn.ch3_forward S WHERE M.Id=S.Mid $PayDateSTRNow GROUP BY M.Id";
		break;
  
	}

$mySqlP="SELECT TRUNCATE(SUM(Amount*Sign),2) AS SUM_RMB FROM ( ".$mySqlP.") A";
//echo $mySqlP."<br>";
$PreSql=mysql_fetch_array(mysql_query($mySqlP,$link_id));
$PreAmount=$PreSql["SUM_RMB"];
echo"
<table width='1200' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
<tr align='center' bgcolor='#CCCCCC'>
<td width='40' class='A1111' style='height:25px;'>序号</td>
<td width='80' class='A1101'>日期</td>
<td width='130' class='A1101'>费用类别</td>
<td class='A1101'>备注</td>
<td width='100' class='A1101'>进帐金额</td>
<td width='100' class='A1101'>出帐金额</td>
<td width='100' class='A1101'>结余金额</td></tr>
<tr><td class='A0111' height='25'>&nbsp;</td><td class='A0101'>&nbsp;</td><td class='A0101'>&nbsp;</td><td class='A0101'>上次结余</td><td class='A0101'>&nbsp;</td><td class='A0101'>&nbsp;</td><td class='A0101' align='right'>".number_format($PreAmount,2)."</td></tr>";
$ChooseOut="N";
$mySqlN.=" ORDER BY PayDate,Sign";
//echo $mySqlN."<br>";
$myResult = mysql_query($mySqlN,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	$ItemSTR=",BOM采购货款支付,客户退款支出,BOM采购预付订金,非BOM采购货款支付,非BOM采购预付订金,BOM供应商税款,客户货款收入,预收客户货款,开发费用,行政费用,其它收入,汇兑转入,汇兑转出,BOM供应商扣款,员工薪资,试用工薪资,员工借支,社保缴费,假日加班费,快递费,寄样费,节日奖金,总务采购费用,中港运费/报关/商检费用,退税金额,模具退回费用,体检费用,入仓费,Forward杂费,货款返利,助学费用,车辆费用支出,员工离职补助,其它奖金";
	$ItemArray=explode(",",$ItemSTR);
	do{
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$PayDate=$myRow["PayDate"];
		$Id=$myRow["Id"];
		$Payee=$myRow["Payee"];
		$Item=$myRow["Item"];
		$ItemName=$ItemArray[$Item];
		$Remark=$myRow["Remark"];
		$Sign=$myRow["Sign"];
		if($Id==0){
			$TempValue=$Item."~".$PayDate."~".$Remark;
			}
		else{
			$TempValue=$Item."~".$PayDate."~".$Id;
			}
		switch($Sign){
			case "I":
				$outAmount="&nbsp;";
				$PreAmount=sprintf("%.2f",$PreAmount+$Amount);
				$inAmount="<div style='CURSOR: pointer;color:#FF6633' onClick=ShowSheet($i)><input id='Remark$i' name='Remark$i' type='hidden' value='$TempValue'/>".$Amount."</div>";
				break;
			case "O":
				$inAmount="&nbsp;";
				$PreAmount=sprintf("%.2f",$PreAmount-$Amount);
				$outAmount="<div style='CURSOR: pointer;color:#FF6633' onClick=ShowSheet($i)><input id='Remark$i' name='Remark$i' type='hidden' value='$TempValue'/>".$Amount."</div>";
				break;
			}
		echo"<tr align='right'><td class='A0111' align='center' height='25'>$i</td><td class='A0101' align='center'>$PayDate</td><td class='A0101' align='left'>$ItemName</td><td class='A0101' align='left'>$Remark</td><td class='A0101'>$inAmount</td><td class='A0101'>$outAmount</td><td class='A0101'>".number_format($PreAmount,2)."</td></tr>";
		echo"<tr id='TrShow$i' style='display:none;background:#CFC;'><td colspan='7' style=\"height:300px\" valign=\"top\" class='A0111'><br><div id='DivShow$i' style='display:none;'></div><br></td></tr>";//隐藏的行
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr class='right'><td class='A0111' align='center' height='25' >&nbsp;</td><td class='A0101' align='center'>&nbsp;</td><td class='A0101' align='left'>&nbsp;</td><td class='A0101' align='left'>本月没有资料</td><td class='A0101'>&nbsp;</td><td class='A0101'>&nbsp;</td><td class='A0101'>&nbsp;</td></tr>";
  	}
echo "</table>";
?>