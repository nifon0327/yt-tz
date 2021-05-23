<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrder_Nosc extends MC_Controller {
/*
	功能:未生产工单
*/
    public $SetTypeId= null;
    public $MenuAction= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 1;
        $this->MenuAction   = $this->pageaction->get_actions('picking_ws');//领料
    }
    
    public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	public function menu()
	{
	     //获取参数
	     $params = $this->input->post();
	     //加载模块
	     $this->load->model('WorkShopdataModel');
	     $this->load->model('ScSheetModel');
	     //调用过程
	     $rows=$this->WorkShopdataModel->get_workshop(1,1);
	     $count = count($rows);
	     for ($i = 0; $i < $count; $i++) {
		     $row = $rows[$i];
		     $overqty =$this->ScSheetModel->get_semi_unscqty($row['Id'],-2);
		     $rows[$i]['subTitle'] = number_format($overqty);
	     }
	     
	     $status=count($rows)>0?1:0;
		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$rows);
		 //输出JSON格式数据
		 $this->load->view('output_json',$data);
	}
	
	public function main()
	{
		$params = $this->input->post();
		
		$versionNum = $this->versionToNumber($this->AppVersion);
		
		$is415Version = $versionNum >= 415 ? true : false;
		
		$types   = element('types',$params,'');
		$top_seg = element('top_seg',$params,'');

		$this->load->model('AppUserSetModel');
		
	    if ($types=='' || $types=='all'){
		    $types=$this->AppUserSetModel->get_parameters($this->LoginNumber,$this->SetTypeId);
	    }
	    else{
		    $this->AppUserSetModel->set_parameters($this->LoginNumber,$this->SetTypeId,$types); 
	    }
       
        $this->load->model('WorkShopdataModel'); 
      
	    if ($types==''){
		    $typesArray=$this->WorkShopdataModel->get_workshop(0,1); 
	    }
	    else{
		    $typesArray=$this->WorkShopdataModel->get_workshop(2,1,$types); 
	    }
	    
	    $numsOfTypes=count($typesArray); 
	    
	    $dataArray  = array();
		$dataArray[]=array('hidden'=>''); 
		
		$this->load->model('staffMainModel'); 
		$this->load->model('StaffWorkStatusModel');
		$this->load->model('ScSheetModel'); 
		$this->load->model('ScCjtjModel');
		
		$lightgray=$this->colors->get_color('lightgray');
		$black    =$this->colors->get_color('black');
		$red      =$this->colors->get_color('red');
		
		$totals=0;
		for ($i = 0; $i < $numsOfTypes; $i++) {
		
		   $oneTypes=$typesArray[$i];
		   if ($oneTypes['selected']==1){
		   
			   $name=$this->staffMainModel->get_staffname($oneTypes['leaderNumber']);
			   $groupnums=$oneTypes['GroupId']==''?0:$this->staffMainModel->get_staffTotals('GroupId',$oneTypes['GroupId']);
			   
			   //$scqty   =$this->ScCjtjModel->get_day_scqty($oneTypes['GroupId']);
			   //$monthqty=$this->ScCjtjModel->get_month_scqty($oneTypes['GroupId']);
			   $overqty =$this->ScSheetModel->get_semi_unscqty($oneTypes['Id'],-2);
			   
			   $average_output =$this->ScCjtjModel->get_day_average($oneTypes['GroupId']);
			   $costAmount = $this->ScSheetModel->get_unsccost($oneTypes['Id']);
			   
			   $scdays = $average_output>0?round($costAmount/$average_output):'0';
			   
			   $unqty=$this->ScSheetModel->get_semi_unscqty($oneTypes['Id'],0);
			   $blqty=$this->ScSheetModel->get_semi_blqty($oneTypes['Id']);
			   $llqty=$this->ScSheetModel->get_semi_llqty($oneTypes['Id']);
			   
			   $listdatas=$this->get_segment_week($oneTypes['Id'],-1);
			   
			   $dataArray[]=array(
					'tag'        =>'nosc',
					'type'       =>$oneTypes['Id'],
					'hidden'     =>'0',
					'segIndex'   =>'-1',
					'method'     =>'segment',
					'name'       =>"$name",
					'number'     =>$oneTypes['leaderNumber'],
					'title'      =>$oneTypes['title'],
					'titleImg'   =>$oneTypes['headImage'],
					'subtitle'   =>$groupnums .'人｜'.$scdays.'天',
					'amount'     =>$is415Version?number_format($overqty).'': array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>number_format($overqty),'Color'=>"$red",'FontSize'=>"20"),
									      array('Text'=>"/" . number_format($unqty),'Color'=>"$lightgray",'FontSize'=>"16")
									   )
									),
					'tj'         =>$is415Version ? number_format($unqty).'' : '',
					'value1'     =>out_format(number_format($unqty),'--'),
					'value2'     =>out_format(number_format($blqty),'--'),
					'value3'     =>out_format(number_format($llqty),'--'),
					'data'       =>$listdatas
			  );
			  $totals++;
		  }
		}
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	public function segment()
	{
		$params       = $this->input->post();
		$segmentIndex = intval( element("segmentIndex",$params,-1));
		$type         = element('type',$params,'');//生产单位ID
		
		$dataArray=array();
		$tag='';
		switch($segmentIndex){
			case -1://本周+逾期
			case  0://未出
			       $tag='week';
			       $dataArray=$this->get_segment_week($type,$segmentIndex);
		           break;
			case  1://待备料
			       $tag='dbl';
			       $dataArray=$this->get_segment_dbl($type,$segmentIndex);
			       break;
			case  2://待领料
			       $tag='dbl';
			       $dataArray=$this->get_segment_dll($type,$segmentIndex);
			       break;
		}
		
	    $rownums  =count($dataArray);
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	function get_segment_week($wsid,$segmentIndex)
	{
		
		$this->load->model('ScSheetModel');
		$rowArray=$this->ScSheetModel->get_semi_weekqty($wsid,$segmentIndex);
		$rownums =count($rowArray);

		$dataArray=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $blqty=$this->ScSheetModel->get_semi_bledqty($wsid,$rows['DeliveryWeek']);
		    $lockqty = $this->ScSheetModel->get_lockedqty($wsid,$rows['DeliveryWeek']);
			$dataArray[]=array(
			    'type'     =>$wsid,
			    'segIndex' =>$segmentIndex,
				'tag'      =>'week',
				'Id'       =>$rows['DeliveryWeek'],
				'showArrow'=>'1',
				'arrowImg' =>'UpAccessory_blue',
				'open'     =>'0',
				'week'     =>$rows['DeliveryWeek'],
				'wtitle'   =>$rows['DeliveryWeek']==0?"交期待定":$this->getWeekToDate($rows['DeliveryWeek']),
				'col2'     =>number_format($rows['Qty']),
				'col2Right'=>'(' . $rows['Counts'] . ')',
				'col2Sub'  =>$blqty>0?number_format($blqty):'',
				'col3'     =>'¥' . number_format($rows['Amount'],0),
				'col3Sub'=>$lockqty > 0 ? number_format($lockqty):'',
				'col3SubImg'=>$lockqty > 0 ? 'ilock_r.png':'',
				'rightTrans'=>'-10',
				'isTotal'  =>"0"
			);
		}
        return $dataArray;
	}
	
	function get_segment_dbl($wsid,$segmentIndex)
	{
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	   
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		$rowArray=$this->ScSheetModel->get_semi_dblsheet($wsid,$actionid);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    switch($actionid){
		       case 104://开料
		          $dataArray[]=array(
					    'type'     =>$wsid,
					    'segIndex' =>$segmentIndex,
						'tag'      =>'dbl',
						'showArrow'=>'1',
				        'arrowImg' =>'UpAccessory_blue',
				        'open'     =>'0',
				        'mStockId' =>$rows['mStockId'],
						'Id'       =>$rows['sPOrderId'],
						'week'     =>$rows['DeliveryWeek'],
						'title'    =>$rows['StuffCname'],
						'topTitle' =>$rows['CutName'],
						'qty'      =>number_format($rows['Qty']),
						'col4'     =>array('Text'=>$rows['created'],'DateType'=>'time') 
					);
		         break;
		         
			   default:
			    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
			   
			     $dataArray[]=array(
				    'type'     =>$wsid,
				    'segIndex' =>$segmentIndex,
					'tag'      =>'order',
					'Picture'    =>''.$rows['Picture'],
					'iconImg'    =>$stuffImg,
					'showArrow'=>'1',
					'open'     =>'0',
				    'arrowImg' =>'UpAccessory_blue',
					'Id'       =>$rows['sPOrderId'],
					'mStockId' =>$rows['mStockId'],
					'week'     =>$rows['DeliveryWeek'],
					'title'    =>$rows['StuffCname'],
					'created'  =>array('Text'=>$rows['created'],'DateType'=>'day'),
					'col1'     =>$rows['OrderPO'],
					'col2'     =>number_format($rows['Qty']),
					'wsFmImg'    =>'',
					'col4'     =>array('Text'=>$rows['created'],'DateType'=>'time') 
				);
			     break;  
		    }
	   }
        
        return $dataArray;
	}
	
	function get_segment_dll($wsid,$segmentIndex)
	{
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	   
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		$rowArray=$this->ScSheetModel->get_semi_dllsheet($wsid,$actionid);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    switch($actionid){
		       case 104://开料
		          $dataArray[]=array(
					    'type'     =>$wsid,
					    'segIndex' =>$segmentIndex,
						'tag'      =>'dbl',
						'showArrow'=>'1',
						'open'     =>'0',
				        'arrowImg' =>'UpAccessory_blue',
						'Id'       =>$rows['sPOrderId'],
						'mStockId' =>$rows['mStockId'],
						'week'     =>$rows['DeliveryWeek'],
						'title'    =>$rows['StuffCname'],
						'topTitle' =>$rows['CutName'],
						'qty'      =>number_format($rows['Qty']),
						'col4'     =>array('Text'=>$rows['created'],'DateType'=>'time')
					);
		         break;
		          
			   default:
				    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
			      $dataArray[]=array(
				    'type'     =>$wsid,
				    'segIndex' =>$segmentIndex,
					'tag'      =>'order',
					'Picture'    =>''.$rows['Picture'],
					'iconImg'    =>$stuffImg,
					'showArrow'=>'1',
					'open'     =>'0',
				    'arrowImg' =>'UpAccessory_blue',
					'Id'       =>$rows['sPOrderId'],
					'mStockId' =>$rows['mStockId'],
					'week'     =>$rows['DeliveryWeek'],
					'title'    =>$rows['StuffCname'],
					'created'  =>array('Text'=>$rows['created'],'DateType'=>'day'),
					'col1'     =>$rows['OrderPO'],
					'col2'     =>number_format($rows['Qty']),
					'wsFmImg'    =>'',
					'col4'     =>array('Text'=>$rows['created'],'DateType'=>'time')
				 );
			     break;  
		    }
	   }
        
        return $dataArray;
	}
	
	
	public function subList(){
	    
	    $params   = $this->input->post();
	    $upTag    = element('upTag',$params,'--');
	    $wsid     = element('type',$params,'');//生产单位ID
	    $id       = element('Id',$params,'');
	    $segmentIndex = intval( element("segmentIndex",$params,-1));
	    
	    $listTag='';
	    $dataArray=array();
	    switch ($upTag) {
			case   'week': 
			         $listTag = 'order';
			         $dataArray=$this->get_subList_order($wsid,$segmentIndex,$id);  
			         break;
			case    'dbl':
			case  'order': 
			         $listTag = 'stuff';
			         $dataArray=$this->get_subList_stuff($wsid,$segmentIndex,$id);  
			         break;
		}
		
		$rownums=count($dataArray);
		if ($rownums>0){
			$dataArray[$rownums-1]["deleteTag"] = $upTag;
		}

		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
		
	}
	
	public function get_subList_order($wsid,$segmentIndex,$dyweek='')
	{   
	    $black      =$this->colors->get_color('black');
	    $red          =$this->colors->get_color('red');
	    
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	    
		$this->load->model('ScSheetModel');
		$this->load->model('YwOrderSheetModel');
		
		$this->load->model('stuffdataModel');
		$this->load->library('datehandler');
		$rowArray=$this->ScSheetModel->get_semi_weeksheet($wsid,$segmentIndex,$dyweek);
		$rownums =count($rowArray);
		$this->load->model('ScGxtjModel');
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $qtycolor = isset($rows['cgSign'])?($rows['cgSign']==1?$red:$black):$black;
		    
		    $processArray=array();

		    // 
		    $stockId=$rows['StockId']; 
		    if ($actionid==102){//皮套加工
			   $stockId=$rows['StockId'];  
			   $this->load->model('ProcessSheetModel');
			   $processArray=$this->ProcessSheetModel->get_sc_processlist($stockId,$rows['sPOrderId']);
		    }
		    $completeImg='';
		    if ($segmentIndex==0 || $segmentIndex==-1){
			    
			    $signed = $this->ScSheetModel->get_ll_info($rows['sPOrderId']);
			    if ($signed != 0) {
				    $test = $signed['CanStock'];
				    if ($test == 3 && $rows['ScQty']<=0)
				    {
					    $completeImg = 'stuff_ybl';
					    if ($this->LoginNumber == 11965) {
						    $completeImg = 'bled_light';
					    }
					    if ($actionid==102 && $this->ScGxtjModel->checkAllGxQty($rows['sPOrderId'])>0) {
						    $completeImg = '';  
					    }
				    }
			    }
			    //$completeImg=($rows['ScFrom']==2 && $rows['ScQty']<=0)?'stuff_ybl':'';
		    }
		   
		      $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
		   
		   
/*
		   if (count($processArray) > 4) {
				    continue;
			    }
			    
*/
			$POrderId = $rows['POrderId'];
			$lockArray = $this->YwOrderSheetModel->check_lock($POrderId, $stockId);
			
			$lock = element('lock',$lockArray,0);
			
			$lockImg = '';
			$remark = '';
			$oper = '';
			$remarkdate = '';
			$remarkIcon = '';
			switch ($lock) {
				case 1:
					$lockImg = 'order_lock';
					$remarkIcon = 'rmk_orderlock';
					$oper    = element('oper',$lockArray,'');
					$remark  = element('remark',$lockArray,'');
					$remarkdate = element('date',$lockArray,'');
				break;
				case 2:
				case 3:
					$lockImg = 'order_lock_s';
					$remarkIcon = 'rmk_stufflock';
					$oper    = element('oper',$lockArray,'');
					$remark  = element('remark',$lockArray,'');
					$remarkdate = element('date',$lockArray,'');
				break;
				default:
				break;
			}
			    
			    
			 $remarkInfo = array();
		    if ($lockImg!='') {
		

			    $created_ct = $this->datehandler->GetDateTimeOutString($remarkdate,"");
				
				$remarkInfo = array('content'=>$remark,
									'oper'=>$created_ct.' '.$oper,
									'img'=>$remarkIcon,
									
									
									);
		    }
			

			
			$dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'order',
				'lockImg'=>$lockImg,
				'lockBeling'=>array('beling'=>$lockImg!=''?'1':'',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									
									),
				'remarkInfo'=>$remarkInfo,
				'Id'         =>$rows['sPOrderId'],
				'showArrow'  =>'1',
				'open'       =>'0',
				'arrowImg'   =>'UpAccessory_gray',
				'Picture'    =>''.$rows['Picture'],
				'iconImg'    =>$stuffImg,
				'week'       =>$rows['DeliveryWeek'],
				'title'      =>$rows['StuffCname'],
				'mStockId'   =>$rows['mStockId'],
				'created'    =>array('Text'=>$rows['created'],'DateType'=>'day'),
				'col1'       =>$rows['OrderPO'],
				'col2'       =>array('Text'=>number_format($rows['Qty']),'Color'=>"$qtycolor"),
				'col3'       =>$segmentIndex<1?($rows['ScQty']>0?number_format($rows['ScQty']):''):'',
				'col4'       =>'¥' . number_format($rows['Price'],4),
				'priceCol4'  =>'1',
				'gprice'     =>'¥'.round($rows['Price'],4),
				'wsFmImg'    =>'pc_'.$wsid,
				'Process'    =>$processArray,
				'completeImg'=>$completeImg,
				'flagBeling'=>array('beling'=>($completeImg!='') ?'1':'',
					'blingVals' =>array(0.6,0.3,0.1,0.3,0.6),
					'belingtime'=>'3'
					),
			);
		}
        return $dataArray;
	}
	
	public function get_subList_stuff($wsid,$segmentIndex,$sPorderId){
	    
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		
		$rowArray=$this->ScSheetModel->get_semi_stocksheet($sPorderId,$segmentIndex);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$actionArray=$segmentIndex==2?$this->MenuAction:array();
		$qtycolor=$this->colors->get_color('qty');
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
		    $llSign=$rows['llEstate']>0?'★':'    ';
		    if ($segmentIndex==2 && $rows['llEstate']==0) continue;
		    $id=$segmentIndex==2?$rows['Id']:$rows['StockId'];
		    
		    
	    	$halfImg = '';
			
			$checkBom = $this->ScSheetModel->semi_bomhead($rows['StockId']);
			if ($checkBom->num_rows() > 0) {
				$halfImg = 'halfProd';
			
			}
		    
		    
			$dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'stuff',
				'Id'         =>$id,
				'actions'    =>$actionArray,
				'title'      =>$rows['StuffCname'],
				'col1'       =>number_format($rows['OrderQty']),
				'col2'       =>number_format($rows['tStockQty']),
				'col3'       =>array('Text'=>$llSign . number_format($rows['llQty']),'Color'=>"$qtycolor"),
				'col3Img'    =>'',
				'Picture'    =>$rows['Picture']."",
				'stuffImg'   =>$stuffImg,
				'StockId'    =>$rows['StockId'].'',
				'halfImg'    =>$halfImg
			);
		}
        return $dataArray;
	}
	
	//领料确认
	public function picking_ws(){
	
		$params   = $this->input->post();
	    $action   = element('Action',$params,'');
	    $id       = element('Id',$params,'');
	    
	    if ($action=='picking_ws' && $this->LoginNumber!='11965'){
		    
		    $this->load->model('CkllsheetModel');
		    
		    $rownums=$this->CkllsheetModel->set_estate($id,'0');
		    
		    $rowArray=array();
		    
		    if ($rownums>0){
			    $records =$this->CkllsheetModel->get_records($id);
			    $rowArray=array(
			            'col3' =>array('Text'=>number_format($records['Qty']))
			          );
		    } 
		    
		    $dataArray=array(
		            'data'         =>$rowArray,
		            'updateObject' =>array(
		                                 array('Key'=>'value2','Value'=>'60','Operator'=>'-')
		                              ),
		            );
		            
		    $data['jsondata']=array('status'=>$rownums>0?1:0,'message'=>'领料成功!','totals'=>$rownums,'rows'=>$dataArray);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    
		$this->load->view('output_json',$data);
	}	
}
