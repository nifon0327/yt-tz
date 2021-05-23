<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Show extends MC_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
	    
	     $this->load->model('menusShowModel');
	     $versionNum = $this->versionToNumber($this->AppVersion);
	     
	     $rows=$this->menusShowModel->get_show_menus();
	     	     
	     
		 $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	
	function item() {
		
		$params = $this->input->post();
		
		$upTag =  element("moduleid",$params,"0");

		$status = 0;
		
		$rows = array('Name'=>'', 'url'=>'');
		$this->load->model('menusShowModel');
		$rowHas=$this->menusShowModel->get_item($upTag);
		
		if (count($rowHas) > 0) {
			$basic = $this->menusShowModel->get_video_path();
			$status = '1';
			$rows = array('Name'=>$rowHas['FilePath'],'url'=>$basic.$rowHas['FilePath']);
		}
		
		$data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$rows);
		 $this->load->view('output_json',$data);
		

		
	}
	
	function save_show() {
		
		$params = $this->input->post();
		
		$upTag =  element("moduleid",$params,"0");
		$message = '上传失败';
		$status = 0;
		
		$this->load->model('menusShowModel');
		$newId=$this->menusShowModel->save_item($params);
		
		if ($newId > 0) {
			$message = '上传成功';
			$status = '1';
		}
		
		$data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>null);
		 $this->load->view('output_json',$data);
		
		
	}
		
}
