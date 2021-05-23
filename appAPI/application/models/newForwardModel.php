<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewForwardModel  
* 新品转发记录类  sql: ac.new_forward 
* 
*/ 
class  NewForwardModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

/** 
* record_list  
* 获取转发记录列表 
* 
* @access public 
* @param  newid  关联的新品ID
* @return pdo obj 
*/  
	public function record_list($newid) {
		
		$sql = "select F.`Id`,
     F.`Description`,
     F.`Rule`,
     F.`NewId`,
     F.`CompanyName`,
     F.`Price`,
     F.`IsEN`,
     F.`Way`,
     F.`MOQ`,
     F.`Estate`,
     F.`Date`,
     F.`Operator`,
     F.`Locks`,
     F.`PLocks`,
     F.`creator`,
     F.`created`,ifnull(M.Name,concat(T.Forshort,'-',T.Name)) as OperatorName from new_forward F
		left join staffmain M on F.Operator = M.Number
		left join ot_staff T on F.Operator = T.Number
		 where 1 and F.NewId=? order by F.created desc";
		 
        $query = $this->db->query($sql,array($newid));
        return $query;
    }

/** 
* record_list  
* 获取转发记录列表 
* 
* @access public
* @param  newid  关联的新品ID
* @return pdo obj 
*/ 
    public function lastest_record($newid) {
	    /*
		$sql = "select  `Id`,
     `Description`,
     `Rule`,
     `NewId`,
     `CompanyName`,
     `Price`,
     `IsEN`,
     `Way`,
     `MOQ`,
     `Estate`,
     `Date`,
     `Operator`,
     `Locks`,
     `PLocks`,
     `creator`,
     `created` from (select `Id`,
     `Description`,
     `Rule`,
     `NewId`,
     `CompanyName`,
     `Price`,
     `IsEN`,
     `Way`,
     `MOQ`,
     `Estate`,
     `Date`,
     `Operator`,
     `Locks`,
     `PLocks`,
     `creator`,
     `created` from new_forward where IsEN=0 and NewId=? order by id desc limit 1) A
union all 
select `Id`,
     `Description`,
     `Rule`,
     `NewId`,
     `CompanyName`,
     `Price`,
     `IsEN`,
     `Way`,
     `MOQ`,
     `Estate`,
     `Date`,
     `Operator`,
     `Locks`,
     `PLocks`,
     `creator`,
     `created` from (
select `Id`,
     `Description`,
     `Rule`,
     `NewId`,
     `CompanyName`,
     `Price`,
     `IsEN`,
     `Way`,
     `MOQ`,
     `Estate`,
     `Date`,
     `Operator`,
     `Locks`,
     `PLocks`,
     `creator`,
     `created` from new_forward where IsEN=1  and NewId=? order by id desc limit 1) B;";
*/
		$sql= "select `Id`,
     `Description`,
     `Rule`,
     `NewId`,
     `CompanyName`,
     `Price`,
     `IsEN`,
     `Way`,
     `MOQ`,
     `Estate`,
     `Date`,
     `Operator`,
     `Locks`,
     `PLocks`,
     `creator`,
     `created` from new_forward where IsEN=1  and NewId=? order by id desc limit 1";

        $query = $this->db->query($sql,$newid);
        
        if ($query->num_rows()<=0) {
	        $query = $this->db->query('select ifnull(Description,\'\') Description,1 IsEN from new_arrivaldata where Id=?',$newid);
        }
        
        return $query;
    }
	
	
/** 
* save_item  
* 插入一条新品转发记录
* you go to talk to your friend talk to my friend talk to me wosongnilikai
* @access public 
* @param  params array 一条纪录所需数据
* @return int 返回生成的主键(失败返回-1) auto_generated_keyid
*/  
	public function save_item($params) {
		
		
		/*
			
		    NSArray *titlesEN = @[@"Customer",@"Description",@"Material",@"Price",@"MOQ",@"Quotes rules"];	
		*/	
			$this->load->model('oprationlogModel');
		$LogItem = 'APP新品';
		$LogFunction = '新品转发';
		$newidIn = element('newid', $params, '-1');
		$Log = '新品Id为'.$newidIn.'的新品，';
		
			$data = array(
			'IsEN' => element('isen', $params, '1'),
               'CompanyName'  => element('Customer', $params, ''),
			   'Price'   => element('Price',  $params, '0'),
			   'MOQ'   => element('MOQ',  $params, 0),
			   'Description'   => element('Description',  $params, ''),
			   'Rule'=>element('rules', $params, ''),
			   'Way'=>element('ways', $params, '0'),
			   'NewId'=>$newidIn,
			    'Date'     => $this->Date,
				 'created'  => $this->DateTime,
				 'creator'=> $this->LoginNumber,
               'Operator' => $this->LoginNumber,
               'Estate'   => '1'
		);
		$this->db->trans_begin();
		$query     = $this->db->insert('new_forward', $data); 
		$insert_id = $this->db->insert_id();
		$OP = 'N';
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $Log.='转发图档报价失败！';
		} else {
			    $this->db->trans_commit();
			    $Log.='转发图档报价成功！';
			    $OP = 'Y';
		}
		
								   $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log,'OperationResult'=>$OP));
		if ($insert_id > 0){
			  						  
				  return $insert_id;
		} else {
			return -1;	
		}
		
	}
	 
	 


}