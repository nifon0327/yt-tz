<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScRemarkModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
	function get_wsdate_remark($iddate,$wsid){
	
	   $sql = "SELECT Id,Remark,creator,created
	           FROM sc1_remark  WHERE Date='$iddate' AND WorkShopId=$wsid AND Estate=1 order by id desc limit 1"; 	
	   $query=$this->db->query($sql);
	   return  $query;
	}
	

	function save_item($params){
	
		$remark = trim( element('remark',$params,''));
		
	    if ($remark!=''){
	        $data=array(
	           'GroupId'=>element('GroupId',$params,'-1'), 
               'Remark'=>$remark,
               'Date'=>element('Id',$params,''),
               'Operator'=>$this->LoginNumber,
               'creator'=>$this->LoginNumber,
               'created'=>$this->DateTime,
               'WorkShopId'=>element('wsid',$params,'')
	       );
	       $this->db->insert('sc1_remark', $data); 
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}
}