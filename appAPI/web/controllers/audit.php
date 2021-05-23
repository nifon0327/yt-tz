<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Audit extends MC_Controller {
     
    function audit_ckreplenish()
	{
		$params   =$this->input->get();
		$Id = element('Id',$params,'');
		$Action = element('Action',$params,'');
		$Operator = element('Operator',$params,'');
		$Reasons = element('Reasons',$params,'');
		
		$this->load->model('CkreplenishModel');
		
		$status = 0;
		switch($Action){
			case 'PASS':
			         $status=$this->CkreplenishModel->set_estate($Id,1,$Operator);
			   break;
			case 'BACK':
			         $status=$this->CkreplenishModel->set_estate($Id,3,$Operator,$Reasons);
			   break;
		}
		
		$OperationResult = $status==1?'Y':'N';
		$message=$status==1?'审核成功！':'删除失败!';
		$data['jsondata']= array("ActionId"=>"$Action","Result"=>"$OperationResult","Info"=>"$message");
		 
		$this->load->view('output_json',$data);
	}

       
}