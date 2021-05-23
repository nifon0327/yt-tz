<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  QcCjtjModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($Id){
	
	   $sql = "SELECT Id,StockId,StuffId,Qty,LineId,Remark,Date,Estate,Operator 
	           FROM qc_cjtj  WHERE Id=?";
	   $query=$this->db->query($sql,array($Id));
	   
	   return  $query->first_row('array');
	}
	
	
	function get_stockid($Id, $recordId='', $StuffId='') {
		
		$StockId = '';
		$sql = '';
		if ($Id != '') {
			$sql = "SELECT StockId  
	           FROM qc_cjtj  WHERE Id=$Id";
		} else if ($StuffId!='' && $recordId!='') {
			$sql = "SELECT StockId  
	           FROM qc_cjtj  WHERE StuffId=$StuffId AND recordId=$recordId Order by id desc limit 1";
		}
		if ($sql != '') {

			$query=$this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->row()->StockId;
			}
			
			
		}
		return $StockId;
	}
	
	
	function djrecords($Sid){
	
	   $sql = "SELECT D.Qty,D.Date,M.Name,D.Id FROM `qc_cjtj` D
LEFT JOIN staffmain M on M.Number=D.Operator
where D.Sid=? order by D.Id desc";
	   $query=$this->db->query($sql,array($Sid));
	   
	   return  $query;
	}
	
	function get_qcqty($Sid)
	{
		  $sql = "SELECT  SUM(Qty) AS Qty  FROM  qc_cjtj  WHERE Sid = ?"; 
          $query=$this->db->query($sql,array($Sid));
	      $row = $query->first_row('array');
	      return $row['Qty']==''?0:$row['Qty'];
	}
	
	function get_lastregister_time($Sid)
	{
		  $sql = "SELECT  max(Date) AS Date  FROM  qc_cjtj  WHERE Sid='$Sid' AND Estate=1 "; 
	      $query=$this->db->query($sql);
	      $row = $query->first_row('array');
	      
	      return $row['Date']==''?0:$row['Date'];
	}
	
	//获取未入库数量
	function get_unrkqty($Sid)
	{
		  $sql = "SELECT  SUM(Qty) AS Qty  FROM  qc_cjtj  WHERE Sid IN($Sid) AND Estate=1"; 
	      $query=$this->db->query($sql);
	      $row = $query->first_row('array');
	      
	      return $row['Qty']==''?0:$row['Qty'];
	}
	
	//获取未入库记录数量
	function get_unrk_counts($Sid)
	{
	   $sql = "SELECT  COUNT(*) AS Counts  FROM  qc_cjtj  WHERE Sid IN($Sid) AND Estate=1"; 
       $query=$this->db->query($sql);
       $row = $query->first_row('array');
       return $row['Counts']==''?0:$row['Counts'];
	}
	
		
	//保存品检记录
	function save_records($params)
	{
	    $Sid    = element('Id',$params,'0');
	    $djQty  = element('Qty',$params,'0');
	        
	    if ($Sid>0 && $djQty>0){   
	    
	        $this->load->model('GysshsheetModel');
		    $records=$this->GysshsheetModel->get_records($Sid);
		    $StockId=$records['StockId'];
		    $StuffId=$records['StuffId'];
		    $shQty  =$records['Qty'];
		    $records=null;
	        
	        $this->load->model('QcMissionModel');
		    $records=$this->QcMissionModel->get_records($Sid);
		    $LineId= $records['LineId'];
			 
			$djedqty  =$this->get_qcqty($Sid);
			
			if (($djedqty+$djQty)>$shQty){
				return 2;
			}
			     
		    $data=array(
	                'Sid'=>$Sid, 
	            'StockId'=>$StockId,
	            'StuffId'=>$StuffId,  
	                'Qty'=>$djQty, 
	             'LineId'=>$LineId,
	             'Remark'=>'',
	               'Date'=>$this->DateTime,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
	       
	       $this->db->insert('qc_cjtj', $data);
	       
	       $newId = $this->db->insert_id(); 
	       
	       return $newId>0?$newId:0;
	   }else{
		   return 0;
	   }
	}
}