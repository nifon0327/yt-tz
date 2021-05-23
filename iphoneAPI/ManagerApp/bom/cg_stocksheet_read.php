<?php 
//BOM未收
$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  S.BuyerId='$BuyerId' "; 
}

if ($ColSign=="Audit"){
	$mySql="SELECT M.CompanyId,P.Forshort,SUM(S.AddQty+S.FactualQty) AS Qty,(SUM(S.AddQty+S.FactualQty)*S.Price*E.Rate) AS Amount   
            FROM $DataIn.cg1_stockmain M  
			LEFT JOIN  $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	        LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
			LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId 
			  LEFT JOIN $DataIn.stuffproperty PE ON PE.StuffId=S.StuffId 
			WHERE  M.BuyerId='$BuyerId'   AND PE.Property<>2 AND PE.Property<>4  AND NOT EXISTS (SELECT R.Mid FROM $DataIn.cg1_stockreview R WHERE  R.Mid=M.Id )  AND M.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')
			  AND NOT EXISTS (SELECT StuffId FROM $DataIn.stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate>0)  																	             
			  $SearchRows1 
		    GROUP BY M.CompanyId  ORDER BY  Amount DESC"; 
}
else{
		if ($ColSign=="Over"){
			   $SearchRows.=" AND  TIMESTAMPDIFF(HOUR,S.ywOrderDTime,NOW())>='4' "; 
		 }
  $mySql="SELECT S.CompanyId,P.Forshort,SUM(S.AddQty+S.FactualQty) AS Qty,(SUM(S.AddQty+S.FactualQty)*S.Price*E.Rate) AS Amount    
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	        LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
			LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
			WHERE S.Mid=0 and  S.Estate=0 and T.mainType<2 and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4  $SearchRows 
			AND NOT EXISTS (SELECT StuffId FROM $DataIn.stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate>0) 
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1))
		   GROUP BY S.CompanyId  ORDER BY  Amount DESC"; 
}

 $Result = mysql_query($mySql,$link_id);
 $sumQty=0; $sumAmount=0;
 if($myRow = mysql_fetch_array($Result)) {
     do {
           $CompanyId=$myRow["CompanyId"];
           $Forshort=$myRow["Forshort"];
           $Qty=$myRow["Qty"]; 
           $Amount=$myRow["Amount"]; 
           
           $sumQty+=$Qty;
           $sumAmount+=$Amount;
           
           $Qty=number_format($Qty);
           $Amount=number_format($Amount);
           
           $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"165",
					             "onTap"=>array("Title"=>"下单-$Forshort","Value"=>"1","Tag"=>"ExtList","Args"=>"$CompanyId"),
					             "Col_A"=>array("Title"=>$Forshort,"Align"=>"L"),
					             "Col_B"=>array("Title"=>"$Qty"),
					             "Col_C"=>array("Title"=>"¥$Amount")
					          ); 
	   } while($myRow = mysql_fetch_array($Result));
	   
	     $sumQty=number_format($sumQty);
         $sumAmount=number_format($sumAmount);
	     $sumArray= array(
					             "View"=>"Total",
					             "Col_A"=>array("Title"=>"合计","Align"=>"L"),
					             "Col_B"=>array("Title"=>"$sumQty"),
					             "Col_C"=>array("Title"=>"¥$sumAmount")
					          ); 

	     array_unshift($jsonArray,$sumArray);
 }
?>