<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Study extends MC_Controller {

	
	
	public function index() {
				$message = "";
				$this->load->model('studytypeModel');
				$query = $this->studytypeModel->get_item();
				$rows = $query->result();
				$totals=$query->num_rows();
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
        }
        
         public function readed() {
			 $message='';
	   $this->load->model('studyReadedModel');
	   $params = $this->input->post();
	   $rows=$this->studyReadedModel->save_item($params);
	   $status=$rows>0?1:0;
	   
	   $liked  = element('readed',$params,'0');
	    $newid  = element('studyid',$params,'0');
	   
	   $data['jsondata']=array('status'=>$status,'message'=>"$newid|$liked",'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
			 
		 } 
    
		public function get_list() {
				$message = "";
				$this->load->model('studysheetModel');
				$params = $this->input->post(); 
				$query = $this->studysheetModel->get_item($params);
				$rows = array();
				$totals=0;
				$allRow = $query->result_array();
			//	$baseUrl = $this->studysheetModel->get_download_path();
			// intro canedit
				$baseJpg = $this->studysheetModel->get_jpg_path();
				
				foreach ($allRow as $singleRow) {
					$totals ++;
					$attached = $singleRow["Icon"];
					if ($attached != "") {
						$singleRow["image"] = "$baseJpg$attached";
						
					} else {
						$singleRow["image"] = "";
					
					}
					$attached = $singleRow["File"];
					if ($attached != "") {
						$singleRow["intro"] = "$baseJpg$attached";
						
					} else {
						$singleRow["intro"] = "";
					
					}
					
/*
					if ($this->LoginNumber == '11965' ) {
						
						$title = $singleRow["title"];
						$qr = $this->db->query("select GoodsId from nonbom4_goodsdata
						where GoodsName ='$title' limit 1");
						if ($qr->num_rows()>0) {
							$ro = $qr->row();
							$goodsId = $ro->GoodsId;
							$singleRow["GoodsId"]=$goodsId;
							$did = $singleRow["Id"];
							$this->db->query("update studysheet
						set GoodsId ='$goodsId' where Id=$did");
						$creator = $singleRow["creator"];
							$this->db->query("update nonbom4_goodsdata
						set Introduction ='$attached' ,Attached=1,Operator='$creator' where GoodsId=$goodsId");
						
						}
				
						
					}
					*/
		
					
					$singleRow["canedit"] = "1";
					$rows[]=$singleRow;
				}
				
				$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
				$this->load->view('output_json',$data);
				
        }

	
}