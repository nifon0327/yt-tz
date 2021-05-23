<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Badge extends MC_Controller {

	public function index()
	{
	    
	     $this->load->model('badgeModel');
	     
	     $menutype   =$this->input->post('menutype'); 
	     
	     $rows=$this->badgeModel->get_badge_value($menutype);
	     
		 $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	function get_inware_amount() {
		 $this->load->model('badgeModel');
	     
	     $menutype   =$this->input->post('menutype'); 
	     
	     $rows=$this->badgeModel->get_inware_amount();
	     
		 $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
		
	}
}
