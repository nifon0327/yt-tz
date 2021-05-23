<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewForwardModel  
* 新品转发记录类  sql: ac.new_forward 
* 
*/ 
class  StudyReadedModel extends MC_Model {
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
		$LogItem = 'APP培训';
		$LogFunction = '培训阅读';
		$newidIn = element('studyid', $params, '-1');
		$Log = 'Id为'.$newidIn.'的培训项目，';
		$liker = $this->LoginNumber;
		$liked = element('readed',$params,'1');
		
			$data = array(
			'Readed' =>$liked,
			   'StudyId'=>$newidIn,
               'Reader' => $liker
		);
		
		$checkHas = " select Id from studyreaded where Reader=$liker and StudyId=$newidIn ";
		$queryHas = $this->db->query($checkHas);
		if ($queryHas->num_rows()>0) {
			$rowHas = $queryHas->row_array();
			$insert_id = $rowHas["Id"];
			$this->db->where('Id',$insert_id);
		$this->db->trans_begin();
		$query=$this->db->update('studyreaded', $data);
						         
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $insert_id = 0;
		} else {
		
			    $this->db->trans_commit();
		}

		}
		 else {
			 $this->db->trans_begin();
		$query     = $this->db->insert('studyreaded', $data); 
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