<?php   
/////////////////////////////////////////////////**********电信---yang 20120801
//计算月份内总数输出
$CheckSumSql= mysql_query("
	SELECT SUM(InAmount) AS SumInAmount,SUM(OutAmount) AS SumOutAmount,Min(StratDate) AS StratDate FROM(
		SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS OutAmount,'0' AS InAmount,Min(M.Date) AS StratDate 
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 $TJA
	UNION ALL 
		SELECT '0' AS OutAmount,SUM(S.Qty*S.Price*C.Rate) AS InAmount,Min(M.OrderDate) AS StratDate 
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 $TJB
	)A",$link_id);
if($CheckSumRow=mysql_fetch_array($CheckSumSql)){//总额和开始的月份
	$SumInAmount=$CheckSumRow["SumInAmount"];
	$SumOutAmount=$CheckSumRow["SumOutAmount"];
	}
////////////////////////////////////////////////
//出货或下单最高金额
$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Amount) AS MaxAmont FROM ( 
		SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE M.Estate=0 $TJA GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
	UNION ALL 
	SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE 1 $TJB GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
	)A",$link_id));
$MaxAmount=$MaxResult["MaxAmont"];
//客户数量
$CheckAllSql= mysql_query("
SELECT SUM(InAmount) AS InAmount,SUM(OutAmount) AS OutAmount,CompanyId,ForShort FROM(
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS OutAmount,'0' AS InAmount,M.CompanyId,D.ForShort
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 $TJA AND D.Estate='1'  GROUP BY M.CompanyId
UNION ALL 
	SELECT '0' AS OutAmount,SUM(S.Qty*S.Price*C.Rate) AS InAmount,M.CompanyId,D.ForShort
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 $TJB AND D.Estate='1' GROUP BY M.CompanyId
)A GROUP BY CompanyId ORDER BY OutAmount DESC LIMIT 10
	",$link_id);//只显示前10个客户
$clientNum= mysql_num_rows($CheckAllSql);

//******************************************************************采购成本
include "../model/subprogram/sys_parameters.php";
$cbAmountUSD=0;$cbAmountRMB=0;$llcbAmountUSD=0;$llcbAmountRMB=0;
$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,SUM(A.OrderQty*A.Price*C.Rate) AS oTheCost2,C.Symbol,B.ProviderType
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.ch1_shipsheet H ON H.POrderId=A.POrderId
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=H.Mid 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1  AND M.Estate=0 $TJA GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$cbAmount=sprintf("%.3f",$CostRow["oTheCost"]);
				$TempSymbol=$CostRow["Symbol"];
				$TempoTheCost=$CostRow["oTheCost"];
				$TempoTheCost2=$CostRow["oTheCost2"];
				$AmountTemp="cbAmount".strval($TempSymbol);
				$$AmountTemp=sprintf("%.0f",$TempoTheCost);//毛利成本
				$AmountTemp2="llcbAmount".strval($TempSymbol);
				$$AmountTemp2=sprintf("%.0f",$TempoTheCost2);//理论成本
				}while ($CostRow= mysql_fetch_array($CostResult));
			}
$SumGrossAmount=$SumOutAmount-($cbAmountUSD+$cbAmountRMB);//毛利率
$profitRMB=$SumOutAmount-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate;//理论利润
?>