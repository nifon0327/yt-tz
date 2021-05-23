<?php   
//ewen-2014.6.9 加入台币统计
//ewen-2014.6.9 加入欧元统计
$GatheringSUM=0;
$ShipResult = mysql_query("
SELECT SUM(GatheringSUM) AS GatheringSUM FROM(
	SELECT SUM( S.Price * S.Qty * D.Rate * M.Sign) AS GatheringSUM FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency WHERE M.Estate =0 
 UNION ALL
	 SELECT SUM(-(M.PayAmount+M.PreAmount)*D.Rate) AS GatheringSUM FROM $DataIn.cw6_orderinmain M  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	)A",$link_id);
if($ShipRow = mysql_fetch_array($ShipResult)) {
	$GatheringSUM=sprintf("%.2f",$ShipRow["GatheringSUM"]); 
	}
//if ($Login_P_Number==10868) echo $GatheringSUM . "/";
$Sql01=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*.C.Rate),0) AS Amount 
FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object D,$DataPublic.currencydata C WHERE M.Mid=0 AND M.CompanyId=D.CompanyId AND D.Currency=C.Id AND M.PayDate>'2008-07-01'",$link_id));
$Yshk=sprintf("%.0f",$Sql01["Amount"]);
$GatheringSUM-=$Yshk;
//if ($Login_P_Number==10868) echo $Yshk;
//汇率
$checkCurrency=mysql_query("SELECT Symbol,Rate FROM $DataPublic.currencydata WHERE Estate=1 AND Id>1 ORDER BY Id",$link_id);
if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
	do{
		$TempRate=strval($checkCurrencyRow["Symbol"])."Rate"; 
		$$TempRate=$checkCurrencyRow["Rate"];	
		}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
	}
// =未收客户货款+现金-没结付款
$jy=(round($GatheringSUM)+round($SumAmount2*$USDRate)+round($SumAmount3*$HKDRate)+round($SumAmount4*$TWDRate)+round($SumAmount5*$EURRate)+round($SumAmount1))-round($noPayRMB);//ewen-2014.6.9 加入台币统计

$ShipResult = mysql_query("
SELECT SUM(Amount) AS Amount,Currency FROM(
	SELECT  IFNULL(( S.Price * S.Qty *M.Sign),0) AS Amount,C.Currency AS Currency FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId WHERE M.Estate =0
 UNION ALL
	 SELECT  IFNULL((M.PayAmount+M.PreAmount)*-1,0) AS Amount,C.Currency AS Currency FROM $DataIn.cw6_orderinmain M  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
UNION ALL
	SELECT IFNULL((-M.Amount),0) AS Amount,C.Currency AS Currency FROM $DataIn.cw6_advancesreceived M,$DataIn.trade_object C WHERE M.Mid=0 AND M.CompanyId=C.CompanyId AND M.PayDate>'2008-07-01'
	)A GROUP BY Currency",$link_id);
if($ShipRow = mysql_fetch_array($ShipResult)) {
	do{
		$TempName="Gathering".strval($ShipRow["Currency"]); 
		$$TempName=sprintf("%.2f",$ShipRow["Amount"]);	
		
		}while($ShipRow = mysql_fetch_array($ShipResult));
	}
$GatheringSUM=$Gathering2*$USDRate+$Gathering1+$Gathering3*$HKDRate+$Gathering4*$EURRate; 

$contentSTR="<li class=TitleBL>未收</li><li class=TitleBR><a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>¥".number_format($GatheringSUM)."</a></li>";
$contentSTR.="<li class=DataA>$".number_format($Gathering2)."</li>";
$contentSTR.="<li class=DataA>¥".number_format($Gathering1)."</li>";
$jyView=$jy>0?"¥".number_format($jy):"(¥".number_format(-$jy).")";
$contentSTR.="<li class=TitleBL>结余</li><li class=TitleBR><span style='CURSOR: pointer;color:#FF6633'>".$jyView."</span></li>";
?>