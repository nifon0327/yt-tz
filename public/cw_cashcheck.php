<?php 
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
/*
现金核对表
用来检查与损益表的数目
*/
//系统使用日期
$SystemDataS="2008-07-01";
//取已结付财务数据条件
$Terms="WHERE 1 AND M.PayDate>='$SystemDataS'"; 
//RMB专有项目
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
	
$MyResult = mysql_query("SELECT  SUM(S.Amount*C.Rate) AS Amount,S.TypeId 
FROM $DataIn.hzqkmain M,$DataIn.hzqksheet S,$DataPublic.currencydata C $Terms AND S.Mid=M.Id AND S.Currency=C.Id GROUP BY S.TypeId ORDER BY S.TypeId",$link_id);
if($cc=mysql_fetch_array($MyResult)){
	do{
		$Amo=sprintf("%.0f",$cc["Amount"]);
		$TypeId=$cc["TypeId"];
		echo $TypeId."行政: ".$Amo."<br>";
		$TempRate="HZ".strval($TypeId);
		$$TempRate=$Amo;
		}while ($cc=mysql_fetch_array($MyResult));
	}
$MyResult = mysql_fetch_array(mysql_query("SELECT  SUM((M.PayAmount-M.Handingfee)*C.Rate) AS Amount FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object S ON S.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency $Terms",$link_id));
echo "OK-01-客户货款(货款-手续费):".sprintf("%.0f",$MyResult["Amount"])."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount*C.Rate) AS Amount FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object S ,$DataPublic.currencydata C  $Terms AND S.CompanyId=M.CompanyId AND C.Id=S.Currency",$link_id));
echo "OK-02-供应商货款:".sprintf("%.0f",$MyResult["Amount"])."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P,$DataPublic.currencydata C  $Terms AND S.Mid=M.Id AND P.CompanyId=S.CompanyId AND C.Id=P.Currency",$link_id));
echo "OK-03-供应商订金:".sprintf("%.0f",$MyResult["Amount"])."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.cw2_gysskmain M,$DataIn.cw2_gyssksheet S ,$DataPublic.currencydata C  $Terms AND S.Mid=M.Id AND C.Id=S.Currency",$link_id));
echo "OK-04-供应商税款:".sprintf("%.0f",$MyResult["Amount"])."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cw3_forward M $Terms",$link_id));
echo "OK-05-Forward杂费:".sprintf("%.0f",$MyResult["Amount"]*$HKD_Rate)."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cw4_freight M $Terms",$link_id));
echo "OK-06-中港运费:".sprintf("%.0f",$MyResult["Amount"])."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.depotCharge) AS Amount FROM $DataIn.cw4_freight M $Terms",$link_id));
echo "OK-07-入仓费:".sprintf("%.0f",$MyResult["Amount"]*$HKD_Rate)."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount+M.checkCharge) AS Amount FROM $DataIn.cw12_declaration M $Terms",$link_id));
echo "OK-08-报关费和商检费:".sprintf("%.0f",$MyResult["Amount"])."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.cwdyfmain M,$DataIn.cwdyfsheet S,$DataPublic.currencydata C  $Terms  AND S.Mid=M.Id AND C.Id=S.Currency",$link_id));
$allKF=$MyResult["Amount"];
echo "OK-09-开发费用:".sprintf("%.0f",$allKF)."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.cwdyfmain M,$DataIn.cwdyfsheet S,$DataPublic.currencydata C  $Terms AND S.Date<'2008-07-01' AND S.Mid=M.Id AND C.Id=S.Currency",$link_id));
echo "&nbsp;&nbsp;&nbsp;&nbsp;开发费用(20080701前):".sprintf("%.0f",$MyResult["Amount"])."<br>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;开发费用(20080701后):".sprintf("%.0f",$allKF-$MyResult["Amount"])."<br>";


$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cwxzmain 	M $Terms",$link_id));
$XZ=$MyResult["Amount"];
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cw11_jjmain 	M $Terms",$link_id));
$JJ=$MyResult["Amount"];
echo "OK-10-正式工薪资+节日奖金:".sprintf("%.0f",$JJ+$XZ)."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount FROM $DataIn.cwxzmain 	M,$DataIn.cwxzsheet S $Terms AND S.Mid=M.Id AND S.Month<'2008-07'",$link_id));

echo "&nbsp;&nbsp;&nbsp;&nbsp;正式工薪资+节日奖金(20080701前):".sprintf("%.0f",$MyResult["Amount"])."<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;正式工薪资+节日奖金(20080701):".sprintf("%.0f",$JJ+$XZ-$MyResult["Amount"])."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.hdjbmain 	M $Terms",$link_id));
$HDJB=$MyResult["Amount"];
echo "OK-11-假日加班费:".sprintf("%.0f",$HDJB)."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount FROM $DataIn.hdjbmain M,$DataIn.hdjbsheet S $Terms AND S.Mid=M.Id AND S.Month<'2008-07'",$link_id));
echo "&nbsp;&nbsp;&nbsp;&nbsp;-假日加班费(20080701前):".sprintf("%.0f",$MyResult["Amount"])."<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;-假日加班费(20080701后):".sprintf("%.0f",$HDJB-$MyResult["Amount"])."<br>";


$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cwxztempmain 	M $Terms",$link_id));
echo "OK-12-试用期薪资:".sprintf("%.0f",$MyResult["Amount"])."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.sbpaymain 	M $Terms",$link_id));
echo "13-社保:".sprintf("%.0f",$MyResult["Amount"])."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(S.mAmount) AS Amount FROM $DataIn.sbpaymain M,$DataIn.sbpaysheet S $Terms AND S.Mid=M.Id AND S.Month<'2008-07'",$link_id));
echo "&nbsp;&nbsp;&nbsp;&nbsp;社保(20080701前):".sprintf("%.0f",$MyResult["Amount"])."<br>";


$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.zw3_purchasem 	M $Terms",$link_id));
echo "OK-14-总务采购费用:".sprintf("%.0f",$MyResult["Amount"])."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cw9_expsheet 	M $Terms",$link_id));
$kd=$MyResult["Amount"];
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.PayAmount) AS Amount FROM $DataIn.cw10_samplemail 	M $Terms",$link_id));
echo "OK-15-快递费+寄样费:".sprintf("%.0f",$MyResult["Amount"]+$kd)."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.cwygjz 	M $Terms",$link_id));
echo "OK-16-员工借支:".sprintf("%.0f",$MyResult["Amount"])."<br>";


$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw4_otherin M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency $Terms",$link_id));
echo "OK-17-其它收入:".sprintf("%.0f",$MyResult["Amount"])."<br>";

$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.InAmount*C.Rate) AS Amount FROM $DataIn.cw5_fbdh M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.InCurrency $Terms",$link_id));
$HD=$MyResult["Amount"]."<br>";
$MyResult = mysql_fetch_array(mysql_query("SELECT SUM(M.OutAmount*C.Rate*-1) AS Amount FROM $DataIn.cw5_fbdh M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.OutCurrency $Terms",$link_id));
echo "OK-16-汇兑损益:".sprintf("%.0f",$MyResult["Amount"]+$HD)."<br>";






?>