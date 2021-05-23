<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  AcFirsttypeModel extends MC_Model {

     
    function __construct()
    {
        parent::__construct();
        
    }
    
    //返回指定Id的记录
	function get_records($FirstId)
	{
		   $sql = "SELECT Id,Letter,FirstId,ListName,Name,TypeId,Estate,Locks,Date,Operator 
		           FROM acfirsttype  WHERE FirstId=?"; 	
		   $query = $this->db->query($sql,array($FirstId));
		   $rows   =  $query->row_array();
		   return $rows;
	 }     	
}