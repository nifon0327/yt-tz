<?php   
//EWEN 2012-10-05 可备料订单数
$Sum_blQty=0;$Sum_OverQty=0;$blCounts=0;
$curDate=date("Y-m-d");
$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
$nextWeek=$dateResult["NextWeek"];
$SearchRows212=" AND  YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)<='$nextWeek' ";
//TIMESTAMPDIFF(minute,B.ableDate,Now())>360
$mySql212="SELECT S.POrderId,S.ProductId,S.Qty,IF(IFNULL(PI.LeadWeek,PL.LeadWeek)<YEARWEEK(CURDATE(),1),S.Qty,0) AS OverQty,E.Type,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,M.CompanyId,IFNULL(PI.LeadWeek,PL.LeadWeek) AS Weeks         
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN  $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.ck_bldatetime B ON B.POrderId=S.POrderId  
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
WHERE  S.scFrom>0 AND S.Estate=1  $SearchRows212  GROUP BY S.POrderId ORDER BY Weeks,POrderId";
//echo $mySql212;
$myResult212 = mysql_query($mySql212,$link_id);
if($myRow212 = mysql_fetch_array($myResult212)){
     $ProductArray=array();
   do{
          $POrderId=$myRow212["POrderId"];
          $ProductId=$myRow212["ProductId"];
          
           $R_EType=$myRow212["Type"]==2?2:0;
       
        		/*
        $ProductArray[]=$ProductId;
        //检查订单备料情况
		$CheckblState=mysql_query("SELECT * FROM (
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(IF(L.llEstate>0,0,L.Qty)),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks 
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
				WHERE G.POrderId='$POrderId' AND ST.mainType<2 ) A 
				WHERE A.K1>=A.K2 AND A.blQty!=A.llQty  AND A.Locks=0",$link_id);
		if (mysql_num_rows($CheckblState)<=0)	 continue;
        */
         //检查订单备料情况
		$CheckblState=mysql_query("
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate  
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
				    FROM   $DataIn.ck5_llsheet L
				    WHERE  L.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2 
				AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')",$link_id);
				//SELECT * FROM () A WHERE A.K1>=A.K2 AND A.blQty!=A.llQty  AND A.Locks=0
		        //if (mysql_num_rows($CheckblState)<=0)	 continue;
		       
		 if($blStateRow = mysql_fetch_array($CheckblState)){
		      $R_K1=$blStateRow["K1"];
		      $R_K2=$blStateRow["K2"];
			  $R_blQty=$blStateRow["blQty"];
			  $R_llQty=$blStateRow["llQty"];
			  $R_Locks=$blStateRow["Locks"];
			  $R_llEstate=$blStateRow["llEstate"];$R_llEstate=0;
			   
			 if ($R_blQty==$R_llQty){
				        if ($R_llEstate==0) continue;//&& $R_EType==0
			  }
			 else{
			            //是否已存在有可备料订单
		              if (in_array($ProductId,$ProductArray))continue;
		              if ($R_EType==2) continue;
					  if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0){
						    $ProductArray[]=$ProductId;   
					  }
					  else{
						    $ProductArray[]=$ProductId; continue;
					  }
			  }
		 }  
		$blCounts++;
		$Sum_blQty+=$myRow212["Qty"];
		$Sum_OverQty+=$myRow212["OverQty"];
       }while($myRow212 = mysql_fetch_array($myResult212));
 }

 $temp_C212=round($Sum_blQty/1000,0);
 $iPhone_C212=$Sum_blQty;
 $OverQty_C212=$Sum_OverQty;
$tmpTitle="<font color='red'>$temp_C212"."k</font>";
?>