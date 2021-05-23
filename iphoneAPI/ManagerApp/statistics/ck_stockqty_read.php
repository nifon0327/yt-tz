<?php 
//在库统计
$NoCompanySTR="AND P.CompanyId!='2166' ";
$lastYear=date("Y")-1;
  
 $tStockResult = mysql_fetch_array(mysql_query("
						SELECT SUM(K.tStockQty*D.Price*C.Rate) AS Amount
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataIn.currencydata C ON C.Id = P.Currency
						LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						WHERE  D.Estate>0 AND K.tStockQty>0  AND  MT.blSign=1 $NoCompanySTR",$link_id));//AND D.Estate>0
$SumTotal=$tStockResult["Amount"];
			   
$mySql="SELECT P.CompanyId,P.Forshort,SUM(K.tStockQty) AS Qty,SUM(K.tStockQty*D.Price*C.Rate) AS Amount,SUM(K.oStockQty*D.Price*C.Rate) AS OAmount 
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataIn.currencydata C ON C.Id = P.Currency
						LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						WHERE   D.Estate>0 AND K.tStockQty>0  AND  MT.blSign=1 $NoCompanySTR
						GROUP BY P.CompanyId ORDER BY  Amount DESC ";    //AND D.Estate>0
 $Result = mysql_query($mySql,$link_id);
 $sumQty=0; $sumAmount=0; $sumYearQty=0; $sumYearAmount=0;$MonthQty=0;$MonthAmount=0;
 $sumOAmount=0;$sumdAmount=0;
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
           //查询未入库数量
           /*
           $checkCgResult=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.cgAmount,0)) AS cgAmount,SUM(IFNULL(A.rkAmount,0)) AS rkAmount FROM(
            SELECT SUM((G.AddQty+G.FactualQty)*D.Price*C.Rate) AS cgAmount,'0' AS rkAmount  
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.rkSign>0 
						WHERE  K.tStockQty>0  AND T.mainType<2 AND B.CompanyId='$CompanyId' 
		   UNION ALL 
                        SELECT '0' AS cgAmount,SUM(R.Qty*D.Price*C.Rate) AS rkAmount  
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.rkSign>0 
                        LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=G.StockId 
						WHERE  K.tStockQty>0  AND T.mainType<2 AND B.CompanyId='$CompanyId')A ",$link_id));
           $cgAmount= $checkCgResult["cgAmount"];
           $rkAmount= $checkCgResult["rkAmount"];
           
           $cgAmount=$Amount+$cgAmount-$rkAmount;
           $sumcgAmount+=$cgAmount;
           $StockPre=$cgAmount==0?0:round(($cgAmount-$OAmount)*100/$cgAmount);
           */
            //有订单需求的库存
           $oStockResult = mysql_fetch_array(mysql_query("SELECT  SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*D.Price*C.Rate,X.OrderQty*D.Price*C.Rate)) AS dAmount  
						FROM (
						SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty FROM(
						            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0  AND  G.ywOrderDTime>'$lastYear-01-01' 
												WHERE  D.Estate>0 AND  K.tStockQty>0  AND MT.blSign=1 AND B.CompanyId='$CompanyId' Group by K.StuffId  
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0   AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  D.Estate>0 AND K.tStockQty>0  AND MT.blSign=1 AND B.CompanyId='$CompanyId' Group by K.StuffId)A GROUP BY A.StuffId 
						)X 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency ",$link_id));

            $dAmount= $oStockResult["dAmount"];
            $sumdAmount+=$dAmount;
           
           $StockPre=$Amount==0?0:round($dAmount*100/$Amount);
           
           $Pre=$SumTotal==0?0:$Amount*100/$SumTotal;
           $Pre=$Pre>=1?sprintf("%.1f",$Pre) . "%":"";
           
           $BelowArray=array();$AddRowArray=array();
           $B_QtyPre=0;$B_YearQtyPre=0;
           //三个月以上未下采单
			$QtyResult=mysql_query("SELECT SUM(A.tStockQty) AS Qty,SUM(A.tStockQty*D.Price*C.Rate) AS Amount,
			SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3,A.tStockQty,0)) AS YearQty,
			SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3,A.tStockQty*D.Price*C.Rate,0)) AS YearAmount 
			FROM (
					SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
					FROM $DataIn.ck9_stocksheet K
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
					LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
					LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
					LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			        LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber  
					LEFT JOIN $DataIn.stuffovertime O ON O.StuffId=K.StuffId 
					WHERE  D.Estate>0 AND K.tStockQty>0 AND B.CompanyId='$CompanyId'  AND MT.blSign=1  GROUP BY K.StuffId 
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
			                 "Col_A"=>array("Title"=>"$B_YearQty","Align"=>"L","Color"=>"#FF0000","RText"=>"$B_YearQtyPreSTR"),
				             "Col_B"=>array("Title"=>"$B_Qty","Color"=>"#86B9D8","RText"=>"$B_QtyPreSTR"),
				             "Col_C"=>array("Title"=>"$B_YearAmount","Color"=>"#FF0000")            
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
					             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"ExtList","Args"=>"$CompanyId"),
					             "Col_A"=>array("Title"=>$Forshort,"Align"=>"L"),
					             "Col_B"=>array("Title"=>"$Qty"),
					             "Col_C"=>array("Title"=>"¥$Amount","AboveTitle"=>"$Pre","AboveColor"=>"#AAAAAA"),
					             "Legend"=>$legend,"Legend2"=>"$StockPre",
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
				             "Col_B"=>array("Title"=>"$MonthQty","Color"=>"#86B9D8","RText"=>"$B_QtyPreSTR"),
				             "Col_C"=>array("Title"=>"¥$MonthAmount","Color"=>"#86B9D8")
			             );  
			             $AddRowArray[]=$MonthArray;  
		   } 
		   
         if ($sumYearQty>0){
             $B_YearQtyPre=$sumAmount>0?round($sumYearAmount/$sumAmount*100):0;
		     $B_YearQtyPreSTR=$B_YearQtyPre>0?"($B_YearQtyPre%)":"";
              $sumYearQty=number_format($sumYearQty);
              $sumYearAmount=number_format($sumYearAmount);
					      $BelowArray=array(
				             "Col_B"=>array("Title"=>"$sumYearQty","Color"=>"#FF0000","RText"=>"$B_YearQtyPreSTR"),
				             "Col_C"=>array("Title"=>"¥$sumYearAmount","Color"=>"#FF0000")
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
					             "Col_A"=>array("Title"=>"合计","Align"=>"L"),
					             "Col_B"=>array("Title"=>"$sumQty"),
					             "Col_C"=>array("Title"=>"¥$sumAmount"),
					             "Legend"=>$legend,"Legend2"=>"$StockPre",
					             "BelowCol"=>$BelowArray,
					             "AddRow"=>$AddRowArray 
					          ); 

	     array_unshift($jsonArray,$sumArray);
 }
?>