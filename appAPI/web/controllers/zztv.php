<?php
//组装tv

defined('BASEPATH') OR exit('No direct script access allowed');

class Zztv extends MC_Controller {
   //生产任务(按拉线)
   public function sctasks()
   {
        $params = $this->input->get();
        
        $WorkShopId = element('wsId',$params,'101');
        $Line       = element('Line',$params,'A');
        
	    $this->load->model('WorkShopdataModel'); 
	    $this->load->model('ScSheetModel'); 
	    $this->load->model('StaffMainModel'); 
	    $this->load->model('ScCjtjModel');
	    $this->load->model('WorksclineModel');
        $this->load->model('ProductdataModel');
        $this->load->model('ScCurrentMissionModel');
        $this->load->model('ScgxRemarkModel');
         
	    $this->load->library('dateHandler');
	    
	    $data=array();
	    
	    $Lines  = explode(',', $Line);
        
	    
	    if (count($Lines)==2){
		   $data['Line']  =$Lines[1] . ',' . $Lines[0]; 
	    }else{
		   $data['Line']  =$Line; 
	    }
	   
	    $m = 0;
	    
	    do{
		    $scLine = $Lines[$m];
		    
		    $records= $this->WorksclineModel->get_scline_info($scLine);
		    $LeaderNumber = $records['GroupLeader'];
		    $GroupId      = $records['GroupId'];
		    $LineId       = $records['Id'];
		    
			$photoPath = $this->StaffMainModel->get_photo($LeaderNumber);
			$data['personImg']  = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $photoPath;
			$data['shopTitle']  = '组装';
	        $data['titleImg']   = '' . $scLine . '1.png'; 
	        $data['titleImg2']  = '' . $scLine . '.png';
	        
	        $ActionId=$this->WorkShopdataModel->get_workshop_actionid($WorkShopId);
	        
	        $rowArray =$this->ScSheetModel->get_canstock_list($WorkShopId,$ActionId,'DSC',$LineId);
	        $rownums =count($rowArray);
	        
	        $curWeeks = $this->ThisWeek;
	        $sPOrderIds =$this->ScCurrentMissionModel->get_all_records();
	        	
	        $overQty=0;      $overCount = 0;
	        $scedQty=0;      $scinqQty  = 0;
	        $bledQty=0;      $bledCount = 0;
		    $listdatas=array();
		    
			for ($i = 0; $i < $rownums; $i++) {
			    $rows =$rowArray[$i];
			    
			    $created    = $rows['created'];
				$created    = $this->datehandler->GetDateTimeOutString($created,"");
				
			    $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'images/noImage.png';
			    
			    if (strpos($sPOrderIds,$rows['sPOrderId'])){
		            $coding='1';
	            }
	            else{
		            $coding='0'; 
	            }
	            
	            $isOver = $rows['LeadWeek']<$curWeeks ? 1 : 0;
	            if ($isOver==1){
		            $overQty+= $rows['Qty'];
		            $overCount++;
	            }
	            
	            $scQty= $this->ScCjtjModel->get_scqty($rows['sPOrderId']);
	            if ($scQty>0){
		            $scedQty+=$scQty;
		            $scinqQty+=$rows['Qty'];
	            }
	            
	            $bledQty+=$rows['Qty'];
	            $bledCount++;
	            
	            $time = '';
	            $min30 = 60 *30;
		        $hour12 = $min30*2 *4*3;
		        $twoDay = $hour12 * 4;
	            $nowTime =strtotime(date("Y-m-d H:i:s"));
	            
		        $minutes=floor($nowTime-strtotime($rows['created'])); 
		        $timeColor = '#727171';
		        $time = $this->datehandler->GetTimeInterval($minutes?$minutes:0);
	
		        if ($minutes > $twoDay)
		        {
			        $timeColor = '#ff0000';
		        } else if ($minutes > $hour12) {
			        $timeColor = '#ff9946';
		        }
		        
		        $Remark='';$R_time='';
		        $Remarks=$this->ScgxRemarkModel->get_remark($rows['sPOrderId']);
		        if ($Remarks->num_rows()>0) {
				    $RemarkRow = $Remarks->row_array();
				    $Remark  = $RemarkRow['Remark'];
				    
				    $R_name = $this->StaffMainModel->get_staffname($RemarkRow['creator']);
				    $R_time = $this->datehandler->GetDateTimeOutString($RemarkRow['created'],'',0);
				    
				    $R_time = $R_name . ' ' . $R_time;
				 }
	            
			    $list=array(
					'week'       =>''.substr($rows['LeadWeek'].'', 4,2),
					'Forshort'   =>''.$rows['Forshort'],
				    'isOver'     =>"$isOver",
					'cName'      =>$rows['cName'],
					'ShipType'   =>''.$rows['ShipType'],
					'nameColor'  =>"",
					'time'       =>"$created",
					'timeColor'  =>"$timeColor",
					'imgUrl'     =>''.$productImg,
					'scqty'      =>round($scQty) ,
					'qty'        =>round($rows['Qty']),
					'isCoding'   =>"$coding",
					'Remark'     =>"$Remark",
					'R_time'     =>"$R_time" 
				);
				
				if ($coding==1){
					array_splice($listdatas, 0,0,array($list));
				}
				else{
					$listdatas[]=$list;
				}
				
				
			}
			$dayQty = $this->ScCjtjModel->get_day_scqty($WorkShopId,'',$GroupId);
			$allQty = $bledQty-$scedQty;
			
	        $data['dayQty']  =number_format($dayQty);
	        $data['allQty']  =number_format($allQty);
	        
	        $data['overQty']    =number_format($overQty);
	        $data['overCount']  = $overCount;
	        
	        $data['scedQty']     = number_format($scedQty);
	        $data['scinqQty']    = number_format($scinqQty);
	        
	        $data['bledQty']    =number_format($bledQty);
	        $data['bledCount']  = $bledCount;
            
            $m++;
        }while(count($listdatas)<=0 && $m<2);
        
        $data['list'] = $listdatas;
        $this->load->view('zztv_sctasks',$data);
  }  
   //车间生产待入库   
   public function storage()
   {

        $params = $this->input->get();
	    
	    $factoryCheck = $this->config->item('factory_check');
        $WorkShopId = element('wsId',$params,'-1');
        
	    $this->load->model('WorkShopdataModel'); 
	    $this->load->model('ScSheetModel'); 
        $this->load->model('ProductdataModel');
	    $this->load->model('ScCjtjModel');
	    $this->load->model('ScCurrentMissionModel');
	    
	    $this->load->library('dateHandler');
	    
	    $sPOrderIds =$this->ScCurrentMissionModel->get_all_records();
	    
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($WorkShopId);
	    
        $rowArray =$this->ScSheetModel->get_stockin_list($WorkShopId,$actionid);
         
        $rownums =count($rowArray);
        
        $curWeeks = $this->ThisWeek;
        $list = array();
        $scingQty = 0;
        $scingCount = 0;
        
        $scedQty = 0;
        $scedCount = 0;
        
        $min30 = 60 *30;
	    $hour12 = $min30*2 *4*3;
	    $twoDay = $hour12 * 4;
	
        $nowTime =strtotime(date("Y-m-d H:i:s"));
        
        $rownums =count($rowArray);
	    $listdatas=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
	        
	        $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
	        
	        $scOk = ($rows['scQty']>=$rows['Qty']) ? true : false;
	        if ($scOk == false) {
	            $minutes=floor((strtotime($nowTime)-strtotime($rows['created']))/60); 
	            if ($minutes>30){
		            $scingQty += $rows['scQty'];
		            $scingCount ++; 
	            }
	        }
	        
	        $scedQty += $rows['scQty'];
		    $scedCount ++;
	        
	        $time = '';
	        $time0 = $rows['creates'];
	        $minusOne = $scOk ? strtotime($rows['created']) : $nowTime;
	        $minutes=floor($minusOne-strtotime($time0)); 
	        $timeColor = '#727171';
	        $flagFix = '';
	        $time = $this->datehandler->GetTimeInterval($minutes?$minutes:0);

	        if ($minutes > $twoDay)
	        {
		        $timeColor = '#ff0000';
		        $flagFix = '_red';
	        } else if ($minutes > $hour12) {
		        $timeColor = '#ff9946';
		        $flagFix = '_orange';
	        }
	        
	        if (strpos($sPOrderIds,$rows['sPOrderId'])){
	            $coding='1';
            }
            else{
	            $coding='0'; 
            }

	        $listdatas[]=array(
			        'imgUrl'     =>"$productImg",
			        'Forshort'   =>''.$rows['Forshort'],
			        'cName'      =>''.$rows['cName'],
			        'Line'       =>''.$rows['Line'],
			        'ShipType'   =>''.$rows['ShipType'],
			        'isCoding'   =>"$coding",
			        'week'       =>''.substr($rows['LeadWeek'].'', 4,2),
				    'isOver'     =>$rows['LeadWeek']<$curWeeks ? '1' : '0',
			        'scQty'      =>''.number_format($rows['scQty']),
			        'Qty'        =>''.number_format($rows['Qty']),
			        'time'       =>"$time",
			        'timeColor'  =>"$timeColor",
			        'flagFix'    =>"$flagFix",
			        'scok'       =>$scOk
		        );
        }

        $todayQty  = $this->ScCjtjModel->get_day_scqty($WorkShopId);
        $data['todayQty']   = number_format($todayQty);
        
        $data['scingQty']   = number_format($scingQty);
        $data['scingCount'] = $scingCount;
        $data['scedQty']    = number_format($scedQty);
        $data['scedCount']  = $scedCount;
        
        $data['list']       = $listdatas;
        
        $this->load->view('zztv_storage',$data);
    }
    
   function totals() {
		
		$listdatas  =array();
		$data['animate']=0;
		
		 $params = $this->input->get();
        $WorkShopId = element('wsId',$params,'101');
        
        $this->load->model('WorkShopdataModel');
        $this->load->model('CheckinoutModel');
        $this->load->model('ProductdataModel');
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
	       
	        $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'images/noImage.png';
	        

	        $times = $this->datehandler->GetSecTimeOutString($rows['created'],'',0);
			   
		    if ($i==0){
	               $seconds=floor((strtotime(date("Y-m-d H:i:s"))-strtotime($rows['created']))); 
		           $data['animate']=$seconds<20?1:0;
	         }
	        
	        $Amount = $rows['Qty'] * $rows['Price'];
	        $listdatas[]=array(
			        'imgUrl'     =>"$productImg",
			        'times'      =>$times,
			        'cName'      =>''.$rows['cName'],
			        'Line'      =>''.$rows['Line'],
			        'Forshort'      =>''.$rows['Forshort'],
			        'week'       =>''.substr($rows['LeadWeek'].'', 4,2),
			        'isOver'     =>$rows['LeadWeek']<$this->ThisWeek ? '1' : '0',
			        'Qty'        =>''.number_format($rows['Qty']),
			        'Price'      =>'¥'.number_format($rows['Price'],2),
			        'Amount'     =>'¥'.number_format($Amount,1)
		        );
        }
        
        $data['lastId']  = $lastId;
        $data['list']  = $listdatas;
		$this->load->view('zztv_totals',$data);
	}
	
	
	function ajax() {
		
		$this->load->library('dateHandler');
		$this->load->model('WorkShopdataModel');
		$this->load->model('staffMainModel');
		$this->load->model('ScCjtjModel');
		$rows=$this->WorkShopdataModel->get_records(101);
		$groups  =$rows['GroupId'];
		//当前车间生产人数
		
		//当天产值
		$groupnums=$groups==''?0:$this->staffMainModel->date_checkInNums_ingroup($groups,$this->Date);
        $timedata = $this->datehandler->get_worktimes();
        
		$lastId = 0;
		$rowArray = $this->ScCjtjModel->get_workshop_screcords(101,$this->Date,6);
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
	
	    
     //生产暂停的任务
    public function suspend(){
	    
	  
	    $params = $this->input->get();
	    
	    $factoryCheck = $this->config->item('factory_check');
        $WorkShopId = element('wsId',$params,'101');
        
	    $this->load->model('WorkShopdataModel'); 
	    $this->load->model('ScSheetModel');
	    $this->load->model('ScgxRemarkModel'); 
	    $this->load->model('staffMainModel'); 
	    
        $this->load->model('ProductdataModel');
	    $this->load->model('ScCjtjModel');
	    $this->load->model('ScCurrentMissionModel');
	    
	    $this->load->library('dateHandler');
	    
	    $thisMon = date('Y-m');
	    
	    
	    $qtyarr = $this->ScCjtjModel->mon_workshop_abnormals($WorkShopId,$thisMon); 
		    
	    $qty1 = $qtyarr['qty1'];
	    $qty2 = $qtyarr['qty2'];
	    $qty3 = $qtyarr['qty3'];
	    
	    $data['abnormal'] = $qty3;
	    $allqty = $qty1 + $qty2 + $qty3;
	    $data['allqty']   = $allqty;
	    $data['grayIndex'] = $allqty > 0? $qty1*50/$allqty : 50;
	    $data['orangeIndex'] = $allqty > 0? $qty2*50/$allqty : 0;
	    $data['redIndex'] = $allqty > 0? $qty3*50/$allqty : 0;
	    
	    $iter = 0;
	    $allred = 0;
	    for ($i = 0; $i < 3; $i ++) {
		    $valM = $i+1;
		    $month = date('Y-m', strtotime("- $valM month",strtotime($thisMon.'-01 06:00:00')));
		    $qtyarr = $this->ScCjtjModel->mon_workshop_abnormals($WorkShopId,$month); 
		    
		    $qty1 = $qtyarr['qty1'];
		    $qty2 = $qtyarr['qty2'];
		    $qty3 = $qtyarr['qty3'];
		    $allqty = $qty1 + $qty2 + $qty3;
		    $allred += ($allqty>0?($qty3/$allqty):0);
		    $iter ++;
	    }
	    if ($iter > 0) {
		    $allred = $allred / $iter;
	    }
	     $data['lastRedIndex'] = $allred;
	    
	    $sPOrderIds =$this->ScCurrentMissionModel->get_all_records();
	    
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($WorkShopId);
	    
        $rowArray =$this->ScSheetModel->get_stockin_list($WorkShopId,'suspend');
         
        $rownums =count($rowArray);
        
        $curWeeks = $this->ThisWeek;
        $list = array();
        $scingQty = 0;
        $scingCount = 0;
        
        $scedQty = 0;
        $scedCount = 0;
        
        $min30 = 60 *30;
	    $hour12 = $min30*2 *4*3;
	    $twoDay = $hour12 * 4;
	
        $nowTime =strtotime(date("Y-m-d H:i:s"));
        
        $rownums =count($rowArray);
	    $listdatas=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
	        
	        $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
	        
	        $scOk = ($rows['scQty']>=$rows['Qty']) ? true : false;
	        if ($scOk == false) {
	            $minutes=floor((strtotime($nowTime)-strtotime($rows['created']))/60); 
	            if ($minutes>30){
		            $scingQty += $rows['scQty'];
		            $scingCount ++; 
	            }
	        }
	        
	        $scedQty += $rows['scQty'];
		    $scedCount ++;
	        
	        $time = '';
	        $time0 = $rows['created'];
	        $minusOne =  $nowTime;
	        $minutes=floor($minusOne-strtotime($time0)); 
	        $timeColor = '#727171';
	        $flagFix = '';
	        if ($min30 <= $minutes) {
		        
	        } else {
		        continue;
	        }
	        $minutes=floor($minusOne-strtotime($rows['creates'])); 
	        $time = $this->datehandler->GetTimeInterval($minutes?$minutes:0);

	        if ($minutes > $twoDay)
	        {
		        $timeColor = '#ff0000';
		        $flagFix = '_red';
	        } else if ($minutes > $hour12) {
		        $timeColor = '#ff9946';
		        $flagFix = '_orange';
// 		        continue;
	        } else {
// 		        continue;
	        }
	        
	        if (strpos($sPOrderIds,$rows['sPOrderId'])){
	            $coding='1';
            }
            else{
	            $coding='0'; 
            }
            
         
            $remark = $remarker = '';
            $remarkquery = 
        	$this->ScgxRemarkModel->get_remark(element('sPOrderId',$rows,'0'));
            if ($remarkquery->num_rows() > 0) {
	            $remarkRow = $remarkquery->row_array();
	            $remark = $remarkRow['Remark'];
	            $modified =  $remarkRow['created'];
	            $modifier =  $remarkRow['creator'];
	            $operator=$this->staffMainModel->get_staffname($modifier);   
				$times =  $this->datehandler->GetDateTimeOutString($modified,$this->DateTime);
				$remarker = $times.' '.$operator;
            }


	        $listdatas[]=array(
			        'imgUrl'     =>"$productImg",
			        'Forshort'   =>''.$rows['Forshort'],
			        'cName'      =>''.$rows['cName'],
			        'Line'       =>''.$rows['Line'],
			        'ShipType'   =>''.$rows['ShipType'],
			        'isCoding'   =>"$coding",
			        'week'       =>''.substr($rows['LeadWeek'].'', 4,2),
				    'isOver'     =>$rows['LeadWeek']<$curWeeks ? '1' : '0',
			        'scQty'      =>''.number_format($rows['scQty']),
			        'Qty'        =>''.number_format($rows['Qty']),
			        'time'       =>"$time",
			        'timeColor'  =>"$timeColor",
			        'flagFix'    =>"$flagFix",
			        'scok'       =>$scOk,
			        'sctime'     =>$minutes,
			        'remark'     =>$remark,
			        'remarker'   =>$remarker
		        );
        }
        
        	usort($listdatas, function($a, $b) {
	            $al = ($a['sctime']);
	            $bl = ($b['sctime']);
	            if ($al == $bl)
	                return 0;
	            return ($al > $bl) ? -1 : 1;
	        });
	        
	        
        
       $data['list']  =$listdatas;
// 	   echo(124);
	   $this->load->view('zztv_suspend',$data);
    }

    

    function _get_arr($arr,$ind)
    {
         $val = !empty($arr[$ind]) ? $arr[$ind] : null;
         return $val;
    }
}