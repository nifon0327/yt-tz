<?php 
//未收订单毛利分析
//include "../../../basic/parameter.inc";
//$jsonArray = array();

$dataArray=array();$dataArray2=array();
//读取未出订单信息
$orderResult = mysql_query("
	SELECT M.CompanyId,C.Forshort,SUM(S.Price*S.Qty*D.Rate) AS Amount,SUM(S.Qty) AS Qty,A.ColorCode  
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN  $DataIn.yw1_ordermain M ON S.OrderNumber = M.OrderNumber
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	LEFT JOIN $DataIn.chart2_color A ON A.CompanyId=M.CompanyId
	WHERE S.Estate>0  GROUP BY M.CompanyId ORDER BY Amount DESC",$link_id);

if ($orderRow = mysql_fetch_array($orderResult)) {
	$i=0;$TotalAmount=0;$TotalProfit=0;$OtherAmount=0;$OtherProfit=0;$TotalQty=0;$OtherQty=0;
	do{
		$CompanyId=$orderRow["CompanyId"];
		$Forshort=$orderRow["Forshort"];
		$RMBAmount=$orderRow["Amount"];
		$Qty=$orderRow["Qty"];
		if ($i>9){
			$OtherAmount+=$RMBAmount;
			$OtherQty+=$Qty;
		}
		$TotalAmount+=$RMBAmount;
		$TotalQty+=$Qty;
        $ColorCode =$orderRow["ColorCode"]==""?"BBBBBB":$orderRow["ColorCode"];    
        
       if ($i<10 && $RMBAmount>0){
           $dataArray[]=array( "$CompanyId","$Forshort","$RMBAmount","$ColorCode","$Qty");  
       }
				//计算毛利
			$Result = mysql_fetch_array(mysql_query("	
				SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount,A.oTheCost
				FROM $DataIn.yw1_ordermain M
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
				LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
				LEFT JOIN (
					SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,M.CompanyId
					FROM  $DataIn.cg1_stocksheet A
					LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
					LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
					LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
					LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
					WHERE 1 AND S.Estate>'0' GROUP BY M.CompanyId
					) A ON A.CompanyId=M.CompanyId
				WHERE S.Estate>0  AND M.CompanyId='$CompanyId' GROUP BY  M.CompanyId
			",$link_id));//GROUP BY  M.CompanyId 低版本不可省略
			$cbAmount=sprintf("%.0f",$Result["oTheCost"]);//成本
			
			//$ddbl=sprintf("%.1f",($RMBAmount/$AllOrderAmount)*100);//订单金额/总订单金额
			//毛利
			$TempProfit=sprintf("%.0f",$RMBAmount-$cbAmount);
			if ($i>9){
					$OtherProfit+=$TempProfit;
				}
			//毛利率
		//	$ProfitTempPC=sprintf("%.1f",($TempProfit/$RMBAmount)*100);
	   $TotalProfit+=$TempProfit;	
	   if ($i<10 && $TempProfit>0){
			$dataArray2[]=array( "$CompanyId","$Forshort","$TempProfit","$ColorCode","");  
		}
	  if ( $RMBAmount>0)$i++;
    }while ($orderRow = mysql_fetch_array($orderResult));
    if ($OtherAmount>0){
        $dataArray[]=array( "0","其它","$OtherAmount","CCCCCC","$OtherQty");  
        $dataArray2[]=array( "0","其它","$OtherProfit","CCCCCC","");  
    }
    
    $TotalAmount=number_format($TotalAmount);
    $TotalProfit=number_format($TotalProfit);
    $TotalQty=number_format($TotalQty);
    $jsonArray=array("Title"=>"未出分析图","Date"=>"","DateType"=>"0",
							    "data1"=>array("Title"=>"未出金额","Total"=>"¥$TotalAmount","TotalQty"=>"$TotalQty" ,"PreChar"=>"¥","data"=>$dataArray),
							    "data2"=>array("Title"=>"未出利润","Total"=>"¥$TotalProfit","PreChar"=>"¥","data"=>$dataArray2)
							    );
   }
 //echo json_encode($jsonArray);
?>