<?php 
//未出按客户显示明细
//权限
  if (versionToNumber($AppVersion)>=287){
    $AmountMargin="9,0,0,0";
 }
 else{
	 $AmountMargin="";
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
 $SearchCompany=$checkCompanyId>0? " AND M.CompanyId='$checkCompanyId' ":"";
 
 
  //待出订单
 $waitResult=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS Qty,SUM( S.Price*S.Qty*D.Rate) AS Amount,COUNT(*) AS Counts  
         FROM $DataIn.yw1_ordermain M
         LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
         LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
         LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
         WHERE 1 and S.Estate>=2  $SearchCompany ",$link_id));
       
$waitQty=number_format(sprintf("%.0f",$waitResult["Qty"]));
$waitAmount=number_format(sprintf("%.0f",$waitResult["Amount"]));
$waitCounts=$waitResult["Counts"];
 
   //逾期待出    
$waitOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
   FROM(
          SELECT Max(T.Date) AS scDate,Y.Qty,Y.Price,Y.OrderNumber    
          FROM $DataIn.yw1_ordersheet Y 
          LEFT JOIN $DataIn.yw1_ordermain M ON Y.OrderNumber=M.OrderNumber  
		  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=Y.POrderId  
		  WHERE  Y.Estate>=2  $SearchCompany GROUP BY Y.POrderId 
		)S 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		WHERE  TIMESTAMPDIFF( DAY, S.scDate , Now())>=5",$link_id));

if ($waitResult["Qty"]>0){	
		 $waitOverQty=$waitOverResult["Qty"]>0?number_format($waitOverResult["Qty"]):$waitOverQty; 	
		 $waitOverAmount=$waitOverResult["Amount"]>0?"¥" . number_format($waitOverResult["Amount"]):"";
		 $AddIcon=$waitOverQty>0?array("Name"=>"label_5d"):array();
 			
 $tempArray=array(
		                  "Id"=>"$checkCompanyId",
		                  "onTap"=>array("Target"=>"List1","Args"=>"$checkCompanyId|WAIT"),
		                  "Col1"=>array("Text"=>"待出","Frame"=>"25,10,80,20"),
		                  "Col2"=>array("Text"=>"$waitQty","RLText"=>"($waitCounts)","RLColor"=>"#BBBBBB",
		                                      "BelowText"=>array("Text"=>"$waitOverQty","Color"=>"#FF0000")),
		                  "Col3"=>array("Text"=>"¥$waitAmount","Margin"=>"$AmountMargin",
		                                      "BelowText"=>array("Text"=>"$waitOverAmount","Color"=>"#FF0000")),
		                   "AddIcon"=>$AddIcon 
		               );
 $jsonArray[]=array("Tag"=>"Week","onTap"=>"1","hidden"=>"1","data"=>$tempArray); 
}
 
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];

$mySql="SELECT M.CompanyId,COUNT(*) AS Counts,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price) AS Amount,SUM(S.Qty*S.Price*D.Rate) AS RmbAmount,C.Forshort,D.PreChar,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks  
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
		    WHERE S.Estate=1   $SearchCompany  GROUP BY YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1) ORDER BY Weeks";
		    //echo $mySql;
    $viewHidden=1;
    $myResult = mysql_query($mySql,$link_id);
    while($myRow = mysql_fetch_assoc($myResult))
    {
                   
                   $Weeks=$myRow["Weeks"];
                   $Qty=$myRow["Qty"];	
                   $Amount=$myRow["Amount"];	
                   $PreChar=$myRow["PreChar"];	
                   $Counts=$myRow["Counts"];	
                   
                if ($Weeks>0){
					     $year=substr($Weeks, 0,4);
					     $week=substr($Weeks, 4,2);
					     $dateArray= GetWeekToDate($Weeks,"m/d");
					     
					     $weekName="Week " . $week;
					     $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
					     
					     $nameColor="";
					     $weekColor=$curWeeks>$Weeks?"#FF0000":"";
				          $rowColor=$curWeeks==$Weeks?$CURWEEK_BGCOLOR:"#FFFFFF";
				          
				           //已备料数量
		                 $blResult=mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS Qty FROM (
							SELECT M.CompanyId,S.Id,S.POrderId,S.ProductId,S.Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty 
							FROM   (SELECT S.* FROM $DataIn.yw1_ordersheet S WHERE S.scFrom>0 AND S.Estate=1)S 
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
										 WHERE  S.scFrom>0 AND S.Estate=1 AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)='$Weeks'  and L.Estate=0 GROUP BY L.StockId
									 ) L ON L.StockId=G.StockId
							WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2 $SearchCompany  AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)='$Weeks'   
							AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
							 GROUP BY S.POrderId 
							) A  WHERE  A.blQty=A.llQty ",$link_id));
					
		               $blQty=$blResult["Qty"]==""?"":number_format($blResult["Qty"]);
		     }
		     else{
		           $Weeks="TBC";
		           $week="00";
			       $weekName="待定"; $dateSTR="  交期待定"; 
			       $nameColor="#0000FF";$weekColor="";
			       $rowColor="#FFFFFF";
			       $blQty="";
		     }
                   
                   $sumQty=number_format($Qty);
				   $sumAmount=number_format($Amount);
		           $tempArray=array(
		                  "Id"=>"$CompanyId",
		                   "RowSet"=>array("bgColor"=>"$rowColor"),
		                  "onTap"=>array("Target"=>"List1","Args"=>"$checkCompanyId|$Weeks"),
		                  "Col1"=>array("Week"=>"$week","BelowText"=>array("Text"=>"$dateSTR"),"bgColor"=>"$weekColor"),
		                  "Col2"=>array("Text"=>"$sumQty","Color"=>"$weekColor","RLText"=>"($Counts)","RLColor"=>"#BBBBBB",
		                                  "BelowText"=>array("Text"=>"$blQty","Color"=>"#3888B6","RIcon"=>"ibl_r")),
		                  "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"$AmountMargin")
		               );
		          $jsonArray[]=array("Tag"=>"Week","onTap"=>"1","hidden"=>"$viewHidden","data"=>$tempArray); 
      }
?>