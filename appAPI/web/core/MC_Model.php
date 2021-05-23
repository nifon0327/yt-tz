<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MC_Model  extends CI_Model {
    public $DataIn     = null;
    public $DataPublic = null;
    public $DataSub    = null;
    public $LoginNumber= null;
    public $UserId     = null;
    public $Date       = null;
    public $DateTime   = null;
    public $ThisWeek   = null;
    
    function __construct()
    {
        parent::__construct();
         $this->load->helper('array');
          
        $this->DataIn      = $this->config->item('DataIn');
        $this->DataSub     = $this->config->item('DataSub');
        $this->DataPublic  = $this->config->item('DataPublic');
        $this->LoginNumber = $this->input->post('LoginNumber'); 
        $this->UserId      = $this->input->post('UserId'); 
        $this->Date        = date('Y-m-d'); 
        $this->DateTime    = date('Y-m-d H:i:s'); 
        $this->ThisWeek    = $this->get_CurrentWeek();
    }
    
      //获取当周
     public function get_CurrentWeek(){
	     $query= $this->db->query("SELECT YEARWEEK(CURDATE(),1) AS week");
	     $rows=$query->row(0);
         return $rows->week;
     }
     
     //获取指定日期的周数
     public function get_DateWeek($date){
	     $query= $this->db->query("SELECT YEARWEEK($date,1) AS week");
	     $rows=$query->row(0);
         return $rows->week;
     }
}
?>
