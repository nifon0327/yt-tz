<?php 
//未出订单
   
    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
     if($TRow = mysql_fetch_array($TResult)){
          $isPrice=1;
      }
      else{
	      $isPrice=0;
     }
 
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];
	    
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
//未出货订单总额
$noshipResult = mysql_query("SELECT SUM(S.Qty-S.shipQty) AS Qty,SUM((S.Qty-S.shipQty)*S.Price*D.Rate) AS Amount,SUM(S.Qty*S.Price*D.Rate) AS TotalAmount 
	FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate>0 GROUP BY S.POrderId
        )S  
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 ",$link_id);
if($noshipRow = mysql_fetch_array($noshipResult)) {
	$AllOrderAmount=sprintf("%.0f",$noshipRow["Amount"]);
	$TotalAmount  =sprintf("%.0f",$noshipRow["TotalAmount"]);
	$AllOrderQty=$noshipRow["Qty"];
}

//逾期未出    
$noshipOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty-S.shipQty) AS Qty,SUM((S.Qty-S.shipQty)*S.Price*D.Rate) AS Amount
		FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate>0 GROUP BY S.POrderId
        )S  
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
		LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		WHERE   IFNULL(PI.LeadWeek,PL.LeadWeek)<'$curWeeks' AND  IFNULL(PI.LeadWeek,PL.LeadWeek)>0",$link_id));
		
 $OverPercent=$AllOrderAmount>0?round($noshipOverResult["Amount"]/$AllOrderAmount*100):0;
 $AllOverQty=number_format($noshipOverResult["Qty"]); 		
 $AllOverAmount=number_format($noshipOverResult["Amount"]);		
				 
$noProfitResult = mysql_query("SELECT SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
            LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND S.Estate>'0' AND A.Level=1 ",$link_id);
if($noProfitRow = mysql_fetch_array($noProfitResult)) {
	$AllProfitAmount=sprintf("%.0f",($TotalAmount-$noProfitRow["oTheCost"]));
}

if ($AllOrderAmount>0){
	$AllPC=sprintf("%.0f",($AllProfitAmount/$TotalAmount)*100);
	$AllProfitSTR=number_format($AllProfitAmount);
	$AllOrderQty=number_format($AllOrderQty);
}
$hidden=1;

$dataArray=array();
$jsondata=array();
$colArray[]=array(
                       "Col1"=>array("Text"=>""),
                       "Col2"=>array("Text"=>"$AllPC%","Color"=>"#00BB00","RIcon"=>"iprofit_r"));

$colArray[]=array(
                       "Icon"=>array("Name"=>"label_all"),
                       "Col1"=>array("Text"=>"$AllOrderQty"),
                       "Col2"=>array("Text"=>"¥" . number_format($AllOrderAmount)));
 $colArray[]=array(
                       "Icon"=>array("Name"=>"label_over"),
                       "Col1"=>array("Text"=>"$AllOverQty","Color"=>"#FF0000"),
                       "Col2"=>array("Text"=>"¥$AllOverAmount","Color"=>"#FF0000"));                      

$TitleSTR=versionToNumber($AppVersion)>=278?"":"总计";//Created by 2014/09/02
$tempArray=array(
                      "Id"=>"",
                      "Percent"=>array("Title"=>"$TitleSTR","Value"=>"$OverPercent"),
                      "data"=>$colArray
                   );
  $dataArray[]=array("Tag"=>"Percent","data"=>$tempArray);
  $jsondata[]=array("head"=>array(),"ModuleId"=>"110","data"=>$dataArray); 

/*
 //待出订单
 $waitResult=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty-S.shipQty) AS Qty,SUM(S.Price*(S.Qty-S.shipQty)*D.Rate) AS Amount,COUNT(*) AS Counts  
         FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate>=2 GROUP BY S.POrderId
        )S  
         LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
         LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
         LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
         WHERE 1 ",$link_id));
       
$waitQty=number_format(sprintf("%.0f",$waitResult["Qty"]));
$waitAmount=number_format(sprintf("%.0f",$waitResult["Amount"]));
$waitCounts=$waitResult["Counts"];
  //逾期待出    
$waitOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
   FROM(
          SELECT Max(T.Date) AS scDate,(Y.Qty-Y.shipQty) AS Qty,Y.Price,Y.OrderNumber    
          FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate>=2 GROUP BY S.POrderId
           )Y   
		  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=Y.POrderId  
		  WHERE  1 GROUP BY Y.POrderId 
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
 
 //未用生产项目的成品类配件
 $waitOverResult2 = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
   FROM(
          SELECT Max(L.Received) AS scDate,Y.Qty,Y.Price,Y.OrderNumber    
          FROM $DataIn.yw1_ordersheet Y 
		  LEFT JOIN  $DataIn.ck5_llsheet  L ON  L.POrderId=Y.POrderId   
		  WHERE  Y.Estate>=2 AND NOT EXISTS (SELECT POrderId FROM $DataIn.sc1_cjtj T WHERE T.POrderId=Y.POrderId) GROUP BY Y.POrderId 
		)S 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		WHERE  TIMESTAMPDIFF( DAY, S.scDate , Now())>=5",$link_id));	
 $waitOverQty+=$waitOverResult2["Qty"]==""?0:$waitOverResult2["Qty"]; 	
 $waitOverAmount+=$waitOverResult2["Amount"]==""?0:$waitOverResult2["Amount"];		

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
 $jsondata[]=array("head"=>$headArray,"ModuleId"=>"216","onTap"=>"1","Layout"=>$Layout2,"hidden"=>"$hidden","data"=>array()); 


   //未出订单
	$dataArray=array();
	


	$WeekResult = mysql_query("
		 SELECT IFNULL(PI.LeadWeek,PL.LeadWeek) AS Weeks,Count(*) AS Counts,SUM(S.Qty-S.shipQty) AS Qty,SUM(IF(S.Estate>1,(S.Qty-S.shipQty),0) ) AS WaitQty, SUM( S.Price * (S.Qty-S.shipQty)* D.Rate ) AS Amount,SUM(IF(E.Type=2,(S.Qty-S.shipQty),0) ) AS LockQty 
	     FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate=1 GROUP BY S.POrderId
         )S  
	     LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber = M.OrderNumber
	     LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	     LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	     LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
	     LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
	    WHERE 1  GROUP BY IFNULL(PI.LeadWeek,PL.LeadWeek)  ORDER BY Weeks 
	",$link_id);
	$SumblQty=0;$SumLockQty=0;$SumOverQty=0;
	while($WeekRow = mysql_fetch_array($WeekResult)) {
		     $Weeks=$WeekRow["Weeks"];
		     $WeekQty=number_format($WeekRow["Qty"]);
		     $WaitQty=$WeekRow["WaitQty"];
		     $LockQty=$WeekRow["LockQty"];
		     
		     $SumLockQty+=$LockQty;
		     $SumOverQty+=$curWeeks>$Weeks && $Weeks>0?$WeekRow["Qty"]:0;
		    
		     $WeekAmount=number_format($WeekRow["Amount"]) ;
		     $Counts=$WeekRow["Counts"];
		      
		     $WaitQty=$WaitQty>0?number_format($WaitQty):"";
		     
		     //已备料数量
		     $blResult=mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS Qty FROM (
							SELECT M.CompanyId,S.Id,S.POrderId,S.ProductId,(S.Qty-S.shipQty) AS Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty 
							FROM (
							   SELECT S.Id,S.POrderId,S.OrderNumber,S.ProductId,S.Estate,S.Qty,S.Price,
							       SUM(IFNULL(C.Qty,0)) AS shipQty 
					               FROM $DataIn.yw1_ordersheet S 
					               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
					               WHERE S.scFrom>0 GROUP BY S.POrderId
					        )S  
							LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
							LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
							LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
							LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
							LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
							LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id   
						    LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
							LEFT JOIN (
										 SELECT L.StockId,SUM(L.Qty) AS Qty 
										 FROM $DataIn.yw1_ordersheet S 
										 LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
										 LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
										 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
										 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
										 WHERE  S.scFrom>0 AND S.Estate=1 AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$Weeks'  GROUP BY L.StockId
									 ) L ON L.StockId=G.StockId
							WHERE  ST.mainType<2  AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$Weeks'   
							AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
							 GROUP BY S.POrderId 
							) A  WHERE  A.blQty=A.llQty ",$link_id));
					
		      $blQty=$blResult["Qty"]==""?0:$blResult["Qty"];
		      $SumblQty+=$blQty;
		     
		       if ($Weeks>0){
			     $year=substr($Weeks, 0,4);
			     $week=substr($Weeks, 4,2);
			     $dateArray= GetWeekToDate($Weeks,"m/d");
			     
			     $weekName="Week " . $week;
			     $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
			     
			     $nameColor="";
			     $weekColor=$curWeeks>$Weeks?"#FF0000":"";
		          $rowColor=$curWeeks==$Weeks?$CURWEEK_BGCOLOR:"#FFFFFF";
		     }
		     else{
		           $Weeks="TBC";
		           $week="00";
			       $weekName="待定"; $dateSTR="  交期待定"; 
			       $nameColor="#0000FF";$weekColor="";
			       $rowColor="#FFFFFF";
		     }
		    
		     $AddArray=array();
		     if ($blQty>0 || $LockQty>0){
			        $LockQty=$LockQty>0?number_format($LockQty):"";
			        $blQty=$blQty>0?number_format($blQty):"";
			        //,"Align"=>"R","FontSize"=>"13","Margin"=>"-30,0,0,0",
		           $AddArray= array(
				                     array("Text"=>"$blQty","Copy"=>"Col1","Color"=>"#3888B6","RIcon"=>"ibl_r"),
				                     array("Text"=>"$LockQty","Copy"=>"Col3","Color"=>"#FF0000","RIcon"=>"ilock_r")
				                     );
				}
				
	          $headArray=array(
						                      "Id"=>"$Weeks",
						                       "RowSet"=>array("bgColor"=>"$rowColor"),
						                       "onTap"=>array("Target"=>"List0","Args"=>"$Weeks"),	
						                      "Title"=>array("Week"=>"$week","WeekDate"=>"$dateSTR","bgColor"=>"$weekColor"),
						                       "Col1"=>array("Text"=>"$WeekQty","RLText"=>"($Counts)","RLColor"=>"#BBBBBB","Color"=>"$weekColor"),
						                       "Col3"=>array("Text"=>"¥$WeekAmount"),
						                       "AddRow"=>$AddArray
						                   );                   
        $jsondata[]=array("head"=>$headArray,"ModuleId"=>"110","onTap"=>"1","IconSet"=>$IconSet,"Layout"=>$Layout,"hidden"=>"$hidden","data"=>array()); 
	 }

	$jsonArray=array("data"=>$jsondata);//"rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"110")),
?>