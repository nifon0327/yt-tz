<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StudytypeModel extends MC_Model {
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
		$reader = $this->LoginNumber;
		
        $sql    = "SELECT T.TypeId as Id,T.Name,T.Estate ,IFNULL(A.ReadedCount,0 ) ReadedCount
from studytype T 
left join (select sum(1-ifnull(R.Readed,0)) ReadedCount,D.TypeId from studysheet D 
left join studyreaded R on R.StudyId=D.Id and R.Reader=$reader group by D.TypeId) A on A.TypeId=T.TypeId
where T.Estate>=1";
        $query=$this->db->query($sql);
        return $query;

    }
}