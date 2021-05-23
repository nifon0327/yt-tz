<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Swapdata extends MC_Controller {
     
    public  $CompanyIds = '100446,100435,100470,100468,100508'; //BSD SK￥,Trio SK￥,鑫宏力SK￥(100470),奇利新SK￥
     
    public function get_ch_shipdata(){
     
	       $params = $this->input->get();
	       $Mid = element('Mid',$params,'0');
	       $KEY = element('KEY',$params,'');
	       if ($KEY=='READMC'){
		       $this->load->model('ChShipsheetModel');    
		       $dataArray=$this->ChShipsheetModel->get_company_sheet($Mid,$this->CompanyIds); 
		       $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$dataArray);
		       $this->load->view('output_json',$data);
	       }
      }
       
       
      public function get_ch_productdata(){
	      
	      $params = $this->input->get(); 
	      $KEY = element('KEY',$params,''); 
	      if ($KEY=='READMC'){
		       $this->load->model('ProductdataModel'); 
		       $dataArray=$this->ProductdataModel->get_company_sheet($this->CompanyIds);
		       
		       $this->load->model('pandsModel'); 
		       $this->load->model('StuffdataModel'); 
		       $newDataArray =  array();
		       foreach($dataArray as $productRow){
			       
	
				   $productId = $productRow["ProductId"];
			
			       $pandRow  = $this->pandsModel->get_relation($productId);
			       $relation = $pandRow["Relation"];
			       $boxSpec = $pandRow["Spec"];
			       $boxWeight = $pandRow["Weight"];
			       
			       $boxPcs    = $this->StuffdataModel->get_boxpcs($productId);
			       $boxPcs = $boxPcs==""?0:$boxPcs;
			       
			       $inboxCode    = $this->StuffdataModel->get_boxCode($productId,'内箱');
			       $outboxCode    = $this->StuffdataModel->get_boxCode($productId,'外箱');
			       $Code = $outboxCode."|".$inboxCode;
			       $newDataArray[] = array("Id"=>$productRow["Id"],
			                               "ProductId"=>$productId,
			                               "cName"=>$productRow["cName"],
			                               "eCode"=>$productRow["eCode"],
			                               "TypeId"=>$productRow["TypeId"],
			                               "Price"=>$productRow["Price"],
			                               "Unit"=>$productRow["Unit"],
			                               "MainWeight"=>$productRow["MainWeight"],
			                               "Weight"=>$productRow["Weight"],
			                               "maxWeight"=>$productRow["maxWeight"],
			                               "minWeight"=>$productRow["minWeight"],
			                               "MisWeight"=>$productRow["MisWeight"],
			                               "CompanyId"=>$productRow["CompanyId"],
			                               "Description"=>$productRow["Description"],
			                               "Remark"=>$productRow["Remark"],
			                               "pRemark"=>$productRow["pRemark"],
			                               "TestStandard"=>$productRow["TestStandard"],
			                               "Relation"=>$relation,
			                               "boxSpec"=>$boxSpec,
			                               "boxWeight"=>$boxWeight,
			                               "BoxPcs"=>$boxPcs,
			                               "Date"=>$productRow["Date"],
			                               "PackingUnit"=>$productRow["PackingUnit"],
			                               "Estate"=>$productRow["Estate"],
			                               "Locks"=>$productRow["Locks"],
			                               "Code"=>$Code,
			                               "Operator"=>$productRow["Operator"]);
			       
		       }
		       $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$newDataArray);
		       $this->load->view('output_json',$data);
	       }
	      
      }
      
      
      public function get_ch_orderdata(){
	      
	      $params = $this->input->get();   
	      $KEY = element('KEY',$params,'');
	      if ($KEY=='READMC'){
		       $this->load->model('YwOrderSheetModel'); 	       
		       $dataArray=$this->YwOrderSheetModel->get_company_sheet($this->CompanyIds); 
     
		       $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$dataArray);
		       $this->load->view('output_json',$data);
	      }      
      }   
      
      public function get_order_pisheet(){
	      
	      $params = $this->input->get();   
	      $KEY = element('KEY',$params,'');
	      if ($KEY=='READMC'){
		       $this->load->model('YwOrderSheetModel'); 	       
		       $dataArray=$this->YwOrderSheetModel->get_company_pisheet($this->CompanyIds); 
     
		       $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$dataArray);
		       $this->load->view('output_json',$data);
	      }  
      }
      
       
}