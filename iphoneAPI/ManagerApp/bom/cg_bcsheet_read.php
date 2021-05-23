<?php 
//BOM未补
$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  F.BuyerId='$BuyerId' "; 
}
if ($CompanyId!="") {
       $SearchRows.=" AND  A.CompanyId='$CompanyId' "; 
}

if ($ColSign=="thSign"){
   $SearchRows.=" AND NOT EXISTS (SELECT R.Mid FROM $DataIn.ck2_threview R WHERE  R.Mid=S.Id  AND R.Estate<>2) 
            AND A.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')";	            
   $mySql="SELECT A.CompanyId,P.Forshort,SUM(S.Qty) AS Qty,(SUM(S.Qty*D.Price)*E.Rate) AS Amount 
				FROM $DataIn.ck2_thsheet S
				LEFT JOIN $DataIn.ck2_thmain A ON A.Id=S.Mid 
				LEFT JOIN $DataIn.bps F ON F.StuffId = S.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	            LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	            LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId
				WHERE  M.BranchId =4   AND D.StuffId>0 $SearchRows  GROUP BY A.CompanyId ORDER BY Amount DESC"; 
	
}
else{
$mySql="SELECT A.CompanyId,P.Forshort,SUM(A.thQty - IFNULL(B.bcQty,0)) AS Qty,(SUM((A.thQty - IFNULL(B.bcQty,0))*D.Price)*E.Rate) AS Amount 
              FROM (
						SELECT S.Id,S.StuffId,Max(M.Date) AS Date,M.CompanyId,SUM( S.Qty ) AS thQty
						FROM $DataIn.ck2_thsheet S
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        GROUP BY M.CompanyId,S.StuffId 
				)A
				LEFT JOIN (
				   SELECT S.StuffId,M.CompanyId, IFNULL(SUM(S.Qty), 0 ) AS bcQty FROM 
				   $DataIn.ck3_bcsheet S 
				   	LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				   GROUP BY M.CompanyId,S.StuffId
				)B ON B.StuffId=A.StuffId  AND B.CompanyId=A.CompanyId
				LEFT JOIN $DataIn.bps F ON F.StuffId = A.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	            LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	            LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId
				WHERE  M.BranchId =4  AND A.thQty>IFNULL(B.bcQty,0) AND D.StuffId>0 $SearchRows  GROUP BY A.CompanyId ORDER BY Amount DESC";    
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
					             "onTap"=>array("Title"=>"未补-$Forshort","Value"=>"1","Tag"=>"ExtList","Args"=>"$CompanyId"),
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