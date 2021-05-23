<?php 
//在库统计
$NoCompanySTR=" AND D.ComboxSign=0  ";
$NoCompanySTR="";
$lastYear=date("Y")-1;
  
$bfTypesSql = mysql_query("SELECT C.Id,C.TypeName,C.TypeColor FROM $DataPublic.ck8_bftype  C 
							 WHERE 1 and C.TypeName!=''  ORDER BY C.Id DESC",$link_id);
$bfTypes = array();							   
while ($bfTypesRow = mysql_fetch_array($bfTypesSql)) {
	$bfTypes[]=array("Id"=>$bfTypesRow["Id"],
					  "Name"=>$bfTypesRow["TypeName"],
					  "Color"=>$bfTypesRow["TypeColor"]);
}
							   

$tStockResult = mysql_fetch_array(mysql_query("
						SELECT SUM(K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType   
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0  AND TM.blSign=1 $NoCompanySTR",$link_id));//AND D.Estate>0
/*
$tStockResult = mysql_fetch_array(mysql_query("
                       SELECT SUM(K.Qty-K.llQty) AS tStockQty,
						       SUM((K.Qty-K.llQty)*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount 
						FROM ck1_rksheet K 
						INNER JOIN ck1_rkmain M ON K.Mid=M.Id 
						INNER JOIN Stuffdata D ON D.StuffId=K.StuffId 
						INNER JOIN  trade_object P ON P.CompanyId=M.CompanyId 
						INNER JOIN  currencydata C ON C.Id = P.Currency
						WHERE K.llSign>0",$link_id));
$SumTotal=$tStockResult["Amount"];
/*
$mySql="SELECT P.CompanyId,P.Forshort,SUM(K.Qty-K.llQty) AS Qty,
			SUM((K.Qty-K.llQty)*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount,
            SUM(CK.oStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS OAmount 
						FROM ck1_rksheet K 
						INNER JOIN ck1_rkmain M ON K.Mid=M.Id 
						INNER JOIN Stuffdata D ON D.StuffId=K.StuffId 
						INNER JOIN  trade_object P ON P.CompanyId=M.CompanyId 
						INNER JOIN  currencydata C ON C.Id = P.Currency
                        INNER JOIN  ck9_stocksheet CK ON CK.StuffId=K.StuffId 
						WHERE K.llSign>0  GROUP BY P.CompanyId ORDER BY  Amount DESC"; 
*/					 			   
$mySql="SELECT P.CompanyId,P.Forshort,SUM(K.tStockQty) AS Qty,
						SUM(K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount,
						SUM(K.oStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS OAmount 
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id =T.mainType 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0  AND TM.blSign=1  AND D.ComboxSign=0 
						GROUP BY P.CompanyId ORDER BY  Amount DESC ";    //AND D.Estate>0					
 $Result = mysql_query($mySql,$link_id);
 $sumQty = $sumAmount = $sumYearQty = $sumYearAmount = $MonthQty = $MonthAmount=0;
 $sumOAmount = $sumdAmount=0;
 if($myRow = mysql_fetch_array($Result)) {
     do {
           $CompanyId=$myRow["CompanyId"];
           $Forshort=$myRow["Forshort"];
           $Qty=$myRow["Qty"]; 
           $Amount=$myRow["Amount"]; 
           $AmountColor=$Amount>=500000?"#FF0000":"";
           
           $sumQty+=$Qty;
           $sumAmount+=$Amount;
           
           $OAmount=$myRow["OAmount"]; 
           $sumOAmount+=$OAmount;

            //有订单需求的库存
           $oStockResult = mysql_fetch_array(mysql_query("SELECT  SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate,X.OrderQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate)) AS dAmount  
						FROM (
						SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty 
						   FROM(
						            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType  
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0  AND  G.ywOrderDTime>'$lastYear-01-01' 
												WHERE  K.tStockQty>0  AND TM.blSign=1 AND B.CompanyId='$CompanyId' Group by K.StuffId  
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType 
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0 AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0  AND TM.blSign=1 AND B.CompanyId='$CompanyId' Group by K.StuffId
						     )A GROUP BY A.StuffId 
						)X 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency ",$link_id));

            $dAmount= $oStockResult["dAmount"];
            $sumdAmount+=$dAmount;
           
           $StockPre=$Amount==0?0:round($dAmount*100/$Amount);
           $StockPre=$StockPre>100?100:$StockPre;
           $Pre=$SumTotal==0?0:$Amount*100/$SumTotal;
           $Pre=$Pre>=1?sprintf("%.1f",$Pre) . "%":"";
           
           $BelowArray=array();$AddRowArray=array();
           $B_QtyPre=0;$B_YearQtyPre=0;
           //三个月以上未下采单
			$QtyResult=mysql_query("SELECT SUM(A.tStockQty) AS Qty,SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount,
			SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3,A.tStockQty,0)) AS YearQty,
			SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3,A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate,0)) AS YearAmount 
			FROM (
					SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
					FROM $DataIn.ck9_stocksheet K
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
					LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType  
					LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
					LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			        LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber  
					LEFT JOIN $DataIn.stuffovertime O ON O.StuffId=K.StuffId 
					WHERE  K.tStockQty>0 AND B.CompanyId='$CompanyId'  AND TM.blSign=1  GROUP BY K.StuffId 
			)A 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
			WHERE  TIMESTAMPDIFF(MONTH,A.DTime,Now())>0",$link_id);// AND D.Estate>0 
		    if($QtyRow = mysql_fetch_array($QtyResult)) {
		              $B_Qty=$QtyRow["Qty"];
		              $B_Amount=$QtyRow["Amount"];
		              $B_YearQty=$QtyRow["YearQty"];
		              $B_YearAmount=$QtyRow["YearAmount"];
		               
		              $sumYearQty+=$B_YearQty;
		              $sumYearAmount+=$B_YearAmount;
		             
		              $B_Qty=$B_Qty-$B_YearQty;
		              $B_Amount=$B_Amount-$B_YearAmount;
		              $MonthQty+=$B_Qty;
		              $MonthAmount+=$B_Amount;
		              
		              
		              if ($B_Qty>0 || $B_YearQty>0){
		                  $B_QtyPre=$Amount>0?round($B_Amount/$Amount*100):0;
		                  $B_QtyPreSTR=$B_QtyPre>0?"($B_QtyPre%)":"";
		                  
		                  $B_YearQtyPre=$Amount>0?round($B_YearAmount/$Amount*100):0;
		                  $B_YearQtyPreSTR=$B_YearQtyPre>0?"($B_YearQtyPre%)":"";
		                  
		                  $B_Qty=$B_Qty==0?"":number_format($B_Qty);
		                  $B_YearQty=$B_YearQty==0?"":number_format($B_YearQty);
		                   $B_YearAmount= $B_YearAmount==0?"":"¥" .number_format($B_YearAmount);
					      $BelowArray=array(
			                 "Col_A"=>array("Title"=>"$B_YearQty",
							 				   "Align"=>"R",
											   "Color"=>"#FF0000",
											   "RText"=>"$B_YearQtyPreSTR",
											   "RTFontSize"=>"11"),
				             "Col_B"=>array("Title"=>"$B_Qty",
							 			      "Align"=>"R",
											  "Frame"=>"8,34,67,17",
											  "Color"=>"#86B9D8",
											  "RText"=>"$B_QtyPreSTR"),
				             "Col_C"=>array("Title"=>"$B_YearAmount",
							 				   "Color"=>"#FF0000")            
			             );  
			             $AddRowArray[]=$BelowArray;
		             } 
		    }
           
           $B_Pre=100-$B_QtyPre-$B_YearQtyPre;
           $B_Pre=$B_Pre<0?0:$B_Pre;
           $legend="$B_Pre,$B_QtyPre,$B_YearQtyPre";
           $Qty=number_format($Qty);
           $Amount=number_format($Amount);
           $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"165",
					             "onTap"=>array("Title"=>"在库",
								 				   "Value"=>"1",
												   "Tag"=>"ExtList",
												   "Args"=>"$CompanyId"),
					             "Col_A"=>array("Title"=>$Forshort,
								    			   "Align"=>"L",
												   "FontSize"=>"13.5"),
					             "Col_B"=>array("Title"=>"$Qty",
								 				   "FontSize"=>"13"),
					             "Col_C"=>array("Title"=>"¥$Amount",
								 				   "FontSize"=>"13",
												   "AboveTitle"=>"$Pre",
												   "AboveColor"=>"#AAAAAA"),
					             "Legend"=>$legend,
								  "Legend2"=>"$StockPre",
					             "BelowCol"=>$BelowArray,
					             "AddRow"=>$AddRowArray 
					          ); // "Color"=>"$AmountColor",
	   } while($myRow = mysql_fetch_array($Result));
         $BelowArray=array();$AddRowArray=array();
         
          $B_QtyPre=0;$B_YearQtyPre=0;
          if ($MonthQty>0){
             $B_QtyPre=$sumAmount>0?round($MonthAmount/$sumAmount*100):0;
		     $B_QtyPreSTR=$B_QtyPre>0?"($B_QtyPre%)":"";
              $MonthQty=number_format($MonthQty);
              $MonthAmount=number_format($MonthAmount);
					      $MonthArray=array(
				             "Col_B"=>array("Title"=>"$MonthQty",
							 				   "FontSize"=>"13",
											   "Color"=>"#86B9D8",
											   "RText"=>"$B_QtyPreSTR",
											   "Align"=>"R",
											   "Frame"=>"100,38,88,16",
											   "RTFontSize"=>"11"),
				             "Col_C"=>array("Title"=>"¥$MonthAmount",
							 				   "Color"=>"#86B9D8",
											   "FontSize"=>"13",
											   "Margin"=>"0,3.5,0,0")
			             );  
			             $AddRowArray[]=$MonthArray;  
		   } 
		   
         if ($sumYearQty>0){
             $B_YearQtyPre=$sumAmount>0?round($sumYearAmount/$sumAmount*100):0;
		     $B_YearQtyPreSTR=$B_YearQtyPre>0?"($B_YearQtyPre%)":"";
              $sumYearQty=number_format($sumYearQty);
              $sumYearAmount=number_format($sumYearAmount);
					      $BelowArray=array(
				             "Col_B"=>array("Title"=>"$sumYearQty",
							 				   "Color"=>"#FF0000",
											   "FontSize"=>"13",
											   "Align"=>"R",
											   "Frame"=>"100,55,88,16",
											   "RText"=>"$B_YearQtyPreSTR",
											   "RTFontSize"=>"11"),
				             "Col_C"=>array("Title"=>"¥$sumYearAmount",
							 				   "Color"=>"#FF0000",
											   "FontSize"=>"13",
											   "Margin"=>"0,5,0,0")
			             );  
			             $AddRowArray[]=$BelowArray;  
		   } 
		
			$B_Pre=100-$B_QtyPre-$B_YearQtyPre;
			$B_Pre=$B_Pre<0?0:$B_Pre;
			$legend="$B_Pre,$B_QtyPre,$B_YearQtyPre";
         
			$StockPre=$sumAmount==0?0:round($sumdAmount*100/$sumAmount);
                
			$sumQty=number_format($sumQty);
			$sumAmount=number_format($sumAmount);          
			$sumArray= array(
					             "View"=>"Total",
					            /* "Col_A"=>array("Title"=>"合计","Align"=>"L"),*/
					             "Col_B"=>array("Title"=>"$sumQty",
								 				   "FontSize"=>"13",
												   "Margin"=>"0,3,0,0"),
					             "Col_C"=>array("Title"=>"¥$sumAmount",
								 				   "Margin"=>"0,3,0,0",
												   "FontSize"=>"13"),
					             "Legend"=>$legend,
								  "Legend2"=>"$StockPre",
					             "BelowCol"=>$BelowArray,
					             "AddRow"=>$AddRowArray ,
								  "bfTypes"=>$bfTypes
					          ); 

	     array_unshift($jsonArray,$sumArray);
 }
?>