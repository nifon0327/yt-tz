<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sctv extends MC_Controller {

    //未生产订单
    public function order()
    {
	        $data=array("test"=>"");
	        $params = $this->input->get();
	        $oneTypeId = element('wsId',$params,'-1');
	        $part = element('part',$params,'head');
		   
		    $this->load->model('WorkShopdataModel'); 
		    $this->load->model('ScSheetModel'); 
		    $this->load->model('staffMainModel'); 
		    $this->load->model('ProcessSheetModel');
		    $this->load->model('ScGxtjModel');
		    $this->load->model('ScCjtjModel');
		    $this->load->model('StuffdataModel');
		    
		    $this->load->library('dateHandler');
		    
		    $oneTypes = $this->WorkShopdataModel->get_records($oneTypeId);
			$groupnums=$oneTypes['GroupId']==''?0:$this->staffMainModel->get_checkInNums_ingroup($oneTypes['GroupId']);
			   
			$LeaderNumber = $oneTypes['LeaderNumber'];

			$avgoutput  = $this->ScCjtjModel->get_day_average($oneTypes['GroupId']);

			
			/*
			switch($oneTypeId) {
				case '102': $LeaderNumber=10641;break;
				case '103': $LeaderNumber=10756;break;
			}
			*/
			
            $photoPath = $this->staffMainModel->get_photo($LeaderNumber);
			   
	        $data['personImg']  = 'http://' . $_SERVER['HTTP_HOST'] . '/'.$photoPath;
	        $data['shopTitle']  = ''.$oneTypes['Name'];
	        
	        $list = array();
			$query = $this->ScSheetModel->getyll_list($oneTypeId);
	        $rows=$query->result_array();
			$counts=count($rows);
			//echo $counts.'<br>';
			
			$overCount = 0;
			$overSum = 0;
			$thisCount = 0;
			$thisSum = 0;
			$nxtCount = 0;
			$nxtSum = 0;
			$allMount = 0;

			$bgindex =  0;
	        if ($counts > 0) {
		        $plus = 0;
		        
		        $curWeeks = $this->ThisWeek;
		        for($i = $bgindex; $i < $counts; $i++) {
		        	$oneRow = $rows[$i];
		        	$processArray=array();

				    $stockId=$oneRow['StockId'];  
				    $sPOrderId = $oneRow['sPOrderId'];
				    $gxQty = $this->ScGxtjModel->checkAllGxQty($sPOrderId);
				    if ($gxQty > 0) {
					    continue;
				    } 
				    $processArray=$this->ProcessSheetModel->get_gxTypes($stockId,$sPOrderId);
					$week = $oneRow['DeliveryWeek'];
					$oneQty = $oneRow['Qty'];
					if ($week > $curWeeks) {
						$nxtSum +=$oneQty;
						$nxtCount ++;
					} else if ($week == $curWeeks) {
						$thisSum +=$oneQty;
						$thisCount ++;
					} else {
						$overSum+=$oneQty;
						$overCount ++;
					}
					$allMount += $oneQty*$oneRow['Price'];
					
			        $stuffid=$oneRow['StuffId'];
			        $imgUrl = $this->StuffdataModel->get_stuff_icon($stuffid);
			        if ($oneRow['Picture'] < 1) {
				       $imgUrl = 'images/noImage.png'; 
			        }
			        $dateOne = $oneRow['created'];
					$created_ct = $this->datehandler->GetDateTimeOutString($dateOne,"");
			    
			    $plus ++;
			    
					if ($part == 'body' ) {
						if ($plus<=12)
						continue;
					} else {
						if ($plus>12)
						continue;
					}
			        $list[]=array(
			        	'week'   =>''.substr($week.'', 4,2),
			        	'isOver' => $week<$curWeeks ? '1' : '0',
			        	'cName'  =>''.$oneRow['StuffCname'],
			        	'imgUrl' =>''.$imgUrl,
			        	'qty'    =>''.number_format($oneRow['Qty']),
			        	'time'   =>$created_ct,
			        	'process'=>$processArray
		        );
					
					
		        }
	     }
	     $taskdays   = $avgoutput>0 ? round($allMount/$avgoutput) : '0';
	       $data['overQty']    = ''.number_format($overSum);
	        $data['allQty']     = ''.number_format($overSum+ $thisSum + $nxtSum);
	        
	        $data['overCount']  = ''.$overCount;
	        $data['shopId']  = ''.$oneTypeId;
	         $data['personInfo'] = ''.$groupnums.'人｜' . $taskdays .'天';
	        $data['curQty']     = ''.number_format($thisSum);
	        $data['curCount']   = ''.$thisCount;
	        
	        $data['weekQty']    = ''.number_format($nxtSum);
	        $data['weekCount']  = ''.$nxtCount;
	        $data['part'] = ''.$part;
	     
	     $data['list'] = $list;
         $this->load->view('sctv_order',$data);
   }

    //车间生产工序任务
    public function gxtasks()
    {
        $data   = array("test"=>"");
        $params = $this->input->get();
        
        $oneTypeId = element('wsId',$params,'-1');
        $gxId      = element('gxId',$params,'1');
        $processId  = element('processId',$params,'6010');
        
	    $this->load->model('WorkShopdataModel'); 
	    $this->load->model('ScSheetModel'); 
	    $this->load->model('staffMainModel'); 
	    $this->load->model('ProcessSheetModel');
	    $this->load->model('ScGxtjModel');
	    $this->load->model('StuffdataModel');
	    $this->load->model('ScCjtjModel');
	    
	    $this->load->library('dateHandler');
	    
	    $numsDict = array(
				'102'=>array('4','11896','10793','11671','10655'),
				'103'=>array('3','10756','10656','11981','10093')
				);
				
		if ($gxId>0){
			 $numbers = $this->_get_arr($numsDict,"$oneTypeId");
			 $LeaderNumber = $numbers[intval($gxId)];
		}else{
			 switch($gxId){
			      case "A": 
			      case "B": $LeaderNumber = 11896; break;
			      case "C": $LeaderNumber = 11969; break;
			 }
		}
		
        $oneTypes = $this->WorkShopdataModel->get_records($oneTypeId);
		$groupnums=$oneTypes['GroupId']==''?0:$this->staffMainModel->get_checkInNums_ingroup($oneTypes['GroupId']);
		   
		$workshopid = $oneTypes['Id'];
		$curWeeks = $this->ThisWeek;
		//$dayqty   =$this->ScCjtjModel->get_day_scqty($workshopid);
		//$overqtyRow = $this->ScSheetModel->get_semi_bledqtyweb($workshopid,'over');
		//$curqtyRow = $this->ScSheetModel->get_semi_bledqtyweb($workshopid,$curWeeks);
		//$nxtqtyRow = $this->ScSheetModel->get_semi_bledqtyweb($workshopid,'over+');
		
		$unqtyRow   = $this->ScSheetModel->get_semi_bledqtyweb($oneTypes['Id'],'');
		
		$avgoutput  = $this->ScCjtjModel->get_day_average($oneTypes['GroupId']);
		$unoutput   = $unqtyRow->amount;
		$taskdays   = $avgoutput>0 ? round($unoutput/$avgoutput) : '0';
			
		//$allQtySum = $overqtyRow->qty + $curqtyRow->qty + $nxtqtyRow->qty;
		/*	
		$allDsc = $this->ProcessSheetModel->getAllDscQty($workshopid);
		$dscRow1 = $this->_get_arr($allDsc,$gxId);
		
		
		$allDscOver = $this->ProcessSheetModel->getAllDscQtyWeek($workshopid,'over');
		$dscRow1Over = $this->_get_arr($allDscOver,$gxId);
		
		$allDscthis = $this->ProcessSheetModel->getAllDscQtyWeek($workshopid,'this');
		$dscRow1this = $this->_get_arr($allDscthis,$gxId);
		
		$allDscthisP = $this->ProcessSheetModel->getAllDscQtyWeek($workshopid,'this+');
		$dscRow1thisP = $this->_get_arr($allDscthisP,$gxId);
		*/
		$dscRows= $this->ProcessSheetModel->get_dsc_gxqty($workshopid);
		if (isset( $dscRows["$gxId"])){
			$dscRow = $dscRows["$gxId"];
		}else{
			$dscRow = $this->ProcessSheetModel->set_gxarray_zerovalue();
		}
		
		$scQty1 = $this->ProcessSheetModel->getGxYscQty($workshopid,$gxId);
		
		$photoPath = $this->staffMainModel->get_photo($LeaderNumber);
		
        $data['personImg']  = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $photoPath;
        $data['shopTitle']  = ''.$oneTypes['Name'];
        $data['titleImg']  = 'round' . $gxId . '.png';
        $data['personInfo'] = ''.$groupnums.'人｜' . $taskdays .'天';
        
         $data['allQty']     = ''.number_format($dscRow['qty']);
         
        $data['overQty']    = ''.number_format($dscRow['overqty']);
        $data['overCount']  = ''.$dscRow['overcts'];
        
        $data['curQty']     = ''.number_format($dscRow['thisqty']);
        $data['curCount']   = ''.$dscRow['thiscts'];
        
        $data['weekQty']    = ''.number_format($dscRow['nextqty']);
        $data['weekCount']  = ''.$dscRow['nextcts'];
       
        //$data['allQty']     = ''.number_format($dscRow1['qty']);
        //$data['overQty']    = ''.number_format($dscRow1Over['qty']);
        //$data['allQty']     = ''.number_format($dscRow1['qty']);
        
        $data['dayQty']    = ''.number_format($scQty1);
        
       // $data['overCount']  = ''.$dscRow1Over['cts'];
        $data['shopId']  = ''.$oneTypeId;
        
        //$data['curQty']     = ''.number_format($dscRow1this['qty']);
        //$data['curCount']   = ''.$dscRow1this['cts'];
        
        //$data['weekQty']    = ''.number_format($dscRow1thisP['qty']);
        //$data['weekCount']  = ''.$dscRow1thisP['cts'];
        
        $data['gxDisplay'] = $gxId;
		$actionid = $oneTypes['ActionId'];
		
		$bledSign = $gxId <=1 ? 0 : 20;
		
		$rowArray=$this->ScSheetModel->get_semi_bledsheet($workshopid,$actionid,$bledSign);//当前生产
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$black      =$this->colors->get_color('black');
	    $orange     =$this->colors->get_color('orange');
	    
		$lastTimeArray=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $processArray=array();
            
            $breakSign=0;
            $foundSign=0;
            $preqty=0;
            $postion = -1;
            $firstSign=1;
		    if ($actionid==102){//皮套加工
			   $stockId=$rows['StockId'];  
			   $this->load->model('ProcessSheetModel');
			   $processArray=$this->ProcessSheetModel->get_sc_processlist2($stockId,$rows['sPOrderId']);
			   
			   foreach($processArray as $processRows){
				   if ($processRows['TypeId']==$gxId){
					   if ($processRows['DjQty']>=$processRows['Qty']){
					       $breakSign=1;
						   break;
					   }
					   $foundSign=1;
					   
					   if ($processRows['DjQty']>0 && strlen($processRows['LastTime'])>5){
					      $counts=count($lastTimeArray);
					      if ($counts == 0){
						     $lastTimeArray[]=$processRows['LastTime'];
						     $postion = 0;
					      }
					      else{
						      for($n=0;$n<$counts; $n++){
						          if (strtotime($processRows['LastTime'])>strtotime($lastTimeArray[$n])){
						             $postion = $n;
						             array_splice($lastTimeArray, $postion, 0, $processRows['LastTime']);
						             break;
						          }
					          }
					          $postion =$counts;
					      } 
					   }
					   break;
				   }
				   else{
					   $preqty=$processRows['GxQty'];
					   $firstSign++;
				   }
			   }
			   
		    }else{
		    
		    }
		   if ($breakSign==1 || $foundSign==0) continue; 
		    //if ($breakSign==1 || ($foundSign==0 && $gxId>1)) continue;
		    if ($preqty==0 && $gxId>1 && $firstSign!=1) continue;
		    
		    $stuffid=$rows['StuffId'];
	        $imgUrl = $this->StuffdataModel->get_stuff_icon($stuffid);
			        
    if ($rows['Picture'] < 1) {
				       $imgUrl = 'images/noImage.png'; 
			        }

		    $nameColor=$rows['Picture']==1?$orange:$black;
		    $week = $rows['DeliveryWeek'];
		     
			$dateOne = $rows['created'];
			$created_ct = $this->datehandler->GetDateTimeOutString($dateOne,"");
			    
		    
			$listdata=array(
			    'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'week'       =>''.substr($week.'', 4,2),
			    'isOver'     => $week<$curWeeks ? '1' : '0',
				'cName'      =>$rows['StuffCname'],
				'nameColor'  =>"$nameColor",
				'time'       =>"$created_ct",
				'imgUrl'     =>''.$imgUrl,
				'scqty'      =>round($rows['ScQty']) ,
				'qty'        =>round($rows['Qty']),
				'process'    =>$processArray
			);
			
			if ($postion>=0){
				 array_splice($dataArray, $postion, 0, 0);
				 $dataArray[$postion]=$listdata;
				 
			}else{
				 $dataArray[]=$listdata;
			}
		}
		 
         $data['list'] = $dataArray;
        $this->load->view('sctv_gxtasks',$data);
     }
    
    //车间生产统计  
    public function totals()
    {
		$data=array("test"=>"");
		$params = $this->input->get();
		
		$factoryCheck = $this->config->item('factory_check');
		$laborCost    = $this->config->item('standard_labor_cost');
		$worktime     = $this->config->item('standard_work_hour');
		
		$oneTypeId = element('wsId',$params,'-1');
		$this->load->model('WorkShopdataModel'); 
		$this->load->model('ScSheetModel'); 
		$this->load->model('staffMainModel'); 
		$this->load->model('ProcessSheetModel');
		$this->load->model('ScGxtjModel');
		$this->load->model('StuffdataModel');
		$this->load->model('ScCjtjModel');
		
		$this->load->library('dateHandler');
		
		$worktimeArr = $this->datehandler->get_worktimes();
		$workHour = $worktimeArr[1] + $worktimeArr[2]/60;
		
		$oneTypes = $this->WorkShopdataModel->get_records($oneTypeId);
		$groupnums=$oneTypes['GroupId']==''?0:$this->staffMainModel->get_checkInNums_ingroup($oneTypes['GroupId']);
		   
		$LeaderNumber = $oneTypes['LeaderNumber'];
		$workshopid = $oneTypes['Id'];
		$curWeeks = $this->ThisWeek;
		
		  
		$day_output   =$this->ScCjtjModel->get_workshop_day_output($oneTypes['Id']);
		$month_output =$this->ScCjtjModel->get_workshop_month_output($oneTypes['Id']);
		
		$day_valuation=$groupnums*$laborCost*$worktime;
		$month_valuation=$day_valuation*25;//需更改
		$timeRealPer = $workHour /$worktime*50;
		$workReal = $workHour*$groupnums*$laborCost;
		$data['blueIndex'] = $workHour /$worktime;
		$data['redIndex'] = $day_output/($day_valuation>0?$day_valuation:1);
		
		$fix = ($day_output/($day_valuation>0?$day_valuation:1)*50-$timeRealPer);
		$timeRealPer = $timeRealPer > 0 ? $timeRealPer :($day_valuation>0?$day_valuation:1); 
		
		$redIndexShow = '';
		if ($workReal > 0) {
			
			if ($day_output > $workReal) {
				$redIndexShow =round( ($day_output - $workReal)/$workReal*100);
			} else {
				$redIndexShow =round( ($workReal-$day_output)/$workReal*100);
			}
		} else {
			$redIndexShow = $day_output > 0 ? '100':'0';
		}
// 		$data['redIndexShow'] = abs(round($day_output>=$day_valuation && $day_valuation>0? ($fix/$timeRealPer*100)-100:($fix/$timeRealPer*100)));
		$data['redIndexShow'] =$redIndexShow;
		
		$data['redColor'] =$day_output>0 && $day_output>=$workReal?'#01be56':'#fd0300';
		$data['day_color']=$day_output>0 && $day_output>=$workReal?'fontGreen30':'fontRed30';
		
		$dayScQty = $this->ScCjtjModel->get_day_scqty($workshopid,'');
		$monthQty = $this->ScCjtjModel->get_month_scqty($workshopid,'');
		
		$data['monSc'] = number_format($monthQty);
		$data['daySc'] = number_format($dayScQty);
		
		$data['monScRMB'] = '¥'.number_format($month_output);
		$data['dayScRMB'] = '¥'.number_format($day_output);
		$data['monGjRMB'] = '¥'.number_format($month_valuation);
		$data['dayGjRMB'] = '¥'.number_format($day_valuation);

		$len = 7;
		$dateList = array();
		
		$headArr = array();
		$nowdate = date('Y-m-d');
		$nowdateTime = strtotime($nowdate);
		
		$allAm = array();
		$allPm = array();
		$allEm = array();
		
		$allSm = array();
		
		$ValAm = array();
		$ValPm = array();
		$ValEm = array();
		
		$ValSm = array();
		
		$m = $factoryCheck==1?0:1;
		for ($i=($len-$m); $i >=1 ;$i--){
			$hasS = $i==1 ? "":"s";
			$strtime = strtotime("-$i day$hasS",$nowdateTime);
			$dateOne = date("Y-m-d",$strtime);
			if ($factoryCheck==1 && date('w',$strtime)==0) {
			   continue;
			}
			$dateList[]=$dateOne;
			
			$week06 = date('w',$strtime);
			$headArr[]= array(
				'title'=>date("m-d",$strtime),
				'color'=>($week06==0 || $week06==6)?'#ff0000':'#727171'
			);
			
			$oneAm = $this->ScCjtjModel->get_am_scqty($workshopid,$dateOne);
			$onePm = $this->ScCjtjModel->get_pm_scqty($workshopid,$dateOne);
			$oneEm = $this->ScCjtjModel->get_eve_scqty($workshopid,$dateOne);
			$allAPE = $oneAm + $onePm+$oneEm;
			$allSm[]=$allAPE > 0 ?$allAPE : 0;
			$allAm[]=$oneAm>0?$oneAm:0;
			$allPm[]=$onePm>0?$onePm:0;
			$allEm[]=$oneEm>0?$oneEm:0;
			
			
			$ValAm[]=$day_valuation*2/5;
			$ValPm[]=$day_valuation*2/5;
			$ValEm[]=$day_valuation*1/5;
			
			$ValSm[]=$day_valuation;
		
		}
		$dateList[]=date('Y-m-d');
// 		$headArr[]=date("m-d");
	$week06 = date('w');
			$headArr[]= array(
				'title'=>date("m-d"),
				'color'=>($week06==0 || $week06==6)?'#ff0000':'#727171'
			);
			
		
		
		$dateOne =$nowdate;
		$oneAm = $this->ScCjtjModel->get_am_scqty($workshopid,$dateOne);
		$onePm = $this->ScCjtjModel->get_pm_scqty($workshopid,$dateOne);
		$oneEm = $this->ScCjtjModel->get_eve_scqty($workshopid,$dateOne);
		$allAPE = $oneAm + $onePm+$oneEm;
		$allSm[]=$allAPE > 0 ?$allAPE : 0;
		$allAm[]=$oneAm>0?$oneAm:0;
		$allPm[]=$onePm>0?$onePm:0;
		$allEm[]=$oneEm>0?$oneEm:0;
		
		
		$ValAm[]=$day_valuation*2/5;
		$ValPm[]=$day_valuation*2/5;
		$ValEm[]=$day_valuation*1/5;
		
		$ValSm[]=$day_valuation;
		$max=$day_valuation;
		for($i=0; $i<7; $i ++ ) {
			if ($allSm[$i]>$max) {
				$max = $allSm[$i];
			}
		}
		
		$maxIndex = 1.1;
		$ValAm[]=$max*2/5*$maxIndex;
		$ValPm[]=$max*2/5*$maxIndex;
		$ValEm[]=$max*1/5*$maxIndex;
		
		$ValSm[]=$max*$maxIndex;
		$allAm[]=$max*2/5*$maxIndex;
		$allPm[]=$max*2/5*$maxIndex;
		$allEm[]=$max*1/5*$maxIndex;
		
		$allSm[]=$max*$maxIndex;
		  
		//10656
		$data['headArr']=$headArr;
		$data['allAm']=implode(',', $allAm);
		$data['allPm']=implode(',', $allPm);
		$data['allEm']=implode(',', $allEm);
		
		$data['allSm']=implode(',', $allSm);
		
		$data['valAm']=implode(',', $ValAm);
		$data['valPm']=implode(',', $ValPm);
		$data['valEm']=implode(',', $ValEm);
		
		$data['valSm']=implode(',', $ValSm);
		
			
		$allgx= array();
		//$allLeft = $this->ProcessSheetModel->getAllLeftQty($workshopid);
		$numsDict = array(
				'102'=>array('4','11896','10793','11671','10655'),
				'103'=>array('3','10656','11981','10093','10093')
				);
		$numbers = $this->_get_arr($numsDict,"$workshopid");
		$gxtypes = $numbers?$numbers[0] :0;
		if ($gxtypes > 0) {
			
			$allLeft = $this->ProcessSheetModel->getAllLeftQty($workshopid);
			$allDsc = $this->ProcessSheetModel->getAllDscQty($workshopid);

			for ($i=1;$i<$gxtypes+1;$i++) {
				$data1 = array('type'=>$i);
				$LeaderNumber = $numbers[$i];
				$scQty1 = $this->ProcessSheetModel->getGxYscQty($workshopid,$i);
				//$dscQty1 = $this->ProcessSheetModel->getGxDscQty($workshopid,$i);
				$leftRow1 = $allLeft[$i];
				$dscRow1 = $allDsc[$i];
				$data1['sc1'] = number_format($scQty1);
				$data1['dsc1'] = number_format($dscRow1['qty']);
				$data1['left1'] = number_format($leftRow1['qty']);
				$data1['leftCt1'] = number_format($leftRow1['cts']);
				$data1['name'] = $this->staffMainModel->get_staffname($LeaderNumber);
				$photoPath = $this->staffMainModel->get_photo($LeaderNumber);
				$data1['personImg']  = 'http://' . $_SERVER['HTTP_HOST'] . '/' .$photoPath;
				$allgx[]=$data1;
			}		
		}
		
		$data['list'] = $allgx;
		
		$this->load->view('sctv_totals',$data);
	}
   
   //车间生产待出    
   public function shipment()
   {
        $data=array("test"=>"");
        $data=array("test"=>"");
        $params = $this->input->get();
	    
	    $factoryCheck = $this->config->item('factory_check');
	    
        $oneTypeId = element('wsId',$params,'-1');
	    $this->load->model('WorkShopdataModel'); 
	    $this->load->model('ScSheetModel'); 
	    $this->load->model('StuffdataModel');
	    $this->load->model('CkrksheetModel');
	    $this->load->model('GysshsheetModel');
	    
	    
	    
	    $this->load->library('dateHandler');
	    
	    $oneTypes = $this->WorkShopdataModel->get_records($oneTypeId);
		$workshopid = $oneTypes['Id'];
		$curWeeks = $this->ThisWeek;
        
        $query = $this->ScSheetModel->get_scorder_nosendweb($workshopid);
        $list = array();
        $scingQty = 0;
        $scingCount = 0;
        $scedQty = 0;
        $scedCount = 0;
        
    $min30 = 60 *30;
	$hour12 = $min30*2 *4*3;
	$twoDay = $hour12 * 4;
	
        $nowTime =strtotime(date("Y-m-d H:i:s"));
        if ($query->num_rows() > 0) {
	        $rows = $query->result_array();
	        foreach ($rows as $row) {
		        $stuffid=$row['StuffId'];
		        $imgUrl = $this->StuffdataModel->get_stuff_icon($stuffid);
		     
				$scOk = ($row['ScFrom']==0 || $row['ScQty']>=$row['Qty']) ? true : false;
		        if ($scOk == false) {
			        $scingQty += $row['ScQty'];
			        $scingCount ++;
		        } else {
			        $scedQty += $row['ScQty'];
			        $scedCount ++;
		        }

		        $time = '';
		        $time0 = $row['firsttime'];
		        $minusOne = $scOk ? strtotime($row['lasttime']) : $nowTime;
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
		        $week = $row['DeliveryWeek'];
		        
		        		        
				$time = $factoryCheck==1?'':'...'.$time;
		        $list[]=array(
			        'imgUrl'     =>"$imgUrl",
			        'cName'      =>''.$row['StuffCname'],
			        'week'       =>''.substr($week.'', 4,2),
				    'isOver'     =>$week<$curWeeks ? '1' : '0',
			        'scQty'      =>''.number_format($row['ScQty']),
			        'Qty'        =>''.number_format($row['Qty']),
			        'time'       =>"$time",
			        'timeColor'  =>"$timeColor",
			        'flagFix'    =>"$flagFix",
			        'scok'       =>$scOk
		        );
		        
	        }
        }
        $allNotOut = $scingQty + $scedQty;
       // $rkall = $this->CkrksheetModel->get_workshop_rkqty($workshopid);
        $shall = $this->GysshsheetModel->get_workshop_shqty($workshopid);
          
        $data['waitQty']    = number_format($allNotOut);
        $data['sendQty']    = number_format($shall);
        
        $data['scingQty']   = number_format($scingQty);
        $data['scingCount'] = $scingCount;
        $data['scedQty']    = number_format($scedQty);
        $data['scedCount']  = $scedCount;
        
        $data['list']       = $list;
        
        $this->load->view('sctv_shipment',$data);
    }
    

    function _get_arr($arr,$ind)
    {
         $val = !empty($arr[$ind]) ? $arr[$ind] : null;
         return $val;
    }
}