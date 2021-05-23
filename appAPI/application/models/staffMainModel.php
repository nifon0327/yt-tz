<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StaffMainModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //返回指定Id的记录
	function get_records($Number=0){
	
	   $sql = "SELECT Id, cSign, WorkAdd, Currency, Number, IdNum, Name, Nickname, Grade, KqSign, BranchId, JobId, GroupId, Mail, GroupEmail, AppleID, ExtNo, ComeIn, ContractSDate, ContractEDate, Introducer, OffStaffSign, FormalSign, FormalManager, FormalContent, AttendanceFloor, Estate, Locks, Date, Operator, PLocks, creator, created, modifier, modified 
	           FROM staffmain  WHERE Number=?"; 	
	   $query=$this->db->query($sql,array($Number));
	   
	   return  $query->first_row('array');
	}

    //考勤人数
    function get_checkInNums_ingroup($group) {
	    $nowDate = $this->Date;
	    $sql = "SELECT COUNT(*) AS Nums FROM checkinout  C 
  LEFT JOIN  staffmain M  ON M.Number=C.Number  
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$nowDate' AND C.CheckType='I'  AND  M.GroupId in ($group)";
 		$Nums = '';
 		$query=$this->db->query($sql);
 		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $Nums=$row->Nums;
	     }
	     return $Nums;
 		
    }
    
    //考勤人数
    function date_checkInNums_ingroup($group,$nowDate='') {
	    $sql = "SELECT COUNT(*) AS Nums FROM checkinout  C 
  LEFT JOIN  staffmain M  ON M.Number=C.Number  
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$nowDate' AND C.CheckType='I'  AND  M.GroupId in ($group)";
 		$Nums = '';
 		$query=$this->db->query($sql);
 		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $Nums=$row->Nums;
	     }
	     return $Nums;
    }
    
    //考勤人数
    function mon_checkInNums_ingroup($group,$mon='') {
	    $sql = "SELECT COUNT(*) AS Nums FROM checkinout  C 
  LEFT JOIN  staffmain M  ON M.Number=C.Number  
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m')='$mon' AND C.CheckType='I'  AND  M.GroupId in ($group)";
 		$Nums = '';
 		$query=$this->db->query($sql);
 		if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $Nums=$row->Nums;
	     }
	     return $Nums;
 		
    }
    
    //获取采购员
    function get_all_buyers() {
		
		$sql = "SELECT M.Number,M.Name FROM staffmain M WHERE  M.BranchId=4 AND M.Estate='1'";
		$query=$this->db->query($sql);
		if ($query->num_rows()>0){
		    return $query->result_array();
	    }
	    return null;
	}
    
    //获取员工人数
    function get_staffTotals($filedname='',$value=''){
        $this->db->where('Estate',1);
        if ($filedname!='' && $value!=''){
           if (in_array(strtolower($filedname), array('branchid','groupid','jobid')))
           {
              $values=explode(',', $value);
              if (count($values)>1){
	             $this->db->where_in($filedname, $values); 
              }
              else{
	             $this->db->where($filedname,$value);   
              }
	          
           }
        }
        
        $this->db->from('staffmain');
        return $this->db->count_all_results();
    }
    
   //获取员工姓名    
   function get_staffname($user_number){
         $name='';
         $query=$this->get_record($user_number,'name');
         if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $name=$row->name;
	     }
	     return $name;
    }
    
    //获取员工小组GroupId    
   function get_groupid($user_number){
         $groupid='';
         $query=$this->get_record($user_number,'GroupId');
         if ($query->num_rows()>0){
		    $row = $query->first_row();
		    $groupid=$row->GroupId;
	     }
	     return $groupid;
    }
    
    //获取员工相片路径
    function get_photo($user_number){
          $photo_path='download/staffPhoto/P' . $user_number . '.png';
          if (file_exists($this->config->item('document_root') . $photo_path)){
	           return   $photo_path;
          } 
          return "";
    }
    
    function get_record($user_number,$fields){
          $fields=$fields==""?"*":$fields;
          $sql = "SELECT  $fields  FROM  staffmain  WHERE Number = ?"; 
          $query=$this->db->query($sql, array($user_number));

          return $query;
    }
    
    function get_branch_record($branch_id,$csign){
          $sql = "SELECT  *  FROM  staffmain  WHERE Estate=1 and BranchId = ? and cSign=?"; 
          $query=$this->db->query($sql, array($branch_id,$csign));
          
          $rows=array();
           foreach ($query->result_array() as $row){
                 $staff_photo=$this-> get_photo($row['Number']);
                  $rows[]=array(
				         'Number'=>$row['Number'],
					     'Name'=>$row['Name'],
					     'staff_photo'=>$staff_photo
		    );	
        }
        return $rows;
    }
    
    function get_branch_supervisor($branch_id,$csign){
	      $sql = "SELECT  *  FROM  staffmain  WHERE Estate=1 and BranchId = ? and cSign=? and JobId=39"; 
          $query=$this->db->query($sql, array($branch_id,$csign));
          return $query;
    }
    
    function get_number_fromidcard($idcard)
    {
       $idcard=strlen($idcard)>8?substr($idcard,-8):$idcard;
       if ($idcard == '') {
	       return '';
       }
	   $sql = "SELECT  Number FROM  staffmain  WHERE IdNum=? "; 
	   $query=$this->db->query($sql,array($idcard));
	   if ($query->num_rows() == 1) {
		   $row = $query->row_array();
		   return $row['Number'];
	   } 
	   
	   return '';
	    
    }
    
    function get_groupleader($groupid) {
	    $name = '';
	    $sql = "
	    	select M.Name 
	    	from staffgroup G 
	    	left join staffmain M on G.GroupLeader=M.Number
	    	where G.GroupId=?
	    	
	    ";
	    $query=$this->db->query($sql,$groupid);
	    if ($query->num_rows()>0) {
		    $row = $query->row();
		    $name = $row->Name;
	    }
	    return $name;
    }
    
    function get_branch_nums($BranchId) {
	    $Nums = 0;
	    $sql ="SELECT Count(*) AS Nums FROM staffmain M  WHERE 1 AND  M.BranchId='$BranchId' AND M.Estate=1";
	    $query=$this->db->query($sql);
	    if ($query->num_rows()>0) {
		    $row = $query->row();
		    $Nums = $row->Nums;
	    }
	    return $Nums;
	    
    }
    
    function compute_gl($outDate,$ComeIn) {
		
		$this->load->library('datehandler');
		$MonthNums=$this->datehandler->getDifferMonthNum($outDate,$ComeIn);
		$ComeYears=floor($MonthNums/12);
		$ComeMonths=$MonthNums-$ComeYears*12;
        $glPhone=$ComeYears . "|" . $ComeMonths;
        
        return $glPhone;
	}

  //根据Idnum或工号获取员工资料
  function get_StaffByNumber($idcard){

    $idcard=strlen($idcard)>8?substr($idcard,-8):$idcard;
       if ($idcard == '') {
         return '';
       }

    $sql = "SELECT Name, Number,BranchId,JobId From staffmain WHERE IdNum=$idcard or Number = $idcard Limit 1";
    $query = $this->db->query($sql);

    if ($query->num_rows() > 0){
      return $query->row_array(0);
    }else{
      return '';
    }
  }
    
}
?>
