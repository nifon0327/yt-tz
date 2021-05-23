<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  AuditUnionModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
        
    }
    
    //kqqj_audit  kqqjsheet
    
	function month_list() {
		
		//14400,1,0 4h overs
		$sql = "
select count(*) nums,
	   sum(if(M.times > 14400,1,0)) as overs,
	   DATE_FORMAT(M.Date,'%Y-%m') as month 
from
(
	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	kqqj_audit A 
	left join kqqjsheet S on A.Sid=S.Id
	
union all

	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	hzqk_audit A 
	left join hzqksheet S on A.Sid=S.Id
	
) M
group by DATE_FORMAT(M.Date,'%Y-%m') order by month desc";
		
		$query=$this->db->query($sql);
	   return  $query;
		
	}
	
	function month_subs($month) {
		
		//14400,1,0 4h overs
		$sql = "
select count(*) nums,
	   sum(if(M.times > 14400,1,0)) as overs,
	   M.Date
from
(
	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	kqqj_audit A 
	left join kqqjsheet S on A.Sid=S.Id
	where DATE_FORMAT(A.Date,'%Y-%m')='$month'
union all

	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	hzqk_audit A 
	left join hzqksheet S on A.Sid=S.Id
	where DATE_FORMAT(A.Date,'%Y-%m')='$month'
) M
group by M.Date order by Date desc";
		
		$query=$this->db->query($sql);
	   return  $query;
		
	}
	
	function date_subs($date) {
		
		//14400,1,0 4h overs
		$sql = "
select count(*) nums,
	   sum(if(M.times > 14400,1,0)) as overs,
	   M.type,M.title
from
(
	select 1 as type,'请假' as title,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	kqqj_audit A 
	left join kqqjsheet S on A.Sid=S.Id
	where A.Date='$date'
union all

	select 2 as type,'行政请款' as title,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	hzqk_audit A 
	left join hzqksheet S on A.Sid=S.Id
	where A.Date='$date'
) M
group by M.type order by type desc";
		
		$query=$this->db->query($sql);
	   return  $query;
		
	}

    
       	
}