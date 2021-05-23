<?php
//出货tv

defined('BASEPATH') OR exit('No direct script access allowed');

class Chtv extends MC_Controller {
   //待出列表
   public function totals()
   {
        $params = $this->input->get();
        
        $page  = element('Page',$params,'1');

	    $this->load->model('TradeObjectModel'); 
	    $this->load->model('ProductstockModel'); 
	    
	    $logopath = $this->TradeObjectModel->get_logo_path();
	    
	    $rowArray = $this->ProductstockModel->get_order_stockqty();
	    
	    $listdata = array();
	    $stratIndex = $page==1 ? 0 : 10;
	    $limitIndex = $page==1 ?10 : 25;

        $sumtStockQty = $sumOverQty = $sumAmount = 0;
        
        $rowdatas = array();
	    for ($i = 0,$rownums=count($rowArray); $i < $rownums; $i++)
	    {
	        $rows =$rowArray[$i];
	        
	        $imgUrl = $logopath . 'L' . $rows['CompanyId'] . '.png';
	         
	        $rowdatas[]=array(
				'imgUrl'     =>''.$imgUrl,
				'CompanyId'  =>''.$rows['CompanyId'],
				'tStockQty'  =>''.number_format($rows['tStockQty']),
			    'Counts'     =>''.$rows['Counts'],
				'OverQty'    =>''.number_format($rows['OverQty']),
			    'OverCounts' =>''.$rows['OverCounts'],
			    'StaffName'  =>''.$rows['StaffName'],
			    'Forshort'   =>''.$rows['Forshort'],
				'Amount'    =>$rows['Amount']
			);
			$sumtStockQty+= $rows['tStockQty'];
			$sumOverQty+= $rows['OverQty'];
            $sumAmount+= $rows['Amount'];
            
	    }
	    
	    for ($j = $stratIndex,$nums = count($rowdatas); $j < $nums; $j++)
	    {
		    $row = $rowdatas[$j];
		    $percent = round($row['Amount']/$sumAmount*100);
		    $row['Percent'] = $percent;
		    
		    $listdata[] = $row;
		    if ($j>$limitIndex) break;
	    }
	    
	    $data['tStockQty'] = $sumtStockQty;
	    $data['OverQty'] = $sumOverQty;
	    $data['list'] = $listdata;
	    
	    
	    if ($page==1){
		     $this->load->view('chtv_totals',$data);  
	    }else{
		    $this->load->view('chtv_totals_2',$data);
	    }        
  }  
 
}