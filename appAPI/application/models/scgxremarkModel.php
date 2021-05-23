<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScgxRemarkModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
	function get_remark($sporderid){
	
	   $sql = "SELECT Remark,creator,created,ProcessId 
	           FROM scgx_remark  WHERE 1 AND sPOrderId=$sporderid AND Estate=1 order by id desc limit 1"; 	
	   $query=$this->db->query($sql);
	   return  $query;
	}
	
	function get_process_remark($sporderid,$processid){
	
	   $sql = "SELECT Remark,creator,created,ProcessId 
	           FROM scgx_remark  WHERE ProcessId=$processid AND sPOrderId=$sporderid  AND Estate=1 order by id desc limit 1"; 	
	   $query=$this->db->query($sql);
	   return  $query;
	}
	
	function get_process_remark_in($sporderid,$processid,$time1,$time2){
	
	   $sql = "SELECT Remark,creator,created,ProcessId 
	           FROM scgx_remark  WHERE ProcessId=$processid AND sPOrderId=$sporderid  AND Estate=1 AND created>='$time1' AND created<='$time2' order by id desc limit 1"; 	
	   $query=$this->db->query($sql);
	   return  $query;
	}
	function get_remark_in($sporderid,$time1,$time2){
	
	   $sql = "SELECT Remark,creator,created,ProcessId 
	           FROM scgx_remark  WHERE sPOrderId=$sporderid  AND Estate=1 AND created>='$time1' AND created<='$time2' order by id desc limit 1"; 	
	   $query=$this->db->query($sql);
	   return  $query;
	}
	
	function save_item_auto($params){
	
		$remark = trim( element('rmk',$params,''));
		
	    if ($remark!=''){
	        $data=array(
	           'ProcessId'=>element('pid',$params,'0'), 
               'Remark'=>$remark,
               'Date'=>element('date',$params,''),
               'Operator'=>element('oper',$params,''),
               'creator'=>element('oper',$params,''),
               'created'=>element('time',$params,''),
               'sPOrderId'=>element('id',$params,''),
               'modifier'=>element('oper',$params,'')
	       );
	       $this->db->insert('scgx_remark', $data); 
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}


	function save_item($params){
	
		$remark = trim( element('remark',$params,''));
		
	    if ($remark!=''){
	        $data=array(
	           'ProcessId'=>element('ProcessId',$params,'0'), 
               'Remark'=>$remark,
               'Date'=>$this->Date,
               'Operator'=>$this->LoginNumber,
               'creator'=>$this->LoginNumber,
               'created'=>$this->DateTime,
               'sPOrderId'=>element('Id',$params,'')
	       );
	       $this->db->insert('scgx_remark', $data); 
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}
}