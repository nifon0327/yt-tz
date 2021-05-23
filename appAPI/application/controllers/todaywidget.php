<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TodayWidget extends MC_Controller {

	public function index()
	{
	    
	     $this->load->model('todayWidgetModel');
	     
	     $menutype   =$this->input->post('menutype'); 
	     
	     $rows=$this->badgeModel->get_badge_value($menutype);
	     
		 $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
}
