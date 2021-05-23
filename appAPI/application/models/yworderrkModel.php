<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  YwOrderRkModel extends MC_Model{

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($POrderId){
	
	   $sql = "SELECT Id,POrderId,sPOrderId,ProductId,Qty,Price,scDate  
	           FROM yw1_orderrk 
	           WHERE POrderId=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	//返回工单的入库数量
	function get_rkqty($sPOrderId){
	
	   $sql  = "SELECT SUM(Qty)  AS Qty FROM yw1_orderrk WHERE sPOrderId=?"; 	
	   $query= $this->db->query($sql,array($sPOrderId));
	   $row  = $query->first_row('array');
	   
	   return $row['Qty']==''?0:$row['Qty'];
	}
	
	//保存入库记录
	function save_records($sPOrderId,$POrderId='',$StockId='', $locationId=0){
	
	   if ($POrderId==''){
		   $this->load->model('ScSheetModel');
		   $records=$this->ScSheetModel->get_records($sPOrderId);
		
		   $POrderId=$records['POrderId'];
		   $StockId =$records['StockId'];
	    }
	    
		$Operator=$this->LoginNumber;
		$sql="CALL proc_yw1_orderrk_update('$POrderId','$sPOrderId','$StockId','$locationId','$Operator') ";
		
		$this->db->query($sql);
	}
	
	
	//查找已存在的库存位置
	function get_product_location($ProductId)
	{
		$sql = "SELECT SUM(S.Qty-ifnull(C.Qty,0)) AS rkQty,S.LocationId,L.Identifier  
			FROM  (select sum(ifnull(C.Qty,0)) as Qty,C.LocationId,C.POrderId from yw1_orderrk C where C.ProductId=? GROUP BY C.POrderId ) S 
LEFT JOIN (select sum(ifnull(C.Qty,0)) as Qty,C.POrderId from ch1_shipsheet C where C.ProductId=? GROUP BY C.POrderId ) C on C.POrderId=S.POrderId
					
			LEFT JOIN ck_location L ON L.Id=S.LocationId 
			WHERE   1 GROUP BY S.LocationId;";
		$query = $this->db->query($sql,array($ProductId,$ProductId));
		return $query->result_array();
	}
	/**/
	
	//查找产品的数量（按区位）
	function get_region_productqty($ProductId ,$Region)
	{
	    $rkQty=0;
	    
	    if ($Region == '') {
		    $sql = "
		SELECT SUM(S.Qty-ifnull(C.Qty,0)) AS rkQty
				 FROM  (select sum(ifnull(C.Qty,0)) as Qty,C.LocationId,C.POrderId from yw1_orderrk C where C.ProductId=? GROUP BY C.POrderId )  S 
				 LEFT JOIN (select sum(ifnull(C.Qty,0)) as Qty,C.POrderId from ch1_shipsheet C where C.ProductId=? GROUP BY C.POrderId ) C on C.POrderId=S.POrderId
				 LEFT JOIN ck_location L ON L.Id=LocationId 
				 WHERE   1
	 ";
	 
	 
		$query = $this->db->query($sql,array($ProductId,$ProductId));
	    } else {
		    $sql = "
		SELECT SUM(S.Qty-ifnull(C.Qty,0)) AS rkQty
				 FROM  (select sum(ifnull(C.Qty,0)) as Qty,C.LocationId,C.POrderId from yw1_orderrk C where C.ProductId=? GROUP BY C.POrderId )  S 
				 LEFT JOIN (select sum(ifnull(C.Qty,0)) as Qty,C.POrderId from ch1_shipsheet C where C.ProductId=? GROUP BY C.POrderId ) C on C.POrderId=S.POrderId
				 LEFT JOIN ck_location L ON L.Id=LocationId 
				 WHERE   L.Region=? 
	 ";
	 
	 
		$query = $this->db->query($sql,array($ProductId,$ProductId,$Region));
	    }
		
		$rows  = $query->first_row('array');
		return  $rows['rkQty']==''?'':$rows['rkQty'];
	}
	
}