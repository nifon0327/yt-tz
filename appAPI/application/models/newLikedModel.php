<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewForwardModel  
* 新品转发记录类  sql: ac.new_forward 
* 
*/ 
class  NewLikedModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }



/** 
* save_item  
* 插入一条记录
* you go to talk to your friend talk to my friend talk to me wosongnilikai
* @access public 
* @param  params array 一条纪录所需数据
* @return int 返回生成的主键(失败返回-1) auto_generated_keyid
*/  
	public function save_item($params) {
		
		
		/*
			
		    NSArray *titlesEN = @[@"Customer",@"Description",@"Material",@"Price",@"MOQ",@"Quotes rules"];	
		*/	
		//	$this->load->model('oprationlogModel');
		$LogItem = 'APP新品';
		$LogFunction = '新品收藏';
		$newidIn = element('newid', $params, '-1');
		$Log = '新品Id为'.$newidIn.'的新品，';
		$liker = $this->LoginNumber;
		$liked = element('liked',$params,'0');
		
			$data = array(
				'Liked'    => $liked,
			    'NewId'    => $newidIn,
				'created'  => $this->DateTime,
				'creator'  => $liker,
                'Liker'    => $liker,
                'Estate'   => '1'
		);
		
		$checkHas = " select Id from new_liked where Liker=$liker and NewId=$newidIn ";
		$queryHas = $this->db->query($checkHas);
		if ($queryHas->num_rows()>0) {
			$rowHas = $queryHas->row_array();
			$insert_id = $rowHas["Id"];
			$this->db->where('Id',$insert_id);
		$this->db->trans_begin();
		$query=$this->db->update('new_liked', $data);
						         
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $insert_id = 0;
		} else {
		
			    $this->db->trans_commit();
		}

		}
		 else {
			 $this->db->trans_begin();
		$query     = $this->db->insert('new_liked', $data); 
		$insert_id = $this->db->insert_id();
		$OP = 'N';
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();

		} else {
			    $this->db->trans_commit();

			    $OP = 'Y';
		}
		 }
		

		
		if ($insert_id > 0){
			  						  
				  return $insert_id;
		} else {
			return -1;	
		}
		
	}
	 
	 


}