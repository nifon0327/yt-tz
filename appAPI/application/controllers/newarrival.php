<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Newarrival extends MC_Controller {
   
   /*
   * 
   * DEFAULT get all new arrivals 
   */
   public function get_types() {
	   $this->load->model('stufftypeModel');
	   $query = $this->stufftypeModel->get_types_new();
	   $rows  = $query->result_array();
	   $totals = $query->num_rows();
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$rows);
	   $this->load->view('output_json',$data);
   }
   
   
   
   
    public function type_sum() {
	   $this->load->model('newArrivalModel');
	    $rows  = array();
	   $totals = 0;
/*
	  
	   
*/
	  $params = $this->input->post();
	  if (element('LoginNumber',$params,-1) == -11093 || element('LoginNumber',$params,-1) == -11965) {
		   $query = $this->newArrivalModel->type_sum();
	   $rows  = $query->result_array();
	   $totals = $query->num_rows();
	  }
	   
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$rows);
	   $this->load->view('output_json',$data);
	}
   
   
   	public function lastest_record() {
				$message = "";
				//
				$params = $this->input->post();
				$newid = element('newid',$params,'-1');
				$this->load->model('newForwardModel');
				$query = $this->newForwardModel->lastest_record($newid);
				$rows = $query->result();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
        }
        
        public function save_record() {
	   $message='';
	   $this->load->model('newForwardModel');
	   $rows=$this->newForwardModel->save_item($this->input->post());
	   $status=$rows>0?1:0;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
		}
		 
		 public function user_like() {
			 $message='';
	   $this->load->model('newLikedModel');
	   $params = $this->input->post();
	   $rows=$this->newLikedModel->save_item($params);
	   $status=$rows>0?1:0;
	   
	   $liked  = element('liked',$params,'0');
	    $newid  = element('newid',$params,'0');
	   
	   $data['jsondata']=array('status'=>$status,'message'=>"$newid|$liked",'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
			 
		 }
		 public function readed() {
			 $message='';
	   $this->load->model('newReadedModel');
	   $params = $this->input->post();
	   $rows=$this->newReadedModel->save_item($params);
	   $status=$rows>0?1:0;
	   
	   $liked  = element('readed',$params,'0');
	    $newid  = element('newid',$params,'0');
	   
	   $data['jsondata']=array('status'=>$status,'message'=>"$newid|$liked",'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
			 
		 } 
			public function record_list() {
				$message = "";
				//
				
				
				$params = $this->input->post();
				$newid = element('newid',$params,'-1');
				$this->load->model('newForwardModel');
				$query = $this->newForwardModel->record_list($newid);
				$rows = $query->result();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
        }

		
   
		public function index() {
				$message = "";
				//
				$this->load->model('currencyModel');
				$rateQuery = $this->currencyModel->get_item(array('selectid'=>'2'));
				$rateRow = $rateQuery->row_array();
				$message = $rateRow['Rate'];
				
				$this->load->model('newArrivalModel');
				$query = $this->newArrivalModel->get_item($this->input->post());
				$message = $this->newArrivalModel->get_dfile_path();
				$rows = $query->result();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
        }
        
        function frommain() {
	        $message = "";
				//
				$this->load->model('currencyModel');
				$rateQuery = $this->currencyModel->get_item(array('selectid'=>'2'));
				$rateRow = $rateQuery->row_array();
				$message = $rateRow['Rate'];
				
				$this->load->model('newArrivalModel');
				$query = $this->newArrivalModel->get_item($this->input->post());
				$message = $this->newArrivalModel->get_dfile_path();
				$rows = $query->result_array();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);

        }
		
		
	
		 public function get_providers() {
			 		//
				$this->load->model('currencyModel');
				$rateQuery = $this->currencyModel->get_item(array('selectid'=>'2'));
				$rateRow = $rateQuery->row_array();
				$message = $rateRow['Rate'];
				
				$this->load->model('tradeObjectModel');
				$query = $this->tradeObjectModel->get_item($this->input->post());
				$rows = $query->result();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
		 }

			public function edit_item() {
	   $message='';
	   $this->load->model('newArrivalModel');
	   $rows=$this->newArrivalModel->edit_item($this->input->post());
	   $status=$rows;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array("editok"=>"$rows"));
	    $this->load->view('output_json',$data);
		}
		public function save_item() {
	   $message='';
	   $this->load->model('newArrivalModel');
	   $rows=$this->newArrivalModel->save_item($this->input->post());
	   $status=$rows>0?1:0;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
		}
		
}