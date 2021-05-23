<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  HzdoctypeModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


    public function get_item($params=array()){
        $SearchRows='';
		 $Estate = element('Estate',$params,-1);
		 $PstmtArray = array();
		 if ($Estate != -1) {
			 $SearchRows .= ' and T.Estate=? ';
			 $PstmtArray[]=$Estate;
		 }
		
		
        $sql    = 'SELECT T.Id,T.Name,T.SubName,T.Remark,T.SortId,T.Estate,T.Locks,T.Date,M.Name AS Operator 
                  FROM zw2_hzdoctype T 
                  LEFT  JOIN staffmain   M  ON M.Number=T.Operator  WHERE 1 and T.Id in (1,20,2,23,35,42,33) ' . $SearchRows . ' ORDER BY SortId';
        $query=$this->db->query($sql,$PstmtArray);
        return $query;

    }
}