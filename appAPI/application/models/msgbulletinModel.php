<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MsgBulletinModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


    public function get_record($Id){

		
        $sql    = "SELECT Id,Title,Content,Type,Date,Operator,Estate 
		           FROM msg1_bulletin  
		           WHERE  Id=?";
        $query=$this->db->query($sql,array($Id));
        
        return $query;
    }
    
    public function get_titles($Date)
    {
        $dataArray  = array();
        if ($Date==''){
	        $sql   = "SELECT Title,Date FROM msg1_bulletin WHERE  1 Order by Id DESC LIMIT 3";
        }else{
	        $sql   = "SELECT Title,Date FROM msg1_bulletin WHERE  Date=?";
        }
	    
        $query = $this->db->query($sql,array($this->Date));
        $dates = date('m/d');
        foreach ($query->result_array() as $rows){
           $dates = date('m/d',strtotime($rows['Date'])); 
           $dataArray[]=array(
	                  'title'=> array('Text'=>$rows['Title'] . ' ' . $dates, 'FontSize'=>'12', 'FontWeight'=>'regular'),
	                  'type' =>'1'
	                  );
        }
        return $dataArray;
    }
}