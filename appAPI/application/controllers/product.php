<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Product extends MC_Controller {
		
			public function product_his() {
			$this->load->model('orderSheetModel');//StuffPropertyModel
            $params = $this->input->post();
			$productid = element('productid',$params,-1);
			
			$rows = $this->orderSheetModel->product_analyse_sheet($productid);
			
			$data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($rows),'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}



			public function url_box() {
			$this->load->model('productdataModel');//StuffPropertyModel
            $params = $this->input->post();
			$porderid = element('order',$params,-1);
			$rows1=$this->productdataModel->url_box($porderid);
			$rows = array();
			$rows[]=$rows1;
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($rows),'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		

		public function bom_detail() {
			$this->load->model('productdataModel');//StuffPropertyModel
            $params = $this->input->post();
			$productid = element('productid',$params,-1);
			$rows = array();
            $bomhead = $this->productdataModel->bom_head($productid);
            $bomlist = $this->productdataModel->order_bomdetail_1($productid);
            $bomcost = $this->productdataModel->product_cost($productid);
			
            $rows[]=$bomhead;
            $rows[]=$bomlist;
            $rows[]=$bomcost; 
            $this->load->model('stuffdataModel');
			$basePath = $this->stuffdataModel->get_picture_path();
	        $data['jsondata']=array('status'=>'1','message'=>$basePath,'totals'=>2,'rows'=>$rows);
		    $this->load->view('output_json',$data);
			
		}
		
		
		public function type_sum() {
			
		    $this->company_sum();
        }
		
		public function combo_list() {
			 $this->load->model('productdataModel');//StuffPropertyModel
            $params = $this->input->post();
			 
            $combo = $this->productdataModel->combo_list($params);
			$basePath = $this->productdataModel->get_picture_path();
			$rows = $combo;
	        $totals = count($combo);
	        $data['jsondata']=array('status'=>'1','message'=>$basePath,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
			
		}
		
		public function can_forbidden_stuff() {
			 $this->load->model('productdataModel');//StuffPropertyModel
            $params = $this->input->post();
			$productid = element('productid',$params,-1);
            $combo = $this->productdataModel->canforbidden_stuff($productid);
			$rows = $combo;
	        $totals = count($combo);
	        $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
			
		}
		
		public function company_sum() {
			$message='';
            $this->load->model('tradeObjectModel');
	         $params = $this->input->post();
            $query = $this->tradeObjectModel->get_item_psum($params);
            $rows  = $query->result();
	        $totals = $query->num_rows();
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
        }
		
			
		public function add_order() {
			
			$message='';
            $this->load->model('productdataModel');
	        $params = $this->input->post();
            $status = $result  = $this->productdataModel->save_order($params);
           
	        $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>1,'rows'=>1);
		    $this->load->view('output_json',$data);
			
		}
		
		public function search_company() {
			
			
			$message='';
            $this->load->model('ProductdataModel');
	        $params = $this->input->post();
            $query  = $this->ProductdataModel->get_incompany($params);
            $rows   = $query->result();
	        $totals = $query->num_rows();
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		

		public function index() {
			$message='';
            $this->load->model('ProductdataModel');
	        $params = $this->input->post ( null );
            $query  = $this->ProductdataModel->get_items($params);
            //$rows   = $query->result();
	        //$totals = $query->num_rows();
	        $totals = 100;
	        $rows   = array();
	        $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		    $this->load->view('output_json',$data);
        }

		public function add() {
			$message='';
            $this->load->model('ProductdataModel');
	        $params = $this->input->post ( null );
            $result = $this->ProductdataModel->add_items($params);
	        $data['jsondata']=array('status'=>$result,'message'=>$message,'totals'=>'1','rows'=>'1');
		    $this->load->view('output_json',$data);
       }
}