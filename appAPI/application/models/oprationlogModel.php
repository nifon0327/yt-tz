<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// xin
class  OprationlogModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

	public function save_item($params=array()) {

		$data = array('DateTime'       =>element('DateTime',$params,$this->DateTime),
					  'Operator'       =>element('Operator',$params,$this->LoginNumber),
					  'Item'           =>element('LogItem',$params,''),
					  'Funtion'        =>element('LogFunction',$params,''),
					  'Log'            =>element('Log',$params,''),
					  'OperationResult'=>element('OperationResult',$params,'N'),
					  'created'        =>$this->DateTime,
					  'creator'        =>$this->LoginNumber,
					  'Date'           =>$this->Date
					  );
		
		$this->db->trans_begin();
		$query     = $this->db->insert('oprationlog', $data); 
		$insert_id = $this->db->insert_id();
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
		} else {
			    $this->db->trans_commit();
		}
		if ($insert_id > 0){
			  						  
				  return $insert_id;
		} else {
			return -1;	
		}
		
	}	

	 
}