<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CgstocksheetModel extends MC_Model {
	
	 //返回指定StockId的记录
	function get_records($StockId, $Id=''){
	
	   $sql = "SELECT S.Id,S.Mid,S.POrderId,S.StockId,S.StuffId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.Price,S.CostPrice,
	           S.CompanyId,S.BuyerId,S.DeliveryDate,S.DeliveryWeek,S.StockRemark,S.AddRemark,S.cgSign,S.scSign,S.rkSign,S.blSign,
	           S.Estate,S.Locks,S.ywOrderDTime,S.Date,S.Operator 
	           FROM cg1_stocksheet S WHERE S.StockId=?";
	   if ($Id != '') {
		   $StockId = $Id;
		   
		    $sql = "SELECT S.Id,S.Mid,S.POrderId,S.StockId,S.StuffId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.Price,S.CostPrice,
	           S.CompanyId,S.BuyerId,S.DeliveryDate,S.DeliveryWeek,S.StockRemark,S.AddRemark,S.cgSign,S.scSign,S.rkSign,S.blSign,
	           S.Estate,S.Locks,S.ywOrderDTime,S.Date,S.Operator 
	           FROM cg1_stocksheet S WHERE S.Id=?";
	   }
	           
	           
	   $query=$this->db->query($sql,array($StockId));
	   
	   return $query->first_row('array');
	}
	
	//检查是否订单中最后一个需备料的配件
	function get_check_lastblsign($POrderId,$StuffId){
	    $LastBlSign =0 ;
		$sql = "SELECT COUNT(*) AS Counts,SUM(IF(K.tStockQty+IFNULL(S.Qty,0)>=G.OrderQty,1,0)) AS Nums,
		        SUM(IF(D.StuffId='$StuffId',1,0)) AS lastSign 
			    FROM yw1_ordersheet Y 
			    LEFT JOIN cg1_stocksheet G  ON Y.POrderId=G.POrderId 
			    LEFT JOIN ck9_stocksheet K ON K.StuffId = G.StuffId
			    LEFT JOIN stuffdata D ON D.StuffId = G.StuffId
			    LEFT JOIN stufftype T ON T.TypeId = D.TypeId
			    LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType 
			    LEFT JOIN gys_shsheet S ON S.StockId=G.StockId AND S.Estate>0 
			    WHERE Y.Estate>0 AND G.POrderId = '$POrderId'
			    AND TM.blSign=1 
			    AND K.tStockQty < G.OrderQty";
		$query=$this->db->query($sql);	
		
		if ($query->num_rows()>0) {
		      $rows = $query->row_array();
		      if ($rows["lastSign"]>0 && $rows["Nums"]==$rows["Counts"]){
			      $LastBlSign=$rows["Nums"];
		      }
		}							   							   
		return 	$LastBlSign;							   
	}
	
	
	//配件，订单锁定状态
	function get_check_locksign($POrderId,$StockId){
		
		$sql = "SELECT SUM(IFNULL(A.Locks,0)) AS Locks 
					FROM (
					    SELECT Locks FROM yw2_orderexpress WHERE POrderId =$POrderId AND Type='2'
					UNION ALL
					    SELECT Locks FROM cg1_lockstock WHERE StockId =$StockId AND Locks=0 
					UNION ALL 
					    SELECT getStockIdLock('$StockId') AS Locks 
					)A ";
					
        $query = $this->db->query($sql);
		$row =   $query->first_row('array');
		return $row['Locks']>0?1:0;
		/*	
		$sql =  "SELECT Id FROM yw2_orderexpress WHERE POrderId =? AND Type='2' LIMIT 1";
		$query = $this->db->query($sql,$POrderId);
		$row =  $query->num_rows();
		if($row>0){
			return 1 ; 
		}
        
        if($StockId>0){
	        $sql =  "SELECT Id FROM cg1_lockstock WHERE StockId =? AND Locks=0 LIMIT 1";
			$query = $this->db->query($sql,$StockId);
			$row =  $query->num_rows();
			if($row>0){
				return 1 ; 
			}
			
			$sql =  "SELECT getStockIdLock('$StockId') AS Locks";
			$query = $this->db->query($sql);
			$row =   $query->first_row('array');
			if($row['Locks']>0){
				return 1 ; 
			}
		}
		return 0 ;
       */
	}

}
