<?php 
///待出按客户显示明细
//权限
 if (versionToNumber($AppVersion)>=287){
    $AmountMargin="-10,0,0,0";
 }
 else{
	 $AmountMargin="-20,0,0,0";
 }

$ReadPower=1;
  if ($LoginNumber!=""){
			    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
			    if($TRow = mysql_fetch_array($TResult)){
			       $ReadPower=1;
			    }
			    else{
			       $ReadPower=0;
			    }
} 

 $SearchWeek=$checkWeek>0 ?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)='$checkWeek'":"";
 $SearchWeek=$checkWeek=="TBC" ?" AND  IFNULL(PI.LeadWeek,0)=0 AND  IFNULL(PL.LeadWeek,0)=0 ":$SearchWeek;

 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];

/*
$mySql="SELECT M.CompanyId,COUNT(*) AS Counts,SUM(S.Qty-S.shipQty) AS Qty,SUM((S.Qty-S.shipQty)*S.Price) AS Amount,
SUM((S.Qty-S.shipQty)*S.Price*D.Rate) AS RmbAmount,C.Forshort,D.PreChar,SUM(IF(S.ShipType='',1,0)) AS shipType,
SUM(IF(E.Type=2,(S.Qty-S.shipQty),0)) AS LockQty ,
SUM(IF(S.ShipType='',((S.Qty-S.shipQty)*S.Price),0)) AS noShipAm ,
SUM(IF(S.ShipType='',(S.Qty-S.shipQty),0)) AS noShipQty
			 FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,S.ShipType,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate>1 GROUP BY S.POrderId
              )S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
             LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
		    WHERE S.Estate>1   $SearchWeek  GROUP BY M.CompanyId ORDER BY RmbAmount DESC";
*/
$thisDate=date("Y-m-d");
$overdate=date("Y-m-d",strtotime("$thisDate  -6 day"));

$mySql = "SELECT B.CompanyId,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS Qty,SUM((B.rkQty-B.shipQty)*B.Price) AS Amount,
		               SUM(IF (B.overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(B.OverSign) AS OverCounts,
		                SUM(IF (B.overSign=1,(B.rkQty-B.shipQty)*B.Price,0)) AS OverAmount,
		               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS RmbAmount,M.Name AS StaffName,P.Forshort,
		               D.PreChar,SUM(B.shipType) AS shipType,SUM(IF(B.Type=2,B.rkQty-B.shipQty,0)) AS  LockQty,
		               SUM(IF(B.shipType='0',((B.rkQty-B.shipQty)*B.Price),0)) AS noShipAm ,
                       SUM(IF(B.shipType='0',(B.rkQty-B.shipQty),0)) AS noShipQty   
				 FROM(
					SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty,
					       IF(rkDate<'$overdate',1,0) AS OverSign,IF(A.ShipType='',1,0) AS shipType,A.Type 
					FROM (
					    SELECT M.CompanyId,S.POrderId,S.Qty,S.Price,S.ShipType,E.Type,
					           SUM(R.Qty) AS rkQty,MAX(R.Date) AS rkDate 
					    FROM yw1_ordersheet S 
					    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
					    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
					    LEFT JOIN  yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
					    LEFT JOIN  yw3_pisheet PI ON PI.oId=S.Id 
                        LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
					    WHERE S.Estate>0 $SearchWeek GROUP BY S.POrderId 
					)A 
					LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
					GROUP BY A.POrderId
				) B 
				INNER JOIN  trade_object P ON P.CompanyId=B.CompanyId 
				INNER JOIN  currencydata D ON D.Id=P.Currency 
				LEFT  JOIN  staffmain M ON M.Number=Staff_Number 
				WHERE B.rkQty>B.shipQty GROUP BY B.CompanyId ORDER BY RmbAmount DESC ";	    
		    //echo $mySql;
    $viewHidden=1;
    $myResult = mysql_query($mySql,$link_id);
    while($myRow = mysql_fetch_assoc($myResult))
    {
                   $CompanyId=$myRow["CompanyId"];	
                   $Forshort=$myRow["Forshort"];	
                   $Qty=$myRow["Qty"];	
                   $Amount=$myRow["Amount"];	
                   $PreChar=$myRow["PreChar"];	
                   $Counts=$myRow["Counts"];	
                   $LockQty=$myRow["LockQty"];	
                   
                   $sumQty=number_format($Qty);
				   $sumAmount=number_format($Amount);
				   
				   $i5dQty = $myRow["OverQty"];
				   $i5dAm = $myRow["OverAmount"];
				   $nums = $myRow["OverCounts"];
				  /* 
				     //逾期待出    
				$waitOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty ,count(*) as nums,SUM(S.Qty*S.Price) as Amount
				   FROM(
				          SELECT Max(T.Date) AS scDate,(S.Qty-S.shipQty) AS Qty,S.Price    
				          FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
			               FROM $DataIn.yw1_ordersheet S 
			               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
			               WHERE S.Estate>1 GROUP BY S.POrderId
			              )S 
				          LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
						  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=S.POrderId  
						  WHERE  S.Estate>=2  AND M.CompanyId='$CompanyId' GROUP BY S.POrderId 
						)S 
						WHERE  TIMESTAMPDIFF( DAY, S.scDate , Now())>=5",$link_id));
						$i5dQty = $waitOverResult["Qty"];
						$i5dAm = $waitOverResult["Amount"];
						$nums = $waitOverResult["nums"];
				*/		
                 $i5d=$i5dQty>0?1:0; 
				 
				 	
                 $iQuery=$myRow["shipType"]>0?1:0;	
                 
                 $AddArray=array();
				 
				 $noShipQty = $myRow["noShipQty"];
				  $noShipAm = $myRow["noShipAm"];
				  if (versionToNumber($AppVersion)>=300){
					  
					  $NewAddRows = array();
				  if ($i5d==1) {
					  $i5dAm = number_format($i5dAm);
					   $i5dQty = number_format($i5dQty); 
					    $NewAddRows[]= array(
					                     array("Text"=>"$PreChar$i5dAm","ColName"=>"Col3","Color"=>"#FF0000"),
										  array("Text"=>"$i5dQty","ColName"=>"Col1","Color"=>"#FF0000","RLText"=>"($nums)","RLColor"=>"#BBBBBB","LIcon"=>"i5d_r","LIconX"=>"83"),
					                     );
				  }
				    if ($noShipQty>0) {
					  $noShipAm = number_format($noShipAm);
					   $noShipQty = number_format($noShipQty);
			
										  
										     $NewAddRows[]= array(
					                     array("Text"=>"$PreChar$noShipAm","ColName"=>"Col3","Color"=>"#358FC1"),
										  array("Text"=>"$noShipQty","ColName"=>"Col1","Color"=>"#358FC1","RLText"=>"($iQuery)","RLColor"=>"#BBBBBB","LIcon"=>"iquery_r","LIconX"=>"83"),
					                     );
				  }
				  
				  }
			     if ($LockQty>0){
				        $LockQty=$LockQty>0?number_format($LockQty):"";
			           $AddArray[]= 
					                     array("Text"=>"$LockQty","ColName"=>"Col3","Color"=>"#FF0000","RIcon"=>"ilock_r")
					                     ;
					}

		           $tempArray=array(
		                  "Id"=>"$CompanyId",
		                  "onTap"=>array("Target"=>"List1","Args"=>"$checkWeek|$CompanyId"),
		                  "Title"=>array("Text"=>"$Forshort","Color"=>"$TITLE_GRAYCOLOR"),
		                  "Col1"=>array("Text"=>"$sumQty","RLText"=>"($Counts)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0"),
		                  "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"$AmountMargin"),
		                  "AddRows"=>$AddArray,"NewAddRows"=>$NewAddRows,
		                   "i5d"=>"$i5d",
		                   "iQuery"=>"$iQuery"
		               );
		          $jsonArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"$viewHidden","data"=>$tempArray); 
      }
?>