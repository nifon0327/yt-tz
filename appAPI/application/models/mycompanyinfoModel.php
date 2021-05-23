<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MyCompanyInfoModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

	
	public function get_records($Id){
		 $sql = "SELECT Id,Company,Forshort,Tel,Fax,Address,ZIP,WebSite,LinkMan,Mobile,Email,Estate 
		         FROM my1_companyinfo 
		         WHERE Id=? LIMIT 1"; 
		         
	     $query=$this->db->query($sql,$Id);
	     
	     return  $query->first_row('array');
	}
	
	public function get_fields($Id,$fieldname)
	{
	    $this->db->select($fieldname);
	    $this->db->where('Id',$Id);
	    $query = $this->db->get('my1_companyinfo');
	    $rows=$query->first_row('array');
	    return  $rows[$fieldname];
	}
	 
}