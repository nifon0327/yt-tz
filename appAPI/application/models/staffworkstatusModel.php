<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StaffWorkStatusModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //返回上班人员总数
    function get_worksTotals($GroupId=0){
    
	    $this->db->where('CheckType',1);
	    if ($GroupId!=0){
	        $idArray=explode(',', $GroupId);
		    $this->db->where_in('GroupId',$idArray); 
	    }
	    
	    $this->db->from('staff_workstatus');
	    
        return $this->db->count_all_results();
    }
   
    /*
    function get_CurDateWorks($GroupId=0){
    
	    $this->db->where('Date',$this->Date);
	    
	    if ($GroupId!=0){
	        $idArray=explode(',', $GroupId);
		    $this->db->where_in('GroupId',$idArray); 
	    }
	    
	    $this->db->from('staff_workstatus');
	    
        return $this->db->count_all_results();
    }
    */
    
    function update_estate(){
	   $sql='UPDATE     staff_workstatus A 
			 INNER JOIN staffmain M ON M.Number=A.Number
			 SET A.CheckType=0,A.Date=CURDATE()
			 WHERE (A.CheckType=1 AND A.Date!=CURDATE() AND M.KqSign=3) OR M.Estate=0';
	   $this->db->query($sql);
   }

}