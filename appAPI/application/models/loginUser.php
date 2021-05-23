<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginUser extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //检查用户
    function check_user($dbname,$username, $password)
    {
	    $sql = "SELECT * FROM $dbname.UserTable Where uName =? And uPwd =? AND uType IN (1,2,4)"; //  And uType  IN (?)
        $query=$this->db->query($sql, array($username, $password));//,'1,4'
        return $query;
    }
    
    //检查用户类别
    function  get_user_type($userid)
    {
	    $sql = "SELECT uType FROM UserTable WHERE Id =?"; 
        $query=$this->db->query($sql, array($userid));
        $row = $query->row_array();
	    return $row['uType'];
    }
    
    //取得公司内部人员信息
    function get_user_info($dbname,$user_number)
    {
         $DataPublic=$this->DataPublic;
         $sql = "Select M.cSign,M.Name,GroupName,M.Estate  From  $DataPublic.staffmain M
	                                              LEFT JOIN $dbname.staffgroup G ON G.GroupId=M.GroupId
	                                               Where M.Number = ?"; 
        $query=$this->db->query($sql, $user_number);
        return $query;
    }
    
    //客人
     function get_clientuser_info($dbname,$user_number)
    {
         $DataPublic=$this->DataPublic;
         $sql = "Select M.cSign,M.Name,GroupName,M.Estate  From  $DataPublic.staffmain M
	                                              LEFT JOIN $dbname.staffgroup G ON G.GroupId=M.GroupId
	                                               Where M.Number = ?"; 
        $query=$this->db->query($sql, $user_number);
        return $query;
    }
    
    //取得外部人员信息
    function get_outuser_info($dbname,$user_number)
    {
	     $sql = "Select '7' AS cSign,M.Name,M.Forshort AS GroupName,M.Estate  From $dbname.ot_staff M
	                                               Where M.Number =?"; 
        $query=$this->db->query($sql, $user_number);
        return $query;
	}
	
	//检查权限
	 public function check_authority_modules($Modules){

	    $sql="SELECT A.ModuleId FROM  upopedom A 
		                    LEFT JOIN funmodule B ON B.ModuleId=A.ModuleId 
		                    WHERE A.Action>0 AND B.Id>0 AND A.UserId=? AND A.ModuleId IN ($Modules) ";              
	    $query=$this->db->query($sql,array($this->UserId,));
	    return $query->num_rows()>0?true:false;
    }

     public function check_authority_Items($Items){
	    $sql="SELECT A.ItemId  FROM tasklistdata A
	           LEFT JOIN taskuserdata B ON B.ItemId=A.ItemId
	           WHERE  A.Estate=1  AND B.UserId=? AND  A.ItemId IN($Items)";
		                    
	    $query=$this->db->query($sql,array($this->LoginNumber));
	    return $query->num_rows()>0?true:false;
    }
    
    //检查设备UUID
    public function check_user_uuid($username,$BundleId,$Device,$UUID)
    {
	    $sql = "SELECT Id,Estate FROM app_uuid WHERE UUID =? and BundleId= ?"; 
        $query=$this->db->query($sql, array($UUID,$BundleId));
        
        if ($query->num_rows()>0){
	           $row = $query->row_array();
	           $this->update_user_uuid($row['Id']);
	           return $row['Estate'];
        }else{
               $this->save_user_uuid($username,$BundleId,$Device,$UUID);
	           return -1;
        }
    }
    
    function update_user_uuid($Id)
    {
	      $data=array(
	               'LastTime'=>$this->DateTime
	              );
	              
	   $this->db->update('app_uuid',$data, array('Id' => $Id));
    }
    
    function save_user_uuid($username,$BundleId,$Device,$UUID)
    {
	        $inRecode = array(
	                     'UserName'=>"$username",
	                       'BundleId'=>"$BundleId",
	                           'Device'=>"$Device",
	                            'UUID' =>"$UUID",
	                       'LastTime'=>$this->DateTime,
	                              'Date'=>$this->Date,
	                             'Estate'=>"0",
	                          'created'=>$this->DateTime
				          );     
				          
				  $this->db->insert('app_uuid',$inRecode); 
    }
}
?>
