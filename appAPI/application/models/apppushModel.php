<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  AppPushModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //保存设备$token
    function save_push_mainapp($bundleId,$userId,$token)
    {
          $token=preg_replace('/[<>]/','',$token);
	      $sql   = "REPLACE  INTO push_mainapp(token,bundleId,userId,Date,creator,created) Values ('$token','$bundleId','$userId',CURDATE(),'$userId',NOW())";          
		  $query = $this->db->query($sql);
    }
    
     //保存设备$token
    function save_push_clientapp($bundleId,$userId,$token)
    {
         $token=preg_replace('/[<>]/','',$token);
	     $sql   = "REPLACE  INTO push_clientapp(token,bundleId,userId,Date,creator,created) Values ('$token','$bundleId','$userId',CURDATE(),'$userId',NOW())";          
		 $query = $this->db->query($sql);
    }
}