<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  AppUserSetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //获取APP个人设置信息
    public function get_parameters($Number,$TypeId) {
        $params='';
		//$sql = "SELECT Parameters FROM app_userset WHERE Number=? AND TypeId=?";   
		//$query=$this->db->query($sql,array($Number,$TypeId));
		$this->db->select('Parameters');
	    $query = $this->db->get_where('app_userset', array('Number' => $Number,'TypeId' => $TypeId), 0, 1);
		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $params=$row->Parameters;
	    }
	   return $params;
	}
	
	public function set_parameters($Number,$TypeId,$params){
	
	    $this->db->select('Id');
	    $query = $this->db->get_where('app_userset', array('Number' => $Number,'TypeId' => $TypeId), 0, 1);
		if ($query->num_rows()>0){//更新记录
		   $row = $query->first_row();
		   $id=$row->Id;
		   
		   $data=array('Parameters'=>$params,'modifier'=>$this->LoginNumber,'modified'=>$this->DateTime);
		   $this->db->update('app_userset', $data, "Id = $id");
		}
		else{//新增记录
			$data = array(
               'Number'     =>$Number,
               'TypeId'     =>$TypeId,
               'Parameters' =>$params,
               'Date'       => $this->Date,
               'Operator'   =>$this->LoginNumber,
               'creator'    => $this->LoginNumber,
               'created'    => $this->DateTime
            );
           $this->db->insert('app_userset', $data); 
		}
	}	
}