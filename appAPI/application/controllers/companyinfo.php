<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Companyinfo extends MC_Controller {
   
   /*
   * 
   * DEFAULT get all doc types
   */
		public function index() {
				$message = "";
				$this->load->model('hzdoctypeModel');
				$query = $this->hzdoctypeModel->get_item();
				$rows = $query->result();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
        }
		
		public function del_doc() {
				
			$message='';
	   		$this->load->model('hzdocModel');
	   		$rows=$this->hzdocModel->delete_item($this->input->post());
	   		$status=$rows>0?1:0;
	   		$data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array());
	    	$this->load->view('output_json',$data);
		}
		
		public function modify_doc() {
			
			$message='';
	   		$this->load->model('hzdocModel');
	   		$rows=$this->hzdocModel->update_item($this->input->post());
	   		$status=$rows>0?1:0;
	   		$data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array());
	    	$this->load->view('output_json',$data);
			
		}
		
		public function save_doc() {
	   $message='';
	   $this->load->model('hzdocModel');
	   $rows=$this->hzdocModel->save_item($this->input->post());
	   $status=$rows>0?1:0;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
		}
		
		
		public function get_list() {
				$message = "";
				$this->load->model('hzdocModel');
				$params = $this->input->post(); 
				$query = $this->hzdocModel->get_item($params);
				$rows = array();
				$totals=0;
				$allRow = $query->result_array();
				$baseUrl = $this->hzdocModel->get_download_path();
				$gettype = element('TypeId', $params, '');
				$basePdf = $this->hzdocModel->get_pdf_path($gettype);
				$baseJpg = $this->hzdocModel->get_jpg_path();
				
				foreach ($allRow as $singleRow) {
					$totals ++;
					$attached = $singleRow["Attached"];
					if ($attached != "") {
						$singleId  = $singleRow["Id"];
						if ($gettype!=33){
								$imgUrl    = $this->hzdocModel->get_jpg($singleId);
								$singleRow["image"] = "$baseUrl$imgUrl";
								$hasJpg = strstr($attached, '.jpg'); 
								if ($hasJpg == ".jpg") {
									$singleRow["pdf"]   = $baseJpg.$attached;
								} else {
									$singleRow["pdf"]   = $basePdf.$attached; 
								}
					   }else{
						        $singleRow["pdf"]   = $basePdf.$attached;
					   }
					} else {
						$singleRow["image"] = "";
						$singleRow["pdf"]   = "";
					}
					$rows[]=$singleRow;
				}
				
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
				
        }
        
        public function detail_info() {
			
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>'','rows'=>null);
		$this->load->view('output_json',$data);
		
    }

}