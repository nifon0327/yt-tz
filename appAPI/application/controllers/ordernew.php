<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ordernew extends MC_Controller {
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
		
		$this->load->model('LoginUser');
		
		
		$this->can_see_profit = $this->LoginUser->check_authority_Items('144');
		
		
    }
    
    public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	function allmonths_list($CompanyId='') {
		$params = $this->input->post();
        $this->load->model('YwOrderSheetModel');
	    
	    $numsOfTypes=0; 
	    
		
		$cansee = $this->can_see_profit;
		
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
		$query = $this->YwOrderSheetModel->order_new_mon($CompanyId);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();
        
        // 
        
        $costGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->noprof_month_cost_groups('',$CompanyId, 'month');
	        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $acost = $Arows['oTheCost'];
		        $costGroups[$amonth]= $acost;
	        } 
        }
        
        
        $amtGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->noprof_month_amount_groups('',$CompanyId, 'month');
        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $amtGroups[$amonth]= $Arows;
	        } 
        }
        
        $allcostGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->order_mon_cost_groups('',$CompanyId, 'month');
        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $acost = $Arows['oTheCost'];
		        $allcostGroups[$amonth]= $acost;
	        } 
        }
        
        
        $LocksGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->check_lock_date_company_groups('', $CompanyId, '', 'month');
	        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $acost = $Arows['Locks'];
		        $LocksGroups[$amonth]= $acost;
	        } 
        }
        
        

		$thisMonth = date('Y-m');
		for ($i = 0; $i < $numsOfTypes; $i++) {
			$rows = $resultArray[$i];
		   /*
			    AS Month,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount 
		   */
		   $Month = element('Month',$rows,'');
		   //$cost = $this->YwOrderSheetModel->order_mon_cost($Month, $CompanyId) ;
		   
		   $cost = element("$Month", $allcostGroups,0);
		   $amount = element('Amount',$rows,'');
		   $LowProfit = element('LowProfit',$rows,'');
		   $LowProfit = $LowProfit>0?$LowProfit:'';
		   $noProfRow =  element($Month, $amtGroups, array()); //$this->YwOrderSheetModel->noprof_month_amount($Month, $CompanyId);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = 0;
		   if ($vag2 > 0) {
			   $noProfCost = element("$Month",$costGroups,0);
		   }
		  // $noProfCost = $this->YwOrderSheetModel->noprof_month_cost($Month, $CompanyId);
		   

		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   $percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
		   $data = array();
		   if ($i==0 && $CompanyId=='') {
			   $data = $this->get_mon_subList(0, $Month);
		   }
		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   $lock = $explock = '';
		   if ($CompanyId!='') {
			   $lock = element("$Month", $LocksGroups,''); //$this->YwOrderSheetModel->check_lock_date_company('', $CompanyId,$Month);
		       $explock = $this->YwOrderSheetModel->check_explock_date_company('', $CompanyId,$Month);
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
			 $Counts = element('Counts',$rows,'');
							   		
		   $onedatas=array(
			'tag'        =>$CompanyId!=''?'day_order': 'mon_order',
			'type'       =>$CompanyId!=''? $CompanyId: $Month,
			'Id'         =>$Month,
			'open'       =>($i==0 && $CompanyId=='')?'1':'0',
			'method'     =>'subList',
			'showArrow'  =>'1',
			'title'      =>$titleAttr,
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
			'amount'     =>'¥'.number_format($amount),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'month'      =>$Month,	
			'vag2'=>$CompanyId!=''?"$vag2":null,
			'vag1'=>$CompanyId!=''?$LowProfit:null,
			'counts'	 =>$Counts,
			"percent" =>
						$percent >= 0 ? 
						array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'6',
							   			  'Color'   =>"$percentcolor")
							   		
							   		)
						   		)
						   		:array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>'-',
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>"".(0-$percent),
							   			  'FontSize'=>'24',
							   			  'Color'   =>"$percentcolor",
							   			  'FontName'=>'AshCloud61'),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'8',
							   			  'Color'   =>"$percentcolor",
							   			   'FontName'=>'AshCloud61')
							   		
							   		)
						   		),
			"pie2"=>
			
			$percent >= 0 ? 
				array(
					array("value"=>$percent,"color"=>"$percentcolor"),
					array("value"=>100-$percent,"color"=>$CompanyId!=''?'clear': "#efefef")
				) : array()
				,
			'chartBgImg'=>$CompanyId!=''?'chartFrame':'chartFrame2',
			'data'       =>$data
	       );
	       if ($cansee == false) {
		       unset($onedatas['percent']);
		       unset($onedatas['pie2']);
	       }
	       $dataArray[]=$onedatas;

			$totals++;
      }

	  return $dataArray;
	}
	
	function sub_months() {
		$params = $this->input->post();
		
		$menu_id = element('menu_id', $params, 0);
		
		$CompanyId = element('Id', $params, 0);
		
		$dataArray = $this->allmonths_list($CompanyId);
		       
		
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>'','rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
		
	}
	function menus() {
		
		$params = $this->input->post();
		
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>'','rows'=>null);
	    
	    $this->load->view('output_json',$data); 
		
	}
	
	function all_companys_list() {
		
		$this->load->model('YwOrderSheetModel');
		$this->load->model('TradeObjectModel');
	    
	    $numsOfTypes=0; 
	    
	    $cansee = $this->can_see_profit;
	    
		
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
		$query = $this->YwOrderSheetModel->order_new_company();
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();
		$Month = '';
		$thisMonth = date('Y-m');
		
		$costGroups = array();
		if ($cansee) {
			$nocostgroup = $this->YwOrderSheetModel->noprof_month_cost_groups('','', 'com');
        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['CompanyId'];
		        $acost = $Arows['oTheCost'];
		        $costGroups[$amonth]= $acost;
	        }
		}
		 
        $allcostGroups = array();
		if ($cansee) {
			$nocostgroup = $this->YwOrderSheetModel->order_mon_cost_groups('','', 'com');
	        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['CompanyId'];
		        $acost = $Arows['oTheCost'];
		        $allcostGroups[$amonth]= $acost;
	        } 
        }
        $amtGroups = array();
        if ($cansee) {
	        $nocostgroup = $this->YwOrderSheetModel->noprof_month_amount_groups('','', 'com');
	        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['CompanyId'];
		        $amtGroups[$amonth]= $Arows;
	        } 
        }
        

		$LogoPath = $this->TradeObjectModel->get_logo_path();
		for ($i = 0; $i < $numsOfTypes; $i++) {
			/*
				C.Forshort,M.CompanyId,D.PreChar,C.Logo,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,SUM(S.Qty*S.Price) AS realAmount,COUNT(*) as Counts,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount ,SUM(IF(F.Percent<3,1,0)) AS LowProfit 
			*/
			$rows = $resultArray[$i];
			
			$CompanyId=$rows['CompanyId'];
			$Logo=$rows['Logo'];
			$Forshort=$rows['Forshort'];

		  // $cost = $this->YwOrderSheetModel->order_mon_cost($Month, $CompanyId) ;
		  $cost =  element("$CompanyId",$allcostGroups,0);
		   $amount = element('Amount',$rows,'');
		  
		   
		   $noProfRow = element("$CompanyId", $amtGroups,null); ;//$this->YwOrderSheetModel->noprof_month_amount($Month, $CompanyId);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = 0;
		   if ($vag2 >0) {
			   $noProfCost = element("$CompanyId",$costGroups,0);
		   }
		   //$noProfCost = $this->YwOrderSheetModel->noprof_month_cost($Month, $CompanyId);
		   

		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   $percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
		   $data = array();
		   
		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   
		   
			 $Counts = element('Counts',$rows,'');
							   		
		   $onedatas=array(
			'tag'        =>'mon_order',
			'type'       =>''.$CompanyId,
			'Id'         =>$CompanyId,
			'method'     =>'sub_months',
			'showArrow'  =>'1',
			'forshort'=>$Forshort,
			'titleImg'=>$LogoPath.$Logo,
			'amount'     =>'¥'.number_format($amount),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'month'      =>$Month,	
			'counts'	 =>$Counts,
			"percent" =>
						$percent >= 0 ? 
						array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'6',
							   			  'Color'   =>"$percentcolor")
							   		
							   		)
						   		)
						   		:array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>'-',
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>"".(0-$percent),
							   			  'FontSize'=>'24',
							   			  'Color'   =>"$percentcolor",
							   			  'FontName'=>'AshCloud61'),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'8',
							   			  'Color'   =>"$percentcolor",
							   			   'FontName'=>'AshCloud61')
							   		
							   		)
						   		),
			"pie2"=>
			
			$percent >= 0 ? 
				array(
					array("value"=>$percent,"color"=>"$percentcolor"),
					array("value"=>100-$percent,"color"=>"#efefef")
				) : array()
				,
			'chartBgImg'=>'chartFrame2',
			'data'       =>$data
	       );
	       
	       if ($cansee == false) {
		       unset($onedatas['pie2']);
		       unset($onedatas['percent']);
	       }
	       $dataArray[]=$onedatas;
			$totals++;
      }

	  return $dataArray;
		
	}
	
	public function main()
	{
		$params = $this->input->post();
		$dataArray = array();
		$menu_id = element('menu_id', $params, 0);
		switch ($menu_id) {
			case 0:{
				$dataArray = $this->allmonths_list();
			}
			break;
			case 1:{
				$dataArray = $this->all_companys_list();
			}
			break;
		}
		       
		
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>'','rows'=>$dataArray);
	    
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
		
	
		$cansee = $this->can_see_profit;
		$totals=0;
		$query = $this->YwOrderSheetModel->order_new_company($iddate);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		  $dataArray[]=array(
			'tag'        =>'margin','height'=>'1.5');
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
		   if ($cansee)
		   $cost = $this->YwOrderSheetModel->order_date_company_cost($iddate, $CompanyId) ;
		   else $cost=0;
		   $lock = $this->YwOrderSheetModel->check_lock_date_company($iddate, $CompanyId);
		   $explock = $this->YwOrderSheetModel->check_explock_date_company($iddate, $CompanyId);
		   
		   if ($cansee)
		   $noProfRow = $this->YwOrderSheetModel->noprof_date_company_amount($iddate, $CompanyId);
		   else $noProfRow = null;
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   $noProfCost = 0;
		   if ($vag2 > 0)
		   $noProfCost = $this->YwOrderSheetModel->noprof_date_company_cost($iddate, $CompanyId);
		   
		   $amount = element('Amount',$rows,'');
		   $profit = $amount-$cost;
		   $trueAmount = $amount-$noProfAm;
		   $trueProfit = $profit-$noProfAm+$noProfCost;
		   //$percent = $trueAmount>0?(round($trueProfit/$trueAmount*100)):0;
		   if($trueAmount>0){
			   $percent = round($trueProfit/$trueAmount*100);
		   }else {
		       //卖价为0时
			   $percent = round($profit/$cost*100);
		   }
		   $percentcolor = $this->YwOrderSheetModel->getcolor_profit($percent);
		   
		   $trueProfitShow = number_format($trueProfit);
		   if ($noProfAm == $amount) {
			   $percent = '--';
			   $trueProfitShow = '--';
		   }
		   
		   $Counts = element('Counts',$rows,'');

		   $onedatas=array(
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
			'counts'=>$Counts,
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
			'amount'     =>$rows['PreChar'].number_format($rows['realAmount']),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'percentY'=>'-6',
			"percent" =>
			$percent>=0?
			array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'6',
							   			  'Color'   =>"$percentcolor")
							   		
							   		)
						   		)
						   		:
						   		array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>'-',
							   			  'FontSize'=>'9',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>"".(0-$percent),
							   			  'FontSize'=>'20',
							   			  'Color'   =>"$percentcolor",
							   			  'FontName'=>'AshCloud61'),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'7',
							   			  'Color'   =>"$percentcolor",
							   			   'FontName'=>'AshCloud61')
							   		
							   		)
						   		)
						   		,
	       );
	       if ($cansee == false) {
		       unset($onedatas['percentY']);
		       unset($onedatas['percent']);
	       }
	       $dataArray[]=$onedatas;
 $dataArray[]=array(
			'tag'        =>'margin','height'=>'1.5');
      }
       
		return $dataArray;

	}

	public function get_com_subList($CompanyId, $iddate, $Month='') {
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$white   =$this->colors->get_color('white');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$orderorange =$this->colors->get_color('orderorange');
		$ordergreen  =$this->colors->get_color('ordergreen');
		$ordergray  =$this->colors->get_color('ordergray');
		
		$cansee = $this->can_see_profit;
		
		
		$thisDay = date('m-d');
		$this->load->model('YwOrderSheetModel');
		$this->load->model('TradeObjectModel');
		$this->load->model('ProductdataModel');
		
		$rowarray = $this->YwOrderSheetModel->order_date_company_list($iddate,$CompanyId, $Month);

		

		$dataArray = array();
		
		foreach ($rowarray as $rows) {
		
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

			$percentcolor = '';
			$percent = '';
			if ($cansee) {
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
			} else {
				$noProfit = 1;
				
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
			'cellMargin'=>'0',
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
			'col4'    =>
			$percent>=0?
			array('Text'=>$percent, 'Color'=>$percentcolor)
			
			:array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>'-',
							   			  'FontSize'=>'9',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>"".(0-$percent),
							   			  'FontSize'=>'18',
							   			  'Color'   =>"$percentcolor",
							   			  'FontName'=>'AshCloud61'),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'6',
							   			  'Color'   =>"$percentcolor",
							   			   'FontName'=>'AshCloud61')
							   		
							   		)
						   		)
			,
			'col5'    =>$PreChar.number_format(element('Amount',$rows,'')),
			'oper'    =>$oper,
			'lockImg' =>$lockImg,
			'lockBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									),
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
		
		$cansee = $this->can_see_profit;
		
	
		$totals=0;
		$query = $this->YwOrderSheetModel->order_new_date($month);
		$resultArray = $query->result_array();
		$numsOfTypes = count($resultArray);
        $dataArray  = array();
        
        $costGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->noprof_month_cost_groups($month,'', 'date');
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $acost = $Arows['oTheCost'];
		        $costGroups[$amonth]= $acost;
	        } 
        }
        
        $allcostGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->order_mon_cost_groups($month,'', 'date');
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $acost = $Arows['oTheCost'];
		        $allcostGroups[$amonth]= $acost;
	        } 
        }
        $amtGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->noprof_month_amount_groups($month,'', 'date');
	        
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $amtGroups[$amonth]= $Arows;
	        } 
        }
        
        $LocksGroups = array();
        if ($cansee == true) {
	        $nocostgroup = $this->YwOrderSheetModel->check_lock_date_company_groups('', '', $month, 'date');
	        foreach ($nocostgroup as $Arows) {
		        $amonth = $Arows['Month'];
		        $acost = $Arows['Locks'];
		        $LocksGroups[$amonth]= $acost;
	        } 
        }
        
        


		for ($i = 0; $i < $numsOfTypes; $i++) {
			$rows = $resultArray[$i];
		   /*
			    OrderDate,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount  
		   */
		   $OrderDate = element('OrderDate',$rows,'');
		   $LowProfit = element('LowProfit',$rows,'');
		   $LowProfit = $LowProfit>0?$LowProfit:'';
 		//   $cost = $this->YwOrderSheetModel->order_date_cost($OrderDate) ;
$cost = element($OrderDate, $allcostGroups, 0);

//$cost =  $allcostGroups[$OrderDate];
		   
		   $lock =  element($OrderDate, $LocksGroups, ''); //$this->YwOrderSheetModel->check_lock_date($OrderDate);
		   $explock = $this->YwOrderSheetModel->check_explock_date($OrderDate);
		   
		   
		    $noProfRow = element($OrderDate, $amtGroups, null); ;//$this->YwOrderSheetModel->noprof_date_amount($OrderDate);
		   $vag2 = '';
		   $noProfAm = 0;
		   if ($noProfRow != null) {
			   $vag2 = $noProfRow['Nums'];
			   $noProfAm = $noProfRow['Amount'];
			   $vag2 = $vag2>0 ? ''.$vag2 : '';
		   }
		   
		   $noProfCost = 0;
		   if ($vag2 > 0)
			   $noProfCost = element($OrderDate, $costGroups, 0);
		   //$noProfCost = $this->YwOrderSheetModel->noprof_date_cost($OrderDate);
		   
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
 		  



			$dateCom = explode('-', $title);
			
			 $Counts = element('Counts',$rows,'');
			
		   $onedatas =array(
			'tag'        =>'day_order',
			'type'       =>$OrderDate,
			'Id'         =>$OrderDate,
			'open'       => $istoday ? '0':'0',
			'method'     =>'subList',
			'$cost'=>$cost,
			'showArrow'  =>'1',
			'lockImg'    =>$explock > 0 ? 'orders_lock':'',
			'lock_sImg'  =>$lock > 0 ? 'orders_lock_s':'',
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'datecom1'   =>$dateCom[0],
			'datecom2'   =>$dateCom[1],
			'dateBg'=>$istoday?$this->color_todayyellow:'',
			'fmColor'=>($weekday==0 ||$weekday==6)?$this->color_weekredborder:$this->color_daybordergray,
			'dateColor'=>($weekday==0 ||$weekday==6)?$this->color_weekred:$this->color_daygray,
			
// 			'title'       =>array('Text'=>$title,'Color'=>($weekday==0 ||$weekday==6)?$this->color_weekred:$this->color_daygray,'dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?$this->color_weekredborder:$this->color_daybordergray,'dateBg'=> $istoday?$this->color_todayyellow:''),
			'amount'     =>'¥'.number_format($amount),
			"qty"        =>''.number_format(element('Qty',$rows,'')),
			'counts'=>$Counts.'',
			'chartBgImg'=>'chartFrame',
			"percent" =>
						$percent >= 0 ? 
						array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>"$percent",
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'6',
							   			  'Color'   =>"$percentcolor")
							   		
							   		)
						   		)
						   		:array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>'-',
							   			  'FontSize'=>'11',
							   			  'Color'   =>"$percentcolor"),
							   		array('Text'    =>"".(0-$percent),
							   			  'FontSize'=>'24',
							   			  'Color'   =>"$percentcolor",
							   			  'FontName'=>'AshCloud61'),
							   		array('Text'    =>'%',
							   			  'FontSize'=>'8',
							   			  'Color'   =>"$percentcolor",
							   			   'FontName'=>'AshCloud61')
							   		
							   		)
						   		),
			"pie2"=>
			
			$percent >= 0 ? 
				array(
					array("value"=>$percent,"color"=>"$percentcolor"),
					array("value"=>100-$percent,"color"=>"#clear")
				) : array()
	       );
	       
	       if ($cansee == false ) {
		       unset($onedatas['pie2']);
		       unset($onedatas['percent']);
		       unset($onedatas['chartBgImg']);
	       }
	       
	       $dataArray[]=$onedatas;
/*
		if ($istoday) {
			$data = $this->get_day_subList(0,$OrderDate);
			$dataArray = array_merge($dataArray,$data);
		}
*/
      }
       
		return $dataArray;
	}

	
	public function subList(){
	    
	    $params   = $this->input->post();
	    $wsid     = element('type',$params,'');//生产单位ID
	    $id       = element('Id',$params,'');
	    $upTag    = element('upTag',$params,'');
	    $menu_id    = element('menu_id',$params,'0');
	    $segmentIndex = intval( element("segmentIndex",$params,0));
	    
	    $listTag='';
	    $dataArray=array();
	    switch ($upTag) {
			case  'day_order': 
			         $listTag = 'com_order';
			         if ($menu_id==0) {
				         $dataArray=$this->get_day_subList($wsid,$id); 
			         } else {
				         $dataArray=$this->get_com_subList($wsid,'',$id);
			         }
			          
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
