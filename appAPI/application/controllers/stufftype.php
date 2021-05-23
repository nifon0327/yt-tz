<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Stufftype extends MC_Controller {

		public function index() {
			$message='';
            $this->load->model('StufftypeModel');
	        $params = $this->input->post ( null );
            $query  = $this->StufftypeModel->get_item($params);
            $rows   = $query->result();
	        $totals = $query->num_rows();
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
        }

		public function add() {
			$message='';
            $this->load->model('StufftypeModel');
	        $params = $this->input->post ( null );
            $result = $this->StufftypeModel->add_item($params);
	        $data['jsondata']=array('status'=>$result,'message'=>$message,'totals'=>'1','rows'=>'1');
		    $this->load->view('output_json',$data);
       }
}