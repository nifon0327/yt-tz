<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Scanner extends MC_Controller {

		public function index() {
			
        }

		public function stockid_info() {
			$message='';

	        $params = $this->input->post();
	        $infostr = element('scaninfo', $params, '');
	        
	        $stuffid = "";
	        $infos = explode("|", $infostr);
// 20160603000900
	        if (count($infos)>=3) {
		        //抽检条码 供应商ID｜stuffid｜qty 
		        $stuffid = $infos[1];
		        $infos0 = count($infos)==4? $infos[1]:$infos[0];
		        $sub0 = substr($infos0, 0,1);
		        if ($sub0 == 'N') {
			        $stuffid  = str_replace('N', '', $infos0);
		        } else if ($sub0 == 'C') {
			        $stuffid  = str_replace('C', '', $infos0);
		        }
	        } else if (count($infos)==2) {
		        $stuffid = $infos[0];
		        $stuffid = str_replace('N', '', $stuffid);
		        $stuffid = str_replace('C', '', $stuffid);

		        $infostr = str_replace('N', '', $infostr);
		         $infostr = str_replace('|', '', $infostr);
		         $infostr = str_replace('*', '', $infostr);
		         $infostr = str_replace('C', '', $infostr);
		         $infostr = str_replace('M', '', $infostr);

		        if (strlen($infostr)>=14) {
			        //14流水号Id stockid 
			        $this->load->model('gysshsheetModel');
		        	$query = $this->gysshsheetModel->get_item_usestockid($infostr);
					if ($query->num_rows() > 0) {
						$row = $query->row_array();
						$stuffid = $row["StuffId"];
	 				} else {
		 				
		 				$this->load->model('ScSheetModel');
		 				$row = $this->ScSheetModel->get_stuffId_mstock($infostr,1);
		 				if ($row != null) {
			 				$stuffid = $row["StuffId"];
		 				}
		 				
		 				
	 				}
	        	}
		        
	        } else {
		        $infostr = str_replace('N', '', $infostr);
		        $infostr = str_replace('|', '', $infostr);
		        $infostr = str_replace('*', '', $infostr);
		        $infostr = str_replace('C', '', $infostr);
		        $infostr = str_replace('M', '', $infostr);
		        
		        if (strlen($infostr)>=14) {
			        //14流水号Id stockid 
			        $this->load->model('gysshsheetModel');
		        	$query = $this->gysshsheetModel->get_item_usestockid($infostr);
					if ($query->num_rows() > 0) {
						$row = $query->row_array();
						$stuffid = $row["StuffId"];
	 				}	
	 				 else {
		 				
		 				$this->load->model('ScSheetModel');
		 				$row = $this->ScSheetModel->get_stuffId_mstock($infostr,1);
		 				if ($row != null) {
			 				$stuffid = $row["StuffId"];
		 				}
		 				
		 				
	 				}
	        	} else {
		        	
		        	$stuffid = $infostr;
	        	}
	        }
	       
	        $this->load->model('stuffdataModel');
	        $query = $this->stuffdataModel->getdata_usestuffid($stuffid);
	        $rows = $query->result();
	        $data['jsondata']=
	        array(
	        	  'status' => '1',
	        	  'message'=> $message,
	        	  'totals' => '1',
	        	  'rows'   => $rows
	        	  );
		    $this->load->view('output_json',$data);
       }
}