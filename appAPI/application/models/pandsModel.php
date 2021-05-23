<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  pandsModel extends MC_Model{

    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_relation($ProductId){
	
	   $sql = "SELECT A.Relation,D.Spec,D.Weight FROM pands A
		       LEFT JOIN stuffdata D ON D.StuffId=A.StuffId 
		       WHERE A.ProductId=? AND D.TypeId=9040 LIMIT 1"; 	
	   $query=$this->db->query($sql,array($ProductId));
	   $row = $query->row_array();
	   return $row;
	}
    
    
}