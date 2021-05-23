<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StaffGroupModel extends MC_Model{

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定GroupId的记录
	function get_records($id=0){
	
	   $sql = "SELECT BranchId,GroupId,GroupName,GroupLeader,TypeId,Date,Estate,Locks FROM staffgroup WHERE GroupId=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}

}