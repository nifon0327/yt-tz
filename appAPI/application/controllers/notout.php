<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notout extends MC_Controller {
	
	function __construct()
    {
        parent::__construct();
        
           
        $this->color_red = $this->colors->get_color('red');
        $this->color_superdark = $this->colors->get_color('superdark');
        $this->color_grayfont = $this->colors->get_color('grayfont');
        $this->color_weekredborder = $this->colors->get_color('weekredborder');
		$this->color_weekred = $this->colors->get_color('weekred');
		$this->color_daybordergray = $this->colors->get_color('daybordergray');
		$this->color_daygray = $this->colors->get_color('daygray');
		$this->color_todayyellow = $this->colors->get_color('todayyellow');
		$this->color_lightgreen = $this->colors->get_color('lightgreen');
		$this->color_bluefont = $this->colors->get_color('bluefont');

        
    }
    
    
    function get_unit_qtyinfo($val) {
	    $unit = '';
	    if ($val > 1000000) {
		    $val = round($val / 1000000);
		    $unit = 'M';
	    } else if ($val > 10000) {
		    $val = round($val / 10000);
		    $unit = 'w';
	    } else if ($val > 1000) {
		    $val = round($val / 1000);
		    $unit = 'k';
	    }
	    return array('val'=>$val, 'unit'=>$unit);
    }
    
    function headInfo() {
	    
	    
	    $this->load->model('YwOrderSheetModel');
	    $waitcpSumRow = $this->YwOrderSheetModel->get_waitcp_sum();
	    $notoutSumRow = $this->YwOrderSheetModel->get_notout_sum();
	    $notoutOverSumRow = $this->YwOrderSheetModel->get_notout_sum('1');
	    
	    $allAmount = 0;
	    
	    $waitcpAmount = $waitcpSumRow['Amount'];
	    $waitcpQty = $waitcpSumRow['tStockQty'];
	    $waitcpAmount10d = $waitcpSumRow['OverAmount'];
	    $waitcpQty10d = $waitcpSumRow['OverQty'];
	    
	    $waitcpAmountLeft = $waitcpAmount - $waitcpAmount10d;
	    $waitcpQtyLeft = $waitcpQty - $waitcpQty10d;
	    
	    
	    $notoutAmount = $notoutSumRow['Amount'];
	    $notoutQty = $notoutSumRow['Qty'];
	    
	    $notoutOverAmount = $notoutOverSumRow['Amount'];
	    $notoutOverQty = $notoutOverSumRow['Qty'];
	    
	    $notoutAmountLeft = $notoutAmount - $notoutOverAmount;
	    $notoutQtyLeft = $notoutQty - $notoutOverQty;
	    
	    
	    $pie = array(
		    array('value'=>''.$notoutOverAmount,'color'=>'#ffa9a5'),
		    array('value'=>''.$notoutAmountLeft,'color'=>'#e8f1f8'),
		    array('value'=>''.$waitcpAmount,'color'=>'#clear')
	    );
	    
	    $pie2 = array(
		    array('value'=>''.$waitcpAmount10d,'color'=>'#FF6B00'),
		    array('value'=>''.$waitcpAmountLeft,'color'=>'#01be56'),
		    array('value'=>''.$notoutAmount,'color'=>'#clear')
	    );
	    
	    
	    
	    
	    $allAmount  = $notoutAmount + $waitcpAmount;
	    $per1 = $per2 = $per3 = $per4 = '0'; 
	    if ($allAmount > 0) {
		    $percent = round($notoutOverAmount / $allAmount *100);
		    $per1 = array(
			    'isAttribute'=>'1',
		   		'attrDicts'=>array(
			   		array('Text'    =>"$percent",
			   			  'FontName'=>'AshCloud61',
			   			  'FontSize'=>'25'),
			   		array('Text'    =>'%',
			   			  'FontSize'=>'6')
			   	)
		    );
		    $percent = round($notoutAmountLeft / $allAmount *100);
		    $per2 = $per1;
		    $per2['attrDicts'][0]['Text'] = "$percent";
		    
		    $percent = round($waitcpAmount10d / $allAmount *100);
		    $per3 = $per1;
		    $per3['attrDicts'][0]['Text'] = "$percent";
		    
		    $percent = round($waitcpAmountLeft / $allAmount *100);
		    $per4 = $per1;
		    $per4['attrDicts'][0]['Text'] = "$percent";
	    }
	    
	    $title_0 = $this->get_unit_qtyinfo($notoutAmount);
	    $title_1 = $this->get_unit_qtyinfo($allAmount);
	    $title_2 = $this->get_unit_qtyinfo($waitcpAmount);
	    
	    $val_0 = array(
		    'isAttribute'=>'1',
	   		'attrDicts'=>array(
		   		array('Text'    =>''.$title_0['val'],
		   			  'FontName'=>'AshCloud61',
		   			  'FontSize'=>'40'),
		   		array('Text'    =>$title_0['unit'],
		   			  'FontName'=>'NotoSansHans-Light',
		   			  'FontSize'=>'10')
		   	)
	    );
	    
	    $val_1 = array(
		    'isAttribute'=>'1',
	   		'attrDicts'=>array(
		   		array('Text'    =>''.$title_1['val'],
		   			  'FontName'=>'AshCloud61',
		   			  'FontSize'=>'40'),
		   		array('Text'    =>$title_1['unit'],
		   			  'FontName'=>'NotoSansHans-Light',
		   			  'FontSize'=>'10')
		   	)
	    );
	    
	    $val_2 = array(
		    'isAttribute'=>'1',
	   		'attrDicts'=>array(
		   		array('Text'    =>''.$title_2['val'],
		   			  'FontName'=>'AshCloud61',
		   			  'FontSize'=>'40'),
		   		array('Text'    =>$title_2['unit'],
		   			  'FontName'=>'NotoSansHans-Light',
		   			  'FontSize'=>'10')
		   	)
	    );
	    
	    $titleArr = array(
		   array('title'=>'未完成','val'=>$val_0),
		   array('title'=>'未出','val'=>$val_1),
		   array('title'=>'成品','val'=>$val_2)
	    );
	    
	    $query = $this->YwOrderSheetModel->get_waitcp_companys();
	    $rightCharts = array();
	    if ($query->num_rows() > 0 && $waitcpAmount > 0) {
		    $rs = $query->result_array();
		    $colorLine= '#AACBE6';
		    foreach ($rs as $rows) {
			    $percent = $rows['Amount'] / $waitcpAmount;
			    $rightCharts[]= array(
			    	'title'=>$rows['Forshort'], 
			    	'percent'=>round($percent*100,0).'%', 
			    	'val'=>$percent, 
			    	'color'=>$colorLine
			    );
		    }
		    
	    }
	    
	    $cts = count($rightCharts);
	    if ($cts < 4) {
		    
		    for ($i = $cts; $i< 4; $i++) {
			     $rightCharts[]= array(
			    	'title'=>'--', 
			    	'percent'=>'--'
			    );
		    }
		    
	    }
	    
	    $query = $this->YwOrderSheetModel->get_overnotout_companys();
	    $leftCharts = array();
	    if ($query->num_rows() > 0 && $notoutOverAmount > 0) {
		    $rs = $query->result_array();
		    $colorLine= '#ffa9a5';
		    foreach ($rs as $rows) {
			    $percent = $rows['Amount'] / $notoutOverAmount;
			    $leftCharts[]= array(
			    	'title'=>$rows['Forshort'], 
			    	'percent'=>round($percent*100,0).'%', 
			    	'val'=>$percent, 
			    	'color'=>$colorLine
			    );
		    }
		    
	    }
	    
	    $cts = count($leftCharts);
	    if ($cts < 4) {
		    
		    for ($i = $cts; $i< 4; $i++) {
			     $leftCharts[]= array(
			    	'title'=>'--', 
			    	'percent'=>'--'
			    );
		    }
		    
	    }
	    $status = array(
		    'val1'=>number_format($notoutOverQty)."\n¥".number_format($notoutOverAmount),
		    'val2'=>number_format($notoutQtyLeft)."\n¥".number_format($notoutAmountLeft),
		    'val3'=>number_format($waitcpQty10d)."\n¥".number_format($waitcpAmount10d),
		    'val4'=>number_format($waitcpQtyLeft)."\n¥".number_format($waitcpAmountLeft),
		    'per1'=>$per1,
		    'per2'=>$per2,
		    'per3'=>$per3,
		    'per4'=>$per4,
		    'titleLeft'=>'未完成',
		    'titleRight'=>'成品',
		    'titleArr'=>$titleArr,
		    'leftImg'=>'sh_notout',
		    'leftCharts'=>$leftCharts,
		    'rightCharts'=>$rightCharts,
		    'rightImg'=>'sh_product',
		    'pie'=>$pie,
		    'pie2'=>$pie2
	    );
		
		
		
	    $data['jsondata']=array('status'=>$status,'message'=>'','totals'=>1,'rows'=>null);
	    $this->load->view('output_json',$data);
	    
    }
    
       
   
        
	
	function waitcp_companys() {
		$subList = array();
		$this->load->model('YwOrderSheetModel');
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		$query = $this->YwOrderSheetModel->get_waitcp_companys('','1');

	    if ($query->num_rows() > 0) {
		    $rs = $query->result_array();

		    foreach ($rs as $rows) {
			    $Forshort = $rows['Forshort'];
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
				
				$prechar = $rows['PreChar'];
			    $waitcpAmount = $rows['Amount'];
			    $waitcpQty = $rows['tStockQty'];
			    $waitcpCounts = $rows['Counts'];
			    $waitcpAmount10d = $rows['OverAmount'];
			    $waitcpQty10d = $rows['OverQty'];
		
			    
				$subList[]=array(
					'tag'=>'ch_total',
					'companyImg'=>$LogoPath.$Logo,
					'forshort'=>$Forshort,
					'showArrow'=>'1',
					'arrowImg'=>'UpAccessory_gray',
					'open'=>'',
					'type'=>'waitcp',
					'Id'=>''.$CompanyId,
					'chartWid'=>'0',
					'col4marginR'=>'25',
					'row1Y'=>$waitcpQty10d>0?'0':'7',
					'col1'=>
						array(
							'Color'=>$this->color_superdark,
							'Text'=>number_format($waitcpQty)
						),
					'col1R'=>number_format($waitcpCounts),
					'col2'=>$prechar.number_format($waitcpAmount),
					'col3'=> 
						array(
							'Color'=>'#FF6B00',
							'Text'=>$waitcpQty10d>0? number_format($waitcpQty10d):''
						)
					 ,
					'col3RImg'=>$waitcpQty10d>0?'i10d_r.png':'',
					'col4'=>
						array(
							'Color'=>'#FF6B00',
							'Text'=>$waitcpAmount10d>0?$prechar.number_format($waitcpAmount10d):''
						)
						
				);
				

		    }
		    
	    }

		return $subList;
	}
	
	function notout_week_companys($week) {
		
		//notout_week_companys
		$subList = array();
		$this->load->model('YwOrderSheetModel');
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		$query = $this->YwOrderSheetModel->notout_week_companys($week);

	    if ($query->num_rows() > 0) {
		    $rs = $query->result_array();

		    foreach ($rs as $rows) {
			    $Forshort = $rows['Forshort'];
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
				
				$prechar = $rows['PreChar'];
			    $waitcpAmount = $rows['Amount'];
			    $waitcpQty = $rows['Qty'];
			    $waitcpCounts = $rows['Counts'];
		
				$bledQty = $this->YwOrderSheetModel->get_bled_qty_week($week, $CompanyId);
			    
				$subList[]=array(
					'tag'=>'ch_total',
					'companyImg'=>$LogoPath.$Logo,
					'forshort'=>$Forshort,
					'showArrow'=>'1',
					'arrowImg'=>'UpAccessory_gray',
					'open'=>'',
					'type'=>'notout',
					'Id'=>$week.'|'.$CompanyId,
					'chartWid'=>'0',
					'col4marginR'=>'25',
					'row1Y'=>$bledQty>0 ||$rows['LockQty']>0 ?'0':'7',
					'col1'=>
						array(
							'Color'=>$this->color_superdark,
							'Text'=>number_format($waitcpQty)
						),
					'col1R'=>number_format($waitcpCounts),
					'col2'=>$prechar.number_format($waitcpAmount),
					'col3'=> 
						array(
							'Color'=>'#358fc1',
							'Text'=>$bledQty>0? number_format($bledQty):''
						)
					 ,
					 'col3RImg'=>$bledQty>0?'ibl_r.png':'',
					'col4RImg'=>$rows['LockQty']>0?'ilock_r.png':'',
					'col4'=>
						array(
							'Color'=>$this->color_red,
							'Text'=>$rows['LockQty']>0?$prechar.number_format($rows['LockQty']):''
						)
						
				);
				

		    }
		    
	    }

		return $subList;

	}
	
	function notout_company_weeks($CompanyId) {
		$this->load->model('YwOrderSheetModel');
		$this->load->model('ProductdataModel');
		$this->load->library('datehandler');
		$query = $this->YwOrderSheetModel->get_notout_weeks($CompanyId);
		$rowNums = $query->num_rows();
		$curWeeks = $this->ThisWeek;
		if ($rowNums > 0) {
			$rs = $query->result_array();
			for($i=0; $i<$rowNums; $i++) {
			
				$rows = $rs[$i];
				
				$week = $rows['Weeks'];
				
				$bledQty = $this->YwOrderSheetModel->get_bled_qty_week($week,$CompanyId);
				$subList[]=array(
					'tag'=>'ch_total',
					'hasWeek'=>'1',
					'Id'=>($week==''?'TBC':(''.$week) ).'|'.$CompanyId,
					'week'=>$week.'',
					'weekTitle'=>$week==''?'交期待定':$this->getWeekToDate($week),
					'showArrow'=>'1',
					'open'=>'',
					'segIndex'=>'-1',
					'type'=>'notout',
// 					'chartWid'=>'0',
					'row1Y'=>$bledQty>0 || $rows['LockQty']>0 ?'0':'7',
					'col4marginR'=>'25',
					'col1'=>
					array(
						'Color'=>($week!='' && $curWeeks<=$week) ? $this->color_superdark:$this->color_red,
						'Text'=>number_format($rows['Qty'])
					),
					'col1R'=>number_format($rows['Counts']),
					'col2'=>'¥'.number_format($rows['Amount']),
					'col3'=> 
						array(
							'Color'=>'#358fc1',
							'Text'=>$bledQty>0? number_format($bledQty):''
						)
					 ,
					'col3RImg'=>$bledQty>0?'ibl_r.png':'',
					'col4'=>
						array(
							'Color'=>$this->color_red,
							'Text'=>$rows['LockQty']>0?number_format($rows['LockQty']):''
						),
					'col4RImg'=>$rows['LockQty']>0?'ilock_r.png':''
						
				);

			
		}
		}
		
		
		return $subList;

	}
	function segList() {
		$params = $this->input->post();
		$noout_index = element('noout_index', $params , '1');
		$Id = element('Id', $params , '');
		$type = element('type', $params , '');
		$subList = array();
		switch ($noout_index) {
			case 0:
			{
				$subList = $this->notout_company_weeks($Id);
			}
			break;
			case 1:{
				
				switch ($type) {
					case 'waitcp': {
						$subList = $this->waitcp_companys();
					}
					break;
					
					case 'notout':
					{
						
						$subList = $this->notout_week_companys($Id);
					}
					break;
				}
				
			}
			break;
			
			case 2:{
				$subList = $this->waitcp_company_sublist($Id,1);
			}
			break;
		}
		
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$subList);
	    
		$this->load->view('output_json',$data);
	}
	
	//need model YwOrderSheetModel
	function remark_inlist(&$nofinishList,$POrderId ) {
		$remarkNew = $this->YwOrderSheetModel->order_remark($POrderId);
		if ($remarkNew!=null && element('Remark',$remarkNew,'')!='') {
			
	        $modifier = element('Name',$remarkNew,'');
	        $remark   = element('Remark',$remarkNew,'');
	        $modified = element('Date',$remarkNew,''); 
			$times =  $this->GetDateTimeOutString($modified,$this->DateTime);
			$remarkArray=array(
				'content'=>$remark,
				'oper'=>$times.' '.$modifier,
				'tag'=>'remarkNew',
				'margin_left'=>'68',
				'img'=>'remark1',
				'separ_left'=>'25'	
			);
			$nofinishList[count($nofinishList)-1]['hideLine']='1';
			$nofinishList[]=$remarkArray;
		}
	}
	
	function waitcp_company_sublist($CompanyId, $needsGray='') {
		$this->load->model('YwOrderSheetModel');
		$this->load->model('ProductdataModel');
		$this->load->library('datehandler');
		
		$query = $this->YwOrderSheetModel->get_wait_cp('',$CompanyId);
		
		
		$cpList = array();
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			foreach ($rs as $rows) {
				
				$rkDate = $rows['rkDate'];
				$rkDate = strtotime($rkDate);
				
				
				$weekDate = date('Y-m-d', $rkDate);
				
				
				$seconds = strtotime($this->DateTime)- strtotime($rows['OrderDate']);
				$time = $this->datehandler->GetTimeInterval($seconds);
				$timeColor = (strtotime($rows['Leadtime'])- strtotime($this->DateTime) )
				> 0 ? $this->color_grayfont:$this->color_red;
				$productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
				
				$val_2 = array(
				    'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>''.number_format($rows['Qty']),
				   			  'Color'=>$this->color_superdark,
				   			  'FontSize'=>'11'),
				   		array('Text'    =>' '.number_format($rows['tStockQty']),
				   			  'Color'=>'#01be56',
				   			  'FontSize'=>'11'),
				   	)
			    );

				
				$cpList[]=array(
					'tag'=>'z_order',
					'Id'         =>$rows['POrderId'].'|'.$rows['ProductId'],
					'POrderId'   =>$rows['POrderId'],
				    'productImg' =>$productImg,
// 				    'arrowImg'=>'UpAccessory_gray',
'arrowImg'   =>$needsGray==1?'UpAccessory_gray': 'arrow_gray_s',
				    'type'=>'waitcp',
				    'open'=>'',
				    'Picture'    =>$rows['TestStandard']==1?1:0,
					'showArrow'=>'1',
				    'shipImg'    =>'',
					'week'       =>$rows['Weeks'],
					'title'      =>$rows['cName'],
					'col1'       =>$rows['OrderPO'],
					'col2'       =>array('Text'    =>''.number_format($rows['Qty']),
				   			  'Color'=>$this->color_superdark,
				   			  'light'=>'11','Align'=>'R'),
					'col2Img'    =>'',
					'week_s'=>array('weekDate'=>$weekDate),
					'col3'=>array('Text'    =>' '.number_format($rows['tStockQty']),
				   			  'Color'=>'#01be56',
				   			  'light'=>'11'),
					'percent'       =>
					    array('Align'=>'L','Text'=>$rows['PreChar'].number_format( round($rows['Price'],2),2), 'Color'=>$this->color_superdark),
					'col2X'  =>'-30',
					'col3X'  =>'-42',
					'col4X'  =>'-23',
					'col3Img'    =>'',
					'col4'       =>array(
							'Text'=>'...'.$time,
							'Color'=>$timeColor
						),
					'completeImg'=>'',
					'lineLeft'=>'25',
					'inspectImg' =>''
				);
				
				
				$this->remark_inlist($cpList,$rows['POrderId']);
				
}
				
			}
		return $cpList;
	}
	
	function notout_weekcompany_sublist($week, $CompanyId) {
		
		$this->load->model('YwOrderSheetModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('ProductdataModel');
		$this->load->library('datehandler');
		
		$query = $this->YwOrderSheetModel->notout_orderlist($week,$CompanyId);
		
		$this->load->model('ScCurrentMissionModel');
		$this->load->model('ScSheetModel');
		$sPOrderIds =$this->ScCurrentMissionModel->get_all_records();
		
		
		$subList = array();
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			foreach ($rs as $rows) {
				
				$POrderId = $rows['POrderId'];
				$lockArray = $this->YwOrderSheetModel->check_lock($POrderId);
			
				$lock = element('lock',$lockArray,0);
				
				$lockImg = '';
				$remark = '';
				$oper = '';
				$remarkDate = '';
				switch ($lock) {
					case 1:
						$lockImg = 'order_lock';
						$oper    = element('oper',$lockArray,'');
						$remark  = element('remark',$lockArray,'');
						$remarkDate = element('date',$lockArray,'');

					break;
					case 2:
					case 3:
						$lockImg = 'order_lock_s';
						$oper    = element('oper',$lockArray,'');
						$remark  = element('remark',$lockArray,'');
						$remarkDate = element('date',$lockArray,'');
						
					break;
					default:
					break;
				}
			
			
				$scQtyRow = $this->ScCjtjModel->get_order_scqty($POrderId, 1);
				
				$productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
				
				
				$ShipType=$rows["ShipType"];
				if ($rows["ShipType"]=='credit' || $rows["ShipType"]=='debit'){
		           $ShipType=$rows["ShipType"]=='credit'?31:32;
			    }
			    
			    
			     if (strpos($sPOrderIds,$rows['sPOrderId'])){
		            $inspectImg='coding';
	            }
	            else{
		            $inspectImg=''; 
	            }
			    
			    $completeImg='';
		    
			    if ($scQtyRow==null || $scQtyRow['qty'] <= 0) {
				    $signed = $this->YwOrderSheetModel->get_bled_qty_id($rows['POrderId']);
				    if ($signed != 0) {
					    
					    if ($signed>0 && $signed >= $rows['Qty'])
					    {
						    $completeImg = 'stuff_ybl';
						    
						    if ($scQtyRow && $scQtyRow['qty'] > 0) {
							    $completeImg = '';  
						    }
					    }
				    }
			    }
			    
			    
			    
				
				$percent = $this->YwOrderSheetModel->getOrderProfit($POrderId);
				$percent = $percent['percent'].'%';
				$percentColor = $this->YwOrderSheetModel->getcolor_profit($percent);
				$subList[]=array(
					'tag'=>'z_order',
					'Id'         =>$POrderId.'|'.$rows['ProductId'],
					'POrderId'   =>''.$POrderId,
					's_z'=>$rows['sPOrderId'],
				    'productImg' =>$productImg,
				    'arrowImg'=>'arrow_gray_s',
				    'type'=>'notout',
				    'open'=>'',
				    'Operator'=>'',
				    'Picture'    =>$rows['TestStandard']==1?1:0,
					'showArrow'=>'1',
				    'shipImg'    =>'ship'.$ShipType,
					'week'       =>$rows['Weeks'],
					'title'      =>$rows['cName'],
					'col1'       =>$rows['OrderPO'],
					'col2'       =>number_format($rows['Qty']),
					'col3'=>array('Color'=>'#01be56', 'Text'=>$scQtyRow['qty']>0?number_format($scQtyRow['qty']):''),
					'created'=>array('DateType'=>'day','Text'=>$rows['OrderDate'],'Color'=>'#358fc1'),
					'line'=>$scQtyRow['line'],
					'col2Img'    =>'scdj_11',
					'percent'       =>
					    array('Text'=>$rows['PreChar'].number_format( round($rows['Price'],2),2), 'Color'=>$this->color_superdark,'Align'=>'R'),
					'col4X'  =>'-2',
					'col2X'  =>'15',
					'col3X'  =>'15',
					'col3Img'    =>'scdj_12',
					'col4'       =>array(
							'Text'=>''.$percent,
							'Color'=>$percentColor
						),
					'completeImg'=>$completeImg,
					'lineLeft'=>'25',
					'lockImg'=>'',
					'lockImg_s'=>$lockImg,
					'locksBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'inspectImg' =>$inspectImg
				);
				
				if ($remark != '') {
					$times =  $this->GetDateTimeOutString($remarkDate,$this->DateTime);
					$remarkArray=array(
						'content'=>$remark,
						'oper'=>$times.' '.$oper,
						'tag'=>'remarkNew',
						'margin_left'=>'68',
						'img'=>'remark1',
						'separ_left'=>'25'	
					);
					$subList[count($subList)-1]['hideLine']='1';
					$subList[]=$remarkArray;
				} else 
					$this->remark_inlist($subList,$rows['POrderId']);
				
}
				
			}
		return $subList;
		
	}
	
	function subList() {
		
		$params = $this->input->post();
		$noout_index = element('noout_index', $params , '1');
		$upTag = element('upTag', $params , '');
		$Id = element('Id', $params , '');
		$type = element('type', $params , '');
		
		$subList = array();
		
		$arrayOne = array('tag'=>'none');
		
		switch ($noout_index) {
			case 0:
			{
				switch ($upTag) {
					case 'ch_total':{
						$infos = explode('|', $Id);
							
							if (count($infos) > 1)
							$subList = $this->notout_weekcompany_sublist($infos[0],$infos[1]);
					}
					break;
				
					case 'z_order': {
						$infos = explode('|', $Id);
						if (count($infos) > 1) {
							$type = 'notout';
							$subList = $this->same_product_infos($infos[1], $infos[0],'', $type);
						}
						
					}
				}
			}
			break;
			
			case 1:
			{
				switch ($upTag) {
					case 'ch_total':{
						if ($type == 'notout') {
							$infos = explode('|', $Id);
							
							if (count($infos) > 1)
							$subList = $this->notout_weekcompany_sublist($infos[0],$infos[1]);
						} else {
							$subList = $this->waitcp_company_sublist($Id);
						}
					}
					break;
					case 'z_order': {
					$infos = explode('|', $Id);
					if (count($infos) > 1) {
						$subList = $this->same_product_infos($infos[1], $infos[0],'', $type);
					}
					
					}
					break;
				}
				//
				
				
			}
			break;
			
			case 2:{
				switch ($upTag) {
					
					case 'z_order': {
					$infos = explode('|', $Id);
					if (count($infos) > 1) {
						$subList = $this->same_product_infos($infos[1], $infos[0],'', $type);
					}
					
					}
					break;
				}
			}
			break;
		}
		
		
	    $cts = count($subList);
	    if ($cts > 0) {
		    $subList[$cts - 1]['deleteTag'] = $upTag;
	    }
	    
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$subList);
	    
		$this->load->view('output_json',$data);
		
	}
	
		
	
	function not_out_sections() {
		
		$sectionList = array();
		$this->load->model('YwOrderSheetModel');
		$waitcpSumRow = $this->YwOrderSheetModel->get_waitcp_sum();
	    $waitcpAmount = $waitcpSumRow['Amount'];
	    $waitcpQty = $waitcpSumRow['tStockQty'];
	    $waitcpCounts = $waitcpSumRow['Counts'];
	    $waitcpAmount10d = $waitcpSumRow['OverAmount'];
	    $waitcpQty10d = $waitcpSumRow['OverQty'];

	    
		$sectionList[]=array(
			'tag'=>'notout',
			'titleIcon'=>'sh_product',
			'weekTitle'=>'成品',
			'showArrow'=>'1',
			'method'=>'segList',
			'segIndex'=>'-1',
			'type'=>'waitcp',
			'Id'=>'waitcp',
			'chartWid'=>'0',
			'col4marginR'=>'25',
			'col1'=>
				array(
					'Color'=>$this->color_superdark,
					'Text'=>number_format($waitcpQty)
				),
			'col1R'=>number_format($waitcpCounts),
			'col2'=>'¥'.number_format($waitcpAmount),
			'col3'=> 
				array(
					'Color'=>'#FF6B00',
					'Text'=>number_format($waitcpQty10d)
				)
			 ,
			'col3RImg'=>'i10d_r.png',
			'col4'=>
				array(
					'Color'=>'#FF6B00',
					'Text'=>'¥'.number_format($waitcpAmount10d)
				)
				
		);
		
		$query = $this->YwOrderSheetModel->get_notout_weeks();
		$rowNums = $query->num_rows();
		$curWeeks = $this->ThisWeek;
		if ($rowNums > 0) {
			$rs = $query->result_array();
			for($i=0; $i<$rowNums; $i++) {
			
				$rows = $rs[$i];
				
				$week = $rows['Weeks'];
				
				$bledQty = $this->YwOrderSheetModel->get_bled_qty_week($week);
				$sectionList[]=array(
					'tag'=>'notout',
					'hasWeek'=>'1',
					'Id'=>$week==''?'TBC':(''.$week),
					'week'=>$week.'',
					'weekTitle'=>$week==''?'交期待定':$this->getWeekToDate($week),
					'showArrow'=>'1',
					'method'=>'segList',
					'segIndex'=>'-1',
					'type'=>'notout',
					'chartWid'=>'0',
					'row1Y'=>$bledQty>0 || $rows['LockQty']>0 ?'0':'7',
					'col4marginR'=>'25',
					'col1'=>
					array(
						'Color'=>($week!='' && $curWeeks<=$week) ? $this->color_superdark:$this->color_red,
						'Text'=>number_format($rows['Qty'])
					),
					'col1R'=>number_format($rows['Counts']),
					'col2'=>'¥'.number_format($rows['Amount']),
					'col3'=> 
						array(
							'Color'=>'#358fc1',
							'Text'=>$bledQty>0? number_format($bledQty):''
						)
					 ,
					'col3RImg'=>$bledQty>0?'ibl_r.png':'',
					'col4'=>
						array(
							'Color'=>$this->color_red,
							'Text'=>$rows['LockQty']>0?number_format($rows['LockQty']):''
						),
					'col4RImg'=>$rows['LockQty']>0?'ilock_r.png':''
						
				);

			
		}
		}
		
		
		return $sectionList;
	}
	
	function not_out_nocpsections() {
				$sectionList = array();
		
		$sectionList[]= array('tag'=>'none','footHeight'=>'1');


		$this->load->model('YwOrderSheetModel');
		$this->load->model('Ch1shipsheetModel');
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();

		$query = $this->YwOrderSheetModel->notout_week_companys('');

	    if ($query->num_rows() > 0) {
		    $rs = $query->result_array();

		    foreach ($rs as $rows) {
			    $Forshort = $rows['Forshort'];
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
				
				$prechar = $rows['PreChar'];
			    $Amount = $rows['Amount'];
			    $Qty = $rows['Qty'];
			    $Counts = $rows['Counts'];
			    $OverAmount = $rows['OverAmount'];
			    $OverCounts = $rows['OverCounts'];
			    $OverQty = $rows['OverQty'];
		


				
				
				$puncInfo = $this->Ch1shipsheetModel->get_order_punctuality(7, $CompanyId);
				
				$percent = $puncInfo['percent'];
				$percentColor = $puncInfo['color'];
				
				$chartValInner = array(
					array('value'=>$percent, 'color'=>$percentColor),
					array('value'=>100-$percent, 'color'=>'#clear')
				);
				
				$chartPercent=array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$percent",
					   			  'FontSize'=>'11',
					   			  'Color'   =>"$percentColor"),
					   		array('Text'    =>'%',
					   			  'FontSize'=>'6',
					   			  'Color'   =>"$percentColor")
					   		
					   	)
			    );

				
				$realAmount = $rows['realAmount'];
				
				$trueProfit = $realAmount-$this->YwOrderSheetModel->nooutorder_company_cost($CompanyId);
				$percent = $realAmount>0?(round($trueProfit/$realAmount*100)):0;
			    $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
				
				
			    $sectionList[]=array(
					'tag'=>'notout',
					'showArrow'=>'1',
					'method'=>'segList',
					'companyImg'=>$LogoPath.$Logo,
					'forshort'=>$Forshort,
					'type'=>'waitnocp',
					'Id'=>''.$CompanyId,
					'col4marginR'=>'25',
					'row1Y'=>$OverAmount>0?'0':'7',
					'percent'=>$chartPercent,
					'percent2'=>array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'6',
							   			  'Color'   =>"$percentcolor")
							   		
							   		)
						   		),
					"pie2"=>
				array(
					
					array("value"=>$percent>0?$percent:'0',"color"=>"$percentcolor"),
					array("value"=>100-$percent,"color"=>"#f7f7f7")
				),
				'chartBgImg'=>'chartFrame2',
					'pie'=>$chartValInner,
					'col1'=>array(
							'Color'=>$this->color_superdark,
							'Text'=>number_format($Qty)
						),
					'col1R'=>number_format($Counts),
					'col2'=>$prechar.number_format($Amount),
					'col3'=> 
						array(
							'Color'=>$this->color_red,
							'Text'=>$OverQty>0? number_format($OverQty):''
						)
					 ,
					'col3R'=>$OverCounts>0?number_format($OverCounts):'',
					'col4'=>
						array(
							'Color'=>$this->color_red,
							'Text'=>$OverAmount>0?$prechar.number_format($OverAmount):''
						),
					'footHeight'=>'1'
				);

								

		    }
		    
	    }



		
		return $sectionList;

	}
	
	function not_out_cpsections() {
		
		$sectionList = array();
		
		$sectionList[]= array('tag'=>'none','footHeight'=>'1');


		$this->load->model('YwOrderSheetModel');
		$this->load->model('Ch1shipsheetModel');
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		$waitcpSumRow = $this->YwOrderSheetModel->get_waitcp_sum();
		
		$waitcpAmountAll = $waitcpSumRow['Amount'];
		$query = $this->YwOrderSheetModel->get_waitcp_companys('','1');

	    if ($query->num_rows() > 0) {
		    $rs = $query->result_array();

		    foreach ($rs as $rows) {
			    $Forshort = $rows['Forshort'];
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
				
				$prechar = $rows['PreChar'];
			    $waitcpAmount = $rows['Amount'];
			    $waitcpQty = $rows['tStockQty'];
			    $waitcpCounts = $rows['Counts'];
			    $waitcpAmount10d = $rows['OverAmount'];
			    $waitcpQty10d = $rows['OverQty'];
		
				$waitcpAmountRmb = $rows['sAmount'];
				$percent = null;
				if ($waitcpAmountAll > 0) {
					$percent = round($waitcpAmountRmb/$waitcpAmountAll*100);
					
					if ($percent > 0)
					$percent = array(
					    'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>''.$percent,
					   			  'Color'=>$this->color_grayfont,
					   			  'FontSize'=>'14'),
					   		array('Text'    =>'%',
					   			  'Color'=>$this->color_grayfont,
					   			  'FontSize'=>'8')
					   	)
				    );

				}
				
				$noreciveInfo = $this->Ch1shipsheetModel->company_month_unrecived($CompanyId);
				
				
				$nopayInfo = array(
					    'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>$noreciveInfo['over']>0? $noreciveInfo['prechar']. number_format($noreciveInfo['over'])  : '--',
					   			  'Color'=>$this->color_red,
					   			  'FontSize'=>'9'),
					   		array('Text'    =>$noreciveInfo['all']>0? '/'.$noreciveInfo['prechar']. number_format($noreciveInfo['all'])  : '/--',
					   			  'Color'=>$this->color_grayfont,
					   			  'FontSize'=>'9')
					   	)
				    );
				    
				    
				$zxb = $noreciveInfo['safe'];
				if ($zxb!='') {
					$charFirst = substr($zxb, 0, 1);
					if (intval($charFirst) <= 0) {
						$zxb = str_replace($charFirst, '', $zxb);
					} else {
						$charFirst = $prechar;
					}
					$zxb = str_replace($prechar, '', $zxb);
					$zxb = str_replace('¥', '', $zxb);
					$zxb = str_replace('$', '', $zxb);
					$zxb = intval($zxb);
					$unitZxb = '';
					if ($zxb > 100000) {
						$zxb = round($zxb/10000);
						$unitZxb = 'w';
					}
					$zxb = $charFirst.number_format($zxb).$unitZxb;
				} else {
					$zxb = '--';
				}
				
				
				
			    $sectionList[]=array(
					'tag'=>'notout',
					'showArrow'=>'1',
					'method'=>'segList',
					'companyImg'=>$LogoPath.$Logo,
					'forshort'=>$Forshort,
					'type'=>'waitcp',
					'Id'=>''.$CompanyId,
					'hasSub'=>'1',
					'col4marginR'=>'10',
					'row1Y'=>$waitcpQty10d>0?'0':'7',
					'zxbImg'=>'icon_zxb.png',
					'zxb'=>$zxb,
					'payImg'=>'payed_'. $noreciveInfo['mode'],
					'nopay'=>$nopayInfo,
					'percent2'=>$percent,
					'percent2Y'=>$waitcpQty10d>0?'-11':'-4',
					'col1'=>array(
							'Color'=>$this->color_superdark,
							'Text'=>number_format($waitcpQty)
						),
					'col1R'=>number_format($waitcpCounts),
					'col2'=>$prechar.number_format($waitcpAmount),
					'col3'=> 
						array(
							'Color'=>'#FF6B00',
							'Text'=>$waitcpQty10d>0? number_format($waitcpQty10d):''
						)
					 ,
					'col3RImg'=>$waitcpQty10d>0?'i10d_r.png':'',
					'col4'=>
						array(
							'Color'=>'#FF6B00',
							'Text'=>$waitcpAmount10d>0?$prechar.number_format($waitcpAmount10d):''
						),
					'footHeight'=>'1'
				);

								

		    }
		    
	    }



		
		return $sectionList;
	}
	
	public function main() {
		
		$params = $this->input->post();
		$noout_index = element('noout_index', $params , '1');
		$sectionList = array();
		
		switch ($noout_index) {
			//默认
			case 1:{
				$sectionList = $this->not_out_sections();
			}
			break;
			//未完成
			case 0:{
				$sectionList = $this->not_out_nocpsections();
				
			}
			break;
			// 成品
			case 2:{
				$sectionList = $this->not_out_cpsections();
			}
			break;
		}
		
	    $data['jsondata']=array('status'=>'1','message'=>$noout_index==2?'':'1','totals'=>1,'rows'=>$sectionList);
	    $this->load->view('output_json',$data);
	}
	
	function same_product_infos($ProductId, $aMid, $CompanyId='', $cptypes='') {
		$this->load->model('Ch1shipsheetModel');
		$this->load->model('TradeObjectModel');
		$this->load->model('YwOrderSheetModel');
		$this->load->library('datehandler');
		$this->load->model('ProductdataModel');
		$this->load->model('ScCjtjModel');
		
		$subList = array();
		
		if ($CompanyId=='') {
			$CompanyId = $this->ProductdataModel->get_companyid($ProductId);
			
		}
		
		$prechar = $this->TradeObjectModel->get_prechar($CompanyId);
		
		$query = $this->YwOrderSheetModel->get_unfinished($ProductId);
		
		if ($query->num_rows() > 0) {
			
			$nofinishList = array();
			
			$rs = $query->result_array();
			$allNoQty = 0;
			$allNoQtyAmount = 0;
			foreach ($rs as $rows) {
			/*
				SELECT  S.ScQty, S.Qty, P.Price, Y.POrderId,Y.Qty AS OrderQty, 
IFNULL( PI.LeadWeek, PL.LeadWeek ) AS LeadWeek, P.cName, P.TestStandard, P.ProductId, 
L.Letter AS Line,L.GroupId,M.OrderPO ,M.OrderDate
			*/	
				$getOrderProfit = $this->YwOrderSheetModel->getOrderProfit($rows['POrderId']);
				
				$allNoQty += $rows['Qty'];
				$allNoQtyAmount += $rows['Qty']*$rows['Price'];
				
				$OrderDate = $rows['OrderDate'];
				$OrderDate = strtotime($OrderDate);
				$titleAttr = array(
					'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>date('m-d',$OrderDate)."\n",
				   			  'FontSize'=>'10'),
				   		array('Text'    =>date('Y',$OrderDate),
				   			  'FontSize'=>'7')
				   		
				   	)

				);
				
				$ShipType=$rows["ShipType"];
				if ($rows["ShipType"]=='credit' || $rows["ShipType"]=='debit'){
		           $ShipType=$rows["ShipType"]=='credit'?31:32;
			    }
			    
				$seconds = strtotime($this->DateTime)- strtotime($rows['OrderDate']);
				$time = $this->datehandler->GetTimeInterval($seconds);
				$timeColor = (strtotime($rows['Leadtime'])- strtotime($this->DateTime) )
				> 0 ? $this->color_grayfont:$this->color_red;
				
				
				$scQty = $this->ScCjtjModel->get_order_scqty($rows['POrderId']);
				$bgcolor = '#ffffff';
					
				if ($rows['POrderId'] == $aMid && $cptypes=='notout') {
					$bgcolor = '#fffcbb';
				}
					
				$nofinishList[]=array(
					'tag'=>'sub_order',
					'bg_color'=>$bgcolor,
					'Id'         =>$rows['POrderId'].'|'.$rows['ProductId'],
					'arrowImg'   =>'UpAccessory_gray',
					'POrderId'   =>$rows['POrderId'],
				    'productImg' =>'',
				    'shipImg'    =>'ship'.$ShipType,
				    'dateTitle'=>$titleAttr,
				    'noimg'=>'1',
					'week'       =>$rows['LeadWeek'],
					'title'      =>$rows['cName'],
					'topRightWid'=>'40',
					'created'    =>array(
							'Text'=>'...'.$time,
							'Color'=>$timeColor,
							'light'=>'11'
						),
					'col1'       =>$rows['OrderPO'],
					'col2'       =>array(
						'Align'=>'L','Text'=>number_format($rows['Qty'])
					) ,
					'col2Img'    =>'scdj_11',
					'titleImg'=>'sh_ordered',
					'col3'       =>
					    array('Text'=>$scQty>0?''.$scQty:'', 'Color'=>'#01be56'),
					'col3X'  =>'20',
					'col2X'  =>'15',
					'col4X'  =>'-8',
					'titleImgY'=>'5',
					'col3Img'    =>'scdj_12',
					'col4'       =>$getOrderProfit ? array(
						'Text'=>$getOrderProfit['percent'].'%',
						'Color'=>$getOrderProfit['color']
					) : null,
					'completeImg'=>'',
					'lineLeft'=>'25',
					'inspectImg' =>''
				);
				
				$this->remark_inlist($nofinishList,$rows['POrderId']);
				
/*
				$remarkNew = $this->YwOrderSheetModel->order_remark($rows['POrderId']);
				if ($remarkNew!=null && element('Remark',$remarkNew,'')!='') {
					
			        $modifier = element('Name',$remarkNew,'');
			        $remark   = element('Remark',$remarkNew,'');
			        $modified = element('Date',$remarkNew,''); 
					$times =  $this->GetDateTimeOutString($modified,$this->DateTime);
					$remarkArray=array(
						'content'=>$remark,
						'oper'=>$times.' '.$modifier,
						'tag'=>'remarkNew',
						'img'=>'remark1'			
					);
					$nofinishList[count($nofinishList)-1]['hideLine']='1';
					$nofinishList[]=$remarkArray;

				}
*/
				
				
			}
			
			$allNoQty = number_format($allNoQty);
			$allNoQtyAmount = number_format($allNoQtyAmount);
			$subList[]=array(
				'tag'=>'subtitle',
				'title'=>'未完成',
				'titleImg'=>'sh_notout',
				'col1'=>$allNoQty,
				'bgcolor'=>'#f7f7f7',
				'hideLine'=>'1',
				'col2'=>$prechar.$allNoQtyAmount
			);
			$nofinishList[count($nofinishList)-1]['hideLine']='1';
			$subList = array_merge($subList, $nofinishList);
		}
		
		$query = $this->YwOrderSheetModel->get_wait_cp($ProductId);
		if ($query->num_rows() > 0) {
			$cpList = array();
			$allCPQty = 0;
			$allCPQtyAmount = 0;
			$rs = $query->result_array();
			foreach ($rs as $rows) {
				
				$allCPQty += $rows['Qty'];
				$allCPQtyAmount += $rows['Qty']*$rows['Price'];
				$rkDate = $rows['rkDate'];
				$rkDate = strtotime($rkDate);
				
				
				$weekDate = date('Y-m-d', $rkDate);
				$titleAttr = array(
					'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>date('m-d',$rkDate)."\n",
				   			  'FontSize'=>'10'),
				   		array('Text'    =>date('Y',$rkDate),
				   			  'FontSize'=>'7')
				   		
				   	)

				);
				
				$seconds = strtotime($this->DateTime)- strtotime($rows['OrderDate']);
				$time = $this->datehandler->GetTimeInterval($seconds);
				$timeColor = (strtotime($rows['Leadtime'])- strtotime($this->DateTime) )
				> 0 ? $this->color_grayfont:$this->color_red;
				
				$bgcolor = '#ffffff';
					
				if ($rows['POrderId'] == $aMid && $cptypes=='waitcp') {
					$bgcolor = '#fffcbb';
				}
					
				
				
				$cpList[]=array(
					'tag'=>'sub_order',
					'Id'         =>$rows['POrderId'].'|'.$rows['ProductId'],
					'POrderId'   =>$rows['POrderId'],
				    'productImg' =>'',
				    'shipImg'    =>'',
				    'bg_color'=>$bgcolor,
				    'dateTitle'=>$titleAttr,
				    'noimg'=>'1',
					'week'       =>$rows['Weeks'],
					'title'      =>$rows['cName'],
					'col1'       =>$rows['OrderPO'],
					'col2'       =>array(
						'Align'=>'L','Text'=>number_format($rows['Qty'])
					) ,
					'col2Img'    =>'scdj_11',
					'titleImg'=>'sh_in.png',
					'col2X'  =>'15',
					'week_s'=>array('weekDate'=>$weekDate),
					'col3'       =>
					    array('Text'=>''.$rows['tStockQty'], 'Color'=>'#01be56'),
					'col3X'  =>'20',
					'col4X'  =>'-23',
					'titleImgY'=>'5',
					'col3Img'    =>'sh_product_0.png',
					'col4'       =>array(
							'Text'=>'...'.$time,
							'Color'=>$timeColor
						),
					'completeImg'=>'',
					'lineLeft'=>'25',
					'inspectImg' =>''
				);
				
				
				$this->remark_inlist($cpList,$rows['POrderId']);
				

				
			}
			if ($allCPQty > 0) {
				$allCPQty = number_format($allCPQty);
				$allCPQtyAmount = number_format($allCPQtyAmount);
				$subList[]=array(
					'tag'=>'subtitle',
					'title'=>'成品',
					'titleImg'=>'sh_product.png',
					'col1'=>$allCPQty,
					'bgcolor'=>'#f7f7f7',
					'hideLine'=>'1',
					'col2'=>$prechar.$allCPQtyAmount
				);
				
				$cpList[count($cpList)-1]['hideLine']='1';
				
				$subList = array_merge($subList, $cpList);
			}
			
		}
		
		/*
			CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,
          (S.Qty-B.shipQty) AS Qty,S.Price,(B.rkQty-B.shipQty) AS tStockQty,
          S.ShipType,S.Estate,S.scFrom,P.cName,P.TestStandard,C.Forshort, 
          PI.Leadtime,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks 
		*/
		
		$query = $this->YwOrderSheetModel->get_rk_shipped($ProductId);
		if ($query->num_rows() > 0) {
			
			
			$chList = array();
			
			$rs = $query->result_array();
			
			
			$allChQty = 0;
			$allChQtyAmount = 0;
			foreach ($rs as $rows) {
			/*
				SELECT  S.Qty, P.Price, Y.POrderId,Y.Qty AS OrderQty,
MIN(S.Date) rkDate, 
IFNULL( PI.LeadWeek, PL.LeadWeek ) AS LeadWeek, P.cName, P.TestStandard, P.ProductId, 
L.Letter AS Line,L.GroupId,M.OrderPO ,M.OrderDate,CM.Id as chMid,C.created as chDate,CM.InvoiceNO,C.Qty as chQty

			*/	
			
				$allChQty += $rows['chQty'];
				$allChQtyAmount += $rows['chQty']*$rows['Price'];
			
				
				
				
				$rkDate = $rows['rkDate'];
				$rkDate = strtotime($rkDate);
				

				
				$chDate = $rows['chDate'];
				$chDate = strtotime($chDate);
				$titleAttr = array(
				'isAttribute'=>'1',
		   		'attrDicts'=>array(
			   		array('Text'    =>date('m-d',$rkDate)."\n",
			   			  'FontSize'=>'10'),
			   		array('Text'    =>date('Y',$rkDate)."\n\n",
			   			  'FontSize'=>'7'),
			   		array('Text'    =>date('m-d',$chDate)."\n",
			   			  'FontSize'=>'10'),
			   		array('Text'    =>date('Y',$chDate),
			   			  'FontSize'=>'7')
			   		
			   	)

				);
				
				
				$ShipType=$rows["Ship"];
				if ($rows["ShipType"]=='credit' || $rows["ShipType"]=='debit'){
		           $ShipType=$rows["ShipType"]=='credit'?31:32;
			    }
				
				$seconds = strtotime($rows['chDate'])- strtotime($rows['OrderDate']);
				$time = $this->datehandler->GetTimeInterval($seconds);
				$timeColor = (strtotime($rows['Leadtime'])- strtotime($rows['chDate']) )
				> 0 ? $this->color_grayfont:$this->color_red;
				
					$bgcolor = '#ffffff';
					
					if ($rows['Mid'] == $aMid) {
						$bgcolor = '#fffcbb';
					}
					
					$chList[]=array(
					'tag'=>'sub_order',
					'bg_color'=>$bgcolor,
					'Id'         =>$rows['POrderId'].'|'.$rows['ProductId'],
					'arrowImg'   =>'UpAccessory_gray',
					'POrderId'   =>$rows['POrderId'],
				    'productImg' =>'',
				    'shipImg'    =>'ship'.$ShipType,
				    'dateTitle'=>$titleAttr,
				    'noimg'=>'1',
					'title'      =>$rows['InvoiceNO'],
					'created'    =>array(
							'Text'=>$time,
							'Color'=>$timeColor
						),
					'col1'       =>$rows['OrderPO'],
					'col2'       =>array(
						'Align'=>'L','Text'=>number_format($rows['chQty'])
					) ,
					'col2Img'    =>'sh_shiped_0',
					'titleImg'=>'sh_in.png',
					'titleImg2'=>'sh_out.png',
					'col3'       =>$prechar.$rows['Price'],
					'col3X'  =>'20',
					'col2X'  =>'15',
					'col4X'  =>'-8',
					'titleImgY'=>'-10',
					'titleX'=>'-24',
					'col3Img'    =>'',
					'col4'       =>$prechar. ($rows['chQty']*$rows['Price']),
					'completeImg'=>'',
					'lineLeft'=>'25',
					'inspectImg' =>''
				);
				
				
								
			}
			
			
			if ($allChQty > 0) {
				$allChQty = number_format($allChQty);
				$allChQtyAmount = number_format($allChQtyAmount);
				$subList[]=array(
					'tag'=>'subtitle',
					'title'=>'已出',
					'titleImg'=>'sh_shiped.png',
					'col1'=>$allChQty,
					'bgcolor'=>'#f7f7f7',
					'hideLine'=>'1',
					'col2'=>$prechar.$allChQtyAmount
				);
				$subList = array_merge($subList, $chList);
			}
		}

		if (count($subList) > 0) {
			$subList[count($subList)-1]['hideLine']='0';
		}
		return $subList;
		
	}

		
}