<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CgSemifinishedModel extends MC_Model {

     function get_processing_price($mStockId)
     {
         $Price = 0;
	      $sql = "SELECT G.Price FROM cg1_semifinished S 
                         INNER  JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId 
                         INNER JOIN cg1_stocksheet G  ON G.StockId = S.StockId
                         INNER JOIN stuffdata D ON D.StuffId = G.StuffId
                         INNER JOIN stufftype T ON T.TypeId = D.TypeId
                        WHERE  S.mStockId='$mStockId'  AND   T.mainType ='3' "; 	
                        
           $query=$this->db->query($sql);
             if ($query->num_rows()>0){
	              $rows = $query->first_row('array');
	              $Price = $rows['Price'];
             }
	       
	        return $Price;
   }
	 	 
}