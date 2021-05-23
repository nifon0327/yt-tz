<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Order extends MC_Controller {
	
		
		public function param_stockid() {
			
			$params = $this->input->post();
			$newStockid = element('stockid',$params,-1);

            if ($newStockid > 0) { 
	            $paramNiew = $this->get_params_usestockid($newStockid); 
            }
            
            $rows = $paramNiew;
            
            $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$rows);
		    $this->load->view('output_json',$data);
           
		}
		
		function child_stuffs() {
			$params = $this->input->post();
			$mstockid = element('Id',$params,-1);
		    $stockid = element('type',$params,-1);
		    $this->load->model('cg1stocksheetModel');
		    $rows = $this->cg1stocksheetModel->get_child_stuff($mstockid, $stockid);
			
			$data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		function mother_stuff() {
			$params = $this->input->post();
			$stuffid = element('stuffid',$params,-1);
		    $stockid = element('stockid',$params,-1);
		    $this->load->model('cg1stocksheetModel');
		    $rows = $this->cg1stocksheetModel->get_parent_info($stockid);
			
			$data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$rows);
		    $this->load->view('output_json',$data);

		}
		
		public function semi_bom() {
			   $params = $this->input->post();

			  $this->load->model('ScSheetModel');
			  $this->load->model('stuffdataModel');
			  $this->load->model('CkrksheetModel');

		    $stuffid = element('stuffid',$params,-1);
		    $mStockid = element('stockid',$params,-1);
		    

			$rows = array();
			$bomhead = array(
				'potitle'=>'PO',
				'qtytitle'=>'数量',
				'pricetitle'=>'含税价',
				'porderid' =>$mStockid
				);
			$query = $this->ScSheetModel->semi_bomhead($mStockid);
			//OrderPo,D.StuffCname,K.tStockQty ,W.Name,SC.WorkShopId,CG.Price,CG.StuffId
			
			$bluefont        =$this->colors->get_color('bluefont');
			$grayfont        =$this->colors->get_color('grayfont');
			if ($query->num_rows() > 0) {
				$row = $query->first_row('array');
				$bomhead['cname']='      '.$row['StuffCname'].'';
				$bomhead['tstock']=''.number_format($row['tStockQty']);
				$bomhead['ws']=''.$row['Name'];
				$bomhead['po']=''.$row['OrderPo'];
				$bomhead['qty']=''.number_format($row['Qty']);
				$bomhead['price']='¥'.round($row['Price'],2);
				$bomhead['week']=''.$row['DeliveryWeek'];
				$StuffId  = $row['StuffId'];
                $bomhead['icon_url']=$this->stuffdataModel->get_stuff_icon($StuffId);


				$records = $this->CkrksheetModel->get_stuff_location($StuffId);
				$locations = array();
				$inter = 0;
				foreach ($records as $row1){
			       $rkloc     = '未设置';
			       $rkQty     = number_format($row1['rkQty']);
			       
			       if ($row1['Identifier']!=''){
				      $idents=explode('-', $row1['Identifier']);
			          $rkloc=$idents[count($idents)-1]; 
			       }
			       
			       $locations[]=''.$rkloc;
			       $inter ++;
			    }
			    
			    if ($inter > 0) {
				    $attr = array('isAttribute'=>'1');
				    $attrDicts = array();
				    for ($i=0;$i<$inter;$i++) {
					    $attrDicts[]=array('Text'=>''.$locations[$i],'Color'=>$bluefont,'FontSize'=>'11');
					    if ($i<($inter-1))
					    $attrDicts[]=array('Text'=>'|','Color'=>$grayfont,'FontSize'=>'11');
				    }
				    $attr['attrDicts'] = $attrDicts;
				    $bomhead['position'] = $attr;
			    }
				
				$bomhead['wsImg']='ws_'.$row['WorkShopId'].'.png';
			}
            
            $bomlistDict = $this->ScSheetModel->semi_bomlist($mStockid);
			$bomhead['price']='¥'.$bomlistDict['taxPrice'];
            $rows[]=$bomhead;
            $rows[]=$bomlistDict['list'];
            
            
			$basePath = $this->stuffdataModel->get_picture_path();
	        $data['jsondata']=array('status'=>'1','message'=>$basePath,'totals'=>2,'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		public function bom_detail() {
		
		    $params = $this->input->post();
		    $porderid = element('productid',$params,-1);
		    $stuffid = element('stuffid',$params,-1);
		    $newStockid = element('stockid',$params,-1);
		    
			$this->load->model('orderSheetModel');
			$rows = array();
            $bomhead = $this->orderSheetModel->order_headinfo($porderid);
            
            if ($newStockid > 0 && $porderid <= 0) {
            
	            $paramNiew = $this->get_params_usestockid($newStockid);
	            $stuffid = $paramNiew["StuffId"];
	            $porderid = $paramNiew["POrderId"];
	            
            }
           
            if ($stuffid==-1) {
	            $bomlist = $this->orderSheetModel->order_bomdetail($porderid);
            }
             
            else {
	            $bomlist = $this->orderSheetModel->order_bomdetail_tp2($porderid,$stuffid);
            }

            $rows[]=$bomhead;
            $rows[]=$bomlist;
            
            $bomcost = $this->orderSheetModel->order_bomcost($porderid);
			$rows[]=$bomcost; 
            
            $this->load->model('stuffdataModel');
            
			$basePath = $this->stuffdataModel->get_picture_path();
	        $data['jsondata']=array('status'=>'1','message'=>$basePath,'totals'=>2,'rows'=>$rows);
		    $this->load->view('output_json',$data);
			
	}
		
		//qc_record
	public function qc_record() {
			$this->load->model('stuffdataModel');//StuffPropertyModel
            $params = $this->input->post();
			$stuffid = element('stuffid',$params,-1);
			
			$rows = $this->stuffdataModel->qc_record_his($stuffid);
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($rows),'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		
	public function url_box() {
			$this->load->model('orderSheetModel');//StuffPropertyModel
            $params = $this->input->post();
			$porderid = element('order',$params,-1);
			$rows1=$this->orderSheetModel->url_box($porderid);
			$rows = array();
			$rows[]=$rows1;
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($rows),'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		
	public function product_his() {
			$this->load->model('orderSheetModel');//StuffPropertyModel
            $params = $this->input->post();
			$productid = element('productid',$params,-1);
			
			if (strlen($productid) == 12) {
				$productid = $this->orderSheetModel->get_productid($productid);
			}
			
			$rows = $this->orderSheetModel->product_analyse_sheet($productid);
			
			$data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($rows),'rows'=>$rows);
		    $this->load->view('output_json',$data);
		}
		
		 function get_params_usestockid($StockId) { 
            $this->load->model('stuffdataModel');
            $this->load->model('ScSheetModel');
            $this->load->model('cg1stocksheetModel'); 
            $stuffid = $porderid = $imgPath = $StuffCname = '';
            
            
			$query = $this->cg1stocksheetModel->get_params_usestockid($StockId);
			$checkBom = $this->ScSheetModel->semi_bomhead($StockId);
			$record = $this->ScSheetModel->get_records_mstock($StockId);
			$isSemi = 0;
			$hasGx = 0;
			if ($checkBom->num_rows() > 0) {
				$isSemi = '1';
			}
			if ($record['ActionId']==102) {
				$hasGx = 1;
			}
			$ischild = 0;
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$stuffid = $row->StuffId;
				$ischild = $this->stuffdataModel->get_ischild($stuffid);
				$porderid = $row->POrderId;
				$StuffCname = $row->StuffCname;
				
	    		
				$imgPath = $this->stuffdataModel->get_stuff_picture($stuffid);
				if ($this->LoginNumber == 11965) {
				//$ischild = 1;
 				$imgPath = 'http://ashcloud.com/workshop/image/recording.gif';
 				

			$imgPath = 'http://116.6.107.228:8062/download/stufffile/92421.gif';

			}
			}
			
			
	    		
	    return array("StuffId"=>"$stuffid",
	    			 "POrderId"=>"$porderid",
	    			 "imgUrl"=>"$imgPath",
	    			 "StuffCname"=>"$StuffCname",
	    			 'child'=>"$ischild",
	    			 'isSemi'=>"$isSemi",
	    			 'hasGx' =>"$hasGx");
    }
    
    
    
    function gx_img() {
	    $params = $this->input->post();
	    $this->load->model('ProcessSheetModel');
	    $stuffid = element('stuffid',$params,'-1');
	    $rows = $this->ProcessSheetModel->gx_img($stuffid);
	    $i = 0;
	    $mStuffId = $stuffid;
	    foreach ($rows as $row) {
		    $StuffId     = $row['StuffId'];
		    $ProcessId   = $row['ProcessId'];
		    $ProcessFile = $mStuffId."_".$StuffId."_".$ProcessId.".jpg";
		    if(file_exists("../download/processimg/".$ProcessFile)){
			    $rows[$i]['url'] = $this->config->item('download_path') . "/processimg/" .$ProcessFile;
			}
		    
		    $i ++;
	    }
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$i,'rows'=>$rows);
		$this->load->view('output_json',$data);
	    
    }
}