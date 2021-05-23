 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quality_new extends MC_Controller {
/*
	功能:质检功能页面
*/
    public $SetTypeId= null;
    public $MenuAction= null;
    public $PrintAction= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 3;//单选设置
        $this->MenuAction   = $this->pageaction->get_actions('arrive');//到达
        $this->PrintAction  = $this->pageaction->get_actions('print');  //标签打印
    }
    
    
    function qc_frametype() {
	    $this->load->model('ckBasketTypeModel'); 
	    $params = $this->input->post();
	    $StuffId = element('stuffid',$params,-1);
	    
	    $this->load->model('StuffDataModel');
	    
	    
	    $stuffRow = $this->StuffDataModel->get_records($StuffId);
	    
	    $basketType = $stuffRow['basketType'];
	    // .Unit,S.TypeId,
	    $SendFloor = $stuffRow['SendFloor'];
	    $stuffUnit = $stuffRow['Unit'];
	    $stuffType = $stuffRow['TypeId'];
	   // $basketType = 2;
	    
	    $needFrame = 1;
	    
	    $noFrameUnits = array(1, 14, 17, 19, 20);
	    
	    if (in_array($stuffUnit, $noFrameUnits) || $stuffType == 9040 || $SendFloor==12) {
		    $needFrame = 0;
		    $basketType = -1;
	    } 
	    
	    if ($this->LoginNumber == 11965) {
		    $needFrame = 0;
		    $basketType = -1;
	    }
	    
	    $rs = $this->ckBasketTypeModel->getBasketType();
	    $dataArray = array();
	    foreach ($rs as $rows) {
		    
		    
		    $dataA=array(
				'Id'=>$rows['id'],
				'img'=>'frame_'.$rows['id']
		    );
		    
		    if ($rows['id'] == $basketType) {
			    $dataA['select'] = 1;
		    }
		    $dataArray[]=$dataA;
	    }
	    
	   
// 	    $needFrame = 0;
	    $data['jsondata']=array('status'=>1,'message'=>"$needFrame",'totals'=>1,'rows'=>$dataArray);
	    $this->load->view('output_json',$data);
	    
	    
    }
    
    function qc_headinfo() {
	    $params = $this->input->post();
	    $gys_id = element('qc_id',$params,-1);
	    
	    
	    
	    $status = null;
	    
	    if ($gys_id  > 0) {
		    $this->load->model('GysshsheetModel');
		    $this->load->model('QcCjtjModel');
		    $this->load->model('StuffDataModel');
		    
		    
		    $rows = $this->GysshsheetModel->get_records($gys_id);
		    
		    $Qty = $rows['Qty'];
		    
		    
		    
		    $AQL = $rows['AQL'];
		    $status = array('AQL'=>'AQL:'.$AQL);
		    
		    $CheckSign = $rows['CheckSign'];
		    $StuffId = $rows['StuffId'];
		    
		   // $img= $this->StuffDataModel->get_stuff_picture($StuffId);
		   // $status['stuffImg'] = $img;
		    $status['col2'] = number_format($Qty);
		    $scedQty = $this->QcCjtjModel->get_qcqty($gys_id);
		    if ($CheckSign==0) {
			    $aqlInfo = $this->GysshsheetModel->get_aql_infos($AQL, $scedQty);
			    //.'->'.$aqlInfo['reqty']
			    $status['col3'] = number_format($aqlInfo['check']);
			    $status['reqty'] = intval( $aqlInfo['reqty']);
			    $status['needcheck'] = 1;
			    
		    } else {
			    $status['AQL'] = '';
			    
			    $status['col3'] = number_format($scedQty);
			    
		    }
		    
	    }
	    
	    
	    
	    $data['jsondata']=array('status'=>$status,'message'=>'','totals'=>1,'rows'=>array());
		    $this->load->view('output_json',$data);
    }
    
    //test
        public function djRecord() {
	        
	        $params = $this->input->post();
			$this->load->model('QcCjtjModel');
			$this->load->model('LabelPrintModel'); 
			
			$Sid = element('Id',$params,-1);
			$query = $this->QcCjtjModel->djrecords($Sid);
			$listArr = array();
			$records = array();
			$scedQty  =$this->QcCjtjModel->get_qcqty($Sid);
			
			$nums = $query->num_rows();
			if ($nums > 0) {
				$i = 0;
				  //标签打印设置 
		   
		  	
				foreach ($query->result_array() as $myRow) {
					
				    $prints=array();
			        $djQty = intval($myRow['Qty']);
			        $newId = $myRow['Id'];
			        $actions=array();
				    $prints[]=$this->LabelPrintModel->get_qcrecord_print($Sid,$newId,$djQty,0,$scedQty);
				    $actions=$this->PrintAction;
				    $actions[0]['data']= $prints; 
				    $scedQty-=$djQty; 
					$records[]=array(
						'actions'=>$actions,
						'tag'    =>"djRecord",
						'col1'   =>"$djQty",
						'col2'   =>array('Text'=>''.$myRow['Date'],'DateType'=>'time'),
						'col3'   =>''.$myRow['Name'],
						'index'  =>"".($nums-$i)
						
					);
					$i++;
				}
 			$aSection = array("data"=>$records);
			$listArr[]=$aSection;
			}
			
	
		    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$listArr);
		    $this->load->view('output_json',$data);
		}
	
	public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	function menus() {
		
		
		   //获取参数
	     $params = $this->input->post();
	     $qtycolor   =$this->colors->get_color('qty');
	     $redcolor   =$this->colors->get_color('red');
	     $lightgray  =$this->colors->get_color('lightgray');
	     
	     //加载模块
	     $this->load->model('BaseMPositionModel'); 
	     $this->load->model('GysshsheetModel');
	     //调用过程
	     $rows=$this->BaseMPositionModel->get_warehouse(1,1,$this->SetTypeId);
	     $counts=count($rows);
	     /*if ($counts > 3) {
		     array_splice($rows,3,$counts-3);
	     }
	     $counts = $counts > 3 ? 3 :$counts;*/
	     
	     $index = 0;
	     for ($i=0;$i<$counts;$i++){
	     
	        if ($rows[$i]['selected'] == 1) {
		        $index = $i;
		        
	        }
	        $title = $rows[$i]['title'];
	        
	        $title = str_replace('(', '', $title);
	        $title = str_replace(')', '', $title);
	        $rows[$i]['title'] = $title;
		    $rows[$i]['cellType']='1';
		     
	     }

	     

		 $data['jsondata']=array('status'=>'','message'=>''.$index,'rows'=>$rows);
		 //输出JSON格式数据
		 $this->load->view('output_json',$data);
		
	}
	
	public function menu(){
	    //获取参数
	     $params = $this->input->post();
	     $qtycolor   =$this->colors->get_color('qty');
	     $redcolor   =$this->colors->get_color('red');
	     $lightgray  =$this->colors->get_color('lightgray');
	     
	     //加载模块
	     $this->load->model('BaseMPositionModel'); 
	     $this->load->model('GysshsheetModel');
	     //调用过程
	     $rows=$this->BaseMPositionModel->get_warehouse(1,1,$this->SetTypeId);
	     $counts=count($rows);
	     /*if ($counts > 3) {
		     array_splice($rows,3,$counts-3);
	     }
	     $counts = $counts > 3 ? 3 :$counts;*/
	     for ($i=0;$i<$counts;$i++){
	     
	         $kdcounts = $this->GysshsheetModel->get_order_counts($rows[$i]['Id'],1);
	         $qccounts = $this->GysshsheetModel->get_order_counts($rows[$i]['Id'],2);
	         $qcovercounts = $this->GysshsheetModel->get_overed_order_counts($rows[$i]['Id'],2);
	         
	         
	         $lastColor = $qcovercounts >0? $redcolor :$qtycolor;
	         if ($kdcounts>0 || $qccounts>0){
		         $rows[$i]['subTitle']=array(
	                                             'isAttribute'=>'1',
								                 'attrDicts'  =>array(
	                                                   array('Text'=>$kdcounts . '/','Color'=>$lightgray,'FontSize'=>'8'),
									                   array('Text'=>$qccounts,'Color'=>$lastColor,'FontSize'=>'8')
									               )
	                                             );
	         }
		     
	     }
	     $status=count($rows)>0?1:0;
	     

		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$rows);
		 //输出JSON格式数据
		 $this->load->view('output_json',$data);
		 
	}
	
	function up_top() {
		
		
		 $params = $this->input->post();
        $isShadow = element('isShadow',$params,'0');
        $Floor = element('menu_id',$params,'');
        
        $this->load->model('AppUserSetModel');
        $this->load->model('BaseMPositionModel');
        $selected=0;
		if ($Floor=='' || $Floor=='all'){
		    $Floor=$this->AppUserSetModel->get_parameters($this->LoginNumber,$this->SetTypeId);
	    }
	    else{
		     $this->AppUserSetModel->set_parameters($this->LoginNumber,$this->SetTypeId,$Floor); 
	    }

	    if ($Floor==''){
		    $typesArray=$this->BaseMPositionModel->get_warehouse(0,1,$this->SetTypeId); 
		    $this->AppUserSetModel->set_parameters($this->LoginNumber,$this->SetTypeId,$Floor);
		    
		    $numsOfTypes=count($typesArray);
		    if ($numsOfTypes>0){
               $oneTypes=$typesArray[0];
	           $Floor=$oneTypes['Id'];
            }
 
	    }
	    else{
	        $selected=1;
		    $typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$Floor); 
	    }        

        
	   
	    $dataArray=array();

        $this->load->model('GysshsheetModel');
        $rowArray=$this->GysshsheetModel->get_supplier_list($Floor,1);
        $totals=count($rowArray); 
        $allQty = 0;
        $count = 0;
        
        $this->load->library('datehandler');
        $nowtimes = strtotime('now');
        
        $oneDayMinSec = 24 * 3600;
        $halfDaySec = 12 * 3600;
        
        $versionNum = $this->versionToNumber($this->AppVersion);
		$is428Version = $versionNum >= 428 ? true : false;
		
		
        for ($i = 0; $i < $totals; $i++) {
           $rows=$rowArray[$i];
           
           $carImg=$this->get_car_image(round($rows['Amount']));
           $minus = $nowtimes - strtotime($rows['time']);
           $time = $this->datehandler->GetTimeInterval($minus,1);
			$timeColor = $minus >$oneDayMinSec ?'#ff0000':'#ff921d';		
           $badge=number_format($rows['Qty']);
           $allQty += $rows['Qty'];
           $count += $rows['Cts'];
           
           $timeImg = '';
           
           if ($minus > $oneDayMinSec) {
	           $timeImg = 'clock_red';
	           $timeColor = '#ff0000';
	           
	           if ($is428Version) {
		           $carImg = $carImg.'_red';
	           }
           } else if ($minus > $halfDaySec) {
	           $timeImg = 'clock_orange';
	           $timeColor = '#ff921d';
	           if ($is428Version) {
		           $carImg = $carImg.'_red';
	           }
           }
           

           $dataArray[]=array(
                       'parentId'=> out_format($Floor),
//                        'timeImg'=>$timeImg,
                       'iconBling'=>array('beling'=>$timeImg==''?'':'1',
									'blingVals' =>array(1,0.65,0.16,0.65,1),
									),
                       'timeBling'=>array('beling'=>'1',
									'blingVals' =>array(1,0.65,0.16,0.65,1),
									),
//                        'time_s'=>$timeImg==''?null:array('Text'=>$time,'Color'=>$timeColor),
		                     'Id' => out_format($rows['CompanyId']),
						  'qty' => "$badge",
						  'forshort' => out_format($rows['Forshort']),
						'halfImg' => $rows['CompanyId']<1000 ? 'halfClear' :'',
						 'icon' => $carImg
		    );
		    if ($this->LoginNumber == 11965) {

		    }
		    
        } 
	    $dataArray[]=array(
                       'parentId'=> out_format($Floor),
		                     'Id' => 'add',
						 'icon' => 'qc_add_order'
		    );
	    
	    
	    $Estate=2;//待检
	    $this->load->model('BaseMPositionModel'); 
	    $this->load->model('StuffdataModel');
	    $this->load->model('CgstocksheetModel'); 
	    
        $records=$this->BaseMPositionModel->get_records($Floor);
	    $checkSign=$records['CheckSign']; 
	            
		       
	    $sheets=$this->GysshsheetModel->get_floor_order($Floor,$Estate,$checkSign);
	    $dataArray2 = array();
	    
	    $allQty2 = 0;
        $count2 = 0;
        
        $red   = $this->colors->get_color('red');
       $black = $this->colors->get_color('black');
       $lightgray = $this->colors->get_color('lightgray');
       $qtycolor  = $this->colors->get_color('qty');
       $lightblue = $this->colors->get_color('lightblue');
       $yellowgreen = $this->colors->get_color('yellowgreen');
       $yellowgreen = '#b2fcc6';
       
       $curweeks = $this->ThisWeek;
        
	    foreach ($sheets as $rows) {
		    
		    $Decimals=isset($rows['Decimals'])?$rows['Decimals']:0;
		    
		    $week = element('DeliveryWeek', $rows, 0);
		    
		    $qtycolors = '#727171'; 
		    if ($week >0 && $week < $curweeks) {
			    $qtycolors = $red;
		    }
		    
		    
		    $stuffImg=$rows['Picture']>0?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
			$count2 ++;
			$allQty2 += $rows['Qty'];
			$minus = $nowtimes - strtotime($rows['shDate']);
			
			
			$lastbgColor = '';
			$lastBlSign  = $this->CgstocksheetModel->get_check_lastblsign($rows['POrderId'],$rows['StuffId']);
	           if ($lastBlSign>0){
	               $lastbgColor = $lastBlSign>1?$lightblue:$yellowgreen;
	           }
			
			$timeColor = $minus >$oneDayMinSec ?'#ff0000':'#727171';
           $time = $this->datehandler->GetTimeInterval($minus,1);
           $timeImg = '';
           if ($minus > $oneDayMinSec) {
	           $timeImg = 'clock_red';
	           $timeColor = '#ff0000';
           } else if ($minus > $halfDaySec) {
	           $timeImg = 'clock_orange';
	           $timeColor = '#ff921d';
           }
		    $dataArray2[]=array(
                       'parentId'=> out_format($Floor),
					   'timeBling'=>array('beling'=>'1',
									'blingVals' =>array(1,0.65,0.16,0.65,1),

									),
                       'timeImg'=>$timeImg,
                       'time_s'=>$timeImg==''?null:array('Text'=>$time,'Color'=>$timeColor),
		                     'Id' => out_format($rows['Id']),
						  'qty' =>array('Text'=> number_format($rows['Qty'],$Decimals),'BgColor'=>$lastbgColor, 'Color'=>$qtycolors),
						 'icon'=>$stuffImg,
						 
						 'Picture'=>$rows['Picture']==1?'1':''
		    );
		    
	    }
	           	           
	    
	    $statusDict = array(
		    'title1'=>'开单',
		    'count1'=>$count<=0?'--':number_format($count),
		    'qty1'=>$count<=0?'--':number_format($allQty),
		    'source1'=>$dataArray,
		    'hide2'=>$count2>0?'':1,
		    'title2'=>'待检',
		    'count2'=>$count2<=0?'--':number_format($count2),
		    'qty2'=>$count2<=0?'--':number_format($allQty2),
		    'source2'=>$dataArray2
		    
	    );
	    
	    
	    $data['jsondata']=array('status'=>$statusDict,'message'=>"$Floor",'totals'=>$totals,'rows'=>'');
	    
	    $this->load->view('output_json',$data);  
	}
	
	public function top_top(){
	    $params = $this->input->post();
        $isShadow = element('isShadow',$params,'0');
        $Floor = element('types',$params,'');
        
        $this->load->model('AppUserSetModel');
        $this->load->model('BaseMPositionModel');
        $selected=0;
		if ($Floor=='' || $Floor=='all'){
		    $Floor=$this->AppUserSetModel->get_parameters($this->LoginNumber,$this->SetTypeId);
	    }
	    else{
		     $this->AppUserSetModel->set_parameters($this->LoginNumber,$this->SetTypeId,$Floor); 
	    }

	    if ($Floor==''){
		    $typesArray=$this->BaseMPositionModel->get_warehouse(0,1,$this->SetTypeId); 
		    $this->AppUserSetModel->set_parameters($this->LoginNumber,$this->SetTypeId,$Floor);
		    
		    $numsOfTypes=count($typesArray);
		    if ($numsOfTypes>0){
               $oneTypes=$typesArray[0];
	           $Floor=$oneTypes['Id'];
            }
 
	    }
	    else{
	        $selected=1;
		    $typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$Floor); 
	    }        

        /*
	    if ($Floor=='all' || $Floor==''){
		    $typesArray=$this->BaseMPositionModel->get_warehouse(0,1,$this->SetTypeId); 
		    $numsOfTypes=count($typesArray);
		    
		    if ($numsOfTypes>0){
               $oneTypes=$typesArray[0];
	           $Floor=$oneTypes['Id'];
            }
	    }
	    else{
		    $typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$Floor); 
	    }
	    */
	   
	    $dataArray=array();

        $this->load->model('GysshsheetModel');
        $rowArray=$this->GysshsheetModel->get_supplier_list($Floor,1);
        $totals=count($rowArray); 
        
        for ($i = 0; $i < $totals; $i++) {
           $rows=$rowArray[$i];
           
           $carImg=$this->get_car_image(round($rows['Amount']));
           $badge=($rows['Qty']>=1000)?round($rows['Qty']/1000) . 'k':round($rows['Qty']);
           
           $dataArray[]=array(
                       'parentId'=> out_format($Floor),
		                     'Id' => out_format($rows['CompanyId']),
						  'badge' => "$badge",
						  'title' => out_format($rows['Forshort']),
						'halfImg' => $rows['CompanyId']<1000 ? 'halfClear' :'',
						 'carImg' => $carImg
		    );
        } 
	    
	    
	    $data['jsondata']=array('status'=>'1','message'=>"$Floor",'totals'=>$totals,'rows'=>$dataArray);
	    
	    $this->load->view('output_json',$data);   
      
	}
	
	function dj_main() {
		$params = $this->input->post();
		$types   = element('types',$params,'');//送货楼层
		$top_seg = element('top_seg',$params,'');
		
		$destineId = element('destineId',$params,'');

		
		//$this->load->model('AppUserSetModel');
		$this->load->model('BaseMPositionModel'); 
		
	    $selected=1;
		$typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$types);       

	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	    $navtitle='选择楼层';
	    $dataArray=array();

	    
	    $i=0; 
	    $Titles=$this->get_titles();
	    
        $numsOfTypes=count($typesArray); 
        
        $addedLineid = '';
    
        $indexScrollString = '';
        if ($numsOfTypes>0){
            for ($j = 0; $j < $numsOfTypes; $j++){
	                 $oneTypes=$typesArray[$j];
	                if ($oneTypes['Id']==$types){
		                break;
	                }
	        } 
	        $Floor=$oneTypes['Id'];
	        $navtitle=$oneTypes['title'];
	        
	        $records=$this->BaseMPositionModel->get_records($Floor);
	        $checkSign=$records['CheckSign'];
	        

	        $this->load->model('GysshsheetModel');
		        
	       
			$this->load->model('QcsclineModel');
        $scLines=$this->QcsclineModel->get_sclineNo($Floor);
		        
       $addedLineid = '';
	        if (count($scLines)==1){
		        $addedLineid=key($scLines);
	        }

	        
	      //  $rows=$this->GysshsheetModel->get_checking_counts($Floor,1);
	        $rowsArray=$this->get_segment_list($Floor,1);
	        $openSign=count($rowsArray)>0?1:0;
/*
	        if ($destineId != '') {
		        $iter = 0;
		        foreach ($rowsArray as $rows) {
			        if ($indexScrollString == '' && $rows['tag']=='qcStuff' && $rows['Id']==$destineId) {
				        $indexScrollString = '0,'.$iter;
				        break;
			        }
			        $iter ++;
		        }
	        }
*/
			$dataArray[]=array(
	                            'tag'         =>'none',
	                            'allotset'    =>"$addedLineid",
			                    'data'=>$rowsArray
					);
			$i++;
			
						
        }
	    
	    
	    $data['jsondata']=array('status'=>array('to'=>$indexScrollString,'allotset'=>$addedLineid.''),'message'=>''.$Floor,'totals'=>$i,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
	}
	function drk_main() {
		$params = $this->input->post();
		$types   = element('types',$params,'');//送货楼层
		$top_seg = element('top_seg',$params,'');
		
		$destineId = element('destineId',$params,'');

		
		//$this->load->model('AppUserSetModel');
		$this->load->model('BaseMPositionModel'); 
		
	    $selected=1;
		$typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$types);       

	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	    $navtitle='选择楼层';
	    $dataArray=array();

	    
	    $i=0; 
	    $Titles=$this->get_titles();
	    
        $numsOfTypes=count($typesArray); 
        
        $this->load->model('QcsclineModel');
        
        $indexScrollString = '';
        if ($numsOfTypes>0){
            for ($j = 0; $j < $numsOfTypes; $j++){
	                 $oneTypes=$typesArray[$j];
	                if ($oneTypes['Id']==$types){
		                break;
	                }
	        } 
	        $Floor=$oneTypes['Id'];
	        $navtitle=$oneTypes['title'];
	        
	        $records=$this->BaseMPositionModel->get_records($Floor);
	        $checkSign=$records['CheckSign'];
	        

	        $this->load->model('GysshsheetModel');
		        
	       
			
			//品检中
	        
	      //  $rows=$this->GysshsheetModel->get_checking_counts($Floor,1);
	        $rowsArray=$this->get_segment_list($Floor,4);

/*
	        if ($destineId != '') {
		        $iter = 0;
		        foreach ($rowsArray as $rows) {
			        if ($indexScrollString == '' && $rows['tag']=='qcStuff' && $rows['Id']==$destineId) {
				        $indexScrollString = '0,'.$iter;
				        break;
			        }
			        $iter ++;
		        }
	        }
*/
			$dataArray[]=array(
	                            'tag'         =>'none',
			                    'data'=>$rowsArray
					);
			$i++;
			
						
        }
	    
	    
	    $data['jsondata']=array('status'=>array('to'=>$indexScrollString),'message'=>''.$Floor,'totals'=>$i,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
	}
	
	function flip_vals() {
		$params = $this->input->post();
		$Floor   = element('menu_id',$params,'');//送货楼层
		$this->load->model('GysshsheetModel'); 
		$this->load->model('QcBadrecordModel'); 
		
		// drk
		$rows=$this->GysshsheetModel->get_qcrk_counts($Floor);
		
		$drk = array('val'=>$rows['Qty']<=0?'--':number_format($rows['Qty']));
		
		
		 {
			  $beling=$this->GysshsheetModel->get_drk_blings($Floor);
			 
			$drk['beling']=$beling.'';
			$blingVals = array(1,0.65,0.3,0.65,1);
			$drk['blingVals']=$blingVals;
		}
		
		
		
		
		$rows=$this->QcBadrecordModel->get_unth_counts($Floor,1);
		$tl = array('val'=>$rows['Qty']<=0?'--':number_format($rows['Qty']));
		$beling=$rows['Qty']>0?'1':'';
			 
		$tl['beling']='';
		$data['jsondata']=array('status'=>array('drk'=>$drk, 'tl'=>$tl),'message'=>'','totals'=>'','rows'=>null);
	    
	   $this->load->view('output_json',$data); 
		
		
	}
	
	public function main()
	{
		$params = $this->input->post();
		$types   = element('menu_id',$params,'');//送货楼层
		$top_seg = element('top_seg',$params,'');
		
		//$this->load->model('AppUserSetModel');
		$this->load->model('BaseMPositionModel'); 
		
	    $selected=1;
		$typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$types);       

	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	    $navtitle='选择楼层';
	    $dataArray=array();
	    $dataArray[]=array('hidden'=>'');
	    
	    $i=0; 
	    $Titles=$this->get_titles();
	    
        $numsOfTypes=count($typesArray); 
        
        $this->load->model('QcsclineModel');
        if ($numsOfTypes>0){
            for ($j = 0; $j < $numsOfTypes; $j++){
	                 $oneTypes=$typesArray[$j];
	                if ($oneTypes['Id']==$types){
		                break;
	                }
	        } 
	        $Floor=$oneTypes['Id'];
	        $navtitle=$oneTypes['title'];
	        
	        $records=$this->BaseMPositionModel->get_records($Floor);
	        $checkSign=$records['CheckSign'];
	        

	        $this->load->model('GysshsheetModel');
		        
	       
			
			//品检中
	        
	       // $rows=$this->GysshsheetModel->get_checking_counts($Floor,1);
	        $rowsArray=$this->get_segment_list($Floor,2);
	        $openSign=count($rowsArray)>0?1:0;
			$dataArray[]=array(
	                            'tag'         =>'none',
			                    'data'=>$rowsArray
					);
			$i++;
			
						
        }
	    
	    
	    $data['jsondata']=array('status'=>'1','message'=>''.$Floor,'totals'=>$i,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data);   
	}
	
	public function segment()
	{
	   $params = $this->input->post();
	   $Floor         = element('type',$params,'');//送货楼层
	   $segmentIndex  = element('segmentIndex',$params,'');//选择分类
	   
	   $dataArray=$this->get_segment_list($Floor,$segmentIndex); 
	   
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>'1','rows'=>$dataArray);
	   $this->load->view('output_json',$data);
	}
	
	function get_segment_list($Floor,$segmentIndex)
	{
	   $this->load->model('GysshsheetModel');
	   $this->load->model('StuffdataModel');
	   $this->load->model('QcsclineModel');
	   $this->load->model('QcCjtjModel');
	   $this->load->model('GysshRemarkModel');
	   $this->load->model('CgstocksheetModel');
	   $this->load->model('StuffPropertyModel');
	   
	   $red   = $this->colors->get_color('red');
       $black = $this->colors->get_color('black');
       $lightgray = $this->colors->get_color('lightgray');
       $qtycolor  = $this->colors->get_color('qty');
       $lightblue = $this->colors->get_color('lightblue');
       $yellowgreen = $this->colors->get_color('yellowgreen');
       $yellowgreen = '#b2fcc6';
       $factoryCheck=$this->config->item('factory_check');
       $nowtimes = strtotime('now');
	   $sheets=array();
	   $actions=array();
	   
	   $this->load->model('ckrksheetModel'); 
       switch($segmentIndex){
	       case 1:
	           $Estate=2;//待检
	           $this->load->model('BaseMPositionModel'); 
		       $records=$this->BaseMPositionModel->get_records($Floor);
	           $checkSign=$records['CheckSign']; 
	           
	           $actions=$this->pageaction->get_actions('operate');
	           
	           $listArray = $checkSign==0?$this->pageaction->get_actions('settasks,returnstock,remark')
	                                     :$this->pageaction->get_actions('allot,returnstock,remark');
	         
		       $addlists = $this->pageaction->get_actions('change');
		        if (count($addlists)>0){
		             $addlists[0]['Name']='更改品检方式';
	                 array_push($listArray,$addlists[0]);
	            }
		       $actions[0]['list']=$listArray;  
		       
	           $sheets=$this->GysshsheetModel->get_floor_order($Floor,$Estate,$checkSign);
	          break;
	          
	       case 2: //品检中
	           $actions=$this->pageaction->get_actions('operate');
	           
	           $this->load->model('BaseMPositionModel');
	           $records=$this->BaseMPositionModel->get_records($Floor);
	           $checkSign=$records['CheckSign'];
	           $sheets=$this->GysshsheetModel->get_checking_order($Floor,1);           
	          break;
	       case 3: //品检报告
	           $sheets=$this->GysshsheetModel->get_checked_order($Floor); 
	           $actions=$this->pageaction->get_actions('qcreport');
	          break;
	       case 4: //待入库
	       
	       		
	           $sheets=$this->GysshsheetModel->get_qcrk_order($Floor); 
	           $actions=$this->pageaction->get_actions('stockin');
	           if ($Floor==6 || $Floor==12){
		           $actions[0]['Multi']='1';
	           }
	           if ($Floor==3 || $Floor==6 || $Floor==17 || $Floor==12){
		           $actions[0]['Location']='1';
	           }
	           
	          break;
       }
       	
       $dataArray=array();
       $scLines=$this->QcsclineModel->get_sclineNo($Floor);
       $scLinecount=count($scLines);
       
       $tvipArray=$this->QcsclineModel->get_refreshTV($Floor);
 
	   foreach ($sheets as $rows){
		   $stuffImg=$rows['Picture']>0?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
		   $stuffImg= $rows['Picture']<=0 && $segmentIndex==1?'':$stuffImg;
		   if (isset($rows['LineId']) && $scLinecount>0){
		       $_line=$rows['LineId'];
			   $rtIcon=isset($scLines[$_line])?'qc_' . $scLines[$_line]:'';
		   }
		   else{
			   $rtIcon=$this->get_checksign_image($rows['CheckSign']);
		   }
		   
		   $frameCapacity='';
		   
		   $remarkArray=array();
		   
		   if ($segmentIndex==1 || $segmentIndex==2){
			   $remarks=$this->GysshRemarkModel->get_records($rows['Id']);
			   if (count($remarks)>0){
			     $times =  $this->GetDateTimeOutString($remarks['created'],$this->DateTime);
/*
				 $remarkArray=array(
							'tag'      =>'remarkNew',
							'headline' =>'备注: ',
							'Record'   =>$remarks['Remark'],
							'Recorder' =>$times . ' ｜ '. $remarks['Operator'],
							'bgcolor'  =>'#FFFFFF',
							'left_sper'=>'15',
							'RID'      =>$remarks['Id'] 
			         );
*/
			         
			    $remarkArray = array(
					'tag'=>'remark2',
					'margin_left'=>'54',
					'separ_left'=>'15',
					'content'=>$remarks['Remark'],
					'oper'=>$times . ' ｜ '. $remarks['Operator']
					
				);
			   }
		   }
		   
		   $lastbgColor='';
		   if ($segmentIndex==1 || $segmentIndex==2){
			   //检查是否为最后一个需备料配件
	           $lastBlSign  = $this->CgstocksheetModel->get_check_lastblsign($rows['POrderId'],$rows['StuffId']);
	           if ($lastBlSign>0){
	               $lastbgColor = $lastBlSign>1?$lightblue:$yellowgreen;
	           }
		   }
		   
		   if($segmentIndex==2){
			   
			   
	          
			   
	            $frameCapacity=$this->StuffdataModel->get_framecapacity($rows['StuffId']);
		        $djQty=$this->QcCjtjModel->get_qcqty($rows['Id']);
		        $listArray = $djQty>0? $this->pageaction->get_actions('register,returnstock,qcreport,remark') : $this->pageaction->get_actions('register,returnstock,qcreport,remark');
	           
		        $listArray[0]['MaxQty']=$rows['Qty']-$djQty;
		        $listArray[0]['DjQty'] =$djQty;
		        
		         $actions[0]['list']=$listArray;
		   }
		   $Decimals=isset($rows['Decimals'])?$rows['Decimals']:0;
		   
		   $auditImg = '';

		   if ($segmentIndex == 2) {
/*
			   if ($this->LoginNumber == 11965) {
				   $rows['Picture'] = 2;
			   }
*/
			   if ($rows['Picture']!=1 && $rows['Picture']>0) {
				   $auditImg = 'wait_audit';
			   }
 			   
		   }
		   $Property = $this->StuffPropertyModel->get_property($rows['StuffId']);
		   
		   
		   
		   $rowArray= array(
					'tag'     =>'qcStuff',
					'hideLine'=>count($remarkArray)>0?'1':'0',
					'actions' =>$actions,
				    'refreshTV'=>'0',
				    'tv_IP'=>array(),
					'Id'      =>$rows['Id'],
					'StockId' =>strlen($rows['StockId']<15)?$rows['StuffId']:$rows['StockId'],
					'StuffId' =>$rows['StuffId'],
					'week'    =>$rows['DeliveryWeek'],
					'rtIcon'  =>$rtIcon,
					'col1Img' =>'ws_'.element('WorkShopId',$rows,0).'_1.png',
					'title'   =>$rows['StuffId'] . '-' . $rows['StuffCname'],
					'col1'    =>out_format($rows['Forshort']),
				    'col2'    =>array('Text'=>number_format($rows['OrderQty'],$Decimals),'BgColor'=>$lastbgColor),
					'col3'    =>array('Text'=>number_format($rows['Qty'],$Decimals),'Color'=>$qtycolor),
					'Picture' =>$rows['Picture']==1?'1':'',
					'auditImg'=>$auditImg,
					'auditBeling'=>array('beling'=>'1',
							'blingVals' =>array(1,0.55,0.15,0.55,1),
							'belingtime'=>'3'),
					'selected'=>'',
			        'stuffImg'=>$stuffImg,
			   'FrameCapacity'=>$frameCapacity,
			        'process' =>array(),
			        'Property'=>$Property
				);
   
		   switch($segmentIndex){
		       case 1:
		          $timediff = strtotime($this->DateTime . '')- strtotime($rows['shDate']); 
		          $hours =  intval($timediff/3600);
		          $color =  $hours>=24?$red:$black;
		          $rowArray['time']=$factoryCheck==1?'':array('Text'=>$rows['shDate'],'DateType'=>'time','Color'=>$color);
		          
		          
		          unset($rows['refreshTV']);
		          unset($rows['tv_IP']);
		          break;
		          
		       case 2:
	              $rowArray['col4']=number_format($djQty,$Decimals);
	              if (isset($rows['LineId'])){
	                  $_line=$rows['LineId'];
		              $rowArray['lineId']=$_line;
		              
		              if (isset($tvipArray[$_line])){
		                  $tvip=$tvipArray[$_line];
		                  
		                  for ($n=0,$ipcounts=count($tvip);$n<$ipcounts;$n++)
		                  {
		                     if ($tvip[$n]['ImageSign']==1){
			                     $tvip[$n]['data']=$rows['StuffId']; 
		                     }else{
			                     $tvip[$n]['data']='';
		                     }
		                     unset($tvip[$n]['ImageSign']);
			              }
			              $rowArray['tv_IP']= $tvip;
			              $rowArray['detail_TV']= '1';
		              }
	              }

	              $lasttime= $this->QcCjtjModel->get_lastregister_time($rows['Id']);
	              if ($lasttime!=''){
		              
		              if ($rows['Qty'] > $djQty && ( $nowtimes- strtotime($lasttime))<1800) {
			              $rowArray['col4']=array(
				              'Text'=>number_format($djQty,$Decimals),
				              'beling'=>'1',
// 				              'blingVals' =>array(1,0.65,0.1,0.65,1,1),
			              );
			              
		              }
		              $rowArray['time']=$factoryCheck==1?'':array('Text'=>$lasttime,'DateType'=>'time','Color'=>$black);
	              }
	              
	              
		          break;
		       case 3:
		          $rowArray['col4']=number_format($rows['scQty'],$Decimals);
		          unset($rows['refreshTV']);
		          unset($rows['tv_IP']);
		          break;
		      case 4:
		      		 $frameCapacity=$this->StuffdataModel->get_framecapacity($rows['StuffId']);
		      		 $basketType=$this->StuffdataModel->get_basketType($rows['StuffId']);
		      		 if ($frameCapacity > 0 && $basketType>0) {
			      		 $rowArray['frameNum'] = ceil($rows['scQty']/$frameCapacity);
			      		 $rowArray['frameImg'] = 'frame_'.$basketType;
			      		 $rowArray['addHeight'] = 13;
			      		 $rowArray['colsY'] = -12;
			      		 
		      		 }
		      
		      		
		      		
					$qtyinstock = $this->ckrksheetModel->get_region_stuffqty($rows['StuffId'],'');
					
					if ($this->LoginNumber == 11965 && $qtyinstock<=0) {
				$qtyinstock = '2000';	
				}
					
					 

					$rowArray['in_stuff'] = array(
				    	'Text'=>$qtyinstock>0?(number_format(floatval($qtyinstock), $Decimals)):'',

'Color'=>'#01be56',
				    	'OutLineColor'=>'#FFFFFF',
				    	'OutLine'=>'3',
				    	'beling'=>'1',
				    	'lbl_alpha'=>'1',
	                );
		      		
		      
		      
		          $rowArray['col4']=number_format($rows['scQty'],$Decimals);
		          unset($rows['refreshTV']);
		          unset($rows['tv_IP']);
		          $rowArray['time']=$factoryCheck==1?'':array('Text'=>$rows['scDate'],'DateType'=>'time');
		         break;
		   }
		   $dataArray[]=$rowArray;
		   
		   if (count($remarkArray)>0){
		     	$dataArray[]=$remarkArray; 
		   }
	   }
	   
	   if ($segmentIndex==3){
	       //按月份显示品检报告
		   $this->load->model('QcBadrecordModel');
		   $sheets=$this->QcBadrecordModel->get_badrecord_month($Floor,3);
		   
		   foreach ($sheets as $rows){
		       $col1='';
			   if ($rows['Qty']>0){
				   $col1=array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['Qty']),'Color'=>$red,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['badCounts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         );
			   }
			   
			   
			   $timeMon = strtotime($rows['Month']);
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
					'tag'      =>'arrow',
					'method'   =>'qc_report',
					'isTotal'  =>'1',
					'type'     =>$Floor,
					'Id'       =>$rows['Month'],
					'title'    =>$titleAttr,
					'col1'     =>$col1,
				    'col2'     =>array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['shQty']),'Color'=>$black,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         )
					);
			}
	   }
	
	   return $dataArray;
	}
	
	//退料记录
	public function tl_segment()
	{
	    $params = $this->input->post();
	    $Floor         = element('type',$params,'');//送货楼层
	    $segmentIndex  = element('segmentIndex',$params,'');//选择分类
	   
        $lightgray = $this->colors->get_color('lightgray');
        $black     = $this->colors->get_color('black');
        $red       = $this->colors->get_color('red');
        
	    $this->load->model('QcBadrecordModel');
		$sheets=$this->QcBadrecordModel->get_unth_companylist($Floor,1); 
		
		$dataArray=array();
		
		foreach ($sheets as $rows){
		
		   $dataArray[]=array(
				'tag'      =>'edit',
				'showArrow'=>'1',
				'method'   =>'tl_sub',
				'type'     =>"$Floor",
				'open'     =>'0',
				'edit'     =>'0',
				'Id'       =>$rows['CompanyId'],
				'title'    =>$rows['Forshort'],
			    'col1'     =>array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['Qty']),'Color'=>$black,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         )
				);
		}
		
	   $this->load->model('CkthsheetModel');
	   $sheets=$this->CkthsheetModel->get_th_monthlist($Floor,3);
	   
	   foreach ($sheets as $rows){
	       $col1='';
		   if ($rows['unQty']>0){
			   $col1=array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['unQty']),'Color'=>$red,'FontSize'=>'13'),
			                 array('Text'=>'(' . $rows['unCounts']. ')','Color'=>$lightgray,'FontSize'=>'10')
			                )
                         );
		   }
		   $timeMon = strtotime($rows['Month']);
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
				'tag'      =>'arrow',
				'method'   =>'tl_main',
				'isTotal'  =>'1',
				'type'     =>$Floor,
				'Id'       =>$rows['Month'],
				'title'    =>$titleAttr,
				'col1'     =>$col1,
			    'col2'     =>array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['Qty']),'Color'=>$black,'FontSize'=>'13'),
			                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
			                )
                         )
				);
		}
		
	   
	   $totals=count($dataArray);
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	   $this->load->view('output_json',$data);
	}
	
	public function tl_sub()
	{
		$params = $this->input->post();
	    $Floor     = element('type',$params,'');//送货楼层
	    $CompanyId = element('Id',$params,'');
	    $uptag     = element('upTag',$params,'');
	    
	    $red        =$this->colors->get_color('red');
        $lightgray  =$this->colors->get_color('lightgray');

        $this->load->model('StuffdataModel');
	    $this->load->model('QcBadrecordModel');
	    
	    $actions=$this->pageaction->get_actions('printlabel');
	    
	    $sheets=$this->QcBadrecordModel->get_unth_list($Floor,$CompanyId);
	    
	    $dataArray=array();
	    foreach ($sheets as $rows){
		   $badPercent=round($rows['Qty']/$rows['shQty']*100,1);
		   $stuffImg=$rows['Picture']>0?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
		   $badimgs =$this->QcBadrecordModel->get_badpictures($rows['Id']);
		   $dataArray[]=array(
				'tag'     =>'stuffImgs',
				//'method'  =>'tl_sub_sub',
				'actions' =>$actions,
				'Id'      =>$rows['Id'],
				'title'   =>$rows['StuffId'] . '-' . $rows['StuffCname'],
				'col1'    =>$rows['shQty'],
				'col2'    =>array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['Qty']),'Color'=>$red,'FontSize'=>'12'),
			                 array('Text'=>'(' . $badPercent. '%)','Color'=>$lightgray,'FontSize'=>'8')
			                )
                         ),
                'Picture' =>$rows['Picture']==1?'1':'',
				'selected'=>'',
			    'stuffImg'=>$stuffImg,
                'time'    =>array('Text'=>$rows['created'],'DateType'=>'time'),
				'operator'=>$rows['Operator'],
				'imgs'    =>$badimgs
				);
		}
		
	   $totals=count($dataArray);
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	   $this->load->view('output_json',$data);
	    
	}
	
	public function arrive_top()
	{
	   $params = $this->input->post();
	   $types  = element('types',$params,'');//供应商CompanyId 
	   $Floor  = element('parentId',$params,'');//送货楼层

	   
	   $this->load->model('GysshsheetModel');
	   $typesArray=$this->GysshsheetModel->get_supplier_list($Floor,1);
	   $numsOfTypes=count($typesArray);
	   
	   $this->load->library('datehandler');
        $nowtimes = strtotime('now');
        
        $oneDayMinSec = 24 * 3600;
        $halfDaySec = 12 * 3600;
$versionNum = $this->versionToNumber($this->AppVersion);
		$is428Version = $versionNum >= 428 ? true : false;
		
	   
	   $dataArray=array();
	   for ($i = 0; $i < $numsOfTypes; $i++) {
		
		   $rows=$typesArray[$i];
		   
		   $carImg=$this->get_car_image(round($rows['Amount']));
		   $badge2=number_format($rows['Qty']);
	       $badge=($rows['Qty']>=1000)?round($rows['Qty']/1000) . 'k':round($rows['Qty']);
	           
	           
	       $minus = $nowtimes - strtotime($rows['time']);
           $time = $this->datehandler->GetTimeInterval($minus,1);
			$timeColor = $minus >$oneDayMinSec ?'#ff0000':'#ff921d';		


           
           $timeImg = '';
           if ($minus > $oneDayMinSec) {
	           $timeImg = 'clock_red';
	           $timeColor = '#ff0000';
	           if ($is428Version) {
		           $carImg = $carImg.'_red';
	           }
           } else if ($minus > $halfDaySec) {
	           $timeImg = 'clock_orange';
	           $timeColor = '#ff921d';
	           if ($is428Version) {
		           $carImg = $carImg.'_red';
	           }
           }
           

           /*
	           $dataArray[]=array(
                       'parentId'=> out_format($Floor),
                       'timeImg'=>$timeImg,
                       'timeBling'=>array('beling'=>'1',
									'blingVals' =>array(1,0.65,0.16,0.65,1),
									),
                       'time_s'=>$timeImg==''?null:array('Text'=>$time,'Color'=>$timeColor),
		                     'Id' => out_format($rows['CompanyId']),
						  'qty' => "$badge",
						  'forshort' => out_format($rows['Forshort']),
						'halfImg' => $rows['CompanyId']<1000 ? 'halfClear' :'',
						 'icon' => $carImg
		    );
           */

	           
	       $dataArray[]=array(
		       'tag'=>'new',
// 		       'timeImg'=>$timeImg,
		       'iconBling'=>array('beling'=>$timeImg==''?'':'1',
									'blingVals' =>array(1,0.65,0.16,0.65,1),
									),
//                        'time_s'=>$timeImg==''?null:array('Text'=>$time,'Color'=>$timeColor),
                     'Id' => out_format($rows['CompanyId']),
				  'badge' => "$badge",
				  'qty' => "$badge2",
				  'forshort' => out_format($rows['Forshort']),
				  'title' => out_format($rows['Forshort']),
				'halfImg' => $rows['CompanyId']<1000 ? 'halfClear' :'',
				'icon' => $carImg,
				 'carImg' => $carImg
			    );        
		} 
		
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$numsOfTypes,'rows'=>$dataArray);
	    $this->load->view('output_json',$data);
		    
	}
	
	public function arrive_main(){
		$params = $this->input->post();
		
		$CompanyId = element("types",$params,"");
		$Floor     = element("parentId",$params,"");
		$Estate =1;//未到达
		 
		$red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black      =$this->colors->get_color('black');
        
		 $dataArray[]=array('no'=>'1');
		 
		 $totals=0;
		if ($CompanyId>0){
		     $this->load->model('BaseMPositionModel');
		     $records=$this->BaseMPositionModel->get_records($Floor);
		     $FloorAddress=$records['Address']; 
		      
		     $records=null;
		      
		     if ($CompanyId>=1000){
			    $this->load->model('TradeObjectModel');
		        $records=$this->TradeObjectModel->get_records($CompanyId);
		        $Address=$records['Address'];
		     }
		     else{
			     $this->load->model('WorkShopdataModel');
			     $records=$this->WorkShopdataModel->get_records($CompanyId);
		         $Address=$records['AddressName'] .  substr($records['Floor'],-1) . 'F'; 
		    }

		    $this->load->model('GysshsheetModel');
		    $mrows=$this->GysshsheetModel->get_sh_order($Floor,$CompanyId,$Estate);
		    $totals=count($mrows);
		    
		    $this->load->model('stuffdataModel');
		    $actions=$this->MenuAction;
		    foreach ($mrows as $rows){
		        $rowArray=array();
		        $BillNumber=$rows['BillNumber'];
		        
		        $Decimals = element('Decimals', $rows, 0);
		        
			    $rowArray[]=array(
			                'tag'     =>'distance',
							'col1'    =>$rows['OverQty']==0?'':number_format($rows['OverQty'],$Decimals),
							'col2'    =>array(
                                                'isAttribute'=>'1',
							                    'attrDicts'  =>array(
                                                 array('Text'=>number_format($rows['Qty'],$Decimals),'Color'=>"$black",'FontSize'=>'13'),
								                 array('Text'=>'(' . $rows['Counts'] . ')','Color'=>"$lightgray",'FontSize'=>'10')
								                )
	                                       ),
							'title'   =>out_format($BillNumber),
							'time'    =>array('Text'=>$rows['created'],'DateType'=>'time'),
							'begin'   =>"$Address",
							'end'     =>"$FloorAddress",
							'distance'=>''
							
				);
				
				$distance=array(
				            'begin'   =>"$Address",
							'end'     =>"$FloorAddress",
							'distance'=>''
				);
				
				$sheets=$this->GysshsheetModel->get_billnumber_sheet($BillNumber,$Estate);
				
				foreach ($sheets as $rows){
					  $Decimals = element('Decimals', $rows, 0);
					
				   $stuffImg=$rows['Picture']>0?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
				   $rtIcon=$this->get_checksign_image($rows['CheckSign']);
				   $rowArray[]= array(
						'tag'     =>'qcStuff',
						'hideLine'=>'0',
						'actions' =>$actions,
						'Id'      =>$rows['Id'],
						'StuffId' =>$rows['StuffId'],
						'StockId' =>$rows['StockId'],
						'week'    =>$rows['DeliveryWeek'],
						'rtIcon'  =>$rtIcon,
						'title'   =>$rows['StuffId'] . '-' . $rows['StuffCname'],
					    'col2'    =>number_format($rows['OrderQty'],$Decimals),
						'col3'    =>number_format($rows['Qty'],$Decimals),
						'Picture' =>$rows['Picture']==1?'1':'',
						'selected'=>'',
				        'stuffImg'=>$stuffImg,
				        'process' =>array()
				        /*
						'process' =>array(
						         array('Title'=>'采','Color'=>'','Value'=>'916','Badge'=>'1'),
								 array('Title'=>'单','Color'=>'','Value'=>'916','Badge'=>'1'),
								 array('Title'=>'检','Color'=>'','Value'=>'916','Badge'=>'1'),
								 array('Title'=>'入','Color'=>'','Value'=>'916','Badge'=>'1')
						)
						*/
					);
				}
				$rowCount = count($rowArray);
				if ($rowCount > 0) {
					$rowArray[$rowCount-1]['round'] = '1';
					$rowArray[$rowCount-1]['hideLine'] = '1';
					
				}
				$dataArray[]=array('data'=>$rowArray,'Id'=>$BillNumber);
			}
		}
		
		
		$data['jsondata']=array('status'=>$distance,'message'=>'','totals'=>'1','rows'=>$dataArray);
	    $this->load->view('output_json',$data);
	}
	
	
	public function get_location()
	{
	    
	    $params = $this->input->post();
	    $Sid    = element("Id",$params,'');
	    $Ids    = element("Ids",$params,'');
	     
	    $this->load->model('GysshsheetModel');
	    $this->load->model('CkrksheetModel');
	    $this->load->model('StuffdataModel');
	    
	    $records = $this->GysshsheetModel->get_records($Sid);
	    $Floor   = $records['Floor'];
	    $StuffId = $records['StuffId'];
	    
	    $records = null;
	    
	    $locations = '';
	    $records = $this->CkrksheetModel->get_stuff_location($StuffId);
	    
	    $stuffs        = $this->StuffdataModel->get_records($StuffId);
	    $CheckSign     = $stuffs['CheckSign'];
	    $FrameCapacity = $stuffs['FrameCapacity'];
	    $basketType = $stuffs['basketType'];
	    foreach ($records as $row){
	       $rkloc     = '未设置';
	       $rkQty     = number_format($row['rkQty']);
	       
	       if ($row['Identifier']!=''){
		      $idents=explode('-', $row['Identifier']);
	          $rkloc=$idents[count($idents)-1]; 
	       }
	       
	       $locations .= $locations==''?$rkQty . "($rkloc)":"," . $rkQty . "($rkloc)";
	    }
	    
	    $this->load->model('QcCjtjModel');
	    if ($Ids!=''){
		    $unrkQty = $this->QcCjtjModel->get_unrkqty($Ids);
	        $unrkCounts = $this->QcCjtjModel->get_unrk_counts($Ids);
	    }else{
		    $unrkQty = $this->QcCjtjModel->get_unrkqty($Sid);
	        $unrkCounts = $this->QcCjtjModel->get_unrk_counts($Sid);
	    }
	    
	    if ($CheckSign==0 && $FrameCapacity>0){
		    $frames=ceil($unrkQty/$FrameCapacity);
		    
		    $unrkCounts=$frames;
	    }
	    
	    $dataArray=array();
	    switch($Floor){
		    case 3:
		    case 17:
		        $this->load->model('CkLocationModel');
	            $dataArray=$this->CkLocationModel->get_locations($Floor,$StuffId);
		        break;
		    case 6:
		    case 12:
		        $this->load->model('CkLocationModel');
	            $dataArray=$this->CkLocationModel->get_locations(3,$StuffId);
		      break;
	    }
	    
	    
	    
	    

	    $status = array('zaiku' =>$locations,
	    				'qty'   =>"$unrkQty",
	    				'frames'=>"$unrkCounts"
	    				);
	    if ($basketType > 0 && $unrkCounts>0) {
		    $status['frameNum']=$unrkCounts;
		    $status['frameImg']='frame_'.$basketType;
		    $status['$FrameCapacity'] = 'frame_'.$FrameCapacity;
	    } 		
	    
	    
	   $data['jsondata']=array('status'=>$status,'message'=>'','user'=>'','rows'=>$dataArray);
	    
		$this->load->view('output_json',$data);
	}
	
	
	
	public function optionlist(){
	    $params = $this->input->post();
	    $upTag  = element("upTag",$params,'');
		$types  = element("types",$params,'3');
		
		$types=explode(',', $types);
		$this->load->model('QcsclineModel');
		$dataArray=	$this->QcsclineModel->get_scline($types[0]);
		
		 $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);	
		 	
	}
	

    public function causetype(){
	    $params = $this->input->post();
	    $Sid   = element("Id",$params,''); 
	    
	    $this->load->model('GysshsheetModel');
	    $records=$this->GysshsheetModel->get_records($Sid);
	    $type   =$records['TypeId'];
	    
	    $this->load->model('QcCauseTypeModel');
		$dataArray=	$this->QcCauseTypeModel->get_type_cause($type);
		
		$counts=count($dataArray);
		if ($counts==0){
			$dataArray=	$this->QcCauseTypeModel->get_type_cause(1);
		    $counts=count($dataArray);
		}
		$dataArray[]=array('Id'=>'-1','Name'=>'其它原因','Color'=>'#FF0000');
		
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$counts,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
    }
	
	function get_titles()
	{
		return array('待检','品检中','品检报告','待入库','退料');
	}
	
	function get_car_image($Amount)
	{
	   return $Amount>=200000?'qc_new_car_3':($Amount>=50000?'qc_new_car_2':'qc_new_car_1');
	}
	
	function get_checksign_image($checkSign)
	{
		return 'qc_check_' . $checkSign;
	}
	
	//设置到达状态
	public function arrive()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Ids    = element("Id",$params,'');
	    $idList = element("idList",$params,'');
	    $Floor     = element("parentId",$params,"");
	    
	    $rowArray=array();
	    $status=0;
	    if ($action == 'multi_arrive'){
		    $idLists=explode(';', $idList);
		    $Ids='';
		    
		    foreach ($idLists as $List){
		       $Lists=explode(':', $List);
		       if (count($Lists)==2){
			    $Ids.=$Ids==''?$Lists[1]:',' . $Lists[1];
		       }
		    }
	    }
	    
	    if (($action=='arrive' ||  $action == 'multi_arrive') && $Ids!=''){
		    $this->load->model('GysshsheetModel');
		    $status=$this->GysshsheetModel->set_estate($Ids,2);
		    if ($status==1){
		       /*
			    $this->load->model('QcsclineModel');
		        $scLines=$this->QcsclineModel->get_sclineNo($Floor);
		        
	       
		        if (count($scLines)==1){
			        $lineId=key($scLines);
			        $this->load->model('QcMissionModel');
			        
			        $idLists=explode(',', $Ids);
			        foreach ($idLists as $Id){
			           $status=$this->QcMissionModel->save_records($Id,$lineId);
			        }
		        }

		       */		        
			    $rowArray=array(
			            'Action' =>'delete' 
			          );
		    }
	    }
	    
	    $message=$status==1?'设置到达状态成功！':'设置到达状态失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
	}
	
	//分配品检线
	public function allot()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id    = element("Id",$params,'');
	    $lineId = element("lineId",$params,'');
	    
	    
	    $rowArray=array();
	    $status=0;
	    if ($action == 'allot'){
	       $this->load->model('QcMissionModel');
	       $status=$this->QcMissionModel->save_records($Id,$lineId);
	       if ($status==1){
			    $rowArray=array(
			            'Action' =>'delete' 
			          );
		    }
	    }
	    $message=$status==1?'分配品检线成功！': '分配品检线失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
	}
	
	
	public function multi_allot()
	{
		$params  = $this->input->post();
	    $action  = element("Action",$params,'');
	    $ListIds = element("ListIds",$params,'');
	    $lineId  = element("lineId",$params,'');
	    
	    
	    $rowArray=array();
	    $status=0;
	    if ($action == 'multi_allot'){
	        $this->load->model('QcMissionModel');
	        $ListIdsArr = explode(',', $ListIds);
		    foreach ($ListIdsArr as $Sid) {
		    	$status=$this->QcMissionModel->save_records($Sid,$lineId);
		    }
	       
	        if ($status==1){
			    $rowArray=array(
			            'Action' =>'' 
			          );
		    }
	    }
	    $message=$status==1?'分配品检线成功！': '分配品检线失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
	}
	
	//设置当前任务
	public function settasks()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id    = element("Id",$params,'');
	    
	    $rowArray=array();
	    $status=0;
	    
	    if ($action=='settasks' && $Id>0){
	            $this->load->model('GysshsheetModel');
	            $records=$this->GysshsheetModel->get_records($Id);
	            $Floor = $records ['Floor'];
	            
		        $this->load->model('QcsclineModel');
		        $scLines=$this->QcsclineModel->get_sclineNo($Floor);
		        
	       
		        if (count($scLines)==1){
			        $lineId=key($scLines);
			        $this->load->model('QcMissionModel');
			        $status=$this->QcMissionModel->save_records($Id,$lineId);
			        if ($status==1){
						    $rowArray=array(
						            'Action' =>'delete' 
						          );
					    }
		        }
	    }
	    
	    $message=$status==1?'设置当前任务成功！': '设置当前任务失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
	    
	}
	
	//退料功能
	public function returnstock()
	{
	    $params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id     = element("Id",$params,'');
	    
	    $rowArray=array();
	    $status=0;
	    
	    if ($action=='returnstock' && $Id>0){
		    $this->load->model('QcBadrecordModel');
		    $status=$this->QcBadrecordModel->save_returnstock($params);
		    
		    if ($status==1){
			    $rowArray=array(
			            'Action' =>'delete' 
			          );
		    }
	    }
	    
	    $message=$status==1?'退回操作成功！': '退回操作失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data); 
    }
	//退料功能
	public function multi_returnstock()
	{
	    $params = $this->input->post();
	    $action = element("Action",$params,'');
	    $ListIds     = element("ListIds",$params,'');
	    
	    $rowArray=array();
	    $status=0;
	    
	    if ($action=='multi_returnstock' && strlen($ListIds)>0){
		    $this->load->model('QcBadrecordModel');
		    $status=$this->QcBadrecordModel->save_multi_returnstock($params);
		    
		    if ($status==1){
			    $rowArray=array(
			            'Action' =>'' 
			          );
		    }
	    }
	    
	    $message=$status==1?'退回操作成功！': '退回操作失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data); 
    }

	//退回功能
	public function goback()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id    = element("Id",$params,'');
	    
	    $rowArray=array();
	    $status=0;
	    /*
	     if ($action=='goback'){
		  $this->load->model('GysshsheetModel');
		    $status=$this->GysshsheetModel->set_estate($Id,1);
		    if ($status==1){
			    $rowArray=array(
			            'Action' =>'delete' 
			          );
		    }
	    }
	    
	    $message=$status==1?'退回操作成功！': '退回操作失败!';
	    */
	    $message='退回操作暂未启用';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);  
	}
	
	//更改品检方式
	public function change()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id    = element("Id",$params,'');
	    
	    $this->load->model('GysshsheetModel');
	    $records=$this->GysshsheetModel->get_records($Id);
	    $StuffId=$records['StuffId'];
	    $records=null;
        
        $rowArray=array();
	    if ($action=='change' && $StuffId>0){
	        $this->load->model('StuffdataModel');
	        $records=$this->StuffdataModel->get_records($StuffId);
	        $checkSign=$records['CheckSign']==1?0:1;
	    
		    $status=$this->StuffdataModel->set_checksign($StuffId,$checkSign);
		    if ($status==1){
		        $rtIcon=$this->get_checksign_image($checkSign);
			    $rowArray=array(
			            'rtIcon' =>$rtIcon 
			          );
		    }
	    }
	    $dataArray=array('data'=>$rowArray);
	    
	    $message=$status==1?$StuffId .' 更改品检方式成功！': $StuffId .' 更改品检方式失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);  
	}
	
	//备注
	public function remark()
	{
		$params = $this->input->post();
		
	    $action = element("Action",$params,'');
	    $Id     = element("Id",$params,'');
	    $remark = element("remark",$params,'');
	    
	    $status=0;
	    $rowArray=array();
	    if ($action=='remark' && $Id!='')
	    {
		    $this->load->model('GysshRemarkModel');
		    $status=$this->GysshRemarkModel->save_shremark($Id,$remark);
		    if ($status==1){
			    $rowArray=array(
			            'remark'  =>$remark
			          );
		    }
	    }
	    
	    $dataArray=array(
		            'data'   =>$rowArray
		            );
	    
	    $message=$status==1?'保存备注信息成功！':'保存备注信息失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	//设置装框数量
	public function setFrameCapacity()
	{
		$params        = $this->input->post();
		$action        = element('Action',$params,'');
	    $StuffId       = element('StuffId',$params,'');
	    $FrameCapacity = element('FrameCapacity',$params,'');
	
	    $status=0;
	    $rowArray=array();
	    if ($action=='setFrameCapacity' && $StuffId>0 &&  $FrameCapacity>0)
	    {
		    $this->load->model('StuffdataModel');
		    $status=$this->StuffdataModel->set_framecapacity($StuffId,$FrameCapacity);
		    if ($status==1){
			    $rowArray=array(
			            'FrameCapacity' =>"$FrameCapacity"
			          );
		    }
	    }
	    $dataArray=array("data"=>$rowArray);
	    
	    $message=$status==1?'设置成功！':'设置失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);   
	}
	
	//品检数量登记
	public function register()
	{
		$params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $djtype     = element('djType',$params,'');
	    $bpQty      = element('bpQty',$params,'0');
	    
	    $this->load->model('GysshsheetModel');
	    $this->load->model('StuffDataModel');
	    $Sid    = element('Id',$params,'0');
	    $records=$this->GysshsheetModel->get_records($Sid);
        $StuffId=$records['StuffId'];
        
        $frameCapacity = $this->StuffDataModel->get_framecapacity($StuffId);
        $speak = '保存失败';
	    if ($action=='register' && $djtype=='qc' && $frameCapacity>0){
	        $rowArray=array();
	        $actions =array();
	        
	        $basketType = element('frameTypeId', $params, -1);
	        if ($basketType > 0) {
		        $this->StuffDataModel->set_basketType($StuffId, $basketType);
	        }
	        
	        
		    $this->load->model('QcCjtjModel');
	        $newId=$this->QcCjtjModel->save_records($params);
		    
		    $actions=array();
		    $rowArray=array();
		    
		    if ($newId>0){
		       $status=1;
		       $message='登记成功!';
		       
		       
		       $djQty  = element('Qty',$params,'0');
		       
		       
		       
		       $newbpId =0;
		       $speak = '登记'.$djQty;
		       if ($bpQty>0){
			       
		          
		          $this->load->model('Ck7bprkModel');
		          $newbpId=$this->Ck7bprkModel->save_records($StuffId,$bpQty,'品检备品入库',1);
		          $speak .= ',备品'.$bpQty;
		       }
			   //标签打印设置
			   
			   $records=$this->GysshsheetModel->get_records($Sid);
		       $ActionId=$records['ActionId'];
		       
		       if ($ActionId==0 || ($ActionId>0 && ($ActionId==104 || $ActionId==105))){
			        $actions=$this->get_action_print($Sid,$newId,$djQty,$bpQty);
		       } 
		       
		       $djedQty  =$this->QcCjtjModel->get_qcqty($Sid); 
		          
		       $rowArray=array(
		                'refreshTV' =>'1',
			            'col4'      =>array('Text'=>number_format($djedQty))
			          );
		    }
		    else{
			    $message=$status==2?'登记数量大于送货单数量':'登记失败!';
			    if ($frameCapacity <= 0) {
				    $message = '未设置装框数量';
			    }
			    $status=0;
		    }
		    
		   $dataArray=array(
		            'actions'=>$actions,
		            'data'   =>$rowArray
		            );
		   
	        $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$status,'rows'=>$dataArray,'speak'=>$speak);
	    }
	    else{
		    $message = '非法操作!';
		     if ($frameCapacity <= 0) {
				    $message = '未设置装框数量';
			    }
		    $data['jsondata']=array('status'=>'0','message'=>$message,'totals'=>'0','rows'=>array(),'speak'=>$speak);
	    }
	    
		$this->load->view('output_json',$data);
	}
	
	public function printlabel()
	{
		$params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $Bid        = element('Id',$params,'0');
	    
	    $actions=array();
		$status = 0;
		    
	    $this->load->model('QcBadrecordModel');
	    $records=$this->QcBadrecordModel->get_records($Bid);
	    
	    $Qty = $records['Qty'];
	    if ($Qty>0){
	        $status = 1;
	        $this->load->model('LabelPrintModel');
	        $prints[]=$this->LabelPrintModel->get_qcbadrecords_print($Bid);
            $actions=$this->PrintAction;
            $actions[0]['data']= $prints;
		}
		$dataArray=array(
		            'actions'=>$actions,
		            'data'   =>array()
		            );
		            
	    $message=$status==1?'生成退料数据成功！': '生成退料数据失败!';
	    
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$status,'rows'=>$dataArray);
	    $this->load->view('output_json',$data); 
	}
	
	//获取标签打印内容
	public function get_action_print($Sid,$newId,$djQty,$bpQty){
	
	    $this->load->model('LabelPrintModel');
        $prints[]=$this->LabelPrintModel->get_qcregister_print($Sid,$newId,$djQty,$bpQty);
        $actions=$this->PrintAction;
        $actions[0]['data']= $prints; 
        
        return $actions;
	}
	
   //品检报告
   public function qcreport()
   {
		$params = $this->input->post();
		
	    $action = element("Action",$params,'');
	    $Id     = element("Id",$params,'');
	    
	    $status=0;
	    $rowArray=array();
	    if ($action=='qcreport' && $Id!=''){
		    $this->load->model('QcBadrecordModel');
		    $Bid=$this->QcBadrecordModel->save_records($params);
	        if ($Bid>0){
	            $status = 1;
	            
	            $noaccept = element('noaccept', $params,'');
				 $remark = '';
				 if ($noaccept == '1') {
					 $remark = '抽检不合格';
					 
					 
					 
				 }
	            
	            
	            $this->load->model('LabelPrintModel');
		        $prints[]=$this->LabelPrintModel->get_qcbadrecords_print($Bid);
		        if (count($prints[0])>0){
			        $actions=$this->PrintAction;
			        $actions[0]['Action']= 'print';
	                $actions[0]['data']= $prints;
		        }else{
			        $actions=array();
		        }
	            
			    $rowArray=array(
			            'actions'=>$actions,
			            'Action' =>'delete' 
			          );
		    }
	    }
	    
	    $message=$status==1?'生成品检报告成功！': '生成品检报告失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data); 
   }

	
	//品检配件入库(需选择库位)
   public function rk_save()
   {
	   $params = $this->input->post();
	   $Id         = element("Id",$params,'');
	   $Ids        = element("Ids",$params,'');
	   $LocationId = element("LocationId",$params,'0'); //库位Id 
	   $frameCount = element("frameCount",$params,'');  //框数
	   
	   $Ids=$Ids==''?$Id:$Ids;
	   
	   if ($Ids!=''){
		   $this->load->model('CkrksheetModel');
		   $status=$this->CkrksheetModel->save_location_records($Ids,$frameCount,$LocationId);
		   
		   if ($status==1){
			    $rowArray=array(
			            'Action' =>'' 
			          );
		    }
	   }
	   
	    $message=$status==1?'配件入库成功！': '配件入库失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
	   
   }
   
   public function stockin()
   {
		$params = $this->input->post();
		
	    $action = element("Action",$params,'');
	    $Id     = element("Id",$params,'');
	    
	    $status=0;
	    $rowArray=array();
	    if ($action=='stockin' && $Id!=''){
	    
		   $this->load->model('CkrksheetModel');
		   $status=$this->CkrksheetModel->save_records($Id);
		   
		   if ($status==1){
			    $rowArray=array(
			            'Action' =>'delete' 
			          );
		    }
		    
	    }
	    $message=$status==1?'配件入库成功！': '配件入库失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
   }
   
   //生成退料单记录
   public function tlOrder()
   {
	    $params = $this->input->post();
		
	    $action = element("Action",$params,'');
	    $Ids    = element("ListIds",$params,'');
	    
	    $status=0;
	    $rowArray=array();
	    if ($action=='tlOrder' && $Ids!=''){
		    $this->load->model('CkthsheetModel');
		    $status=$this->CkthsheetModel->save_records($Ids);
		    if ($status==1){
				    $rowArray=array(
				            'Action' =>'' 
				          );
			    }
		}
	    
	    $message=$status==1?'生成退料单成功！': '生成退料单失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data); 
   }
}
