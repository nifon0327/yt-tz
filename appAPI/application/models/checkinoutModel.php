<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CheckinoutModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

     //获取打卡楼层人数
     function get_floor_checknumbers($dFromId,$date='')
     {
        $date=$date==''?$this->Date:$date;
        
        $sql    = "SELECT COUNT(1) AS Counts  FROM checkinout WHERE Date=? AND dFromId IN($dFromId)";
        $query  = $this->db->query($sql,$date);
        $row    = $query->row_array();
	    return $row['Counts'];
     }
     
     //获取在岗人数
     function get_group_worknumbers($group,$date='')
     {
         $date=$date==''?$this->Date:$date;
	     $sql = "SELECT COUNT(*) AS Nums FROM checkinout  C 
                  LEFT JOIN  staffmain M  ON M.Number=C.Number  
                  WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$date' AND C.CheckType='I'  AND  M.GroupId in ($group) 
                        AND NOT EXISTS(SELECT W.Number FROM staff_workstatus W WHERE W.Number=C.Number AND W.CheckType=0)";
 		$Nums = 0;
 		$query=$this->db->query($sql);
 		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $Nums=$row->Nums;
	     }
	     return $Nums;
     }
     
     //获取实际工作时长(按月份)
     function get_month_worktimes($groups,$month='')
     {
         $month=$month==''?date('Y-m'):$month;
	     $sql = "SELECT SUM(S.SdTime+S.JbTime+S.JbTime2+S.JbTime3) AS  times 
				FROM kqdaytj S 
				WHERE DATE_FORMAT(S.Date,'%Y-%m')='$month' AND S.GroupId IN ($groups)";
 		$times = 0;
 		$query=$this->db->query($sql);
 		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $times=$row->times;
	     }
	     return $times;
     }
     
      //获取实际工作时长(按日期)
     function get_day_worktimes($groups,$date='')
     {
         $date=$date==''?date('Y-m-d'):$date;
	     $sql = "SELECT SUM(S.SdTime+S.JbTime+S.JbTime2+S.JbTime3) AS  times 
				FROM kqdaytj S 
				WHERE DATE_FORMAT(S.Date,'%Y-%m-%d')='$date' AND S.GroupId IN ($groups)";
 		$times = 0;
 		$query=$this->db->query($sql);
 		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $times=$row->times;
	     }
	     return $times;
     }
    
     //判断当天是否已打上班卡
     function is_checkIn($number, $checkDate){

        $sql = "SELECT * FROM checkinout Where Number='$number' AND KrSign=0 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='$checkDate' ORDER BY CheckTime";
        $query = $this->db->query($sql);
        return $query->num_rows();
     }

     //插入考勤资料
     function save_check($parameters){
        $parameters['ZlSign'] = '0';
        $parameters['KrSign'] = '0';
        $parameters['otReason'] = '';
        $parameters['created'] = $this->DateTime;
        $parameters['Date'] = $this->Date;

        $this->db->insert('checkinout', $parameters);
        $newId = $this->db->insert_id(); 
           
        return $newId>0?'Y':'N';
     }

   }