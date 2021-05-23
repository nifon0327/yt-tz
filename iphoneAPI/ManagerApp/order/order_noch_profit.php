<?php 
//数据统计-未出订单 
   
    $jsonArray = array();
    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
     if($TRow = mysql_fetch_array($TResult)){
          $isPrice=1;
      }
      else{
	      $isPrice=0;
     }

//未出货订单总额
$noshipResult = mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 and S.Estate>'0'",$link_id);
if($noshipRow = mysql_fetch_array($noshipResult)) {
	$AllOrderAmount=sprintf("%.0f",$noshipRow["Amount"]);
	$AllOrderQty=$noshipRow["Qty"];
}
$noProfitResult = mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND A.Level=1 AND S.Estate>'0'",$link_id);
if($noProfitRow = mysql_fetch_array($noProfitResult)) {
	$AllProfitAmount=sprintf("%.0f",($AllOrderAmount-$noProfitRow["oTheCost"]));
}

if ($AllOrderAmount>0){
	$AllPC=sprintf("%.0f",($AllProfitAmount/$AllOrderAmount)*100);
	$AllProfitSTR=number_format($AllProfitAmount);
	$AllOrderQty=number_format($AllOrderQty);
}

if ($dModuleId==1232){
	    $sumdataArray=array();
		$sumdataArray[]=array(
			            "View"=>"Total",
			            "Id"=>"$ModelId",
			             "RowSet"=>array("bgColor"=>"#FFFFFF"),
			            "onTap"=>array("Title"=>"未出订单金额","Value"=>"1","Tag"=>"Chart","Args"=>"ChartA"),
			            "Col_A"=>array("Title"=>"","Icon"=>"statics","Align"=>"L"),
			            "Col_B"=>array("Title"=>"$AllOrderQty","Align"=>"R"),
			            "Col_C"=>array("Title"=>"¥" . number_format($AllOrderAmount),"Align"=>"R","Margin"=>"10,0,0,0",
			            "AboveTitle"=>"$AllPC%(¥$AllProfitSTR)","AboveColor"=>"#00A945")
			     );
      $SegmentArray=array();
     $jsonArray[]=array( "GroupName"=>"","Segmented"=>$SegmentArray,"Data"=>$sumdataArray); 
}
else{
		//按周以PI交期分组读取未出订单
		$dataArray=array();
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
		$curWeeks=$dateResult["CurWeek"];
		//$curWeeks=date("Y") . date('W');
		$WeekResult = mysql_query("
			 SELECT YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)  AS Weeks,Count(*) AS Counts,SUM( S.Qty ) AS Qty,SUM(IF(S.Estate>1,S.Qty,0) ) AS WaitQty, SUM( S.Price * S.Qty * D.Rate ) AS Amount,SUM(IF(E.Type=2,S.Qty,0) ) AS LockQty 
		     FROM $DataIn.yw1_ordersheet S
		     LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber = M.OrderNumber
		     LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		     LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
		     LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
		     LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
		    WHERE S.Estate >0  GROUP BY YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) ORDER BY Weeks 
		",$link_id);//AND Year(substring(PI.Leadtime,1,10))>0
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
			     
			     if ($Weeks>0){
				     $year=substr($Weeks, 0,4);
				     $week=substr($Weeks, 4,2);
				     $dateArray= GetWeekToDate($Weeks,"m/d");
				     
				     $weekName="Week " . $week;
				     $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
				     
				     $nameColor="";
				     $weekColor=$curWeeks>$Weeks?"#FF0000":"#888888";
			          $rowColor=$curWeeks==$Weeks?"#CCFF99":"#FFFFFF";
			     }
			     else{
			           $Weeks="TBC";
				       $weekName="待定"; $dateSTR=""; 
				       $nameColor="#0000FF";$weekColor="#888888";
				       $rowColor="#FFFFFF";
			     }
			     
			     $WaitQty=$WaitQty>0?number_format($WaitQty):"";
			     
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
								WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)='$Weeks'   
								AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
								 GROUP BY S.POrderId 
								) A  WHERE  A.blQty=A.llQty ",$link_id));
						
			      $blQty=$blResult["Qty"]==""?0:$blResult["Qty"];
			      $SumblQty+=$blQty;
			     
			    
			     $AddArray=array();
			     if ($blQty>0 || $LockQty>0){
				        $LockQty=$LockQty>0?number_format($LockQty):"";
				        $blQty=$blQty>0?number_format($blQty):"";
				        //,"Align"=>"R","FontSize"=>"13","Margin"=>"-30,0,0,0",
			           $AddArray= array(
					                     array("Title"=>"$blQty","Copy"=>"Col_B","Color"=>"#00A945","IconType"=>"13"),
					                     array("Title"=>"$LockQty","Copy"=>"Col_C","Color"=>"#FF0000","IconType"=>"12")
					                     );
					}
			     $dataArray[]=array(
			            "View"=>"List",
			            "Id"=>"WeekList",
			            "RowSet"=>array("bgColor"=>"$rowColor"),
			            "onTap"=>array("Title"=>"$weekName","Value"=>"1","Tag"=>"wList","Args"=>"$Weeks"),
			            "Col_A"=>array("Title"=>"$weekName","Color"=>"$nameColor","BelowTitle"=>"$dateSTR","BelowColor"=>"#AAAAAA","Align"=>"L"),
			            "Col_B"=>array("Title"=>"$WeekQty","Color"=>"$weekColor","AboveTitle"=>"$WaitQty","AboveColor"=>"#00A945","Margin"=>"0,0,20,0",
			                                       "RightTitle"=>"($Counts)","RightColor"=>"#000000","Align"=>"R"),
			            "Col_C"=>array("Title"=>"¥$WeekAmount","Align"=>"R"),
			            "AddRow"=>$AddArray
			     );
		 }
		 
		
		 $AddArray=array();
		 if ($SumblQty>0 || $SumLockQty>0 || $SumOverQty>0){
		        $SumLockQty=$SumLockQty>0?number_format($SumLockQty):"";
		        $SumblQty=$SumblQty>0?number_format($SumblQty):"";
		        $SumOverQty=$SumOverQty>0?number_format($SumOverQty):"";
		        
		       $AddArray= array(
		                         array("Title"=>"$SumLockQty","Copy"=>"Col_A","Color"=>"#FF0000","IconType"=>"12",
		                                   "Align"=>"R","FontSize"=>"13","Margin"=>"-40,0,0,0"),
			                     array("Title"=>"$SumblQty","FontSize"=>"13","Copy"=>"Col_B","Color"=>"#00A945","IconType"=>"13"),
			                     array("Title"=>"$SumOverQty","FontSize"=>"13","Copy"=>"Col_C","Color"=>"#FF0000","IconType"=>"1")
			                     );
			}
		$sumdataArray=array();
		$sumdataArray[]=array(
			            "View"=>"Total",
			            "Id"=>"$ModelId",
			             "RowSet"=>array("bgColor"=>"#FFFFFF"),
			            "onTap"=>array("Title"=>"未出订单金额","Value"=>"1","Tag"=>"Chart","Args"=>"ChartA"),
			            "Col_A"=>array("Title"=>"","Icon"=>"statics","Align"=>"L"),
			            "Col_B"=>array("Title"=>"$AllOrderQty","Align"=>"R"),
			            "Col_C"=>array("Title"=>"¥" . number_format($AllOrderAmount),"Align"=>"R","Margin"=>"10,0,0,0",
			            "AboveTitle"=>"$AllPC%(¥$AllProfitSTR)","AboveColor"=>"#00A945"),
			            "AddRow"=>$AddArray
			     );
		
		 $SegmentArray=array();
		$jsonArray[]=array( "GroupName"=>"","Segmented"=>$SegmentArray,"Data"=>$sumdataArray); 
		$jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
 }

 $dataArray=array();
//读取未出订单
$ShipResult = mysql_query("
	SELECT M.CompanyId,C.Forshort,C.Currency,D.Rate,SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS Amount
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	WHERE S.Estate>'0'  GROUP BY M.CompanyId  ORDER BY  Amount DESC
",$link_id);
$TotalAmount=0;$TotalQty=0;$ProfitTotal=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
				$CompanyId=$ShipRow["CompanyId"];
				$Forshort=$ShipRow["Forshort"];
				$Rate=$ShipRow["Rate"];
				$Currency=$ShipRow["Currency"];
				$Qty=$ShipRow["Qty"];
				$TotalQty+=$Qty;
				$Amount=$ShipRow["Amount"];
				$TotalAmount+=$Amount;
				
				$cbResult = mysql_fetch_array(mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost
					FROM   $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.cg1_stocksheet A ON A.POrderId=S.POrderId 
					LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
					LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId
					LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency 	
					WHERE  M.CompanyId='$CompanyId' AND S.Estate>'0'",$link_id));
				$cbAmount=$cbResult["oTheCost"];
		      
		       
		       if ($AllOrderAmount>0){
			       $OrderPC=($Amount/$AllOrderAmount)*100;
			       $OrderPC_1=$OrderPC>=1?sprintf("%.1f",$OrderPC) . "%":"";
				   //$OrderPC=$OrderPC>=1?"(" . sprintf("%.1f",$OrderPC) . "%)":"";
		       }
		       else{
			       $OrderPC_1="";
		       }
		       
		      if ($AllProfitAmount>0){
			       $TempPC=(($Amount-$cbAmount)/$AllProfitAmount)*100;
			       $TempPC_Color=$TempPC>$OrderPC?"#00A945":"#FF0000";
			       $TempPC_1=$TempPC>=1? sprintf("%.1f",$TempPC) . "%":"";
				   //$TempPC=$TempPC>=1?"(" . sprintf("%.1f",$TempPC) . "%)":"";
		       }else{
			       $TempPC_1="";   
		       }
		       
		       if ($Amount>0){
		           $sumProfitRMB=number_format($Amount-$cbAmount);
			        $profitRMB2PC=(($Amount-$cbAmount)/$Amount)*100;
			        /*
			        $profitColor=$profitRMB2PC>=15?"#FF7E1C":"#FF0000";
			        $profitColor=$profitRMB2PC>=20?"#00A945":$profitColor;
			       */
			       $profitColor=$profitRMB2PC>$AllPC?"#00A945":"#FF0000";
			        $profitRMB2PC_1=$profitRMB2PC>=1? sprintf("%.1f",$profitRMB2PC) . "%":"";
				   // $profitRMB2PC=$profitRMB2PC>=1?"(" . sprintf("%.1f",$profitRMB2PC) . "%)":"";
				   
		       }else{
			       $profitRMB2PC_1=""; 
			       $sumProfitRMB="";
		       }

		       
		       $Qty=number_format($Qty);
		       $Amount=number_format($Amount);
		       $dataArray[]=array(
			            "View"=>"List",
			            "Id"=>"Week",
			             "RowSet"=>array("bgColor"=>"#FFFFFF"),
			            "onTap"=>array("Title"=>"$Forshort","Value"=>"1","Tag"=>"Week","Args"=>"$CompanyId"),
			            "Col_A"=>array("Title"=>"$Forshort","Align"=>"L"),//"AboveTitle"=>"$TempPC","AboveColor"=>"#000000",
			            "Col_B"=>array("Title"=>"$Qty","Align"=>"R"),//,"AboveTitle"=>"$OrderPC","AboveColor"=>"#000000"
			            "Col_C"=>array("Title"=>"¥$Amount","Align"=>"R"),//,"AboveTitle"=>"$profitRMB2PC","AboveColor"=>"$profitColor"
			            "BelowLine"=>array(
			                     array("Title"=>"$OrderPC_1","Color"=>"#000000","Icon"=>"preicon_1"),
			                     array("Title"=>"$TempPC_1","Color"=>"$TempPC_Color","Icon"=>"preicon_2"),
			                     array("Title"=>"$profitRMB2PC_1","Color"=>"$profitColor","Icon"=>"preicon_3",
			                               "RightTitle"=>" (¥$sumProfitRMB)","RightColor"=>"#CCCCCC")
			                     )
			    );
           $i++;
      }while ($ShipRow = mysql_fetch_array($ShipResult));
      $jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
}
?>