<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipped extends MC_Controller {
	
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
        
        
        /*
	        'red'   => $this->color_red,
			'green' => '#00ff00',
			'blue'  => '#0000ff',
			'yellow'=> '#ff00ff',
			'orange'=> '#FF6633',
			'purple'=> '#800080',
			'qty'   => '#459fd1',
			'lightgreen' => $this->color_lightgreen,
			'lightgray'  => '#bbbbbb',
			'lightblue'  => '#ccffff',
			'yellowgreen'=> '#c3ff64',
			'bluefont'   => $this->color_bluefont,
			'grayfont'   => $this->color_grayfont,
			'lightgray2' => '#dddddd',
			'superdark'  => $this->color_superdark,
			'ordergray'  => '#b0b5ba',
			'orderorange'  => '#f09300',
			'ordergreen'  => '#01be56',
			'weekred'     =>$this->color_weekred,
			'weekredborder'     =>$this->color_weekredborder,
			'daybordergray'    =>$this->color_daybordergray,
			'daygray'    =>$this->color_daygray,
			'todayyellow'=>$this->color_todayyellow
        */
        
    }
    
       
    function company_submonths($CompanyId) {
	    
	    $this->load->model('Ch1shipsheetModel');
		
		$query = $this->Ch1shipsheetModel->shipped_months($CompanyId);
		$subList = array();
		$allAmount = 1;
		//$this->Ch1shipsheetModel->month_amount('',$CompanyId);
		
		
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {
				
				$month = $rows['Month'];
				$puncInfo = $this->Ch1shipsheetModel->get_order_punctuality(2, $month, $CompanyId);
				
				
				$allAmount = $this->Ch1shipsheetModel->month_amount($month);
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

				$prechar = $rows['PreChar'];
				$NoPayAmount = $rows['NoPayAmount'];
				
				$payed = $this->Ch1shipsheetModel->month_payed($month, $CompanyId);
				
				$NoPayAmount=$NoPayAmount-$payed;
				
			    $timeMon = strtotime($month);
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
							   		
				$Rate = $rows['Rate'];
				
				$percent = '';
				$Amount = $rows['Amount'];
				if ($allAmount > 0) {
					$percent =round($Amount / $allAmount *100);
				}
				$col1Obj = null;
				if ($percent>0) {
					$percentColor = $this->color_grayfont;
					$col1Obj=array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$percent",
					   			  'FontSize'=>'12',
					   			  'Color'   =>"$percentColor"),
					   		array('Text'    =>'%',
					   			  'FontSize'=>'8',
					   			  'Color'   =>"$percentColor")
					   		
					   	)
				    );

				}


				$subList[]=array(
					'tag'=>'shtotal',
					'Id'=>$CompanyId,
					'type'=>$month,
					'segIndex'=>'-1',
					'showArrow'=>'1',
					'open'=>'',
					'pieValue'=>$chartValInner,
					'percent'=>$chartPercent,
					'title'=>$titleAttr,
					'nopayed'=>$NoPayAmount,
					'col4'=>$prechar.number_format($NoPayAmount),
					'col3'=>$prechar.number_format($Amount),
					'col2'=>number_format($rows['Qty']),
					'col1'=>$col1Obj,
					'faceImg'=>''
				);

			}
			
		}
		
				
		return $subList;
    }
    
    function month_subcompanys($month) {
	    
	    $this->load->model('Ch1shipsheetModel');
	    $this->load->model('TradeObjectModel');
	    $query = $this->Ch1shipsheetModel->month_subcompanys($month);
		$subList = array();
		
		$allAmount = $this->Ch1shipsheetModel->month_amount($month);
		
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {

				$Forshort = $rows['Forshort'];
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
				
				$puncInfo = $this->Ch1shipsheetModel->get_order_punctuality(2, $month, $CompanyId);
				
				$percent = $puncInfo['percent'];
				$percentColor = $puncInfo['color'];
				if ($percent==0) {
					$percentColor = $this->color_red;
				}
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

				$prechar = $rows['PreChar'];
				$Amount = $rows['Amount'];
				$Rate = $rows['Rate'];
				
				$percent = '';
				if ($allAmount > 0) {
					$percent =round($Amount / $allAmount *100);
				}
				$col1Obj = null;
				if ($percent>0) {
					$percentColor = $this->color_grayfont;
					$col1Obj=array(
				        'isAttribute'=>'1',
				   		'attrDicts'=>array(
					   		array('Text'    =>"$percent",
					   			  'FontSize'=>'12',
					   			  'Color'   =>"$percentColor"),
					   		array('Text'    =>'%',
					   			  'FontSize'=>'8',
					   			  'Color'   =>"$percentColor")
					   		
					   	)
				    );

				}
				
				
				
				
				$payed = $this->Ch1shipsheetModel->get_payed($month,$CompanyId);
				
				$NoPayAmount=$Amount-$payed;
				
			    $timeMon = strtotime($month);
			    

				$subList[]=array(
					'tag'=>'shtotal',
					'Id'=>''.$CompanyId,
					'type'=>$month,
					'segIndex'=>'-1',
					'open'=>'',
					'titleIsImg'=>'1',
					'showArrow'=>'1',
					'pieValue'=>$chartValInner,
					'percent'=>$chartPercent,
					'iconTitle'=>$Forshort,
					'titleImg'=>$LogoPath.$Logo,
					'nopayed'=>$NoPayAmount,
					'col4'=>$prechar.number_format($NoPayAmount),
					'col3'=>$prechar.number_format($Amount),
					'col2'=>number_format($rows['Qty']),
					'col1'=>$col1Obj,
					'faceImg'=>''
				);

				
			}
	    }
	    return $subList;
	    
    }
        
	function segements() {
		

		
		$params = $this->input->post();
		$subList = array();
	    $menu_id = element('menu_id', $params , '0');
	    $Id = element('Id', $params , '0');

		switch ($menu_id) {
			case 0: $subList = $this->month_subcompanys($Id);
			break;
			case 1: $subList = $this->company_submonths($Id);
			break;
		}
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$subList);
	    
		$this->load->view('output_json',$data);
		
	}
	
	public function menu()
	{
	     $params = $this->input->post();
	     
	     $dataArray[]=array(
					      'cellType'=>"1",
						  'title'       =>"已出",
						  'selected'=>"1",
						  'Id'          =>"0"
					  );
		$dataArray[]=array(
					      'cellType'=>"1",
						  'title'       =>"客人",
						  'selected'=>"1",
						  'Id'          =>"1"
					  );
	    
	     $status=count($dataArray)>0?1:0;
		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$dataArray);
		 $this->load->view('output_json',$data);
	}

	function company_month_list($CompanyId, $month, $aInvoiceNO='') {
		
		$this->load->model('Ch1shipsheetModel');
	    $this->load->model('TradeObjectModel');
	    
	    
	    
	    $query = $this->Ch1shipsheetModel->month_company_list($month,$CompanyId,$aInvoiceNO);
		$subList = array();
		$downLoadPath = $this->config->item('download_path').'/';
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			if ($aInvoiceNO!='') {
				$aRow = $query->row();
				$CompanyId = $aRow->CompanyId;
			}
			
			$prechar = $this->TradeObjectModel->get_prechar($CompanyId);
		    $Forshort = $this->TradeObjectModel->get_forshort($CompanyId);
	    
			
			foreach ($rs as $rows) {

				$InvoiceNO = $rows['InvoiceNO'];
				$Mid = $rows['Id'];
				$date = $rows['Date'];
				
				$istoday = $date==$this->Date ? true : false;
				$ShipType=$rows["Ship"];
				if ($rows["ShipType"]=='credit' || $rows["ShipType"]=='debit'){
		           $ShipType=$rows["ShipType"]=='credit'?31:32;
			    }
			    $DeclareType=$rows["Type"]==1?1:0; 
				
				$weekday = date('w',strtotime($date));
				$dateCom = explode('-', substr($date, 5));
				
				$titleimgs = array();
				$titleimgs[]= 'ship'.$ShipType;
				if ($DeclareType == 1) {
					$titleimgs[]='declare.png';
				}
				
			    $titleTopObj = array('Text'=>'','Color'=>($weekday==0 ||$weekday==6)?$this->color_weekred:$this->color_daygray,'dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?$this->color_weekredborder:$this->color_daybordergray,'light'=>'12.5','subImgs'=>$titleimgs,'eachImgFrame'=>'43,2,15,15','dateBg'=> $istoday?$this->color_todayyellow:'');
				
// 				$puncInfo = $this->Ch1shipsheetModel->get_order_punctuality(3, $Mid);
				$Amount = $rows['Amount'];
				$payed = $this->Ch1shipsheetModel->get_payed('','',$Mid);
				$NoPayAmount=$Amount-$payed;
				$AmountColor = round($NoPayAmount) == '0' ? $this->color_lightgreen:$this->color_superdark; 
				if ($payed > 0 && $NoPayAmount!='0') {
					$AmountColor = $this->color_bluefont;	
				}
				
				$imgsArr = array();
				$InvoiceFile = $rows["InvoiceFile"];
				$InvoiceFilePath = "";
					
				if ($InvoiceFile==1){
					$InvoiceFilePath="$downLoadPath"."invoice/".rawurlencode($InvoiceNO).".pdf";
					$imgsArr[]= array(
				    "url"=>"$InvoiceFilePath",
				    "title"=>" invoice",
				    "url_thumb"=>"$InvoiceFilePath",
				    "Type"=>"html",
				    'static'=>'invoice_s',
				    "NavTitle"=>"invoice"
				);


				}
				
				  
				

				$iter = 0;
				$imgQuery = $this->Ch1shipsheetModel->get_ch_imgs($Mid);
				if ($imgQuery->num_rows() > 0) {
					
					$imgRs = $imgQuery->result_array();
					foreach ($imgRs as $checkImgRow) {
						$imgPic = $checkImgRow["Picture"];
					    $imgPicTitle = $checkImgRow["Remark"];
					    $hasStatic = "";
					    if ($imgPicTitle=="") {
						    switch($iter) {
							    case 0: $imgPicTitle = "AW";$hasStatic="AW"; break;
							    case 1: $imgPicTitle = "BL";$hasStatic="BL";break;
// 							    default: $imgPicTitle = "";break;
						    }
					    }
					
					    if (file_exists("../download/invoice/$imgPic")) {
						   
					    } else {
						    $hasStatic = "";
					    }
					   
					    $InvoiceFilePath ="$downLoadPath"."invoice/$imgPic";
					   
					    if ($hasStatic == "") {
						    $imgsArr[]= array( 
						     "url"=>"$InvoiceFilePath",
						     "title"=>" $imgPicTitle",
						     "url_thumb"=>"$InvoiceFilePath",
						     "Type"=>"img",
						     "NavTitle"=>"$imgPicTitle"
						     );

					    } else {
						    $imgsArr[]= array(
						     "url"=>"$InvoiceFilePath",
						     "title"=>" $imgPicTitle",
						     "url_thumb"=>"$InvoiceFilePath",
						     "Type"=>"img",
						     "NavTitle"=>"$imgPicTitle",
						     'static'=>"$hasStatic"
						     );

					    } 
						 					   
					    $iter ++;

					}
					
				}
				

								     
				     
				$subList[]=array(
					'tag'=>'invoice',
					'Mid'=>$Mid,
					'PreChar'=>$prechar,
					'Id'=>''.$InvoiceNO,
					'titleTop'=>$titleTopObj,
					'title'=>$InvoiceNO,
					'forshort'=>$Forshort,
					'type'=>$CompanyId,
					'col2'=>array('Text'=>$prechar.number_format($Amount), 'Color'=>$AmountColor),
					'col1'=>number_format($rows['Qty']),
					'imgs'=>$imgsArr
				);

				
			}
	    }
	    return $subList;
		
	}
	
	function subList() {
		
		$params = $this->input->post();
		$menu_id = element('menu_id', $params , '0');
		$upTag = element('upTag', $params , '');
		$Id = element('Id', $params , '');
		$type = element('type', $params , '');
		
		$subList = array();
		
		$arrayOne = array('tag'=>'none');
		
		switch ($menu_id) {
			case 0:
			{
				switch ($upTag) {
					case 'shtotal':{
						$subList = $this->company_month_list($Id, $type);
					}
					break;
				
					case 'z_order': {
						$infos = explode('|', $Id);
						if (count($infos) > 1) {
							$subList = $this->same_product_infos($infos[1], $infos[0], $type);
						}
						
					}
				}
			}
			break;
			
			case 1:
			{
				switch ($upTag) {
					case 'shtotal':{
						$subList = $this->company_month_list($Id, $type);
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
	
	function month_list() {
		
		$this->load->model('Ch1shipsheetModel');
		
		$query = $this->Ch1shipsheetModel->shipped_months();
		$sectionList = array();
		$sortAmount = array();
		$rowNums = $query->num_rows();
		$thisMonth = date('Y-m');
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {
				
				$month = $rows['Month'];
				$puncInfo = $this->Ch1shipsheetModel->get_order_punctuality(1, $month);
				
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

				$prechar = $rows['PreChar'];
				$NoPayAmount = $rows['NoPayAmount'];
				
				$payed = $this->Ch1shipsheetModel->month_payed($month);
				
				$NoPayAmount=$NoPayAmount-$payed;
				
			    $timeMon = strtotime($month);
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



				
				$opened = 0;
				$datasub = array();
				if ($month == $thisMonth) {
					$opened = 1;
					$datasub = $this->month_subcompanys($month);
				}

				$sectionList[]=array(
					'tag'=>'shhead',
					'Id'=>$month,
					'segIndex'=>'-1',
					'open'=>''.$opened,
					'data'=>$datasub,
					'showArrow'=>'1',
					'method'=>'segements',
					'pieValue'=>$chartValInner,
					'percent'=>$chartPercent,
					'title'=>$titleAttr,
					'nopayed'=>$NoPayAmount,
					'col4'=>$prechar.number_format($NoPayAmount),
					'col3'=>$prechar.number_format($rows['Amount']),
					'col2'=>number_format($rows['Qty']),
					'faceImg'=>''
				);
				$sortAmount[]=$rows['Amount'];

			}
			
		}
		
		
		//排序
		arsort($sortAmount,SORT_NUMERIC);
		$j=0;
		while(list($key,$val)= each($sortAmount)) 
		{
		    $j++;
		    $sectionList[$key]['faceImg']='face_1';
		    if ($j==6) break;
		}

		if ($rowNums > 12) {
			asort($sortAmount,SORT_NUMERIC);
			$j=0;
			while(list($key,$val)= each($sortAmount)) 
			{
			    $j++;
			    $sectionList[$key]['faceImg']='face_2';
			    if ($j==6) break;
			}
		}
				
		return $sectionList;
	}
	
	function client_list(){
		
		$this->load->model('Ch1shipsheetModel');
		$this->load->model('TradeObjectModel');
		
		$query = $this->Ch1shipsheetModel->shipped_companys();
		$sectionList = array();
		$LogoPath = $this->TradeObjectModel->get_logo_path();
		
		$iterator = 0;
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {
				
				$CompanyId = $rows['CompanyId'];
				$Logo = $rows['Logo'];
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

				$prechar = $rows['PreChar'];
				$NoPayAmount = $rows['NoPayAmount'];
				
				$payed = $this->Ch1shipsheetModel->month_payed('',$CompanyId);
				
				$NoPayAmount=$NoPayAmount-$payed;
				
				$opened = 0;
				$datasub = array();
				if ($iterator == 0) {
					$opened = 1;
					$datasub = $this->company_submonths($CompanyId);
				}

				
			    

				$sectionList[]=array(
					'tag'=>'shhead',
					'Id'=>$CompanyId,
					'segIndex'=>'-1',
					'showArrow'=>'1',
					'open'=>$opened,
					'data'=>$datasub,
					'method'=>'segements',
					'pieValue'=>$chartValInner,
					'percent'=>$chartPercent,
					'titleIsImg'=>'1',
					'titleImg'=>$LogoPath.$Logo,
					'iconTitle'=>$rows['Forshort'],
					'nopayed'=>$NoPayAmount,
					'col4'=>$prechar.number_format($NoPayAmount),
					'col3'=>$prechar.number_format($rows['Amount']),
					'col2'=>number_format($rows['Qty']),
					'faceImg'=>''
				);

				$iterator++;
			}
			
		}
		
				
		return $sectionList;
	}

	
	public function main() {
		
		$params = $this->input->post();
		$menu_id = element('menu_id', $params , '0');
		$sectionList = array();
		switch ($menu_id) {
			case 0: $sectionList = $this->month_list();
			break;
			case 1: $sectionList = $this->client_list();
			break;
		}
	    $data['jsondata']=array('status'=>'1','message'=>'1','totals'=>1,'rows'=>$sectionList);
	    $this->load->view('output_json',$data);
	}
	
	function invoice_list($Mid,$PreChar) {
		$this->load->model('Ch1shipsheetModel');
		$this->load->model('ProductdataModel');
		$this->load->library('datehandler');
		$query = $this->Ch1shipsheetModel->invoice_list($Mid);
		$subList = array();
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {
				/*SELECT S.POrderId,O.OrderPO,S.Qty,S.Price,S.Type,P.cName,P.eCode,P.TestStandard,M.Sign,N.OrderDate,M.Date AS chDate,PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1) AS Weeks,YEARWEEK(M.Date,1) AS chWeeks    ProductId */
				$POrderId = $rows['POrderId'];
				$productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
				$TestStandardState = $this->ProductdataModel->get_teststandard_state($rows['POrderId']);
				$Price=number_format(round($rows['Price'],2),2);
				$Amount=number_format(round($rows['Qty']*$rows['Price']*$rows['Sign'],2),2);	
				$seconds = strtotime($rows['chDate'])- strtotime($rows['OrderDate']);
				$time = $this->datehandler->GetTimeInterval($seconds);

				$timeColor = (strtotime($rows['Leadtime'])- strtotime($rows['chDate']) )
				> 0 ? $this->color_grayfont:$this->color_red;
				$subList[]=array(
					    'type'       =>$rows['CompanyId'].'',
					    'segIndex'   =>'',
						'tag'        =>'z_order',
						'Id'         =>$rows['Mid'].'|'.$rows['ProductId'],
						'showArrow'  =>'1',
						'open'       =>'0',
						'arrowImg'   =>'UpAccessory_gray',
						'POrderId'   =>$rows['POrderId'],
						'ProductId'  =>$rows['ProductId'],
						'Picture'    =>$rows['TestStandard']==1 && $TestStandardState==1?1:0,
						'standard'   =>$rows['TestStandard'].'',
					    'productImg' =>$productImg,
					    'shipImg'    =>'',
						'week'       =>$rows['Weeks'],
						'title'      =>$rows['cName'],
						'created'    =>array(
							'Text'=>$time,
							'Color'=>$timeColor
						),
						'col1'       =>$rows['OrderPO'],
						'col2'       =>$rows['Qty'],
						'col2Img'    =>'sh_shiped_0.png',
						'col3'       =>$PreChar.$Price,
						'col3Img'    =>'',
						'col4'       =>$PreChar.$Amount,
						'completeImg'=>'',
						'inspectImg' =>''
					);

				
			}
		}
		
		return $subList;
		
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
	
	function same_product_infos($ProductId, $aMid, $CompanyId='') {
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
				
				$nofinishList[]=array(
					'tag'=>'sub_order',
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
					'col2'       =>number_format($rows['Qty']),
					'col2Img'    =>'scdj_11',
					'titleImg'=>'sh_ordered',
					'col3'       =>
					    array('Text'=>$scQty>0?''.number_format($scQty):'', 'Color'=>'#01be56'),
					'col3X'  =>'20',
					'col2X'  =>'10',
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
				
				
				$cpList[]=array(
					'tag'=>'sub_order',
					'Id'         =>$rows['POrderId'].'|'.$rows['ProductId'],
					'POrderId'   =>$rows['POrderId'],
				    'productImg' =>'',
				    'shipImg'    =>'',
				    'dateTitle'=>$titleAttr,
				    'noimg'=>'1',
					'week'       =>$rows['Weeks'],
					'title'      =>$rows['cName'],
					'col1'       =>$rows['OrderPO'],
					'col2'       =>number_format($rows['Qty']),
					'col2Img'    =>'scdj_11',
					'titleImg'=>'sh_in.png',
					'col2X'  =>'10',
					'week_s'=>array('weekDate'=>$weekDate),
					'col3'       =>
					    array('Text'=>''.number_format($rows['tStockQty']), 'Color'=>'#01be56'),
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
					'col2'       =>number_format($rows['chQty']),
					'col2Img'    =>'sh_shiped_0',
					'titleImg'=>'sh_in.png',
					'titleImg2'=>'sh_out.png',
					'col3'       =>$prechar.round( $rows['Price'],2),
					'col3X'  =>'20',
					'col2X'  =>'10',
					'col4X'  =>'-8',
					'titleImgY'=>'-10',
					'titleX'=>'-24',
					'col3Img'    =>'',
					'col4'       =>$prechar. number_format($rows['chQty']*$rows['Price']),
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
	
	public function same_infos() {
		$params = $this->input->post();
		$productid = element('productid', $params , '');
		$subList = array();
		if ($productid != '') {
			$subList = $this->same_product_infos($productid, '', '');
		}
$data['jsondata']=array('status'=>'1','message'=>'1','totals'=>1,'rows'=>$subList);
	    $this->load->view('output_json',$data);
	}
	
	public function invoice() {
		
		$params = $this->input->post();
		$invoiceNo = element('invoiceNo', $params , '');
		$subList = $this->company_month_list('', '', $invoiceNo);
		
		$sectionList = array();
		$sectionList[]=array('data'=>$subList);
		$Mid = '';
		$PreChar = '';
		if (count($subList)>0) {
			$Mid = $subList[0]['Mid'];
			$PreChar = $subList[0]['PreChar'];
			
			$subList = $this->invoice_list($Mid,$PreChar);
			$sectionList[]=array('data'=>$subList);
		}
		
		$data['jsondata']=array('status'=>'1','message'=>'1','totals'=>1,'rows'=>$sectionList);
	    $this->load->view('output_json',$data);
		
		
	}
	
}