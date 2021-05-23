<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  WorkShopdataModel extends MC_Model {

    public $SetTypeId= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 1;
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT S.Id,S.Name,S.LeaderNumber,S.GroupId,S.WorkAddId,S.ActionId,S.semiSign,S.ScCheckSign,S.Floor,S.Estate,
	                  S.dFromId,B.Name AS AddressName,B.Address  
	           FROM workshopdata S
	           LEFT JOIN staffworkadd B ON B.Id=S.WorkAddId
	           WHERE S.Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	//返回半成品加工生产单位
	function get_semi_workshop($semiSign=1){
		$sql = "SELECT S.Id,S.Name,S.ActionId,S.ScCheckSign,S.Floor,S.Estate 
	           FROM workshopdata S
	           WHERE S.Estate=1 AND S.semiSign=? "; 	
	   $query=$this->db->query($sql,$semiSign);
	   return  $query->result_array(); ;
	}
	
    
    //获取生产单位名称  $getSign 获取数据类型   $semiSign 半成品加工
    function get_workshop($getSign,$semiSign,$params='') {
        $dataArray=array();
        switch($getSign){
	        case 1:
	             $this->load->model('AppUserSetModel');
		         $params=$this->AppUserSetModel->get_parameters($this->LoginNumber,$this->SetTypeId);
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
	   
	    $this->db->select('Id,Name,LeaderNumber,GroupId');
	    $query = $this->db->get_where('workshopdata', array('Estate' => 1,'semiSign' => $semiSign));
	  
		foreach($query->result_array() as $row){
		    $id=$row['Id'];
		    $name=$row['Name'];
		    $leaderNumber=$row['LeaderNumber'];
		    $groupId=$row['GroupId'];
		    
		    $selected=$pcounts>0?(in_array($id,$paramsArray)?1:0):1;
		    $headimage=$this->get_icon($id);
		    
		    $dataArray[]=array(
			    'CellType'=>'1',
				'headImage'=>"$headimage",
				'img_0'=>$headimage .'_0',
			    'img_1'=>$headimage .'_1',
				'title'=>"$name",
				'selected'=>"$selected",
				'leaderNumber'=>"$leaderNumber",
				'GroupId'=>"$groupId",
				'Id'=>"$id"
			);
		}
		return $dataArray;
   }
   
   //获取生产车间的工艺流程Id
   function get_workshop_actionid($id){
        $actionid=103;
        
	    $this->db->select('ActionId');
	    $query = $this->db->get_where('workshopdata', array('Id' => $id), 0, 1);
		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $actionid=$row->ActionId;
	    }
	    return $actionid;
   }
   
    //获取生产车间的备料周
   function get_canstock_weeks($id){
        $canweeks=0;
        
	    $this->db->select('CanStockWeek');
	    $query = $this->db->get_where('workshopdata', array('Id' => $id), 0, 1);
		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $canweeks=$row->CanStockWeek;
		    
		   
		    $thisDate=$this->Date;
		    switch($canweeks){
			    case 0:
			        $canweeks=$this->ThisWeek;
			        break;
			    case -1://皮套备料
			        $candays=date('w')>=4 || date('w')==0?7:0;
			        $candate=date("Y-m-d",strtotime("$thisDate  +$candays day"));
			        $canweeks=$this->get_DateWeek($candate);
			        break;
			    default:
			         $candays=$canweeks*7;
			         $candate=date("Y-m-d",strtotime("$thisDate  +$candays day"));
			         $canweeks=$this->get_DateWeek($candate);
			        break;
		    }
	    }
	    	    
	    return $canweeks;
   }
   
   //取得生产单位小组Id
   function get_workshop_groupid($id,$semiSign)
   {
	    $groupid=''; 
        if ($id=='' && $semiSign==1)
        {
	        $this->db->select('GROUP_CONCAT(GroupId) AS GroupId');
	        $query = $this->db->get_where('workshopdata', array('semiSign' => '1','Estate' => '1'), 0, 1);
        }
        else{
	        $this->db->select('GroupId');
	        $query = $this->db->get_where('workshopdata', array('Id' => $id), 0, 1);
        }
	    
		if ($query->num_rows()>0)
		{
		    $row = $query->first_row();
		    $groupid=$row->GroupId;
	    }
	    
	    return $groupid;
   }
   
    //取得生产单位小组Id
   function get_action_groupid($ActionId)
   {
	    $groupid=''; 
      
        $this->db->select('GroupId');
        $query = $this->db->get_where('workshopdata', array('Id' => $ActionId), 0, 1);
	    
		if ($query->num_rows()>0)
		{
		    $row = $query->first_row();
		    $groupid=$row->GroupId;
	    }
	    
	    return $groupid;
   }


   
   function get_icon($id){
	   return 'ws_'.$id;
   }	
}