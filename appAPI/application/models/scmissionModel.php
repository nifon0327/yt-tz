<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScMissionModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT Id,sPOrderId,Operator,DateTime,Estate,FinishTime
	           FROM sc1_mission  WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	//保存当前生产线设置
	function set_scline($sPOrderId,$lineId){
	
	    if ($sPOrderId!='' && $lineId>0){
	        $data=array(
	             'LineId'=>$lineId, 
              'sPOrderId'=>$sPOrderId,
               'DateTime'=>$this->DateTime,
                 'Estate'=>'1',
               'Operator'=>$this->LoginNumber 
	       );
	       $this->db->insert('sc1_mission', $data); 
	       
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}
}