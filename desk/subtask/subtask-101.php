<?php   
//电信-zxq 2012-08-01
//负利 订单数量统计
$myResult=mysql_query("SELECT 
C.Forshort,S.POrderId,(S.Qty*S.Price*D.Rate) AS Amount,S.Qty,(S.Price*D.Rate) AS SalePrice
FROM $DataIn.yw1_ordersheet S
LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
WHERE 1 AND S.Estate>0 ORDER BY M.CompanyId",$link_id);
$Type1Qty=0;
$TYpe2Qty=0;
$OrderNum=0;
$TotalsaleRMB=0;
$TotalQty=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Forshort=$myRow["Forshort"];					//订单流水号
		$POrderId=$myRow["POrderId"];					//订单流水号
		$Qty=$myRow["Qty"];								//订单数量
		$SalePrice=$myRow["SalePrice"];					//产品售价
		$saleRMB=sprintf("%.3f",$myRow["Amount"]); //订单金额
        $TotalsaleRMB+=$saleRMB;
       $TotalQty+=$Qty;
         $OrderNum++;
		//配件成本计算
		/*$llcbUSD=0;//理论USD成本
		$llcbRMB=0;//理论RMB成本
		$CostResult=mysql_query("SELECT SUM(A.OrderQty*A.Price*C.Rate) AS cbAmount,C.Symbol
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1  AND S.POrderId='$POrderId' GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$TempSymbol=$CostRow["Symbol"];
				$TempoTheCost=$CostRow["cbAmount"];
				$AmountTemp="llcb".strval($TempSymbol);
				$$AmountTemp=sprintf("%.0f",$TempoTheCost);//理论成本
				}while ($CostRow= mysql_fetch_array($CostResult));
			}
		//理论净利
		$lljlAmount=sprintf("%.3f",($saleRMB-$llcbUSD-$llcbRMB-$llcbRMB*$HzRate)/$Qty);
		if($SalePrice!=0)$profitRMB2PC=sprintf("%.0f",($lljlAmount*100)/$SalePrice);
		$lljlAmount=$lljlAmount==-0?0:$lljlAmount;
		if($profitRMB2PC<=3){//理论净利百分比负值
			if($lljlAmount<0){	//计算0以下总数
				$Type1Qty++;
				}
			else{				//计算0-3的总数
				$Type2Qty++;
				}
			}
		$OrderNum++;*/
		}while ($myRow = mysql_fetch_array($myResult));
	}

$TotalsaleRMB=round($TotalsaleRMB/10000,0);
$TotalQty=round($TotalQty/1000,0);

$tmpTitle="<font color='red'>$TotalQty"."k</font>"."/"."<font color='red'>$TotalsaleRMB"."w</font>";
?> 