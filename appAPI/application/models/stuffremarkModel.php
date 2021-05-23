<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StuffremarkModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    function get_stuff_remark($stuffid, $type=1){
	
	   $sql = "SELECT R.Id,R.Remark,M.Name,R.Date
	           FROM stuffremark R 
left join staffmain M on R.Operator=M.Number
 WHERE R.stuffid='$stuffid' AND R.type=$type  AND R.Estate=1 order by R.Id desc limit 1;"; 	
	   $query=$this->db->query($sql);
	   if ($query->num_rows() > 0) {
		   return $query->row_array();
	   }
	   return  null;
	}
	

	function save_item($params, $type=1){
	
	
	/*
		$inRecode="INSERT INTO $DataIn.stuffremark (Id,StuffId,Type,Remark,Date,Operator) VALUES (NULL,'$StuffId','1','$Remark','$curDate','$Operator')";
	*/
		$remark = trim( element('remark',$params,''));
		
	    if ($remark!=''){
	        $data=array(
	           'Date'=>$this->Date, 
               'Remark'=>$remark,
               'Type'=>$type,
               'Operator'=>$this->LoginNumber,
               'creator'=>$this->LoginNumber,
               'created'=>$this->DateTime,
               'StuffId'=>element('Id',$params,'')
	       );
	       $this->db->insert('stuffremark', $data); 
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}

}