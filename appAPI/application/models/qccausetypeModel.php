<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  QcCauseTypeModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
        
    }
    
    //返回指定Id的记录
	function get_records($id=0)
	{
	
	   $sql = "SELECT Id,Cause,Type,Estate,Locks,Date,Operator 
	           FROM qc_causetype  WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   
	   return  $query->first_row('array');
	}
    	
    function get_type_cause($Type=0)
    {
	   $sql = "SELECT Id,Cause AS Name ,if(Id=148,'1','0') as noReqty  
	           FROM qc_causetype  WHERE Type=? AND Estate=1"; 	
	   $query=$this->db->query($sql,array($Type)); 
	   return $query->result_array();
    }
    
    
}