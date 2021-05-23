<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewForwardModel  
* 新品转发记录类  sql: ac.new_forward 
* 
*/ 
class  Ck8bftypeModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
	
	function get_for_selectcell() {
		
		$query = $this->get_items_pick();
		$allItems = array();
		foreach ($query->result_array() as $row) {
			$Id = $row["Id"];
			$TypeName = $row["TypeName"];
			$allItems[]=array(
							   "title"    =>"$TypeName",
							   "Id"       =>"$Id",
							   "CellType" =>"5",
							   "selected" =>"0",
							   "infos"    =>""
							  ); 
		}
		return $allItems;
	}
	
	function get_items_pick() {
		$sql = "select Id,TypeName from ck8_bftype where Estate>=1";
		return $this->db->query($sql);
	}
	 
	 


}