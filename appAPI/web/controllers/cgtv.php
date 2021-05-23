<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cgtv extends MC_Controller {

       // 采购 仿app  
   public function totals()
   {
                
                
                
        $this->load->model('YwOrderSheetModel');
        
        
        $ADATA['puc1']       = '85';
	    $ADATA['puc2']       = '90';
	    $ADATA['puc3']       = '87';
	    $ADATA['pucTitle1']       = '17周';
	    $ADATA['pucTitle2']       = '10';
	    $ADATA['pucTitle3']       = '10';
	    
        
        $ADATA['grayIndex']       = '0';
	    $ADATA['grayIndex1']       = '0';
	    $ADATA['grayIndex2']       = '0';
	    $ADATA['grayIndex3']       = '0';
	    
	    
	    
        $message='';
		$this->load->model('cg1stocksheetModel');
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$czId = element('cz',$params,'');

		$nextWeekTime     = date('Y-m-d', strtotime('+1 week'));
		$nextnextWeekTime = date('Y-m-d', strtotime('+2 weeks'));
		
		$lastWeekTime     = date('Y-m-d', strtotime('-1 week'));
		$lastlastWeekTime = date('Y-m-d', strtotime('-2 weeks'));
		
		$thisWeek     = $this->ThisWeek;
		
		$nextWeek     = $this->getWeek($nextWeekTime);
		$nextnextWeek = $this->getWeek($nextnextWeekTime);
		
		$lastWeek     = $this->getWeek($lastWeekTime);
		$lastlastWeek = $this->getWeek($lastlastWeekTime);
		// getall 
		$rs = $this->cg1stocksheetModel->get_weeks_before('9999999',$buyerId);
		
		$overAmount = $overCounts = 0;
		$curAmount  = $curCounts = 0;
		$nextAmount = $nextCounts = 0;
		$nnextAmount = $nnextCounts = 0;
		
		$allamount = $allQty = 0;
		if ($rs != null) {
			foreach ($rs as $rows) {
				$rowWeek   = $rows['Weeks'];
				$rowAmount = $rows['Amount'];
				$rowQty    = $rows['Qty'];
				$rowCounts = $rows['Counts'];
				
				$allQty += $rowQty;
				$allamount += $rowAmount;
				
				if ($rowWeek!='') {
					if ($rowWeek < $thisWeek) {
					$overAmount +=$rowAmount;
					$overCounts += $rowCounts;
				} else if ($rowWeek == $thisWeek) {
					$curAmount +=$rowAmount;
					$curCounts += $rowCounts;
				} else if ($rowWeek == $nextWeek) {
					$nextAmount +=$rowAmount;
					$nextCounts += $rowCounts;
				} else {
					$nnextAmount +=$rowAmount;
				}
				} 
			}
		}
		
		$percent1 = $percent2 = $percent3 = $percent4 = '--';
		if ($allamount > 0) {
			$percent1 = round($overAmount / $allamount *50, 1);
			$percent2 = round($curAmount  / $allamount *50, 1);
			$percent3 = round($nextAmount / $allamount *50, 1);
			$percent4 = 50 - $percent1 - $percent2 - $percent3;
			
			$ADATA['grayIndex']       = $percent4;
		    $ADATA['grayIndex1']       = $percent3;
		    $ADATA['grayIndex2']       = $percent2;
		    $ADATA['grayIndex3']       = $percent1;
		    
		    
	    
		}
		
		$ADATA['all_amount']       = '¥'.number_format($allamount);
	    $ADATA['all_qty']       = number_format($allQty).'pcs';
	    
	    $ADATA['all_amount1']       = $overAmount<=0?'': '¥'.number_format($overAmount);
	    $ADATA['all_counts1']       = $overCounts<=0?'': number_format($overCounts);
	    
	    
	    $ADATA['all_amount2']       = $curAmount<=0?'': '¥'.number_format($curAmount);
	    $ADATA['all_counts2']       = $curCounts<=0?'': number_format($curCounts);
	    
	    
	    $ADATA['all_amount3']       = $nextAmount<=0?'': '¥'.number_format($nextAmount);
	    $ADATA['all_counts3']       = $nextCounts<=0?'': number_format($nextCounts);
	    
	    
	    $status['chartVals'] = array(
		    array('value'=>$nnextAmount,'color'=>'#d5d5d5'),
		    array('value'=>$nextAmount,'color'=>'#c7e0ed'),
		    array('value'=>$curAmount,'color'=>'#358fc1'),
		    array('value'=>$overAmount,'color'=>'#fd0300')
	    );
	    
	    
	     $ADATA['pucTitle1']       = substr($thisWeek, 4, 2).'周';
	     $ADATA['pucTitle2']       = substr($lastWeek, 4, 2).'周';
	     $ADATA['pucTitle3']       = substr($lastlastWeek, 4, 2).'周';
	    $punctuality = $this->cg1stocksheetModel->cg_punctuality('','', $thisWeek, $buyerId);
	    
	    $ADATA['puc1']       = $punctuality!=null ?$punctuality:'--';
	    $punctualityColor = $punctuality >=90 ? '#01be56':'#ff0000';
	    
	    
		$punctuality = $this->cg1stocksheetModel->cg_punctuality('','', $lastWeek,$buyerId);
		$punctualityColor = $punctuality >=90 ? '#01be56':'#ff0000';
		 $ADATA['puc2']       = $punctuality!=null ?$punctuality:'--';
	    
		$punctuality = $this->cg1stocksheetModel->cg_punctuality('','', $lastlastWeek,$buyerId);
		 $ADATA['puc3']       = $punctuality!=null ?$punctuality:'--';
		
	   
	   
	    
		$this->load->model('cg1stocksheetModel');
		$this->load->model('StaffMainModel');

		$nextWeekTime     = date('Y-m-d', strtotime('+1 week'));
		
		$nextWeek     = $this->getWeek($nextWeekTime);
		$data=$this->cg1stocksheetModel->all_cgmain_new($nextWeek);
		
		$listall = array();
	    foreach ($data as $rows) {
		    
		    $adata = $rows;
		    
		    $allqtyOne = 0;
		    $allqtyOne = $rows['v_1']+$rows['v_2']+$rows['v_3']+$rows['v_4'];
			$adata['grayIndex']       =$allqtyOne<=0?'0': ($rows['v_3']/$allqtyOne*100);
			$adata['grayIndex1']       =$allqtyOne<=0?'0': ($rows['v_4']/$allqtyOne*100);
			$adata['grayIndex2']       =$allqtyOne<=0?'0': ($rows['v_2']/$allqtyOne*100);
			$adata['grayIndex3']       =$allqtyOne<=0?'0': ($rows['v_1']/$allqtyOne*100);
		    
		    $buyerId = element('buyer', $rows, '');
		    
		    $img = $this->StaffMainModel->get_photo($buyerId);
		    $adata['img'] = '../../../'.$img;
			$listall[]=$adata;
		    
		    /*
			    $personsArr[]=array(
		    	'tag'=>'cg_person',
		    					"buyer"=>"$oldBuyerId",
	    						"name"=>"$BuyerName",
	    						"amount"=>"¥$BuyerAmount",
	    						"allqty"=>"$BuyerQty".'pcs',
	    						'valX'=>'-28',
	    						'count1'=>''.$buyerWaitCgCount,
	    						'count2'=>''.$BuyerOverCount,
	    						'count3'=>''.$BuyerCurCount,
	    						"value1"=>"¥$buyerWaitCgAmount",
	    						"value2"=>"¥$BuyerOverAmount",
	    						"value3"=>"¥$BuyerCurAmount",
	    						'v_1'=>$val1,
	    						'v_2'=>$val2,
	    						'v_3'=>$val3,
	    						'v_4'=>$val4,
	    						'chartVals'=>array(
			    							array('value'=>$val1,'color'=>'#fd0300'),
			    							array('value'=>$val2,'color'=>'#358fc1'),
			    							array('value'=>$val4,'color'=>'#c7e0ed'),
			    							array('value'=>$val3,'color'=>'#d5d5d5')
			    						),

	    						
	    						);

		    */
	    }
	    
	    
	    
	    
	    
		for ($i=0; $i<4; $i++) {
			
			
		}
		

	
        $ADATA['list']       = $listall;
        

        if ($czId == 'cz') {
	        var_dump($listall);
        }
        
        $this->load->view('cgtv_totals',$ADATA);
    }
    

    function _get_arr($arr,$ind)
    {
         $val = !empty($arr[$ind]) ? $arr[$ind] : null;
         return $val;
    }
}