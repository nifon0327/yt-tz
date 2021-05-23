<?php 
//在库统计
$NoCompanySTR=" AND D.ComboxSign=0 ";//AND P.CompanyId!='2166' 
$lastYear=date("Y")-1;
  
			   
$mySql="SELECT P.Forshort,P.CompanyId, SUM(K.tStockQty*D.Price*C.Rate) AS Amount,SUM(K.tStockQty) as allAm, count(*) as numberC 
FROM $DataIn.ck9_stocksheet K 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId 
LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType  
LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
WHERE  K.tStockQty>0  AND TM.blSign=1  $NoCompanySTR  and T.TypeID='$typeID'
group by P.CompanyId order by Amount desc";    //AND D.Estate>0
 $Result = mysql_query($mySql,$link_id);
 $sumQty=0; $sumAmount=0; $sumYearQty=0; $sumYearAmount=0;$MonthQty=0;$MonthAmount=0;
 $sumOAmount=0;$sumdAmount=0;
 if($myRow = mysql_fetch_array($Result)) {
     do {
           $compID = $myRow["CompanyId"];
           $Forshort=$myRow["Forshort"];
           $Qty=$myRow["allAm"]; 
           $Amount=$myRow["Amount"]; 
           $AmountColor="";
           
      $CompanyId = $compID;
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
												WHERE  K.tStockQty>0  AND TM.blSign=1  AND T.TypeId='$typeID' AND B.CompanyId='$CompanyId' $NoCompanySTR Group by K.StuffId  
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype TM ON TM.Id = T.mainType   
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0   AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0  AND TM.blSign=1 AND T.TypeId='$typeID' AND B.CompanyId='$CompanyId'  $NoCompanySTR Group by K.StuffId)A GROUP BY A.StuffId 
						)X 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency ",$link_id));

            $dAmount= $oStockResult["dAmount"];
          
           $StockPre=$Amount==0?0:round($dAmount*100/$Amount);
		
         $numberC = $myRow["numberC"];
           $Qty=number_format($Qty);
           $Amount=number_format($Amount);
           $jsonArray[]= array(
					             "View"=>"S1",
					             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"TypeList","Args"=>"$typeID|$compID"),
					             "Col_A"=>array("Title"=>'' . $Forshort,"Color"=>"#86B9D8","Align"=>"L","FontSize"=>"13.5"),
					             "Col_B"=>array("Title"=>"$Qty","RText"=>"($numberC)","RTFontSize"=>"11","FontSize"=>"13"),
					             "Col_C"=>array("Title"=>"¥$Amount","FontSize"=>"13"),
					             
					              "Legend2"=>"$StockPre"
					          ); // "Color"=>"$AmountColor",
	   } while($myRow = mysql_fetch_array($Result));

        
         
      
 }
?>