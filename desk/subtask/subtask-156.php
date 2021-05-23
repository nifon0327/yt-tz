<?php   
//电信-zxq 2012-08-01
/*
功能：统计未出订单毛利=未出货订单RMB总额-订单配件采购RMB总额-订单配件USD总额
独立已更新:EWEN 2009-12-18 17:15
*/
//未出订单RMB总额
if($noshipAmount==""){
	$noshipResult = mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 AND S.Estate>'0'",$link_id);
	if($noshipRow = mysql_fetch_array($noshipResult)) {
		$noshipAmount=sprintf("%.0f",$noshipRow["Amount"]);
		}
	}
//配件成本:
$CostResult=mysql_query("SELECT 
	SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost
		FROM $DataIn.cg1_stocksheet A
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		WHERE 1 AND S.Estate>'0'  ORDER BY A.Id DESC",$link_id);
if($CostRow= mysql_fetch_array($CostResult)){
	do{
		$TempoTheCost=$CostRow["oTheCost"];
		$AmountCB=sprintf("%.0f",$TempoTheCost);
		}while($CostRow= mysql_fetch_array($CostResult));
	}
$GrossProfit=$noshipAmount-$AmountCB;
$GrossProfitPcnt=sprintf("%.0f",($GrossProfit*100/$noshipAmount));
$OutputInfo.="<tr $TR_bgcolor><td colspan='3' width='200'>未出订单毛利：<span class='yellowN'>".number_format($GrossProfit)."</span>RMB (<a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>$GrossProfitPcnt% 详情</a>)</td></tr>"
?>