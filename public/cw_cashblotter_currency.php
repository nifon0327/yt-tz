<?php 
//电信-zxq 2012-08-01
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$PayDateSTR=" and M.PayDate>='$PreMonthS' AND M.PayDate<'$PreMonthE'";//选定月份前的结余,条件 
$PayDateSTRNow=" and DATE_FORMAT(M.PayDate,'%Y-%m')='$ChooseMonth'";//选定月份的项目,条件 
/*1-其它收入*/			$mySqlP.="SELECT M.Amount AS Amount,'1' AS Sign FROM $DataIn.cw4_otherin M WHERE 1 AND M.Estate='0' AND M.Currency='$Currency' $PayDateSTR";
									$mySqlN.=" SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('其它收入') AS Item,'I' AS Sign,M.Remark AS Remark FROM $DataIn.cw4_otherin M WHERE 1  AND M.Estate='0' AND M.Currency='$Currency' $PayDateSTRNow";
/*2-汇兑转入*/			$mySqlP.=" UNION ALL SELECT M.InAmount AS Amount,'1' AS Sign FROM $DataIn.cw5_fbdh M WHERE 1 and M.InCurrency='$Currency' $PayDateSTR";
									$mySqlN.=" UNION ALL SELECT M.InAmount AS Amount,M.PayDate AS PayDate,concat('转入') AS Item,'I' AS Sign,M.Remark AS Remark FROM $DataIn.cw5_fbdh M WHERE 1 and M.InCurrency='$Currency' $PayDateSTRNow";
/*3-汇兑转出*/			$mySqlP.=" UNION ALL SELECT M.OutAmount AS Amount,'-1' AS Sign FROM $DataIn.cw5_fbdh M WHERE 1 and M.OutCurrency='$Currency' $PayDateSTR";
									$mySqlN.=" UNION ALL SELECT M.OutAmount AS Amount,M.PayDate AS PayDate,concat('转出') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw5_fbdh M WHERE 1 and M.OutCurrency='$Currency' $PayDateSTRNow";
/*4-开发费用*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id  WHERE 1 AND S.Currency='$Currency' $PayDateSTR";
									$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,concat('开发费用') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwdyfmain M LEFT JOIN $DataIn.cwdyfsheet S ON S.Mid=M.Id WHERE 1 AND S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
/*5-行政费用*/			$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
									$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('行政费用') AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.hzqkmain M LEFT JOIN $DataIn.hzqksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
/*6-供应商税款*/		$mySqlP.=" UNION ALL SELECT SUM(S.Amount) AS Amount,'-1' AS Sign FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
									$mySqlN.=" UNION ALL SELECT SUM(S.Amount) AS Amount,M.PayDate AS PayDate,	concat('供应商税款') AS Item,	'O' AS Sign,	M.Remark AS Remark FROM $DataIn.cw2_gysskmain M LEFT JOIN $DataIn.cw2_gyssksheet S ON S.Mid=M.Id	WHERE 1 and S.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
/*7-客户货款*/			$mySqlP.=" UNION ALL SELECT M.PayAmount-M.Handingfee AS Amount,'1' AS Sign FROM $DataIn.cw6_orderinmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTR";
									$mySqlN.=" UNION ALL SELECT M.PayAmount-M.Handingfee AS Amount,M.PayDate AS PayDate,concat('客户货款收入'),'I' AS Sign,M.Remark FROM $DataIn.cw6_orderinmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow";
/*8-预收货款*/			$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'1' AS Sign FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D WHERE M.CompanyId=D.CompanyId AND D.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
									$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('预收客户货款'),'I' AS Sign,M.Remark FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D WHERE M.CompanyId=D.CompanyId AND D.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
/*9-货款支出*/			$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTR";
									$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('供应商货款支付'),'O' AS Sign,M.Remark FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow";
/*10-客户回扣*/			$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw1_tkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTR";
									$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('客户退款支出'),'O' AS Sign,M.Remark FROM $DataIn.cw1_tkoutmain M,$DataIn.trade_object C WHERE C.CompanyId=M.CompanyId AND C.Currency='$Currency' $PayDateSTRNow";
/*11扣供应商货款*/	$mySqlP.=" UNION ALL SELECT SUM(T.Amount) AS Amount,'1' AS Sign FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.cw15_gyskkmain G ON G.Id=T.Mid LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND M.Date>='$PreMonthS' AND M.Date<'$PreMonthE' AND T.Kid!=0 AND P.Currency='$Currency' GROUP BY T.Kid";//扣供应商款
									$mySqlN.=" UNION ALL SELECT SUM(T.Amount) AS Amount,M.Date AS PayDate,concat('供应商扣款') AS Item,'I' AS Sign,M.Remark FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw1_fkoutmain M ON T.Kid=M.Id LEFT JOIN $DataIn.cw15_gyskkmain G ON G.Id=T.Mid LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND DATE_FORMAT(M.Date,'%Y-%m')='$ChooseMonth' AND T.Kid!=0 AND P.Currency='$Currency' $BankStr GROUP BY T.Kid";//供应商扣款
/*12预付订金*/			$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P WHERE S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency='$Currency' $PayDateSTR  GROUP BY M.Id";
									$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('预付订金') AS Item, 'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P WHERE S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency='$Currency' $PayDateSTRNow  GROUP BY M.Id";
/*13中港运费/报关/商检费用*/ $mySqlP.=" UNION ALL SELECT (M.PayAmount+M.declarationCharge+M.checkCharge) AS Amount,'-1' AS Sign FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId AND C.Currency='$Currency' $PayDateSTR GROUP BY M.Id";
												$mySqlN.=" UNION ALL SELECT (M.PayAmount+M.declarationCharge+M.checkCharge) AS Amount,M.PayDate AS PayDate,concat('中港运费/报关/商检费用'),'O' AS Sign,M.Remark FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S,$DataPublic.freightdata C WHERE M.Id=S.Mid AND C.CompanyId=S.CompanyId AND C.Currency='$Currency' $PayDateSTRNow GROUP BY M.Id";
switch($Currency){
	case 1: //RMB
		//当月之前结余	//当月记录
		/*1－员工薪资*/		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cwxzmain M WHERE 1 $PayDateSTR";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('员工薪资') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwxzmain M WHERE 1 $PayDateSTRNow";
		/*2－试用工薪资*/	$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cwxztempmain M WHERE 1 $PayDateSTR";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('试用工薪资') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwxztempmain M WHERE 1 $PayDateSTRNow";
		/*3－员工借支*/		$mySqlP.=" UNION ALL SELECT M.Amount AS Amount,'-1' AS Sign FROM $DataIn.cwygjz M WHERE 1 $PayDateSTR";
										$mySqlN.=" UNION ALL SELECT M.Amount AS Amount,M.PayDate AS PayDate,concat('员工借支') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cwygjz M WHERE 1 $PayDateSTRNow";
		/*4－社保缴费*/		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.sbpaymain M WHERE 1 $PayDateSTR";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('社保缴费') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.sbpaymain M WHERE 1 $PayDateSTRNow";
		/*5－假日加班费*/	$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.hdjbmain M WHERE 1 $PayDateSTR";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('假日加班费') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.hdjbmain M WHERE 1 $PayDateSTRNow";
		/*6－快递费*/		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('快递费'),'O' AS Sign,M.Remark FROM $DataIn.cw9_expsheet M,$DataIn.ch9_expsheet S WHERE M.Id=S.Mid $PayDateSTRNow GROUP BY M.Id";
		/*7－寄样费*/		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('寄样费'),'O' AS Sign,M.Remark FROM $DataIn.cw10_samplemail M,$DataIn.ch10_samplemail S WHERE M.Id=S.Mid $PayDateSTRNow GROUP BY M.Id";
		/*8－节日奖金*/		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw11_jjmain M WHERE 1 $PayDateSTR GROUP BY M.Id";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('节日奖金') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.cw11_jjmain M WHERE 1 $PayDateSTRNow";
		/*9－总务费用*/		$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.zw3_purchasem M WHERE 1 $PayDateSTR GROUP BY M.Id";
										$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('总务采购费用') AS Item,'O' AS Sign,M.Remark AS Remark FROM $DataIn.zw3_purchasem M WHERE 1 $PayDateSTRNow";
		/*10－退税收益*/	$mySqlP.=" UNION ALL SELECT SUM(M.Taxamount)  AS Amount,'1' AS Sign  FROM $DataIn.cw14_mdtaxmain M WHERE 1 and M.Taxdate>='$PreMonthS' AND M.Taxdate<'$PreMonthE' and M.Estate='0'";		
										$mySqlN.=" UNION ALL SELECT ifnull(M.Taxamount,0) AS Amount,M.Taxdate AS PayDate,concat('退税金额') AS Item,'I' AS Sign,M.Remark AS Remark   FROM $DataIn.cw14_mdtaxmain  M WHERE 1  and DATE_FORMAT(M.Taxdate,'%Y-%m')='$ChooseMonth' and M.Estate='0'";
	break;
	case 2:	//USD
	break;
	case 3:	//HKD
	/*1-入仓费*/			$mySqlP.=" UNION ALL SELECT M.depotCharge AS Amount,'-1' AS Sign FROM $DataIn.cw4_freight_declaration M,$DataIn.ch4_freight_declaration S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
									$mySqlN.=" UNION ALL SELECT M.depotCharge AS Amount,M.PayDate AS PayDate,concat('入仓费'),'O' AS Sign,M.Remark FROM $DataIn.cw4_freight_declaration M WHERE 1 AND M.depotCharge>0 $PayDateSTRNow";
	/*2-Forward杂费*/$mySqlP.=" UNION ALL SELECT M.PayAmount AS Amount,'-1' AS Sign FROM $DataIn.cw3_forward M,$DataIn.ch3_forward S WHERE M.Id=S.Mid $PayDateSTR GROUP BY M.Id";
									$mySqlN.=" UNION ALL SELECT M.PayAmount AS Amount,M.PayDate AS PayDate,concat('Forward杂费'),'O' AS Sign,M.Remark FROM $DataIn.cw3_forward M,$DataIn.ch3_forward S WHERE M.Id=S.Mid $PayDateSTRNow GROUP BY M.Id";

		
		
	break;
	}
$mySqlP="SELECT TRUNCATE(SUM(Amount*Sign),2) AS SUM_RMB FROM ( ".$mySqlP.") A";
$PreSql=mysql_fetch_array(mysql_query($mySqlP,$link_id));
$PreAmount=$PreSql["SUM_RMB"];

echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
<tr bgcolor='$theDefaultColor' onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
<td class='A0111' width='$Field[1]'>&nbsp;</td>
<td class='A0101' width='$Field[3]'>&nbsp;</td>
<td class='A0101' width='$Field[5]'>&nbsp;</td>
<td class='A0101' width='$Field[7]'>上次结余</td>
<td class='A0101' width='$Field[9]'>&nbsp;</td>
<td class='A0101' width='$Field[11]'>&nbsp;</td>
<td class='A0101' width='' align='right'>$PreAmount</td>
</tr></table>";
$ChooseOut="N";
$mySqlN.=" ORDER BY PayDate,Sign";
$myResult = mysql_query($mySqlN,$link_id);
$i=2;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$PayDate=$myRow["PayDate"];
		$Item=$myRow["Item"];		
		$Remark=$myRow["Remark"];
		$Sign=$myRow["Sign"];
		switch($Sign){
			case "I":
				$outAmount="&nbsp;";
				$inAmount=$Amount;				
				$PreAmount=sprintf("%.2f",$PreAmount+$inAmount);
				break;
			case "O":				
				$outAmount=$Amount;
				$inAmount="&nbsp;";
				$PreAmount=sprintf("%.2f",$PreAmount-$outAmount);
				break;
			}
		$ValueArray=array(
			array(0=>$PayDate,	1=>"align='center'"),
			array(0=>$Item),
			array(0=>$Remark,	3=>"..."),
			array(0=>$inAmount,	1=>"align='right'"),
			array(0=>$outAmount,1=>"align='right'"),
			array(0=>$PreAmount,1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
pBottom($i-2,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>