<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  WorksclineModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
        
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT Id,Letter,Name,WorkShopId,GroupId,IP,Remark,Estate,Locks,Date,Operator 
	           FROM workscline  WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   
	   return  $query->first_row('array');
	}
	
	//获取生产线信息
	function get_scline_info($line)
	{
		$sql = "SELECT L.Id,M.Name,G.GroupId,G.GroupLeader    
	            FROM workscline L  
	            LEFT JOIN staffgroup G ON G.GroupId=L.GroupId
	            LEFT JOIN staffmain  M ON M.Number=G.GroupLeader 
	            WHERE  L.Letter='$line'"; 
	    $query=$this->db->query($sql);
	    return  $query->first_row('array');        
	}
	
    
    //获取生产单位拉线名称
    function get_scline($WrokShopId='') {
     
	    $sql = "SELECT L.Id,L.Letter,M.Name,G.GroupLeader    
	            FROM workscline L  
	            LEFT JOIN staffgroup G ON G.GroupId=L.GroupId
	            LEFT JOIN staffmain  M ON M.Number=G.GroupLeader 
	            WHERE L.Estate=1 AND L.WorkShopId=$WrokShopId ORDER BY Id"; 
	           
	    $query=$this->db->query($sql);      
		foreach($query->result_array() as $rows){
		    $dataArray[]=array(
			        'Id'   => $rows['Id'],
					'line' => $rows['Letter'],
					'Name' => out_format($rows['Name'],'未设置') 
			);
		}
		
		return $dataArray;
   }
    
   //获取生产单位的分拣口
    function get_scline_sortbox($WrokShopId='') {
     
	    $this->db->select('Id,Name,Letter');
	    $query = $this->db->get_where('workscline_sortbox', array('Estate' => 1,'WorkShopId' => $WrokShopId));
	    $this->db->order_by("Id", "asc"); 
	    
		foreach($query->result_array() as $rows){
		    $dataArray[]=array(
			        'Id'   => $rows['Id'],
					'Name' => $rows['Name']
			);
		}
		
		return $dataArray;
   }
   //获取登陆是否为生产线拉长
   function get_groupleader_in($Number){
       $sql = "SELECT L.Id,COUNT(1) AS Counts FROM workscline L  
	           INNER JOIN staffgroup G ON G.GroupId=L.GroupId WHERE G.GroupLeader=$Number LIMIT 1";
	           
	   $query=$this->db->query($sql);
	   $row = $query->first_row('array');
		
	   return $row['Id']==''?0:$row['Id'];        
   }
   
   function get_icon($id){
	   return 'ws_'.$id;
   }	
}