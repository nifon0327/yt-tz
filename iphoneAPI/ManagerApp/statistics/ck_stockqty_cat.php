<?php 
//在库统计
$NoCompanySTR=" AND D.ComboxSign=0 ";
$lastYear=date("Y")-1;
  

			   
$mySql="SELECT T.TypeName,T.TypeId, SUM(K.tStockQty*D.Price*C.Rate) AS Amount,SUM(K.tStockQty) as allAm,SUM(K.oStockQty) as allOAm  , count(*) as numberC 
			FROM $DataIn.ck9_stocksheet K 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId 
			LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
			LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType 
			LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
			WHERE  K.tStockQty>0  AND TM.blSign=1   $NoCompanySTR 
			group by T.TypeID order by Amount desc";    //AND D.Estate>0 AND T.mainType<2
 $Result = mysql_query($mySql,$link_id);
 $sumQty=0; $sumAmount=0; $sumYearQty=0; $sumYearAmount=0;$MonthQty=0;$MonthAmount=0;
 $sumOAmount=0;$sumdAmount=0;
 $sumNumberC = 0;
 if($myRow = mysql_fetch_array($Result)) {
     do {
           
           $Forshort=$myRow["TypeName"];
           $Qty=$myRow["allAm"]; 
           $Amount=$myRow["Amount"]; 
           $AmountColor="";
           
           $sumQty+=$Qty;
           $sumAmount+=$Amount;
           
           $OAmount=$myRow["allOAm"]; 
           $sumOAmount+=$OAmount;
          
		  $TpID = $myRow["TypeId"];
            //有订单需求的库存
           $oStockResult = mysql_fetch_array(mysql_query("SELECT  SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*D.Price*C.Rate,X.OrderQty*D.Price*C.Rate)) AS dAmount  
						FROM (
						SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty FROM(
						            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType  
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0  AND  G.ywOrderDTime>'$lastYear-01-01' 
												WHERE  K.tStockQty>0  AND TM.blSign=1   AND T.TypeId='$TpID' $NoCompanySTR Group by K.StuffId  
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType  
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0   AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0  AND TM.blSign=1   AND T.TypeId='$TpID' $NoCompanySTR Group by K.StuffId)A GROUP BY A.StuffId 
						)X 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency ",$link_id));//AND T.mainType<2 

            $dAmount= $oStockResult["dAmount"];
            $sumdAmount+=$dAmount;
           
           $StockPre=$Amount==0?0:round($dAmount*100/$Amount);
           
        
         $numberC = $myRow["numberC"];
		 $sumNumberC += $numberC;
           $Qty=number_format($Qty);
           $Amount=number_format($Amount);
           $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"165",
					             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"TypeList","Args"=>"$TpID"),
					             "Col_A"=>array("Title"=>$Forshort,"Align"=>"L","FontSize"=>"13.5"),
					             "Col_B"=>array("Title"=>"$Qty","RText"=>"($numberC)","FontSize"=>"13"),
					             "Col_C"=>array("Title"=>"¥$Amount","FontSize"=>"13"),
					             "Legend2"=>"$StockPre"
					           
					          ); // "Color"=>"$AmountColor",
	   } while($myRow = mysql_fetch_array($Result));

        
         
          $StockPre=$sumAmount==0?0:round($sumdAmount*100/$sumAmount);
                
		 $sumQty=number_format($sumQty);
         $sumAmount=number_format($sumAmount);          
	     $sumArray= array(
					             "View"=>"Total",
					            "Col_A"=>array("Title"=>"合计","Align"=>"L","FontSize"=>"13.5"),
					             "Col_B"=>array("Title"=>"$sumQty","FontSize"=>"13","Margin"=>"0,3,0,0","RText"=>"($sumNumberC)","RTFontSize"=>"11"),
					             "Col_C"=>array("Title"=>"¥$sumAmount","Margin"=>"0,3,0,0","FontSize"=>"13"),
					             "Legend2"=>"$StockPre"
					          ); 

	     array_unshift($jsonArray,$sumArray);
 }
?>