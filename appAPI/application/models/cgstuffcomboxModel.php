<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CgStuffcomboxModel extends MC_Model {

     function get_stuffcombox_list($mStockId,$sPOrderId='',$OrderQty)
     {
          $Searchs = $sPOrderId==''?'':" AND L.sPOrderId='$sPOrderId'  ";
          
	      $sql = "SELECT A.StockId,A.StuffId,ROUND($OrderQty*(A.OrderQty/G.OrderQty),U.Decimals) AS OrderQty,
	                   D.StuffCname,U.Name AS UnitName,D.SendFloor,D.Picture,K.tStockQty,
	                   A.llQty,IF(A.llEstate>0,1,0) AS  llEstate,G.CompanyId,T.mainType,TM.blSign,C.Forshort,U.Decimals  
						FROM(
						     SELECT A.mStockId,A.StockId,A.StuffId,A.OrderQty,IFNULL(SUM(L.Qty),0) as llQty,IFNULL(SUM(L.Estate),0) as llEstate 
	                         FROM cg1_stuffcombox A  
	                         LEFT JOIN ck5_llsheet L ON A.StockId=L.StockId  $Searchs 
	                         WHERE  A.mStockId='$mStockId' GROUP BY A.StockId 
	                   )A 
	                    INNER JOIN cg1_stocksheet G ON G.StockId=A.mStockId   
						INNER JOIN stuffdata D ON D.StuffId = A.StuffId
						INNER JOIN stuffunit U ON U.Id=D.Unit 
						INNER JOIN stufftype T ON T.TypeId=D.TypeId
			   	        INNER JOIN stuffmainType TM ON TM.Id=T.mainType 
						INNER JOIN ck9_stocksheet K ON K.StuffId=D.StuffId 
						LEFT JOIN  trade_object C ON C.CompanyId=G.CompanyId  
						WHERE  1 "; 	
                        
           $query=$this->db->query($sql);
		   return $query->result_array();	
    }
    
    
   function get_records($StockId){
	
	   $sql = "SELECT S.Id,S.POrderId,S.mStockId,S.StockId,S.mStuffId,S.StuffId, S.Relation, 
			   S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.Date,S.Operator,S.Estate,S.Date,S.Operator 
			   FROM cg1_stuffcombox S WHERE S.StockId=?";
	   $query=$this->db->query($sql,array($StockId));
	   
	   return $query->first_row('array');
	}
	 	 
}