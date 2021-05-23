<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  AppsheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    public function getAppVersion($BundleId){
       $dataArray=array();
       $sql="SELECT version,link,updateItem FROM app_sheet  WHERE appname = ?";
       $query = $this->db->query($sql,array($BundleId));
       if ($query->num_rows()>0){
	       $row = $query->row_array();
	       $dataArray=array(
	          'BundleId'=>$BundleId,
	          'version'=>$row['version'],
	          'link'=>$row['link'],
	          'updateItem'=>$row['updateItem'],
	       );
       }
       
       return $dataArray;
    }
    
}