<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cktv extends MC_Controller {

       public function index()
       {
	       
	        $params = $this->input->get();
	        $sendFloor = element('floor',$params,'-1');
	        
	        $this->load->model('Ck9stocksheetModel');
	        $this->load->model('CkrksheetModel');
	        $this->load->model('Ck7bprkModel');
	        $this->load->model('Ck8bfsheetModel');
	         $this->load->model('WarehouseModel');
	        
	        $query = $this->Ck9stocksheetModel->get_all_qty($sendFloor);
	        $todayrkRow = $this->CkrksheetModel->get_today_rked($sendFloor);
	        $row = $query->first_row('array');  
	        $allckQty = $row['Amount'];
	       
	        $query = $this->Ck9stocksheetModel->get_all_hasorder_floor($sendFloor);
	        $rowHasOrder = $query->first_row('array'); 
	        $hasOrderQty = $rowHasOrder['OrderAmount'];
	        $query = $this->Ck9stocksheetModel->get_over3m_notout($sendFloor);
	        $rowChart = $query->first_row('array');
	        $redQty = $rowChart['YearAmount'];
	        $query = $this->Ck9stocksheetModel->get_in1m_notout($sendFloor);
	        $rowChart = $query->first_row('array');
	        $blueQty = $rowChart['YearAmount'];
	        $leftQty = $allckQty - $blueQty - $redQty;
	        
	        $rowBP = $this->Ck7bprkModel->get_day_qtycount($sendFloor);
	        $rowBPMon = $this->Ck7bprkModel->get_month_qtycount($sendFloor);
	        $rowBF = $this->Ck8bfsheetModel->get_day_qtycount($sendFloor);
	        $rowBFMon = $this->Ck8bfsheetModel->get_month_qtycount($sendFloor);
	       
//      	top 
	        $data['ckQty']=number_format($row['Qty']);
	        $data['ckStuffCount']=number_format($row['Count']);

	        $data['rkQty']=number_format($todayrkRow['rkQty']);
	        $data['rkCount']=number_format($todayrkRow['counts']);
	        
	        $data['blQty']=number_format($rowBP['Qty']);
	        $data['blCount']=number_format($rowBP['counts']);
//			center this is for the draow
            $allckQty  = $allckQty > 0 ? $allckQty : 1;
	        $data['blueIndex']=( $blueQty/$allckQty*100);
	        $data['blueNextIndex']=($leftQty/$allckQty*100);
	        $data['redIndex']=(($redQty/$allckQty*100)>=100 ? 99 :($redQty/$allckQty*100) );
	        
	        $data['greenIndex']= ($rowHasOrder['OrderAmount']/$row['Amount']*100);
	        $data['centerQty'] = number_format($rowHasOrder['OrderQty']).'pcs';
	      
	      // $data['greenIndex']= ($orderAmount/$row['Amount']*100);
	       // $data['centerQty'] = number_format($orderQty).'pcs';
	        
// 	        bottom
	        $data['waitbuQty'] = "0";
	        $data['waitbuCount'] = "0";
	        $data['buQty'] = "0";
	        $data['buCount'] = "0";
	        
	        $data['todayBpQty'] = $data['blQty'];
	        $data['todayBpCount'] = $data['blCount'];
	        $data['monthBpQty'] = number_format($rowBPMon['Qty']);
	        $data['monthBpCount'] = number_format($rowBPMon['counts']);
	        
	        $data['todayBfQty'] = number_format($rowBF['Qty']);
	        $data['todayBfCount'] = number_format($rowBF['counts']);
	        $data['monthBfQty'] = number_format($rowBFMon['Qty']);
	        $data['monthBfCount'] = number_format($rowBFMon['counts']);
	        
	        
            $this->load->view('cktv_totals',$data);
       }
       
       public function totals()
       {
	        $params = $this->input->get();
	        $whName = element('Floor',$params,'-1');
	        
	         $this->load->model('WarehouseModel');
	         $this->load->model('CkrksheetModel');
	         $this->load->model('CkllsheetModel');
	         
	         $data = array();
	         
	        $records =$this-> WarehouseModel->get_warehourse_byname($whName);
	        $warehouseId = $records['Id'];
	        $sendFloor      = $records['SendFloor'];
	        $records = null;
	       
	        if ($warehouseId<=0) return;
	        
	        $records = $this->CkrksheetModel->get_stock_amount($warehouseId,$sendFloor);
            $stockQty         = round($records['Qty']);
            $stockCount    = round($records['Counts']);
            $stockAmount = round($records['Amount']);
            $records = null;
           
            $data['ckQty']=number_format($stockQty);
	        $data['ckStuffCount']=number_format($stockCount);
	        
	        $records = $this->CkrksheetModel->get_rk_daycount($warehouseId);
	        $data['rkQty']=number_format($records['Qty']);
	        $data['rkCount']=number_format($records['Counts']);
	        $records = null;
	        
	        $records = $this->CkrksheetModel->get_order_amount($warehouseId,$sendFloor);
	         $orderQty        = round($records['OrderQty']);  //订单需求数量
             $orderAmount= round($records['Amount']); //订单需求金额
             $M1Amount   = round($records['M1Amount']);//一个月内未有下单
             $M3Amount   = round($records['M3Amount']);//三个月内未有下单
        
             $M0Amount = $orderAmount-$M1Amount;
             $M1Amount = $M1Amount - $M3Amount;
            
             $data['redIndex']=round($M3Amount/$stockAmount*100);
	         $data['blueNextIndex']=round($M1Amount/$stockAmount*100);
	         $data['blueIndex']=100-$data['blueNextIndex']-$data['redIndex'];
	        
	        $data['greenIndex']= round($orderAmount/$stockAmount*100);
	        //$data['centerQty'] = number_format($orderQty).'pcs';
	        $data['centerQty'] = '';
	        
             $records =null;
             $records =$this->CkrksheetModel->get_bprk_daycount($warehouseId);//当日备品转入
             $data['todayBpCount'] = number_format($records['Counts']);
             $data['todayBpQty']     = number_format($records['Qty']);
             $data['blQty']       = $data['todayBpQty'] ;
	          $data['blCount'] =$data['todayBpCount'] ;
             
             $records =null;
             $records =$this->CkrksheetModel->get_bprk_monthcount($warehouseId);//当月备品转入
             $data['monthBpCount'] = number_format($records['Counts']);
             $data['monthBpQty']     = number_format($records['Qty']);
             
             $records =null;
             $records =$this->CkllsheetModel->get_bf_daycount($warehouseId,$sendFloor);//当日报废
             $data['todayBfCount'] = number_format($records['Counts']);
             $data['todayBfQty']      = number_format($records['Qty']);
             
             $records =null;
             $records =$this->CkllsheetModel->get_bf_monthcount($warehouseId,$sendFloor);//当月报废
             $data['monthBfCount'] = number_format($records['Counts']);
             $data['monthBfQty']      = number_format($records['Qty']);
         
            $data['waitbuQty'] = "0";
	        $data['waitbuCount'] = "0";
	        $data['buQty'] = "0";
	        $data['buCount'] = "0";
	        
	        $this->load->view('cktv_totals',$data);
	   }
}