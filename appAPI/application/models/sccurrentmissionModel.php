<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScCurrentMissionModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT Id,sPOrderId,LineId,Operator,DateTime,Estate,FinishTime
	           FROM sc_currentmission  WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	//返加当前生产线记录
	function get_all_records(){
	   $sql = "SELECT GROUP_CONCAT(sPOrderId) AS sPOrderId FROM sc_currentmission"; 	
	   $query=$this->db->query($sql);
	   $row = $query->row_array();
	   
	   return $row['sPOrderId'];
	}
	
	//保存当前生产线设置
	function set_currentscline($sPOrderId,$lineId,$sortboxId,$line){
	
	    if ($sPOrderId!='' && $lineId>0){
	        $datetime=$this->DateTime;
	        $operator=$this->LoginNumber;
	        
	        $sql="INSERT INTO sc_currentmission (sPOrderId,LineId,SortboxId,LineNumber,DateTime,Estate,Operator)
	              VALUES ('$sPOrderId','$lineId','$sortboxId','$line','$datetime','1','$operator')  
	              ON DUPLICATE KEY UPDATE sPOrderId='$sPOrderId',SortboxId='$sortboxId',LineNumber='$line',DateTime='$datetime',Operator='$operator' ";
	        $this->db->query($sql); 
	        return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}
}