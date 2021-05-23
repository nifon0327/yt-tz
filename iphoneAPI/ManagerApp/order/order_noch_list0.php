<?php 
//未出按客户显示明细
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

 $SearchWeek=$checkWeek>0 ?" AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$checkWeek'":"";
 $SearchWeek=$checkWeek=="TBC" ?" AND  IFNULL(PI.LeadWeek,0)=0 AND  IFNULL(PL.LeadWeek,0)=0 ":$SearchWeek;

 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];

$mySql="SELECT M.CompanyId,COUNT(*) AS Counts,SUM(S.Qty-S.shipQty) AS Qty,SUM((S.Qty-S.shipQty)*S.Price) AS Amount,
            SUM((S.Qty-S.shipQty)*S.Price*D.Rate) AS RmbAmount,C.Forshort,D.PreChar,
            SUM(IF(E.Type=2,1,0)) AS Locks,SUM(IF(E.Type=2,(S.Qty-S.shipQty),0)) AS LockQty 
			FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate=1 GROUP BY S.POrderId
            )S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
             LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
             LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
		    WHERE S.Estate=1   $SearchWeek  GROUP BY M.CompanyId ORDER BY RmbAmount DESC";
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
                  $iLock=$myRow["Locks"]>0?1:0;	
                  
                   $sumQty=number_format($Qty);
				   $sumAmount=number_format($Amount);
				   
			 //已备料数量
		     $blResult=mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS Qty FROM (
							SELECT S.CompanyId,S.Id,S.POrderId,S.ProductId,(S.Qty-S.shipQty) AS Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty 
							FROM   (SELECT S.*,M.CompanyId,SUM(IFNULL(C.Qty,0)) AS shipQty 
							              FROM $DataIn.yw1_ordersheet S 
							              LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
							              LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
							              WHERE S.scFrom>0 AND S.Estate=1 AND M.CompanyId='$CompanyId'  GROUP BY S.POrderId
							              )S 
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
										 WHERE  S.scFrom>0 AND S.Estate=1 $SearchWeek  GROUP BY L.StockId
									 ) L ON L.StockId=G.StockId
							WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  $SearchWeek  
							AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
							 GROUP BY S.POrderId 
							) A  WHERE  A.blQty=A.llQty ",$link_id));
				  $blQty=	$blResult["Qty"];
				  $iStock=$blQty>0?1:0;
				  
				  $iRemark=0;
				  if ($checkWeek>0){
	                          $RemarkResult=mysql_query("SELECT R.Id,R.Remark  
	                           FROM $DataIn.yw1_ordermain M
			                   LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
			                   LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
                               LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId
			                   LEFT JOIN  $DataIn.yw2_orderremark R ON R.POrderId=S.POrderId 
				              WHERE  M.CompanyId='$CompanyId' AND S.Estate=1  AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)='$checkWeek'  AND R.Type=2   ORDER BY R.Id DESC",$link_id);
				            if($RemarkRow = mysql_fetch_assoc($RemarkResult)){
					            $iRemark=$RemarkRow["Remark"]==""?0:1;
				            }
				            //$iRemark=mysql_num_rows($RemarkResult)>0?1:0;      
				  }
				  
				  $AddArray=array();
			     if ($blQty>0 || $LockQty>0){
				        $LockQty=$LockQty>0?number_format($LockQty):"";
				        $blQty=$blQty>0?number_format($blQty):"";
			           $AddArray= array(
					                     array("Text"=>"$blQty","ColName"=>"Col1","Color"=>"#3888B6","RIcon"=>"ibl_r"),
					                     array("Text"=>"$LockQty","ColName"=>"Col3","Color"=>"#FF0000","RIcon"=>"ilock_r")
					                     );
					}

		           $tempArray=array(
		                  "Id"=>"$CompanyId",
		                  "onTap"=>array("Target"=>"List1","Args"=>"$checkWeek|$CompanyId"),
		                  "Title"=>array("Text"=>"$Forshort","Color"=>"$TITLE_GRAYCOLOR"),
		                  "Col1"=>array("Text"=>"$sumQty","RLText"=>"($Counts)","RLColor"=>"#BBBBBB","Margin"=>"26,0,0,0"),
		                  "Col3"=>array("Text"=>"$PreChar$sumAmount","Margin"=>"$AmountMargin"),
		                  "AddRows"=>$AddArray, "iRemark"=>"$iRemark"
		                 // "iStock"=>"$iStock",
						 // "iLock"=>"$iLock",
		               );
		          $jsonArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"$viewHidden","data"=>$tempArray); 
      }
?>