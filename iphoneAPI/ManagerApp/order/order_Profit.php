<?php 
 /*
 功能模块:毛利计算 
 传入参数:$CompanyId,$saleRmbAmount,$POrderId,$ProfitColorSign=1;
 输出参数:$GrossProfit,$profitRMB2PC,$profitColor 
 */
 $profitbgColor="";
 if ($POrderId!="" && $ReadProfitColorSign!=1){
		 //$HzRate=$CompanyId==1044?0.95:1;
		//配件成本计算:只计算需要采购的部分
		$cbAmountUSD=0;$cbAmountRMB=0;$llcbAmountUSD=0;$llcbAmountRMB=0;//初始化
		$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*IFNULL(C.Rate,1)) AS oTheCost,SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost2,IFNULL(C.Symbol,'RMB') AS Symbol,B.ProviderType 
		        FROM $DataIn.cg1_stocksheet A
		        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
		        LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 	
		        WHERE 1  AND S.POrderId='$POrderId' AND A.Level=1  GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
		        do{
		                $cbAmount=sprintf("%.3f",$CostRow["oTheCost"]);
		                $TempSymbol=$CostRow["Symbol"];
		                $TempoTheCost=$CostRow["oTheCost"];
		                $TempoTheCost2=$CostRow["oTheCost2"];
		                $AmountTemp="cbAmount".strval($TempSymbol);
		                $$AmountTemp+=sprintf("%.3f",$TempoTheCost);//毛利成本
		                $AmountTemp2="llcbAmount".strval($TempSymbol);
		                $$AmountTemp2+=sprintf("%.3f",$TempoTheCost2);//理论成本
		                }while ($CostRow= mysql_fetch_array($CostResult));
		        }
		$GrossProfit=sprintf("%.3f",$saleRmbAmount-$cbAmountUSD-$cbAmountRMB);
		//单品毛利
		$profitRMB=sprintf("%.3f",$GrossProfit/$Qty);
		//理论净利
		include "../../model/subprogram/sys_parameters.php";//$HzRate
		
		$profitRMB2=$saleRmbAmount-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate;
		$sumProfitRMB+=$profitRMB2;
		$sumProfitRMB2+=$profitRMB2;
		$profitRMB2=sprintf("%.2f",$profitRMB2/$Qty);
		 
		$GrossProfit=number_format(sprintf("%.2f",$GrossProfit));		                    
		$profitRMB2PC=$Price==0?0:sprintf("%.0f",($profitRMB2*100)/($Price*$Rate));
}
if ($profitRMB2PC>10){
        $profitColor="#009900";
    }
    else{
      $profitColor=$profitRMB2PC>=3?"#FF7C03":"#FF0000";
      /*
      if ($profitRMB2PC<3){
	      $profitbgColor="#FAA8A4"; $profitColor="#FFFFFF";
      }
      */
    }
 ?>