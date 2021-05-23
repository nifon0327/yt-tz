<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  SemifinishedBomModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT Id,mStuffId,StuffId,Relation,Date,Operator,creator,created 
	           FROM semifinished_bom  WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	function get_mstuffid_counts($StuffId,$returnSign=0)
	{
		$IdList=array();
		$this->get_semifinished_relation($StuffId,$IdList,0);
		
		if ($returnSign==1){
			if (count($IdList)>0){
						 return implode(',',$IdList);
					}else{
						return '';
					}
		}else{
			 return $IdList;  
		}
	}
	
	function get_semifinished_relation($StuffId,&$IdList,$depth)
	{
			$sql="SELECT mStuffId AS mStuffId FROM  semifinished_bom WHERE StuffId IN (?) GROUP BY mStuffId";
			 $query = $this->db->query($sql,$StuffId);
			 if ($query->num_rows() > 0) {
			   foreach($query->result_array() as $rows){
				      $mStuffId = $rows['mStuffId'];
				      if (!in_array($mStuffId, $IdList)){
					        $IdList[]=$mStuffId;  
					        if ($depth<10){
								  $this->get_semifinished_relation($mStuffId,$IdList,$depth+1);
							 }
				      }
			   }
			}
    }
	
}