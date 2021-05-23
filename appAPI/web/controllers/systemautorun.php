<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SystemAutorun extends MC_Controller {
     
    function main($doAction='0')
	{
	       if ($doAction==1) {
	            if (date('d')=='01')  $this->statics();
	            
	            $info=date('Y-m-d') . "已自动运行！\r\n";
				$fp = fopen("web\logs\autorun.log", "a");
				 fwrite($fp, $info);
				 fclose($fp);
	       }
	}

    function statics()
    {
		    $this->load->model('CkrksheetModel'); 
		    $this->load->model('StatisticsModel');
		     
		    $FirstIds="1403,14030,1405";
		    $FirstIdArray=explode(',', $FirstIds);
		    
		    $curdate =$this->Date;
		    $Month=date('Y-m',strtotime("$curdate - 1 month"));
	       
		    foreach($FirstIdArray as $FirstId){
		    
		             $totalValue = 0; $otherValue = "";
					 switch($FirstId){
					     case 1403://原材料
						    $records = $this->CkrksheetModel->get_stock_amount('all');
					        $totalValue = round($records['Amount']);
					        $records = null;
							        
						    $records = $this->CkrksheetModel->get_order_amount('all','');
					        $orderAmount= round($records['Amount']);      //订单需求金额
					        $M1Amount   = round($records['M1Amount']);//一个月内未有下单
					        $M3Amount   = round($records['M3Amount']);//三个月内未有下单
					        
					        $otherValue="$orderAmount|$M1Amount|$M3Amount";
					    break;
					 default:
					      $Amount  = $this->StatisticsModel->get_totals_amount($FirstId);
						  $totalValue = round($Amount);
					   break;
                }
                
                $this->StatisticsModel->save_lastmonth_totalvalue($FirstId,$Month,$totalValue,$otherValue);
                //echo "$FirstId: $TotalValue / $otherValue <br>";
		   }				        
    } 
    
}