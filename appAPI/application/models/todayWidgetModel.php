<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  TodayWidgetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
  
    public function get_badge_value($menutype){
      $this->load->model('LoginUser');
       
      $dataArray=array();
         
	  $sql="SELECT ModuleId,name,oldModuleId,oldItemId,leftbadge,rightbadge FROM ac_menus WHERE parent_id=0 and typeid=? and Estate=1 and (LENGTH(leftbadge)>0 OR LENGTH(rightbadge)>0) order by Id";
	  $query = $this->db->query($sql,$menutype);
	  foreach($query->result_array() as $row){
	       $checkSign = true; 
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){
	       
	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }
	       
	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          } 
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }
	       
	       if ($checkSign==true){
	         $left_value="";
	         $right_value=""; 
	         if ($row['leftbadge']!=""){
	           $badges=explode('/', $row['leftbadge']);
	             if (count($badges)==1){
			          $left_value=$this->$badges[0]();  
		         }
		         else{
			         $this->load->model($badges[0]);
		             $left_value=$this->$badges[0]->$badges[1]();        
		         }
		      }
		      
		      if ($row['rightbadge']!=""){
	           $badges=explode('/', $row['rightbadge']);
	             if (count($badges)==1){
			          $right_value=$this->$badges[0]();  
		         }
		         else{
			         $this->load->model($badges[0]);
		             $right_value=$this->$badges[0]->$badges[1]();        
		         }
		      }
		      
		       $dataArray[]=array(
		           'id'=>$row['ModuleId'],
		           'moduleid'=>$row['ModuleId'],
		           'name'=>$row['name'],
		           'leftBadge'=>"$left_value",
		           'rightBadge'=>"$right_value",
		           'color'=>''
		      );  
	       }
    }
    return $dataArray;
  }
  
  public function getTotalClients(){
	   //读取无线用户数
       return  file_get_contents("http://192.168.16.2/Web_Query/Cisco_WLC_TotalClients.php");
  }
}