<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ordertv extends MC_Controller {

       //车间生产待出    
   public function todays()
   {
                
                
                
        $this->load->model('YwOrderSheetModel');
	    
	    $numsOfTypes=0; 
	    $ADATA['grayIndex']       = '10';
	    $listall = array();
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$white   =$this->colors->get_color('white');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$orderorange =$this->colors->get_color('orderorange');
		$ordergreen  =$this->colors->get_color('ordergreen');
		$ordergray  =$this->colors->get_color('ordergray');
		
	
		$totals=0;
		
		
		$params = $this->input->get();
        $findDate = element('date',$params,'');
        if ( $findDate != '') {
	        $today = $findDate;
        } else {
	        $today = date('Y-m-d');
        }
//  $today = '2016-07-18';
		$query = $this->YwOrderSheetModel->order_new_date('',$today);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);

		if ($numsOfTypes == 1) {
			  
			$rows = $resultArray[0];
		   $OrderDate = $today;
// 		   $OrderDate = element('OrderDate',$rows,'');
		   $LowProfit = element('LowProfit',$rows,'');
		   $LowProfit = $LowProfit>0?$LowProfit:'';
		   $cost = $this->YwOrderSheetModel->order_date_cost($OrderDate) ;
		   
		   $lock = $this->YwOrderSheetModel->check_lock_date($OrderDate);
		   $explock = $this->YwOrderSheetModel->check_explock_date($OrderDate);
		   
		   
		    $noProfRow = $this->YwOrderSheetModel->noprof_date_amount($OrderDate);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = 0;
		   if ($vag2 > 0)
		   $noProfCost = $this->YwOrderSheetModel->noprof_date_cost($OrderDate);
		   
		   $amount = element('Amount',$rows,'');
		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   $percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   $ADATA['grayIndex']       = $percent/2;
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);

		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   
		   $weekday = date('w',strtotime($OrderDate));
		   $title = date('m-d',strtotime($OrderDate));
		   
		   $istoday = $OrderDate == $this->Date ? true : false;

		   $dateCom = explode('-', $title);
			
		   $Counts = element('Counts',$rows,'');
		
		
			
		$listall[]=array(
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'datecom'    =>$dateCom,
			'istoday'    =>$istoday,
			'amount'     =>'¥'.number_format($amount),
			'qty'        =>''.number_format(element('Qty',$rows,'')),
			'counts'     =>$Counts.'',
			'percent'    =>$percent,
			'percolor'   =>$percentcolor,
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
		);
			
		   
		
      } else {
	      $OrderDate = $today;
	         
		   $weekday = date('w',strtotime($OrderDate));
		   $title = date('m-d',strtotime($OrderDate));
		   
		   $istoday = $OrderDate == $this->Date ? true : false;

		   $dateCom = explode('-', $title);
			
	      $listall[]=array(
			'vag1'       =>'',
			'vag2'       =>'', 
			'datecom'    =>$dateCom,
			'istoday'    =>$istoday,
			'amount'     =>'¥'.number_format(0),
			'qty'        =>''.number_format(0),
			'counts'     =>'',
			'percent'    =>'',
			'percolor'   =>'',
			'lockImg'    =>'',
			'lock_sImg'  =>'',
		);
	      
      }


        $this->load->model('TradeObjectModel');
	    
	    $numsOfTypes=0; 
	    $iddate = $today;
		
	
		$totals=0;
		$query = $this->YwOrderSheetModel->order_new_company($iddate);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		  
		for ($i = 0; $i < $numsOfTypes; $i++) {
			$rows = $resultArray[$i];
		   
		   $LowProfit = element('LowProfit',$rows,'');
		   $LowProfit = $LowProfit>0?$LowProfit:'';
		   $Forshort = element('Forshort',$rows,'');
		   $CompanyId = element('CompanyId',$rows,'');
		   $Logo = element('Logo',$rows,'');
		   $cost = $this->YwOrderSheetModel->order_date_company_cost($iddate, $CompanyId) ;
		   $lock = $this->YwOrderSheetModel->check_lock_date_company($iddate, $CompanyId);
		   $explock = $this->YwOrderSheetModel->check_explock_date_company($iddate, $CompanyId);
		   
		   $noProfRow = $this->YwOrderSheetModel->noprof_date_company_amount($iddate, $CompanyId);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   $noProfCost = 0;
		   if ($vag2 > 0)
		   $noProfCost = $this->YwOrderSheetModel->noprof_date_company_cost($iddate, $CompanyId);
		   
		   $amount = element('Amount',$rows,'');
		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   //$percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   if($trueAmount>0){
			   $percent = round($trueProfit/$trueAmount*100);
		   }else {
		       //卖价为0时
			   $percent = round($profit/$cost*100);
		   }
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
		   
		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   $Counts = element('Counts',$rows,'');
		   
		   $listall[]=array(
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'img'        =>$Logo==''?'':$LogoPath.$Logo,
			'forshort'   =>$Forshort,
			'amount'     =>$rows['PreChar'].number_format($rows['realAmount']),
			'qty'        =>''.number_format(element('Qty',$rows,'')),
			'counts'     =>$Counts.'',
			'percent'    =>$percent,
			'percolor'   =>$percentcolor,
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
		);
		
		  
      }
      
/*
      $listall[]=array(
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'img'        =>$Logo==''?'':$LogoPath.$Logo,
			'forshort'   =>$Forshort,
			'amount'     =>$rows['PreChar'].number_format($rows['realAmount']),
			'qty'        =>''.number_format(element('Qty',$rows,'')),
			'counts'     =>$Counts.'',
			'percent'    =>$percent,
			'percolor'   =>$percentcolor,
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
		);
*/

       






        $ADATA['list']       = $listall;

        
        
        $this->load->view('ordertv_today',$ADATA);
    }
    

    function _get_arr($arr,$ind)
    {
         $val = !empty($arr[$ind]) ? $arr[$ind] : null;
         return $val;
    }
}