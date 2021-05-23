<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Staff extends MC_Controller {

	
	
	
	public function staff_list() {
			
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>'','rows'=>null);
		$this->load->view('output_json',$data);
		
    }

	
}