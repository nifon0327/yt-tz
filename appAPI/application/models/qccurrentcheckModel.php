<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  QcCurrentCheckModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($LineId){
	
	   $sql = "SELECT Id,line,LineId,gys_Id,StockId,DateTime,Number,Weight
	           FROM qc_currentcheck  WHERE LineId=?"; 	
	   $query=$this->db->query($sql,array($LineId));
	   return  $query->first_row('array');
	}
	
	//返加当前生产线记录
	function get_all_records(){
	   $sql = "SELECT GROUP_CONCAT(stuffId) AS StuffId FROM qc_currentcheck"; 	
	   $query=$this->db->query($sql);
	   $row = $query->row_array();
	   
	   return $row['StuffId'];
	}
	
	//保存当前生产线设置
	function set_currentcheck($gys_Id,$lineId){
	
	    if ($gys_Id>0 && $lineId>0){
	         
	        $this->load->model('GysshsheetModel');
	        $records=$this->GysshsheetModel->get_records($gys_Id);
	        $StockId=$records['StockId'];
	        $StuffId=$records['StuffId'];
	        
	        $datetime=$this->DateTime;
	        $operator=$this->LoginNumber;
	        
	        $sql="UPDATE qc_currentcheck SET StockId='$StockId',StuffId='$StuffId',DateTime='$datetime',number='$operator' WHERE LineId='$lineId'";
	        $this->db->query($sql); 
	        return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}
}