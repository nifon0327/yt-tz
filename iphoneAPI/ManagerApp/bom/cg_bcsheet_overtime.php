<?php 
//BOM下单明细
include "../../basic/downloadFileIP.php";

$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  F.BuyerId='$BuyerId' "; 
}
if ($CompanyId!="") {
       $SearchRows.=" AND  A.CompanyId='$CompanyId' "; 
}

$mySql="SELECT A.Id,A.Date,A.StuffId,A.CompanyId,( A.thQty - IFNULL(B.bcQty,0)) AS Qty,P.Forshort,E.Rate,D.Price FROM (
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
				)B ON B.StuffId=A.StuffId   AND B.CompanyId=A.CompanyId
				LEFT JOIN $DataIn.bps F ON F.StuffId = A.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	            LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	            LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId
				WHERE  M.BranchId =4  AND A.thQty>IFNULL(B.bcQty,0) AND D.StuffId>0 $SearchRows  ORDER BY  A.CompanyId";    
  //echo $mySql; 
 $Result = mysql_query($mySql,$link_id);
  $sumQty=0; $sumAmount=0;$totalQty=0;$totalAmount=0;$Counts=0;
 if($myRow = mysql_fetch_array($Result)) {
          $oldCompanyId=$myRow["CompanyId"];
          $Forshort=$myRow["Forshort"];
          $Rate=$myRow["Rate"];
     do {
           $CompanyId=$myRow["CompanyId"];
            if ($CompanyId!=$oldCompanyId){
	              if ($Counts>0){
	                    $totalQty+=$sumQty;
		                $sumQty=number_format($sumQty);
		                $sumAmount=$sumAmount*$Rate;
		                $totalAmount+=$sumAmount;
		                $sumAmount=number_format($sumAmount,2);
		                $dataArray[]=array("$oldCompanyId","$Forshort","$sumQty","¥$sumAmount"); 
	                }
		            $sumQty=0; $sumAmount=0;$Counts=0;
		            $oldCompanyId=$CompanyId;
		            $Forshort=$myRow["Forshort"];
		            $Rate=$myRow["Rate"];
            }
          
            $Qty=$myRow["Qty"];
            $Price=$myRow["Price"];
            $StuffId=$myRow["StuffId"];
            //最后一次退货时间
            $LastDate=$Date;$th_OverQty=$myRow["Qty"];$bc_OverQty=0;
            $thDateResult=mysql_query("SELECT M.Date,S.Qty FROM $DataIn.ck2_thsheet S 
                        LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        WHERE S.StuffId='$StuffId' ORDER BY M.Date DESC ",$link_id);
		    while($thDateRow = mysql_fetch_array($thDateResult)){
		          $th_Qty=$thDateRow["Qty"];
		          $th_OverQty-=$th_Qty;
		          $thDays=abs(ceil((strtotime($curDate)-strtotime($thDateRow["Date"]))/3600/24));
		          if ($thDays>=15){
			         $bc_OverQty+=$th_Qty;
		          }
		          if ($th_OverQty<=0){
			          $LastDate=$thDateRow["Date"];
			          $bc_OverQty+=$th_OverQty;
			          break;
		       }   
            }
            
           $Days=abs(ceil((strtotime($curDate)-strtotime($LastDate))/3600/24));
            if ($Days>=15){
	            $Counts++;
	            $sumQty+= $bc_OverQty;
	            $Amount=$Price*$bc_OverQty;
	            $sumAmount+=$Amount;
            } 
	   } while($myRow = mysql_fetch_array($Result));
	            if ($Counts>0){
	                    $totalQty+=$sumQty;
		                $sumQty=number_format($sumQty);
		                $sumAmount=$sumAmount*$Rate;
		                $totalAmount+=$sumAmount;
		                $sumAmount=number_format($sumAmount,2);
		                $dataArray[]=array("$oldCompanyId","$Forshort","$sumQty","¥$sumAmount"); 
	                }
	                
	     $totalQty=number_format($totalQty);
         $totalAmount=number_format($totalAmount);
	     $jsonArray[]= array(
					             "View"=>"Total",
					             "Col_A"=>array("Title"=>"合计","Align"=>"L"),
					             "Col_B"=>array("Title"=>"$totalQty"),
					             "Col_C"=>array("Title"=>"¥$totalAmount")
					          ); 
	    for($i=0;$i<count($dataArray);$i++){
	           $tempArray=$dataArray[$i];
		        $jsonArray[]= array(
							             "View"=>"List",
							             "Id"=>"1182",
							             "onTap"=>array("Title"=>"未补-" .$tempArray[1] ,"Value"=>"1","Tag"=>"ExtList","Args"=>$tempArray[0]),
							             "Col_A"=>array("Title"=>$tempArray[1],"Align"=>"L"),
							             "Col_B"=>array("Title"=>$tempArray[2]),
							             "Col_C"=>array("Title"=>$tempArray[3])
							          ); 
         }
 }

?>