<?php
//开料tv

defined('BASEPATH') OR exit('No direct script access allowed');

class Kltv extends MC_Controller {

     function totals() {
		
		$params = $this->input->get();
        $WorkShopId = element('wsId',$params,'105');
        
        $this->load->model('WorkShopdataModel');
        $this->load->model('CheckinoutModel');
        $this->load->model('StuffdataModel');
        $this->load->model('ScCjtjModel');
        $this->load->model('staffMainModel');
        
        $this->load->library('dateHandler');
        $laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');
        
        $timedata = $this->datehandler->get_worktimes();
		//$worktime = $timedata[0];
        
        $rows=$this->WorkShopdataModel->get_records($WorkShopId);
		$groups  =$rows['GroupId'];
		//当前车间生产人数
		
		//当天产值
		$groupnums=$groups==''?0:$this->staffMainModel->date_checkInNums_ingroup($groups,$this->Date);
		$output =$this->ScCjtjModel->get_workshop_day_output($WorkShopId);
		
		$listdatas  =array();
		$data['animate']=0;
		
		$data['wsId'] =$WorkShopId;
		
		$times_2 = $timedata[0];
		$data['worktime'] = $times_2;
		$times_2Arr = explode(':', $times_2);
		$data['worktime_0'] = element(0,$times_2Arr,'');
		$data['worktime_1'] = element(1,$times_2Arr,'');
		
		$data['groupnums'] = $groupnums;
		$data['output']    = number_format($output);
		$hoursNow = $timedata[1];
		
		
		$newDay = $hoursNow *$groupnums*$laborCost;

		
		$sleepRabit = '';
		$day_output = $output;
		$day_valuation  =$groupnums*$laborCost*$worktime;
 
 
	   $phidden = '0';
	   
	   
	   $percent = $newDay>0?
	   ''.round(($day_output-$newDay)/$newDay*100)
	   :($day_output>0?'100':'0') ;
	   
	   
	   $percentColor = '#01be56';
	   
	   if ($percent>15) {
		   
	   } else if ($percent > 0) {
		   $sleepRabit = 'rabbit.png';
	   } else if ($percent > -15) {
		   $sleepRabit = 'turtle.png';
		   $percent = 0- $percent;
	   } else {
		   $percentColor = '#ff0000';
		   $percent = 0- $percent;
	   }
	   
	   if ($day_output <= 0 && $newDay <= 0) {
			$sleepRabit = 'rabbit_sleep.png';
			$phidden = '1';
	   }
	   
	   
	   $dayDict = array(
		   'pHidden'    =>$phidden,
		   'pColor'     =>$percentColor,
		   'img'      =>$sleepRabit,
		   'percent'    =>$percent
	   );

		$data['dayDict'] = $dayDict;
		$data['blueIndex'] = $day_valuation>0?''.($newDay / $day_valuation):'0';
		
		$data['redIndex'] = $day_valuation>0?''.($day_output/ $day_valuation):($day_output>0?'1':'0');
		//当天生产记录，只取最后10条
		$rowArray = $this->ScCjtjModel->get_workshop_screcords($WorkShopId,$this->Date,6);
        $rownums =count($rowArray);
        
	    $listdatas=array();
	    $lastId=0;
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
	        
	        $lastId = $i==0? $rows['Id']: $lastId;
	        /*
	        SELECT S.Id,S.GroupId,S.sPOrderId,S.POrderId,S.StockId,S.Qty,S.Remark,D.StuffId,D.StuffCname,D.Picture,
	                        M.DeliveryWeek AS LeadWeek,G.Price,S.created,F.Name AS creator  */

	        $productImg=$rows['Picture']==1?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'images/noImage.png';
	        

	         $times = $this->datehandler->GetSecTimeOutString($rows['created'],'',0);
			 if ($i==0){
	               $seconds=floor((strtotime(date("Y-m-d H:i:s"))-strtotime($rows['created']))); 
		           $data['animate']=$seconds<20?1:0;
	         }  
	        
	        $Amount = $rows['Qty'] * $rows['Price'];
	        $listdatas[]=array(
			        'imgUrl'     =>"$productImg",
			        'times'      =>$times,
			        'cName'      =>''.$rows['StuffCname'],
			        'creator'    =>''.$rows['creator'],
			        'week'       =>''.substr($rows['LeadWeek'].'', 4,2),
			        'isOver'     =>$rows['LeadWeek']<$this->ThisWeek ? '1' : '0',
			        'Qty'        =>''.number_format($rows['Qty']),
			        'Price'      =>'¥'.number_format($rows['Price'],3),
			        'Amount'     =>'¥'.number_format($Amount,1)
		        );
        }
        
        $data['lastId']  = $lastId;
        $data['list']  = $listdatas;
		$this->load->view('kltv_totals',$data);
	}
	
	
	function totals_ajax() {
	
	    $params = $this->input->get();
        $WorkShopId = element('wsId',$params,'105');
		
		$this->load->library('dateHandler');
		$this->load->model('WorkShopdataModel');
		$this->load->model('staffMainModel');
		$this->load->model('ScCjtjModel');
		
		$rows=$this->WorkShopdataModel->get_records($WorkShopId);
		$groups  =$rows['GroupId'];
		//当前车间生产人数
		
		//当天产值
		$groupnums=$groups==''?0:$this->staffMainModel->date_checkInNums_ingroup($groups,$this->Date);
        $timedata = $this->datehandler->get_worktimes();
        
		$lastId = 0;
		$rowArray = $this->ScCjtjModel->get_workshop_screcords($WorkShopId,$this->Date,6);
        $rownums =count($rowArray);
        
        $dataOne = array(
			'id'=>'',
			'time'=>$timedata[0],
			'num'=>$groupnums);
		$times_2 = $timedata[0];
		$dataOne['time'] = $times_2;
		$times_2Arr = explode(':', $times_2);
		$dataOne['tim_0'] = element(0,$times_2Arr,'');
		$dataOne['tim_1'] = element(1,$times_2Arr,'');
        if ($rownums > 0) {
	        for ($i=0; $i<$rownums; $i++) {
		         $rows =$rowArray[$i];
	        if ($i==0) {
		        $dataOne['id'] = $rows['Id'];
	        }
	        
	          $times = $this->datehandler->GetSecTimeOutString($rows['created'],'',0);
			    
	        
	        $dataOne['time_'.$i] = $times;
	        }
	       
        }
		
		$data['jsondata'] = $dataOne;
		$this->load->view('output_json',$data);
	}
	
}