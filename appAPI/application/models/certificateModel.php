<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CertificateModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


    public function get_item($params=array()){

		
        $sql    = "SELECT D.Id,D.Caption,D.Attached,D.EndDate 
		           FROM zw2_hzdoc D 
		           WHERE  D.Id IN (366,1178,1179,1180,1197,1251,1263) 
		           ORDER BY  Field(Id,1178,1180,1179,1263,366,1251,1197)";
        $query=$this->db->query($sql);
        
        return $query;
    }
}