<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CompanyInfoModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }

	
	public function get_records($CompanyId){
		 $sql = "SELECT CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,BankUID,BankAccounts,IBAN,Estate 
		         FROM companyinfo 
		         WHERE CompanyId=? AND Estate=1 ORDER BY Id DESC LIMIT 1"; 
		         
	     $query=$this->db->query($sql,array($CompanyId));
	     
	     return  $query->first_row('array');
	}
	
	public function get_fields($CompanyId,$fieldname)
	{
	    $this->db->select($fieldname);
	    $this->db->where('Id',$Id);
	    $query = $this->db->get('companyinfo');
	    $rows=$query->first_row('array');
	    return  $rows[$fieldname];
	}
}