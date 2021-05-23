<?php 
//未出订单
    if (versionToNumber($AppVersion)>=287){
    $AmountMargin="-10,0,0,0";
 }
 else{
	 $AmountMargin="-20,0,0,0";
 }
 
    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
     if($TRow = mysql_fetch_array($TResult)){
          $isPrice=1;
      }
      else{
	      $isPrice=0;
     }
     $jsondata = array();
//布局设置
$Layout=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col2"=>array("Frame"=>"115,32,48, 15","Align"=>"L"),
                          "Col3"=>array("Frame"=>"180,32,48, 15","Align"=>"L"),
                          "Col4"=>array("Frame"=>"230,32,43, 15"));
                          
$Layout2=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col2"=>array("Frame"=>"115,32,48, 15","Align"=>"L"),
                          "Col4"=>array("Frame"=>"180,32,48, 15","Align"=>"L")
                          );   
  
  //图标设置                           
 if (versionToNumber($AppVersion)>=278){//Created by 2014/09/02
		 $IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"105,35,10,10"),
	                          "Col3"=>array("Name"=>"scdj_12","Frame"=>"170,35,10,10")
	                          );
 }  
 else{                                              
		$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"105,35,8.5,10"),
		                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"165,35,13,10")
		                          );
}
/*
 //待出订单
 $waitResult=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS Qty,SUM( S.Price*S.Qty*D.Rate) AS Amount,COUNT(*) AS Counts  
         FROM $DataIn.yw1_ordermain M
         LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
         LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
         LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
         WHERE 1 and S.Estate>=2 ",$link_id));
       
$waitQty=number_format(sprintf("%.0f",$waitResult["Qty"]));
$waitAmount=number_format(sprintf("%.0f",$waitResult["Amount"]));
$waitCounts=$waitResult["Counts"];
  //逾期待出    
$waitOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
   FROM(
          SELECT Max(T.Date) AS scDate,Y.Qty,Y.Price,Y.OrderNumber    
          FROM $DataIn.yw1_ordersheet Y 
		  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=Y.POrderId  
		  WHERE  Y.Estate>=2  GROUP BY Y.POrderId 
		)S 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		WHERE  TIMESTAMPDIFF( DAY, S.scDate , Now())>=5",$link_id));
		
 $waitOverQty=$waitOverResult["Qty"]; 	
 $waitOverAmount=$waitOverResult["Amount"];
 */
 
$thisDate=date("Y-m-d");
$overdate=date("Y-m-d",strtotime("$thisDate  -6 day"));

$waitResult=mysql_fetch_array(mysql_query("SELECT B.CompanyId,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS tStockQty,
               SUM(IF (B.overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(OverSign) AS OverCounts,
               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS Amount, 
               SUM(IF(B.overSign=1,(B.rkQty-B.shipQty)*B.Price*D.Rate,0)) AS OverAmount   
		 FROM(
			SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty,
			       IF(rkDate<'$overdate',1,0) AS OverSign  
			FROM (
			    SELECT M.CompanyId,S.POrderId,S.Qty,S.Price,SUM(R.Qty) AS rkQty,MAX(R.Date) AS rkDate   
			    FROM yw1_ordersheet S 
			    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
			    WHERE S.Estate>0  GROUP BY S.POrderId 
			)A 
			LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
			GROUP BY A.POrderId
		) B 
		INNER JOIN  trade_object P ON P.CompanyId=B.CompanyId 
		INNER JOIN  currencydata D ON D.Id=P.Currency 
		LEFT  JOIN  staffmain M ON M.Number=Staff_Number 
		WHERE B.rkQty>B.shipQty ",$link_id));
				
$waitQty=number_format(sprintf("%.0f",$waitResult["tStockQty"]));
$waitAmount=number_format(sprintf("%.0f",$waitResult["Amount"]));
$waitCounts=$waitResult["Counts"];

$waitOverQty=$waitResult["OverQty"]; 	
$waitOverAmount=$waitResult["OverAmount"];
				

$AddArray=array();
 if ($waitOverQty>0){
			        $waitOverQty=$waitOverQty>0?number_format($waitOverQty):"";
			        $waitOverAmount=$waitOverAmount>0?number_format($waitOverAmount):"";
		           $AddArray= array(
				                     array("Text"=>"$waitOverQty","Copy"=>"Col1","Color"=>"#FF0000"),
				                     array("Text"=>"¥$waitOverAmount","Copy"=>"Col3","Color"=>"#FF0000")
				                     );
	}
				
$headArray=array(
                   "Id"=>"",
                   "onTap"=>array("Target"=>"List0","Args"=>""),
                   "Title"=>array("Text"=>"  成品","FontSize"=>"13","Color"=>"$CURWEEK_TITLECOLOR"),
                   "Col1"=>array("Text"=>"$waitQty","RLText"=>"($waitCounts)","RLColor"=>"#BBBBBB"),
                   "Col3"=>array("Text"=>"¥$waitAmount"),
                   "AddIcon"=>array("Name"=>"label_5d"),
                   "AddRow"=>$AddArray
				);	
				$arrayData = array();
				{
					$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];
/*
$mySql="SELECT M.CompanyId,COUNT(*) AS Counts,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price) AS Amount,SUM(S.Qty*S.Price*D.Rate) AS RmbAmount,C.Forshort,D.PreChar,SUM(IF(S.ShipType='',1,0)) AS shipType,SUM(IF(E.Type=2,S.Qty,0)) AS LockQty   
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
             LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
		    WHERE S.Estate>1   $SearchWeek  GROUP BY M.CompanyId ORDER BY RmbAmount DESC";
	*/	    
		    //echo $mySql;
	$mySql = "SELECT B.CompanyId,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS Qty,SUM((B.rkQty-B.shipQty)*B.Price) AS Amount,
		               SUM(IF (B.overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(B.OverSign) AS OverCounts,
		               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS RmbAmount,M.Name AS StaffName,P.Forshort,
		               D.PreChar,SUM(B.shipType) AS shipType,SUM(IF(B.Type=2,B.rkQty-B.shipQty,0)) AS  LockQty    
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
				   
				     //逾期待出    
				$waitOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty 
				   FROM(
				          SELECT Max(T.Date) AS scDate,S.Qty    
				          FROM $DataIn.yw1_ordersheet S 
				          LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber  
						  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=S.POrderId  
						  WHERE  S.Estate>=2  AND M.CompanyId='$CompanyId' GROUP BY S.POrderId 
						)S 
						WHERE  TIMESTAMPDIFF( DAY, S.scDate , Now())>=5",$link_id));
                 $i5d=$waitOverResult["Qty"]>0?1:0; 	
                 $iQuery=$myRow["shipType"]>0?1:0;	
                 
                 $AddArray=array();
			     if ($LockQty>0){
				        $LockQty=$LockQty>0?number_format($LockQty):"";
			           $AddArray= array(
					                     array("Text"=>"$LockQty","ColName"=>"Col3","Color"=>"#FF0000","RIcon"=>"ilock_r")
					                     );
					}

		           $tempArray=array(
		                  "Id"=>"$CompanyId","Left"=>"44",
		                  "onTap"=>array("Target"=>"List1","Args"=>"$checkWeek|$CompanyId"),
		                  "Title"=>array("Text"=>"$Forshort","Color"=>"$TITLE_GRAYCOLOR"),
		                  "Col1"=>array("Text"=>"$sumQty","RLText"=>"($Counts)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0"),
		                  "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"$AmountMargin"),
		                  "AddRows"=>$AddArray,
		                   "i5d"=>"$i5d",
		                   "iQuery"=>"$iQuery"
		               );
		          $arrayData[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"$viewHidden","data"=>$tempArray); 
      }
				}

			
 $jsondata[]=array("head"=>$headArray,"ModuleId"=>"216","onTap"=>"0","Border"=>"0.35","Layout"=>$Layout2,"hidden"=>"0","data"=>$arrayData); 


  
	$jsonArray=array("data"=>$jsondata,"remarkH"=>"0.1");//"rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"110")),
?>