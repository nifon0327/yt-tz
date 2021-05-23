<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Stuff extends MC_Controller {

		public function type_sum() {
			$message='';
            $this->load->model('StufftypeModel');
	         $params = $this->input->post();
            $query = $this->StufftypeModel->get_item_sum($params);
            $rows  = $query->result();
	        $totals = $query->num_rows();
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
        }
		
		public function combo_list() {
            $this->load->model('stuffdataModel');//StuffPropertyModel
            $params = $this->input->post();
            $combo = $this->stuffdataModel->combo_list($params);
			$basePath = $this->stuffdataModel->get_picture_path();
			$rows = $combo['data'];
	        $totals = $combo['total'];
	        $data['jsondata']=array('status'=>'1','message'=>$basePath,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		public function cg_six_month() {
			$message='';
            $this->load->model('stuffdataModel');
            $result = $this->stuffdataModel->get_cgSixMonth();
	        $data['jsondata']=array('status'=>1,'message'=>$message,'totals'=>'1','rows'=>$result);
		    $this->load->view('output_json',$data);
		}
		
		public function cg_chart_type() {
			$message='';

            $this->load->model('StufftypeModel');
			$params = $this->input->post();
			$typeid = element('typeid',$params,'0');
            $query = $this->StufftypeModel->get_typeCGChartData($typeid);
            $plots = $query->result_array();
			$plotsReal = $plots;
            if ($this->LoginNumber == 11965) {
	            $plotsReal = array();
	            $i = 0;
	            foreach ($plots as $plot) {
		            $plot['X']=$i;
		            $plotsReal[]=$plot;
		            if (($i%2)==0) {
			            $plot['X']=$i+0.5;
			            $plotsReal[]=$plot;
		            }
		            $i ++;
	            }
            }
            
	        $data['jsondata']=array('status'=>1,'message'=>$message,'totals'=>$query->num_rows(),'rows'=>$plotsReal);
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
       
         public function search_items() {
	       	$message='';
            $this->load->model('stuffdataModel');
	         $params = $this->input->post();
            $query = $this->stuffdataModel->search_items($params);
            $rows  = $query->result_array();
	        $totals = $query->num_rows();
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);

       }
       
       
       public function bp_search() {
	       
	       $message='';
            $this->load->model('stuffdataModel');
	         $params = $this->input->post();
            $rows = $this->stuffdataModel->bp_search($params);

	        $totals = count($rows);
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
       }
       
     
       
       //
}