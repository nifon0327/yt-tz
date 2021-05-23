<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Cg_client extends MC_Controller {

	
	
	function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->StuffPuncInfo = array('none'=>'');
        
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



		 
	public function index() {
			 
		$message='';
		$this->load->model('cg1stocksheetModel');

		$dict=$this->cg1stocksheetModel->all_cgmain();
		$status=count($dict)>0?1:0;
	   
	   
		$data['jsondata']=array('status'=>$status,'message'=>"",'rows'=>$dict);
		$this->load->view('output_json',$data);
			
			 
			 
	} 
	
	function cg_ordered_months($buyerId='') {
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		
		$this->load->model('cg1stocksheetModel');
		

		$rs=$this->cg1stocksheetModel->cg_ordered_months($buyerId);
		if ($rs != null) {
			foreach ($rs as $rows) {

				$Month = $rows['Month'];
				$timeMon = strtotime($Month);
			    $titleObj['isAttribute']='1';
			    $titleObj['attrDicts']=array(
				   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
				   			  'FontSize'=>'14',
				   			  'FontWeight'=>'bold',
				   			  'Color'   =>"#3b3e41"),
				   		array('Text'    =>"\n".date('Y', $timeMon),
				   			  'FontSize'=>'9',
				   			  'Color'   =>"#727171")
				   		);
				$abNormarl = $rows['abQty'];
				$onedata=array(
					'tag'=>'notout',
					'Id'=>$rows['Month'],
					'segIndex'=>$buyerId,
					'showArrow'=>'1',
					'method'=>'ordered_subs',
					'title'=>$titleObj,
					'col4marginR'=>'10',
					'percent2'=>array('Text'=>$abNormarl>0? number_format($abNormarl):'','Color'=>'#ff0000','light'=>'13'),
					'percent2Y'=>'-13',
					'col1'=>number_format($rows['Qty']),
					'col1R'=>number_format($rows['Count']),
					'col2'=>'¥'.number_format($rows['Amount'])
					
				);
				
				if ($rows['noQty']>0) {
					$onedata['col3'] = number_format($rows['noQty']);
					$onedata['col3R'] =number_format($rows['noCount']);
					$onedata['col4'] = '¥'.number_format($rows['noAmount']);
					
				} else {
					$onedata['row1Y'] = '8'; 
					$onedata['percent2Y'] = '-3.5'; 
				}

				$sectionList[]=$onedata;	
			}
		}
		
		
		return $sectionList;
	}
	

	
	function cg_unrecieved_companys($buyerId='') {
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();


		$rs=$this->cg1stocksheetModel->cg_unrecieved_companys($buyerId);
		if ($rs != null) {
			foreach ($rs as $rows) {
				if ($rows['Count'] > 0 && $rows['Amount'] > 0)
				{
					
					$Forshort = $rows['Forshort'];
					$CompanyId = $rows['CompanyId'];
					$Logo = $rows['Logo'];
					
					
					$onedata=array(
						'tag'=>'notout',
						'Id'=>$rows['CompanyId'],
						'segIndex'=>$buyerId,
						'showArrow'=>'1',
						'method'=>'unrecieve_subweeks',
						'addval'=>$rows['Count'],
						//'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
						'title'=>$Forshort,
						'col4marginR'=>'20',
						'col1'=>number_format($rows['Qty']),
						'col1R'=>number_format($rows['Count']),
						'col2'=>'¥'.number_format($rows['Amount'])
						
					);
					
					if ($rows['OverCount']>0) {
						$onedata['col3'] = number_format($rows['OverQty']);
						$onedata['col3R'] =number_format($rows['OverCount']);
						$onedata['col4'] = '¥'.number_format($rows['OverAmount']);
					} else {
						$onedata['row1Y'] = '8'; 
					}
	
					$sectionList[]=$onedata;
				}
				
				
			}
		}
		
		
		return $sectionList;
	}
	
	function ordered_subs() {
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$month = element('Id',$params,'');
		
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();

		$rs=$this->cg1stocksheetModel->cg_ordered_companys($buyerId,$month);
		if ($rs != null) {
			foreach ($rs as $rows) {
				
				
				$Forshort = $rows['Forshort'];
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
				
				$abNormarl = $rows['abQty'];
				$onedata=array(
					'tag'=>'ch_total',
					'Id'=>$rows['CompanyId'],
					'segIndex'=>$buyerId,
					'type'=>$month,
					'showArrow'=>'1',
					'open'=>'',
					'method'=>'ordered_sublist',
					'addval'=>$rows['Count'],
					//'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
					'title'=>$Forshort,
					'col4marginR'=>'10',
					'percent2'=>array('Text'=>$abNormarl>0? number_format($abNormarl):'','Color'=>'#ff0000','light'=>'13'),
					'percent2Y'=>'-13',
					'col1'=>number_format($rows['Qty']),
					'col1R'=>number_format($rows['Count']),
					'col2'=>'¥'.number_format($rows['Amount'])
					
				);
				
				if ($rows['noQty']>0) {
					$onedata['col3'] = number_format($rows['noQty']);
					$onedata['col3R'] =number_format($rows['noCount']);
					$onedata['col4'] = '¥'.number_format($rows['noAmount']);
					
				} else {
					$onedata['row1Y'] = '8'; 
					$onedata['percent2Y'] = '-3.5'; 
				}

				$sectionList[]=$onedata;
			
			
				
			}
		}
		
		$data['jsondata']=array('status'=>'','message'=>"1",'rows'=>$sectionList);
		$this->load->view('output_json',$data);
	}
	
	function unrecieve_subweeks() {
		
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$CompanyId = element('Id',$params,'');
		$subList = $this->cg_unreceived_weeks($buyerId, $CompanyId);
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		$data['jsondata']=array('status'=>'','message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
	}
	
	function cg_unreceived_weeks($buyerId='', $CompanyId='') {
		
		
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		$rs=$this->cg1stocksheetModel->cg_unrecieved_weeks($buyerId,'',$CompanyId);
		if ($rs != null) {
			foreach ($rs as $rows) {
				if ($rows['Counts'] > 0 && $rows['Amount'] > 0)
				{
					
					$weeks = $rows['Weeks'];
					$sectionList[]=array(
						'tag'=>$CompanyId !=''?'ch_total':'notout',
						'Id'=>$weeks==''?'notsure':$weeks,
						'segIndex'=>$CompanyId!=''?''.$CompanyId:$buyerId,
						'type'=>$CompanyId,
						'showArrow'=>'1',
						'open'=>'',
						'row1Y'=>'8',
						'week'=>$weeks,
						'col4marginR'=>'20',
						'hasWeek'=>'1',
						'method'=>$CompanyId!=''?'cg_unreceivelist':'cg_unreceive_subs',
						'addval'=>$rows['Counts'],
						'weekTitle'=>$weeks==''?'交期待定':$this->getWeekToDate($weeks),
						'col1'=>number_format($rows['Qty']),
						'col1R'=>number_format($rows['Counts']),
						'col2'=>'¥'.number_format($rows['Amount']),
						
					);
				}
			}
		}
		return $sectionList;
	}
	
	function cg_unreceive_subs() {
		
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('TradeObjectModel');
		
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$weeks = element('Id',$params,'');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		$subList = array();
		$rs=$this->cg1stocksheetModel->cg_unrecieved_weeks($buyerId, $weeks);
		if ($rs != null) {
			foreach ($rs as $rows) {
				if ($rows['Counts'] > 0 && $rows['Amount'] > 0)
				{
					
					$Forshort = $rows['Forshort'];
					$CompanyId = $rows['CompanyId'];
					$Logo = $rows['Logo'];
					$subList[]=array(
						'tag'=>'ch_total',
						'open'=>'',
						'Id'=>$CompanyId.'|'.$weeks,
						'type'=>$buyerId,
						'showArrow'=>'1',
						'row1Y'=>'8',
						'method'=>'cg_unreceivelist',
						//'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
						'title'=>$Forshort,
						'col1'=>number_format($rows['Qty']),
						'col1R'=>number_format($rows['Counts']),
						'col2'=>'¥'.number_format($rows['Amount']),
						
					);
				}
			}
		}
		
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>'','message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);

	}
	
	function cgsend_months($buyerId='') {
		
		
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		$rs=$this->cg1stocksheetModel->cg_send_months($buyerId);
		if ($rs != null) {
			foreach ($rs as $rows) {
				
				$Month = $rows['Month'];
				$timeMon = strtotime($Month);
			    $titleObj['isAttribute']='1';
			    $titleObj['attrDicts']=array(
				   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
				   			  'FontSize'=>'14',
				   			  'FontWeight'=>'bold',
				   			  'Color'   =>"#3b3e41"),
				   		array('Text'    =>"\n".date('Y', $timeMon),
				   			  'FontSize'=>'9',
				   			  'Color'   =>"#727171")
				   		);
				$onedata=array(
					'tag'=>'notout',
					'Id'=>$rows['Month'],
					'segIndex'=>$buyerId,
					'showArrow'=>'1',
					'method'=>'cgsend_month_subs',
					'title'=>$titleObj,
					'col1'=>number_format($rows['Qty']),
					'col2'=>'¥'.number_format($rows['Amount'])
					
				);
				
				
				$onedata['row1Y'] = '8'; 
				

				$sectionList[]=$onedata;
				
			
				
			}
		}
		
		
		return $sectionList;
	}
	
	
	
	function cgunpay_months($buyerId='') {
		
		
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		$rs=$this->cg1stocksheetModel->cg_unpay_months($buyerId);
		$sumOverAmount = 0;
		$sumAmount = 0;
		if ($rs != null) {
			foreach ($rs as $rows) {
				
				$Month = $rows['Month'];
				$timeMon = strtotime($Month);
			    $titleObj['isAttribute']='1';
			    $titleObj['attrDicts']=array(
				   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
				   			  'FontSize'=>'14',
				   			  'FontWeight'=>'bold',
				   			  'Color'   =>"#3b3e41"),
				   		array('Text'    =>"\n".date('Y', $timeMon),
				   			  'FontSize'=>'9',
				   			  'Color'   =>"#727171")
				   		);
				$onedata=array(
					'tag'=>'notout',
					'Id'=>$rows['Month'],
					'segIndex'=>$buyerId,
					'showArrow'=>'1',
					'method'=>'cgunpay_month_subs',
					'title'=>$titleObj,
					'col1'=>array('Text'=>number_format($rows['OverAmount']),'Color'=>'#ff0000'),
					'col2'=>'¥'.number_format($rows['Amount'])
					
				);
				$sumOverAmount += $rows['OverAmount'];
				$sumAmount += $rows['Amount'];
				
				$onedata['row1Y'] = '8'; 
				

				$sectionList[]=$onedata;
				
			
				
			}
		}
		
		if ($sumAmount > 0) {
			$tmp = array();
			$tmp[]= array(
				'showArrow'=>'',
				'title'=>'合计',
				'tag'=>'notout',
				'row1Y'=>'8',
				'col1'=>array('Text'=>number_format($sumOverAmount),'Color'=>'#ff0000'),
				'col2'=>'¥'.number_format($sumAmount)
			);
			
			$sectionList = array_merge($tmp, $sectionList);
		}
		return $sectionList;
	}

// cg_unpay_companys
	function cgunpay_month_subs() {
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('TradeObjectModel');
		
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$month = element('Id',$params,'');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		$subList = array();
		$rs=$this->cg1stocksheetModel->cg_unpay_companys($buyerId,$month);
		if ($rs != null) {
			foreach ($rs as $rows) {

				{
					
					$Forshort = $rows['Forshort'];
					$CompanyId = $rows['CompanyId'];
					$Logo = $rows['Logo'];
					
					$GysPayMode = $rows["GysPayMode"];
	  
     $payText = "";
     
     switch ($GysPayMode) {
	     
	     case 0:
	     $payText = "30d";
	     break;
	     
	     case 1:
	     $payText = "现金";
	     break;
	    
	     case 2:
	     $payText = "60d";
	     break;
     }
     if ($payText!='') {
	     $payText = $payText."\n";
     }
	 				$titleObj['isAttribute']='1';
				    $titleObj['attrDicts']=array(
					   		array('Text'    =>$payText,
					   			  'FontSize'=>'10',
					   			  'Color'   =>"#358fc1"),
					   		array('Text'    =>$Forshort,
					   			  'FontSize'=>'13',
					   			  'Color'   =>"#3b3e41")
					   		);
					

					
					$subList[]=array(
						'tag'=>'ch_total',
						'open'=>'',
						'Id'=>$CompanyId,
						'type'=>$month,
						'showArrow'=>'1',
						'row1Y'=>'8',
						'method'=>'cg_unpaylist',
						'title'=>$titleObj,
/*
						'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
						'forshort'=>$Forshort,
*/
						'col1'=>array('Text'=>$rows['OverAmount']<=0?'':number_format($rows['OverAmount']),'Color'=>'#ff0000'),

						'col2'=>'¥'.number_format($rows['Amount']),
						
					);
				}
			}
		}
		
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		
		$data['jsondata']=array('status'=>'','message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}

	
	function cgsend_month_subs() {
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('TradeObjectModel');
		
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$weeks = element('Id',$params,'');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		$subList = array();
		$rs=$this->cg1stocksheetModel->cg_send_companys($weeks,$buyerId);
		if ($rs != null) {
			foreach ($rs as $rows) {

				{
					
					$Forshort = $rows['Forshort'];
					$CompanyId = $rows['CompanyId'];
					$Logo = $rows['Logo'];
					
					
					$Qty=$rows["Qty"];
					$OverQty=$rows["OverQty"];

				
				    $Percent_Color="#01be56";
				    $OverPercent=$Qty>0? round(($Qty-$OverQty)/$Qty*100) . "%":"";
				    $Percent_Color=$OverPercent<90?"#ff0000":$Percent_Color;
				      
					$subList[]=array(
						'tag'=>'ch_total',
						'open'=>'',
						'Id'=>$CompanyId.'|'.$weeks,
						'type'=>$buyerId,
						'showArrow'=>'1',
						'row1Y'=>'8',
						'method'=>'cg_sendlist',
						//'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
						'title'=>$Forshort,
						'percent'=>array('Text'=>$OverPercent, 'Color'=>$Percent_Color),
						'col1'=>number_format($Qty),
						'col2'=>'¥'.number_format($rows['Amount']),
						
					);
				}
			}
		}
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>'','message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	
	function cg_unpaylist() {
		
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->model('StuffPropertyModel');
		
		$this->load->library('datehandler');
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$month = element('type',$params,'');
		$companyId = element('Id',$params,'');
		 
		
		$rs=$this->cg1stocksheetModel->cg_unpay_sublist($buyerId, $month, $companyId);
		

		$afterDate = date('Y-m-d', strtotime('-90 days'));
		
		$subList = array();
		if ($rs != null) {
			
			foreach ($rs as $rows) {
				
				$StuffId = $rows['StuffId'];
				$StockId = $rows['StockId'];
				$Picture = $rows['Picture'];
				$POrderId = $rows['POrderId'];
				$DevelopState = $rows['DevelopState'];
				$PurchaseID = $rows['PurchaseID'];
				

	            $Qty=$rows["Qty"];//采购数量

				
				$iconImg = '';
				if ($Picture == 1) {
					$iconImg = $this->stuffDataModel->get_stuff_icon($StuffId);
				}
				
				

				
				$processDict = $this->cg1stocksheetModel->cg_process_2($StockId,$StuffId);
				
				
				$lockArray = $this->cg1stocksheetModel->get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState);
				
				$evenPunc = element("$StuffId", $this->StuffPuncInfo, null);
				if ($evenPunc == null) {
					
					$evenPunc = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId, $afterDate);
					

					$this->StuffPuncInfo["$StuffId"] = $evenPunc;
				}
				
				
				$flagImg = '';
				if ($evenPunc!=null &&$evenPunc < 85) {
					$flagImg = 'cg_list';
				}
				
				if ($flagImg == '') {
					$this->load->model('QcBadrecordModel');
					
					$allAndBadInfo = $this->QcBadrecordModel->get_allandbad($StuffId);
					$allshQty = $allAndBadInfo['allQty'];
					$allbadQty = $allAndBadInfo['badQty'];
					$allGoodQty = $allshQty - $allbadQty;
					
					$badPercent =
					$goodPercent = '--';
					if ($allshQty > 0) {
						$badPercent = round( $allbadQty / $allshQty *100 );
						
						if ($badPercent > 20) {
							$flagImg = 'cg_list';
						}
					}

					
				}
				
				$lock = element('lock',$lockArray,0);
				
				$Remark="";
				$oper1 = '';
				

				$remarkInfo = null;
				$cg_remark = $this->cg1stocksheetModel->cg_remarkinfo($StockId);
				if ($cg_remark != null) {
					$Remark = $Remark == '' ? '':$Remark.'  ';
					$Remark.= $cg_remark['Remark'];
					$oper1 = date('m/d', strtotime($cg_remark['Date'])).'  '.$cg_remark['Name'];
				}
				
				if ($Remark != '') {
					$remarkInfo = array(
						'content'=>$Remark,
						'img'=>'remark1',
						'oper'=>$oper1
									
					);
				}
				
				$lockImg = '';
				$oper = 
				$operDate = 
				$remark = '';
				$remarkIcon = '';
				
				$sortNumber = 0;
				
				if ($lock > 0) {
					$oper     = element('oper',$lockArray,'');
					$operDate = element('date',$lockArray,'');
					$remark   = element('remark',$lockArray,'');
					switch ($lock) {
						case 9:
							$sortNumber = 10000000000000;
							$lockImg = 'cg_dev_lock';
							$remarkIcon = 'cg_dev_remark';
						break;
						case 1:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock';
							$remarkIcon = 'rmk_orderlock';
						break;
						case 2:
						case 3:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock_s';
							$remarkIcon = 'rmk_stufflock';
						break;
						case 8:
							$sortNumber = 9000000000000;
							$lockImg = 'cg_needlocks';
							$remarkIcon = 'cg_neededlock';
						break;
						default:
						break;
					}
					if ($remarkInfo == null) {
						$remarkInfo = array(
							'content'=>$remark,
							'oper'=>date('m-d', strtotime($operDate)).' '.$oper,
							'img'=>$remarkIcon
						);
					} else {
						$remarkInfo['content2']=$remark;
						$remarkInfo['oper2']=date('m-d', strtotime($operDate)).' '.$oper;
						$remarkInfo['img2']=$remarkIcon;
					}
					
				}
				
				$sortNumber += intval($POrderId);
				
				$weekChanged = $this->cg1stocksheetModel->check_week_changed($StockId);
				$weekColor = $weekChanged == true ? '#54BCE5':'';
				$Property = $this->StuffPropertyModel->get_property($StuffId);
				$subList[]=array(
					'tag'=>'cg_order',
					'weekColor'=>$weekColor,
					'showArrow'=>'1',
					'srt'=>$sortNumber,
					'arrowImg'=>'arrow_gray_s',
					'open'=>'',
					'Id'=>''.$StockId,
					'StockId'=>''.$StockId,
					'type'=>''.$StuffId,
					'method'=>'sub_pages',
					'col1Img'=>'',
					'col2Img'=>'',
					'col3Img'=>'',
					'lockImg'=>$lockImg,
					'completeImg'=>$flagImg,
					'flagScale'=>'0.75',
					'lockBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					
					'flagBeling'=>array('beling'=>$flagImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'remarkInfo'=>$remarkInfo,
					'week'=>$rows['Weeks'],
					'Property'=>$Property,
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'iconImg'=>$iconImg,
					'Picture'=>$Picture==1?'1':'',
					'col2'=>number_format($rows['Qty']),
					'col4'=>$rows['PreChar'].(number_format($rows['Price']*$rows['Qty'])),
					'col3'=>array(
						'Text'=>$rows['PreChar'].$rows['Price'],
						'Color'=>'#3b3e41'
					),
					'col1'=>$PurchaseID,
					'Legend'=>$processDict['process']
					
				);
				
				
			}

			
		}
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		$data['jsondata']=array('status'=>'','message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);

		
	}
	
	function cg_send_sublist($buyerId, $companyId, $checkMonth) {
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->model('StuffPropertyModel');
		$this->load->model('CkrksheetModel');
		
		$this->load->library('datehandler');
		$rs=$this->cg1stocksheetModel->cg_send_sublist($checkMonth, $buyerId, $companyId);
		

		$afterDate = date('Y-m-d', strtotime('-90 days'));
		/*
			A.StockId,S.StuffId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       A.Qty,A.rkQty,S.Price,D.StuffCname,D.Picture,D.DevelopState,C.Rate,C.PreChar,R.Mid  
	     FROM
	     
	     SELECT S.StockId,G.StuffId,S.Qty,(G.FactualQty+G.AddQty) AS cgQty,G.Price,YEARWEEK(G.DeliveryDate,1) AS Weeks,
YEARWEEK(M.rkDate,1) AS  rkWeeks,
DATE_FORMAT(M.rkDate,'%m-%d') rkDateTitle,
GM.PurchaseID,P.Forshort,D.StuffCname,D.Picture,D.DevelopState,C.Rate,C.PreChar  
		*/ 
		
		$subList = array();
		if ($rs != null) {
			
			foreach ($rs as $rows) {
				
				$StuffId = $rows['StuffId'];
				$StockId = $rows['StockId'];
				$Picture = $rows['Picture'];
				$POrderId = $rows['POrderId'];
				$DevelopState = $rows['DevelopState'];
				$PurchaseID = $rows['PurchaseID'];
				$rkDateTitle =$rows['rkDateTitle']; 
				$rkWeeks =$rows['rkWeeks']; 
				
				$rkQty=$rows["Qty"];//入库数量
	            $Qty=$rows["cgQty"];//采购数量
				$iconImg = '';
				if ($Picture == 1) {
					$iconImg = $this->stuffDataModel->get_stuff_icon($StuffId);
				}
				
				

				
				$processDict = $this->cg1stocksheetModel->cg_process_2($StockId,$StuffId);
				
				
				$lockArray = $this->cg1stocksheetModel->get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState);
				
				$evenPunc = element("$StuffId", $this->StuffPuncInfo, null);
				if ($evenPunc == null) {
					
					$evenPunc = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId, $afterDate);
					

					$this->StuffPuncInfo["$StuffId"] = $evenPunc;
				}
				
				
				$flagImg = '';
				if ($evenPunc!=null &&$evenPunc < 85) {
					$flagImg = 'cg_list';
				}
				if ($flagImg == '') {
					$this->load->model('QcBadrecordModel');
					
					$allAndBadInfo = $this->QcBadrecordModel->get_allandbad($StuffId);
					$allshQty = $allAndBadInfo['allQty'];
					$allbadQty = $allAndBadInfo['badQty'];
					$allGoodQty = $allshQty - $allbadQty;
					
					$badPercent =
					$goodPercent = '--';
					if ($allshQty > 0) {
						$badPercent = round( $allbadQty / $allshQty *100 );
						
						if ($badPercent > 20) {
							$flagImg = 'cg_list';
						}
					}

					
				}
				
				
				$lock = element('lock',$lockArray,0);
				
				$Remark="";
				$oper1 = '';
				

				$remarkInfo = null;
				$cg_remark = $this->cg1stocksheetModel->cg_remarkinfo($StockId);
				if ($cg_remark != null) {
					$Remark = $Remark == '' ? '':$Remark.'  ';
					$Remark.= $cg_remark['Remark'];
					$oper1 = date('m/d', strtotime($cg_remark['Date'])).'  '.$cg_remark['Name'];
				}
				
				if ($Remark != '') {
					$remarkInfo = array(
						'content'=>$Remark,
						'img'=>'remark1',
						'oper'=>$oper1
									
					);
				}
				
				$lockImg = '';
				$oper = 
				$operDate = 
				$remark = '';
				$remarkIcon = '';
				
				$sortNumber = 0;
				
				if ($lock > 0) {
					$oper     = element('oper',$lockArray,'');
					$operDate = element('date',$lockArray,'');
					$remark   = element('remark',$lockArray,'');
					switch ($lock) {
						case 9:
							$sortNumber = 10000000000000;
							$lockImg = 'cg_dev_lock';
							$remarkIcon = 'cg_dev_remark';
						break;
						case 1:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock';
							$remarkIcon = 'rmk_orderlock';
						break;
						case 2:
						case 3:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock_s';
							$remarkIcon = 'rmk_stufflock';
						break;
						case 8:
							$sortNumber = 9000000000000;
							$lockImg = 'cg_needlocks';
							$remarkIcon = 'cg_neededlock';
						break;
						default:
						break;
					}
					if ($remarkInfo == null) {
						$remarkInfo = array(
							'content'=>$remark,
							'oper'=>date('m-d', strtotime($operDate)).' '.$oper,
							'img'=>$remarkIcon
						);
					} else {
						$remarkInfo['content2']=$remark;
						$remarkInfo['oper2']=date('m-d', strtotime($operDate)).' '.$oper;
						$remarkInfo['img2']=$remarkIcon;
					}
					
				}
				
				$rkQtyIn = $this->CkrksheetModel->get_rked_qty($StockId);
				
				$sortNumber += intval($POrderId);
				
				$weekChanged = $this->cg1stocksheetModel->check_week_changed($StockId);
				$weekColor = $weekChanged == true ? '#54BCE5':'';
				$Property = $this->StuffPropertyModel->get_property($StuffId);
				$subList[]=array(
					'tag'=>'cg_order',
					'weekColor'=>$weekColor,
					'showArrow'=>'1',
					'srt'=>$sortNumber,
					'arrowImg'=>'arrow_gray_s',
					'open'=>'',
					'Id'=>''.$StockId,
					'StockId'=>''.$StockId,
					'type'=>''.$StuffId,
					'method'=>'sub_pages',
					'week_s'=>array('week'=>$rkWeeks),
					'weekleft'=>$rkDateTitle,
					'weekleftX'=>'-22',
					'weekleftY'=>'2',
					'col1Img'=>'',
					'col2Img'=>'icgdate',
					'col3Img'=>'cg_rked',
					'lockImg'=>$lockImg,
					'completeImg'=>$flagImg,
					'flagScale'=>'0.75',
					'lockBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'flagBeling'=>array('beling'=>$flagImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'remarkInfo'=>$remarkInfo,
					'week'=>$rows['Weeks'],
					'Property'=>$Property,
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'iconImg'=>$iconImg,
					'Picture'=>$Picture==1?'1':'',
					'col2'=>number_format($Qty),
					'col4'=>$rows['PreChar'].($Qty*$rows['Price']),
					'col3'=>array(
						'Text'=>number_format($rkQty),
						'Color'=>$rkQtyIn==$rkQty?'#01be56':'#3b3e41'
					),
					'col1'=>$PurchaseID,
					'Legend'=>$processDict['process']
					
				);	
			}	
		}
		return $subList;
	}
	
	function ordered_sublist() {
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->model('StuffPropertyModel');
		
		$this->load->library('datehandler');
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');
		$month = element('type',$params,'');
		$companyId = element('Id',$params,'');
		 
		
		$rs=$this->cg1stocksheetModel->cg_ordered_sublist($buyerId, $month, $companyId);

		$afterDate = date('Y-m-d', strtotime('-90 days'));
		
		$subList = array();
		if ($rs != null) {
			
			foreach ($rs as $rows) {
				
				$StuffId = $rows['StuffId'];
				$StockId = $rows['StockId'];
				$Picture = $rows['Picture'];
				$POrderId = $rows['POrderId'];
				$DevelopState = $rows['DevelopState'];
				$PurchaseID = $rows['PurchaseID'];
				
				$rkQty=$rows["rkQty"];//送货+入库数量
	            $Qty=$rows["Qty"];//采购数量
	            $noSendQty=$Qty-$rkQty;
				
				$iconImg = '';
				if ($Picture == 1) {
					$iconImg = $this->stuffDataModel->get_stuff_icon($StuffId);
				}
				$processDict = $this->cg1stocksheetModel->cg_process_2($StockId,$StuffId);
				
				
				$lockArray = $this->cg1stocksheetModel->get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState);
				
				$evenPunc = element("$StuffId", $this->StuffPuncInfo, null);
				if ($evenPunc == null) {
					
					$evenPunc = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId, $afterDate);
					

					$this->StuffPuncInfo["$StuffId"] = $evenPunc;
				}
				
				
				$flagImg = '';
				if ($evenPunc!=null &&$evenPunc < 85) {
					$flagImg = 'cg_list';
				}
				
				if ($flagImg == '') {
					$this->load->model('QcBadrecordModel');
					
					$allAndBadInfo = $this->QcBadrecordModel->get_allandbad($StuffId);
					$allshQty = $allAndBadInfo['allQty'];
					$allbadQty = $allAndBadInfo['badQty'];
					$allGoodQty = $allshQty - $allbadQty;
					
					$badPercent =
					$goodPercent = '--';
					if ($allshQty > 0) {
						$badPercent = round( $allbadQty / $allshQty *100 );
						
						if ($badPercent > 20) {
							$flagImg = 'cg_list';
						}
					}

					
				}
				
				$lock = element('lock',$lockArray,0);
				
				$Remark="";
				$oper1 = '';
				

				$remarkInfo = null;
				$cg_remark = $this->cg1stocksheetModel->cg_remarkinfo($StockId);
				if ($cg_remark != null) {
					$Remark = $Remark == '' ? '':$Remark.'  ';
					$Remark.= $cg_remark['Remark'];
					$oper1 = date('m/d', strtotime($cg_remark['Date'])).'  '.$cg_remark['Name'];
				}
				
				if ($Remark != '') {
					$remarkInfo = array(
						'content'=>$Remark,
						'img'=>'remark1',
						'oper'=>$oper1
									
					);
				}
				
				$lockImg = '';
				$oper = 
				$operDate = 
				$remark = '';
				$remarkIcon = '';
				
				$sortNumber = 0;
				
				if ($lock > 0) {
					$oper     = element('oper',$lockArray,'');
					$operDate = element('date',$lockArray,'');
					$remark   = element('remark',$lockArray,'');
					switch ($lock) {
						case 9:
							$sortNumber = 10000000000000;
							$lockImg = 'cg_dev_lock';
							$remarkIcon = 'cg_dev_remark';
						break;
						case 1:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock';
							$remarkIcon = 'rmk_orderlock';
						break;
						case 2:
						case 3:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock_s';
							$remarkIcon = 'rmk_stufflock';
						break;
						case 8:
							$sortNumber = 9000000000000;
							$lockImg = 'cg_needlocks';
							$remarkIcon = 'cg_neededlock';
						break;
						default:
						break;
					}
					if ($remarkInfo == null) {
						$remarkInfo = array(
							'content'=>$remark,
							'oper'=>date('m-d', strtotime($operDate)).' '.$oper,
							'img'=>$remarkIcon
						);
					} else {
						$remarkInfo['content2']=$remark;
						$remarkInfo['oper2']=date('m-d', strtotime($operDate)).' '.$oper;
						$remarkInfo['img2']=$remarkIcon;
					}
					
				}
				
				$sortNumber += intval($POrderId);
				
				$weekChanged = $this->cg1stocksheetModel->check_week_changed($StockId);
				$weekColor = $weekChanged == true ? '#54BCE5':'';
				$Property = $this->StuffPropertyModel->get_property($StuffId);
				$subList[]=array(
					'tag'=>'cg_order',
					'weekColor'=>$weekColor,
					'showArrow'=>'1',
					'srt'=>$sortNumber,
					'arrowImg'=>'arrow_gray_s',
					'open'=>'',
					'Id'=>''.$StockId,
					'StockId'=>''.$StockId,
					'type'=>''.$StuffId,
					'method'=>'sub_pages',
					'col1Img'=>'',
					'col2Img'=>'',
					'col3Img'=>'',
					'lockImg'=>$lockImg,
					'completeImg'=>$flagImg,
					'flagScale'=>'0.75',
					'lockBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'flagBeling'=>array('beling'=>$flagImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'remarkInfo'=>$remarkInfo,
					'week'=>$rows['Weeks'],
					'Property'=>$Property,
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'iconImg'=>$iconImg,
					'Picture'=>$Picture==1?'1':'',
					'col2'=>number_format($rows['Qty']),
					'col4'=>$rows['PreChar'].$rows['Price'],
					'col3'=>array(
						'Text'=>number_format($noSendQty),
						'Color'=>'#ff0000'
					),
					'col1'=>$PurchaseID,
					'Legend'=>$processDict['process']
					
				);
				
				
			}

			
		}
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		$data['jsondata']=array('status'=>''.$rownums,'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
	}
	
	function cg_unreceive_sublist($buyerId, $companyId, $weeks) {
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->model('StuffPropertyModel');
		
		$this->load->library('datehandler');
		$rs=$this->cg1stocksheetModel->cg_unrecieved_list($buyerId, $weeks, $companyId);
		

		$afterDate = date('Y-m-d', strtotime('-90 days'));
		/*
			A.StockId,S.StuffId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       A.Qty,A.rkQty,S.Price,D.StuffCname,D.Picture,D.DevelopState,C.Rate,C.PreChar,R.Mid  
	     FROM
		*/ 
		
		$subList = array();
		if ($rs != null) {
			
			foreach ($rs as $rows) {
				
				$StuffId = $rows['StuffId'];
				$StockId = $rows['StockId'];
				$Picture = $rows['Picture'];
				$POrderId = $rows['POrderId'];
				$DevelopState = $rows['DevelopState'];
				$PurchaseID = $rows['PurchaseID'];
				
				$rkQty=$rows["rkQty"];//送货+入库数量
	            $Qty=$rows["Qty"];//采购数量
	            $noSendQty=$Qty-$rkQty;
				
				$iconImg = '';
				if ($Picture == 1) {
					$iconImg = $this->stuffDataModel->get_stuff_icon($StuffId);
				}
				
				

				
				$processDict = $this->cg1stocksheetModel->cg_process_2($StockId,$StuffId);
				
				
				$lockArray = $this->cg1stocksheetModel->get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState);
				
				$evenPunc = element("$StuffId", $this->StuffPuncInfo, null);
				if ($evenPunc == null) {
					
					$evenPunc = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId, $afterDate);
					

					$this->StuffPuncInfo["$StuffId"] = $evenPunc;
				}
				
				
				$flagImg = '';
				if ($evenPunc!=null &&$evenPunc < 85) {
					$flagImg = 'cg_list';
				}
				
				if ($flagImg == '') {
					$this->load->model('QcBadrecordModel');
					
					$allAndBadInfo = $this->QcBadrecordModel->get_allandbad($StuffId);
					$allshQty = $allAndBadInfo['allQty'];
					$allbadQty = $allAndBadInfo['badQty'];
					$allGoodQty = $allshQty - $allbadQty;
					
					$badPercent =
					$goodPercent = '--';
					if ($allshQty > 0) {
						$badPercent = round( $allbadQty / $allshQty *100 );
						
						if ($badPercent > 20) {
							$flagImg = 'cg_list';
						}
					}

					
				}
				
				$lock = element('lock',$lockArray,0);
				
				$Remark="";
				$oper1 = '';
				

				$remarkInfo = null;
				$cg_remark = $this->cg1stocksheetModel->cg_remarkinfo($StockId);
				if ($cg_remark != null) {
					$Remark = $Remark == '' ? '':$Remark.'  ';
					$Remark.= $cg_remark['Remark'];
					$oper1 = date('m/d', strtotime($cg_remark['Date'])).'  '.$cg_remark['Name'];
				}
				
				if ($Remark != '') {
					$remarkInfo = array(
						'content'=>$Remark,
						'img'=>'remark1',
						'oper'=>$oper1
									
					);
				}
				
				$lockImg = '';
				$oper = 
				$operDate = 
				$remark = '';
				$remarkIcon = '';
				
				$sortNumber = 0;
				
				if ($lock > 0) {
					$oper     = element('oper',$lockArray,'');
					$operDate = element('date',$lockArray,'');
					$remark   = element('remark',$lockArray,'');
					switch ($lock) {
						case 9:
							$sortNumber = 10000000000000;
							$lockImg = 'cg_dev_lock';
							$remarkIcon = 'cg_dev_remark';
						break;
						case 1:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock';
							$remarkIcon = 'rmk_orderlock';
						break;
						case 2:
						case 3:
							$sortNumber = 20000000000000;
							$lockImg = 'order_lock_s';
							$remarkIcon = 'rmk_stufflock';
						break;
						case 8:
							$sortNumber = 9000000000000;
							$lockImg = 'cg_needlocks';
							$remarkIcon = 'cg_neededlock';
						break;
						default:
						break;
					}
					if ($remarkInfo == null) {
						$remarkInfo = array(
							'content'=>$remark,
							'oper'=>date('m-d', strtotime($operDate)).' '.$oper,
							'img'=>$remarkIcon
						);
					} else {
						$remarkInfo['content2']=$remark;
						$remarkInfo['oper2']=date('m-d', strtotime($operDate)).' '.$oper;
						$remarkInfo['img2']=$remarkIcon;
					}
					
				}
				
				$sortNumber += intval($POrderId);
				
				$weekChanged = $this->cg1stocksheetModel->check_week_changed($StockId);
				$weekColor = $weekChanged == true ? '#54BCE5':'';
				$Property = $this->StuffPropertyModel->get_property($StuffId);
				$subList[]=array(
					'tag'=>'cg_order',
					'weekColor'=>$weekColor,
					'showArrow'=>'1',
					'srt'=>$sortNumber,
					'arrowImg'=>'arrow_gray_s',
					'open'=>'',
					'Id'=>''.$StockId,
					'StockId'=>''.$StockId,
					'type'=>''.$StuffId,
					'method'=>'sub_pages',
					'col1Img'=>'',
					'col2Img'=>'',
					'col3Img'=>'',
					'lockImg'=>$lockImg,
					'completeImg'=>$flagImg,
					'flagScale'=>'0.75',
					'lockBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'flagBeling'=>array('beling'=>$flagImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
					'remarkInfo'=>$remarkInfo,
					'week'=>$rows['Weeks'],
					'Property'=>$Property,
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'iconImg'=>$iconImg,
					'Picture'=>$Picture==1?'1':'',
					'col2'=>number_format($rows['Qty']),
					'col4'=>$rows['PreChar'].$rows['Price'],
					'col3'=>array(
						'Text'=>number_format($noSendQty),
						'Color'=>'#ff0000'
					),
					'col1'=>$PurchaseID,
					'Legend'=>$processDict['process']
					
				);
				
				
			}

			
		}
		
		usort($subList, function($a, $b) {
	            $al = ($a['srt']);
	            $bl = ($b['srt']);
	            if ($al == $bl)
	                return 0;
	            return ($al > $bl) ? 1 : -1;
	        });
		
		return $subList;

		
	}
	
		
	
	function waitcg_subs() {
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');

		$companyIdweek = element('Id',$params,'');
		$companyIdweek = explode('|', $companyIdweek);

		$subList = $this->waitcg_sublist($buyerId, $companyIdweek[0],$companyIdweek[1]);
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		$data['jsondata']=array('status'=>''.$rownums,'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	
	function cg_unreceivelist() {
		
		$params = $this->input->post();
		

		$companyId = element('type',$params,'');

		$buyerId = element('buyerId',$params,'');
		$week = element('Id',$params,'');
		
		$subList = $this->cg_unreceive_sublist($buyerId, $companyId, $week);
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		
		$data['jsondata']=array('status'=>''.$rownums,'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	
	
	function cg_sendlist() {
		
		$params = $this->input->post();
		

		$companyId = element('type',$params,'');

		$buyerId = element('buyerId',$params,'');


		$companyIdweek = element('Id',$params,'');
		$companyIdweek = explode('|', $companyIdweek);

		$subList = $this->cg_send_sublist($buyerId, $companyIdweek[0],$companyIdweek[1]);
		
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>''.$rownums,'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	
	
	public function personMain() {
		
		$status = '';
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');


		$seg_id = element('seg_id',$params,'0');
		
		$sectionList = array();
		$cts = '';
		$titleObj = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>'待采', 'FontSize'=>'9'),
				array('Text'=>' '.$cts, 'FontSize'=>'8','Color'=>'#727171')
			));
		switch ($seg_id) {
			case 0:{
				$cts = 0;

				$sectionList = $this->waitcg_companys($buyerId);
				foreach ($sectionList as $rows) {
					$cts += $rows['addval'];
				}
			}
			
			break;
			
			case 1:{
				$cts = 0;
				$titleObj['attrDicts'][0]['Text'] = '未收';
				$sectionList = $this->cg_unreceived_weeks($buyerId);
				foreach ($sectionList as $rows) {
					$cts += $rows['addval'];
				}
				
			}
			break;
		}
		
		$menuRows = null;
		if ($cts != '') {
			
		    $menuRows = array();
		    
		    for ($i = 0 ; $i < $seg_id; $i ++) {
			    $menuRows[]=array('none'=>'');
		    }
		    
		    $titleObj['attrDicts'][1]['Text'] = ' '.$cts;
		    
		    $menuRows[]=array('title'=>$titleObj);
	    

		}
		
		
		$data['jsondata']=array('status'=>$status,'message'=>"1",'rows'=>$sectionList,'menu'=>$menuRows);
		$this->load->view('output_json',$data);
		
		
	}
	
	function get_k_qty($val) {
	    $unit = '';
	    if ($val > 10000) {
		    $val = number_format(round($val / 1000)) ;
		    $unit = 'k';
	    } else {
		    $val = number_format(round($val)) ;
	    }
	    return $val.$unit;
    }
	
	
	
	
	function subList() {
	
	//sleep(1);
		
		$params = $this->input->post();
		$ISPAD = element('ISPAD', $params , '0');
		$capacity = $ISPAD == 1 ? 5 : 3;
		
		$subList = array();
		
		$limitted = 3;
		$upTag = element('upTag', $params , '0');
		$segmentIndex = element('segmentIndex', $params , '-1');
		 
	    if ($upTag != 'loadall') {
			$subList[]=array('tag'=>'estate','bg_color'=>'#efefef');
			
			$subList[]=array(
						'tag'=>'subimg',
						'height'=>'55',
						'lineLeft'=>'76',
						'padLeft'=>'76',
						
						'imgs'=>array(),'bgcolor'=>'#efefef'
						);
						$subList[]=array('tag'=>'estate','bg_color'=>'#efefef');
			
			$subList[]=array(
						'tag'=>'subimg',
						'height'=>'55',
						'lineLeft'=>'76',
						'padLeft'=>'76',
						
						'imgs'=>array(),'bgcolor'=>'#efefef'
						);
		}
	    
	    
	    
	    $subList[count($subList)-1]['deleteTag'] = $upTag;
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$subList);
	    
		$this->load->view('output_json',$data);
	
}
	
	
	
	function sub_pages() {
		
		
			
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->library('datehandler');
		$this->load->model('QcBadrecordModel');
		
		
		$data = array();
		$pages = array();
		$rightCharts=array();
		
		$now = strtotime('now');
		
		$params = $this->input->post();
		$StockId = element('Id',$params,'');

$StockId2 = element('StockId',$params,'');
if (strlen($StockId2)>=14) {
	$StockId = $StockId2;
}
		$StuffId = element('type',$params,'0');
		
/*
 $StockId = '201606160019014';
 $StuffId = '90259';
*/
/*
if ($this->LoginNumber == 11965) {
	 $StockId = '201606160019014';
 $StuffId = '95703';
}
*/

		$StuffIdRow = $this->stuffDataModel->get_records($StuffId);
		$UnitName = strtolower( $StuffIdRow['UnitName'] );
		$rs = $this->cg1stocksheetModel->get_stock_process_all($StockId);
		$StockProcess = array();
		
		$firstTime = '';
		$lastTime = '';
		
		$newWeeks = '';
		if ($rs != null) {
			foreach ($rs as $rows) {
				if ($lastTime == '') {
					$lastTime = $rows['date'];
				}
				$firstTime = $rows['date'];
				
				
				if ($rows['keyval'] == 11) {
					
						
					$oldweek = $rows['title'];
					$oldweek = substr($oldweek, 4,2);
					
					
					
					if ($newWeeks == '') {
						$newWeeks = $rows['otherid'];
						$newWeeks = substr($newWeeks, 4,2);
						
						$newtitle = '原交期'. $oldweek . '周改现交期'. $newWeeks.'周';
						$rows['title'] = $newtitle;
						$newWeeks = $oldweek;
					} else {
						
						$newtitle = '交期由'. $oldweek . '周改为交期'. $newWeeks.'周';
						$rows['title'] = $newtitle;
						
						$newWeeks = $oldweek;
						
					}
					
					
					// 
					$operatorCom = explode('|||', $rows['operator']);
					if (count($operatorCom) == 2) {
						$rows['operator'] = $operatorCom[1];
						if ($operatorCom[0]!='') {
							$rows['title'] = $rows['title']."\n备注:".$operatorCom[0];
						}
						
					}
					
						
					
				}
				
				
				/*
					if ($this->LoginNumber == 11965) {
					$StockProcess[]=array(
					'tag'=>'estate',
					'addImg'=>'greenMinus',
					'imgLeft'=>'30',
					'position'=>'B04',
					'img'=>$rows['img'],
					'desc'=>$rows['title'],
					'oper'=>$rows['operator'],
					'date'=>array(
						'dateCom'=>$firstTime == ''?null: explode('-', date('y-m-d', strtotime($firstTime))),
						'eachFrame'=>'0,12.5,13,12'
					),
					
				);
				
				} else 
				*/
				{
					$StockProcess[]=array(
					'tag'=>'estate', 
					'img'=>$rows['img'],
					'desc'=>$rows['title'],
					'oper'=>$rows['operator'],
					'dateCompos'=>$firstTime == ''?null: explode('-', date('y-m-d', strtotime($firstTime))),
				);
				
				}
				
				if ($rows['otherid']!='' && $rows['keyval'] == 5) {
					
					
					$imgsBad = $this->QcBadrecordModel->get_badpictures($rows['otherid']);
					if (count($imgsBad) > 0) {
						$StockProcess[count($StockProcess)-1]['hideLine']='1';
						$StockProcess[]=array(
						'tag'=>'subimg',
						'height'=>'55',
						'lineLeft'=>'76',
						'padLeft'=>'76',
						'imgs'=>$imgsBad
						);
					}
					
				}
				
			
			}
		}
		$timeInfo = null;
		if ($firstTime != '' && $firstTime != $lastTime) {
			$timeInfo = $this->datehandler->timediff_arr($firstTime, $lastTime);
			$timeString = ($timeInfo['day']<99?sprintf("%02d", $timeInfo['day']):$timeInfo['day']).'天'
						   .sprintf("%02d", $timeInfo['hour']).'时'
						   .sprintf("%02d", $timeInfo['min']).'分';
						   
			if (count($StockProcess) > 0) {
				$StockProcess[0]['top'] = $timeString;
			}
		}
		
		
		
		
				
		$pages[]=$StockProcess;		
		$valsPunc = array();
		$thisMonthTime = strtotime('-0 month');
		$lastMonthTime1 = strtotime('-1 month');
		$lastMonthTime2 = strtotime('-2 month');
		//$lastMonthTime3 = strtotime('-3 month');
		
		$thisMonth = date('Y-m');
		$lastMonth1 = date('Y-m', $lastMonthTime1);
		$lastMonth2 = date('Y-m', $lastMonthTime2);
		//$lastMonth3 = date('Y-m', $lastMonthTime3);
		
		
		$rs = $this->cg1stocksheetModel->get_punc_months($StuffId);
		if ($rs != null) {
			$iter = 0;
			foreach ($rs as $rows) {
				
				$tempmonth= $rows['month'];
				$temptime = strtotime($rows['month']);
				switch ($iter) {
					case 0: $thisMonth=$tempmonth; $thisMonthTime = $temptime; break;
					case 1: $lastMonth1=$tempmonth; $lastMonthTime1 = $temptime; break;
					case 2: $lastMonth2=$tempmonth; $lastMonthTime2 = $temptime; break;
				}
				$iter ++;
			}
		}
		
		
		if ($lastMonthTime2 > $lastMonthTime1) {
			$lastMonthTime2 = strtotime('-1 month', $lastMonthTime1);
			$lastMonth2 = date('Y-m', $lastMonthTime2);
		}
		$defNum = 3;
		$pucval0 = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId,'', $thisMonth);
		if ($pucval0==null) {
			$defNum --;
		}
		$pucval1 = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId,'', $lastMonth1);
		if ($pucval1==null) {
			$defNum --;
		}
		$pucval2 = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId,'', $lastMonth2);
		if ($pucval2==null) {
			$defNum --;
		}
		$evenPunc = '--';
		if ($defNum > 0) {
			$evenPunc = ($pucval0 + $pucval1 + $pucval2)/$defNum;

		}
		
		
		$puncColor = $evenPunc >= 90 ? '#01be56':'#ff0000';
		$attri = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>$evenPunc!='--'?''.round( $evenPunc):'--','FontName'=>'AshCloud61','FontSize'=>'20','Color'=>$puncColor),
				array('Text'=>'%','FontName'=>'AshCloud61','FontSize'=>'6','Color'=>$puncColor)
			)
		);
		if ($evenPunc< 90) {
			$puncColor = '#ffa9a5';
		}
		$valsPunc[]=array('title'=>'平均',
			'percent'=>$attri,
			'charts'=>array(
				array('value'=>''.$evenPunc,'color'=>$puncColor),
				array('value'=>100-$evenPunc,'color'=>'#clear'),
			)
		);
		
		$puncColor = $pucval0 >= 90 ? '#01be56':'#ff0000';
		$attri = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>$pucval0==null?'--':''.round( $pucval0),'FontName'=>'AshCloud61','FontSize'=>'20','Color'=>$puncColor),
				array('Text'=>'%','FontName'=>'AshCloud61','FontSize'=>'6','Color'=>$puncColor)
			)
		);
		if ($pucval0< 90) {
			$puncColor = '#ffa9a5';
		}
		$valsPunc[]=array('title'=>intval(date('m',$thisMonthTime)). '月',
			'percent'=>$attri,
			'charts'=>array(
				array('value'=>''.$pucval0,'color'=>$puncColor),
				array('value'=>100-$pucval0,'color'=>'#clear'),
			)
		);
		
		$puncColor = $pucval1 >= 90 ? '#01be56':'#ff0000';
		$attri = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>$pucval1==null?'--':''.round( $pucval1),'FontName'=>'AshCloud61','FontSize'=>'20','Color'=>$puncColor),
				array('Text'=>'%','FontName'=>'AshCloud61','FontSize'=>'6','Color'=>$puncColor)
			)
		);
		if ($pucval1< 90) {
			$puncColor = '#ffa9a5';
		}
		$valsPunc[]=array('title'=>intval(date('m', $lastMonthTime1)). '月',
			'percent'=>$attri,
			'charts'=>array(
				array('value'=>''.$pucval1,'color'=>$puncColor),
				array('value'=>100-$pucval1,'color'=>'#clear'),
			)
		);
		$puncColor = $pucval2 >= 90 ? '#01be56':'#ff0000';
		$attri = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>$pucval2==null?'--':''.round( $pucval2),'FontName'=>'AshCloud61','FontSize'=>'20','Color'=>$puncColor),
				array('Text'=>'%','FontName'=>'AshCloud61','FontSize'=>'6','Color'=>$puncColor)
			)
		);
		if ($pucval2< 90) {
			$puncColor = '#ffa9a5';
		}
		$valsPunc[]=array('title'=>intval(date('m', $lastMonthTime2)). '月',
			'percent'=>$attri,
			'charts'=>array(
				array('value'=>''.$pucval2,'color'=>$puncColor),
				array('value'=>100-$pucval2,'color'=>'#clear'),
			)
		);
		
		
		
		$valsOrder = array();
		
		$rs = $this->cg1stocksheetModel->cg_months_group($StuffId);
		$maxQty = -1;
		$ctsOrder = 0;
		if ($rs != null) {
			$i = 0;
			$lastMonthDif = date('Y-m', strtotime('+1 month'));
			foreach ($rs as $rows) {
				
				$qtyOne = $rows['Qty'];
				
				if ($qtyOne > $maxQty) {
					$maxQty = $qtyOne;	
				}
				$monthtime = $rows['month'];
				$month = intval(date('m', strtotime($monthtime))). '月';
				$dif = 1;

				if ($lastMonthDif != '') {
					$dif = $this->datehandler->getDifferMonthNum($lastMonthDif,$monthtime);
					
					if ($dif > 1) {
						$monthtimes = strtotime($monthtime);
						for($j=($dif-1); $j>=1; $j--) {
							$valsOrder[]=array(
								'qty'=>'--',
								'title'=>intval(date('m', strtotime("+$j month", $monthtimes))). '月',
								'scale'=>'0.01',
								'num'=>0
							);
							$i ++;
						}
					}
				}
				// getDifferMonthNum
				
				$valsOrder[]=array(
					'qty'=>$this->get_k_qty($qtyOne),
					'title'=>$month,
					'scale'=>'0',
					'num'=>$qtyOne
				);
				$i ++;
				$lastMonthDif = $monthtime;
			}
			
			$maxQty += 200;

			for ($j=0; $j<$i; $j++) {
				if ($maxQty > 0) {
					$valsOrder[$j]['scale'] = $valsOrder[$j]['num'] / $maxQty;
				}
			}
			$ctsOrder = $i;
			
		}
		
		$allAndBadInfo = $this->QcBadrecordModel->get_allandbad($StuffId);
		$allshQty = $allAndBadInfo['allQty'];
		$allbadQty = $allAndBadInfo['badQty'];
		$allGoodQty = $allshQty - $allbadQty;
		
		$badPercent =
		$goodPercent = '--';
		if ($allshQty > 0) {
			$badPercent = round( $allbadQty / $allshQty *100 );
			
			$goodPercent = 100 - $badPercent;
		}
		
		$attri = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>''.$goodPercent,'FontName'=>'AshCloud61','FontSize'=>'20'),
				array('Text'=>'%','FontName'=>'AshCloud61','FontSize'=>'6')
			)
		);
		
		$attri2 = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>''.$badPercent,'FontName'=>'AshCloud61','FontSize'=>'20'),
				array('Text'=>'%','FontName'=>'AshCloud61','FontSize'=>'6')
			)
		);
		
		
		
		$allBadCauses = $this->QcBadrecordModel->get_all_badcauses($StuffId, '');

		if ($allBadCauses != null) {
			
			foreach ($allBadCauses as $rows) {
				$percent = '';
				if ($allbadQty > 0) {
					$percent = $rows['Qty'] / $allbadQty;
				}
				$rightCharts[]= array(
					'title'=>$rows['Cause'],
					'percent'=>round($percent*100).'%',
					'color'=>'#ffa9a5',
					'val'=>$percent,
					'$now'=>$now
				);
				
			}
			
		}
		/*
			@"centerQty",@"unit",@"goodPercent",
                                @"badPercent",@"goodQty",@"badQty"];
		*/
		$pages[]=array(
			array(
				'tag'=>'bads',
				'centerQty'=>number_format($allshQty),
				'unit'=>''.$UnitName,
				'goodQty'=>number_format($allGoodQty).'',
				'badQty'=>number_format($allbadQty) .'',
				'goodPercent'=>$attri,
				'badPercent'=>$attri2,
				'rightCharts'=>$rightCharts,
				'pie'=>array(
					array('value'=>''.$allGoodQty,'color'=>'#01be56'),
					array('value'=>''.$allbadQty,'color'=>'#clear'),
				),
				'pie2'=>array(
					array('value'=>''.$allbadQty,'color'=>'#ff0000'),
					array('value'=>''.$allGoodQty,'color'=>'#clear'),
				)
					
			),
			array(
				'tag'=>'punctual',
				'num'=>intval($evenPunc),
				'vals'=>$valsPunc
			),
			array(
				'tag'=>'orders',
				'num'=>$ctsOrder,
				'vals'=>$valsOrder
			),
			
		);
		
		$data[]= array(
			'tag'=>'pages',
			'api'=>'cg_client',
			'pages'=>$pages
			
		);
		
		$rownums=count($data);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$data[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>$timeInfo,'message'=>"",'rows'=>$data);
		$this->load->view('output_json',$data);

		

	}
	
	
	
	public function main() {
		$status = '';
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');


		$menu_id = element('menu_id',$params,'');
		$seg_id = element('seg_id',$params,'0');
		if ($menu_id != '') {
			$seg_id = $menu_id;
		}
		
		$sectionList = array();
		
		switch ($seg_id) {
			case 0:{
				$sectionList = $this->cg_unrecieved_companys($buyerId);
				
			}
			
			break;
			
			case 1:{
				$sectionList = $this->cgsend_months($buyerId);
				
			}
			break;
			
			case 2:{
				$sectionList = $this->cgunpay_months($buyerId);
			}
			break;
			case 3:{
				$sectionList = $this->cg_ordered_months($buyerId);
				
			}
			break;
		}
		
		$menuRows = null;
		
		
		
		$data['jsondata']=array('status'=>$status,'message'=>"1",'rows'=>$sectionList);
		$this->load->view('output_json',$data);		
	}
	
	public function headInfo() {
			 
		$message='';
		$this->load->model('cg1stocksheetModel');
		
		$params = $this->input->post();
		$buyerId = element('buyerId',$params,'');

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
		
		$percent1 = $percent2 = $percent3 = '--';
		if ($allamount > 0) {
			$percent1 = round($overAmount / $allamount *100, 1);
			$percent2 = round($curAmount  / $allamount *100, 1);
			$percent3 = round($nextAmount / $allamount *100, 1);
		}
		
		
		$status=array(
			'title'=>'未收',
			'amount'=>'¥'.number_format($allamount),
			'allqty'=>number_format($allQty).'pcs',
			'amount1'=>$overAmount<=0?'': '¥'.number_format($overAmount),
			'amount2'=>$curAmount<=0?'': '¥'.number_format($curAmount),
			'amount3'=>$nextAmount<=0?'': '¥'.number_format($nextAmount),
			'counts1'=>$overCounts<=0?'': number_format($overCounts),
			'counts2'=>$curCounts<=0?'': number_format($curCounts),
			'counts3'=>$nextCounts<=0?'': number_format($nextCounts),
			'titleR1'=>substr($thisWeek, 4, 2).'周',
			'titleR2'=>substr($lastWeek, 4, 2).'周',
			'titleR3'=>substr($lastlastWeek, 4, 2).'周',
			'percent1'=>$percent1<=0?'': array(
				'isAttribute'=>'1',
				'attrDicts'=>array(
					array('Text'=>''.$percent1,'FontName'=>'AshCloud61', 'FontSize'=>'17'),
					array('Text'=>'%','FontName'=>'AshCloud61',  'FontSize'=>'7')
				)
			),
			'percent2'=>$percent2<=0?'': array(
				'isAttribute'=>'1',
				'attrDicts'=>array(
					array('Text'=>''.$percent2, 'FontName'=>'AshCloud61', 'FontSize'=>'17'),
					array('Text'=>'%','FontName'=>'AshCloud61',  'FontSize'=>'7')
				)
			),
			'percent3'=>$percent3<=0?'': array(
				'isAttribute'=>'1',
				'attrDicts'=>array(
					array('Text'=>''.$percent3,'FontName'=>'AshCloud61',  'FontSize'=>'17'),
					array('Text'=>'%','FontName'=>'AshCloud61',  'FontSize'=>'7')
				)
			)
		);
	   
	    $status['chartVals'] = array(
		    array('value'=>$nnextAmount,'color'=>'#d5d5d5'),
		    array('value'=>$nextAmount,'color'=>'#c7e0ed'),
		    array('value'=>$curAmount,'color'=>'#358fc1'),
		    array('value'=>$overAmount,'color'=>'#fd0300')
	    );
	    
	    $punctuality = $this->cg1stocksheetModel->cg_punctuality('','', $thisWeek, $buyerId);
	    $punctualityColor = $punctuality >=90 ? '#01be56':'#ff0000';
	    $status['chartR1Vals'] = array(
		    array('value'=>$punctuality,'color'=>$punctualityColor),
		    array('value'=>100 - $punctuality,'color'=>'#clear')
	    );
	    $status['percentR1'] = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>''.$punctuality, 'FontSize'=>'13.5','Color'=>$punctualityColor,'FontName'=>'AshCloud61'),
				array('Text'=>'%', 'FontSize'=>'5','Color'=>$punctualityColor,'FontName'=>'AshCloud61')
			)
		);
	    
		$punctuality = $this->cg1stocksheetModel->cg_punctuality('','', $lastWeek,$buyerId);
		$punctualityColor = $punctuality >=90 ? '#01be56':'#ff0000';
	    $status['chartR2Vals'] = array(
		    array('value'=>$punctuality,'color'=>$punctualityColor),
		    array('value'=>100 - $punctuality,'color'=>'#clear')
	    );
	    $status['percentR2'] = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>''.$punctuality, 'FontSize'=>'13.5','Color'=>$punctualityColor,'FontName'=>'AshCloud61'),
				array('Text'=>'%', 'FontSize'=>'5','Color'=>$punctualityColor,'FontName'=>'AshCloud61')
			)
		);
		$punctuality = $this->cg1stocksheetModel->cg_punctuality('','', $lastlastWeek,$buyerId);
		$punctualityColor = $punctuality >=90 ? '#01be56':'#ff0000';
	    $status['chartR3Vals'] = array(
		    array('value'=>$punctuality,'color'=>$punctualityColor),
		    array('value'=>100 - $punctuality,'color'=>'#clear')
	    );
	    $status['percentR3'] = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>''.$punctuality, 'FontSize'=>'13.5','Color'=>$punctualityColor,'FontName'=>'AshCloud61'),
				array('Text'=>'%', 'FontSize'=>'5','Color'=>$punctualityColor,'FontName'=>'AshCloud61')
			)
		);
	   
	   
	   
	    $menuRows = null;
	    if ($buyerId != '') {
		    $menuRows = array();
		    
		    
		    $cts = $this->cg1stocksheetModel->get_waitcg_count($buyerId);
		    //$cts = 20;
		    $titleObj = array(
			'isAttribute'=>'1',
			'attrDicts'=>array(
				array('Text'=>'待采', 'FontSize'=>'9'),
				array('Text'=>' '.$cts, 'FontSize'=>'8','Color'=>'#727171')
			));
		    $menuRows[]=array('title'=>$titleObj);
		    
		    $cts = $this->cg1stocksheetModel->get_unrecived_count($buyerId);
		    $titleObj['attrDicts'][0]['Text'] = '未收';
		    $titleObj['attrDicts'][1]['Text'] = ' '.$cts;
		    $menuRows[]=array('title'=>$titleObj);

	    }
	   
		$data['jsondata']=array('status'=>$status,'message'=>"",'rows'=>$menuRows);
		$this->load->view('output_json',$data);
			
			 
			 
	}
	
	
	function cg_order() {
		
		$status = '1';
		
		$data['jsondata']=array('status'=>$status,'message'=>"ok",'rows'=>array('ok'=>''));
		$this->load->view('output_json',$data);
		
	}
    
	
	
}