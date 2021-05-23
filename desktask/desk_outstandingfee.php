<?
//ewen 2012-12-13
include "../model/modelhead.php";
$Terms="WHERE 1 AND M.Estate=3"; //取数据条件：处于审核通过但未结付的状态
$Result="SELECT IFNULL(SUM((M.AddQty+M.FactualQty)*M.Price),0) AS Amount,		'1' AS Name,P.Currency,'供应商货款' AS ItemName FROM $DataIn.cw1_fkoutsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms GROUP BY P.Currency";//供应商货款
$Result.=" UNION ALL SELECT IFNULL(SUM(M.FactualQty*M.Price),0) AS Amount,	'2' AS Name,P.Currency,'佣金' AS ItemName FROM $DataIn.cw1_tkoutsheet M LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency $Terms GROUP BY P.Currency";//客户回扣
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'3' AS Name,P.Currency,'未付订金' AS ItemName FROM $DataIn.cw2_fkdjsheet M LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId WHERE 1 AND M.Estate=3 AND M.Did=0  GROUP BY P.Currency";
$Result.=" UNION ALL SELECT IFNULL(SUM(-M.Amount),0) AS Amount,					'4' AS Name,P.Currency,'未抵货款的订金' AS ItemName FROM $DataIn.cw2_fkdjsheet M LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId WHERE 1 AND M.Did=0 AND (M.Estate=0 OR M.Estate=3) GROUP BY P.Currency";//未抵货款的订金(需扣除此金额)
$Result.=" UNION ALL SELECT IFNULL(SUM(T.Amount*-1),0) AS Amount ,				'5' AS Name, P.Currency ,'扣供应商货款' AS ItemName FROM $DataIn.cw15_gyskksheet T LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId WHERE 1 AND M.Estate=0 AND T.Kid=0 GROUP BY P.Currency";			//扣供应商货款
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'6' AS Name,M.Currency,'供应商税款' AS ItemName FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms";	//供应商税款
$Result.=" UNION ALL SELECT IFNULL(SUM(-M.Taxamount ),0) AS Amount,			'7' AS Name,'1' AS Currency,'未结付退税收益' AS ItemName FROM $DataIn.cw14_mdtaxmain M WHERE 1 AND M.Estate=3";//未结付退税
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'8' AS Name,M.Currency,'开发费用' AS ItemName FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms GROUP BY M.Currency";				//开发费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Qty*M.Price),0) AS Amount,				'9' AS Name,'1' AS Currency,'总务采购费用' AS ItemName FROM $DataIn.zw3_purchases M $Terms";		//总务采购费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'10' AS Name,M.Currency,'行政费用' AS ItemName FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms GROUP BY M.Currency";	//行政费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'11' AS Name,'1' AS Currency,'员工薪资' AS ItemName FROM $DataIn.cwxzsheet M $Terms";				//员工薪资
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'12' AS Name,'1' AS Currency,'假日加班费' AS ItemName FROM $DataIn.hdjbsheet M $Terms";				//假日加班费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.mAmount+M.cAmount),0) AS Amount,'13' AS Name,'1' AS Currency,'社保费用' AS ItemName FROM $DataIn.sbpaysheet M $Terms";	//社保费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'14' AS Name,'1' AS Currency,'节日奖金' AS ItemName FROM $DataIn.cw11_jjsheet M $Terms";			//节日奖金
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'15' AS Name,'3' AS Currency,'Forward费用' AS ItemName FROM $DataIn.ch3_forward M LEFT JOIN $DataPublic.freightdata B ON B.CompanyId=M.CompanyId  $Terms";//Forward费用:不分货币？
$Result.=" UNION ALL SELECT IFNULL(SUM(M.mcWG*M.Price),0) AS Amount,			'16' AS Name,'1' AS Currency,'中港运费' AS ItemName FROM $DataIn.ch4_freight_declaration M $Terms";	//中港运费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.depotCharge),0) AS Amount,			'17' AS Name,'3' AS Currency,'入仓费' AS ItemName FROM $DataIn.ch4_freight_declaration M $Terms";	//中港运费
$Result.=" UNION ALL SELECT IFNULL(SUM(M.declarationCharge+M.checkCharge),0) AS Amount,'18' AS Name,'1' AS Currency,'报关/商检费用' AS ItemName FROM $DataIn.ch4_freight_declaration M $Terms";			//报关费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'19' AS Name,'1' AS Currency,'快递费用' AS ItemName FROM $DataIn.ch9_expsheet M $Terms";			//快递费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount),0) AS Amount,					'20' AS Name,'1' AS Currency,'寄样费用' AS ItemName FROM $DataIn.ch10_samplemail M $Terms";			//寄样费用
$Result.=" UNION ALL SELECT IFNULL(SUM(M.Amount*-1),0) AS Amount,				'21' AS Name,M.Currency,'其他收入' AS ItemName FROM $DataIn.cw4_otherin M WHERE M.Estate=2 GROUP BY M.Currency";			//其他收入

$SumAmount_1=$SumAmount_2=$SumAmount_3=$SumAmount_4=0;
$noPaySql = mysql_query("$Result",$link_id);
if($noPayRow = mysql_fetch_array($noPaySql)) {
	$Num=1;
	do{
		$noPayRMB=sprintf("%.2f",$noPayRow["Amount"]);
		$Currency=$noPayRow["Currency"];
		$Name=$noPayRow["Name"];
		$ItemName=$noPayRow["ItemName"];
		$TempName="A".$Name."_".strval($Currency);
		$$TempName=$noPayRMB;
		$TempItemName="ItemName_".strval($Name);
		$$TempItemName=$ItemName;
		$TempSumAmount="SumAmount_".strval($Currency);
		$$TempSumAmount+=$noPayRMB;
		}while ($noPayRow = mysql_fetch_array($noPaySql));
	}
//总RMB
$rateResult = mysql_query("SELECT Rate,Id FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Id=$rateRow["Id"];
		$TempCurrency="SumAmount_".strval($Id);
		$SumRMB+=$$TempCurrency*$rateRow["Rate"];
		$TempRate="Rate_".strval($Id);
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
?>
<title>审核通过未结付金额统计</title>
<body>
<form name="form1" method="post" action="">
<table width="720" border="0" cellspacing="0">
  <tr>
    <td height="24" colspan="6" align="center">
      <p>审核通过未结付金额统计</p>
      <p>&nbsp;</p></td>
    <td colspan="2" valign="bottom" align="left">统计日期:<?=date("Y年m月d日")?></td>
    </tr>
  <tr class=''>
    <td width="40" class="A1111" height="25" align="center">序&nbsp;号</td>
    <td width="180" class="A1101">项&nbsp;目</td>
    <td width="75" align="center" class="A1101">&nbsp;</td>
    <td width="75" align="center" class="A1101">&nbsp;</td>
    <td width="73" align="center" class="A1101">&nbsp;</td>
    <td width="75" class="A1101" align="center">未付HKD</td>
    <td width="73" class="A1101" align="center">未付USD</td>
    <td width="75" class="A1101" align="center">未付RMB</td>
  </tr>
<?
for($i=1;$i<=$Name;$i++){
	$TempAmount1="A".strval($i)."_1";
	$TempAmount2="A".strval($i)."_2";
	$TempAmount3="A".strval($i)."_3";
	$TempItem="ItemName_".strval($i);
	//百分比计算
	//比率计算
	$FK=$$TempAmount1+$$TempAmount2*$Rate_2+$$TempAmount3*$Rate_3;
	$FKpc=$SumRMB>0?sprintf("%.1f",($FK/$SumRMB)*100):0;
	$FKpc=$FKpc==0?"":$FKpc."%";
	echo"
	<tr>
	<td  height='25' class='A0111' align='center'>$i</td>
    <td  height='25' class='A0101'>".$$TempItem."</td>
  	<td class='A0101' align='right'>&nbsp;</td>
  	<td class='A0101' align='right'>&nbsp;</td>
	<td class='A0101' align='right'>$FKpc &nbsp;</td>
    <td class='A0101' align='right'>".zerotospace($$TempAmount3)."&nbsp;</td>
  	<td class='A0101' align='right'>".zerotospace($$TempAmount2)."&nbsp;</td>
  	<td class='A0101' align='right'>".zerotospace($$TempAmount1)."&nbsp;</td>
</tr>
	";
	}
	?>
<tr class=''>
    <td height="25" class="A0111" colspan="2">合 计: 转RMB总额约：<?=number_format($SumRMB)."元"?></td>
    <td height="25" class="A0101">&nbsp;</td>
    <td height="25" class="A0101">&nbsp;</td>
    <td class="A0101" align="right">&nbsp;</td>
    <td align="right" class="A0101"><?=$SumAmount_3?>&nbsp;</td>
    <td align="right" class="A0101"><?=$SumAmount_2?>&nbsp;</td>
    <td align="right" class="A0101"><?=$SumAmount_1?>&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>