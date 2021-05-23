<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  QcsclineModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
        
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT Id,Floor,LineNo,Line,Name,Estate,Locks,Date,Operator 
	           FROM qc_scline  WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   
	   return  $query->first_row('array');
	}
	
	//获取品检线号
	 function get_sclineNo($Floor=3) 
	 {
        $dataArray=array();  
	    $sql = "SELECT Id,LineNo FROM qc_scline WHERE Estate=1 AND Floor=?"; 
	           
	    $query=$this->db->query($sql,array($Floor));      
		foreach($query->result_array() as $rows){
		    $Id=$rows['Id'];
		    $dataArray[$Id]=$rows['LineNo'];
		}
		
		return $dataArray;
     }
     
      function get_refreshTV($Floor=3) 
      {
        $this->load->model('OtdisplayModel');
        
        $dataArray=array();
        
	    $sql = "SELECT Id,RefreshTV FROM qc_scline WHERE Estate=1 AND LENGTH(RefreshTV)>0 AND Floor=?"; 
	           
	    $query=$this->db->query($sql,array($Floor));      
		foreach($query->result_array() as $rows){
		    $Id=$rows['Id'];
		    $tvs=$this->OtdisplayModel->get_display_tvip($rows['RefreshTV']);
		    $dataArray[$Id]=$tvs;
		}
		
		return $dataArray;
      }
    
    //获取品检拉线名称
    function get_scline($Floor=3) 
    {
        $dataArray=array();  
	    $sql = "SELECT Id,LineNo,Name FROM qc_scline WHERE Estate=1 AND Floor=?"; 
	         
	    $query=$this->db->query($sql,array($Floor));      
		foreach($query->result_array() as $rows){
		    $dataArray[]=array(
			        'Id'   => $rows['Id'],
					'line' => $rows['LineNo'],
					'Name' => out_format($rows['Name'],'未设置') 
			);
		}
		
		return $dataArray;
    }
    	
}