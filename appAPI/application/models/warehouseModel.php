<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  WarehouseModel extends MC_Model {

    public $SetTypeId= null;
    
    function __construct()
    {
        parent::__construct();
    }
    
     function get_records($Id)
    {
	    $sql = 'SELECT Id,Name,WorkAdd,Floor,SendFloor,Remark,Address,Estate,Locks FROM warehouse WHERE Id=? LIMIT 1';
	          
	   $query=$this->db->query($sql,array($Id));
	   return  $query->first_row('array');        
    }
    
    function get_warehourse($sendFloor)
    {
	     $sql = "SELECT Id,Name,WorkAdd,Floor,SendFloor,Remark,Address,Estate,Locks FROM warehouse WHERE FIND_IN_SET(SendFloor,$sendFloor) LIMIT 1";
	          
	     $query=$this->db->query($sql);
	      return  $query->first_row('array');   
    }
    
    function get_warehourse_byname($name)
    {
	     $sql = "SELECT Id,Name,WorkAdd,Floor,SendFloor,Remark,Address,Estate,Locks FROM warehouse WHERE Name='$name' LIMIT 1";
	          
	     $query=$this->db->query($sql);
	      return  $query->first_row('array');   
    }
    
   
  
    //获取仓库信息 $getSign 获取数据类型  
    function get_warehouse($getSign,$setTypeId,$params='',$Type=1) {
        $dataArray=array();
        
        switch($getSign){
	        case 1:
	             $this->load->model('AppUserSetModel');
		         $params=$this->AppUserSetModel->get_parameters($this->LoginNumber,$setTypeId);
		         $paramsArray=explode(',', $params);  
	           break;
	        case 2:
	             $paramsArray=explode(',', $params);
	           break;
	        default:
	             $paramsArray=array();
	           break;
        }
	    $pcounts=count($paramsArray); 
	    $selected=$pcounts>0?0:1;
	     
	    $this->db->select('Id,Name');
	    $this->db->where(array('Estate =' => 1,'Type'=>$Type));
	    $this->db->order_by('SortId','asc');
	    $query = $this->db->get('warehouse');
	   
		foreach($query->result_array() as $row){
		    $id=$row['Id'];
		    $name=$row['Name'];
		    
		    $selected=$pcounts>0?(in_array($id,$paramsArray)?1:0):$selected;
		    
		    $dataArray[]=array(
			    'cellType'=>"1",
				'title'=>"$name",
				'selected'=>"$selected",
				'Id'=>"$id"
			);
			$selected = 0;
		}
		return $dataArray;
   }

	 
}