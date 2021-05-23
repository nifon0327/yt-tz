<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Ck9stocksheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    function get_records($StuffId)
    {
	     $sql = "SELECT * FROM ck9_stocksheet WHERE StuffId=?"; 	
	     $query=$this->db->query($sql,$StuffId);
	   
	      return  $query->first_row('array');
    }
    
	public function get_item_stuffid($stuffid=-1) {
		$sql = "select * from ck9_stocksheet where StuffId=?;";
		return $this->db->query($sql,$stuffid);
	}
    // $LastBgColor="";$LastBlSign=0;
    // 检查备料
    public function stuff_blcheck($FromPageName="",$StuffId,$POrderId) {
	     $LastBgColor="";$LastBlSign=0;
	     
	     switch ($FromPageName) {
		     case "sh": {
			      $isLastStockSql = "SELECT COUNT(*) AS Counts,SUM(IF(K.tStockQty+IFNULL(S.Qty,0)>=G.OrderQty,1,0)) AS Nums,SUM(IF(D.StuffId=?,1,0)) AS lastSign 
										   FROM    yw1_ordersheet Y 
										   LEFT JOIN    cg1_stocksheet G  ON Y.POrderId=G.POrderId 
										   LEFT JOIN    ck9_stocksheet K ON K.StuffId = G.StuffId
										   LEFT JOIN    stuffdata D ON D.StuffId = G.StuffId
										   LEFT JOIN    stufftype T ON T.TypeId = D.TypeId
										   LEFT JOIN    gys_shsheet S ON S.StockId=G.StockId AND S.Estate>0 
										   WHERE Y.Estate>0 AND G.POrderId = ?
										   AND T.mainType in (1,0)
										   AND K.tStockQty < G.OrderQty";
				   $query = $this->db->query($isLastStockSql,array($StuffId,$POrderId));
				   if($query->num_rows() == 1) {
					   $lastStockRow = $query->row_array();
					   if($lastStockRow["lastSign"]>0 && $lastStockRow["Nums"]==$lastStockRow["Counts"])    
					   {
						    $LastBlSign=$lastStockRow["Nums"];
						    $LastBgColor =$LastBlSign>1?"#CCFFFF":"#C3FF64";//B0FF8E#CFFFA0
					   }
					   
				   }
		     }
		     break;
		    
			 default: {
			 	  $checkedEstate = 0;
				  $checkOrderSql="SELECT Estate FROM    yw1_ordersheet WHERE POrderId=? AND Estate>=1";
				  $query = $this->db->query($checkOrderSql,$POrderId);
				  if ($query->num_rows()>0) {
					  $row = $query->row_array();
					  $checkedEstate = $row['Estate'];
				  }
				  if ($checkedEstate >= 1) {
					  
					  $isLastStockSql = "SELECT G.StockId, D.StuffId
										   FROM    cg1_stocksheet G  
										   LEFT JOIN    ck9_stocksheet K ON K.StuffId = G.StuffId
										   LEFT JOIN    stuffdata D ON D.StuffId = G.StuffId
										   LEFT JOIN    stufftype T ON T.TypeId = D.TypeId
										   WHERE G.POrderId = ?
										   AND T.mainType in (1,0)
										   AND K.tStockQty < G.OrderQty";
						 $query = $this->db->query($isLastStockSql,$POrderId);
				   if($query->num_rows() == 1) 
					{
							$lastStockRow = $query->row_array();
							$lastStuffId = $lastStockRow["StuffId"];
							if($lastStuffId == $StuffId)
							{
							    $LastBlSign=1;
								$LastBgColor = "#C3FF64";//B0FF8E#CFFFA0
							}
				    }

					  
				  }
			 }
			 break;
	     }
	     
	     return array($LastBlSign ,$LastBgColor);
	    
    }
   

	//所有在库
	public function get_all_inware() {
		
		$sql = "SELECT SUM(K.tStockQty) as Qty,SUM(K.tStockQty*D.Price*C.Rate) AS Amount
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN  bps B ON B.StuffId=D.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0  AND T.mainType<2  ";
		$query = $this->db->query($sql);
		return  $query;
	}
	
	//所有在库数量
	public function get_all_qty($sendFloor) {
	
	     $SearchRows=$sendFloor == 'all'?'':'AND D.SendFloor=' . $sendFloor;
/*
	     $sql = "SELECT SUM(A.tStockQty) as Qty,COUNT(1) AS Count,SUM(A.Amount ) AS Amount
                  FROM (
                       SELECT SUM(K.Qty-K.llQty) AS tStockQty,
						      SUM((K.Qty-K.llQty)*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount 
						FROM ck1_rksheet K 
						INNER JOIN ck1_rkmain M ON K.Mid=M.Id 
						INNER JOIN Stuffdata D ON D.StuffId=K.StuffId 
						INNER JOIN  trade_object P ON P.CompanyId=M.CompanyId 
						INNER JOIN  currencydata C ON C.Id = P.Currency
						WHERE K.llSign>0 $SearchRows GROUP BY StockId)A"; 
*/
				$sql = "SELECT SUM(K.tStockQty) as Qty,COUNT(*) AS Count,
		SUM(K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						LEFT JOIN  bps B ON B.StuffId=D.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0 $SearchRows AND TM.blSign=1 AND D.ComboxSign=0";
						
					
						
		$query = $this->db->query($sql);
		return  $query;
	}
	
	
	//有单的库存
	public function get_all_hasorder() {
		
		$lastYear = date("Y") - 1;
				$sql = "SELECT SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty,X.OrderQty)) AS OrderQty,
						SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*X.Price*C.Rate,X.OrderQty*X.Price*C.Rate)) AS OrderAmount  
						FROM (
						   SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty,A.Price FROM(
						            SELECT K.StuffId,
						                   SUM(IFNULL(G.OrderQty,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price    
												FROM  ck9_stocksheet K
												LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0   
						                        AND  G.ywOrderDTime>'$lastYear-01-01'  
												WHERE  K.tStockQty>0  AND TM.blSign=1 Group by K.StuffId  
								   UNION ALL 
						              SELECT K.StuffId,
						                     SUM(IFNULL(R.Qty*-1,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price   
												FROM  ck9_stocksheet K
												LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType  
						                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0  
						                        AND  G.ywOrderDTime>'$lastYear-01-01'     
						                        LEFT JOIN  ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0  AND TM.blSign=1 Group by K.StuffId)A GROUP BY A.StuffId 
						)X 
						LEFT JOIN  ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN  bps B ON B.StuffId=X.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency 
						WHERE 1 
				";
		$query = $this->db->query($sql);
		return  $query;
	}
	

//有单的库存sendfloor
	public function get_all_hasorder_floor($SendFloor, $LocationId='', $aStuffId='') {
		
		$lastYear = date("Y") - 1;
		$SearchRows=$SendFloor == 'all'?'':'AND D.SendFloor=' . $SendFloor;
		if ($SendFloor == '') {
			$SearchRows = '';
		}
		
		
		if ($LocationId != '') {
			
			$arr = $stuffIdsstr = array('-1');
			if ($aStuffId!='') {
				$SearchRows.= "  AND  K.StuffId=$aStuffId ";
			} else {
				$sql = " select R.StuffId  From ck1_rksheet R  LEFT JOIN ck_location L ON L.Id=R.LocationId where   (L.Id='$LocationId' OR L.Mid='$LocationId') and R.StuffId!='' and R.StuffId is not null Group by R.StuffId
	;";
				$query = $this->db->query($sql);
				if ($query->num_rows() > 0) {
					$rs = $query->result();
					foreach ($rs as $rows) {
						if ($rows->StuffId > 0)
						$stuffIdsstr[]=$rows->StuffId;
					}
				}
				$arr = $stuffIdsstr;
				
				
				$stuffIdsstr = implode(',', $stuffIdsstr);
				$SearchRows.= "  AND  K.StuffId in ($stuffIdsstr)";
			}
			
			$sql = "
			SELECT SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty,X.OrderQty)) AS OrderQty,
				SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*X.Price*C.Rate,X.OrderQty*X.Price*C.Rate)) AS OrderAmount ,SUM(K.tStockQty*X.Price*C.Rate) AS  Amount ,Group_concat(X.StuffId ) stuffs 
				FROM (
				SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty,A.Price  
				         FROM(
				            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price    
										FROM  ck9_stocksheet K
										LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
										LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
										LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType
				                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0    
				                        AND  G.ywOrderDTime>'$lastYear-01-01' 
										WHERE  K.tStockQty>0   AND TM.blSign=1 $SearchRows  Group by K.StuffId  
						UNION ALL 
				             SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price   
										FROM  ck9_stocksheet K
										LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
										LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
										LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType
				                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0    
				                        AND  G.ywOrderDTime>'$lastYear-01-01'  
				                        LEFT JOIN  ck5_llsheet R ON R.StockId=G.StockId 
										WHERE  K.tStockQty>0  AND TM.blSign=1 $SearchRows  Group by K.StuffId
				       )A GROUP BY A.StuffId 
				)X 
				LEFT JOIN  ck9_stocksheet K ON K.StuffId=X.StuffId  
				LEFT JOIN  bps B ON B.StuffId=X.StuffId 
				LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency 
				
				 ;";
			$query = $this->db->query($sql);
			if ($aStuffId!='') {
				return $query->row()->OrderQty;
			}
			return  array('row'=>$query->row_array(), 'stuff'=>$arr);
			
		} else {
			$sql = "SELECT SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty,X.OrderQty)) AS OrderQty,
				SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*X.Price*C.Rate,X.OrderQty*X.Price*C.Rate)) AS OrderAmount  
				FROM (
				SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty,A.Price  
				         FROM(
				            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price    
										FROM  ck9_stocksheet K
										LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
										LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
										LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType
				                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0    
				                        AND  G.ywOrderDTime>'$lastYear-01-01' 
										WHERE  K.tStockQty>0   AND TM.blSign=1 $SearchRows  Group by K.StuffId  
						UNION ALL 
				             SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price   
										FROM  ck9_stocksheet K
										LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
										LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
										LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType
				                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0    
				                        AND  G.ywOrderDTime>'$lastYear-01-01'  
				                        LEFT JOIN  ck5_llsheet R ON R.StockId=G.StockId 
										WHERE  K.tStockQty>0  AND TM.blSign=1 $SearchRows  Group by K.StuffId
				       )A GROUP BY A.StuffId 
				)X 
				LEFT JOIN  ck9_stocksheet K ON K.StuffId=X.StuffId  
				LEFT JOIN  bps B ON B.StuffId=X.StuffId 
				LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency 
				WHERE 1  ";

			
		}
		$query = $this->db->query($sql);
		return  $query;
	}


	
	//超过三个月未出的
	public function get_over3month_notout() {
		
		
				$sql = "SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*D.Price*C.Rate) AS YearAmount
				FROM (
						SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN  yw1_ordersheet Y ON Y.POrderId=S.POrderId
				        LEFT JOIN  yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
						WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3  ";
		$query = $this->db->query($sql);
		return  $query;
	}
	
	//三个月以上未出的
	public function get_over3m_notout($sendFloor) {
		
		
		$sql = "SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS YearAmount

				FROM (
						SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN  yw1_ordersheet Y ON Y.POrderId=S.POrderId
				        LEFT JOIN  yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
						WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3 AND D.SendFloor=? ";
				
				if ($sendFloor == 'all') {
					$sql = "SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS YearAmount

				FROM (
						SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN  yw1_ordersheet Y ON Y.POrderId=S.POrderId
				        LEFT JOIN  yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
						WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3  ";
				}
		$query = $this->db->query($sql,$sendFloor);
		return  $query;
	}

	//1个月以内未出的
	public function get_in1m_notout($sendFloor) {
		
		
		$sql = "SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS YearAmount

				FROM (
						SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN  yw1_ordersheet Y ON Y.POrderId=S.POrderId
				        LEFT JOIN  yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
						WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())<1  AND D.SendFloor=? ";
				if ($sendFloor == 'all') {
					$sql = "SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS YearAmount

				FROM (
						SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN  yw1_ordersheet Y ON Y.POrderId=S.POrderId
				        LEFT JOIN  yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
						WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())<1  ";
				}
		$query = $this->db->query($sql,$sendFloor);
		return  $query;
	}
	
	//获取所有在库数量（按仓库分类）
	public function get_warehouse_qty($WarehouseId='')
	{
	    $SearchRows=$WarehouseId==''?'':' AND BM.WarehouseId=' . $WarehouseId;
		$sql = "SELECT BM.WarehouseId,SUM(K.tStockQty) as Qty,COUNT(1) AS Counts,
		SUM(K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  base_mposition BM ON BM.Id=S.SendFloor 
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						LEFT JOIN  bps B ON B.StuffId=D.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0 AND TM.blSign=1   GROUP by BM.WarehouseId ";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $WarehouseId=$row['WarehouseId'];
	        $rowsArray[$WarehouseId]['Counts']=$row['Counts'];
	        $rowsArray[$WarehouseId]['Qty']=$row['Qty'];
	        $rowsArray[$WarehouseId]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
	}
	
	//获取有单库存数量（按仓库分类）
	public function get_warehouse_hasorder($WarehouseId='')
	{
	    $SearchRows=$WarehouseId==''?'':' AND BM.WarehouseId=' . $WarehouseId;
	    
	    $startDate = date('Y') .'-01-01';
		$sql = "SELECT BM.WarehouseId,SUM(IF(S.OrderQty>K.tStockQty,K.tStockQty,S.OrderQty)) AS Qty,COUNT(1) AS Counts,
						SUM(IF(S.OrderQty>K.tStockQty,K.tStockQty*S.Price*C.Rate,S.OrderQty*S.Price*C.Rate)) AS Amount  
						FROM (
						   SELECT A.SendFloor,A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty,A.Price FROM(
						            SELECT D.SendFloor,K.StuffId,
						                   SUM(IFNULL(G.OrderQty,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price    
												FROM  ck9_stocksheet K
												LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0   
						                        AND  G.ywOrderDTime>'$startDate'  
												WHERE  K.tStockQty>0  $SearchRows AND D.ComboxSign=0  AND TM.blSign=1 Group by K.StuffId  
								   UNION ALL 
						              SELECT D.SendFloor,K.StuffId,
						                     SUM(IFNULL(R.Qty*-1,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price   
												FROM  ck9_stocksheet K
												LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType  
						                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0  
						                        AND  G.ywOrderDTime>'$startDate'     
						                        LEFT JOIN  ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0  AND TM.blSign=1 Group by K.StuffId)A GROUP BY A.StuffId 
						)S 
						LEFT JOIN  base_mposition BM ON BM.Id=S.SendFloor
						LEFT JOIN  ck9_stocksheet K ON K.StuffId=S.StuffId  
						LEFT JOIN  bps B ON B.StuffId=S.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency 
						WHERE 1 GROUP BY BM.WarehouseId ";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $WarehouseId=$row['WarehouseId'];
	        $rowsArray[$WarehouseId]['Counts']=$row['Counts'];
	        $rowsArray[$WarehouseId]['Qty']=$row['Qty'];
	        $rowsArray[$WarehouseId]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
	}
	
	//获取1个月内有订单的配件库存
	public function get_warehouse_onemonth($WarehouseId='') 
	{
	     $SearchRows=$WarehouseId==''?'':' AND BM.WarehouseId=' . $WarehouseId;
	     
	     $sql = "SELECT BM.WarehouseId,SUM(A.tStockQty) AS Qty,COUNT(1) AS Counts,
	                      SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
				FROM (
						SELECT D.SendFloor,S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(S.ywOrderDTime,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN  stuffmaintype TM ON TM.Id = T.mainType 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid   
						WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0  AND TM.blSign=1  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  base_mposition BM ON BM.Id=A.SendFloor 
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())<1 GROUP BY BM.WarehouseId";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]['Counts']=$row['Counts'];
	        $rowsArray[$SendFloor]['Qty']=$row['Qty'];
	        $rowsArray[$SendFloor]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
  }
  
  //获取3个月以上下采购单的配件库存
	public function get_warehouse_overmonth($WarehouseId='') 
	{
	     $SearchRows=$WarehouseId==''?'':' AND BM.WarehouseId=' . $WarehouseId; 
	     
	     $sql = "SELECT BM.WarehouseId,SUM(A.tStockQty) AS Qty,COUNT(1) AS Counts,
	                      SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
				FROM (
						SELECT D.SendFloor,S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(S.ywOrderDTime,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN  stuffmaintype TM ON TM.Id = T.mainType 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid   
						WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0  AND TM.blSign=1  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  base_mposition BM ON BM.Id=A.SendFloor 
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3 GROUP BY A.SendFloor";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]['Counts']=$row['Counts'];
	        $rowsArray[$SendFloor]['Qty']=$row['Qty'];
	        $rowsArray[$SendFloor]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
  }
  
  //获取所有在库数量（按仓库分类）
	public function get_sendfloor_qty($sendFloor='')
	{
	    $SearchRows=$sendFloor==''?'':' AND D.SendFloor=' . $sendFloor;
		$sql = "SELECT D.SendFloor,SUM(K.tStockQty) as Qty,COUNT(1) AS Counts,
		SUM(K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						LEFT JOIN  bps B ON B.StuffId=D.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0 AND TM.blSign=1   GROUP by D.SendFloor ";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]['Counts']=$row['Counts'];
	        $rowsArray[$SendFloor]['Qty']=$row['Qty'];
	        $rowsArray[$SendFloor]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
	}
	
	//获取有单库存数量（按仓库分类）
	public function get_sendfloor_hasorder($sendFloor='')
	{
	    $SearchRows=$sendFloor==''?'':' AND D.SendFloor=' . $sendFloor;
	    
	    $startDate = date('Y') .'-01-01';
		$sql = "SELECT S.SendFloor,SUM(IF(S.OrderQty>K.tStockQty,K.tStockQty,S.OrderQty)) AS Qty,COUNT(1) AS Counts,
						SUM(IF(S.OrderQty>K.tStockQty,K.tStockQty*S.Price*C.Rate,S.OrderQty*S.Price*C.Rate)) AS Amount  
						FROM (
						   SELECT A.SendFloor,A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty,A.Price FROM(
						            SELECT D.SendFloor,K.StuffId,
						                   SUM(IFNULL(G.OrderQty,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price    
												FROM  ck9_stocksheet K
												LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0   
						                        AND  G.ywOrderDTime>'$startDate'  
												WHERE  K.tStockQty>0  $SearchRows AND D.ComboxSign=0  AND TM.blSign=1 Group by K.StuffId  
								   UNION ALL 
						              SELECT D.SendFloor,K.StuffId,
						                     SUM(IFNULL(R.Qty*-1,0)) AS OrderQty,IF(D.CostPrice=0,D.Price,D.CostPrice) as Price   
												FROM  ck9_stocksheet K
												LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType  
						                        LEFT JOIN  cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0  
						                        AND  G.ywOrderDTime>'$startDate'     
						                        LEFT JOIN  ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0  AND TM.blSign=1 Group by K.StuffId)A GROUP BY A.StuffId 
						)S 
						LEFT JOIN  ck9_stocksheet K ON K.StuffId=S.StuffId  
						LEFT JOIN  bps B ON B.StuffId=S.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency 
						WHERE 1 GROUP BY S.SendFloor ";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]['Counts']=$row['Counts'];
	        $rowsArray[$SendFloor]['Qty']=$row['Qty'];
	        $rowsArray[$SendFloor]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
	}
	
	//获取1个月内有订单的配件库存
	public function get_sendfloor_onemonth($sendFloor='') 
	{
	     $SearchRows=$sendFloor==''?'':' AND D.SendFloor=' . $sendFloor;
	     
	     $sql = "SELECT A.SendFloor,SUM(A.tStockQty) AS Qty,COUNT(1) AS Counts,
	                      SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
				FROM (
						SELECT D.SendFloor,S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(S.ywOrderDTime,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN  stuffmaintype TM ON TM.Id = T.mainType 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid   
						WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0  AND TM.blSign=1  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())<1 GROUP BY A.SendFloor";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]['Counts']=$row['Counts'];
	        $rowsArray[$SendFloor]['Qty']=$row['Qty'];
	        $rowsArray[$SendFloor]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
  }
  
  //获取3个月以上下采购单的配件库存
	public function get_sendfloor_overmonth($sendFloor='') 
	{
	     $SearchRows=$sendFloor==''?'':' AND D.SendFloor=' . $sendFloor;
	     
	     $sql = "SELECT A.SendFloor,SUM(A.tStockQty) AS Qty,COUNT(1) AS Counts,
	                      SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
				FROM (
						SELECT D.SendFloor,S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(S.ywOrderDTime,M.Date)) AS DTime 
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN  stuffmaintype TM ON TM.Id = T.mainType 
						LEFT JOIN  bps B ON B.StuffId=K.StuffId   
						LEFT JOIN  cg1_stocksheet S ON S.StuffId=K.StuffId
						LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid   
						WHERE  K.tStockQty>0 $SearchRows AND D.ComboxSign=0  AND TM.blSign=1  GROUP BY K.StuffId 
				)A 
				LEFT JOIN  stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN  trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3 GROUP BY A.SendFloor";			
		$query = $this->db->query($sql);
		$rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]['Counts']=$row['Counts'];
	        $rowsArray[$SendFloor]['Qty']=$row['Qty'];
	        $rowsArray[$SendFloor]['Amount']=round($row['Amount'],2);
	    }
	    return $rowsArray;
  }
}