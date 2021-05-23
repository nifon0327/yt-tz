<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  AppuserLogModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    
    function get_out_netnums($Device='') {
	    
	    $Searchs= $Device==''?'':" AND Device Like '$Device%' ";
	    $sql = "SELECT COUNT(*) AS outNums 
	                 FROM (SELECT Id FROM app_userlog WHERE Date=CURDATE() and  Device!='iphoneAPI'  $Searchs  and NetType=2  GROUP BY creator,Device)A";
	  /*  $sql = "
	   select count(*) as outNums from (SELECT MAX(Id) as Id  FROM app_userlog WHERE Date=CURDATE() and  Device!='iphoneAPI'  $Searchs  and (IP='192.168.20.200' or IP not like '192.168.%')  GROUP BY creator,Device)  LL
;
	    ";
	    */
	     
	    if ($Device == 'web') {
		    $sql = "select count(*) as outNums from (SELECT sId  FROM online where IP='192.168.20.200' or IP not like '192.168.%'  GROUP BY uId ) A  ;";
	    }
	    $query = $this->db->query($sql);
	    if ($query->num_rows() > 0) {
		    return $query->row()->outNums;
	    }
	    return 0;
    }

    
    function get_inout_netnums($Device='') {
	    
	    $Searchs= $Device==''?'':" AND Device Like '$Device%' ";
	    $sql = "
	    select sum(if(L.IP='192.168.20.200' or L.IP not like '192.168.%',1,0)) outNums,count(*) as Nums from app_userlog L 
inner join (SELECT MAX(Id) as Id  FROM app_userlog WHERE Date=CURDATE() and  Device!='iphoneAPI'  $Searchs    GROUP BY creator,Device) LL on LL.Id=L.Id 
;
	    ";
	    
	    if ($Device == 'web') {
		    $sql = "select sum(if(L.IP='192.168.20.200' or L.IP not like '192.168.%',1,0)) outNums,count(*) as Nums from online L 
inner join (SELECT MAX(sId) as sId  FROM online GROUP BY uId) LL on LL.sId=L.sId 
;";
	    }
	    $query = $this->db->query($sql);
	    return  $query->row_array(); 
    }
    
    //获取当天设备数
    function get_device_counts($Device)
    {
        $thisDate=$this->Date;
        
        $Searchs= $Device==''?'':" AND Device Like '$Device%' ";
        
	    $sql   = "SELECT IFNULL(COUNT(1),0) AS Counts 
	              FROM ( 
	                   SELECT 1  FROM app_userlog WHERE Date=CURDATE() and  Device!='iphoneAPI' $Searchs GROUP BY creator,Device
	             )A";
		                 
		$query = $this->db->query($sql);
	    $row   = $query->row_array(); 
	    
	    return $row['Counts'];
    }
    
    
    //获取系统的在线用户数
    function get_web_counts()
    {
	    $sql   = "SELECT IFNULL(COUNT(1),0) AS Counts 
	              FROM ( 
	                    SELECT 1 FROM online GROUP BY uId
	              )A";            
		$query = $this->db->query($sql);
	    $row   = $query->row_array(); 
	    
	    return $row['Counts'];
    }

    //获取最大日操作次数
    function get_max_dayclicks($Date='')
    {
        $thisDate=$this->Date;
		$sql   = " SELECT MAX(A.Counts) AS Counts FROM (
					     SELECT (S.Count0+S.Count1) AS Counts FROM (
					       SELECT Date,IFNULL(COUNT(*),0) AS Count0,0 AS Count1  
					       FROM app_userlog 
					       WHERE Date>='$Date' AND Date<'$thisDate' GROUP BY Date
					    UNION ALL
						   SELECT DATE_FORMAT(ClickDate,'%Y-%m-%d') AS Date,0 AS Count0, IFNULL(COUNT(*),0) AS Count1  
						       FROM sys7_clicktotal 
					           WHERE DATE_FORMAT(ClickDate,'%Y-%m-%d')>='$Date' AND DATE_FORMAT(ClickDate,'%Y-%m-%d')<'$thisDate' 
					           GROUP BY DATE_FORMAT(ClickDate,'%Y-%m-%d')  
					       )S GROUP BY S.Date 
						)A ";          
		$query = $this->db->query($sql);
	    $row   = $query->row_array();
	    
	    return $row['Counts'];
	}
	
    //获取设备访问App系统的记录数
    function get_app_clicktotals($Date='')
    {
        $thisYear=date('Y');
        
        $Searchs= $Date==''?" DATE_FORMAT(Date,'%Y')='$thisYear' ":" Date>='$Date' ";
        
		$sql   = "SELECT IFNULL(COUNT(*),0) AS Counts  FROM app_userlog WHERE $Searchs";
		                 
		$query = $this->db->query($sql);
	    $row   = $query->row_array();
	    
	    return $row['Counts'];
	}
	
	
	//获取设备访问WEB系统的记录数
   function get_web_clicktotals($Date='')
   {
	  $thisYear=date('Y');
	  $Searchs= $Date==''?" DATE_FORMAT(ClickDate,'%Y')='$thisYear' ":" DATE_FORMAT(ClickDate,'%Y-%m-%d')>='$Date' ";
	  
	  $sql   = "SELECT IFNULL(COUNT(*),0) AS Counts  FROM sys7_clicktotal  WHERE $Searchs ";
	  $query = $this->db->query($sql);
	  $row   = $query->row_array();
	  
	  return $row['Counts'];
   }  
}