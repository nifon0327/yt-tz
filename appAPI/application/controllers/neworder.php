<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NewOrder extends MC_Controller {
/*
	功能:今日生产工单
*/
    public $SetTypeId= null;
    public $MenuAction= null;
    public $NoSendQty = null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 1;
        $this->MenuAction   = $this->pageaction->get_actions('shipping');//出货
        
        $this->NoSendQty    = 0;
    }
    
    public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	
	
	public function main()
	{
		$params = $this->input->post();
        $this->load->model('YwOrderSheetModel');
	    
	    $numsOfTypes=0; 
	    
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$white   =$this->colors->get_color('white');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$orderorange =$this->colors->get_color('orderorange');
		$ordergreen  =$this->colors->get_color('ordergreen');
		$ordergray  =$this->colors->get_color('ordergray');
		
	
		$totals=0;
		$query = $this->YwOrderSheetModel->order_new_mon();
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();

		$thisMonth = date('Y-m');
		for ($i = 0; $i < $numsOfTypes; $i++) {
			$rows = $resultArray[$i];
		   /*
			    AS Month,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount 
		   */
		   $Month = element('Month',$rows,'');
		   $cost = $this->YwOrderSheetModel->order_mon_cost($Month) ;
		   $amount = element('Amount',$rows,'');
		   
		   $noProfRow = $this->YwOrderSheetModel->noprof_month_amount($Month);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = $this->YwOrderSheetModel->noprof_month_cost($Month);
		   

		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   $percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
		   $data = array();
		   if ($i==0) {
			   $data = $this->get_mon_subList(0, $Month);
		   }
		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   
		    $timeMon = strtotime($Month);
		    $titleAttr = array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
							   			  'FontSize'=>'14',
							   			  'FontWeight'=>'bold',
							   			  'Color'   =>"#3b3e41"),
							   		array('Text'    =>"\n".date('Y', $timeMon),
							   			  'FontSize'=>'9',
							   			  'Color'   =>"#727171")
							   		)
						   		);
							   		
							   		
		   $dataArray[]=array(
			'tag'        =>'mon_order',
			'type'       =>$Month,
			'Id'         =>$Month,
			'open'       =>$i==0?'1':'0',
			'method'     =>'subList',
			'showArrow'  =>'1',
			'title'      =>$titleAttr,
			'amount'     =>'¥'.number_format($amount),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'value1'     =>''.number_format(element('NoChQty',$rows,'')),
			"value2"     =>array('Text'=>'¥'.$trueProfitShow, 'Color'=>$trueProfit>0? $ordergreen: $red),
			'month'      =>$Month,		
			"percent" =>array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'9',
							   			  'Color'   =>"$white"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'5.5',
							   			  'Color'   =>"$white")
							   		
							   		)
						   		),
			"pieValue"=>
				array(
					
					array("value"=>$percent>25?0:(25-$percent),"color"=>"$ordergray"),
					array("value"=>"$percent","color"=>"$percentcolor")
				),
			"pie2"=>
				array(
					
					
					array("value"=>"30","color"=>"#01be56"),
					array("value"=>"70","color"=>"#clear")
				),
			'chartBgImg'=>'chartFrame',
			'data'       =>$data
	       );
			$totals++;
      }
       
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
   }
	
	
	
	public function top_seg(){
	
	}
	
	
	public function segment()
	{
		$params   = $this->input->post();
		$wsid     = element('top_segId',$params,'0');
		$type     = element('type',$params,'');//生产单位ID
		
	
	}
	
	public function get_day_subList($wsid,$iddate) {
		
		$this->load->model('YwOrderSheetModel');
		$this->load->model('TradeObjectModel');
	    
	    $numsOfTypes=0; 
	    
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$white   =$this->colors->get_color('white');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$orderorange =$this->colors->get_color('orderorange');
		$ordergreen  =$this->colors->get_color('ordergreen');
		$ordergray  =$this->colors->get_color('ordergray');
		
	
		$totals=0;
		$query = $this->YwOrderSheetModel->order_new_company($iddate);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		  $dataArray[]=array(
			'tag'        =>'margin');
		for ($i = 0; $i < $numsOfTypes; $i++) {
			$rows = $resultArray[$i];
		   /*
			  M.CompanyId  Forshort,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount  
		   */
		   $LowProfit = element('LowProfit',$rows,'');
		   $LowProfit = $LowProfit>0?$LowProfit:'';
		   $Forshort = element('Forshort',$rows,'');
		   $CompanyId = element('CompanyId',$rows,'');
		   $Logo = element('Logo',$rows,'');
		   $cost = $this->YwOrderSheetModel->order_date_company_cost($iddate, $CompanyId) ;
		   $lock = $this->YwOrderSheetModel->check_lock_date_company($iddate, $CompanyId);
		   $explock = $this->YwOrderSheetModel->check_explock_date_company($iddate, $CompanyId);
		   
		   $noProfRow = $this->YwOrderSheetModel->noprof_date_company_amount($iddate, $CompanyId);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = $this->YwOrderSheetModel->noprof_date_company_cost($iddate, $CompanyId);
		   
		   $amount = element('Amount',$rows,'');
		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   $percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
		   
		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   
		  
		   $dataArray[]=array(
			'tag'        =>'com_order',
			'type'       =>$CompanyId,
			'Id'         =>$iddate,
			'open'       =>'0',
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2,
			'method'     =>'subList',
			'showArrow'  =>'1',
			'arrowImg'   =>'arrow_gray_s',
			'title'      =>$Forshort,
			'titleImg'   =>$LogoPath.$Logo,
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
			'amount'     =>'¥'.number_format($amount),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'value1'     =>''.number_format(element('NoChQty',$rows,'')),
			"value2"     =>array('Text'=>'¥'.$trueProfitShow, 'Color'=>$trueProfit>0? $ordergreen: $red),	
			"percent" =>array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'9',
							   			  'Color'   =>"$white"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'5.5',
							   			  'Color'   =>"$white")
							   		
							   		)
						   		),
			"pieValue"=>
				array(
					
					array("value"=>$percent>25?0:(25-$percent),"color"=>"$ordergray"),
					array("value"=>"$percent","color"=>"$percentcolor")
				)
	       );
 $dataArray[]=array(
			'tag'        =>'margin');
      }
       
		return $dataArray;

	}

	public function get_com_subList($CompanyId, $iddate) {
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$white   =$this->colors->get_color('white');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$orderorange =$this->colors->get_color('orderorange');
		$ordergreen  =$this->colors->get_color('ordergreen');
		$ordergray  =$this->colors->get_color('ordergray');
		
		$thisDay = date('m-d');
		$this->load->model('YwOrderSheetModel');
		$this->load->model('TradeObjectModel');
		$this->load->model('ProductdataModel');
		
		$rowarray = $this->YwOrderSheetModel->order_date_company_list($iddate,$CompanyId);

		

		$dataArray = array();
		
		foreach ($rowarray as $rows) {
			/*S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.Qty*S.Price AS Amount,S.ShipType,S.Estate,S.scFrom,P.cName,P.TestStandard,
                             PI.Leadtime,YEARWEEK(PI.Leadtime,1)  AS Weeks */
			$POrderId = element('POrderId',$rows,'');
			$PreChar  = element('PreChar',$rows,'');
			$ProductId = element('ProductId',$rows,'');
			
			
			$oper = element('Operator',$rows,'');
			$TestStandard = element('TestStandard',$rows,'');
			if ($TestStandard > 0) {
				$icon = $this->ProductdataModel->get_picture_path($ProductId);
			} else {
				$icon = '';
			}

			$noProfit = $this->YwOrderSheetModel->checkIsNoProfit($POrderId);
			if ($noProfit > 0) {
				$percent = '--%';
				$percentcolor = $black;
			} else {
				$profitArray  = $this->YwOrderSheetModel->getOrderProfit($POrderId);
				$percent      = element('percent',$profitArray,'-').'%';
				$percentcolor = element('color',$profitArray,'-');
				if ($percent > 10 ) {
					$percentcolor = $ordergreen;
				}
			}
			$lockArray = $this->YwOrderSheetModel->check_lock($POrderId);
			
			$lock = element('lock',$lockArray,0);
			
			$lockImg = '';
			$remark = '';
			switch ($lock) {
				case 1:
					$lockImg = 'order_lock';
					$oper    = element('oper',$lockArray,'');
					$remark  = element('remark',$lockArray,'');
					//$remark .= element('cgRemark',$rows,'');
				break;
				case 2:
				case 3:
					$lockImg = 'order_lock_s';
					$oper    = element('oper',$lockArray,'');
					$remark  = element('remark',$lockArray,'');
				break;
				default:
				break;
			}
			
			
			 $dataArray[]=array(
			'tag'        =>'new_order',
			'open'     =>'0',
			'segIndex'   =>'-1',
			'method'     =>'subList',
			'showArrow'  =>'1',
			'week'       =>element('Weeks',$rows,''),
			'title'      =>element('cName',$rows,''),
			'icon'       =>$icon,
			'Picture'    =>$TestStandard,
			'POrderId'   =>$POrderId,
			'Id'      =>"$POrderId",		
			'col1'    =>element('OrderPO',$rows,''),
			'col2'    =>number_format(element('Qty',$rows,'')),
			'col3'    =>$PreChar. round(element('Price',$rows,''),2),
			'col4'    =>array('Text'=>$percent, 'Color'=>$percentcolor),
			'col5'    =>$PreChar.number_format(element('Amount',$rows,'')),
			'oper'    =>$oper,
			'lockImg' =>$lockImg,
			'remark'  =>$remark
	       );

		}
		
		
		return $dataArray;
	}

	
	public function get_mon_subList($wsid,$month) {
		
		$this->load->model('YwOrderSheetModel');
	    
	    $numsOfTypes=0; 
	    
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$white   =$this->colors->get_color('white');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$orderorange =$this->colors->get_color('orderorange');
		$ordergreen  =$this->colors->get_color('ordergreen');
		$ordergray  =$this->colors->get_color('ordergray');
		
	
		$totals=0;
		$query = $this->YwOrderSheetModel->order_new_date($month);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();

		for ($i = 0; $i < $numsOfTypes; $i++) {
			$rows = $resultArray[$i];
		   /*
			    OrderDate,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount  
		   */
		   $OrderDate = element('OrderDate',$rows,'');
		   $LowProfit = element('LowProfit',$rows,'');
		   $LowProfit = $LowProfit>0?$LowProfit:'';
		   $cost = $this->YwOrderSheetModel->order_date_cost($OrderDate) ;
		   
		   $lock = $this->YwOrderSheetModel->check_lock_date($OrderDate);
		   $explock = $this->YwOrderSheetModel->check_explock_date($OrderDate);
		   
		   
		    $noProfRow = $this->YwOrderSheetModel->noprof_date_amount($OrderDate);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = $this->YwOrderSheetModel->noprof_date_cost($OrderDate);
		   
		   $amount = element('Amount',$rows,'');
		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   $percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);

		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   
		   $weekday = date('w',strtotime($OrderDate));
		   $title = date('m-d',strtotime($OrderDate));
		   
		   $istoday = $OrderDate == $this->Date ? true : false;
// 		   $istoday = false;



			$dateCom = explode('-', $title);
			
		   $dataArray[]=array(
			'tag'        =>'day_order',
			'type'       =>$OrderDate,
			'Id'         =>$OrderDate,
			'open'       => $istoday ? '1':'0',
			'method'     =>'subList',
			'showArrow'  =>'1',
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'title'       =>array('Text'=>$title,'Color'=>($weekday==0 ||$weekday==6)?'#ff665f':'#9a9a9a','dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?'#ffa5a0':'#cfcfcf','dateBg'=> $istoday?'#ffff2e':''),
			'amount'     =>'¥'.number_format($amount),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'value1'     =>''.number_format(element('NoChQty',$rows,'')),
			"value2"     =>array('Text'=>'¥'.$trueProfitShow, 'Color'=>$trueProfit>0? $ordergreen: $red),
			"percent" =>array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'9',
							   			  'Color'   =>"$white"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'5.5',
							   			  'Color'   =>"$white")
							   		
							   		)
						   		),
			"pieValue"=>
				array(
					
					array("value"=>$percent>25?0:(25-$percent),"color"=>"$ordergray"),
					array("value"=>"$percent","color"=>"$percentcolor")
				)
	       );
		if ($istoday) {
			$data = $this->get_day_subList(0,$OrderDate);
			$dataArray = array_merge($dataArray,$data);
		}
      }
       
		return $dataArray;
	}

	
	public function subList(){
	    
	    $params   = $this->input->post();
	    $wsid     = element('type',$params,'');//生产单位ID
	    $id       = element('Id',$params,'');
	    $upTag    = element('upTag',$params,'');
	    $segmentIndex = intval( element("segmentIndex",$params,0));
	    
	    $listTag='';
	    $dataArray=array();
	    switch ($upTag) {
			case  'day_order': 
			         $listTag = 'com_order';
			         $dataArray=$this->get_day_subList($wsid,$id);  
			         break;
			case   'mon_order':
					$listTag = 'day_order';
			        $dataArray=$this->get_mon_subList($wsid,$id);
					break;
			case   'com_order':
					$listTag = 'new_order';
			        $dataArray=$this->get_com_subList($wsid,$id);
					break;
		}
		
		$rownums=count($dataArray);
		if ($rownums>0){
			$dataArray[$rownums-1]["deleteTag"] = $upTag;
		}

		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
		
	}
}
