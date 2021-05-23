<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Warehouse_Stock extends MC_Controller {

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
	
	public function menu()
	{
	     $params = $this->input->post();
	     $this->load->model('WarehouseModel');
	     $rows=$this->WarehouseModel->get_warehouse(1,$this->SetTypeId);
	     
	     $dataArray[]=array(
					      'cellType'=>"1",
						  'title'       =>"全部",
						  'selected'=>"1",
						  'Id'          =>"0"
					  );
	     for ($i = 0,$counts=count($rows); $i < $counts; $i++) {
	         $row=$rows[$i];
	         $row['title']=$row['title'];
	         $dataArray[]=$row;
	     }

	     $status=count($dataArray)>0?1:0;
		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$dataArray);
		 $this->load->view('output_json',$data);
	}
	
	public function main(){
	
		$params = $this->input->post();
		$warehouseId= element('menu_id',$params,'all');
        
        $this->load->model('CkrksheetModel'); 
        $this->load->model('WarehouseModel'); 
        
        if ($warehouseId>0){
	        $records = $this->WarehouseModel->get_records($warehouseId);
			$SendFloor = $records['SendFloor'];
			$whName = $records['Name'];
			$records = null;
		}else{
			 $SendFloor = '';
			 $whName   = '全部';
			 $warehouseId='all';
		}
       
        
        $records = $this->CkrksheetModel->get_stock_amount($warehouseId,$SendFloor);
        $stockQty         = round($records['Qty']);
        $stockAmount = round($records['Amount']);
        $records = null;
        
        if ($warehouseId!='all'){
		        $records = $this->CkrksheetModel->get_stock_amount('all');
		        $totalQty         = round($records['Qty']);
		        $totalAmount = round($records['Amount']);
		        $records = null;
        }else{
	           $totalQty         = $stockQty;
		       $totalAmount = $stockAmount;
        }
        
        $records = $this->CkrksheetModel->get_order_amount($warehouseId,$SendFloor);
        $orderQty        = round($records['OrderQty']);        //订单需求数量
        $orderAmount= round($records['Amount']); //订单需求金额
        $M1Amount   = round($records['M1Amount']);//一个月内未有下单
        $M3Amount   = round($records['M3Amount']);//三个月内未有下单
        
        $M0Amount = $orderAmount-$M1Amount;
        $M1Amount = $M1Amount - $M3Amount;
        
        $M1Percent  = $orderAmount>0?round($M1Amount/$stockAmount*100):0;
        $M3Percent  = $orderAmount>0?round($M3Amount/$stockAmount*100):0;
        $M0Percent  = 100 - $M1Percent - $M3Percent;
        
        $chartVal = array(
				array('value'=>"$M0Percent", 'color'=>'#72b2d4'),
				array('value'=>"$M1Percent", 'color'=>'#dceaf4'),
				array('value'=>"$M3Percent", 'color'=>'#ff3a43'),
			);
		
		$OrderPercent  = $orderAmount>0?round($orderAmount/$stockAmount*100):0;
		$ClearPercent      = 100 - $OrderPercent;
		
	    $chartValInner = array(
				 array('value'=>"$OrderPercent", 'color'=>'#46e346'),
				 array('value'=>"$ClearPercent", 'color'=>'#clear')
		);
		
	  $chartPercent=array(
			        'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>"$OrderPercent",'FontSize'=>'33','FontName'=>'AshCloud61',  'Color'=>"#01be56"),
				   		array('Text'    =>'%',  'FontSize'=>'9',   'FontName'=>'AshCloud61',  'Color'   =>"#01be56")
				   	)
		        );
       
       if ($warehouseId=='all'){
             $topPercent ='';
       }else{
              $stockPercent =$totalAmount>0?round($stockAmount/$totalAmount*100):0;
              $topPercent = array(
			        'isAttribute'=>'1',
			   		'attrDicts'=>array(
				   		array('Text'    =>"$stockPercent",   'FontName'=>'AshCloud61',  'FontSize'=>'20'),
				   		array('Text'    =>"%",    'FontName'=>'AshCloud61',   'FontSize'=>'7')
				   	)
		        );
		}

       $processes = array();
       $typeRecords=  $this->CkrksheetModel->get_stufftype_amount($warehouseId,$SendFloor,4);
       
       foreach($typeRecords as $typeRow){
             
             $TypeId = $typeRow['TypeId'];
             
             $orderRows =$this->CkrksheetModel->get_order_stufftype_amount($warehouseId,$SendFloor,$TypeId);
             $orderTypeAmount = round($orderRows['Amount']);
             $orderRows = null;
             
              $typeAmount = round($typeRow['Amount']);
              $typeValue     = $stockAmount>0?round($typeAmount/$stockAmount,2):0;
              $typePercent  = $typeValue*100;
              
              $anchor = $stockAmount>0?round($orderTypeAmount/$stockAmount,2):0;
 
              $processes[]=array(
								  'value'    =>"$typeValue",
								  'anchor' =>"$anchor",
								  'title'      =>$typeRow['TypeName'],
								  'percent'=>$typePercent . '%'  
			    );

       }

       $records =null;
       
       $items=$this->get_segement_items();
       $dataArray[]=array(
							'tag'            =>'ck_chart',
							'buttonInfo'=>$items,
							'segIndex'    =>'-1',
							'method'      =>'segements',
							'top_percent'=>$topPercent,
							'chartVal'      =>$chartVal,
							'chartValInner'=>$chartValInner,
							'chart_percent'=>$chartPercent,
							'position' =>"",
							'posImg'   =>'',
							'amount'  =>'¥' . number_format($stockAmount),
							'qty'          =>number_format($stockQty),
							'processes'=>$processes
				);

         $typesArray = array();
         $totals = count($dataArray);
         $data['jsondata']=array('status'=>'1','message'=>"",'totals'=>$totals,'datas'=>$typesArray,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);
		
	}
	
	//获取选项列表
	public function get_segement_items() 
	{
		$items = array(
				       'indi_0'=>array('img'=>'wh_gys','selectImg'=>'wh_gys_1','title'=>'供应商'),
					   'indi_1'=>array(
									array('img'=>'wh_iok','selectImg'=>'wh_iok_1','title'=>'出入库'),
									array('img'=>'wh_out','selectImg'=>'wh_out_1','title'=>'出库'),
									array('img'=>'wh_rk',  'selectImg'=>'wh_rk_1',   'title'=>'入库')
						),
						'indi_2'=>array(
									array('img'=>'wh_pos','selectImg'=>'wh_pos_1','title'=>'库位'),
								  	array('img'=>'wh_qty','selectImg'=>'wh_qty_1',  'title'=>'数量')
						)
			);
		return $items;
	}
	
	public function segements(){
	$staSign=0;
	       $params = $this->input->post();
		   $warehouseId  = element('menu_id',$params,'all');
		   $segmentIndex = element('segmentIndex',$params,'0');
           $upTag        = element('upTag', $params , '0');//loadall
         
          $bluefont   = $this->colors->get_color('bluefont');
          $black   = $this->colors->get_color('black');
          $redcolor = $this->colors->get_color('red');
          $ordergreen = $this->colors->get_color('ordergreen');
          $lightgreen = '#46e346';
           
           $this->load->model('CkrksheetModel'); 
           $this->load->model('WarehouseModel'); 
           $this->load->model('stuffdataModel');
           $this->load->model('StaffMainModel');
           $this->load->library('dateHandler');
           
	       if ($warehouseId>0){
		        $records = $this->WarehouseModel->get_records($warehouseId);
				$SendFloor = $records['SendFloor'];
				$records = null;
			}else{
				 $SendFloor = '';
				 $warehouseId='all';
			}
		
		    $records = $this->CkrksheetModel->get_stock_amount($warehouseId,$SendFloor);
	        $totalQty         = round($records['Qty']);
	        $totalAmount = round($records['Amount']);
	        $records = null;
			
			$dataArray=array();
			$arrayOne = array('tag'=>'none');
			$cktapSign = 0;
			switch($segmentIndex){
			    case -1://默认页面
			          $rowsArray = $this->CkrksheetModel->get_stufftype_amount($warehouseId,$SendFloor);
			          
			          $rownums=count($rowsArray);
			          $stratSign = 0;
			          if ($rownums>10){
				          $rownums=$upTag=='loadall'?$rownums:10;
				          $staSign =$upTag=='loadall'?10:0;
				          $cktapSign = 1;
			          }
			          for ($i=$staSign;$i<$rownums;$i++){
			              $rows = $rowsArray[$i];
			              $TypeId = $rows['TypeId'];
			              $typeAmount = round($rows['Amount']);
			             // [@"title",@"col1",@"col2",@"col1Right",@"col2Right",@"col3",@"percent"];
			              $orderRows=$this->CkrksheetModel->get_order_stufftype_amount($warehouseId,$SendFloor,$TypeId);
			              $orderAmount =round($orderRows['Amount']);
			              $orderPercent  = $typeAmount>0? round($orderAmount/$typeAmount*100):0;
			              
			              $percent =$totalAmount>0? round($typeAmount/$totalAmount*100):0;
				          $dataArray[]=array(
				                 'tag'   =>'ck_type',
						    	 'open'=>'',
						    	 'showArrow'=>'1',
						    	 'arrowImg'   =>'UpAccessory_blue.png',
						    	 'Id'    =>"$TypeId",
						    	 'title' =>$rows['TypeName'],
						    	 'col1' =>number_format($rows['Qty']),
						    	 'col1Right'=>number_format($rows['Counts']),
						    	 'col3' =>'¥' .number_format($typeAmount),
						    	 'hasOrderImg'=>"hasOrd",
						    	  'percent' =>"$orderPercent" . "%",
						    	  'rIcon' =>$percent==0?'':'rmb_r',
						    	  'rPercent' =>$percent==0?"":"$percent" . "%"
				           );
			          }
			          
			       break;
			    case 0: //供应商
			         $rowsArray = $this->CkrksheetModel->get_company_amount($warehouseId,$SendFloor);
			          
			          $rownums=count($rowsArray);
			          $stratSign = 0;
			          if ($rownums>10){
				          $rownums=$upTag=='loadall'?$rownums:10;
				          $staSign =$upTag=='loadall'?10:0;
				          $cktapSign = 1;
			          }
			          for ($i=$staSign;$i<$rownums;$i++){
			              $rows = $rowsArray[$i];
			              $CompanyId = $rows['CompanyId'];
			              $typeAmount = round($rows['Amount']);
			             // [@"title",@"col1",@"col2",@"col1Right",@"col2Right",@"col3",@"percent"];
			              $orderRows=$this->CkrksheetModel->get_order_stufftype_amount($warehouseId,$SendFloor,'',$CompanyId);
			              $orderAmount =round($orderRows['Amount']);
			              $orderPercent  = $typeAmount>0? round($orderAmount/$typeAmount*100):0;
			              
			              $percent =$totalAmount>0? round($typeAmount/$totalAmount*100):0;
				          $dataArray[]=array(
				                 'tag'   =>'ck_type',
						    	 'open'=>'',
						    	 'showArrow'=>'1',
						    	 'arrowImg'   =>'UpAccessory_blue.png',
						    	 'Id'    =>"$CompanyId",
						    	 'title' =>$rows['Forshort'],
						    	 'col1' =>number_format($rows['Qty']),
						    	 'col1Right'=>array('Text'=>number_format($rows['Counts']),'light'=>'10'),
						    	 'col3' =>'¥' .number_format($typeAmount),
						    	 'hasOrderImg'=>"hasOrd",
						    	  'percent' =>"$orderPercent" . "%",
						    	  'rIcon' =>$percent==0?'':'rmb_r',
						    	  'rPercent' =>$percent==0?"":"$percent" . "%"
				           );
			          }
			          
			       break;
				
				 case 1://出入库
				 case 21: //出库
				 case 31://入库
				      $rowsArray = $this->CkrksheetModel->get_stuff_outinstock($SendFloor,$segmentIndex);
				      $rownums=count($rowsArray);
				      $stratSign = 0;
			          if ($rownums>10){
				          $rownums=$upTag=='loadall'?$rownums:10;
				          $stratSign =$upTag=='loadall'?10:0;
				          $cktapSign = 1;
			          }
			          
			           for ($i=$stratSign;$i<$rownums;$i++){
			              $rows = $rowsArray[$i];
			               $StuffId = $rows['StuffId'];
			               $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';
			               $imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_picture($StuffId):'';
			               
			               $creator =$this->StaffMainModel-> get_staffname($rows['creator']);
			               $created =$this->datehandler-> GetDateTimeOutString($rows['created'],'');
			              $Decimals = $rows['Decimals']; 
			              
			              $typeColor = $black;
			              $rbgImg = '';
			              $typeImg = 'wh_out';
			              if ($rows['Sign'] == '31'){
				               $typeImg =$rows['Type']==1?'wh_rk_oper':'wh_bp'; 
				               $rbgImg  =$rows['Type']==1?'':'wh_bprk'; 
				               $typeColor = $bluefont;
			              }else{
				              switch($rows['Type']){
				                  case 1:  $typeImg ='wh_bp'; $typeColor  =$ordergreen;   break;
					              case 2:  $typeImg ='wh_bf';     $typeColor =$redcolor;    break;
					              default: $typeImg ='wh_other';$typeColor = $ordergreen; break;
				              }
			              }
			              
			              $actions = array();
						  $actions[]=array('Name'=>'备注','Action'=>'remark','Color'=>'#358fc1');
			              
			              $dataArray[]=array(
				                'tag'=>'ck_stock',
						    	'addedImg'=>$rows['Sign'] == '31' ? 'bluePlus':'greenMinus',
						    	'Id'  =>$StuffId,
						    	'actions'=>$actions,
						    	'StuffId' =>$rows['StuffId'],
						    	'title'=>$rows['StuffCname'],
						    	'col1Img'=>'wh_tstock',
						    	'col1'=>array('dateCom'=>array(),'Text'=>number_format($rows['tStockQty'],$Decimals),'light'=>'11','Color'=>"$black"),
						    	'col2Img'=>'wh_ostock',
						    	'col2'=>number_format($rows['Qty'],$Decimals),
						    	'col3Img'=>$typeImg,
						    	'col3'=>array('Text'=>number_format($rows['Qty'],$Decimals),'Color'=>$typeColor),
						    	//'col4Img'=>$typeImg,
						    	'col4'=>$creator,
						    	'col5'=>$created,
						    	'Picture'    =>$rows['Picture'],
				                'stuffImg'   =>$stuffImg,
				                'imgurl'     =>$imgurl,
				                'location'   =>array('Text'=>$rows['Region'] . $rows['Location'],'border'=>'1','Color'=>$bluefont),
				                'rbgImg' =>$rbgImg
				           );
				           
				           $this->remark_inlist($dataArray,$StuffId);
			          }
	
				   break;
				
				case 2: //库位
				case 22: //按数量    
				    $isPad = element('ISPAD', $params , '0');
				    $capacity = $isPad == 1 ? 5 : 3;
				    
				    $orderbySign= $segmentIndex==22?'qty':'';
		            $rowsArray    = $this->CkrksheetModel->get_stuff_postion($warehouseId,$orderbySign); 
		            $rowcounts   = count($rowsArray);
		            $rownums     = ceil($rowcounts/$capacity);
		            
		            $stratSign = 0;
		            if ($rownums>10){
				          $rownums=$upTag=='loadall'?$rownums:10;
				          $staSign =$upTag=='loadall'?10:0;
				          $cktapSign = 1;
			          }
			          
		            $m=0;
		           for ($i=$stratSign;$i<$rownums;$i++){
		                   $places = array();
							for ($j=0;$j<$capacity && $m<$rowcounts;$j++) {
							
							   $rows = $rowsArray[$m];
							   $overPercent =$rows['Qty']>0?ceil( $rows['OverQty']/$rows['Qty']*100):0;
							   $Percent = 100-$overPercent;
							   
								$chartVal = array(
								       array('value'=>"$overPercent", 'color'=>"$redcolor"),
								       array('value'=>"$Percent", 'color'=>"#57cb79"),
								       array('value'=>'0', 'color'=>'#clear'),
								);
								
								$qtyVal = array(
							        'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>number_format($rows['Qty']),    'FontSize'=>'12'),
								   		array('Text'    =>' ' . $rows['Counts'],     'FontSize'=>'8')
								   	)
						        );
								
								$places[]=array(
									    'Id'  =>'' . $rows['LocationId'],
										'qty'=>$qtyVal,
										'position'=>$rows['Region'] . $rows['Location'],
										'icon'=>$rows['ShelfSign']==1?'wh_place_blue':'',
										'chartVal'=>$chartVal
								);
								$m++;
							}
							
						   $dataArray[]=array(
									  'tag'=>'ck_places',
									  'bgcolor'=>'#f2f2f2',
									  'datas'=>  $places
								    );
				 }
				   break;
				
			}
			
		if ($upTag != 'loadall' && $cktapSign==1 ) {
				$dataArray[]=array('tag'=>'ck_tap','method'=>'segements');
		}
						
	   $totals = count($dataArray);
	    if ($totals > 0) {
		    $dataArray[$totals - 1]['deleteTag'] =$upTag;
	    }

         $data['jsondata']=array('status'=>'1','message'=>"",'totals'=>$totals,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);	
	
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
						    	'showArrow'=>$upTag == 'ck_places' ?'':'1',
						    	'method'=>'sub_pages',
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
						    	'showArrow'=>'1',
						    	'open'=>'',
						    	'method'=>'sub_pages',
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