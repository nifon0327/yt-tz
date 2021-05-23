<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  GysshRemarkModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    //读取备注信息
    function get_records($Sid)
    {
	    $sql='SELECT R.Id,R.Sid,R.Remark,R.Date,R.created,M.Name AS Operator 
	          FROM gys_shremark R 
	          LEFT JOIN staffmain M ON M.Number=R.Operator
	          WHERE R.Sid=? ORDER BY Id DESC LIMIT 1';
	    $query=$this->db->query($sql,array($Sid));
		return $query->first_row('array');
    }
    
    
    //保存备注信息
   function save_shremark($Id,$remark)
   {
	    $inRecode = array(
	                           'Sid'=>"$Id",
	                        'Remark'=>"$remark",
	                          'Date'=>$this->Date,
	                      'Operator'=>$this->LoginNumber,
	                       'creator'=>$this->LoginNumber,
	                       'created'=>$this->DateTime
				          ); 
				          
		$this->db->insert('gys_shremark',$inRecode); 
	    return $this->db->affected_rows();
   }
}