<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Warehouse extends MC_Controller {

    public $SetTypeId= null;
    public $MenuAction= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 2;
        $this->MenuAction   = $this->pageaction->get_actions('picking');//领料
    }
        
    public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','user'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	
	public function get_maintypes()
	{
        $typesArray = array();
        $typesArray[] = array('Id'=>'3' ,'title'=>'组装','titleImg'=>'dzz');
        $typesArray[] = array('Id'=>'17','title'=>'加工','titleImg'=>'process');
        $typesArray[] = array('Id'=>'0' ,'title'=>'外发','titleImg'=>'outgoing');
        
        return $typesArray;
	}
	
	
	public function main(){
	
		$params = $this->input->post();
		$segIndex= element('segIndex',$params,'-1');
        
        $this->load->model('CkllsheetModel'); //备料情况
        $this->load->model('CkbldatetimeModel'); 
        $this->load->model('StaffMainModel'); 
        
        $records = $this->StaffMainModel->get_records($this->LoginNumber);
        $WorkAdd = $records['WorkAdd'];
        $records =null;
        
        $segIndex =$segIndex=='-1'?($WorkAdd==2?1:0):$segIndex;
        
        //$segIndex  = $segIndex=='-1'?0:$segIndex;
        
        $typesArray = $this->get_maintypes();
        
        $numsOfTypes = count($typesArray);
        $dataArray = array();
        $listdatas = array(); 
        $headArray = array();
        
        for ($i = 0; $i < $numsOfTypes; $i++) {  
            
            $sendfloor = $typesArray[$i]['Id'];
            
            $bledArray = $this->CkllsheetModel->get_today_blcounts($sendfloor);
            $bledCounts = $bledArray['Counts'];
            $bledNewCounts = $bledArray['newCounts'];
            
            if ($sendfloor>0){
	            
	             $blArray = $this->CkllsheetModel->get_canstock_qty($sendfloor);
            }else{
	             $blArray = $this->CkllsheetModel->get_outward_blqty();//$sendfloor
            }
            
            
            $kblCounts = $blArray['Counts'];
            if (element('realcount', $blArray , 0) > 0) {
	            $kblCounts = $blArray['realcount'];
            }
            

            
            $hourCounts = $this->CkbldatetimeModel->ck_onehours_blcounts($sendfloor);
            
            if ($segIndex==$i){
               switch($sendfloor){
	               case 0:
	                     $listdatas=$this->get_segment_outbl($sendfloor,$segIndex);
	                 break;
	             default:
	                     $listdatas=$this->get_segment_dbl($sendfloor,$segIndex);
	                     
	                     //if ($this->LoginNumber==10868){
                        $feedArray=array();    
                        $feedArray = $this->get_not_feeding($sendfloor,$segIndex);
                        $listdatas = array_merge($listdatas,$feedArray);
	                    // }
	                break;   
               }
            }
	        
	        $sumCounts = $bledCounts + $kblCounts;
	        $hourCounts=$hourCounts>$bledCounts?$bledCounts:$hourCounts;
	        $percent1  = $sumCounts>0?round(($bledCounts-$hourCounts)/$sumCounts * 100):100;
            $percent3  = $sumCounts>0?round($kblCounts/$sumCounts * 100):0;
            $percent2  = 100-$percent1-$percent3; 
              
            $percentColor = $sumCounts==0?'#EEEEEE':'#FFD04F';
            $headArray[] = array(
	            			'beling'=>array('beling'=>'1'),
                            'Id'         =>$typesArray[$i]['Id'],
                            'title'      =>$typesArray[$i]['title'],
                            'index'      =>$i,
                            'bledValue'  =>"$bledCounts",
                            'kblValue'   =>"$kblCounts",
                            'bledSign'   =>$bledNewCounts>0 || $this->LoginNumber==11965?1:0,
                            'arrowSign'  =>$bledNewCounts>0|| $this->LoginNumber==11965?1:($bledNewCounts==0?0:-1),
                            'pieValue'   =>array(
										   array('value'=>$percent1,'color'=>"$percentColor"),
										   array('value'=>$percent2,'color'=>'#FFE399'),
										   array('value'=>$percent3,'color'=>'#FFFFFF')
									      )
                     );
        }
         
         $dataArray[] = array(
                            'tag'        =>'ckMain',
							'hidden'     =>'0',
							'segIndex'   =>"$segIndex",
							'method'     =>'segment',
                            'headdata'   =>$headArray,
							'data'       =>$listdatas
                     );

         
         $totals = count($dataArray);
         $data['jsondata']=array('status'=>'1','message'=>"$segIndex",'totals'=>$totals,'datas'=>$typesArray,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);
		
	}
	
	public function segment(){
	
		$params       = $this->input->post();
		$segmentIndex = intval( element("segmentIndex",$params,0));
		$upTag        = element("upTag",$params,'zorder');
		$sendfloor    = intval( element("type",$params,''));
		$checkSign  = element("checkSign",$params,'KBL');
		$Id           = element("Id",$params,'');
		
		$dataArray=array();
		if ($checkSign=='Feed'){
			    $dataArray=$this->get_not_feeding_bl($sendfloor,$segmentIndex);
		}
		else{
				switch($upTag){
					case 'ckGroup':
					    $WorkShopId= $Id;
					    $dataArray=$this->get_subList_order($sendfloor,$WorkShopId,$segmentIndex,$checkSign);
					  break;
				}
		}
		
		$totals=count($dataArray);
		
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
  public function subList() 
 {
		$params       = $this->input->post();
		$segmentIndex = intval( element("segmentIndex",$params,0));
		$upTag        = element("upTag",$params,'zorder');
		$sendfloor    = intval( element("type",$params,''));
		$checkSign  = element("checkSign",$params,'DBL');
		$Id           = element("Id",$params,'');
		
		$dataArray=array();
		switch($upTag){
		    case 'zorder':
		        $sPOrderId = $Id;
		        $dataArray=$this->get_subList_stuff($sPOrderId,$sendfloor,$checkSign,$segmentIndex,$upTag);
		      break;
		    case 'dbl':
		    case 'order':
		         $sPOrderId = $Id;
		         $dataArray=$this->get_subList_stuff($sPOrderId,$sendfloor,'KBL',$segmentIndex,$upTag);
		      break;
		}
		
		$totals=count($dataArray);
		
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);

	}
    /** 
	* get_segment_dbl  
	* 待备料明细
	* 
	* @access public 
	* @param  params $sendfloor，$segmentIndex 一条纪录所需数据
	* @return int 返回各楼层待备料明细
	*/     
    function get_segment_dbl($sendfloor,$segmentIndex){
		
		$this->load->model('BaseMPositionModel');
		$this->load->model('WorkShopdataModel');
		$this->load->model('ProductdataModel');
		$this->load->model('staffMainModel');
		$this->load->model('ScSheetModel');
		
		$qtycolor   =$this->colors->get_color('qty');
	    $black      =$this->colors->get_color('black');
	    $lightgray  =$this->colors->get_color('lightgray');
	    $lightgreen =$this->colors->get_color('lightgreen');
	    
	    $factoryCheck=$this->config->item('factory_check');

		$records = $this->BaseMPositionModel->get_records($sendfloor);
		$BlWorkShopIds = $records['BlWorkShopId'];
		$records = null;
		
		$WsidArray=explode(',', $BlWorkShopIds);
		$BlWorkShopId=$WsidArray[0];
		
		$records = $this->WorkShopdataModel->get_records($BlWorkShopId);
		$ActionId = $records['ActionId'];
		$semiSign = $records['semiSign'];
		$records = null;
		
		$actions = array();
		$dataArray=array();

		switch($semiSign){
			case '0'://组装
			       $checkSigns   = array('DBL','KBL');
			       $checkNames = array('待备料','可占用');
			       for($n=0,$counts=count($checkSigns);$n<$counts;$n++)
			       {
				           $checkSign=$checkSigns[$n];
				           $pos = count($dataArray);
				           
				           $rowArray =$this->get_subList_order($sendfloor,$BlWorkShopId,$segmentIndex,$checkSign);
							$rownums  = count($rowArray);

					         $sumQty = 0;
					         for ($i = 0; $i < $rownums; $i++) {
									    $rows =$rowArray[$i];
									     $sumQty+=$rows['OrderQty'];
									     $dataArray[] = $rows;
							  }
							 $headArray=array();      
							 $headArray[]=array(
							    'type'       =>$sendfloor,
							    'segIndex'   =>$segmentIndex,
							    'checkSign' =>$checkSign,
								'tag'        =>'ckGroup',
								'method' =>'segment',
								'Id'         =>$BlWorkShopId,
								'showArrow'  =>'1',
								'open'       =>$rownums>0?'1':'0',
								'half'       =>'0',
								'arrowImg'   =>'UpAccessory_blue',
								'tImg'       =>'',//'ws_' . $BlWorkShopId
								'title'      =>array('Text'=>$checkNames[$n],'Color'=>"$black"),
								'col1'       =>'',
								'col2'       =>array(
												'isAttribute'=>'1',
												'attrDicts'  =>array(
											      array('Text'=>number_format($sumQty),'Color'=>"$black",'FontSize'=>"12"),
										          array('Text'=>"(".$rownums. ')','Color'=>"$lightgray",'FontSize'=>"9")
												   )
												)
							   );
							   array_splice($dataArray,$pos,0,$headArray);
					     }
					     
		        break;
		        
		    default:
		         $rowArray = $this->ScSheetModel->get_semi_canstock($BlWorkShopIds);
				 $rownums  = count($rowArray);
				 $checkSign= 'KBL';
				 
				 $wsnums = count($WsidArray);
				for ($m = 0; $m < $wsnums; $m++) {
				     $WorkShopId=$WsidArray[$m];
				     $title = ''; 
				     $qty =$counts = 0;
				     foreach($rowArray as $rows){
					      if ($rows['WorkShopId']==$WorkShopId){
						          $title = $rows['Name'];
						          $qty  = $rows['Qty'];
						          $counts =  $rows['Counts'];
						          break;
					      }
				     }
				     $openSign = 0;
				     if ($title==''){
					      $records = $this->WorkShopdataModel->get_records($WorkShopId);
					      $title = $records['Name'];
				     }else{
					     $subArray =$this->get_subList_order($sendfloor,$WorkShopId,$segmentIndex,$checkSign);
					     $openSign =count($subArray)>0?1:0;
				     }
				    
				    $dataArray[]=array(
							    'type'       =>$sendfloor,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'ckGroup',
								'method' =>'segment',
								'checkSign' =>$checkSign,
								'Id'         =>$WorkShopId,
								'showArrow'  =>'1',
								'open'       =>$openSign,
								'half'       =>'0',
								'arrowImg'   =>'UpAccessory_blue',
								'tImg'       =>'ws_' . $WorkShopId,
								'title'      =>array('Text'=>$title,'Color'=>"$black"),
								'col1'       =>'',
								'col2'       =>array(
												'isAttribute'=>'1',
												'attrDicts'  =>array(
											      array('Text'=>number_format($qty),'Color'=>"$black",'FontSize'=>"12"),
										          array('Text'=>"(".$counts . ')','Color'=>"$lightgray",'FontSize'=>"9")
												   )
												)
							);
							
							if ($openSign==1){
								   $dataArray= array_merge($dataArray,$subArray);
							}
							

				}
		        break;
        }
        	     
        return $dataArray;
	}
	
	public function get_not_feeding($sendfloor,$segmentIndex)
	{
	   $redcolor      =$this->colors->get_color('red');
	   $black      =$this->colors->get_color('black');
	    $lightgray  =$this->colors->get_color('lightgray');
	    $lightgreen =$this->colors->get_color('lightgreen');
	    
	    $this->load->model('BaseMPositionModel');
	    $this->load->model('CkreplenishModel');
	    
	    $records = $this->BaseMPositionModel->get_records($sendfloor);
		$BlWorkShopIds = $records['BlWorkShopId'];
		$records = null;
		
		$records = $this->CkreplenishModel->get_not_feedings($BlWorkShopIds,1);
		$Qty  =      $records['Qty'];
        $Counts = $records['Counts'];
        $records = null;
        
        $subArray = $this->get_not_feeding_bl($sendfloor,$segmentIndex);
		$rownums  = count($subArray);
        $openSign =$rownums>0?1:0;
        
		$dataArray[]=array(
							    'type'       =>$sendfloor,
							    'segIndex'   =>$segmentIndex,
							    'checkSign' =>'Feed',
								'tag'        =>'ckGroup',
								'method' =>'segment',
								'Id'         =>$BlWorkShopIds,
								'showArrow'  =>'1',
								'open'       =>$openSign,
								'half'       =>'0',
								'arrowImg'   =>'UpAccessory_blue',
								'tImg'       =>'',//'ws_' . $BlWorkShopId
								'title'      =>array('Text'=>'待补料','Color'=>"$redcolor"),
								'col1'       =>'',
								'col2'       =>array(
												'isAttribute'=>'1',
												'attrDicts'  =>array(
											      array('Text'=>number_format($Qty),'Color'=>"$black",'FontSize'=>"12"),
										          array('Text'=>"(".$Counts. ')','Color'=>"$lightgray",'FontSize'=>"9")
												   )
												)
							   );
							   
			if ($openSign==1){
					$dataArray= array_merge($dataArray,$subArray);
			}
			return $dataArray;
	}
	
	 public function get_subList_order($sendfloor,$WorkShopId,$segmentIndex,$checkSign='KBL')
    {
	    $this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		$this->load->model('WorkShopdataModel');
		$this->load->model('CkllsheetModel');
		$this->load->model('ProductdataModel');
		$this->load->model('staffMainModel');
		
		$qtycolor=$this->colors->get_color('qty');
		$redcolor=$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$factoryCheck=$this->config->item('factory_check');
		 
		$printactions = $this->pageaction->get_actions('print');//操作
		 
		$records = $this->WorkShopdataModel->get_records($WorkShopId);
		$ActionId = $records['ActionId'];
		$semiSign = $records['semiSign'];
		$records = null;
		
	   
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		$this->load->model('CgstocksheetModel');
		
		switch($segmentIndex){
		  case 2:
		       $CompanyId = $WorkShopId;
			   $rowArray = $this->CkllsheetModel->get_outward_ordersheet($CompanyId);
			  break;
		  default:
			  $rowArray = $this->ScSheetModel->get_canstock_list($WorkShopId,$ActionId,$checkSign,'');
			  break;
		}

		$rownums =count($rowArray);
		
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		       $decimals = element('Decimals', $rows, 0);
		    $locksign = 0 ;
		    if($checkSign=='KBL'){
			    $locksign =  $this->CgstocksheetModel->get_check_locksign($rows['POrderId'],$rows['mStockId']);
			  
			    if($locksign>0){
				    continue;
			    }
		    }
		    
		    switch($ActionId){
		       case 101://组装	    
				    $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
				    $shipImg   =$rows['ShipType']>0?'ship' .$rows['ShipType']:''; 
				    $cnameColor=$rows['TestStandard']>1?$this->ProductdataModel->get_cname_color($rows['TestStandard']):$black;
		           
		           if (isset($rows['Operator'])){
		               $Operator=$this->staffMainModel->get_staffname($rows['Operator']);
		           }else{
			           $Operator='';
		           }
		            $actions=array();
					if ($checkSign=='KBL'){
				         $actions  = $this->pageaction->get_actions('occupy');//操作
			         } 
		            $createdtime =  $rows['created'];
		            
		         
		            

				    $dataArray[]=array(
							    'type'       =>$sendfloor,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'zorder',
								'checkSign' =>$checkSign=='KBL'?'KZY':$checkSign,
								'Id'         =>$rows['sPOrderId'],
								'showArrow'  =>'1',
								'open'       =>'0',
								'actions'  =>$actions,
								'arrowImg'   =>'UpAccessory_gray',
								'POrderId'   =>$rows['POrderId'],
								'ProductId'  =>$rows['ProductId'],
								'Picture'    =>$rows['TestStandard']==1?1:0,
								'OrderQty'     =>$rows['Qty'],
							    'productImg' =>$productImg,
							    'shipImg'    =>$shipImg,
							    'line'       =>isset($rows['Line'])?($rows['Line']==''?'':$rows['Line']):'',
								'week'       =>$rows['LeadWeek'],
								'title'      =>array('Text'=>$rows['cName'],'Color'=>"$cnameColor"),
								'created'    =>array('Text'=>$rows['OrderDate'],'DateType'=>'day'),
								'Operator'   =>$Operator,
								'col1'       =>array('Text'=>$rows['Forshort'],'Color'=>"$qtycolor",'light'=>'11'),
								'col2'       =>$rows['OrderPO'],
								'col2Img'    =>'',
								'col3'       =>array('Text'=>number_format($rows['Qty'],$decimals),'Color'=>"$black"),
								'col3Img'    =>'scdj_11',
								'col4'       =>$factoryCheck==1?'':array('Text'=>$createdtime,'DateType'=>'time')
							);
		         break;
		       case 104://开料
						  
					$stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
					
					$printactions[0]['Type']='html';
					$printactions[0]['url']   ='cjgl/slicebom_report.php?FromPage=App&POrderId='.$rows['POrderId'].'&sPOrderId='.$rows['sPOrderId'].'&mStockId=' . $rows['mStockId'];
					$col2color = $rows['cgSign']==1?$redcolor:$black;
					$dataArray[]=array(
								    'type'     =>$sendfloor,
								    'segIndex' =>$segmentIndex,
								    'actions'  =>$printactions,
									'tag'      =>'order',
									'Picture'    =>''.$rows['Picture'],
									'iconImg'    =>$stuffImg,
									'showArrow'=>'1',
									'open'     =>'0',
								   'arrowImg' =>'UpAccessory_gray',
									'Id'       =>$rows['sPOrderId'],
									'mStockId' =>$rows['mStockId'],
									'week'     =>$rows['DeliveryWeek'],
									'title'    =>$rows['StuffCname'],
                                    'created'=>array('subImgs'=> null),
									'col1Img' =>'cut_1',
									'col1'     =>$rows['CutName'],
									'col2'     =>'',
									'col2Img'    =>'',
									'col2'     =>array('Text'=>number_format($rows['Qty'],$decimals),'Color'=>"$col2color"),
									'col2Img'    =>'scdj_11',
									'col4'     =>array('Text'=>$rows['created'],'DateType'=>'time')
							);
						
		         break;
		         
			   default:
			      $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
			      $actions = array();
			      if ($rows['sPOrderId']>0){
						  $printactions[0]['Type']='html';
						  $printactions[0]['url']   ='cjgl/ck_bl_report1.php?FromPage=App&POrderId='.$rows['POrderId'].'&sPOrderId='.$rows['sPOrderId'].'&mStockId=' . $rows['mStockId'];
						  $actions=$printactions;
				  }
					      
			    if ($segmentIndex==2){
						     $dataArray[]=array(
								    'type'     =>$sendfloor,
								    'segIndex' =>$segmentIndex,
								    'actions'  =>$actions,
									'tag'      =>'order',
									'Picture'    =>''.$rows['Picture'],
									'iconImg'    =>$stuffImg,
									'showArrow'=>'1',
									'open'     =>'0',
								   'arrowImg' =>'UpAccessory_gray',
									'Id'       =>$rows['sPOrderId']==0?$rows['mStockId']:$rows['sPOrderId'],
									'mStockId' =>$rows['mStockId'],
									'week'     =>$rows['DeliveryWeek'],
									'title'    =>$rows['StuffCname'],
									'created'  =>array('Text'=>$rows['created'],'DateType'=>'day','subImgs'=> null),
									'col1Img' =>'scdj_11',
									'col1'     =>array('Text'=>number_format($rows['Qty'],$decimals),'Color'=>"$black"),
									'col2'     =>'',
									'col2Img'    =>'',
									'col4'     =>array('Text'=>$rows['bltime'],'DateType'=>'time') 
							);
					}else{
					       $col2color = $rows['cgSign']==1?$redcolor:$black;
						   $dataArray[]=array(
								    'type'     =>$sendfloor,
								    'segIndex' =>$segmentIndex,
								    'actions'  =>$actions,
									'tag'      =>'order',
									'Picture'    =>''.$rows['Picture'],
									'iconImg'    =>$stuffImg,
									'showArrow'=>'1',
									'open'     =>'0',
								   'arrowImg' =>'UpAccessory_gray',
									'Id'       =>$rows['sPOrderId']==0?$rows['mStockId']:$rows['sPOrderId'],
									'mStockId' =>$rows['mStockId'],
									'week'     =>$rows['DeliveryWeek'],
									'title'    =>$rows['StuffCname'],
									'created'  =>array('subImgs'=> null),
									'col1'     =>isset($rows['OrderPO'])?$rows['OrderPO']:'',
									'col2'     =>array('Text'=>number_format($rows['Qty'],$decimals),'Color'=>"$col2color"),
									'col4'     =>array('Text'=>$rows['created'],'DateType'=>'time') 
							);
					}
			     break;  
		    }
	   }
	   	  
        return $dataArray;
    }
    
    public function get_not_feeding_bl($sendfloor,$segmentIndex)
	{
	    $this->load->model('BaseMPositionModel');
	    $this->load->model('CkreplenishModel');
	    $this->load->model('ScSheetModel');
	    $this->load->model('ScCjtjModel');
	     $this->load->model('ProductdataModel');
	    $this->load->model('stuffdataModel');
	    $this->load->model('YwOrderSheetModel');
	    
	    $qtycolor=$this->colors->get_color('qty');
		$red      =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$grayfont =$this->colors->get_color('grayfont');
	    
	    $records = $this->BaseMPositionModel->get_records($sendfloor);
		$BlWorkShopIds = $records['BlWorkShopId'];
		$records = null;
		
		 $actions  = $this->pageaction->get_actions('picking');//操作
		 $actions[0]['Name']='备料';
		 $actions[0]['Action']='feedPicking';
		 
		$this->load->library('dateHandler');
	    $rowArray=$this->CkreplenishModel->get_not_feeding_sheet($BlWorkShopIds,1);
	    $rownums =count($rowArray);
	    
	    $tmp_POrderId='';
	   $dataArray=array();
	   for ($i = 0; $i < $rownums; $i++) {
	         $rows =$rowArray[$i];
	         
	         if ($tmp_POrderId!=$rows['sPOrderId'] && $rows['sPOrderId']!=''){
	                 $tmp_POrderId = $rows['sPOrderId'];
	                 
		             $records = $this->ScSheetModel->get_records($rows['sPOrderId']);
		             
		             $stuffImg=$records['Picture']==1?$this->stuffdataModel->get_stuff_icon($records['StuffId']):'';
		             
		             $lineImgs = null;
		             $productImg = '';
		             if ($records['POrderId']!='' ){
		               if ($records['ActionId']=='101'){
			              $yw_records = $this->YwOrderSheetModel->get_records($records['POrderId']);
			              
			              $productImg=$yw_records['TestStandard']==1?$this->ProductdataModel->get_picture_path($yw_records['ProductId']):'';
			              $OrderPO= $yw_records['OrderPO'];
			              $records['StuffCname'] = $yw_records['cName'];
			              $records['DeliveryWeek'] = $yw_records['Leadweek'];
// 			              $records['OrderQty'] = $yw_records['Qty'];
			              
			              
			              $records['Picture']=$yw_records['TestStandard']==1 ? '1':'';
			              
			              $scQtyRow = $this->ScCjtjModel->get_order_scqty($records['POrderId'], 1);
			              
			              $lineName = $scQtyRow['line'];
			              $lineImgs = array();
			              $lineImgs[]="http://www.ashcloud.com/appapi/web/views/images/line/$lineName"."1.png";
			           }
			           else{
				              $ms_records = $this->ScSheetModel->get_records_mstock($records['mStockId']);
			                   $OrderPO= $ms_records['OrderPO'];   
			                   $productImg = $stuffImg;
			                   $ms_records = null;
			           }       
		             }else{
			                $OrderPO= '特采单';
		             }
		             
		             $remarkInfo = null;
/*
		             if ($rows['Remark']!=''){
						   $created_ct = $this->datehandler->GetDateTimeOutString($rows['created'],'',0);
					       
							
							$remarkInfo = array('content'=>element('Remark',$rows,''),
									'oper'=>$created_ct.' '.element('StaffName',$rows,''),
									'img'=>''
									);
					 }
*/
		             
		              $dataArray[]=array(
							    'type'       =>$sendfloor,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'order',
								'remarkInfo'=>$remarkInfo,
							//	'showArrow'  =>'0',
							//	'open'       =>'0',
								'created'=>array('subImgs'=> $lineImgs,'eachImgFrame'=>'10,7,11.5,11.5'),
								'Id'         =>$records['sPOrderId'],
								'Picture'    =>''.$records['Picture'],
								'iconImg'    =>$productImg,
								'StuffId'      =>$records['StuffId'],
							    'mStockId' =>isset($records['mStockId'])?$records['mStockId']:'',
								'week'     =>$records['DeliveryWeek'],
								'title'       =>$records['StuffCname'],
								'col1Img' =>'',
								'col1'       =>array('Text'=>''.$OrderPO,'Color'=>$black),
								'col2Img'  =>'',
								'col2'        => '',
								'col3Img'  =>'scdj_11',
								'col3'         =>array('Text'=>number_format($records['Qty']),'Color'=>$black) ,
								'wsFmImg' =>'',
							);
					$records = null;
	         }
	         
	          $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
              
			  $dataArray[]=array(
					    'type'       =>$sendfloor,
					    'segIndex'   =>$segmentIndex,
						'tag'        =>'stuff',
						'actions'    =>$actions,
						'Id'         =>$rows['Id'],
						'title'      =>$rows['StuffCname'],
						'col1Img'    =>'wh_tstock',
						'col1'       =>number_format($rows['tStockQty'],$rows['Decimals']),
						'col2Img'    =>'ibh_gray',
						'col2'       =>array('Text'=>number_format($rows['Qty'],$rows['Decimals']),'Color'=>$red) ,
						'col3Img'    =>'ibl_gray',
						'col3'       =>array('Text'=>number_format($rows['llQty'],$rows['Decimals']),'Color'=>$qtycolor) ,
						'Picture'    =>$rows['Picture'],
						'stuffImg'   =>$stuffImg,
						'completeImg'=>'',
						'StockId'    =>$rows['StockId'].'',
						'hideLine' =>$rows['Remark']!=''?1:0,
						'StuffId'    =>$rows['StuffId'].''
					);
					
					if ($rows['Remark']!=''){
						   $created = $this->datehandler->GetDateTimeOutString($rows['created'],'',0);
					       $dataArray[] = array(
												'tag'=>'remark2',
												 'content'=>array('Text'=>$rows['Remark'],'Color'=>$grayfont) ,
												 'oper'     => $created . ' ' . $rows['StaffName'],
												 'img'      =>''
												
							);
					 }

          }

	   return $dataArray;
}
	
	 /** 
	* get_segment_outbl  
	* 外发备料明细
	* 
	* @access public 
	* @param  params $sendfloor，$segmentIndex 一条纪录所需数据
	* @return int 返回各楼层外发备料明细
	*/ 
	function get_segment_outbl($sendfloor,$segmentIndex)
	{
	    $black      =$this->colors->get_color('black');
	    $lightgray  =$this->colors->get_color('lightgray');
	    
		$this->load->model('CkllsheetModel');
		$rowArray=$this->CkllsheetModel->get_outward_comapny();
		$rownums =count($rowArray);
		
		$dataArray=array();
		for ($i = 0; $i < $rownums; $i++) {
				
				    $rows =$rowArray[$i];
				    $subArray = $this->get_subList_order($sendfloor,$rows['CompanyId'],$segmentIndex,'KBL');
						
					 if (count($subArray)>0){
					         $dataArray[]=array(
						    'type'       =>$sendfloor,
						    'segIndex'   =>$segmentIndex,
							'tag'        =>'ckGroup',
							'method' =>'segment',
							'Id'         =>$rows['CompanyId'],
							'showArrow'  =>'1',
							'open'       =>'1',
							'half'       =>'0',
							'tImg'       =>'',
							'arrowImg' =>'UpAccessory_blue',
							'title'      =>array('Text'=>$rows['Forshort'],'Color'=>"$black"),
							'col1'       =>'',
							'col2'       =>array(
											'isAttribute'=>'1',
											'attrDicts'  =>array(
										      array('Text'=>number_format($rows['Qty']),'Color'=>"$black",'FontSize'=>"12"),
									          array('Text'=>"(".$rows['Counts'] . ')','Color'=>"$lightgray",'FontSize'=>"9")
											   )
											)
						);
						
						  $dataArray= array_merge($dataArray,$subArray);
					 }

				}
        return $dataArray;
	}
	
	//配件明细
	public function get_subList_stuff($sPOrderId,$sendfloor,$checkSign,$segmentIndex,$upTag){
	    
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		$this->load->model('CkrksheetModel');
		$this->load->model('CkllsheetModel');
		$this->load->model('CgStuffcomboxModel');
		$this->load->model('StuffPropertyModel');
		$this->load->model('ScPrinttasksModel');
		
		$qtycolor   = $this->colors->get_color('qty');
		$black      = $this->colors->get_color('black');
		$bluefont   = $this->colors->get_color('bluefont');
	    $lightgray  = $this->colors->get_color('lightgray');
	    
	    $printtaskAction  = $this->pageaction->get_actions('printtask');//打印任务
	     
	    $blType = 1; //备料类型
	    $comboxArray=array();
	    
	    if (strlen($sPOrderId)==15){
	           $mStockId = $sPOrderId;  
	           $blType = 4; //外发关联备料
		       $rowArray=$this->CkllsheetModel->get_outward_unitsheet($mStockId);
		       
	    }else{
		       $rowArray=$this->ScSheetModel->get_scorder_stocksheet($sPOrderId,$checkSign);
	    }
	    
	   $comboxArray=array();
	   $rownums =count($rowArray);
		for ($i = 0; $i < $rownums; $i++) {
		      $rows =$rowArray[$i];
		      
		      $S_OrderQty = $rows['OrderQty'];
		      $S_POrderId = $rows['POrderId'];
		      $S_StockId =   $rows['StockId'];
		      $S_sPOrderId=$blType==4?"":$sPOrderId;
		      $S_ActionId  = $blType==4?"":$rows['ActionId'];
		      
		      $stuffArray=$this->CgStuffcomboxModel->get_stuffcombox_list($S_StockId,$S_sPOrderId,$S_OrderQty);
		      
		      $mcounts=count($stuffArray);
		      if ($mcounts>0){
		           for($m=0;$m<$mcounts;$m++)
		           {
			              if ($blType!=4) $stuffArray[$m]['ActionId']= $S_ActionId;
			              $comboxArray[]=$stuffArray[$m];
			        }
		      }else{
			        $comboxArray[]=$rows;
		      }
		}
		
		
		$actions=array();
		switch($checkSign){
			case 'DBL':
			  $actions  = $this->pageaction->get_actions('picking');//操作
		      $actions[0]['Name']='备料';
			  break;
			case 'KBL':
			
			  $actions  = $this->pageaction->get_actions('stock');//备料
			  $actions[0]['Action']='multiBl';
			  break;
		}
		
		if ($S_POrderId!='' && $blType==1){
			 $printStuffIds = $this->ScPrinttasksModel->get_printtask($S_POrderId);
		}else{
			 $printStuffIds=array();
		}
		 
		$mySupplier=explode(',', $this->getSysConfig(106));
		$semiType  =$this->getSysConfig(103); //半成品类型
		$dataArray=array();
		
		$rownums =count($comboxArray);
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$comboxArray[$i];
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
		    $col3color=$rows['OrderQty']==$rows['llQty']?"$qtycolor":"$black";
		    $llEstate=isset($rows['llEstate'])?($rows['llEstate']>0?"★":""):"";
		    
		    $half= 0 ;
		    if (isset($rows['mainType'])){
			      $half=($semiType==$rows['mainType'] && in_array($rows['CompanyId'], $mySupplier))?1:0;
		    }
		    
		    if ($blType!=4){
			    switch($rows['ActionId']){
				      case 101: $blType=1; break;//成品加工备料
				      case 105: $blType=3; break;//外发配件备料
				       default:  $blType=2; break;//加工配件备料
			    }
		    }
           
		    $dblQty =  $rows['OrderQty']-$rows['llQty'];
		    if (count($actions)>0){
			    $actions[0]['Qty']    =''.$dblQty;
			    $actions[0]['StockId']=$rows['StockId'];
		    }
		    
		    $dblSign = 0;
		    $printSign = -1; 
		    if ($blType==1){
		          $dblSign = $checkSign=='DBL'?($rows['llEstate']>0?1:0):($rows['OrderQty']==$rows['llQty']?0:1);
			      $locations  = $this->CkrksheetModel->get_stuff_picklocation($sPOrderId,$rows['StockId']);
			      
			      if (in_array($rows['StuffId'], $printStuffIds)){
				        $printSign = $this->ScPrinttasksModel->check_printtask($sPOrderId);
				        $printtaskAction[0]['Qty']    =round($rows['OrderQty'],$rows['Decimals']);
			      }
			      
		    }else{
			      $locations = $this->CkrksheetModel->get_stuff_locationqty($rows['StuffId'],$dblQty);
			      $dblSign = $dblQty>0?1:0;
		    }
		   
		    $location  = $locations['location'];
		    $workadd = $locations['workadd'];
		    $locdatas  = $locations['data'];
		    
		    $locColor = $lightgray;
		    switch($sendfloor){
			     case  3: $locColor=$workadd==1?$bluefont:$lightgray;break;
			     case 17: $locColor=$workadd==2?$bluefont:$lightgray;break;
			     default: $locColor = $bluefont;
		    }
		    
		    $Property = $this->StuffPropertyModel->get_property($rows['StuffId']);
		    //$locColor  = $rows['SendFloor']==$sendfloor || $sendfloor==0?$bluefont:$lightgray;
			$dataArray[]=array(
			    'type'       =>'',
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'stuff',
				'actions'    =>$rows['blSign']==1 && $segmentIndex>=0 &&  $dblSign==1?$actions:($printSign==0?$printtaskAction:array()),
				'blType'  =>"$blType",
				'half'       =>"$half",
				'Id'         =>$sPOrderId,
				'StockId'    =>$rows['StockId'],
				'title'      =>$rows['StuffId'].'-'.$rows['StuffCname'],
				'location'   =>array('Text'=>$location,'border'=>'1','Color'=>"$locColor",'data'=>$locdatas),
				'col1Img'=>'scdj_11',
				'col1'       =>number_format($rows['OrderQty'],$rows['Decimals']),
				'col2Img'=>'wh_tstock',
				'col2'       =>array('Text'=>number_format($rows['tStockQty'],$rows['Decimals']),'Color'=>"$black"),
				'col3Img'    =>'wh_bp',
				'col3'       =>array('Text'=>$llEstate . number_format($rows['llQty'],$rows['Decimals']),'Color'=>"$col3color"),
				'col4'       =>$rows['Forshort'],
				'completeImg'=>'',
				'Picture'    =>$rows['Picture'],
				'stuffImg'   =>$stuffImg,
				'rightImg' => $printSign==-1?'':($printSign==1?'print_blue':'print_gray'),
				'Property'=>$Property 
			);
			if ($i==$rownums-1){
				    $dataArray[$i]['deleteTag']=$upTag;
			}
		}
        return $dataArray;
	}
	
	public function top_seg()
	{
		$this->load->model('AppUserSetModel');
	    $params   = $this->input->post();
	    $selectType     = element('selectType',$params,'0');
	    $typesArray=$this->get_maintypes();

		$numsOfTypes=count($typesArray); 
		$dataArray  = array();
		
		$totals=0;
		
		$message = 0;
		for ($i = 0; $i < $numsOfTypes; $i++) {
		   $oneTypes=$typesArray[$i];
		   if ($i == $selectType) {
			   $message = $i;
		   }
		   
		   $dataArray[]=array(
				'tag'        =>'stockedlist',
				'method'     =>'segment',
				'hidden'     =>'0',
				'segIndex'   =>"$i",
				'Id'         =>$oneTypes['Id'],
				'title'      =>' ' . $oneTypes['title'],
				'titleImg'   =>$oneTypes['titleImg'],
				'img_0'      =>$oneTypes['titleImg'] . '_gray',
			    'img_1'      =>$oneTypes['titleImg'] . '_blue',
			);
			$totals++;
		}
		 //输出JSON格式数据
		$data['jsondata']=array('status'=>'1','message'=>''.$message,'totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);

	}
	
	//已备料
	public function stockedlist(){
		$params   = $this->input->post();
		$type          = element('top_seg',$params,'');
		$sendfloor = element('top_segId',$params,'0');
		
		$redcolor   = $this->colors->get_color('red');
		$superdarkcolor   = $this->colors->get_color('superdark');
		$grayfontcolor  = $this->colors->get_color('grayfont');
		
		$this->load->model('CkllsheetModel');
		
		$rowArray=$this->CkllsheetModel->get_month_bledcounts($sendfloor);
	    $open = 0;
		
		$rownums =count($rowArray);
		$subdataArray = array();
		for ($i = 0; $i < $rownums; $i++) {
		         $rows    =$rowArray[$i]; 
		         $month = $rows['Month'];
		         $Counts = $rows['Counts'];
		         $twoCounts = $rows['twoCounts'];
		         $oneCounts = $rows['oneCounts']-$twoCounts;
                 
                 $twoPercent = $Counts>0?ceil($twoCounts/$Counts*100):0;
                 
                 $timeMon = strtotime($month);
		
				 $dataArray[]=array(
					    'tag'         =>'yscMon',
					    'method' =>'bleddate',
					    'type'       =>$sendfloor,
					    'segmentIndex'=>'1',
						'Id'                    =>$month,
						'showArrow'    =>'1',
						'data'        =>$subdataArray,
						'open'       =>''.$open,
						'title'         => array(
							   		'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
								   			  'FontSize'=>'14',
								   			  'FontWeight'=>'bold',
								   			  'Color'   =>"$superdarkcolor"),
								   		array('Text'    =>"\n".date('Y', $timeMon),
								   			  'FontSize'=>'9',
								   			  'Color'   =>"$grayfontcolor")
								   		)
							   		),
						'col2'        =>number_format($Counts),
						'col1'        =>array('Text'=>number_format($twoCounts),'Color'=>"$redcolor") ,
						'pieStyle' =>'1',
						 'pieValue'=>array(
										   array('value'=>$Counts-$oneCounts-$twoCounts,'color'=>'#E0E0E0'),
										   array('value'=>$oneCounts,'color'=>'#F5AB47'),
										   array('value'=>$twoCounts,'color'=>"$redcolor")
									      ),
						'percent' =>array(
							   		'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>"$twoPercent",
								   			  'FontSize'=>'33',
								   			  'Color'   =>"$redcolor",
								   			  'FontName'=>'AshCloud61'),
								   		array('Text'    =>'%',
								   			  'FontSize'=>'8',
								   			  'Color'   =>"$redcolor",
								   			  'FontName'=>'AshCloud61')
								   		
								   		)
							   		)
					);
		}
			
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	//备料
	public function bleddate(){
		$params    = $this->input->post();
	    $month= element('Id',$params,'');
		$sendfloor = element('type',$params,'0');
		
		$this->load->model('CkllsheetModel');
		$redcolor   = $this->colors->get_color('red');
		
		$rowArray=$this->CkllsheetModel->get_date_bledcounts($sendfloor,$month);
		$rownums =count($rowArray);

		$dataArray = array();
	
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		     $date       = $rows['Date'];
		    $dateCom = explode('-', substr($date, 5));
		    $weekday = date('w',strtotime($date));
		    
			$dataArray[]=array(
			    'tag'         =>'zzDay',
			    'method'      =>'bleddatelist',
			    'type'        =>$sendfloor,
			    'segmentIndex'=>'1',
				'Id'          =>$date,
				'showArrow'   =>'1',
				'open'        =>'0',
				'title'       =>array('Text'=>substr($date, 5),'Color'=>($weekday==0 ||$weekday==6)?'#ff665f':'#9a9a9a','dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?'#ffa5a0':'#cfcfcf','light'=>'12.5'),
				'col1'        =>array('Text'=>$rows['twoCounts']==0?'':number_format($rows['twoCounts'],0),'Color'=>"$redcolor"),
				'col2'        =>number_format($rows['Counts'],0),
				'marginR' =>'8'
			);
		}

		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	public function bleddatelist(){
		$params    = $this->input->post();
	    $date= element('Id',$params,'');
		$sendfloor = element('type',$params,'0');
		
		$qtycolor   = $this->colors->get_color('qty');
		$black      = $this->colors->get_color('black');
		$bluefont   = $this->colors->get_color('bluefont');
	    $lightgray  = $this->colors->get_color('lightgray');
	    
		$this->load->model('CkllsheetModel');
		$this->load->model('ProductdataModel');
		$this->load->model('stuffdataModel');
		$this->load->library('datehandler');
		
		$redcolor   = $this->colors->get_color('red');
		$factoryCheck=$this->config->item('factory_check');
		$printactions = $this->pageaction->get_actions('print');//操作
		 
		$rowArray=$this->CkllsheetModel->get_date_bledlist($sendfloor,$date);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $ActionId = $rows['ActionId'];
		    switch($ActionId){
		       case 101://组装	    
				    $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
				    $shipImg   =$rows['ShipType']>0?'ship' .$rows['ShipType']:''; 
				    $cnameColor=$rows['TestStandard']>1?$this->ProductdataModel->get_cname_color($rows['TestStandard']):$black;

                    $bltime = $this->datehandler->geDifferDateTimeNum($rows['ableDate'],$rows['Received'],3);
                    
                    $bltime = $bltime<1?1:$bltime;
                    if($bltime>=60*48){
                        $bltime=intval($bltime/60/24);
	                    $blcolor= '#FF0000';
	                    $bltime="...".$bltime."天";
                    }else if ($bltime>=60){
	                    $bltime=intval($bltime/60);
	                    //$blcolor= '#F5AB47';
	                    $blcolor = $bltime>=12?'#F5AB47':$black;
	                    $bltime=$bltime>=24?"...1天": "...".$bltime."时";
                    }else{
	                    $bltime="...".$bltime."分";
	                    $blcolor = $black;
                    }
                    
  
				    $dataArray[]=array(
							    'type'       =>$sendfloor,
								'tag'        =>'zorder',
								'Id'         =>$rows['sPOrderId'],
								'showArrow'  =>'1',
								'open'       =>'0',
								'arrowImg'   =>'UpAccessory_gray',
								'POrderId'   =>$rows['POrderId'],
								'ProductId'  =>$rows['ProductId'],
								'Picture'    =>$rows['TestStandard']==1?1:0,
								'OrderQty'     =>$rows['Qty'],
							    'productImg' =>$productImg,
							    'standard'   =>$rows['TestStandard'].'',
							    'shipImg'    =>$shipImg,
							    'line'       =>isset($rows['Line'])?($rows['Line']==''?'':$rows['Line']):'',
								'week'       =>$rows['LeadWeek'],
								'weekColor' =>"$black",
								'title'      =>array('Text'=>$rows['cName'],'Color'=>"$cnameColor"),
								'created'    =>array('Text'=>$rows['OrderDate'],'DateType'=>'day'),
								'col1'       =>array('Text'=>$rows['Forshort'],'Color'=>"$qtycolor",'light'=>'11'),
								'col2'       =>$rows['OrderPO'],
								'col2Img'    =>'',
								'col3'       =>array('Text'=>number_format($rows['Qty']),'Color'=>"$black"),
								'col3Img'    =>'scdj_11',
								'col4'       =>$factoryCheck==1?'':array('Text'=>$bltime,'Color'=>"$blcolor")
							);
		         break;
		      default:
		          $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
			      $actions = array();
			      if ($rows['sPOrderId']>0){
						  $printactions[0]['Type']='html';
						  $printactions[0]['url']   ='cjgl/ck_bl_report1.php?FromPage=App&POrderId='.$rows['POrderId'].'&sPOrderId='.$rows['sPOrderId'].'&mStockId=' . $rows['mStockId'];
						  $actions=$printactions;
				  }
				  
                    $bltime =$rows['ableDate']==''?1:$this->datehandler->geDifferDateTimeNum($rows['ableDate'],$rows['created'],3);
                    
                    $bltime = $bltime<1?1:$bltime;
                    
                    if($bltime>=60*48){
                        $bltime=intval($bltime/60/24);
	                    $blcolor= '#FF0000';
	                    $bltime="...".$bltime."天";
                    }else if ($bltime>=60){
	                    $bltime=intval($bltime/60);
	                    $blcolor = $bltime>=12?'#F5AB47':$black;
	                    $bltime=$bltime>=24?"...1天": "...".$bltime."时";
                    }else{
	                    $bltime="...".$bltime."分";
	                    $blcolor = $black;
                    }
                    
		          $dataArray[]=array(
								    'type'     =>$sendfloor,
								    'actions'  =>$actions,
									'tag'      =>'order',
									'Picture'    =>''.$rows['Picture'],
									'iconImg'    =>$stuffImg,
									'created'=>array('subImgs'=> null),
									'showArrow'=>'1',
									'open'     =>'0',
								    'arrowImg' =>'UpAccessory_gray',
									'Id'       =>$rows['sPOrderId']==0?$rows['mStockId']:$rows['sPOrderId'],
									'mStockId' =>$rows['mStockId'],
									'week'     =>$rows['DeliveryWeek'],
									'weekColor' =>"$black",
									'title'    =>$rows['StuffCname'],
									'col1Img' =>'scdj_11',
									'col1'     =>number_format($rows['Qty']),
									'col2'     =>'',
									'col2Img'    =>'',
									'col4'     =>$factoryCheck==1?'':array('Text'=>$bltime,'Color'=>"$blcolor") 
							);

		         break;
		       
		    }
		 }
		 
		 $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);   
	}

	
	//生产备料
	public function picking(){
	
		$params    = $this->input->post();
	    $action      = element('Action',$params,'');
	    $sPOrderId= element('Id',$params,'');
	    $StockId    = element('StockId',$params,'');
	    
	    if ($action=='picking'){
		    
		    $this->load->model('CkllsheetModel');
		    $rownums=$this->CkllsheetModel->set_stock_estate($sPOrderId,$StockId);
		    $rowArray=array();
		    
		    if ($rownums>0){
			    $llQty =$this->CkllsheetModel->get_stock_llqty($sPOrderId,$StockId);
			    $green    = $this->colors->get_color('green');
			    $rowArray=array(
			            'col3'      =>array('Text'=>number_format($llQty),'Color'=>"$green"),
			            'actions' =>array()
			      );
		    } 
		    
		      $dataArray=array(
			            'data'     =>$rowArray
			          );
		            
		    $data['jsondata']=array('status'=>$rownums>0?1:0,'message'=>'领料成功!','totals'=>$rownums,'rows'=>$dataArray);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    
		$this->load->view('output_json',$data);
	}
	
	//半成品备料
	function multiBl(){
		 $params     = $this->input->post();
	     $action     = element('Action',$params,'');
	     $sPOrderId  = element('Id',$params,'');
	     $StockId    = element('StockId',$params,'');
	     $Qty        = element('Qty',$params,'');
	     $blType   = element('blType',$params,'2');
	     
	     $state = 0;
	     $dataArray=array();
	     if ($action=='multiBl'){  
		    $this->load->model('CkllsheetModel');
		    //$state = 1;
		    $sPOrderId=$blType==4?'':$sPOrderId;
		    $state=$this->CkllsheetModel->save_multibl($sPOrderId,$StockId,$Qty,$blType);
		    $rowArray=array();
		    
		    if ($state==1){
		         $llEstate=$blType==2?"★":"";
		         $green       = $this->colors->get_color('green');
		         $qtycolor   = $this->colors->get_color('qty');
		         $col3color =  $blType==2?$qtycolor:$green;
		        $rowArray=array(
			            'col3'      =>array('Text'=>$llEstate . $Qty,'Color'=>"$col3color"),
			            'actions' =>array()
			      );
			    $dataArray=array(
			            'data'         =>$rowArray
			          );
		    } 
		            
		    $data['jsondata']=array('status'=>$state,'message'=>$state==1?'备料成功!':'备料失败!','totals'=>$state,'rows'=>$dataArray);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
		$this->load->view('output_json',$data);
	}
	
	//生产备料
	public function feedPicking()
	{
		$params    = $this->input->post();
	    $action      = element('Action',$params,'');
	    $Id= element('Id',$params,'');
	    
	    if ($action=='feedPicking'){
		    
		    $this->load->model('CkllsheetModel');
		    $state=$this->CkllsheetModel->save_feedPicking($Id);
		    $dataArray=array();
		    
		    if ($state==1){
			    $dataArray=array(
			            'Action' =>'delete' 
			          );
		    }
		            
		    $data['jsondata']=array('status'=>$state,'message'=>'领料成功!','totals'=>'1','rows'=>$dataArray);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    
		$this->load->view('output_json',$data);
	}
	
	
	//占用操作
	function occupy()
	{
		 $params      = $this->input->post();
	     $action        = element('Action',$params,'');
	     $sPOrderId  = element('Id',$params,'');
	     
	     $state = 0;
	     $dataArray=array();
	     if ($action=='occupy'){  
		    $this->load->model('CkllsheetModel');
		   
		    $state=$this->CkllsheetModel->save_occupy($sPOrderId);
		    $rowArray=array();
		    
		    if ($state==1){
			    $dataArray=array(
			            'Action' =>'delete' 
			          );
		    } 
		            
		    $data['jsondata']=array('status'=>$state,'message'=>$state==1?'占用成功!':'占用失败!','totals'=>$state,'rows'=>$dataArray);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
		$this->load->view('output_json',$data);
	}
	
	//加入打印任务
	public function printtask(){
		 $params      = $this->input->post();
	     $action        = element('Action',$params,'');
	     $sPOrderId  = element('Id',$params,'');
	     $Qty            = element('Qty',$params,'');
	     
	     $state = 0;
	     $dataArray=array();
	     if ($action=='printtask'){  
		    $this->load->model('ScPrinttasksModel');
		   
		    $state=$this->ScPrinttasksModel->save_records($sPOrderId,$Qty,3);
		    $rowArray=array();
		    
		    if ($state==1){
			        $rowArray=array(
				             'rightImg' =>'print_blue',
				            'actions'     =>array()
				      );
				      
				    $dataArray=array(
				            'data'         =>$rowArray
				      );
		    } 
		            
		    $data['jsondata']=array('status'=>$state,'message'=>$state==1?'添加打印任务成功!':'添加打印任务失败!','totals'=>$state,'rows'=>$dataArray);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
		$this->load->view('output_json',$data);

	}
	
	
		
	
	 /** 
	* get_segment_feed  
	* 获得待补料数量
	* 
	* @access public 
	* @param  params $sendfloor， 一条纪录所需数据
	* @return int 返回待补料数量
	*/ 
	
	function get_segment_feed($sendfloor)
	{
		
		$dataArray=array();
		
	
        
        return $dataArray;
	}
	
    
    /** 
	* get_bl_sublist  
	* 获得备料订单明细
	* 
	* @access public 
	* @param  params $sendfloor， 一条纪录所需数据
	* @return int 返回备料订单明细
	*/ 
	function get_bl_sublist(){
		
		$params       = $this->input->post();
		$upTag        = intval(element('upTag',$params,''));
		$porderid     = element("porderid",$params,'');//订单流水号
		$sporderid    = element("sporderid",$params,'');//生产工单号
		$workshopid   = element('workshopid',$params,'');//生产单位ID
		$stuffid      = element('stuffid',$params,'');
		
		$segmentIndex = intval(element("segmentIndex",$params,-1));
		$listTag='';
	    $dataArray=array();
	       switch ($upTag) {
			case   1: // 47-1F 生产单位订单明细 展开
			         $listTag = 'order';
			         $dataArray=$this->get_subList_semiorder($workshopid);  
			         break;
			case   2: //48-3A ,48-3B,48-1F   待备料配件明细展开
			       
			         $listTag = 'stuff';
			         $fromAction = 'order';
			         $dataArray=$this->get_subList_orderstuff($porderid,$sporderid,$fromAction);  
			         break;
			         
			case  3 :  //47-1F  外发备料配件明细展开
			case  5 :  //47-1F  待备料明配件细展开
			         $listTag = 'stuff';
			         $fromAction = 'semi';
			         $dataArray=$this->get_subList_orderstuff($porderid,$sporderid,$fromAction);  
			         break;
			         
			      break;
			case  4 :  //48-3A ,48-3B,48-1F  关联外发备料配件
			         $listTag = 'stuff';
			         $dataArray=$this->get_subList_outstuff($porderid,$stuffid);  
			         break;
			         
			      break;
		}

		$rownums=count($dataArray);
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
    
    
     /** 
	* get_subList_semiorder  
	* 获得半成品各楼层可备料生产工单明细
	* 
	* @access public 
	* @param  params $workshopid， 一条纪录所需数据
	* @return int 半成品各楼层可备料生产工单明细
	*/ 
     public function get_subList_semiorder($workshopid){
	    
		$this->load->model('CkllsheetModel');
		$rowArray=$this->CkllsheetModel->get_semi_order($workshopid);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		   
			 $dataArray[]=array(
					    'type'        =>$workshopid,
						'tag'         =>'order',
						'upTag'      =>'5',
						'showArrow'   =>'1',
				        'arrowImg'    =>'UpAccessory_blue',
				        'method'     =>'get_bl_sublist',
				        'open'        =>'0',
				        'created'=>array('subImgs'=> null),
				        'porderid'    =>$rows['POrderId'],
				        'sporderid'   =>$rows['sPOrderId'],
						'deliveryweek'=>$rows['DeliveryWeek'],
						'stuffcname'  =>$rows['StuffCname'],
						'orderqty'    =>number_format($rows['Qty'])
					);
		}
        return $dataArray;
	}
	
	//成品 半成品，外发备料明细
	public function get_subList_orderstuff($porderid,$sporderid,$fromAction){
	    $rowArray = array();
		$this->load->model('CkllsheetModel');
		$this->load->model('stuffdataModel');
		if($fromAction=="order"){
			$rowArray=$this->CkllsheetModel->get_order_stuff($porderid,$sporderid);
		}
		if($fromAction=="semi"){
		    $rowArray=$this->CkllsheetModel->get_semi_stuff($porderid,$sporderid);
		}
		$rownums =count($rowArray);
		$dataArray=array();
		$qtycolor=$this->colors->get_color('qty');
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_picture($rows['StuffId']):'';
		    $llSign=$rows['llEstate']>0?'★':'    ';
		    $id=$rows['StockId'];
			$dataArray[]=array(
			    'type'       =>$sporderid,
				'tag'        =>'stuff',
				'Id'         =>$id,
				'title'      =>$rows['StuffCname'],
				'col1'       =>number_format($rows['OrderQty']),
				'col2'       =>number_format($rows['tStockQty']),
				'col3'       =>array('Text'=>$llSign . number_format($rows['llQty']),'Color'=>"$qtycolor"),
				'col3Img'    =>'',
				'Picture'    =>$rows['Picture'],
				'stuffImg'   =>$stuffImg
			);
		}
        return $dataArray;
	}
	
	
	//关联外发订单明细
    public function get_subList_outstuff($porderid,$stuffid){
    
	    $this->load->model('CkllsheetModel');
		$this->load->model('stuffdataModel');
		$rowArray=$this->CkllsheetModel->get_outward_stuff($porderid,$stuffid);
		$rownums =count($rowArray);
		
		$dataArray=array();
	    $qtycolor=$this->colors->get_color('qty');
	    for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_picture($rows['StuffId']):'';
		    $llSign=$rows['llEstate']>0?'★':'    ';
		    $id=$segmentIndex==2?$rows['Id']:$rows['StockId'];
			$dataArray[]=array(
			    'type'       =>$porderid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'stuff',
				'Id'         =>$id,
				'title'      =>$rows['StuffCname'],
				'col1'       =>number_format($rows['OrderQty']),
				'col2'       =>number_format($rows['tStockQty']),
				'col3'       =>array('Text'=>$llSign . number_format($rows['llQty']),'Color'=>"$qtycolor"),
				'col3Img'    =>'',
				'Picture'    =>$rows['Picture'],
				'stuffImg'   =>$stuffImg
			);
		}
	    return $dataArray;
    }
    
    
    //获得各楼层的在库数量
    public function get_tstockqty(){
       
        $params       = $this->input->post();
		$type    = element('type',$params,'');//生产单位ID
    
        $this->load->model('CkllsheetModel');
		$rowArray =$this->CkllsheetModel->get_outstuff_sheet($porderid,$stuffid);
        $red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
        $dataArray[]=array(
					'tag'        =>'tStockQty',
					'type'       =>$type,
					'hidden'     =>'0',
					'qty'     =>array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(												    
							array('Text'=>number_format($dataArray['sumQty']),'Color'=>"$lightgreen",'FontSize'=>"11"),			      					array('Text'=>number_format($dataArray['sumAmount']),'Color'=>"$black",'FontSize'=>"11"),			      					array('Text'=>number_format($dataArray['moreQty3']),'Color'=>"$black",'FontSize'=>"11"),
							array('Text'=>number_format($dataArray['moreQty2']),'Color'=>"$black",'FontSize'=>"11")
									   )
									),
					'data'       =>$listdatas
			  );
    }
    
	//*******************备品转入
	
	public function bp_save() {
		
	   $message='';
	   $this->load->model('ck7bprkModel');
	   $params = $this->input->post();
	   if (element('LoginNumber',$params,'-1') == 11965) {
		  $insert_id = 1; 
	   } else {
		   $insert_id=$this->ck7bprkModel->save_item($params);
	   }
	   
	   $status=$insert_id>0?1:0;
	   
	   $rows = array();
	   if ($insert_id>0) {
		   $rows[]= $this->ck7bprkModel->getPrintDict($params);
	   }
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>$rows);
	   $this->load->view('output_json',$data);
	}


    //*******************报废
	public function bf_save() {
		
	    $message='';	   
	    $this->load->model('ck8bfsheetModel');
	    $this->load->model('ck9stocksheetModel');
	      
	    $params = $this->input->post();
	    $StuffId = element('stuffid', $params, '-1');
	    $num = element('num',  $params, '0');
	    if ($StuffId<0 || $num<=0) {
			$message='表单数据有误';
			}
	    $rows=$this->ck8bfsheetModel->save_item($params);
	    $checkQtyQuery = $this->ck9stocksheetModel->get_item_stuffid($StuffId);
		$oStockQty = $mStockQty = $tStockQty = 0;
		if ($checkQtyQuery->num_rows()>0) {
			$row = $checkQtyQuery->row_array();
			$oStockQty = $row['oStockQty'];
			$mStockQty = $row['mStockQty'];
			$tStockQty = $row['tStockQty'];
		}
			$OP = 'N';
		if (($oStockQty-$mStockQty)>=$num && $tStockQty>=$num) {
				
		} else {
			$message='配件库存不足';
		}
	    $status=$rows>0?1:0;
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array("insert_id"=>"$rows"));
	    $this->load->view('output_json',$data);
	}

	public function bf_edit() {
		
	   $message='';
	   $this->load->model('ck8bfsheetModel');
	   $rows=$this->ck8bfsheetModel->edit_item($this->input->post());
	   $status=$rows>0?1:0;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array("insert_id"=>"$rows"));
	   $this->load->view('output_json',$data);
	
	
	}	
	public function bf_pick_for_cat() {
	   $message='';
	   $this->load->model('ck8bftypeModel');
	   $rows=$this->ck8bftypeModel->get_for_selectcell();
	   $status='1';
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>$rows);
	   $this->load->view('output_json',$data);
	}
	
	public function bf_modify_pick() {
	
		$message='';
	    $this->load->model('ck8bfsheetModel');
	    $editid = element('editid',$this->input->post(),'-1');
	    $rows=$this->ck8bfsheetModel->get_model_pick($editid);
	    $status='1';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>$rows);
	    $this->load->view('output_json',$data);

	}
	
	public function kd_save() {
		 $this->load->model("gysshsheetModel");
		 $params = $this->input->post();
		 $message = $this->gysshsheetModel->kd_save($params);
	     $data['jsondata']=array('status'=>1,
									'message'=>"$message",
									'totals'=>'1',
									'rows'=>array());
		 $this->load->view('output_json',$data);
   }
	
	public function kd_stuffs() {
		 $this->load->model('cg1stocksheetModel');
	     $params = $this->input->post();
		 $CompanyId = element('companyid',$params,0);
		
	     $stuffsList=$this->cg1stocksheetModel->kd_stuffs_in_company($CompanyId);

		$data['jsondata']=array('status'=>1,
								'message'=>'',
								'totals'=>'1',
								'rows'=>$stuffsList);
	    $this->load->view('output_json',$data);
	}
	
	public function kd_provider() {
		$this->load->model('tradeObjectModel');
		$query=$this->tradeObjectModel->get_list_kd();
		$stuffProviderList = array();
		
		if ($query->num_rows() > 0) {
			foreach($query->result_array() as $sqlRow) {
				 $CompanyId = $sqlRow['CompanyId'];
		  $Forshort = $sqlRow['Forshort'];
		  $Letter = $sqlRow['Letter'];
		  $stuffProviderList[] = array("headImage"=>"",
								   "title"    =>"$Letter-$Forshort",
								   "Id"       =>"$CompanyId",
								   "CellType" =>"2",
								   "selected" =>"0",
								   "infos"    =>""
								   ); 
			}
		}
			$data['jsondata']=array('status'=>1,
									'message'=>'',
									'totals'=>'1',
									'rows'=>$stuffProviderList);
	    $this->load->view('output_json',$data);
	}
	
	
}