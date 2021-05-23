<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  QcMissionModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($Sid){
	
	   $sql = "SELECT Id,LineId,Remark,Estate,rkSign,DateTime,Operator 
	           FROM qc_mission  WHERE Sid=?"; 	
	   $query=$this->db->query($sql,array($Sid));
	   return  $query->first_row('array');
	}
		
	//保存分配拉线设置
	function save_records($Sid,$LineId){
	    
	    if ($Sid>0 && $LineId>0){
	         
	      $data=array(
                    'Sid'=>$Sid,  
                 'LineId'=>$LineId,
                 'rkSign'=>1,
               'DateTime'=>$this->DateTime,
               'Operator'=>$this->LoginNumber,
                'creator'=>$this->LoginNumber,
                'created'=>$this->DateTime 
	       );
	       
	       $this->db->insert('qc_mission', $data); 
	       
	       return 1;
	       //return $this->db->insert_id()>0?1:0;
	   }else{
		   return 0;
	   }
	}
}