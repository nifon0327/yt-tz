<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Warehouse_Stock_S extends MC_Controller {

    public $SetTypeId= null;
    public $MenuAction= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 4;
        $this->load->model('StuffremarkModel');
       // $this->MenuAction   = $this->pageaction->get_actions('picking');//领料
    }
    
    function scan_location() {
	    $params = $this->input->post();
	    $this->load->model('CkrksheetModel');
	    $Identifier = element('scaninfo', $params, -1);
	    $nameid = $this->CkrksheetModel->get_location_name('-1',$Identifier);
	    
	    $data['jsondata']=array('status'=>$nameid,'message'=>'');
	    
		$this->load->view('output_json',$data);
	    
    }
    
    function location_name() {
	    $params = $this->input->post();
	    $this->load->model('CkrksheetModel');
	    $location = element('LocationId', $params, -1);
	    $name = $this->CkrksheetModel->get_location_name($location);
	    $data['jsondata']=array('status'=>'','message'=>$name,'user'=>'');
	    
		$this->load->view('output_json',$data);
    }
        
        
    //need model StuffremarkModel
	function remark_inlist(&$nofinishList,$stuffid,$lefted = '80') {
		$remarkNew = $this->StuffremarkModel->get_stuff_remark($stuffid);
		if ($remarkNew!=null && element('Remark',$remarkNew,'')!='') {
			
	        $modifier = element('Name',$remarkNew,'');
	        $remark   = element('Remark',$remarkNew,'');
	        $modified = element('Date',$remarkNew,''); 
			$times =  date('m-d', strtotime($modified));
			$remarkArray=array(
				'content'=>$remark,
				'oper'=>$times.' '.$modifier,
				'tag'=>'remark2',
				'margin_left'=>$lefted,
				'separ_left'=>$lefted	
			);
			$nofinishList[count($nofinishList)-1]['hideLine']='1';
			$nofinishList[]=$remarkArray;
		}
	}

    public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','user'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	 
	
	public function main(){
	
		$params = $this->input->post();
		$LocationId = element('LocationId',$params,'-1');
		
// 		$LocationId = 15;
        $this->load->model('CkrksheetModel'); 
        $this->load->model('Ck9stocksheetModel'); 
        
        
        $infoOvers = $this->CkrksheetModel->get_location_overs($LocationId);
        
        

        
        $dataArray = array();
        $sectionDict = array('tag'=>'stock');
        
        
        $Amount = element('Amount', $infoOvers, 0);
        $OverAmount = element('OverAmount', $infoOvers, 0);
        $OverAmount1 = element('OverAmount1', $infoOvers, 0);
        $leftedAmount = $Amount- $OverAmount - $OverAmount1;
        $leftedAmount = $leftedAmount > 0 ? $leftedAmount : 0;
        
        $pie = array( array('value'=>"$leftedAmount",'color'=>"#72b2d4"),
				      array('value'=>"$OverAmount1",'color'=>"#dceaf4"),
				      array('value'=>"$OverAmount",'color'=>"#ff3a43")
				    );
        
        $percent = '--';
        if ($Amount > 0 && $OverAmount1>0) {
	        $percent = round($OverAmount1 / $Amount * 100);
        }
        $percentAttr = array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$percent",
					   			  'FontSize'=>'18',
					   			  'FontName'=>'AshCloud61',
					   			  'Color'   =>"#358fc1"),
					   		array('Text'    =>$percent<=0?'':'%',
					   			  'FontSize'=>'6',
					   			  'Color'   =>"#358fc1")
					   		)
					   	);
        $sectionDict['percent1'] = $percentAttr;
        $percent = '--';
        if ($Amount > 0 && $OverAmount>0) {
	        $percent = round($OverAmount / $Amount * 100);
        }
        $percentAttr = array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$percent",
					   			  'FontSize'=>'18',
					   			  'FontName'=>'AshCloud61',
					   			  'Color'   =>"#ff0000"),
					   		array('Text'    =>$percent<=0?'':'%',
					   			  'FontSize'=>'6',
					   			  'Color'   =>"#ff0000")
					   		)
					   	);
        $sectionDict['percent2'] = $percentAttr;
        
        
        $allOrdersInfoA = $this->Ck9stocksheetModel->get_all_hasorder_floor('', $LocationId);
		$allOrdersInfo = $allOrdersInfoA['row'];
		

        $allamount = element('Amount', $allOrdersInfo, 0);
        $orderamount = element('OrderAmount', $allOrdersInfo, 0);
        $percent = '--';
        if ($orderamount > 0 && $allamount>0) {
	        $percent = round($orderamount / $allamount * 100);
        }
        $percentAttr = array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$percent",
					   			  'FontSize'=>'32.5',
					   			  'FontName'=>'AshCloud61',
					   			  'Color'   =>"#01be56"),
					   		array('Text'    =>$percent<=0?'':'%',
					   			  'FontSize'=>'8',
					   			  'Color'   =>"#01be56")
					   		)
					   	);
        $sectionDict['percent'] = $percentAttr;
        
        $leftedAmount = $allamount - $orderamount;
        $leftedAmount = $leftedAmount > 0 ? $leftedAmount : 0;
        
        $pieInner = array( array('value'=>"$orderamount",'color'=>"#01be56"),
				      array('value'=>"$leftedAmount",'color'=>"#clear")
				    );
        $sectionDict['pie2'] = $pieInner;
        
        $sectionDict['pie'] = $pie;
        $sectionDict['qty'] = $infoOvers['Qty']<=0?'--': number_format($infoOvers['Qty']);
        $sectionDict['cts'] = $infoOvers['Counts']<=0?'--': number_format($infoOvers['Counts']);
        $sectionDict['qty1'] = $infoOvers['OverQty1']<=0?'--': number_format($infoOvers['OverQty1']);
        $sectionDict['cts1'] = $infoOvers['OverCounts1']<=0?'': number_format($infoOvers['OverCounts1']);
        $sectionDict['qty2'] = $infoOvers['OverQty']<=0?'--': number_format($infoOvers['OverQty']);
        $sectionDict['cts2'] = $infoOvers['OverCounts']<=0?'': number_format($infoOvers['OverCounts']);
        
        $sectionDict['amount'] =$Amount<=0?'--':  '¥'.number_format($Amount);
        
        $subdata = $this->location_sheets($LocationId);
        $sectionDict['data'] = $subdata;
        $dataArray[]=$sectionDict;
        
        $totals = 1;
        
        
        $data['jsondata']=array('status'=>'1','message'=>"",'totals'=>$totals,'rows'=>$dataArray);
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
		$this->load->model('ProductDataModel');
		$this->load->library('datehandler');
		$this->load->model('QcBadrecordModel');
		
		$this->load->model('CkrksheetModel'); 
        $this->load->model('Ck9stocksheetModel'); 
        
        
		$data = array();
		$pages = array();
		$rightCharts=array();
		
		$now = strtotime('now');
		
		$params = $this->input->post();
		$StockId = '';
		$StuffId = element('Id',$params,'-1');
// 		$StuffId = '99551';
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
		$rs = $this->CkrksheetModel->get_stuff_io_records($StuffId);
		$StockProcess = array();
		$StockProcess[]=array('tag'=>'margin');
		$firstTime = '';
		$lastTime = '';
		
		$newWeeks = '';
		$LocationIdNames = array('k'=>'v');
		if ($rs != null) {
			$it = 0;
			foreach ($rs as $rows) {
				


				$firstTime = $rows['date'];
				$aprocess=array(
					'tag'=>'estate',
					'addImg'=>$rows['keyval']>0?'bluePlus': 'greenMinus',
					'imgLeft'=>'30',
					'sperLeft'=>'98',
					'img'=>$rows['img'],
					'desc'=>$rows['title'],
					'oper'=>$rows['oper'],
					'date'=>array(
						'bg_color'=>'#clear',
						'dateCom'=>$firstTime == ''?null: explode('-', date('y-m-d', strtotime($firstTime))),
						'eachFrame'=>'0,14.5,13,12'
					),
					
				);
				
				if ($rows['keyval'] == -1 || $rows['keyval'] == 2) {
					$aprocess['desc']=$rows['title']."\n".$rows['otherid'];
				}
				
				$alocationId = $rows['LocationId'];	
				if ($alocationId > 0) {
					$alocationname = element($alocationId, $LocationIdNames, null);
					if ($alocationname == null) {
						$alocationname = $this->CkrksheetModel->get_location_name($alocationId);
						$LocationIdNames[$alocationId]=$alocationname;
					}
					
					if ($alocationname != '') {
						$aprocess['position']=$alocationname;
					}
				} else {
					
				}
				
				
				
				$StockProcess[]=$aprocess;
				$it++;
				
							
			}
		} 	
		$pages[]=$StockProcess;	
		
		
		
		//second page 
		
		
		$rs = $this->stuffDataModel->get_related_products($StuffId);
		if ($rs != null) {
			$products = array();
			$rsnums = 0;
			$products[]=array('tag'=>'margin');
			foreach ($rs as $rows) {
				$ProductId = $rows['ProductId'];
				if ($ProductId == '') {
					continue;
				}
				
				$rsnums ++;
				$attri = array(
					'isAttribute'=>'1',
					'attrDicts'=>array(
						array('Text'=>$rows['Forshort'].'-','FontSize'=>'11','Color'=>'#358fc1'),
						array('Text'=>$rows['cName'],'FontSize'=>'11','Color'=>'#3b3e41')
					)
				);
				$attri2 = array(
					'isAttribute'=>'1',
					'attrDicts'=>array(
						array('Text'=>' '.($rows['OrderAll']>0? number_format($rows['OrderAll']):'--').' ','FontSize'=>'11','Color'=>'#3b3e41'),
						array('Text'=>$rows['CountOrder']>0? number_format($rows['CountOrder']):'','FontSize'=>'9','Color'=>'#727171')
					)
				);
				
				$productImg = $rows['TestStandard']==1? $this->ProductDataModel->get_picture_path($ProductId):'';
				$firstTime = $rows['OrderDate'];
				$products[]=array(
					'tag'=>'z_order',
					'title'=>$attri,
					'col2Img'=>'scdj_11',
					'col3Img'=>'',
					'Picture'=>$rows['TestStandard']==1?'1':'',
					'productImg'=>$productImg,
					'col2'=>$attri2,
					'titleX'=>'-23',

					'date_c'=>array(
						'light'=>'10',
						'Color'=>'#727171',
						'dateCom'=>$firstTime == ''?null: explode('-', date('y-m-d', strtotime($firstTime))),
						'eachFrame'=>'12,0,16,14'
					),
				);
						
				
			}
			
			if ($rsnums > 0)
				$pages[]=$products;	
			
			
		}
		
		
		
			
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
		
		$weekqtys = array();
		$sumweekqty = 0;
		$rs = $this->cg1stocksheetModel->stuff_unrecieve_weekqtys($StuffId);
		if ($rs!=null) {
			foreach ($rs as $rows) {
				$sumweekqty += $rows['Qty'];
				$weekqtys[]= array(
					'qty'=>number_format($rows['Qty']),
					'week'=>$rows['Weeks']
				);
			}
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
		
		// ($sumweekqty)<=0?'fake':
		$pages[]=array(
			array(
				'tag'=>'margin'
			),
			array(
				'tag'=> 'weeks',
				'vals'=>$weekqtys,
				'title'=>'未收：'. ($sumweekqty>0?number_format($sumweekqty):'--')
			),
			array(
				'tag'=>'bads',
				'showArrow'=>'1',
				'open'=>'0',
				'Id'=>$StuffId,
				'topCons'=>'2',
				'H'=>110,
				'method'=>'sub_bads',
				'animate'=>'0',
				'centerQty'=>number_format($allshQty),
				'unit'=>''.$UnitName,
				'goodQty'=>number_format($allGoodQty).'',
				'badQty'=>number_format($allbadQty) .'',
				'title'=>'不良品：'. ($allbadQty>0?number_format($allbadQty):'--'),
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
				'title'=>'准时率：'. ($evenPunc>0?(round($evenPunc).'%'):'--'),
				'vals'=>$valsPunc
			)
			
		);
		
		$data[]= array(
			'tag'=>'pages',
			'api'=>'warehouse_stock_s',
			'pages'=>$pages
			
		);
		
		$rownums=count($data);
		if ($rownums>0){
			$upTag = element('upTag',$params,'');
			$data[$rownums-1]["deleteTag"] = $upTag;
		}
		
		
		$data['jsondata']=array('status'=>'','message'=>"",'rows'=>$data);
		$this->load->view('output_json',$data);

		

	}
	

	function sub_bads() {
	
	//sleep(1);
		$this->load->model('QcBadrecordModel');
		
		$params = $this->input->post();
		$ISPAD = element('ISPAD', $params , '0');
		$capacity = $ISPAD == 1 ? 5 : 3;
		
		$subList = array();
		
		$limitted = 3;
		$upTag = element('upTag', $params , '0');
		$StuffId = element('Id', $params , '0');

		 
		$subList[]=array('tag'=>'fake');
		
		$allBadCauses = $this->QcBadrecordModel->get_all_badcauses($StuffId,'','1');
		if ($allBadCauses != null) {
			
			foreach ($allBadCauses as $rows) {
				 
				
				$firstTime = $rows['created'];
				$aprocess=array(
					'tag'=>'estate',
					'img'=>'cg_bad_rec',
					'desc'=>'不良'.(number_format($rows['Qty'])).'pcs',
					'oper'=>$rows['Name'],
					'bg_color'=>'#efefef',
					'date'=>array(
						'dateCom'=>$firstTime == ''?null: explode('-', date('y-m-d', strtotime($firstTime))),
						'eachFrame'=>'0,14.501,13,12',
						'padcolor'=>'#efefef'
					),
					
				);

				$subList[]=$aprocess;
				$imgsBad = $this->QcBadrecordModel->get_badpictures($rows['Id']);
				if (count($imgsBad) > 0) {
					$subList[count($subList)-1]['hideLine']='1';
					$subList[]=array(
					'tag'=>'subimg',
					'height'=>'55',
					'lineLeft'=>'76',
					'padLeft'=>'76',
					'bgcolor'=>'#efefef',
					'imgs'=>$imgsBad
					);
				}
			 
				
			}
			
		}	    
	    
	    
	    $subList[count($subList)-1]['deleteTag'] = $upTag;
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$subList);
	    
		$this->load->view('output_json',$data);
	
}


	function location_sheets($LocationId) {
		
		 $bluefont    = $this->colors->get_color('bluefont');
	  $black   = $this->colors->get_color('black');
	  $redcolor    = $this->colors->get_color('red');
	  
	  $TypeIdsResum = array('9176','9188','9201','9202','9203','9206');
	  
	  $this->load->model('CkrksheetModel'); 
	  $this->load->model('CkLocationModel'); 
      $this->load->model('WarehouseModel'); 
      $this->load->model('stuffdataModel');
      $this->load->model('StuffPropertyModel');
      $this->load->model('Ck9stocksheetModel');
      $this->load->library('datehandler');
      
      $Id = $LocationId;
      $name = $this->CkrksheetModel->get_location_name($LocationId);
      
      $recordsLC = $this->CkLocationModel->get_records($LocationId);
      $regionLC = element('Region', $recordsLC, '');
      $isNRegion = false;
      if ($regionLC == 'N') {
	      $isNRegion = true;
      }
      
      
	   $rowsArray = $this->CkrksheetModel->get_postion_sheet($Id);
	   $rownums=count($rowsArray);
	   $dataArray = array();
	   $overDate =date('Y-m-d',strtotime('-3 month'));
	   
	   $nowdatetime = $this->DateTime;
	   
          for ($i=0;$i<$rownums;$i++){
              $rows = $rowsArray[$i];
              
              $Months = element('Months', $rows, 0);
              $wsname = element('wsname', $rows, '');
              $lockImg = ''; 
              $StuffId = $rows['StuffId'];
              $ischild = $this->stuffdataModel->get_ischild($StuffId);
			  if ($ischild == 1) {
				  $mStuffId = $this->stuffdataModel->get_mstuffid($StuffId);
				   $allOrdersInfoA = $this->Ck9stocksheetModel->get_all_hasorder_floor('', '-1', $mStuffId);
				  
			  } else {
				   $allOrdersInfoA = $this->Ck9stocksheetModel->get_all_hasorder_floor('', '-1', $StuffId);
			  }
             
              $lockImg = '';
              
              if ($allOrdersInfoA<=0) {
	              if ($Months > 3) {
		              $lockImg = 'stk_ware_1';
	              } else if ($Months>=1) {
		              $lockImg = 'stk_ware_2';
		              
	              } else {
		              $lockImg = 'stk_ware_3';
	              }
              }
              
              
              
              
              $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';
              $imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_picture($StuffId):'';
              
             $locations = $this->CkrksheetModel->get_stuff_locationqty($rows['StuffId'],'');
		     $location  = $locations['location'];
		     $locdatas  = $locations['data'];
		     $loc_s = null;
		     if ($locdatas && count($locdatas)>0) {
			     $loc_s = array();
			     
			     $sel = 0;
			     
			     foreach ($locdatas as $aloc) {

				     
				     $loc_s[]=array(
					     'str'=>$aloc['LocationId']>0?$aloc['location']:'无',
					     'sel'=>$aloc['location']==$name && $sel==0?'1':''
				     );
				     if ($aloc['location'] == $name && $sel==0) {
					     $sel = 1;
				     }
			     }
		     }
		    
		     
             $created = $rows['created'];
             $dateArray=array();
              if ($created!=''){
                   $dateArray=array(substr($created,2, 2),substr($created,5, 2),substr($created,8, 2));
                   
                   
                   
              }
              $dateColor  = $created<=$overDate?$redcolor:$black;
              
              $Decimals = $rows['Decimals'];
            $actions = array();
			  $actions[]=array('Name'=>'备注','Action'=>'remark','Color'=>'#358fc1');
			  
			  
			  
			  $stateInfo = $this->CkrksheetModel->get_last_state_time($StuffId);
			  $lasttime = null;
			  if ($stateInfo != null) {
				  $lasttime = element('time', $stateInfo, null);
			  if ($lasttime != null) {
				  $lasttime = $this->datehandler->GetDateTimeOutString($lasttime, $nowdatetime);
			  }
			  }
			  
			  $Property = $this->StuffPropertyModel->get_property($StuffId);
			  
	          $adata=array(
	                'tag'=>'ck_stock',
			    	'Id'  =>$StuffId,
			    	'actions'=>$actions,
			    	'showArrow'=>'1',
			    	'method'=>'sub_pages',
			    	'$stateInfo'=>$stateInfo,
			    	'Property'=>$Property,
			    	'addedTimeX'=>'-13',
			    	'open'=>'',
			    	'StuffId' =>$rows['StuffId'],
			    	'title'=>$rows['StuffId'] . '-' . $rows['StuffCname'],
			    	'col1'=>array('dateCom'=>$dateArray,'eachFrame'=>'0,4,13,12','light'=>'9','Color'=>"$dateColor"),
			    	'col2Img'=>'wh_tstock',
			    	'col2'=>number_format($rows['Qty'],$Decimals),
			    	'col3Img'=>'wh_ostock',
			    	'col3'=>number_format($rows['oStockQty'],$Decimals),
			    	'col4'=>$rows['PreChar'] .number_format($rows['Price'],3), 
			    	'col5'=>$wsname ==''? $rows['Forshort'] : $wsname,
			    	'Picture'    =>$rows['Picture'],
	                'stuffImg'   =>$stuffImg,
	                'forbidImg'=>$lockImg,
	                'fbScale'=>'1.3',
	                'fbBling'=>array('beling'=>'1'),
	                'imgurl'     =>$imgurl,
	                'location'   =>array('Text'=>$location,'loc_s'=>$loc_s,'eachFrame'=>'-74,0,20,15')
	           );
	           
	          $Qty = element('Qty', $rows, 0);
			  $FrameCapacity = element('FrameCapacity', $rows, 0);
			  $basketType = element('basketType', $rows, 0);


/*
			  if ($this->LoginNumber == 11965) {
							  $FrameCapacity = '2000';
							  if ($basketType<=0)
							  $basketType = '1';
						  }
*/
			  $adata['addedTime']=$lasttime;
			  $adata['addImg']=$stateInfo!=null?element('img', $stateInfo, null):null;
				  
			  if ($Qty>0 && $FrameCapacity>0 && $basketType>0) {
				  if ($isNRegion == true) {
					  $basketType = '4';
				  }
				  // 
				  $newFrames = element('newFrames', $rows, 0);
				  $FrameNum = $newFrames>0?$newFrames: intval( ceil($Qty/$FrameCapacity) );
				  $FrameImg = 'frame_'.$basketType;
				  $adata['frameNum']=$FrameNum;
				  $adata['frameImg']=$FrameImg;
				  
			  }
	           
	           $dataArray[]=$adata;
	           
	            $this->remark_inlist($dataArray,$StuffId,'63');
          }
		  return $dataArray;

	}
	
	public function subList()
	{
	         $params = $this->input->post();
			  $warehouseId  = element('menu_id',$params,'all');
			  $segmentIndex = element('segmentIndex',$params,'0');
			  $upTag        = element('upTag',$params,'ck_type');
			  $Id           = element('Id',$params,'');
			  
			  $bluefont    = $this->colors->get_color('bluefont');
			  $black   = $this->colors->get_color('black');
			  $redcolor    = $this->colors->get_color('red');
			  
			  $this->load->model('CkrksheetModel'); 
	          $this->load->model('WarehouseModel'); 
	          $this->load->model('stuffdataModel');
	          
	        if ($warehouseId>0){
		        $records = $this->WarehouseModel->get_records($warehouseId);
				$SendFloor = $records['SendFloor'];
				$records = null;
			}else{
				 $SendFloor = '';
				 $warehouseId='all';
			}
			$dataArray=array();
            $arrayOne = array('tag'=>'none');
            
			  switch($upTag){
				  case 'ck_type':
				     switch($segmentIndex){
					     case -1:
					          $TypeId = $Id;
					          $rowsArray = $this->CkrksheetModel->get_company_amount($warehouseId,$SendFloor,$TypeId);
					          $rownums=count($rowsArray);
					          
					          for ($i=0;$i<$rownums;$i++){
					              $rows = $rowsArray[$i];
					              $CompanyId = $rows['CompanyId'];
					              $comAmount = round($rows['Amount']);
					              $orderRows=$this->CkrksheetModel->get_order_stufftype_amount($warehouseId,$SendFloor,$TypeId,$CompanyId);
					              $orderAmount =round($orderRows['Amount']);
					              $orderPercent  = $comAmount>0? round($orderAmount/$comAmount*100):0;
					              
					              //$percent =$totalAmount>0? round($comAmount/$totalAmount*100):0;
						          $dataArray[]=array(
						                 'tag'   =>'ck_gys',
								    	 'open'=>'',
								    	 'showArrow'=>'1',
								    	 'arrowImg'   =>'UpAccessory_gray.png',
								    	 'Id'    =>"$TypeId|$CompanyId",
								    	 'title' =>$rows['Forshort'],
								    	 'col1' =>number_format($rows['Qty']),
								    	 'col1Right'=>number_format($rows['Counts']),
								    	 'col3' =>'¥' .number_format($comAmount),
								    	 'hasOrderImg'=>"hasOrd",
								    	 'percent' =>"$orderPercent" . "%"
						           );
					          }
					         break;
					    case 0:
					         $CompanyId = $Id;
					          $rowsArray = $this->CkrksheetModel->get_stufftype_amount($warehouseId,$SendFloor,0,$CompanyId);
					          $rownums=count($rowsArray);
					          
					          for ($i=0;$i<$rownums;$i++){
					              $rows = $rowsArray[$i];
					              $TypeId = $rows['TypeId'];
					              $comAmount = round($rows['Amount']);
					              $orderRows=$this->CkrksheetModel->get_order_stufftype_amount($warehouseId,$SendFloor,$TypeId,$CompanyId);
					              $orderAmount =round($orderRows['Amount']);
					              $orderPercent  = $comAmount>0? round($orderAmount/$comAmount*100):0;
					              
					              //$percent =$totalAmount>0? round($comAmount/$totalAmount*100):0;
						          $dataArray[]=array(
						                 'tag'   =>'ck_gys',
								    	 'open'=>'',
								    	 'showArrow'=>'1',
								    	 'arrowImg'   =>'UpAccessory_gray.png',
								    	 'Id'    =>"$TypeId|$CompanyId",
								    	 'title' =>$rows['TypeName'],
								    	 'col1' =>number_format($rows['Qty']),
								    	 'col1Right'=>number_format($rows['Counts']),
								    	 'col3' =>'¥' .number_format($comAmount),
								    	 'hasOrderImg'=>"hasOrd",
								    	 'percent' =>"$orderPercent" . "%"
						           );
					          }
					        break;
				     }
			          
				    break;
				case 'ck_gys':
				   $IdArray=explode('|', $Id);
				   $TypeId         = $IdArray[0];
				   $CompanyId = $IdArray[1];
				   
				   $rowsArray = $this->CkrksheetModel->get_company_sheet($warehouseId,$SendFloor,$CompanyId,$TypeId);
				   $rownums=count($rowsArray);
				   $dataArray[]= $arrayOne;
				   $overDate =date('Y-m-d',strtotime('-3 month'));
				    
			          for ($i=0;$i<$rownums;$i++){
			              $rows = $rowsArray[$i];
			              $StuffId = $rows['StuffId'];
			              $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';
			              $imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_picture($StuffId):'';
			              
			             $locations = $this->CkrksheetModel->get_stuff_locationqty($rows['StuffId'],'');
					     $location  = $locations['location'];
					     $locdatas  = $locations['data'];
					    
					     $locColor  = $bluefont;
		                  $xdDate = $rows['xdDate'];
		                  $dateArray=array();
		                  if ($xdDate!=''){
			                   $dateArray=array(substr($xdDate,2, 2),substr($xdDate,5, 2),substr($xdDate,8, 2));
		                  }
		                  $dateColor  = $xdDate<=$overDate?$redcolor:$black;
		                  
			              $Decimals = $rows['Decimals'];
			            $actions = array();
						  $actions[]=array('Name'=>'备注','Action'=>'remark','Color'=>'#358fc1');
				          $dataArray[]=array(
				                'tag'=>'ck_stock',
						    	'addedImg'=>$upTag == 'ck_places' ? 'greenMinus.png':'',
						    	'Id'  =>$StuffId,
						    	'actions'=>$actions,
						    	'StuffId' =>$rows['StuffId'], 
						    	'title'=>$rows['StuffId'] . '-' . $rows['StuffCname'],
						    	'col1'=>array('dateCom'=>$dateArray,'eachFrame'=>'0,4,13,12','light'=>'9','Color'=>"$dateColor"),
						    	'col2Img'=>'wh_tstock',
						    	'col2'=>number_format($rows['Qty'],$Decimals),
						    	'col3Img'=>'wh_ostock',
						    	'col3'=>number_format($rows['oStockQty'],$Decimals),
						    	'col4'=>'¥' .number_format($rows['Price'],3),
						    	'col5'=>'¥' .number_format($rows['Amount']),
						    	'Picture'    =>$rows['Picture'],
				                'stuffImg'   =>$stuffImg,
				                'imgurl'     =>$imgurl,
				                'location'   =>array('Text'=>$location,'border'=>'1','Color'=>"$locColor",'data'=>$locdatas)
				           );
				            $this->remark_inlist($dataArray,$StuffId,$upTag == 'ck_places' ? '80':'63');
			          }
			          $dataArray[]= $arrayOne;
				   break;
			  case 'ck_places':
			       $rowsArray = $this->CkrksheetModel->get_postion_sheet($Id);
				   $rownums=count($rowsArray);
				   $dataArray[]= $arrayOne;
				   $overDate =date('Y-m-d',strtotime('-3 month'));
				   
			          for ($i=0;$i<$rownums;$i++){
			              $rows = $rowsArray[$i];
			              $StuffId = $rows['StuffId'];
			              $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';
			              $imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_picture($StuffId):'';
			              
			             $locations = $this->CkrksheetModel->get_stuff_locationqty($rows['StuffId'],'');
					     $location  = $locations['location'];
					     $locdatas  = $locations['data'];
					    
					     
		                 $created = $rows['created'];
		                 $dateArray=array();
		                  if ($created!=''){
			                   $dateArray=array(substr($created,2, 2),substr($created,5, 2),substr($created,8, 2));
		                  }
		                  $dateColor  = $created<=$overDate?$redcolor:$black;
		                  
			              $Decimals = $rows['Decimals'];
			            $actions = array();
						  $actions[]=array('Name'=>'备注','Action'=>'remark','Color'=>'#358fc1');
						  
						  
						  
						  
						  
				          $adata=array(
				                'tag'=>'ck_stock',
						    	'Id'  =>$StuffId,
						    	'actions'=>$actions,
						    	'StuffId' =>$rows['StuffId'],
						    	'title'=>$rows['StuffId'] . '-' . $rows['StuffCname'],
						    	'col1'=>array('dateCom'=>$dateArray,'eachFrame'=>'0,4,13,12','light'=>'9','Color'=>"$dateColor"),
						    	'col2Img'=>'wh_tstock',
						    	'col2'=>number_format($rows['Qty'],$Decimals),
						    	'col3Img'=>'wh_ostock',
						    	'col3'=>number_format($rows['oStockQty'],$Decimals),
						    	'col4'=>$rows['PreChar'] .number_format($rows['Price'],3), 
						    	'col5'=>$rows['Forshort'],
						    	'Picture'    =>$rows['Picture'],
				                'stuffImg'   =>$stuffImg,
				                'imgurl'     =>$imgurl,
				                'location'   =>array('Text'=>$location,'border'=>'1','Color'=>"$bluefont",'data'=>$locdatas)
				           );
				           
				          $Qty = element('Qty', $rows, 0);
						  $FrameCapacity = element('FrameCapacity', $rows, 0);
						  $basketType = element('basketType', $rows, 0);
						  // FrameCapacity,D.basketType
						  if ($this->LoginNumber == 11965) {
							  $FrameCapacity = '2000';
							  $basketType = '1';
						  }
						  if ($Qty>0 && $FrameCapacity>0 && $basketType>0) {
							  $FrameNum = intval( ceil($Qty/$FrameCapacity) );
							  $FrameImg = 'frame_'.$basketType;
							  $adata['frameNum']=$FrameNum;
							  $adata['frameImg']=$FrameImg;
							  $adata['addedTime']=null;
						  }
				           
				           $dataArray[]=$adata;
				           
				            $this->remark_inlist($dataArray,$StuffId,'63');
			          }
			      break;
				   
			  }
			  
	    $totals = count($dataArray);
	    if ($totals > 0) {
		    $dataArray[$totals - 1]['deleteTag'] =$upTag;
	    }

         $data['jsondata']=array('status'=>'1','message'=>"",'totals'=>$totals,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);	    
	}
	
	function remark() {
		
		
		$params   = $this->input->post();
		$action   = element('Action',$params,'');
		$stuffid  = element('Id',$params,'');
	    $remark   = element('remark',$params,'');
	    
	    $status=0;
	    $rowArray=array('n'=>'');
	    $newaction = '';
	    $newdata = array('n'=>'');
	    if ($action=='remark' && $stuffid!='')
	    {


			 $this->load->model('StuffremarkModel');
			 $status = $this->StuffremarkModel->save_item($params);
		    if ($status>0){
			   
			     $rowArray=array(
			        'hideLine'=>'1'
			    );
			    $newaction = 'insert';

			    $this->load->model('StaffMainModel');
			    $operator=$this->StaffMainModel->get_staffname($this->LoginNumber);
			    $newdata = array(
			    				'tag'=>'remark2',
			    				'content'=>$remark,
			    				'oper'=>'1分前 '.$operator,
			    				'margin_left'=>'63',
			    				'separ_left'=>'63',

			    				'bgcolor'  =>'#FFFFFF'
			    				 );
  
			}
	    }
	 
		            
		 $dataArray=array("data"=>$rowArray,'Action'=>$newaction,'newdata'=>$newdata);
	    
	    $message=$status==1?'保存备注信息成功！':'保存备注信息失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	
}