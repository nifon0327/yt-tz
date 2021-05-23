<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Bomcg extends MC_Controller {

	
	
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
	
	
	function cg_unreceived_weeks($buyerId='') {
		
		
		$this->load->model('cg1stocksheetModel');
		$sectionList = array();
		$rs=$this->cg1stocksheetModel->cg_unrecieved_weeks($buyerId);
		if ($rs != null) {
			foreach ($rs as $rows) {
// 				if ($rows['Counts'] > 0 && $rows['Amount'] > 0)
				{
					
					if ($rows['Qty']<=0.4) continue;
					$weeks = $rows['Weeks'];
					$adata = array(
						'tag'=>'notout',
						'col4marginR'=>'20',
						'Id'=>$weeks==''?'notsure':$weeks,
						'segIndex'=>$buyerId,
						'showArrow'=>'1',
						'row1Y'=>'8',
						'week'=>$weeks,
						'hasWeek'=>'1',
						'centerX'=>'30',
						
						'method'=>'cg_unreceive_subs',
						'addval'=>$rows['Counts'],
						'weekTitle'=>$weeks==''?'交期待定':$this->getWeekToDate($weeks),
						'col1'=>number_format($rows['Qty']),
						'col1R'=>number_format($rows['Counts']),
						'col2'=>'¥'.number_format($rows['Amount']),
						
					);
					
					
					if ($rows['LastCount'] > 0) {
						$adata['col3R'] = $rows['LastCount'];
						$adata['col3'] = array('Text'=>number_format($rows['LastQty']),'Color'=>'#01be56');

						$adata['col4'] = array('Text'=>'¥'.number_format($rows['LastAmount']),'Color'=>'#01be56');


						$adata['row1Y'] = '';
					}
					
					$sectionList[]=$adata;
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
		
		$pucMonth1 = date('Y-m');
		$pucMonth2 = date('Y-m', strtotime("-1 month"));
		$pucMonth3 = date('Y-m', strtotime("-2 month"));
		
		if ($rs != null) {
			foreach ($rs as $rows) {
// 				if ($rows['Counts'] > 0 && $rows['Amount'] > 0)
				{
					
					
					
					$Forshort = $rows['Forshort'];
					$CompanyId = $rows['CompanyId'];
					$Logo = $rows['Logo'];
					
					/*
						$percent1=$this->ScSheetModel->get_scsheet_punctuality($oneTypes['Id'], $lastMonth);
			   $percentcolor1=$percent1>=85?$lightgreen:$red;
			   $pieValue1 = array(
						array("value"=>"$percent1","color"=>"$percentcolor1"),
						array("value"=>"".(100-$percent1),"color"=>"clear")
					);
			   $chartValue1 = array(
							   		'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>"$percent1",
								   			  'FontSize'=>'14',
								   			  'Color'   =>"$percentcolor1",
								   			  'FontName'=>'AshCloud61'),
								   		array('Text'    =>'%',
								   			  'FontSize'=>'5',
								   			  'Color'   =>"$percentcolor1",
								   			  'FontName'=>'AshCloud61')
								   		
								   		)
							   		);
					*/
					$percent = $this->cg1stocksheetModel->cg_punctuality($pucMonth1,$CompanyId);
					$percent = $percent==null ? '--':$percent;
					$percentcolor=$percent>=85?'#01be56':'#ff0000';
					$pieValue = array(
						array("value"=>"$percent","color"=>"$percentcolor"),
						array("value"=>"".(100-$percent),"color"=>"clear")
					);
					$pie1 = array(
						'percent'=>array(
					   		'isAttribute'=>'1',
					   		'attrDicts'=>array(
						   		array('Text'    =>"$percent",
						   			  'FontSize'=>'16',
						   			  'Color'   =>"$percentcolor",
						   			  'FontName'=>'AshCloud61'),
						   		array('Text'    =>'%',
						   			  'FontSize'=>'5',
						   			  'Color'   =>"$percentcolor",
						   			  'FontName'=>'AshCloud61')
						   		
						   		)
					   		),
						'pie'=>$pieValue
					);
					
					$percent = $this->cg1stocksheetModel->cg_punctuality($pucMonth2,$CompanyId);
					$percent = $percent==null ? '--':$percent;
					$percentcolor=$percent>=85?'#01be56':'#ff0000';
					$pieValue = array(
						array("value"=>"$percent","color"=>"$percentcolor"),
						array("value"=>"".(100-$percent),"color"=>"clear")
					);
					$pie2 = array(
						'percent'=>array(
					   		'isAttribute'=>'1',
					   		'attrDicts'=>array(
						   		array('Text'    =>"$percent",
						   			  'FontSize'=>'14',
						   			  'Color'   =>"$percentcolor",
						   			  'FontName'=>'AshCloud61'),
						   		array('Text'    =>'%',
						   			  'FontSize'=>'4',
						   			  'Color'   =>"$percentcolor",
						   			  'FontName'=>'AshCloud61')
						   		
						   		)
					   		),
						'pie'=>$pieValue
					);
					
					$percent = $this->cg1stocksheetModel->cg_punctuality($pucMonth3,$CompanyId);
					$percent = $percent==null ? '--':$percent;
					$percentcolor=$percent>=85?'#01be56':'#ff0000';
					$pieValue = array(
						array("value"=>"$percent","color"=>"$percentcolor"),
						array("value"=>"".(100-$percent),"color"=>"clear")
					);
					$pie3 = array(
						'percent'=>array(
					   		'isAttribute'=>'1',
					   		'attrDicts'=>array(
						   		array('Text'    =>"$percent",
						   			  'FontSize'=>'14',
						   			  'Color'   =>"$percentcolor",
						   			  'FontName'=>'AshCloud61'),
						   		array('Text'    =>'%',
						   			  'FontSize'=>'4',
						   			  'Color'   =>"$percentcolor",
						   			  'FontName'=>'AshCloud61')
						   		
						   		)
					   		),
						'pie'=>$pieValue
					);
					
					
					$adata = array(
						'tag'=>'ch_total',
						'col4marginR'=>'20',
						'open'=>'',
						'pie_1'=>$pie1,
						'pie_2'=>$pie2,
						'pie_3'=>$pie3,
						'Id'=>$CompanyId.'|'.$weeks,
						'type'=>$buyerId,
						'showArrow'=>'1',
						'centerX'=>'30',
						'row1Y'=>'8',
						'method'=>'cg_unreceivelist',
						//'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
						'title'=>$Forshort,
						'col1'=>number_format($rows['Qty']),
						'col1R'=>number_format($rows['Counts']),
						'col2'=>'¥'.number_format($rows['Amount']),
						
					);
					
					
					if ($rows['LastCount'] > 0) {
						$adata['col3R'] = $rows['LastCount'];
						$adata['col3'] = array('Text'=>number_format($rows['LastQty']),'Color'=>'#01be56');

						$adata['col4'] = array('Text'=>'¥'.number_format($rows['LastAmount']),'Color'=>'#01be56');


						$adata['row1Y'] = '';
					}
					$subList[]=$adata;
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
	
	function waitcg_companys($buyerId='') {
		
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('TradeObjectModel');
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();

		$sectionList = array();
		$rs=$this->cg1stocksheetModel->get_waitcg_companys($buyerId);
		if ($rs != null) {
			$rows = $rs[0];
			$maxAmount = $rows['Amount'];
			
			foreach ($rs as $rows) {
				// if ($rows['CompanyId']>0)
				{
					
					$Forshort = $rows['Forshort'];
					$CompanyId = $rows['CompanyId'];

					$Logo = $rows['Logo'];
					
					$srt = $rows['Amount'];
					
					$unlock = $rows['Qty']-$rows['LockQty'];
					$onedata=array(
						'tag'=>'notout',
						'col4marginR'=>'20',
						'Id'=>$CompanyId,
						'segIndex'=>$buyerId,
						'showArrow'=>'1',
						'method'=>'waitcg_subs',
						'addval'=>$rows['Count'],
						//'companyImg'=>$Logo==''?'':$LogoPath.$Logo,
						'title'=>$Forshort,
						'col1'=>$unlock<=0?'':number_format($unlock),
						'col1R'=>$unlock<=0?'':number_format($rows['Count']-$rows['LockCount']),
						'col2'=>$unlock<=0?'':'¥'.number_format($rows['Amount']-$rows['LockAmount'])
						
					);
					
					if ($unlock <= 0) {
						$onedata['row2Y'] = '-8';
						$onedata['method']='waitcg_loadlocks';
					} else {
						$srt += $maxAmount * 10;
					}
					$onedata['srt'] = $srt;
					if ($rows['LockCount']>0) {
						$onedata['col3'] = array('Text'=>number_format($rows['LockQty']), 'Color'=>'#ff0000');
						$onedata['col3R'] =number_format($rows['LockCount']);
						$onedata['col4'] = array('Text'=>'¥'.number_format($rows['LockAmount']), 'Color'=>'#ff0000');
						$onedata['col4RImg'] = 'ilock_r';
					} else {
						$onedata['row1Y'] = '8'; 
					}
	
					$sectionList[]=$onedata;
				}
				
				
			}
		}
		
		
		usort($sectionList, function($a, $b) {
            $al = ($a['srt']);
            $bl = ($b['srt']);
            if ($al == $bl)
                return 0;
            return ($al > $bl) ? -1 : 1;
        });
	
		return $sectionList;
	}
	
	function cg_unreceive_sublist($buyerId, $companyId, $weeks, $rowid='') {
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->model('StuffPropertyModel');
		
		$this->load->library('datehandler');
		$rs=$this->cg1stocksheetModel->cg_unrecieved_list($buyerId, $weeks, $companyId, $rowid);
		

		$afterDate = date('Y-m-d', strtotime('-90 days'));
		/*
			A.StockId,S.StuffId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       A.Qty,A.rkQty,S.Price,D.StuffCname,D.Picture,D.DevelopState,C.Rate,C.PreChar,R.Mid  
	     FROM
		*/ 
		
		$operate = $this->pageaction->get_action('operate');
		
		
		
		$operate['list'] = $this->pageaction->get_actions('split,alterweek,backset,remark');
		
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
	            
	            $LastNoBL = element('LastNoBL', $rows, 0);
				
				$iconImg = '';
				if ($Picture == 1) {
					$iconImg = $this->stuffDataModel->get_stuff_icon($StuffId);
				}
				
				

				
				$processDict = $this->cg1stocksheetModel->cg_process_2($StockId,$StuffId);
				
				$nosemi = $buyerId==10882 ? 1:0;
				$lockArray = $this->cg1stocksheetModel->get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState,$nosemi);
				
				$evenPunc = element("$StuffId", $this->StuffPuncInfo, null);
				if ($evenPunc == null) {
					
					$evenPunc = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId, $afterDate);
					

					$this->StuffPuncInfo["$StuffId"] = $evenPunc;
				}
				
				
				$flagImg = '';
				if ($evenPunc!=null &&$evenPunc <85) {
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
				$rmk = null;
				$cg_remark = $this->cg1stocksheetModel->cg_remarkinfo($StockId);
				if ($cg_remark != null) {
					$Remark = $Remark == '' ? '':$Remark.'  ';
					$Remark.= $cg_remark['Remark'];
					$rmk  = $cg_remark['Remark'];
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
							'oper'=>($operDate==''?'': date('m-d', strtotime($operDate))).' '.$oper,
							'img'=>$remarkIcon
						);
					} else {
						$remarkInfo['content2']=$remark;
						$remarkInfo['oper2']=($operDate==''?'': date('m-d', strtotime($operDate))).' '.$oper;
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
					'rmk'=>$rmk,
					'actions'=>array($operate),
					'Id'=>''.$rows['Id'],
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
					'w_title'=>$this->getWeekToDate($rows['Weeks']),
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'Property'=>$Property,
					'iconImg'=>$iconImg,
					'Picture'=>$Picture==1?'1':'',
					'col2'=>array('Text'=>number_format($rows['Qty']), 'Color'=>'#3b3e41', 'BgColor'=>$LastNoBL==1?'#d0f7d0':'#ffffff'),
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
	
	function waitcg_sublist($buyerId, $companyId, $EditId='', $checklocksign='') {
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->model('StuffPropertyModel');
		
		$this->load->library('datehandler');
		
		$rs=$this->cg1stocksheetModel->waitcg_list($buyerId,$companyId,$EditId,$checklocksign);
		
		
		$afterDate = date('Y-m-d', strtotime('-90 days'));
		
		$operate = $this->pageaction->get_action('operate');
		$operate['list'] = $this->pageaction->get_actions('update,reset,split,resetstock,remark');
		$subList = array();
		if ($rs != null) {
			$innnerI = 0;
			foreach ($rs as $rows) {
				$innnerI ++;
				$StuffId = $rows['StuffId'];
				$StockId = $rows['StockId'];
				$Picture = $rows['Picture'];
				$POrderId = $rows['POrderId'];
				$DevelopState = $rows['DevelopState'];
				
				$iconImg = '';
				if ($Picture == 1) {
					$iconImg = $this->stuffDataModel->get_stuff_icon($StuffId);
				}
				
				
				$xdTime=$rows['ywOrderDTime'];
				
				$processDict = $this->cg1stocksheetModel->cg_process_2($StockId,$StuffId);
				
				$L_xdTime = $processDict['l_xdtime'];
				
				$nosemi = $buyerId==10882 ? 1:0;
				$lockArray = $this->cg1stocksheetModel->get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState,$nosemi);
				$lock = element('lock',$lockArray,0);
				
				$lastStateTime = '';
				
				if ($lock<=0) {
					$lastStateRow = $this->cg1stocksheetModel->get_state_last($StockId);
				
					if ($lastStateRow != null) {
						$lastStateTime = $lastStateRow['date'];
					}
					
					if ($checklocksign!='' && $checklocksign==1) {
						continue;
					}
				} else {
					if ($checklocksign!='' && $checklocksign==0) {
						continue;
					}
				}
				

				
				$xdTime=$L_xdTime=="" || strtotime($L_xdTime)<strtotime($xdTime)?$xdTime:$L_xdTime;
				

				
				$xdTime=$lastStateTime=="" || strtotime($lastStateTime)<strtotime($xdTime)?$xdTime:$lastStateTime;
				

				
				$times = $this->datehandler->GetDateTimeOutString($xdTime,$this->DateTime);
				
				
				
				
				$evenPunc = element("$StuffId", $this->StuffPuncInfo, null);
				if ($evenPunc == null) {
					
					$evenPunc = $this->cg1stocksheetModel->cg_punctuality_stuff($StuffId, $afterDate);
					

					$this->StuffPuncInfo["$StuffId"] = $evenPunc;
				}
				
				
				$flagImg = '';
				if ($evenPunc!=null && $evenPunc < 80) {
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
				
				
				
				$Remark="";
			    $StockRemark=$rows["StockRemark"];
			    $AddRemark=$rows["AddRemark"];
			    $Remark=$StockRemark==""?"":$StockRemark;
			    $Remark.=$AddRemark==""?"":$AddRemark;
				$remarkInfo = null;
				
				
				$remarkInfo = null;
				$cg_remark = $this->cg1stocksheetModel->cg_remarkinfo($StockId);
				$oper1 = '';
				if ($cg_remark != null) {
					$Remark = $Remark == '' ? '':$Remark.'  ';
					$Remark.= $cg_remark['Remark'];
					$oper1 = date('m/d', strtotime($cg_remark['Date'])).'  '.$cg_remark['Name'];
				}
				
				if ($AddRemark != '') {
					$Opcode =$this->cg1stocksheetModel->getCgOperateDate($StockId,3);
					if ($Opcode != null) {
						
						$oper1 = $this->datehandler->GetDateTimeOutString($Opcode['date'],$this->DateTime).'  '.$Opcode['oper'];
					}
				}
				
				
				$dPrice = $rows['dPrice'];
				$Price = $rows['Price'];
/*
				if ($this->LoginNumber == 11965) {
				$dPrice += 1;	
				}
*/
				$Remarkattri = null;
				$Prechar = $rows['PreChar'];
				if ($dPrice != $Price) {
					$Remarkattri = array(
					'isAttribute'=>'1',
					'attrDicts'=>array(
						array('Text'=>$Remark==''?'':"$Remark\n", 'FontSize'=>'11','FontName'=>'NotoSansHans-Light'),
						array('Text'=>'系统默认设置的单价为', 'FontSize'=>'11','FontName'=>'NotoSansHans-Light'),
						array('Text'=>$Prechar.$dPrice, 'FontSize'=>'11','Color'=>'#ff0000','FontName'=>'NotoSansHans-Light')
					));
					$Remark.='系统默认设置的单价为'.$Prechar.$dPrice;
				}
				
				$CompanyId = $rows['CompanyId'];
				$dCompanyId = $rows['dCompanyId'];
/*
				if ($this->LoginNumber == 11965) {
				$dCompanyId += 1;	
				}
*/
				if ($CompanyId != $dCompanyId) {
					$dForshort = $rows['dForshort'];
					if ($Remarkattri == null) {
						$Remarkattri = array(
							'isAttribute'=>'1',
							'attrDicts'=>array(
								array('Text'=>$Remark==''?'':"$Remark\n", 'FontSize'=>'11','FontName'=>'NotoSansHans-Light'),
								array('Text'=>'替换原有供应商', 'FontSize'=>'11','FontName'=>'NotoSansHans-Light'),
								array('Text'=>$dForshort, 'FontSize'=>'11','Color'=>'#ff0000','FontName'=>'NotoSansHans-Light')
						));
						$Remark.='1';
					} else {
						$Remarkattri['attrDicts'][]=array('Text'=>"\n".'替换原有供应商', 'FontSize'=>'11','FontName'=>'NotoSansHans-Light');
						$Remarkattri['attrDicts'][]=array('Text'=>$dForshort, 'FontSize'=>'11','Color'=>'#ff0000','FontName'=>'NotoSansHans-Light');
					}
				}
				if ($Remark != '' || $Remarkattri != null) {
					$remarkInfo = array(
						'content'=>$Remarkattri!=null?$Remarkattri:$Remark,
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
							$sortNumber = 50000000000000;
							$lockImg = 'cg_dev_lock';
							$remarkIcon = 'cg_dev_remark';
						break;
						case 1:
							$sortNumber = 60000000000000;
							$lockImg = 'order_lock';
							$remarkIcon = 'rmk_orderlock';
						break;
						case 2:
						case 3:
							$sortNumber = 60000000000000;
							$lockImg = 'order_lock_s';
							$remarkIcon = 'rmk_stufflock';
						break;
						case 8:
							$sortNumber = 40000000000000;
							$lockImg = 'cg_needlocks';
							$remarkIcon = 'cg_neededlock';
						break;
						default:
						break;
					}
					
					
					if ($remarkInfo == null) {
						$remarkInfo = array(
							'content'=>$remark,
							'oper'=>($operDate==''?'': date('m-d', strtotime($operDate))).' '.$oper,
							'img'=>$remarkIcon
						);
					} else {
						$remarkInfo['content2']=$remark;
						$remarkInfo['oper2']=($operDate==''?'': date('m-d', strtotime($operDate))).' '.$oper;
						$remarkInfo['img2']=$remarkIcon;
					}



					
				}
				if ($rows['Estate']==1 || ($this->LoginNumber == 11965 && $innnerI==6 )) {
					$flagImg = 'flag_wait_audit';	
					$sortNumber = 30000000000000;
				}
				
				$sortNumber +=( ($rows['Weeks']==''?intval(date('Y').'00'): $rows['Weeks']) * 10000000000);
				$sortNumber += intval($POrderId);
				
				$Property = $this->StuffPropertyModel->get_property($StuffId);
				
				
				$weekChanged = $this->cg1stocksheetModel->check_week_changed($StockId);
				$weekColor = $weekChanged == true ? '#54BCE5':'';
				
				
				
				
				$subList[]=array(
					'tag'=>'cg_order',
					'showArrow'=>'1',
					'Id'=>''.$rows['Id'],
					'srt'=>$sortNumber,
// 					'noweek'=>'1',
					'week'=>$rows['Weeks'],
					'weekColor'=>$weekColor,
					'arrowImg'=>'UpAccessory_gray',
					'open'=>'',
					'StockId'=>''.$StockId,
					'StuffId'=>''.$StuffId,
					'type'=>''.$StuffId,
					'method'=>'sub_pages',
					'col1Img'=>'cg_cgorder',
					'col1qty'=>''.$rows['OrderQty'],
					'addQty'=>''.$rows['AddQty'],
					'col2Img'=>'iostock_gray',
					'col3Img'=>'',
					'canselect'=>$lock<=0?'1':'',
					'canchoose'=>($lock<=0 && $rows['AddQty']<=0)?'1':'',
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
					
// 					'titleX'=>'-28',
					'Property'=>$Property,
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'iconImg'=>$iconImg,
					'Picture'=>$Picture==1?'1':'',
					'selQty'=>''.$rows['Qty'],
					'actions'=>$rows['Estate']==1?array():array($operate),
					'col1'=>number_format($rows['Qty']),
					'col3'=> 
					 array('Text'=> $rows['PreChar'].$rows['Price'],'Color'=>$dPrice != $Price ? '#ff0000':'#3b3e41'),
					
					'col2'=>array(
						'Text'=>number_format($rows['oStockQty']),
						'Color'=>$rows['oStockQty']>0?'#358fc1':'#3b3e41'
					),
					'col4'=>$times,
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
		
		if ($checklocksign!='' && $checklocksign==0) {
			$subList[]=array(
				'tag'=>'ck_tap',
				'open'=>'',
				'title'=>'加载锁定纪录',
				'method'=>'waitcg_loadlock',
				'segIndex'=>$buyerId,
				'Id'=>$companyId
			);
		}
		
		return $subList;
		
	}
	function waitcg_loadlocks() {
		
		$params = $this->input->post();
		$buyerId = element('segmentIndex',$params,'');
		$companyId = element('Id',$params,'');
		$checklocksign = '1';
 		$subList = $this->waitcg_sublist($buyerId, $companyId,'','');
		
		
		
		$data['jsondata']=array('status'=>''.count($subList),'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	function waitcg_loadlock() {
		
		$params = $this->input->post();
		$buyerId = element('segmentIndex',$params,'');
		$companyId = element('Id',$params,'');
		$checklocksign = '1';
 		$subList = $this->waitcg_sublist($buyerId, $companyId,'',$checklocksign);
		
		
		
		$data['jsondata']=array('status'=>''.count($subList),'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	
	
	function waitcg_subs() {
		
		$params = $this->input->post();
		$buyerId = element('segmentIndex',$params,'');
		$companyId = element('Id',$params,'');
		$checklocksign = '';
		if ($this->versionToNumber($this->AppVersion)>=430) {
			$checklocksign = '0';
		}
 		$subList = $this->waitcg_sublist($buyerId, $companyId,'',$checklocksign);
		
		
		
		$data['jsondata']=array('status'=>''.count($subList),'message'=>"1",'rows'=>$subList);
		$this->load->view('output_json',$data);
		
	}
	
	function cg_unreceivelist() {
		
		$params = $this->input->post();
		$buyerId = element('segmentIndex',$params,'');
		$companyIdweek = element('Id',$params,'');
		$companyIdweek = explode('|', $companyIdweek);
		
		$subList = $this->cg_unreceive_sublist($buyerId, $companyIdweek[0],  $companyIdweek[1]);
		
		$rownums=count($subList);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$subList[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>''.count($subList),'message'=>"1",'rows'=>$subList);
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
	
	
	function sub_pages() {
		
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffDataModel');
		$this->load->library('datehandler');
		$this->load->model('QcBadrecordModel');
		
		
		$data = array();
		$pages = array();
		$messages = array();
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
		$rmkDict = array('a'=>'');
		$newWeeks = '';
		if ($rs != null) {
			$POrderId = substr($StockId, 0, 12);
			$rsRmk = $this->cg1stocksheetModel->get_porder_weekremark($POrderId);
			
			if ($rsRmk != null) {
				foreach ($rsRmk as $rows) {
					$titleKey = $rows['OldWeek'].'-'.$rows['NewWeek'];
					$rmkDict["$titleKey"]= $rows['Remark'];
				if ($this->LoginNumber == 11965) {
					$rows['title'] = $titleKey;
					$messages[]=array('l'=>$rows);
				}
				}
			}
			
			foreach ($rs as $rows) {
				if ($lastTime == '') {
					$lastTime = $rows['date'];
				}
				$firstTime = $rows['date'];
				
				
				if ($rows['keyval'] == 11) {
					
						
					$oldweekAll = 
					$oldweek = $rows['title'];
					$oldweek = substr($oldweek, 4,2);
					
					
					
					if ($newWeeks == '') {
						$newWeeksAll = 
						$newWeeks = $rows['otherid'];
						$newWeeks = substr($newWeeks, 4,2);
						
						$newtitle = '原交期'. $oldweek . '周改现交期'. $newWeeks.'周';
						$addrmk = element("$oldweekAll-$newWeeksAll",$rmkDict,'');
						$rows['title'] = $newtitle;
						$newWeeks = $oldweek;
					} else {
						
						
						$newtitle = '交期由'. $oldweek . '周改为交期'. $newWeeks.'周';
						$addrmk = element("$oldweekAll-$newWeeksAll",$rmkDict,'');
						
						$rows['title'] = $newtitle;
						$newWeeksAll = $oldweekAll;
						$newWeeks = $oldweek;
						
					}
					
					
					// 
					$operatorCom = explode('|||', $rows['operator']);
					if (count($operatorCom) == 2) {
						$rows['operator'] = $operatorCom[1];


						
						if ($operatorCom[0]!='') {
							$addrmk = $addrmk==''?'':"\n".$addrmk;
							
							$rows['title'] = $rows['title']."\n备注:".$operatorCom[0].$addrmk;
						} else if ($addrmk!='') {
							$rows['title'] = $rows['title']."\n备注:".$addrmk;
						}
						
					}
					
						
					
				}
				
				$StockProcess[]=array(
					'tag'=>'estate',
					'img'=>$rows['img'],
					'desc'=>$rows['title'],
					'oper'=>$rows['operator'],
					'dateCompos'=>$firstTime == ''?null: explode('-', date('y-m-d', strtotime($firstTime))),
				);
				
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

			//$timeInfo[]=$rmkDict;
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
			'pages'=>$pages
			
		);
		
		$rownums=count($data);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$data[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>$timeInfo,'message'=>$messages,'rows'=>$data);
		$this->load->view('output_json',$data);

	}
	
	public function main() {
		$message='';
		$this->load->model('cg1stocksheetModel');

		$nextWeekTime     = date('Y-m-d', strtotime('+1 week'));
		
		$nextWeek     = $this->getWeek($nextWeekTime);
		$data=$this->cg1stocksheetModel->all_cgmain_new($nextWeek);
		$status=1;
		$sectionList= array();
		
		
		
		
		$sectionList[]=array('data'=>$data);
	   
	   
		$data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$sectionList);
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
			'percent1'=>$percent1<=0?'--': array(
				'isAttribute'=>'1',
				'attrDicts'=>array(
					array('Text'=>''.$percent1,'FontName'=>'AshCloud61', 'FontSize'=>'17'),
					array('Text'=>'%','FontName'=>'AshCloud61',  'FontSize'=>'7')
				)
			),
			'percent2'=>$percent2<=0?'--': array(
				'isAttribute'=>'1',
				'attrDicts'=>array(
					array('Text'=>''.$percent2, 'FontName'=>'AshCloud61', 'FontSize'=>'17'),
					array('Text'=>'%','FontName'=>'AshCloud61',  'FontSize'=>'7')
				)
			),
			'percent3'=>$percent3<=0?'--': array(
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
	
	function save_reset() {
		$params   = $this->input->post();
		$status = '';
$action   = element('Action',$params,'');
if ($action == 'cgd_reset') {
	$this->load->model('cg1stocksheetModel');
	$status = $this->cg1stocksheetModel->cg_reset_add($params);
	
}		
$Log = $message = $status =='1' ? '采购单重置成功':'生成采重置失败';
$comrow = null;
if ($status == -3) {
	$Log = '半成品不可app重置';
	$status = '';
} else if ($status == '1') {
	$comrow = array('ok'=>'');
}
		$data['jsondata']=array('status'=>$status==1?'1':'','message'=>$Log,'rows'=>$comrow);
		$this->load->view('output_json',$data);
	}
	function cg_order() {
		
		$params   = $this->input->post();
		$status = '1';
$status       = element('suce',$params,'');
// 原api
$Log = $message = $status =='1' ? '生成采购单成功':'生成采购单失败';

		$data['jsondata']=array('status'=>$status,'message'=>$Log,'rows'=>array('ok'=>''));
		$this->load->view('output_json',$data);
		
	}
	function resetstock() {
		$params   = $this->input->post();
		$action   = element('Action',$params,'');
	    $remark   = trim( element('remark',$params,''));
	    $Id       = element('Id',$params,'');
		$newdata = array('n'=>'');
	    $status=0;
	    $rowArray=array('n'=>'');
	    $newaction = '';
		
	    if ($action=='resetstock' && $Id>0 )
	    {
		    $this->load->model('cg1stocksheetModel');
		    $status=$this->cg1stocksheetModel->resetstock($Id);
		    if ($status==1){				 
			    $rowss = $this->waitcg_sublist('', '', $Id);
			    if (count($rowss) >= 1) {
					$rowArray = $rowss[0];
					unset($rowArray['open']);
				}
		    }
		    
		    
		    
	    }
	    $dataArray=array("data"=>$rowArray);
	    
	    $message=$status==1?'重置库存成功！':'重置库存失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);

	}
	
	public function remark() {
		$params   = $this->input->post();
		$action   = element('Action',$params,'');
	    $remark   = trim( element('remark',$params,''));
	    $Id       = element('Id',$params,'');
	    
		$newdata = array('n'=>'');
	    $status=0;
	    $rowArray=array('n'=>'');
	    $newaction = '';
		
	    if ($action=='remark' && $Id>0 &&  $remark!='')
	    {
		    $this->load->model('cg1stocksheetModel');
		    $status=$this->cg1stocksheetModel->save_cg_remark($params);
// $status = 1;
		    if ($status==1){
			   
			    $this->load->model('StaffMainModel');
			    $operator=$this->StaffMainModel->get_staffname($this->LoginNumber);
			    
			    
			   $rowArray =array('remarkInfo'=>
			    			array(
			    				'img'=>'remark1',
			    				'content'=>$remark,
			    				'oper'=>'1分前 '.$operator
			    				 )) ;
			    $seg_id       = element('seg_id',$params,'');	
			    if ($seg_id == 1) {
				    $rowss = $this->cg_unreceive_sublist('', '', '', $Id);
			    }	 else  
			    $rowss = $this->waitcg_sublist('', '', $Id);
			    if (count($rowss) >= 1) {
					$rowArray = $rowss[0];
					unset($rowArray['open']);
				}
		    }
		    
		    
		    
	    }
	    $dataArray=array("data"=>$rowArray);
	    
	    $message=$status==1?'备注成功！':'备注失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);

	}
	
	
	function save_update() {
		$status = '1';
		
		$params   = $this->input->post();
		// 原api
		$status       = element('suce',$params,'');
		$EditId       = element('edit_id',$params,'');
		$buyerId      = element('buyerId',$params,'');
		
		
		
		$rowsget = array('n'=>'');
		
		if ($status == '1') {
			
			$rows = $this->waitcg_sublist($buyerId, '', $EditId);
			if (count($rows) >= 1) {
				$rowArray = $rows[0];
				unset($rowArray['open']);
				$rowsget['data'] = $rowArray;
			} else {
				$rowsget['data'] = array('tag'=>'fake');
			}
		}
		
		$message = $status =='1' ? '采购单更新成功':'采购单更新失败';

		$data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>$rowsget);
		$this->load->view('output_json',$data);
		
	}


	function update_pick() {
		
		$params   = $this->input->post();


	    $EditId       = element('EditId',$params,'');
	    $status = array();
	    $rowsDict = array('n'=>'');
	    
	   // $EditId = '202946';
	    if ($EditId != '') {
		    $this->load->model('StaffMainModel');
		    $this->load->model('TradeObjectModel');
		    $this->load->model('cgstocksheetModel');
		    
		    $records = $this->cgstocksheetModel->get_records('',$EditId);
		    $CompanyId = $records['CompanyId'];
		    $buyerId = $records['BuyerId'];
		    $price = $records['Price'];
		    $AddQty = $records['AddQty'];
		    $FactualQty = $records['FactualQty'];
		    $OrderQty = $records['OrderQty'];
		    $Forshort = $this->TradeObjectModel->get_forshort($CompanyId);
		    $Prechar = $this->TradeObjectModel->get_prechar($CompanyId);
		    $name = $this->StaffMainModel->get_staffname($buyerId);
		    

		    
		    $status[]=array('othersinfo'=>array('marklabel'=>''.$name));
		    $status[]=array('othersinfo'=>array('marklabel'=>''.$Forshort));
		    $status[]=array('othersinfo'=>array('marklabel'=>$Prechar.$price));
		    $status[]=array('othersinfo'=>array('marklabel'=>"$OrderQty" ), 'ContentTxt'=>''.$AddQty, 'FieldVal'=>''.$AddQty);
		    
		    $buyers = array();
		    
		    
		    
		    $rs = $this->StaffMainModel->get_all_buyers();
		    if ($rs != null) {
			    foreach ($rs as $rows) {
				    $buyers[]=array(
					    'title'=>$rows['Name'],
					    'Id'=>''.$rows['Number'],
					    'CellType'=>'2'
				    );
			    }
		    }
		    
		    $rs = $this->TradeObjectModel->get_all_providers();
		    if ($rs != null) {
			    foreach ($rs as $rows) {
				    $providers[]=array(
					    'title'=>$rows['Letter']. '-' .$rows['Forshort'],
					    'Id'=>''.$rows['CompanyId'],
					    'CellType'=>'2'
				    );
			    }
		    }
		    
		    
		    $rowsDict['buyer']=$buyers;
		    $rowsDict['provider']=$providers;
	    }
	    
	    
	    $data['jsondata']=array('status'=>$status,'message'=>'','totals'=>'0','rows'=>$rowsDict);
		$this->load->view('output_json',$data);
	    
		
	}
	
	
	function save_deliverydate() {
		
		
		$params   = $this->input->post();
		$newDate = element('deliverydate', $params, '');
		$remark   = trim( element('remark',$params,''));
	    $Id       = element('edit_id',$params,'');
	    
		$newdata = array('n'=>'');
	    $status=0;
	    $rowArray=array('n'=>'');
	    $newaction = '';
		
	    if ($Id>0 &&  $newDate!='')
	    {
		    
		    $this->load->model('cgstocksheetModel');
		    $records = $this->cgstocksheetModel->get_records('',$Id);
//DeliveryDate,S.DeliveryWeek,
		    $StockId = $records['StockId'];
		    $DeliveryDate = $records['DeliveryDate'];
		    $DeliveryWeek = $records['DeliveryWeek'];
		    $params['stockid'] = $StockId;
		    $params['olddate'] = $DeliveryDate;
		    $params['oldweek'] = $DeliveryWeek;
		    
		    $this->load->model('cg1stocksheetModel');
		    $status=$this->cg1stocksheetModel->set_deliverydate($params);

		    if ($status==1){
			   
			    
				$rowss = $this->cg_unreceive_sublist('', '', '', $Id);
			     

			    if (count($rowss) >= 1) {
					$rowArray = $rowss[0];
					unset($rowArray['open']);
				}
		    }
		    
		    
		    
	    }
	    $dataArray=array("data"=>$rowArray);
	    
	    $message=$status==1?'更改交期成功！':'更改交期失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	

	
	function deliverydate_pick() {
		
		$params   = $this->input->post();


	    $EditId       = element('EditId',$params,'');
	    $status = array();
	    $rowsDict = array('n'=>'');
	    
	   // $EditId = '202946';
	    if ($EditId != '') {
		    $this->load->model('StaffMainModel');
		    $this->load->model('TradeObjectModel');
		    $this->load->model('cgstocksheetModel');
		    $this->load->model('cg1stocksheetModel');
		    $this->load->model('YwOrderSheetModel');
		    
		    $records = $this->cgstocksheetModel->get_records('',$EditId);
		    // get_order_deliveryweek
		    $POrderId = $records['POrderId'];
		    $maxweek = $this->YwOrderSheetModel->get_order_deliveryweek($POrderId);
		    $maxweek = $maxweek>0?$maxweek:'';
// 		    $maxweek = '';
		    $weeklist = $this->cg1stocksheetModel->get_week_selections(20,'',$maxweek);
		    $rowsDict['deliverydate']=$weeklist;
		    
	    }
	    
	    
	    $data['jsondata']=array('status'=>$status,'message'=>'','totals'=>'0','rows'=>$rowsDict);
		$this->load->view('output_json',$data);
	    
		
	}
    
	
	
}