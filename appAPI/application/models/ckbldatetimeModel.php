<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CkbldatetimeModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //1小时内可备料订单
	public function ck_onehours_blcounts($sendfloor)
	{
	   $SearchRows='';
	   switch($sendfloor){
		   case  3: $SearchRows=" AND S.WorkShopId='101'";break;
		   case 17: $SearchRows=" AND S.WorkShopId IN(102,103,104,105,106) ";break;
		   default: $SearchRows=" AND S.ActionId='105' ";break;
	   }
	   
	   $sql = "SELECT COUNT(*) AS Counts 
	       FROM (
	           SELECT  T.sPOrderId 
	                 FROM ck_bldatetime T  
	                 LEFT JOIN yw1_scsheet S ON S.sPOrderId=T.sPOrderId 
	                 WHERE T.Date=CURDATE() AND TIMESTAMPDIFF(HOUR,T.ableDate,NOW())<1 $SearchRows 
	        )A WHERE getCanStock(A.sPOrderId,0) IN (2,3)"; 	
	   $query=$this->db->query($sql);
	   $row = $query->first_row('array');
	   $Counts = $row['Counts'];
       return $Counts;
    }	 
}