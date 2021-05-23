<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ChShipsheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_company_sheet($Mid,$CompanyIds)
    {
       $dataArray=array();
       $sql="SELECT M.* FROM ch1_shipmain M 
			 LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
			 WHERE M.Id>$Mid AND  M.CompanyId IN ($CompanyIds) AND M.OPdatetime>='2016-04-15'";
       $query = $this->db->query($sql);
       
       if ($query->num_rows()>0){
	       $dataArray['main'] = $query->result_array();
	       
	       	       
	       $sql2="SELECT S.* FROM ch1_shipmain M 
			 LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
			 WHERE M.Id>$Mid AND  M.CompanyId IN ($CompanyIds) AND M.OPdatetime>='2016-04-15'";
		   $query2 = $this->db->query($sql2); 
		   $dataArray['sheet'] = $query2->result_array();
       }    
       return $dataArray;
    }
    
}