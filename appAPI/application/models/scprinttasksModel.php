<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScPrinttasksModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    //检查是否需加入打印任务,返回数组
     function get_printtask($POrderId){
	
	   $sql = "SELECT GROUP_CONCAT(G.StuffId) AS StuffId 
						FROM cg1_stuffunite G 
						INNER JOIN stuffdata D ON D.StuffId=G.StuffId
						INNER JOIN stufftype T ON T.TypeId=D.TypeId
						WHERE  G.POrderId='$POrderId' AND T.mainType='5'";//有关联参考类配件
	   $query=$this->db->query($sql);
	   $rows=$query->first_row('array');
	   
	   return  $rows['StuffId']==''?array():explode(',',$rows['StuffId']);
	}
	
	function check_printtask($sPOrderId,$CodeType=3){
		  $sql = "SELECT COUNT(*) AS Counts   FROM sc3_printtasks WHERE  sPOrderId='$sPOrderId' AND CodeType='$CodeType' ";
	      $query=$this->db->query($sql);
	      $rows=$query->first_row('array');
	      
	      return $rows['Counts']>0?1:0;
	}

    
    //保存打印任务信息
   function save_records($sPOrderId,$Qty,$CodeType=3)
   {
       $this->load->model('ScSheetModel');
       $records = $this->ScSheetModel->get_records($sPOrderId);
       $POrderId = $records['POrderId'];
       
	    $inRecode = array(
                       'CodeType'=>"$CodeType",
                         'POrderId'=>"$POrderId",
                        'sPOrderId'=>"$sPOrderId",
                                  'Qty'=>"$Qty",
                              'Estate'=>"1",
	                            'Date'=>$this->Date,
	                      'Operator'=>$this->LoginNumber,
	                        'creator'=>$this->LoginNumber,
	                       'created'=>$this->DateTime
				          ); 
				          
		$this->db->insert('sc3_printtasks',$inRecode); 
	    return $this->db->affected_rows();
   }
}