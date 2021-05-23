<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  BaseMPositionModel extends MC_Model {

    public $SetTypeId= null;
    
    function __construct()
    {
        parent::__construct();
    }
    
     function get_records($Id)
    {
	    $sql = 'SELECT Id,Name,CheckSign,BlWorkShopId,Remark,Address,Estate,Locks FROM base_mposition WHERE Id=?  LIMIT 1';
	          
	   $query=$this->db->query($sql,array($Id));
	   return  $query->first_row('array');        
    }
    
    
    
     function get_name($Id)
    {
	    $sql = 'SELECT Name  FROM base_mposition WHERE Id=? LIMIT 1';
	          
	   $query=$this->db->query($sql,array($Id));
	   if ($query->num_rows() > 0) {
		   return $query->row()->Name;
	   }
	   return  '';        
    }
    
    
    function get_sendfloor()
    {
	   $sql = 'SELECT Id,Name,CheckSign,BlWorkShopId FROM base_mposition WHERE Estate=1';
	          
	   $query=$this->db->query($sql,array($Id));
	   return  $query->result_array(); 
    }

  
    //获取仓储位置  $getSign 获取数据类型   $semiSign 半成品加工
    function get_warehouse($getSign,$semiSign,$setTypeId,$params='') {
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
	    
	    $this->db->select('Id,Name');
	    $this->db->where(array('Estate =' => 1,'CheckSign !=' => 99));
	    $this->db->order_by('SortId','asc');
	    $query = $this->db->get('base_mposition');
	   
		foreach($query->result_array() as $row){
		    $id=$row['Id'];
		    $name=$row['Name'];
		    
		    $selected=$pcounts>0?(in_array($id,$paramsArray)?1:0):1;
		    $headimage=$this->get_icon($id);
		    
		    $dataArray[]=array(
			    'CellType'=>"1",
			    'headImage'=>"$headimage",
				'title'=>"$name",
				'selected'=>"$selected",
				'Id'=>"$id"
			);
		}
		return $dataArray;
   }
	
	 
   function get_icon($id){
	   return 'ck_'.$id;
   }

	 
}