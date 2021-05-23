<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class CkllsheetModel  
* 仓库领料记录
* 
*/ 
class  CkllsheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
	
		
	//返回指定Id的记录
	function get_records($Id,$StockId='')
	{
	   if ($Id>0){
		      $sql = "SELECT * FROM ck5_llsheet WHERE Id=?"; 	
	          $query=$this->db->query($sql,$Id);
	   }
	   else{
		      $sql = "SELECT POrderId,sPOrderId,StockId,StuffId,Price,SUM(Qty) AS Qty,Type FROM ck5_llsheet WHERE StockId=?"; 	
	          $query=$this->db->query($sql,$StockId);
	   }
	   return  $query->first_row('array');
	}
	
	//统计工单领料数（按流水号）
	function get_stock_llqty($sPOrderId,$StockId)
	{
	   $sql = "SELECT IFNULL(SUM(Qty),0) AS Qty FROM ck5_llsheet WHERE sPOrderId=? AND StockId=?"; 	
	   $query=$this->db->query($sql,array($sPOrderId,$StockId));
	   $rows=$query->first_row('array');
	   
	   return  $rows['Qty'];
	}
	
	//统计日领料金额
	function get_day_llamount($date='')
	{
	    $date=$date==''?$this->Date:$date;
	    
	    $sql = "SELECT  SUM(S.Qty*D.Price*C.Rate) AS Amount 
					FROM ck5_llsheet S 
					INNER JOIN stuffdata D ON D.StuffId=S.StuffId
					INNER JOIN bps B ON B.StuffId=D.StuffId 
					INNER JOIN trade_object P ON P.CompanyId=B.CompanyId 
					INNER JOIN currencydata C ON C.Id=P.Currency 
					WHERE S.Date=? AND S.Type=1"; 	
	   $query=$this->db->query($sql,array($date));
	   $rows=$query->first_row('array');
	   
	   return  $rows['Amount'];
	}
	
	
	//统计待备的工单的数量（按仓库）
	function get_canstock_qty($sendfloor)
	{
	    $this->load->model('WorkShopdataModel'); 
	    $this->load->model('basempositionModel');
	    
	    $dataArray=array();
	    
        $records      = $this->basempositionModel->get_records($sendfloor);
        $BlWorkShopId = $records['BlWorkShopId'];
        
        $blWorkShopIds = explode(',', $BlWorkShopId);
        $CanWeekStr    = '';
        $WorkShopIdStr = '';
            
        if (count($blWorkShopIds)>1){
	        //(CASE WHEN S.WorkShopId=1 THEN 1  WHEN S.WorkShopId=2 THEN 2   WHEN S.WorkShopId=3 THEN 3 ELSE 4 END)
	        foreach ($blWorkShopIds as $WorkShopId){
	            //取得可备料周数
	           
		        $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($WorkShopId);
		        
		        $CanWeekStr.=  " WHEN A.WorkShopId=$WorkShopId THEN  $canWeeks ";
		        $WorkShopIdStr.= $WorkShopIdStr ==''?$WorkShopId:',' . $WorkShopId;
	        }
	        $CanWeekStr    = " AND A.LeadWeek<=(CASE $CanWeekStr  END) ";
	        $WorkShopIdStr = " AND S.WorkShopId IN ($BlWorkShopId) " ;
        }
        else{
            $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($BlWorkShopId);
	        $CanWeekStr    = " AND A.LeadWeek<=$canWeeks ";
	        $WorkShopIdStr = " AND S.WorkShopId='$BlWorkShopId' ";
        }
        
        switch($BlWorkShopId){
	        case  '101'://组装备料
	            $StockSign = 2;  //已分配备料
	            $sql="SELECT COUNT(1) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty,
	                         SUM(IF(A.LeadWeek<YEARWEEK(CURDATE(),1),A.Qty,0)) AS OverQty,
	                         '0' AS countLocks
	                 FROM (
						SELECT S.POrderId,S.sPOrderId,S.mStockId,S.Qty,S.WorkShopId,IFNULL(PI.LeadWeek,PL.LeadWeek)  AS LeadWeek 
							FROM  yw1_scsheet    S 
                            INNER JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
                            LEFT  JOIN yw3_pisheet PI ON PI.oId=Y.Id
			             	LEFT  JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
							WHERE S.WorkShopId=101 and S.ScFrom>0 AND S.Estate>0  AND  S.scLineId>0 
							AND NOT EXISTS (SELECT POrderId FROM yw2_orderexpress WHERE  POrderId = Y.POrderId  AND Type='2') 
	                   )A 
	                   WHERE A.LeadWeek>0 AND getCanStock(A.sPOrderId,$StockSign)=$StockSign  ";
	                   /* AND EXISTS(  SELECT G.StuffId FROM cg1_stocksheet G 
                                         LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
                                         WHERE G.POrderId=A.POrderId AND G.Level=1 AND D.SendFloor='$sendfloor')
                        */
	           break;
	           
	        default:
	           $StockSign = 1; //待备料
	           $sql="SELECT COUNT(1) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty,
	                        SUM(IF(A.LeadWeek<YEARWEEK(CURDATE(),1),A.Qty,0)) AS OverQty,
	                        '0' AS countLocks  
	                   FROM (
						    SELECT S.POrderId,S.sPOrderId,S.mStockId,S.Qty,S.WorkShopId,G.DeliveryWeek AS LeadWeek 
							FROM  yw1_scsheet    S 
                            INNER JOIN cg1_stocksheet G ON G.StockId=S.mStockId 
							WHERE 1 $WorkShopIdStr  and S.ScFrom>0 AND S.Estate>0 AND G.DeliveryWeek>0 
							AND NOT EXISTS (SELECT POrderId FROM yw2_orderexpress WHERE  POrderId = S.POrderId  AND Type='2') 
							AND NOT EXISTS (SELECT StockId FROM cg1_lockstock WHERE StockId= S.mStockId ) 
	                     )A 
	                     WHERE A.LeadWeek>0 $CanWeekStr   AND getCanStock(A.sPOrderId,$StockSign)=$StockSign";
	               /*
		               		   AND EXISTS(   SELECT G.StuffId FROM cg1_semifinished G 
		                        LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
		                       WHERE G.mStockId=A.mStockId AND D.SendFloor='$sendfloor')
	               */
	           break;
        }
        
        $query=$this->db->query($sql);
/*
        if ($this->LoginNumber == 10868) {
	        echo($sql.'\n\n');
        }
*/
        
	    $row = $query->first_row('array');
        
        $dataArray=array(
                      'Counts'  => $row['Counts']-$row['countLocks'],
                      'realcount'=>$row['Counts'],
                      'Qty'     => $row['Qty'],
                      'OverQty' => $row['OverQty']
                  );
        
         return $dataArray;
	}
	
	//统计外发工单的数量（按仓库）
	function get_outward_blqty($sendfloor='')
	{
	     //关联外发
	     $SearchRows = $sendfloor>0?" AND D.SendFloor = '$sendfloor' ":"";
	     $sql="SELECT COUNT(1) AS Counts,SUM(A.Qty) AS Qty
	               FROM(
	                   SELECT B.* ,COUNT(if(IF(((B.tStockQty+B.llQty)>=B.blQty AND B.llQty<B.blQty),1,0) =0,true,null)) AS kblCount FROM (
						     SELECT G.StockId,(G.FactualQty + G.AddQty) AS Qty,(UG.OrderQty) AS blQty,
						     SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty  
					         FROM  cg1_stuffunite U
					         INNER JOIN yw1_ordersheet Y ON Y.POrderId = U.POrderId 
					         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
	                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
					         INNER JOIN stuffdata D ON D.StuffId = U.StuffId
					         INNER JOIN ck9_stocksheet K ON  K.StuffId=UG.StuffId  
	                         LEFT JOIN ck5_llsheet L ON L.StockId=UG.StockId 
					         WHERE 1 AND Y.Estate>0 AND G.StockId>0 AND G.Level = 1 AND G.Mid>0 $SearchRows 
	                         GROUP BY UG.StockId  
                         ) B  GROUP BY B.StockId 
                     )A WHERE A.kblCount=0 ";
                     
         $query=$this->db->query($sql);
	     $row = $query->first_row('array');
	     $Counts1 = $row['Counts'];
	     $Qty1    = $row['Qty'] ==''?0:$row['Qty'];
	     
	     $query  = null;  $row    = null;
	     //外发加工备料
	     $StockSign = 1; //待备料
         $sql2="SELECT COUNT(1) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty,COUNT(if(getStockIdLock(A.mStockId)>0,true,null)) AS countLocks
               FROM (
				 SELECT S.POrderId,S.sPOrderId,S.mStockId,S.Qty,S.WorkShopId,G.DeliveryWeek AS LeadWeek
					FROM  yw1_scsheet S 
                    INNER JOIN cg1_stocksheet G ON G.StockId=S.mStockId 
					WHERE S.ActionId=105 AND G.Mid=0 AND S.ScFrom>0 AND S.Estate>0   
                 )A 
                 WHERE A.LeadWeek>0 AND getCanStock(A.sPOrderId,$StockSign)=$StockSign 
                 AND EXISTS( SELECT G.StuffId FROM cg1_semifinished G 
                             LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
                             WHERE G.mStockId=A.mStockId $SearchRows)
                 AND NOT EXISTS (SELECT POrderId FROM yw2_orderexpress WHERE  POrderId = A.POrderId  AND Type='2')
                 AND NOT EXISTS (SELECT StockId FROM cg1_lockstock WHERE StockId= A.mStockId )
                 ";
	               
	     
	     $query=$this->db->query($sql2);
	     $row = $query->first_row('array');
	     $Counts2 = $row['Counts'];
	     $countLocksQty = $row['countLocks'];
	     $Qty2    = $row['Qty'] ==''?0:$row['Qty'];
	     
	     //锁定数量
	     
	     
	     
	     $dataArray=array(
                      'Counts'  => ($Counts1+$Counts2-$countLocksQty),
                      'Qty'     => ($Qty1+$Qty2),
                  );
        
         return $dataArray;
	}
	
	
	//统计补料的数量（按仓库）
	function get_feed_blqty($sendfloor){
		
	     $sql="SELECT COUNT(1) AS Counts,SUM(R.Qty) AS Qty 
				FROM ck13_replenish R 
				LEFT JOIN stuffdata D ON D.StuffId = R.StuffId
				WHERE R.Estate>0 AND D.SendFloor = '$sendfloor' ";
                     
         $query=$this->db->query($sql);
	     $row = $query->first_row('array');
	     $Counts = $row['Counts'];
	     $Qty    = $row['Qty'] ==''?0:$row['Qty'];
	     
		 $dataArray=array(
                      'Counts'  => $Counts,
                      'Qty'     => $Qty
                  );
        
         return $dataArray;    
	}
	
	//外发工单的按供应商分类
	function get_outward_comapny()
	{
		$checkBlSign  = 1;//可备料标识
		$ashComapnyId = $this->config->item('ash_in_supplier');
		$sql="SELECT A.CompanyId,A.Forshort,SUM(A.Counts-A.countLocks) AS Counts,SUM(A.Qty-A.locksQty) AS Qty 
            FROM (
	            SELECT  G.CompanyId,P.Forshort,COUNT(*) AS Counts,SUM(Qty) AS Qty,COUNT(if(getStockIdLock(S.mStockId)=1,true,null)) AS countLocks,SUM(if(getStockIdLock(S.mStockId)=1,Qty,0)) AS locksQty  
					FROM  yw1_scsheet S 
					INNER JOIN cg1_stocksheet  G ON G.StockId = S.mStockId
				    INNER JOIN  trade_object P ON P.CompanyId=G.CompanyId 
					WHERE S.scFrom=1 AND S.Estate>0 AND S.WorkShopId=0 AND S.ActionId = 105 
					AND G.CompanyId NOT IN($ashComapnyId)
					AND G.Mid=0 AND G.DeliveryWeek>0 AND getCanStock(S.sPOrderId,$checkBlSign)=$checkBlSign 
					AND NOT EXISTS (SELECT POrderId FROM yw2_orderexpress WHERE  POrderId = S.POrderId  AND Type='2')
                    AND NOT EXISTS (SELECT StockId FROM cg1_lockstock WHERE StockId= S.mStockId ) 
				    GROUP BY G.CompanyId 
		       UNION ALL 
	              SELECT  S.CompanyId,P.Forshort,COUNT(*) AS Counts,SUM(S.Qty) AS Qty,'0' AS countLocks,'0' AS locksQty  
	               FROM(
	                   SELECT A.* ,COUNT(if(IF(((A.tStockQty+A.llQty)>=A.blQty AND A.llQty<A.blQty),1,0) =0,true,null)) AS kblCount FROM (
						     SELECT G.StockId,G.CompanyId,(G.FactualQty + G.AddQty) AS Qty,(UG.OrderQty) AS blQty,
						     SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty  
					         FROM  cg1_stuffunite U
					         INNER JOIN yw1_ordersheet Y ON Y.POrderId = U.POrderId 
					         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
	                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
					         INNER JOIN stuffdata D ON D.StuffId = U.StuffId
					         INNER JOIN ck9_stocksheet K ON  K.StuffId=UG.StuffId   
	                         LEFT JOIN ck5_llsheet L ON L.StockId=UG.StockId 
					         WHERE 1 AND Y.Estate>0 AND G.StockId>0 AND G.Level = 1 AND G.Mid>0  AND G.CompanyId NOT IN($ashComapnyId)                          
                             GROUP BY UG.StockId  
                         ) A  GROUP BY A.StockId 
                     )S 
				 INNER JOIN  trade_object P ON P.CompanyId=S.CompanyId 
				WHERE S.kblCount=0 GROUP BY S.CompanyId 
		    )A GROUP BY A.CompanyId ";
			   
		$query=$this->db->query($sql);
		return $query->result_array();  
	}
	
	//外发工单的订单明细
	function get_outward_ordersheet($CompanyId)
	{
	    $checkBlSign=1;//可备料标识
	    
		$sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,D.Picture,
		      IFNULL(G.created,'') AS created,U.Decimals,IFNULL(B.abledate,'') AS bltime  
				FROM  yw1_scsheet S 
				INNER JOIN cg1_stocksheet  G ON G.StockId = S.mStockId
			    INNER JOIN stuffdata D ON D.StuffId=G.StuffId   
			    INNER JOIN  stuffunit U ON U.Id=D.Unit 
			    LEFT JOIN ck_bldatetime B ON B.sPOrderId=S.sPOrderId 
				WHERE  S.scFrom=1 AND S.Estate>0  AND S.ActionId = 105 AND G.CompanyId='$CompanyId' 
			          AND G.DeliveryWeek>0 AND getCanStock(S.sPOrderId,$checkBlSign)=$checkBlSign 
	     UNION ALL
	            SELECT  '0' AS sPOrderId,S.StockId AS mStockId,S.Qty,
                 S.POrderId,S.StockId,S.StuffId,S.DeliveryWeek,D.StuffCname,D.Picture,IFNULL(S.created,'') AS created,U.Decimals,IFNULL(S.created,'') AS bltime    
	               FROM(
	                   SELECT A.* ,COUNT(if(IF(((A.tStockQty+A.llQty)>=A.blQty AND A.llQty<A.blQty),1,0) =0,true,null)) AS kblCount FROM (
						     SELECT G.POrderId,G.StockId,G.StuffId,(G.FactualQty + G.AddQty) AS Qty,G.DeliveryWeek,G.created,(UG.OrderQty) AS blQty,SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty  
					         FROM  cg1_stuffunite U
					         INNER JOIN yw1_ordersheet Y ON Y.POrderId = U.POrderId 
					         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
	                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
	                         INNER JOIN ck9_stocksheet K ON  K.StuffId=UG.StuffId    
	                         LEFT JOIN ck5_llsheet L ON L.StockId=UG.StockId 
					         WHERE 1 AND Y.Estate>0 AND G.StockId>0 AND G.Level = 1 AND G.Mid>0  AND G.CompanyId='$CompanyId'  
	                         GROUP BY UG.StockId  
                         ) A  GROUP BY A.StockId 
                     )S 
                INNER JOIN stuffdata D ON D.StuffId = S.StuffId 
                INNER JOIN  stuffunit U ON U.Id=D.Unit 
				WHERE S.kblCount=0  ORDER BY DeliveryWeek,StockId ";
			//echo $sql;	
		//锁定的未剔除		
		
		$query=$this->db->query($sql);
		$rowArray = $query->result_array();
		return $rowArray; 	 
		  
	}
	
	//关联外发备料配件明细
	function get_outward_unitsheet($mStockId)
	{
		
		$sql="SELECT S.POrderId,'0' AS sPOrderId,S.StockId AS mStockId,'1' AS scFrom,'1' AS Estate,P.Forshort,
		              G.StockId,G.StuffId,G.OrderQty,D.StuffCname,D.Picture,K.tStockQty,UT.Decimals,'1' AS blSign,
		              SUM(IFNULL(L.Qty,0)) AS llQty, SUM(IF(L.Estate=1,1,0)) AS llEstate  
				FROM cg1_stocksheet S 
				INNER JOIN cg1_stuffunite U ON U.POrderId=S.POrderId AND U.StuffId=S.StuffId 
				INNER JOIN cg1_stocksheet G ON G.POrderId=U.POrderId AND G.StuffId=U.uStuffId 
				INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
			    INNER JOIN stuffunit UT ON UT.Id=D.Unit 
				INNER JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
				INNER JOIN trade_object P ON P.CompanyId=G.CompanyId  
                LEFT JOIN ck5_llsheet L ON L.POrderId=S.POrderId  AND L.StuffId=U.uStuffId  
				WHERE S.StockId='$mStockId' GROUP BY G.StockId"; 
				
	    $query=$this->db->query($sql);
		return $query->result_array(); 
	}
	
	//统计当天备料情况
	function get_today_blcounts($sendfloor)
	{
	    $thisDate=$this->Date;
		switch($sendfloor){
			case 3://组装备料
			   $sql="SELECT COUNT(*) AS Counts,SUM(A.newCounts) AS newCounts,SUM(A.hourCounts) AS hourCounts 
			          FROM (
						SELECT B.sPOrderId,
						       IF(TIMESTAMPDIFF(minute,B.Received,NOW())<10,1,0) AS newCounts,
						       IF(TIMESTAMPDIFF(hour,B.Received,NOW())<1,1,0) AS hourCounts
						FROM(
						    SELECT A.sPOrderId,IF(A.Received='0000-00-00 00:00:00',A.created,A.Received) AS Received  
                            FROM (
						     SELECT L.StockId,L.POrderId,L.sPOrderId,MAX(L.Received)  AS Received,MAX(L.created) AS created  
						     FROM ck5_llsheet L 
						     INNER JOIN yw1_scsheet S ON S.sPOrderId=L.sPOrderId 
                             INNER JOIN  sc1_mission T ON T.sPOrderId=S.sPOrderId    
					     	WHERE DATE_FORMAT(IF(L.Received='0000-00-00 00:00:00',L.created,L.Received),'%Y-%m-%d')='$thisDate'   AND L.Estate=0 
					     	            AND L.Type=1 AND S.ActionId=101  GROUP BY L.sPOrderId)A 
						)B  
						 GROUP BY B.sPOrderId
                      )A"; 
			 break;
		   case 17://半成品备料
		      $sql="SELECT COUNT(*) AS Counts,SUM(A.newCounts) AS newCounts,SUM(A.hourCounts) AS hourCounts 
		              FROM (
						SELECT B.POrderId,B.mStockId,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,
						       IF(TIMESTAMPDIFF(minute,B.created,NOW())<10,1,0) AS newCounts,
						       IF(TIMESTAMPDIFF(hour,B.created,NOW())<1,1,0) AS hourCounts   
						FROM(
						  SELECT A.POrderId,A.mStockId,A.sPOrderId,COUNT(*) AS llCounts,MAX(A.created) AS created 
						   FROM (
						    SELECT L.StockId,S.mStockId,L.POrderId,L.sPOrderId,L.created,G.OrderQty,SUM(L.Qty) AS llQty 
						     FROM ck5_llsheet L 
						     INNER JOIN yw1_scsheet S ON S.sPOrderId=L.sPOrderId 
                             INNER JOIN cg1_semifinished G ON G.mStockId=S.mStockId AND G.StockId=L.StockId 
						     INNER JOIN workshopdata D ON D.Id=S.WorkShopId  
						     WHERE DATE_FORMAT(L.created,'%Y-%m-%d')='$thisDate' AND L.Type=1 AND D.semiSign=1 GROUP BY L.sPOrderId,L.StockId 
                            )A  WHERE A.OrderQty=A.llQty GROUP BY A.sPOrderId
						)B 
						INNER JOIN cg1_semifinished G ON G.mStockId=B.mStockId   
						INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						INNER JOIN stuffmaintype M ON M.Id=T.mainType AND M.blSign=1 
						GROUP BY B.sPOrderId 
				  )A  WHERE A.llCounts>=A.blCounts ";
	          break;
	       case 0://外发备料
	          $sql="SELECT SUM(Counts) AS Counts,SUM(A.newCounts) AS newCounts,SUM(A.hourCounts) AS hourCounts 
	             FROM(
	               SELECT COUNT(*) AS Counts,SUM(A.newCounts) AS newCounts,SUM(A.hourCounts) AS hourCounts 
		              FROM (
						SELECT B.POrderId,B.mStockId,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,
						       IF(TIMESTAMPDIFF(minute,B.created,NOW())<10,1,0) AS newCounts,
						       IF(TIMESTAMPDIFF(hour,B.created,NOW())<1,1,0) AS hourCounts   
						FROM(
						  SELECT A.POrderId,A.mStockId,A.sPOrderId,COUNT(*) AS llCounts,MAX(A.created) AS created 
						   FROM (
						    SELECT L.StockId,S.mStockId,L.POrderId,L.sPOrderId,L.created,G.OrderQty,SUM(L.Qty) AS llQty 
						     FROM ck5_llsheet L 
						     INNER JOIN yw1_scsheet S ON S.sPOrderId=L.sPOrderId 
                             INNER JOIN cg1_semifinished G ON G.mStockId=S.mStockId AND G.StockId=L.StockId 
						     WHERE DATE_FORMAT(L.created,'%Y-%m-%d')='$thisDate' AND L.Type=1 AND S.ActionId=105   GROUP BY L.sPOrderId,L.StockId 
                            )A  WHERE A.OrderQty=A.llQty GROUP BY A.sPOrderId
						)B 
						INNER JOIN cg1_semifinished G ON G.mStockId=B.mStockId   
						INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						INNER JOIN stuffmaintype M ON M.Id=T.mainType AND M.blSign=1 
						GROUP BY B.sPOrderId 
				  )A  WHERE A.llCounts>=A.blCounts 
		   UNION ALL 
	            SELECT  COUNT(*) AS  blCounts,
						       IF(TIMESTAMPDIFF(minute,B.created,NOW())<10,1,0) AS newCounts,
						       IF(TIMESTAMPDIFF(hour,B.created,NOW())<1,1,0) AS hourCounts  
	               FROM(
					     SELECT S.POrderId,S.StockId,MAX(S.created) AS created 
	                    FROM(
								     SELECT G.POrderId,G.StockId,G.StuffId,L.created,(UG.OrderQty) AS blQty,SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty  
							         FROM  cg1_stuffunite U
							         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
			                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
			                         INNER JOIN ck9_stocksheet K ON  K.StuffId=UG.StuffId    
			                         LEFT JOIN ck5_llsheet L ON L.StockId=UG.StockId 
							         WHERE  G.Mid>0  AND DATE_FORMAT(L.created,'%Y-%m-%d')='$thisDate'
			                         GROUP BY G.StockId 
			                     )S 
			              INNER JOIN stuffdata D ON D.StuffId = S.StuffId 
					      WHERE S.blQty=S.llQty GROUP BY S.StockId
				   )B 
		     )A  ";
	          break;
		}
		$Counts = $newCounts = $hourCounts = 0;
		
		$query=$this->db->query($sql);
		if ($query->num_rows()>0) {
		      $rows      = $query->row_array();
		      $Counts    = $rows['Counts'];
		      $newCounts = $rows['newCounts'];
		      $hourCounts= $rows['hourCounts'];
		}
		
		$dataArray=array(
              'Counts'     => $Counts,
              'newCounts'  => $newCounts,
              'hourCounts' => $hourCounts
         );
		return $dataArray;
	}
	
	//按月统计备料数量
   function get_month_bledcounts($sendfloor)
   {
       switch($sendfloor){
			case 3://组装备料
			      $sql="SELECT  DATE_FORMAT(B.Received,'%Y-%m') AS Month,COUNT(*) AS Counts,
								       SUM(IF(TIMESTAMPDIFF(hour,B.ableDate,B.Received)>12,1,0)) AS oneCounts,
								       SUM(IF(TIMESTAMPDIFF(hour,B.ableDate,B.Received)>48,1,0)) AS twoCounts 
									  FROM(
                                            SELECT A.ableDate,IF(A.Received='0000-00-00 00:00:00',A.created,A.Received) AS Received 
                                                  FROM (
														    SELECT S.sPOrderId,MAX(T.DateTime) AS ableDate,MAX(L.Received)  AS Received,MAX(L.created) AS created 
														    FROM yw1_scsheet S
														    INNER JOIN  sc1_mission T ON T.sPOrderId=S.sPOrderId  
                                                            INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId AND L.Estate=0 
													     	WHERE  S.ActionId=101 GROUP BY S.sPOrderId
                                                    )A 
									)B GROUP BY DATE_FORMAT(B.Received,'%Y-%m')  ORDER BY Month DESC";
			   break;
			 case 17://半成品备料
			        $sql="SELECT B.Month,SUM(B.Counts) AS Counts,SUM(B.oneCounts) AS oneCounts,SUM(B.twoCounts) AS twoCounts
								FROM (
								      SELECT A.Month,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m') AS Month,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts
														FROM(
														    SELECT A.sPOrderId,A.mStockId,COUNT(*) AS llCounts,MAX(A.created) AS created  FROM (
														     SELECT L.StockId,L.POrderId,L.sPOrderId,S.mStockId,MAX(L.created) AS created 
														     FROM yw1_scsheet S
								                             INNER JOIN workshopdata D ON D.ActionId=S.ActionId 
														     INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId 
													     	WHERE S.scFrom>0 and  L.Type=1 AND D.semiSign=1 GROUP BY L.StockId)A GROUP BY A.sPOrderId
														)B 
                                                        INNER JOIN cg1_semifinished G ON G.mStockId=B.mStockId  
                                                        INNER JOIN stuffdata D ON D.StuffId=G.StuffId
                                                        INNER JOIN stufftype TY ON TY.TypeId=D.TypeId 
                                                        INNER JOIN stuffmaintype TM ON TM.Id=TY.mainType  
                                                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId
								                        WHERE TM.blSign=1 GROUP BY B.sPOrderId 
													)A 
								 WHERE A.llCounts>=A.blCounts  GROUP BY A.Month
								UNION  ALL
								SELECT A.Month,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m') AS Month,B.sPOrderId,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts 
														FROM(
														    SELECT S.sPOrderId,MAX(L.created) AS created 
														    FROM yw1_scsheet S
								                            INNER JOIN workshopdata D ON D.ActionId=S.ActionId
														    INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId                           
													     	WHERE S.scFrom=0 AND D.semiSign=1 GROUP BY S.sPOrderId
														)B 
								                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId  
								                        GROUP BY B.sPOrderId 
													)A  GROUP BY A.Month
								)B 
								 GROUP BY B.Month ORDER BY Month DESC ";
			   break;
			   case 0://外发备料
			       $sql="SELECT B.Month,SUM(B.Counts) AS Counts,SUM(B.oneCounts) AS oneCounts,SUM(B.twoCounts) AS twoCounts
								FROM (
								SELECT A.Month,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m') AS Month,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts
														FROM(
														    SELECT A.POrderId,A.mStockId,A.sPOrderId,COUNT(*) AS llCounts,MAX(A.created) AS created 
														   FROM (
														    SELECT L.StockId,S.mStockId,L.POrderId,L.sPOrderId,L.created,G.OrderQty,SUM(L.Qty) AS llQty 
														     FROM ck5_llsheet L 
														     INNER JOIN yw1_scsheet S ON S.sPOrderId=L.sPOrderId 
								                             INNER JOIN cg1_semifinished G ON G.mStockId=S.mStockId AND G.StockId=L.StockId 
														     WHERE  S.scFrom>0 AND L.Type=1 AND S.ActionId=105   GROUP BY L.sPOrderId,L.StockId 
								                            )A  WHERE A.OrderQty=A.llQty GROUP BY A.sPOrderId
														)B 
								                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId  
								                        GROUP BY B.sPOrderId 
													)A 
								 WHERE A.llCounts>=A.blCounts  GROUP BY A.Month
						UNION  ALL
								SELECT A.Month,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m') AS Month,B.sPOrderId,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts 
														FROM(
														    SELECT S.sPOrderId,MAX(L.created) AS created 
														    FROM yw1_scsheet S
														    INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId 
													     	WHERE S.scFrom=0 AND S.ActionId=105 GROUP BY S.sPOrderId
														)B 
								                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId  
								                        GROUP BY B.sPOrderId 
													)A   GROUP BY A.Month
							UNION ALL 
                               SELECT A.Month,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
								 FROM (SELECT  DATE_FORMAT(B.created,'%Y-%m') AS Month,
										IF(TIMESTAMPDIFF(hour,B.ableDate,B.created)>12,1,0) AS oneCounts,
										IF(TIMESTAMPDIFF(hour,B.ableDate,B.created)>48,1,0) AS twoCounts
						               FROM(
										     SELECT MAX(L.created) AS created,getStuffDeliveredMinTime(L.StuffId,L.created) AS ableDate  
									         FROM  cg1_stuffunite U
									         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
					                         INNER JOIN cg1_stockmain M  ON M.Id=G.Mid 
					                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
					                         INNER JOIN ck5_llsheet L ON L.StockId=UG.StockId 
									         WHERE  G.Mid>0
					                         GROUP BY G.StockId 
					                     )B  
                                     )A GROUP BY Month 
								)B 
						 GROUP BY B.Month ORDER BY Month DESC";
			   break;
		}
				
		$query=$this->db->query($sql);
		if ($query->num_rows()>0) {
		     return $query->result_array();
		}
		else{
			  return array();
		}
   }
   
   //按月统计备料数量
   function get_date_bledcounts($sendfloor,$month)
   {
       switch($sendfloor){
			case 3://组装备料
			      $sql="SELECT  DATE_FORMAT(B.Received,'%Y-%m-%d') AS Date,COUNT(*) AS Counts,
								       SUM(IF(TIMESTAMPDIFF(hour,B.ableDate,B.Received)>12,1,0)) AS oneCounts,
								       SUM(IF(TIMESTAMPDIFF(hour,B.ableDate,B.Received)>48,1,0)) AS twoCounts 
									  FROM(
                                            SELECT A.ableDate,IF(A.Received='0000-00-00 00:00:00',A.created,A.Received) AS Received 
                                                  FROM (
														    SELECT S.sPOrderId,MAX(T.DateTime) AS ableDate,MAX(L.Received)  AS Received,MAX(L.created) AS created 
														    FROM yw1_scsheet S
														    INNER JOIN  sc1_mission T ON T.sPOrderId=S.sPOrderId  
                                                            INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId AND L.Estate=0 
													     	WHERE  S.ActionId=101 AND DATE_FORMAT(IF(L.Received='0000-00-00 00:00:00',L.created,L.Received),'%Y-%m')='$month' GROUP BY S.sPOrderId
                                                    )A 
									)B GROUP BY DATE_FORMAT(B.Received,'%Y-%m-%d')  ORDER BY Date DESC";
			   break;
			 case 17://半成品备料
			        $sql="SELECT A.Date,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m-%d') AS Date,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts
														FROM(
                                                          SELECT A.POrderId,A.mStockId,A.sPOrderId,COUNT(*) AS llCounts,MAX(A.created) AS created 
						                                  FROM (
														     SELECT L.StockId,S.mStockId,L.POrderId,L.sPOrderId,MAX(L.created) AS created,G.OrderQty,SUM(L.Qty) AS llQty 
														     FROM ck5_llsheet L 
														     INNER JOIN yw1_scsheet S ON S.sPOrderId=L.sPOrderId 
								                             INNER JOIN cg1_semifinished G ON G.mStockId=S.mStockId AND G.StockId=L.StockId 
														     INNER JOIN workshopdata D ON D.Id=S.WorkShopId  
														     WHERE DATE_FORMAT(L.created,'%Y-%m')='$month' AND L.Type=1 AND D.semiSign=1 GROUP BY L.sPOrderId,L.StockId 
								                            )A  WHERE A.OrderQty=A.llQty GROUP BY A.sPOrderId
                             							)B 
                                                        INNER JOIN cg1_semifinished G ON G.mStockId=B.mStockId
                                                        INNER JOIN stuffdata D ON D.StuffId=G.StuffId
                                                        INNER JOIN stufftype TY ON TY.TypeId=D.TypeId 
                                                        INNER JOIN stuffmaintype TM ON TM.Id=TY.mainType  AND TM.blSign=1 
                                                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId
								                        WHERE 1 GROUP BY B.sPOrderId 
													)A 
								 WHERE A.llCounts>=A.blCounts  GROUP BY A.Date ORDER BY Date DESC";
			   break;
			   case 0://外发备料
			       $sql="SELECT B.Date,SUM(B.Counts) AS Counts,SUM(B.oneCounts) AS oneCounts,SUM(B.twoCounts) AS twoCounts
								FROM (
								SELECT A.Date,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m-%d') AS Date,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts
														FROM(
														    SELECT A.sPOrderId,COUNT(*) AS llCounts,MAX(A.created) AS created  FROM (
														     SELECT L.StockId,L.POrderId,L.sPOrderId,MAX(L.created) AS created 
														     FROM yw1_scsheet S
														     INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId 
													     	WHERE S.scFrom>0 and  L.Type=1 AND S.ActionId=105 AND DATE_FORMAT(L.created,'%Y-%m')='$month' GROUP BY L.StockId)A GROUP BY A.sPOrderId
														)B 
								                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId  
								                        GROUP BY B.sPOrderId 
													)A 
								 WHERE A.llCounts>=A.blCounts  GROUP BY A.Date
						UNION  ALL
								SELECT A.Date,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
											          FROM (
														SELECT  DATE_FORMAT(B.created,'%Y-%m-%d') AS Date,B.sPOrderId,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>12,1,0) AS oneCounts,
														       IF(TIMESTAMPDIFF(hour,T.ableDate,B.created)>48,1,0) AS twoCounts 
														FROM(
														    SELECT S.sPOrderId,MAX(L.created) AS created 
														    FROM yw1_scsheet S
														    INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId 
													     	WHERE S.scFrom=0 AND S.ActionId=105 AND DATE_FORMAT(L.created,'%Y-%m')='$month' GROUP BY S.sPOrderId
														)B 
								                        LEFT JOIN ck_bldatetime T ON T.sPOrderId=B.sPOrderId  
								                        GROUP BY B.sPOrderId 
													)A   GROUP BY A.Date 
							UNION ALL 
                               SELECT A.Date,COUNT(*) AS Counts,SUM(A.oneCounts) AS oneCounts,SUM(A.twoCounts) AS twoCounts 
								 FROM (
								       SELECT  DATE_FORMAT(B.created,'%Y-%m-%d') AS Date,
										IF(TIMESTAMPDIFF(hour,B.ableDate,B.created)>12,1,0) AS oneCounts,
										IF(TIMESTAMPDIFF(hour,B.ableDate,B.created)>48,1,0) AS twoCounts
						               FROM(
										     SELECT MAX(L.created) AS created,getStuffDeliveredMinTime(L.StuffId,L.created) AS ableDate ,
										                 (UG.OrderQty) AS blQty,SUM(IFNULL(L.Qty,0)) AS llQty 
									         FROM  cg1_stuffunite U
									         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
					                         INNER JOIN cg1_stockmain M  ON M.Id=G.Mid 
					                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
					                         INNER JOIN ck5_llsheet L ON L.StockId=UG.StockId 
									         WHERE  G.Mid>0 AND DATE_FORMAT(L.created,'%Y-%m')='$month' 
					                         GROUP BY G.StockId 
					                     )B  WHERE B.blQty=B.llQty 
                                     )A GROUP BY A.Date
								)B 
						 GROUP BY B.Date ORDER BY Date DESC";
			   break;
		}
				
		$query=$this->db->query($sql);
		if ($query->num_rows()>0) {
		     return $query->result_array();
		}
		else{
			  return array();
		}
   }
   
 function get_date_bledlist($sendfloor,$date){
	    switch($sendfloor){
			case 3://组装备料
			     $sql="SELECT  S.POrderId,S.ProductId,S.created,B.sPOrderId,B.Qty,B.ActionId,IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek,S.ShipType, L.Letter AS Line,M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,B.ableDate,B.Received  
							  FROM(
                                    SELECT A.POrderId,A.sPOrderId,A.Qty,A.ableDate,A.scLineId,A.ActionId,IF(A.Received='0000-00-00 00:00:00',A.created,A.Received) AS Received 
                                          FROM (
												    SELECT S.POrderId,S.sPOrderId,S.Qty,S.scLineId,S.ActionId,MAX(T.DateTime) AS ableDate,MAX(L.Received)  AS Received,MAX(L.created) AS created 
												    FROM yw1_scsheet S
												    INNER JOIN  sc1_mission T ON T.sPOrderId=S.sPOrderId  
                                                    INNER JOIN  ck5_llsheet L  ON S.sPOrderId=L.sPOrderId AND L.Estate=0 
											     	WHERE  S.ActionId=101 AND DATE_FORMAT(IF(L.Received='0000-00-00 00:00:00',L.created,L.Received),'%Y-%m-%d')='$date' GROUP BY S.sPOrderId
                                            )A 
							)B 
						 	 INNER JOIN yw1_ordersheet S ON S.POrderId=B.POrderId 
							 INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			                 INNER JOIN trade_object A ON A.CompanyId=M.CompanyId 
			                 INNER JOIN productdata P ON P.ProductId=S.ProductId  
			                 LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id
			                 LEFT JOIN yw3_pileadtime PL ON PL.POrderId=S.POrderId 
			                 LEFT JOIN workscline  L ON L.Id=B.scLineId  
			                WHERE 1 GROUP BY  B.sPOrderId ORDER BY TIMESTAMPDIFF(second,B.ableDate,B.Received) DESC
						";
			   break;
			  case 17:
			      $sql="SELECT S.POrderId,S.sPOrderId,S.mStockId,S.Qty,S.ActionId,G.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,D.Picture,T.ableDate,B.created  
                            FROM( 
						              SELECT A.sPOrderId,A.created 
								              FROM (
												SELECT B.POrderId,B.mStockId,B.sPOrderId,B.llCounts,COUNT(*) AS  blCounts,B.created
												FROM(
												  SELECT A.POrderId,A.mStockId,A.sPOrderId,COUNT(*) AS llCounts,MAX(A.created) AS created 
												   FROM (
												    SELECT L.StockId,S.mStockId,L.POrderId,L.sPOrderId,L.created,G.OrderQty,SUM(L.Qty) AS llQty 
												     FROM ck5_llsheet L 
												     INNER JOIN yw1_scsheet S ON S.sPOrderId=L.sPOrderId 
						                             INNER JOIN cg1_semifinished G ON G.mStockId=S.mStockId AND G.StockId=L.StockId 
												     INNER JOIN workshopdata D ON D.Id=S.WorkShopId  
												     WHERE DATE_FORMAT(L.created,'%Y-%m-%d')='$date' AND L.Type=1 AND D.semiSign=1 GROUP BY L.sPOrderId,L.StockId 
						                            )A  WHERE A.OrderQty=A.llQty GROUP BY A.sPOrderId
												)B 
												INNER JOIN cg1_semifinished G ON G.mStockId=B.mStockId   
												INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
												INNER JOIN stufftype T ON T.TypeId=D.TypeId 
												INNER JOIN stuffmaintype M ON M.Id=T.mainType AND M.blSign=1 
												GROUP BY B.sPOrderId 
										  )A  WHERE A.llCounts>=A.blCounts 
						        )B
                                LEFT JOIN yw1_scsheet S ON S.sPOrderId=B.sPOrderId 
								LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
								LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
								LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
								LEFT JOIN  ck_bldatetime T ON T.sPOrderId=S.sPOrderId 
								WHERE 1 GROUP BY S.sPOrderId ORDER BY TIMESTAMPDIFF(second,T.ableDate,B.created) DESC";
			   break;
			case 0:
			      $sql="SELECT S.POrderId,S.sPOrderId,S.mStockId,S.Qty,S.ActionId,G.StockId,
			      G.StuffId,G.DeliveryWeek,D.StuffCname,D.Picture,T.ableDate,A.created  
                            FROM(
								      SELECT A.sPOrderId,A.created 
									      FROM (
												SELECT  B.sPOrderId,B.created,B.llCounts,COUNT(*) AS  blCounts 
												   FROM(
														   SELECT A.sPOrderId,A.mStockId,COUNT(*) AS llCounts,MAX(A.created) AS created  FROM (
														    SELECT L.StockId,L.POrderId,L.sPOrderId,S.mStockId,MAX(L.created) AS created 
														    FROM ck5_llsheet L
                                                            INNER JOIN  yw1_scsheet S  ON S.sPOrderId=L.sPOrderId 
													     	WHERE DATE_FORMAT(L.created,'%Y-%m-%d')='$date'  and  L.Type=1 
													     	AND S.ActionId=105 AND S.ScFrom>0  
													     	GROUP BY L.StockId)A GROUP BY A.sPOrderId
														)B 
                                                        INNER JOIN cg1_semifinished G ON G.mStockId=B.mStockId  
                                                        INNER JOIN stuffdata D ON D.StuffId=G.StuffId
                                                        INNER JOIN stufftype TY ON TY.TypeId=D.TypeId 
                                                        INNER JOIN stuffmaintype TM ON TM.Id=TY.mainType  
								                        WHERE TM.blSign=1 GROUP BY B.sPOrderId 
													)A  WHERE A.llCounts>=A.blCounts  GROUP BY A.sPOrderId
								      UNION  ALL
										    SELECT  B.sPOrderId,B.created
													FROM(
														    SELECT S.sPOrderId,MAX(L.created) AS created 
														    FROM ck5_llsheet L
                                                            INNER JOIN yw1_scsheet S   ON S.sPOrderId=L.sPOrderId  
								                            INNER JOIN workshopdata D ON D.ActionId=S.ActionId 
													      	WHERE DATE_FORMAT(L.created,'%Y-%m-%d')='$date' AND  L.Type=1  
													      	AND S.ActionId=105   AND S.ScFrom=0  GROUP BY S.sPOrderId
													)B  GROUP BY B.sPOrderId 
								 )A 
								LEFT JOIN yw1_scsheet S ON S.sPOrderId=A.sPOrderId 
								LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
								LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
								LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
								LEFT JOIN  ck_bldatetime T ON T.sPOrderId=S.sPOrderId 
								WHERE 1 GROUP BY S.sPOrderId 
          UNION ALL 
	            SELECT  S.POrderId,'0' AS sPOrderId,S.StockId AS mStockId,S.Qty,0 AS ActionId,
                 S.StockId,S.StuffId,S.DeliveryWeek,D.StuffCname,D.Picture,'' AS ableDate,IFNULL(S.created,'') AS created    
	               FROM(
					     SELECT G.POrderId,G.StockId,G.StuffId,(G.FactualQty + G.AddQty) AS Qty,
					     G.DeliveryWeek,G.created,(UG.OrderQty) AS blQty,SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty  
				         FROM  cg1_stuffunite U
				         INNER JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
                         INNER JOIN cg1_stocksheet UG ON UG.POrderId=U.POrderId AND UG.StuffId = U.uStuffId 
                         INNER JOIN ck9_stocksheet K ON  K.StuffId=UG.StuffId    
                         LEFT JOIN ck5_llsheet L ON L.StockId=UG.StockId 
				         WHERE  G.Mid>0  AND DATE_FORMAT(L.created,'%Y-%m-%d')='$date'  
                         GROUP BY G.StockId 
                     )S 
                 INNER JOIN stuffdata D ON D.StuffId = S.StuffId 
				WHERE S.blQty=S.llQty   ORDER BY TIMESTAMPDIFF(second,ableDate,created) DESC";
			  break;
	   }
	   
	   $query=$this->db->query($sql);
		if ($query->num_rows()>0) {
		     return $query->result_array();
		}
		else{
			  return array();
		}
}

 //获取当天仓库的报废数量(按出库记录统计)
     function get_bf_daycount($WarehouseId,$SendFloor,$date='')
     {
	    $date=$date==''?$this->Date:$date;
	   
	    $sql = "SELECT COUNT(*) AS Counts,IFNULL(SUM(A.Qty),0) AS Qty  
	          FROM (
	             SELECT S.StuffId,SUM(S.Qty) AS Qty 
	                  FROM (
					           SELECT B.StuffId,IFNULL(SUM(B.Qty),0) AS Qty 
					           FROM ck5_llsheet B 
				               INNER JOIN ck1_rksheet R ON R.Id=B.RkId 
				               INNER JOIN ck_location L ON L.Id=R.LocationId   
					           WHERE B.Type=2  AND B.Date ='$date' AND  L.WarehouseId ='$WarehouseId'   GROUP BY B.StuffId
					      UNION ALL 
							    SELECT B.StuffId,IFNULL(SUM(-B.Qty),0) AS Qty 
								FROM ck5_llsheet B  
							    INNER JOIN stuffdata D ON B.StuffId=D.StuffId  
								WHERE  B.Type=6  AND B.Date ='$date' AND D.SendFloor IN($SendFloor) GROUP BY B.StuffId
                     )S GROUP BY S.StuffId    
			     )A ";
	     $query=$this->db->query($sql);
	     return $query->first_row('array');
  } 
  
   //获取当月仓库的报废数量(按出库记录统计)
   function get_bf_monthcount($WarehouseId,$SendFloor,$month='')
   {
	    $month=$month==''?date("Y-m"):$month;
	    $sql = "SELECT COUNT(*) AS Counts,IFNULL(SUM(A.Qty),0) AS Qty  
	          FROM (
	             SELECT S.StuffId,SUM(S.Qty) AS Qty 
	                  FROM (
					           SELECT B.StuffId,IFNULL(SUM(B.Qty),0) AS Qty 
					           FROM ck5_llsheet B 
				               INNER JOIN ck1_rksheet R ON R.Id=B.RkId 
				               INNER JOIN ck_location L ON L.Id=R.LocationId   
					           WHERE B.Type=2  AND DATE_FORMAT(B.Date,'%Y-%m') ='$month'  AND  L.WarehouseId ='$WarehouseId'  GROUP BY B.StuffId
					      UNION ALL 
							    SELECT B.StuffId,IFNULL(SUM(-B.Qty),0) AS Qty 
								FROM ck5_llsheet B  
							    INNER JOIN stuffdata D ON B.StuffId=D.StuffId  
								WHERE  B.Type=6   AND DATE_FORMAT(B.Date,'%Y-%m') ='$month'  AND D.SendFloor IN($SendFloor) GROUP BY B.StuffId
                     )S GROUP BY S.StuffId    
			     )A ";
			     
	    $query=$this->db->query($sql);
	    return $query->first_row('array');
   }

  
   
	//设置领料状态
	function set_estate($Id,$Estate=0){
	   
	   $data=array('Estate' =>$Estate,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
	              
	   $this->db->update('ck5_llsheet',$data, array('Id' => $Id));
	   
	   return $this->db->affected_rows();
   }
   
   function set_stock_estate($sPOrderId,$StockId,$Estate=0){
      if ($Estate==0){
	       $data=array('Estate' =>$Estate,
	               'Receiver'=>$this->LoginNumber,
	               'Received'=>$this->DateTime
	              );
      }else{
	       $data=array('Estate' =>$Estate,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
      }     
	   $this->db->update('ck5_llsheet',$data, array('sPOrderId' =>$sPOrderId,'StockId' =>$StockId));
	   $upSign=$this->db->affected_rows();
	   
	   if ($Estate==0){
	         //检查子母配件领料
	         $LStockId=substr($StockId, 0,-2);
		     $checkResult = $this->db->query("SELECT COUNT(*) AS Counts,SUM(L.Estate) AS Estate,G.mStockId
		                FROM ck5_llsheet L  
                         INNER JOIN cg1_stuffcombox G ON G.StockId = L.StockId 
                         WHERE L.StockId LIKE  '$LStockId%'  AND L.sPOrderId = '$sPOrderId'");
			 $checkRow = $checkResult->first_row('array');
			 if ($checkRow['Counts']>0){
			      if ($checkRow['Estate']==0){
			            $mStockId = $checkRow['mStockId'];
				        $data2=array('Estate' =>$Estate,
	                                      'Receiver'=>$this->LoginNumber,
	                                      'Received'=>$this->DateTime
	                    );
	                     $this->db->update('ck5_llsheet',$data2, array('sPOrderId' =>$sPOrderId,'StockId' =>$mStockId));
			      }
			 }
	   }
	   
	   return $upSign;
   }
   
   function save_occupy($sPOrderId)
   {
	    $this->load->model('ScSheetModel');
	    $records=$this->ScSheetModel->get_records($sPOrderId);
		$POrderId=$records['POrderId'];
		$records = null;
		
		$Operator=$this->LoginNumber;
		$sql="CALL proc_yw1_ordersheet_occupy('$POrderId','$sPOrderId','$Operator') ";
		$query = $this->db->query($sql);
		
		$row = $query->first_row('array');
		
		return $row['OperationResult']=='Y'?1:0;
   }
   
   function save_multibl($sPOrderId,$StockId,$Qty,$llSign)
   {
        //$llSign:1, 成品加工备料;2、3,半成品加工和半成品外发备料;4,关联外发备料
	    $this->load->model('CgstocksheetModel');
	    $this->load->model('CgStuffcomboxModel');
	    $records  = $this->CgstocksheetModel->get_records($StockId);
	    $records  = count($records) == 0 ? $this->CgStuffcomboxModel->get_records($StockId) :$records;//子配件备料
		$POrderId = $records['POrderId'];
		$StuffId  = $records['StuffId'];
		$records = null;
		$Operator=$this->LoginNumber;
		$sql="CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$Qty','$Operator','$llSign')";
		$query = $this->db->query($sql);
		$row = $query->first_row('array');
		
		$OperaResult=$row['OperationResult']=='Y'?1:0;
		$query =null;
		

		//外发配件，需自动下采购单
		
		if (($llSign==2 || $llSign==3) && $OperaResult==1)
		{
			 $checkResult = $this->db->query("SELECT getCanStock('$sPOrderId',5)  AS canStock");
			 $checkRow = $checkResult->first_row('array');
			 if ($checkRow['canStock']>1){
				   //已备料，可下采单
				   $this->load->model('CgStockmainModel');
				   $this->CgStockmainModel->save_outbl_tomain($sPOrderId);
			 }
		}
		
		return $OperaResult;
   }
   
   function save_feedPicking($Id)
   {
        $this->load->model('CkreplenishModel');
	    $records=$this->CkreplenishModel->get_records($Id);
	    $POrderId  = $records['POrderId'];
        $StockId = $records['StockId'];
        $StuffId  = $records['StuffId'];
        $Qty  = $records['Qty'];
	             
		 $records = null;
		
		$Operator=$this->LoginNumber;
		$sql="CALL proc_ck5_llsheet_save('$POrderId','$Id','$StockId','$StuffId','$Qty','$Operator','5')";
		$query = $this->db->query($sql);
		$row = $query->first_row('array');
		
		$OperaResult=$row['OperationResult']=='Y'?1:0;
		
		if ($OperaResult==1){
			 
		}
		
		return $OperaResult;
   }
   
  


   /*以下为旧代码*/
	//获取可备料的时间(已弃用)
	function get_bl_nextweek(){
		
		$today=$this->Date;
	    $addDays=date('w')>=4?14:7;
		$nextWeekDate=date("Y-m-d",strtotime("$today  +$addDays   day"));
		$dateResult  = $this->db->query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek");
		$dateRow  = $dateResult->first_row();
		$nextWeek = $dateRow->NextWeek;
		return $nextWeek;
	}
    	
	/** 
	* get_llQty_bysPOrderId  
	* 工单总领料数量
	* 
	* @access public 
	* @param  params $sPOrderId 一条纪录所需数据
	* @return int 返回工单总领料数量
	*/ 
	function get_llQty_bysPOrderId($sporderid){
	
	   $sql = "SELECT  SUM(Qty) AS Qty FROM ck5_llsheet  WHERE sPOrderId=? "; 	
	   $query=$this->db->query($sql,$sporderid);
	   $row = $query->first_row();
	   $Qty=$row->Qty;
	   $Qty=$Qty==""?0:$Qty;
       return $Qty;
	}
	
	/** 
	* get_llQty_byStockId  
	*采购流水号总领料数量
	* 
	* @access public 
	* @param  params $stockid 一条纪录所需数据
	* @return int 返回采购流水号总领料数量
	*/ 
	function get_llQty_byStockId($sporderid,$stockid){
	
	   $dataArray = array();
	   $sumQty = 0 ;
	   $sumQty0 = 0 ;
	   $sumQty1 = 0 ;
	   $sql = "SELECT  IFNULL(SUM(Qty),0) AS Qty,Estate FROM ck5_llsheet  
	                 WHERE sPOrderId =? AND stockid=? Group by Estate  "; 	
	   $query=$this->db->query($sql,array($sporderid,$stockid));
	   foreach ($query->result_array() as $row){
	       $Estate = intval($row["Estate"]);
	       $Qty    = $row["Qty"];
	       switch($Estate){
	          case 1:
		       $sumQty1 += $Qty;
		       break;
	          case 0:
		       $sumQty0 += $Qty;
		       break;     
	       }
	     $sumQty+=$Qty;
	   }
       return $dataArray = array("llQty"=>$sumQty,"llQty1"=>$sumQty1,"llQty0"=>$sumQty0);
	}
	


	 //送货楼层的备料总数情况
	 function  get_blqty_bysendfloor($sendfloor){
	
	     $dataArray = array();
	     $checkBlSign = 99;
         $sumAlreadyBlQty = 0 ;
         $sumWaitBlQty=0;
         $waitBlnum = 0 ;
         $sum0utBlQty = 0 ;
         $outBlnum = 0 ;
         
         
	     switch($sendfloor){
		     
		     case "17": //47-1F 半成品备料
		     
				//$nextWeek = $this->get_bl_nextweek(); //CG.DeliveryWeek<=$nextWeek 
				$SearchRows =" AND CG.DeliveryWeek>0  
				               AND SC.ActionId IN (102,103,104,105) AND SC.scFrom >0 AND SC.Estate>0 "; 
				$sql = "SELECT * FROM (
					        SELECT  SC.POrderId,SC.sPOrderId,SC.Qty,SC.mStockId,SC.ActionId,SC.WorkShopId,
							getCanStock(SC.sPOrderId,$checkBlSign) AS canSign,CG.DeliveryWeek
							FROM  yw1_scsheet SC 
							LEFT  JOIN cg1_stocksheet  CG ON CG.StockId = SC.mStockId
							WHERE  1 $SearchRows AND CG.DeliveryWeek>0 GROUP BY SC.sPOrderId
						) A WHERE A.canSign>0";
		        $query = $this->db->query($sql);   
		        foreach ($query->result_array() as $row){
		            $WorkShopId = $row['WorkShopId'];
		            $ActionId = $row['ActionId'];
		            $DeliveryWeek= $row['DeliveryWeek'];
		            $canSign  = $row['canSign'];
	                $Qty  = $row['Qty'];
	                if($canSign ==1 && $WorkShopId !=0 && $ActionId!=105){
	                   $sumWaitBlQty +=$Qty;
	                   $waitBlnum++;
	                }
	                if(($canSign ==2 || $canSign ==3) && $WorkShopId !=0 && $ActionId!=105){
	                   $sumAlreadyBlQty +=$Qty;
	                }
	                if($canSign ==1 && $WorkShopId ==0 && $ActionId==105){ //外发
	                   $sum0utBlQty +=$Qty;
	                   $outBlnum++;
	                }
	               
		        }
		        //*******补料
		        $feednum =0;
		        $feedqty =0;
		        
		        $feedSql = "SELECT IFNULL(SUM(R.Qty),0) AS feedQty,COUNT(*) AS feedNum 
		                    FROM ck13_replenish R 
		                    LEFT JOIN yw1_scsheet SC ON SC.sPOrderId = R.sPOrderId AND SC.POrderId = R.POrderId
		                    WHERE  SC.ActionId IN (102,103,104,105)";
		        
		        $feedQuery  = $this->db->query($feedSql);
	            $feedRow = $feedQuery->first_row();
	            $feedqty = $feedRow->feedQty;
	            $feednum = $feedRow->feedNum;
		        
		        $dataArray = array('waitBlQty'=>$sumWaitBlQty,
		                           'alreadyBlQty'=>$sumAlreadyBlQty,
		                           'outward' => array('outblqty'=>$sum0utBlQty,'outnum'=>$outBlnum),
		                           'feed' => array('feedqty'=>$feedqty,'feednum'=>$feednum),
		                          );
		         
		     break;
		     
		     case "6": //48-3A  成品备料1
		     case "3": //48-3B  成品备料2
		     case "12": //48-1F 成品备料3
		     
		        $nextWeek = $this->get_bl_nextweek($sendfloor); 
				$SearchRows =" AND SC.ActionId IN (101) AND SC.scFrom >0 AND SC.Estate>0 ";              		 
				$sql="SELECT A.Qty,A.canSign,IFNULL(A.Leadweek,A.NewLeadWeak) AS Leadweek FROM (
							SELECT SC.Qty,getCanStock(SC.sPOrderId,$checkBlSign) AS canSign,   
							PI.Leadweek,YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS NewLeadWeak
							FROM  yw1_scsheet  SC 
							LEFT JOIN yw1_ordersheet S ON S.POrderId=SC.POrderId
							LEFT JOIN cg1_stocksheet G ON G.POrderId = S.POrderId AND G.Level=1
							LEFT JOIN stuffdata D ON D.StuffId = G.StuffId
							LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
							LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id
							LEFT JOIN yw3_pileadtime PL ON PL.POrderId=S.POrderId  
							WHERE 1  $SearchRows  AND D.SendFloor = '$sendfloor' AND SC.Level = 1  GROUP BY SC.sPOrderId ) A  
							WHERE canSign>0 ";			
		        $query = $this->db->query($sql);   
		        foreach ($query->result_array() as $row){                  
				  
		            $Leadweek= $row['Leadweek'];
		            $canSign  = $row['canSign'];
	                $Qty  = $row['Qty'];
		             if($canSign ==1){
	                   $sumWaitBlQty +=$Qty;
	                   $waitBlnum++;
	                }
	                if($canSign ==2 || $canSign ==3){
	                   $sumAlreadyBlQty +=$Qty;
	                }  
		        }
		        
		        //关联外发
		        $outsql="SELECT SUM(G.FactualQty + G.AddQty) AS cgQty,COUNT(*) AS outNum
				         FROM  cg1_stuffunite U
				         LEFT JOIN yw1_ordersheet Y ON Y.POrderId = U.POrderId 
				         LEFT JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
				         LEFT JOIN stuffdata D ON D.StuffId = U.StuffId
				         WHERE D.SendFloor = '$sendfloor' AND Y.Estate>0 AND G.StockId>0 AND G.Level = 1 
				         AND G.Mid>0 AND G.CompanyId NOT IN (2270,100300) ";
				$outResult  = $this->db->query($outsql);
		        $outRow  = $outResult->first_row();
				$sum0utBlQty = $outRow->cgQty;
		        $outBlnum    = $outRow->outNum;
		        //********************补料
		        $feednum =0;
		        $feedqty =0;
		        $dataArray = array('waitBlQty'=>$sumWaitBlQty,
		                           'alreadyBlQty'=>$sumAlreadyBlQty,
		                           'outward' => array('outblqty'=>$sum0utBlQty,'outnum'=>$outBlnum),
		                           'feed' => array('feedqty'=>$feedqty,'feednum'=>$feednum),
		                          );
		     break;
		     
	     }
	     return  $dataArray;
	}
	
	//按仓库各楼层的备料明细
	function get_blorder_bysendfloor($sendfloor){
		
	    $sql='';
	    $dataArray=array();
		switch($sendfloor){
			case "17":
			
		        //$nextWeek = $this->get_bl_nextweek();
				
				/*$SearchRowsA =" AND CG.DeliveryWeek<=$nextWeek AND CG.DeliveryWeek>0  
				                AND SC.ActionId IN (102,103,104) AND SC.scFrom >0 AND SC.Estate>0 ";*/
				$SearchRows =" AND SC.ActionId IN (102,103,104) AND SC.scFrom >0 AND SC.Estate>0 ";
						
				$sql="SELECT A.WorkShopId,A.WorkShopName,SUM(A.Qty) AS blQty,COUNT(*) AS blNum FROM (
						SELECT  SC.Qty,SC.WorkShopId,W.Name AS WorkShopName,
						getCanStock(SC.sPOrderId,1) AS canSign
						FROM  yw1_scsheet SC 
						LEFT  JOIN workshopdata      W  ON W.Id = SC.WorkShopId
						LEFT  JOIN cg1_stocksheet    CG ON CG.StockId = SC.mStockId
						WHERE  1 $SearchRows AND CG.DeliveryWeek>0 GROUP BY SC.sPOrderId) A  
						WHERE A.canSign=1  GROUP BY A.WorkShopId";
		     break; 
		     case "6": //48-3A  成品备料1
		     case "3": //48-3B  成品备料2
		     case "12": //48-1F 成品备料3
		         $curDate=date("Y-m-d");
				 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7 day"));
				 $dateResult = $this->db->query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek");
				 $dateRow  = $dateResult->first_row();
				 $nextWeek = $dateRow->NextWeek;
				 $SearchRowsWeek=" AND  YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)<='$nextWeek' 
				                   AND IFNULL(PI.Leadtime,PL.Leadtime) IS NOT NULL";
				 $SearchRows =" AND SC.ActionId IN (101) AND SC.scFrom >0 AND SC.Estate>0 ";  
				  
				 $sql ="SELECT * FROM (
							SELECT M.CompanyId,C.Forshort ,S.POrderId,S.OrderPO,S.Qty,SC.sPOrderId,
							getCanStock(SC.sPOrderId,1) AS canSign,  
							SC.Qty AS scQty,SC.ActionId,
							P.cName,P.TestStandard,P.ProductId,M.OrderDate,
							IFNULL(PI.Leadweek,YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)) AS Leadweek
							FROM  yw1_scsheet  SC 
							LEFT JOIN yw1_ordersheet S ON S.POrderId=SC.POrderId
							LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
							LEFT JOIN productdata P ON P.ProductId=S.ProductId
							LEFT JOIN trade_object C ON M.CompanyId=C.CompanyId
							LEFT JOIN packingunit U ON U.Id=P.PackingUnit 
							LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id
							LEFT JOIN yw3_pileadtime PL ON PL.POrderId=S.POrderId  
							WHERE 1  $SearchRows $SearchRowsWeek AND SC.Level = 1  GROUP BY SC.sPOrderId 
						) A  WHERE canSign=1 ORDER BY Leadweek";		                
		}
	
		if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
			
		}
		
		return $dataArray;
	}
	
	
	//按楼层获取外发备料工单
    function get_outblorder_bysendfloor($sendfloor){
    
		$sql='';$SearchRows='';
	    $dataArray=array();
	    
	    switch($sendfloor){
			
			case "17": //47-1F
			   
			    $SearchRows =" AND SC.ActionId IN (105) AND WorkShopId = 0 AND SC.scFrom >0 AND SC.Estate>0 "; 
			    
		        $sql = "SELECT A.* FROM (
							SELECT  SC.POrderId,SC.sPOrderId,SC.Qty,SC.mStockId,SC.ActionId,
							getCanStock(SC.sPOrderId,1) AS canSign,(CG.addQty+CG.FactualQty) AS xdQty,
							D.StuffId,D.StuffCname,D.Price,D.Picture,
							IF(CG.DeliveryWeek>0,CG.DeliveryDate,'2099-12-31') AS DeliveryDate,CG.DeliveryWeek
							FROM  yw1_scsheet SC 
							LEFT  JOIN cg1_stocksheet  CG ON CG.StockId = SC.mStockId
							LEFT  JOIN stuffdata       D  ON D.StuffId = CG.StuffId 
							WHERE  1 $SearchRows AND CG.DeliveryWeek>0 AND CG.Mid=0 GROUP BY SC.sPOrderId
					    ) A  WHERE A.canSign=1  ORDER BY DeliveryDate,sPOrderId";
		      break; 
		     case "6": //48-3A  
		     case "3": //48-3B  
		     case "12": //48-1F 
		         $sql ="SELECT U.POrderId,(G.FactualQty + G.AddQty) AS Qty,D.StuffId,D.StuffCname,G.DeliveryWeek,
		                 T.Forshort,M.PurchaseID,G.StockId
				         FROM  cg1_stuffunite U
				         LEFT JOIN yw1_ordersheet Y ON Y.POrderId = U.POrderId 
				         LEFT JOIN cg1_stocksheet G  ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
				         LEFT JOIN stuffdata D ON D.StuffId = U.StuffId
				         LEFT JOIN cg1_stockmain  M ON M.Id = G.Mid
				         LEFT JOIN trade_object   T ON T.CompanyId = M.CompanyId
				         WHERE D.SendFloor = '$sendfloor' AND Y.Estate>0 AND G.StockId>0 
				         AND G.Level = 1 AND G.Mid>0 AND G.CompanyId NOT IN (2270,100300) ";
		     break;
		}
		
		if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();	
		}
		
		return $dataArray;
	}
	
	//按生产单位获得半成品工单
	function  get_semi_order($workshopid){
	
	    //$nextWeek = $this->get_bl_nextweek();
	    $SearchRows =" AND SC.WorkShopId ='$workshopid'  AND SC.scFrom >0 AND SC.Estate>0 ";
		
        $sql = "SELECT A.* FROM (
						SELECT  SC.POrderId,SC.sPOrderId,SC.Qty,SC.mStockId,SC.ActionId,
						getCanStock(SC.sPOrderId,1) AS canSign,D.StuffId,D.StuffCname,D.Picture,
						CG.DeliveryDate,CG.DeliveryWeek
						FROM  yw1_scsheet SC 
						LEFT  JOIN cg1_stocksheet    CG ON CG.StockId = SC.mStockId
						LEFT  JOIN stuffdata         D  ON D.StuffId = CG.StuffId 
						WHERE  1 $SearchRows AND CG.DeliveryWeek>0 GROUP BY SC.sPOrderId
					) A  WHERE A.canSign=1  ORDER BY DeliveryDate,sPOrderId";
		
		$query=$this->db->query($sql); //去掉锁定的
		$dataArray= $query->result_array();
		return $dataArray;	
	}
	
	//获得订单的备料明细
	function  get_order_stuff($porderid,$sporderid){
		
		 $checkSql = "SELECT IFNULL(Qty,0) AS Qty FROM yw1_ordersheet WHERE POrderId = ?";
	     $checkQuery=$this->db->query($checkSql,$porderid);
	     $checkRow = $checkQuery->first_row();
	     $qty=$checkRow->Qty;
	     
	     if($qty==0 ){
		     return array();
	     }
	     
	     $sql = "SELECT ROUND(G.OrderQty*(S.Qty/$qty),1) AS OrderQty,G.StockId,
	            K.tStockQty,
	            D.StuffId,D.StuffCname,D.Picture,
	            F.Remark,
	            M.Name,
	            P.Forshort,
	            U.Name AS UnitName,U.decimals 
				FROM  yw1_scsheet  S 
				LEFT JOIN cg1_stocksheet G ON G.POrderId = S.POrderId  
				LEFT JOIN ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN base_mposition F ON F.Id=D.SendFloor
				LEFT JOIN stufftype T ON T.TypeId=D.TypeId
				LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType
				LEFT JOIN stuffunit U ON U.Id=D.Unit
				WHERE  S.sPOrderId='$sporderid' AND G.Level=1 AND G.blSign=1 ORDER BY D.SendFloor";
	     $query = $this->db->query($sql);  
		 $k=0; 
	     foreach ($query->result_array() as $row){
		
		     $llArray = $this->get_llQty_byStockId($sporderid,$row['StockId']);
		     if($llArray['llQty1']>0) $llEstate = 1;
		     else{
			     $llEstate = 0 ;
		     }
		     $dataArray[$k]['StockId']     = $row['StockId'];
		     $dataArray[$k]['OrderQty']    = $row['OrderQty'];
		     $dataArray[$k]['StuffId']     = $row['StuffId'];
		     $dataArray[$k]['StuffCname']  = $row['StuffCname'];
		     $dataArray[$k]['tStockQty']   = $row['tStockQty'];
		     $dataArray[$k]['Picture']     = $row['Picture'];
		     $dataArray[$k]['llEstate']    = $llEstate;
		     $dataArray[$k]['llQty']       = $llArray['llQty'];
		     $k++;      
		  }
		 return $dataArray;
	}
	
	//获得半成品工单备料明细
    function  get_semi_stuff($porderid,$sporderid){
	     
	     $checkSql = 'SELECT SC.Qty,(CG.addQty+CG.FactualQty) AS xdQty,SC.mStockId FROM yw1_scsheet SC 
	                  LEFT  JOIN cg1_stocksheet    CG ON CG.StockId = SC.mStockId
	                  WHERE SC.sPOrderId = ?';
	     $checkQuery=$this->db->query($checkSql,$sporderid);
	     $checkRow = $checkQuery->first_row();
	     $Qty=$checkRow->Qty;
	     $Qty=$Qty==""?0:$Qty;
	     $xdQty=$checkRow->xdQty;
	     $mStockId = $checkRow->mStockId;  
	     
	     if($Qty==0  || $xdQty==0 || $mStockId ==''){
		     return array();
	     }    
	     $Relation=$Qty/$xdQty;
	     
	     $sql = "SELECT ROUND(A.OrderQty*$Relation,1) AS OrderQty,A.StockId,
		         K.tStockQty,
		         D.StuffId,D.StuffCname,D.Picture,
		         F.Remark,
		         M.Name,
		         P.Forshort,
		         U.Name AS UnitName,U.Decimals 
				 FROM  cg1_semifinished   A 
                 LEFT JOIN cg1_stocksheet G  ON G.StockId = A.StockId
				 LEFT JOIN ck9_stocksheet K ON K.StuffId=A.StuffId 
				 LEFT JOIN stuffdata D ON D.StuffId=A.StuffId 
				 LEFT JOIN stufftype T ON T.TypeId=D.TypeId
				 LEFT JOIN stuffunit U ON U.Id=D.Unit
				 LEFT JOIN staffmain M ON M.Number=G.BuyerId 
				 LEFT JOIN trade_object P ON P.CompanyId=G.CompanyId 
				 LEFT JOIN base_mposition F ON F.Id=D.SendFloor
				 LEFT JOIN stufftype T ON T.TypeId=D.TypeId
				 LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType
				 WHERE  A.POrderId=$porderid AND A.mStockId=$mStockId AND G.blSign=1 ORDER BY D.SendFloor";
		$query = $this->db->query($sql);  
		$k=0; 
	    foreach ($query->result_array() as $row){
		
		     $llArray = $this->get_llQty_byStockId($sporderid,$row['StockId']);
		     if($llArray['llQty1']>0) $llEstate = 1;
		     else{
			     $llEstate = 0 ;
		     }
		     $dataArray[$k]['StockId']     = $row['StockId'];
		     $dataArray[$k]['OrderQty']    = $row['OrderQty'];
		     $dataArray[$k]['StuffId']     = $row['StuffId'];
		     $dataArray[$k]['StuffCname']  = $row['StuffCname'];
		     $dataArray[$k]['tStockQty']   = $row['tStockQty'];
		     $dataArray[$k]['Picture']     = $row['Picture'];
		     $dataArray[$k]['llEstate']    = $llEstate;
		     $dataArray[$k]['llQty']       = $llArray['llQty'];
		     $k++;      
		  }
		 return $dataArray;
	}

	
	
	function  get_outward_stuff($porderid,$stuffid){
	
		$dataArray = array();
		$sql = "SELECT G.StockId, G.OrderQty,D.StuffId,D.StuffCname,D.Picture,
		        P.Forshort,U.Name AS UnitName,K.tStockQty
				FROM  cg1_stuffunite PU
				LEFT JOIN cg1_stocksheet G ON PU.uStuffId = G.StuffId AND PU.POrderId=G.POrderId
				LEFT JOIN ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN stuffunit U ON U.Id=D.Unit
				LEFT JOIN  trade_object P ON P.CompanyId=G.CompanyId 
				WHERE  PU.POrderId=? AND PU.StuffId = ?";
		$sporderid="";
		$query=$this->db->query($sql,array($porderid,$stuffid));
		$k=0;
		foreach ($query->result_array() as $row){
		
		     $llArray = $this->get_llQty_byStockId($sporderid,$row['StockId']);
		     if($llArray['llQty1']>0) $llEstate = 1;
		     else{
			     $llEstate = 0 ;
		     }
		     $dataArray[$k]['StockId']     = $row['StockId'];
		     $dataArray[$k]['OrderQty']    = $row['OrderQty'];
		     $dataArray[$k]['StuffId']     = $row['StuffId'];
		     $dataArray[$k]['StuffCname']  = $row['StuffCname'];
		     $dataArray[$k]['tStockQty']   = $row['tStockQty'];
		     $dataArray[$k]['Forshort']    = $row['Forshort'];
		     $dataArray[$k]['Picture']     = $row['Picture'];
		     $dataArray[$k]['llEstate']    = $llEstate;
		     $dataArray[$k]['llQty']       = $llArray['llQty'];
		     $k++;      
		}
		return $dataArray;
		
	}
	
	
	/** 
	* get_tstockqty_bysendfloor  
	* 获得各楼层的在库数量，下单3个月的，下单1-3个月的
	* 
	* @access public 
	* @param  params $sendfloor 一条纪录所需数据
	* @return int 返回各楼层的在库数量
	*/ 
	
    function  get_tstockqty_bysendfloor($sendfloor){
    
            $dataArray = array();
		    $tStockSql = "SELECT SUM(K.tStockQty) AS tStockQty,SUM(K.tStockQty*D.Price*C.Rate) AS Amount 
						  FROM ck9_stocksheet K
					      LEFT JOIN stuffdata D ON D.StuffId = K.StuffId
						  LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
						  LEFT JOIN bps B ON B.StuffId=D.StuffId 
						  LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
						  LEFT JOIN currencydata C ON C.Id = P.Currency
						  WHERE  K.tStockQty>0  AND T.mainType<2 AND D.Estate>0 AND D.SendFloor=?";
						  
		    $tStockResult = $this->db->query($tStockSql,array($sendfloor));
		    $tStockRow    = $tStockResult->first_row();
		    $sumQty = $tStockRow->tStockQty	;
		    
		    $sumQty=$sumQty==""?0:$sumQty;
			$sumQty=number_format($sumQty); 
			$sumAmount =$tStockRow->Amount;
			$sumAmount=$sumAmount==""?0:$sumAmount;
			$sumAmount=number_format($sumAmount);
			
			$qtySql = "SELECT SUM(IF (TIMESTAMPDIFF(MONTH,A.DTime,Now())>3,A.tStockQty,0)) AS MoreQty3,
					   SUM(IF (TIMESTAMPDIFF(MONTH,A.DTime,Now())<=3  ,A.tStockQty,0)) AS MoreQty2
					   FROM (
								SELECT K.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
								FROM ck9_stocksheet K
								LEFT JOIN stuffdata D ON D.StuffId = K.StuffId
								LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
								LEFT JOIN bps B ON B.StuffId=K.StuffId   
								LEFT JOIN cg1_stocksheet S ON S.StuffId=K.StuffId
								LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
								LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId
						        LEFT JOIN yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
								WHERE  K.tStockQty>0  AND T.mainType<2  AND D.Estate>0 AND D.SendFloor=? GROUP BY K.StuffId 
						)A  WHERE  1";
			 
			$qtyResult = $this->db->query($qtySql,$sendfloor);
		    $qtyRow    = $qtyResult->first_row();	
		    $moreQty3  = $qtyRow->MoreQty3;
		    $moreQty2  = $qtyRow->MoreQty2;
		    $moreQty3  = $moreQty3==""?0:$moreQty3;
		    $moreQty2  = $moreQty2==""?0:$moreQty2;
		    $dataArray = array('sumQty'=>$sumQty,
		                       'sumAmount'=>$sumAmount,
		                       'moreQty3'=>$moreQty3,
		                       'moreQty2'=>$moreQty2
		                       );
		    return $dataArray;
	}
	
	/** 
	* save_item  
	* 插入一条记录
	* 
	* @access public 
	* @param  params array 一条纪录所需数据
	* @return int 返回生成的主键(失败返回-1) auto_generated_keyid
	*/  
	public function save_item($params) {

	   $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,
	                                            'LogFunction'=>$LogFunction,
	                                            'Log'=>$Log,
	                                            'OperationResult'=>$OP));
		if ($insert_id > 0){
			 return $insert_id;
		} else {
			return -1;	
		}
		
	 }
	

}