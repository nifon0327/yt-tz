<?php 
//外发备料 versionToNumber
$SumTotalValue=$overQty=$blCounts=0;
$curDate=date("Y-m-d");
		//$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7day"));
		$nexNextWeekDate = date("Y-m-d",strtotime("$curDate  +14day"));
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek, YEARWEEK('$nexNextWeekDate',1) AS nextNextWeek",$link_id));
		//$nextWeek=$dateResult["NextWeek"];
		$thisWeek = $dateResult["ThisWeek"];
		$nextNextWeek = $dateResult["nextNextWeek"];	
		$SearchRows = " AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) < $nextNextWeek";
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek",$link_id));
		$thisWeek = $dateResult["ThisWeek"];
	$myOrderSql="SELECT S.POrderId,S.ProductId,S.Qty,S.DeliveryDate,PI.PI,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
		Left Join $DataIn.yw3_pileadtime PL On PL.POrderId = S.POrderId
		LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId  
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		WHERE S.scFrom>0 AND S.Estate=1 
		$SearchRows
		GROUP BY S.POrderId ";
		
		$myOrderResult = mysql_query($myOrderSql);
		$rowCount = $OverTotalQty = $totalQty = 0;
		while($myOrderRow = mysql_fetch_assoc($myOrderResult))
	{
		$mainQty=$myOrderRow["Qty"];
		$POrderId = $myOrderRow["POrderId"];
		$Leadtime = $myOrderRow['Leadtime'];
		if ($Leadtime == "") { continue;}
		$piDate = str_replace("*", "", $Leadtime);
		$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
			$Weeks = $piWeekResult["Week"];
	
		
		$hasLine = "";
			$missionPartSql = "Select B.GroupName From $DataIn.sc1_mission A
							   Left Join $DataIn.staffgroup B On B.Id = A.Operator
							   Where A.POrderId = '$POrderId'
							   And B.Estate = '1' Limit 1";
			$missionPartResult = mysql_query($missionPartSql);
			if($missionRow = mysql_fetch_assoc($missionPartResult))
			{
				$line = $missionRow["GroupName"];
				$hasLine = str_replace("组装", "", $line);
			}		
			if ($hasLine=="") { continue;
			}
		//检查订单备料情况
			$CheckblState="
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate  
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
				    FROM  $DataIn.cg1_stocksheet G 
				    LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
				    WHERE  G.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2";
			
			$stockHead = mysql_query($CheckblState);
			if($mainRows = mysql_fetch_assoc($stockHead))
			{
			
				$mainBlQty = $mainRows["blQty"];
				$mainLlQty = $mainRows["llQty"];
				if($mainBlQty != $mainLlQty)
				{
					continue;
				}
				else
				{
					
				}
				$R_K1 = $mainRows["K1"];
				$R_K2 = $mainRows["K2"];
				$R_blQty = $mainRows["blQty"];
				$R_llQty = $mainRows["llQty"];
				$R_Locks = $mainRows["Locks"];
				$R_llEstate = $mainRows["llEstate"];
				
				  $FromWebPage=$R_blQty==$R_llQty?"LBL":"KBL";
	             include "../../admin/order_datetime.php";
				 
				  $BlDate=$R_blQty==$R_llQty?$lbl_Date:$kbl_Date;
				  
				if ($R_blQty==$R_llQty)
				{
					if ($R_llEstate==0)
					{
						continue;
					} 
			    }
				else
				{	
			        if ($R_EType==2) 
			        {
			        continue;}
			        
					if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0)
					{
					   
					}
					else
					{
						continue;
					}
			    }
			}
		$overQty +=  $thisWeek > $Weeks ? $mainQty:0;
		$SumTotalValue += $mainQty;
		$blCounts ++;
	}	
		$overQty = number_format($overQty);
	if ($overQty <= 0 ) {$overQty = "";}
	$SumTotalValue = number_format($SumTotalValue);
?>