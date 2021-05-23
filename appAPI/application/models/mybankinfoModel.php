<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MyBankinfoModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

    function get_record($Id){
          $sql    = "SELECT Id,Title,ShortTitle,Beneficary,Bank,BankAdd,SwiftID,ACNO,CnapsCode,Estate 
		           FROM my2_bankinfo   
		           WHERE  Id=? ";
          $query=$this->db->query($sql,array($Id));
          return  $query->first_row('array');
    }
    
     function get_bankinfo(){
	       $sql    = "SELECT Id,Title,ShortTitle,Bank  FROM my2_bankinfo WHERE Estate=1"; 
	       $query=$this->db->query($sql);
	       return  $query->result_array();
     }
     
    function get_bank_logo($Id)
   {
       $logoPath ="";
      $versionNum = $this->versionToNumber($this->AppVersion);
      $filename = $versionNum>432?'newbank_' . $Id .  '.png':'bank_' . $Id .  '.png';
     // $filename = 'bank_' . $Id .  '.png';
       if(file_exists('../download/banklogo/' . $filename)){
	         $logoPath = $this->config->item('download_path') . '/banklogo/' . $filename;
       }
       return $logoPath;
   }

}