<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// xin
class  OrderExpressModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


	public function get_orderlock($porderid) {
        
        $this->db->select('Type');
        $this->db->from('yw2_orderexpress');
        $this->db->where(array('POrderId'=>$porderid,'Type'=>2)); 
        $this->db->order_by('Id','desc'); 
        $this->db->limit(1);   

        return $this->db->count_all_results()>0?true:false;
	}	
}