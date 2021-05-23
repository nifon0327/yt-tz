<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrder_Scing extends MC_Controller {
/*
	功能:生产中工单
*/
    public $SetTypeId= null;
    public $MenuAction= null;
    public $PrintAction= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 1;
        $this->MenuAction   = $this->pageaction->get_actions('register,remark');//登记 
        $this->RemarkAction   = $this->pageaction->get_actions('remark');// 
        $this->PrintAction  = $this->pageaction->get_actions('print');  //标签打印
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
		     $overqty =$this->ScSheetModel->get_semi_bledqtyweb_s($row['Id'],'')->qty;
		     $rows[$i]['subTitle'] = array('Text'=>number_format($overqty),'Color'=>'#727171') ;
	     }
	     
	     $status=count($rows)>0?1:0;
		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$rows);
		 //输出JSON格式数据
		 $this->load->view('output_json',$data);
	}
	
	public function main()
	{
		$params = $this->input->post();
		$types   = element('types',$params,'');
		$top_seg = element('top_seg',$params,'');
		
		$versionNum = $this->versionToNumber($this->AppVersion);
		$is415Version = $versionNum >= 415 ? true : false;
        
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
		$this->load->model('CheckinoutModel');
		$this->load->model('CkreplenishModel');
		$this->load->library('dateHandler');
		
		$hourArr = $this->datehandler->get_worktimes();
		$hoursNow = $hourArr[1];
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$superdark      =$this->colors->get_color('superdark');
	    $bluefont      =$this->colors->get_color('bluefont');
	    $grayfont      =$this->colors->get_color('grayfont');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$totals=0;
		
		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');
		// print_r($typesArray);
		for ($i = 0; $i < $numsOfTypes; $i++) {
		
		   $oneTypes=$typesArray[$i];
		   if ($oneTypes['selected']==1){
		   
			   $groupnums=$oneTypes['GroupId']==''?0:$this->staffMainModel->get_checkInNums_ingroup($oneTypes['GroupId']);
			   
			   $month_worktime = $this->CheckinoutModel->get_month_worktimes($oneTypes['GroupId'],'');
			   
			   /*
				   
			   */
			  // $groupnumsMon=$oneTypes['GroupId']==''?0:$this->staffMainModel->mon_checkInNums_ingroup($oneTypes['GroupId'],date('Y-m'));
			   
			   $dayqty   =$this->ScCjtjModel->get_day_scqty($oneTypes['Id']);
			  // $bledqty  =$this->ScSheetModel->get_semi_bledqty($oneTypes['Id'],'');
			   $overqty  =$this->ScSheetModel->get_semi_bledqty($oneTypes['Id'],'current');
			   
			   $day_output   =$this->ScCjtjModel->get_workshop_day_output($oneTypes['Id']);
			   $month_output =$this->ScCjtjModel->get_workshop_month_output($oneTypes['Id']);
			   
			   $average_output =$this->ScCjtjModel->get_day_average($oneTypes['GroupId']);
			   
			   $scrows   =$this->ScSheetModel->get_semi_bledqtyweb_s($oneTypes['Id'],'');
			   $bledqty     = $scrows->qty;
			   $bled_output = $scrows->amount;
			   
			   $scdays = $average_output>0?round($bled_output/$average_output):'0';
			   
			   
			   $day_valuation=$groupnums*$laborCost*$worktime;
			   $newDay = $hoursNow *$groupnums*$laborCost;
			   
			   $day_color=$day_output>=$day_valuation?$lightgreen:$red;
			   
			   //$month_valuation=$day_valuation*25;//需更改
			   $month_valuation=$this->ScCjtjModel->get_month_valuation($oneTypes['GroupId']);
			   
			   
			   $newMonth = $month_worktime*$laborCost+$newDay;
			   
			   
			   $newDay = $newDay > $day_valuation? $day_valuation:$newDay;
			   
			   //$day_valuation  =$this->ScCjtjModel->get_day_valuation($oneTypes['GroupId']);//日估值
			   //$month_valuation=$this->ScCjtjModel->get_month_valuation($oneTypes['GroupId']);;//月估值
			   
			   $percent=$day_valuation>0?round($day_output*100/$day_valuation):100;
			   
			   $feedrows = $this->CkreplenishModel->get_not_feedings($oneTypes['Id']);
			   if ($feedrows['Counts']>0){
				   $feedVal = array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>number_format($feedrows['Qty']),'Color'=>"$grayfont",'FontSize'=>"12"),
									      array('Text'=>"(" . number_format($feedrows['Counts']) . ")",'Color'=>"$grayfont",'FontSize'=>"8")
									   )
									);
			   }else{
				    $feedVal='--';
			   }
			   
			   $listdatas=$this->get_subList_order($oneTypes['Id'],-1);
			   //"dayTitle",@"monTitle",@"dayFix",@"monFix"]
			 
			   $sleepRabit = '';
			   $scale = '1';
			   $phidden = '0';
			   if ($day_output <= 0 && $newDay <= 0) {
				   

					$sleepRabit = 'sleepRabit';
					$scale = '1.2';
					$phidden = '1';

			   }
			   $dayDict = array(
				   'darkVal'    =>$day_valuation>0?''.($day_output/ $day_valuation):($day_output>0?'100':'0'),
				   'pointVal'   =>$day_valuation>0?''.($newDay / $day_valuation):'0',
				   'title'      =>'今日',
				   'pHidden'    =>$phidden,
				   'sleep'      =>$sleepRabit,
				   'scale'      =>$scale,
				   'fix'        =>'%',
				   'percent'    =>$newDay>0?''.round(($day_output-$newDay)/$newDay*100):($day_output>0?'100':'0')
			   );
			   //$newMonth = $groupnumsMon *$laborCost*$worktime;
			   
			//   $newMonth = $newMonth > $month_valuation ? $month_valuation:$newMonth;

			   $sleepRabit = '';
			   $scale = '1';
			   $phidden = '0';
			   if ($month_output <= 0 && $newMonth<=0) {
				   

					$sleepRabit = 'sleepRabit';
					$scale = '1.2';
					$phidden = '1';

			   }
			   $monDict = array(
				   'darkVal'    =>$month_valuation>0?''.($month_output/$month_valuation):($month_output>0?'0':'0'),
				   'pointVal'   =>$month_valuation>0?''.($newMonth/$month_valuation):'0',
				   'title'      =>'本月',
				   'pHidden'    =>$phidden,
				   'sleep'      =>$sleepRabit,
				   'scale'      =>$scale,
				   'fix'        =>'%',
				   'percent'    =>$newMonth>0?''.round(($month_output-$newMonth)/$newMonth*100):($month_output>0?'0':'0')
			   );
			   
			   if ($is415Version) {
				    $dataArray[]=array(
					'tag'        =>'scing',
					'type'       =>''.$oneTypes['Id'],
					'hidden'     =>'0',
					'segIndex'   =>'-1',
					'method'     =>'segment',
					'dayDict'    =>$dayDict,
					'monDict'    =>$monDict,
					'trigger'    =>array(
						'3'=>array('title'=>'今日生产','api'=>'todaysc'),
						'4'=>array('title'=>'已生产订单','api'=>'ysc')
					),
					'number'     =>$oneTypes['leaderNumber'],
					'title'      =>
						array(
							'isAttribute'=>'1',
							'attrDicts'  =>array(
									   array(
									   'Text'=>''.$oneTypes['title'],
									   'Color'=>$superdark,
									   'FontSize'=>"14"),
									   array(
									   'Text'=>'  '.$groupnums .'人｜'.$scdays.'天',
									   'Color'=>$grayfont,
									   'FontSize'=>"11")
									   )
							 ),
					'titleImg'   =>$oneTypes['headImage'],
					'subtitle'   =>$groupnums .'人｜'.$scdays.'天',
					'amount'     =>''.number_format($dayqty),
					'czmon'=>$month_output,
					'czmon_v'=>$newMonth,
					'value1'     =>out_format(number_format($bledqty),'--'),
					'value2'     =>out_format(number_format($overqty),'--'),
					'value3'     =>$feedVal,//要改为补料
					'data'       =>$listdatas
			  );

			   } else {
				    $dataArray[]=array(
					'tag'        =>'scing',
					'type'       =>$oneTypes['Id'],
					'hidden'     =>'0',
					'segIndex'   =>'-1',
					'method'     =>'segment',
					'dayDict'    =>$dayDict,
					'monDict'    =>$monDict,
					'number'     =>$oneTypes['leaderNumber'],
					'title'      =>
						$is415Version ? 
						array(
							'isAttribute'=>'1',
							'attrDicts'  =>array(
									   array(
									   'Text'=>''.$oneTypes['title'],
									   'Color'=>$superdark,
									   'FontSize'=>"14"),
									   array(
									   'Text'=>'  '.$groupnums .'人｜'.$scdays.'天',
									   'Color'=>$grayfont,
									   'FontSize'=>"11")
									   )
							 )
						:
						$oneTypes['title'],
					'titleImg'   =>$oneTypes['headImage'],
					'subtitle'   =>$groupnums .'人｜0天',
					'amount'     =>
					$is415Version ? 
								array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>number_format($dayqty),'Color'=>"$qtycolor",'FontSize'=>"24")
									   )
									)
									:
								array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>number_format($dayqty),'Color'=>"$qtycolor",'FontSize'=>"24"),
									      array('Text'=>"/" . number_format($bledqty),'Color'=>"$black",'FontSize'=>"11")
									   )
									),
					"dayValue"  =>array(
					                'isAttribute'=>'1',
									'attrDicts'  =>array(
									   array('Text'=>"¥" . number_format($day_output),'Color'=>"$day_color",'FontSize'=>"10"),
									   array('Text'=>"/¥" . number_format($day_valuation),'Color'=>"$lightgray",'FontSize'=>"10")
									   )
									),				
				    "monthValue"=>array(
					                    'isAttribute'=>'1',
										'attrDicts'  =>array(
										 array('Text'=>"¥" . number_format($month_output),'Color'=>"$red",'FontSize'=>"10"),
										 array('Text'=>"/¥" . number_format($month_valuation),'Color'=>"$lightgray",'FontSize'=>"10")
										)
									),			
					"chartValue" =>array(
										array("$percent","$lightgreen","27","","regular"),
										array("%","$lightgreen","10")
									),
					"pieValue"=>
						array(
							array("value"=>"100","color"=>"#459fd1"),
							array("value"=>"0","color"=>"clear")
						),
					"pieValue2"=>
						array(
							array("value"=>"$percent","color"=>"#00cc00"),
							array("value"=>"".(200-$percent),"color"=>"clear")
						),
					'value1'     =>out_format(number_format($bledqty),'--'),
					'value2'     =>out_format(number_format($overqty),'--'),
					'value3'     =>$feedVal,//要改为补料
					'data'       =>$listdatas
			  );

			   }
			   
			  			  $totals++;
			 
		  }
		}
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	function get_scing_segmentindex($segmentIndex)
	{
	    $segIndex=$segmentIndex;
		switch($segmentIndex){
		   case -1: $segIndex= 20;break;
		   case  0: $segIndex= 21;break;
		   case  1: $segIndex= 22;break;
		   case  2: $segIndex= 23;break;  
		}
		return $segIndex;
	}
	
	
	public function segment()
	{
		$params       = $this->input->post();
		$segmentIndex = intval( element("segmentIndex",$params,-1));
		$type         = element('type',$params,'');//生产单位ID
		
		$dataArray=array();
		$tag='';
		switch($segmentIndex){
			case -1://当前生产单
			case  1://本周+逾期
			       $tag='dbl';
			       $dataArray=$this->get_subList_order($type,$segmentIndex);
		          break;
		    case  0://全部生产单
		          $tag='week';
			      $dataArray=$this->get_segment_week($type,$segmentIndex);
		          break;
			case  2://待补料单
			       $tag='dbl';
			       $dataArray = $this->get_segment_feeding($type,$segmentIndex);
			       //$bledArray = $this->get_feeding_bled($type,$segmentIndex);
			       break;
		}
		
	    $rownums  =count($dataArray);
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	function get_segment_week($wsid,$segmentIndex)
	{
		
		$this->load->model('ScSheetModel');
		
		$segIndex=$this->get_scing_segmentindex($segmentIndex);
		$rowArray=$this->ScSheetModel->get_semi_weekqty_s($wsid,$segIndex);
		$rownums =count($rowArray);

		$dataArray=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    //$blqty=$this->ScSheetModel->get_semi_bledqty($wsid,$rows['DeliveryWeek']);
		    
			$dataArray[]=array(
			    'type'     =>$wsid,
			    'segIndex' =>$segmentIndex,
				'tag'      =>'week',
				'Id'       =>$rows['DeliveryWeek'],
				'showArrow'=>'1',
				'arrowImg' =>'UpAccessory_blue',
				'open'     =>'0',
				'week'     =>$rows['DeliveryWeek'],
				'wtitle'   =>$this->getWeekToDate($rows['DeliveryWeek']),
				'col2'     =>number_format($rows['Qty']),
				'col2Right'=>'(' . $rows['Counts'] . ')',
				'col2Sub'  =>'',
				'col3'     =>'¥' . number_format($rows['Amount'],0),
				'isTotal'  =>"0"
			);
		}
        return $dataArray;
	}
	
	function get_segment_feeding($wsid,$segmentIndex)
	{
		
	    
	   $this->load->model('stuffdataModel');
	   $this->load->model('ScSheetModel');
	   $this->load->model('CkreplenishModel');
	   $this->load->model('ScSheetModel');
	   $this->load->model('ScCjtjModel');
	   
	   $this->load->model('YwOrderSheetModel');
	   $this->load->model('StaffMainModel');
	   $dataArray = array();
	   $this->load->library('dateHandler');
	   	
	    $affirmAction  = $this->pageaction->get_actions('affirm');  //确认
	    $affirmAction[0]['Name']='完成';
	     
	   	$deleteAction = $this->pageaction->get_actions('delete');  //删除
	   	$deleteAction[0]['Action']='delete_feed'; 
	   	
	   $rowArray=$this->CkreplenishModel->get_not_feeding_sheet($wsid);
	   $rownums =count($rowArray);
	   
	   $qtycolor=$this->colors->get_color('qty');
		$red      =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$grayfont =$this->colors->get_color('grayfont');
		
	   $actions=array();
	   $tmp_POrderId='';
	   
	   for ($i = 0; $i < $rownums; $i++) {
	         $rows =$rowArray[$i];
	         
	         if ($tmp_POrderId!=$rows['sPOrderId'] && $rows['sPOrderId']!=''){
	                 $tmp_POrderId = $rows['sPOrderId'];
	                 
		             $records = $this->ScSheetModel->get_records($rows['sPOrderId']);
		             
		             $stuffImg=$records['Picture']==1?$this->stuffdataModel->get_stuff_icon($records['StuffId']):'';
		             
		             /*
		             if ($records['POrderId']!=''){
			              $yw_records = $this->YwOrderSheetModel->get_records($records['POrderId']);
			              $records['StuffCname'] = $yw_records['cName'];
			              $records['DeliveryWeek'] = $yw_records['Leadweek'];
// 			              $records['OrderQty'] = $yw_records['Qty'];
			              
			              
			              $scQtyRow = $this->ScCjtjModel->get_order_scqty($records['POrderId'], 1);
			              
			              $lineName = $scQtyRow['line'];
			              $OrderPO= $yw_records['OrderPO'];
		             }else{
			              $OrderPO= '特采单';
		             }
		             */
		             $ms_records = $this->ScSheetModel->get_records_mstock($records['mStockId']);
		             $ms_stuffImg=$ms_records['Picture']==1?$this->stuffdataModel->get_stuff_icon($ms_records['StuffId']):'';
			         $OrderPO= $ms_records['OrderPO'];

		             
		              $dataArray[]=array(
							    'type'       =>$wsid,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'order',
								'Id'         =>$records['sPOrderId'],
								'Picture'    =>''.$ms_records['Picture'],
								'iconImg'    =>$ms_stuffImg,
								'StuffId'      =>$ms_records['StuffId'],
							    'mStockId' =>isset($records['mStockId'])?$records['mStockId']:'',
								'week'     =>$ms_records['DeliveryWeek'],
								'title'       =>$ms_records['StuffCname'],
								'col1Img' =>'',
								'col1'       =>'' . $OrderPO,
								'col2Img'  =>'',
								'col2'        => '',
								'col3Img'  =>'scdj_11',
								'col3'         =>array('Text'=>number_format($records['Qty']),'Color'=>$black) ,
								'wsFmImg' =>'',
							);
					$records = null;
	         }
	          $actions = array();
	          $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
			 
			  $rtopImg = '';
			   switch($rows['Estate']){
			        case 1:   $rtopImg ='audited_1'; break;
				    case 2:   $rtopImg ='audited_2';  $actions =$deleteAction; break;
				    case 3:   $rtopImg ='audited_3'; break;
				    default: $rtopImg =''; break;
			   }
			  
              $qtycolor = $rows['llQty']==$rows['Qty']?$qtycolor:$black;
              $actions=$rows['llQty']==$rows['Qty']?$affirmAction:$actions;
              
			  $dataArray[]=array(
					    'type'       =>$wsid,
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
						'StuffId'    =>$rows['StuffId'].'',
						'rtopImg'  =>$rtopImg,
						'hideLine' =>$rows['Remark']!=''?1:0
					);
					
					 if ($rows['Estate']==3){
						       $auditTime = $this->datehandler->GetDateTimeOutString($rows['AuditTime'],'',0);
						        $auditor     = $this->StaffMainModel->get_staffname($rows['Auditor']);
					          $dataArray[] = array(
												'tag'=>'remark2',
												 'content'=>array('Text'=>$rows['ReturnReasons'],'Color'=>$red) ,
												 'oper'     => $auditTime . ' ' . $auditor,
												 'img'      =>'audited_3',
												'hideLine' =>$rows['Remark']!=''?1:0
							   );
					 }
						   
					if ($rows['Remark']!=''){
						   $created = $this->datehandler->GetDateTimeOutString($rows['created'],'',0);
					       $dataArray[] = array(
												'tag'=>'remark2',
												 'content'=>array('Text'=>$rows['Remark'],'Color'=>$grayfont) ,
												 'oper'     => $created . ' ' . $rows['StaffName'],
												 'img'      =>''
												
							);
					 }
					    
					//remark2
          }

// 	    if ($this->LoginNumber == 11965) {

			  $dataArray = array_merge($dataArray, $this->feeding_months($wsid, $segmentIndex));
// 		  }
	   return $dataArray;
	}
	
	
	function get_date_feeding($wsid, $segmentIndex, $date)
	{
	   $this->load->model('stuffdataModel');
	   $this->load->model('ScSheetModel');
	   $this->load->model('CkreplenishModel');
	   $this->load->model('ScSheetModel');
	   $this->load->model('YwOrderSheetModel');
	   $this->load->model('StaffMainModel');
	   $this->load->model('ProductdataModel');
	   
	   $this->load->library('dateHandler');
	   	
	    $affirmAction  = $this->pageaction->get_actions('affirm');  //确认
	    $affirmAction[0]['Name']='完成';
	     
	   	$deleteAction = $this->pageaction->get_actions('delete');  //删除
	   	$deleteAction[0]['Action']='delete_feed'; 
	   	
	   $rowArray=$this->CkreplenishModel->get_date_sheet($wsid, $date);
	   $rownums =count($rowArray);
	   
	   $qtycolor=$this->colors->get_color('qty');
		$red      =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$grayfont =$this->colors->get_color('grayfont');
		
	   $actions=array();
	   $tmp_POrderId='';
	   
	   for ($i = 0; $i < $rownums; $i++) {
	         $rows =$rowArray[$i];
	         
	         if ($tmp_POrderId!=$rows['sPOrderId'] && $rows['sPOrderId']!=''){
	                 $tmp_POrderId = $rows['sPOrderId'];
	                 
		             $records = $this->ScSheetModel->get_records($rows['sPOrderId']);
		             
		             $ms_records = $this->ScSheetModel->get_records_mstock($records['mStockId']);
		             $ms_stuffImg=$ms_records['Picture']==1?$this->stuffdataModel->get_stuff_icon($ms_records['StuffId']):'';
			         $OrderPO= $ms_records['OrderPO'];
			        
			        $dataArray[]=array(
							    'type'       =>$wsid,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'order',
								'Id'         =>$records['sPOrderId'],
								'Picture'  =>''.$ms_records['Picture'],
							   'iconImg'    =>$ms_stuffImg,
							   'mStockId'    =>$records['mStockId'].'',
								'StuffId'    =>$ms_records['StuffId'].'',
								'week'     => $ms_records['DeliveryWeek'],
								'title'       => $ms_records['StuffCname'],
								'col1Img' =>'',
								'col1'       =>'' . $OrderPO,
								'col2Img'  =>'',
								'col2'        => '',
								'col3Img'  =>'scdj_11',
								'col3'         =>array('Text'=>number_format($records['Qty']),'Color'=>$black) ,
								'wsFmImg' =>'',
							);
							
		             //$yw_records = $this->YwOrderSheetModel->get_records($records['POrderId']);
		              //$productImg=$yw_records['TestStandard']==1?$this->ProductdataModel->get_picture_path($yw_records['ProductId']):'';
			        // $OrderPO= $yw_records['OrderPO'];
		            /*
		              $dataArray[]=array(
							    'type'       =>$wsid,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'order',
								'showArrow'  =>'0',
								'open'       =>'0',
								'Id'         =>$records['sPOrderId'],
								'TestStandard'  =>''.$yw_records['TestStandard'],
								 'productImg' =>$productImg,
								//'iconImg'    =>$stuffImg,
								'ProductId' =>$yw_records['ProductId'],
								'week'     =>$yw_records['Leadweek'],
								'title'       =>$yw_records['cName'],
								'col1Img' =>'',
								'col1'       =>'' . $OrderPO,
								'col2Img'  =>'',
								'col2'        => '',
								'col3Img'  =>'scdj_11',
								'col3'         =>array('Text'=>number_format($records['Qty']),'Color'=>$black) ,
								'wsFmImg' =>'',
							);
					 */
					$records = null;
	         }
	          $actions = array();
	          $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
			 
			  $rtopImg = '';
			   switch($rows['Estate']){
			        case 1:   $rtopImg ='audited_1'; break;
				    case 2:   $rtopImg ='audited_2';  $actions =$deleteAction; break;
				    case 3:   $rtopImg ='audited_3'; break;
				    default: $rtopImg =''; break;
			   }
			  
              $qtycolor = $rows['llQty']==$rows['Qty']?$qtycolor:$black;
              $actions=$rows['llQty']==$rows['Qty']?$affirmAction:$actions;
              
			  $dataArray[]=array(
					    'type'       =>$wsid,
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
						'StuffId'    =>$rows['StuffId'].'',
						'rtopImg'  =>$rtopImg,
						'hideLine' =>$rows['Remark']!=''?1:0
					);
					
					 if ($rows['Estate']==3){
						       $auditTime = $this->datehandler->GetDateTimeOutString($rows['AuditTime'],'',0);
						        $auditor     = $this->StaffMainModel->get_staffname($rows['Auditor']);
					          $dataArray[] = array(
												'tag'=>'remark2',
												 'content'=>array('Text'=>$rows['ReturnReasons'],'Color'=>$red) ,
												 'oper'     => $auditTime . ' ' . $auditor,
												 'img'      =>'audited_3',
												'hideLine' =>$rows['Remark']!=''?1:0
							   );
					 } else if ($rows['Remark']!=''){
						   $created = $this->datehandler->GetDateTimeOutString($rows['created'],'',0);
					       $dataArray[] = array(
												'tag'=>'remark2',
												 'content'=>array('Text'=>$rows['Remark'],'Color'=>$grayfont) ,
												 'oper'     => $created . ' ' . $rows['StaffName'],
												 'img'      =>''
												
							);
					 }
					    
					//remark2
          }


		  
	   return $dataArray;
	}

	
	
	function feeding_month_dates($wsid,$month, $segmentIndex='-1') {
		
		
	    $this->load->model('CkreplenishModel');
	   
	   
	    $this->load->library('dateHandler');
	    
     	$dataArray = array();
        $rowArray=$this->CkreplenishModel->get_month_dates($wsid,$month);
	    $rownums =count($rowArray);

	    for ($i = 0; $i < $rownums; $i++) {
	         
	        $rows =$rowArray[$i];
	        $date = $rows['Date'];
	        $weekday = date('w',strtotime($date));
			$istoday = $date==$this->Date ? true : false;
		    
		    
		    $dateCom = explode('-', substr($date, 5));
		    $titleAttr = array('Text'=>substr($date, 5),'Color'=>($weekday==0 ||$weekday==6)?'#ff665f':'#9a9a9a','dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?'#ffa5a0':'#cfcfcf','light'=>'12.5','dateBg'=> $istoday?'#ffff2e':'');
						   		
						   		
			$col3Attr = array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>number_format($rows['Qty']),
							   			  'FontSize'=>'12',
							   			  'Color'   =>"#3b3e41"),
							   		array('Text'    =>'('. $rows['Counts'] .')',
							   			  'FontSize'=>'9',
							   			  'Color'   =>"#727171")
							   		)
						   		);
	        $dataArray[]= array(
		        'tag'=>'zzDay',
		        'Id'=>$date,
		        'showArrow'=>'1',
		        'open'=>'',
		        'type'=>$wsid,
		        'segIndex' =>$segmentIndex,
		        'title'=>$titleAttr,
		        'marginR'=>'8',
		        'col2'=>$col3Attr
	        );
	        
	        
	    }
	    return $dataArray;
		
	}

	
	
	function feeding_months($wsid, $segmentIndex='-1') {
		
		
	    $this->load->model('CkreplenishModel');
	   
	   
	    $this->load->library('dateHandler');
	    
     	$dataArray = array();
        $rowArray=$this->CkreplenishModel->get_month_feedings($wsid);
	    $rownums =count($rowArray);
	    $curMonth = date('Y-m');
	    for ($i = 0; $i < $rownums; $i++) {
	         
	        $rows =$rowArray[$i];
	        $month = $rows['Month'];
	        
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
						   		
						   		
			$col3Attr = array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>number_format($rows['Qty']),
							   			  'FontSize'=>'12',
							   			  'Color'   =>"#3b3e41"),
							   		array('Text'    =>'('. $rows['Counts'] .')',
							   			  'FontSize'=>'9',
							   			  'Color'   =>"#727171")
							   		)
						   		);
			$opened = 0;
			if ($curMonth == $month) {
				$opened = 1;
			}
	        $dataArray[]= array(
		        'tag'=>'wtotal',
		        'Id'=>$month,
		        'showArrow'=>'1',
		        'open'=>$opened,
		        'type'=>$wsid,
		        'segIndex' =>$segmentIndex,
		        'col1'=>$titleAttr,
		        'col3'=>$col3Attr
	        );
	        if ($opened == 1) {
		        $dataArray = array_merge($dataArray, $this->feeding_month_dates($wsid, $month, $segmentIndex));
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
			case 'wtotal':
			 $dataArray=$this->feeding_month_dates($wsid, $id, $segmentIndex);
			break;
		
			case 'zzDay':
			 $dataArray=$this->get_date_feeding($wsid, $segmentIndex, $id) ;
			break;
		}
		
		$rownums=count($dataArray);
		if ($rownums>0){
			$dataArray[$rownums-1]["deleteTag"] = $upTag;
		}

		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
		
	}
	
	public function get_subList_order($wsid,$segmentIndex,$dyweek=''){
	
	    $black      =$this->colors->get_color('black');
	    $red          =$this->colors->get_color('red');
	    $orange     =$this->colors->get_color('orange');
		$versionNum = $this->versionToNumber($this->AppVersion);
		$is415Version = $versionNum >= 415 ? true : false;
        
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	    
		$this->load->model('ScSheetModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('ScGxtjModel');
		$this->load->library('dateHandler');
		
		$min30 = 60 *30;
		$hour12 = $min30*2 *4*3;
		$twoDay = $hour12 * 4;
		

		$segIndex=$this->get_scing_segmentindex($segmentIndex);
		
		$nowdateTime = strtotime($this->DateTime) ;
		
		if ($segmentIndex==-1 || $segmentIndex==1){
		
			$rowArray=$this->ScSheetModel->get_semi_bledsheet($wsid,$actionid,$segIndex);
		}else{
		    $rowArray=$this->ScSheetModel->get_semi_weeksheet($wsid,$segIndex,$dyweek);
		}
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$this->load->model('StuffdataModel');
		$this->load->model('StaffMainModel');
		$this->load->model('ScgxRemarkModel');
        $this->load->model('ProcessSheetModel');
         
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $scqty = $rows['ScQty'];
		    $Qty = $rows['Qty'];
		    
		    $beling = '';
		    
		    $qtycolor = isset($rows['cgSign'])?($rows['cgSign']==1?$red:$black):$black;
		    
		    $processArray=array();
		    $actions=array();
			//$actions=$this->RemarkAction;
		    $actions=$this->MenuAction;
		    $scedInterval = '';
		    $minus = 0;
		    $sPorderId = $rows['sPOrderId'];
		    
		    if ($actionid==102){//皮套加工
			   $stockId=$rows['StockId'];  
			  
			   $processArray=$this->ProcessSheetModel->get_sc_processlist($stockId,$sPorderId,1);
			   $actions=array();
			   $actions=$this->RemarkAction;
			   $beginTime = strtotime($this->ScGxtjModel->get_begin_time($sPorderId));
			   if ($beginTime != '') {
			   		$lastTime = $scqty >= $Qty ? strtotime($this->ScGxtjModel->get_last_time($sPorderId)) : $nowdateTime;
			   		
			   		$minus = floor($lastTime-$beginTime);
			   		$scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
			   		
			   }
			   
		    }else{
		      // $actions[0]['MaxQty']= $rows['Qty']-$rows['ScQty']; 
		      // $actions[0]['DjQty'] = $rows['ScQty'];
		      
		      $beling = '';
		      $beginTime = strtotime($this->ScCjtjModel->get_begin_time($sPorderId));
		        if ($beginTime != '') {
			   		$lastTime = $scqty >= $Qty ? strtotime($this->ScCjtjModel->get_last_time($sPorderId)) : $nowdateTime;
			   		
			   		$minus = floor($lastTime-$beginTime);
			   		
			   		if ($minus < 1800 && $scqty < $Qty ) {
				   		$beling = '1';
			   		}
			   		
			   		if ($this->LoginNumber ==  11965) {
				   		$beling = '1';
			   		}
			   		$scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
			   		
			   }
		    }
		    
		    $timeColor = '#727171';
		    if ($minus > $twoDay)
	        {
		        $timeColor = '#ff0000';
	        } else if ($minus > $hour12) {
		        $timeColor = '#ff9946';
	        }
		    
		    $frameCapacity=$this->StuffdataModel->get_framecapacity($rows['StuffId']);
		    $nameColor=$rows['Picture']==1?$orange:$black;
		    
		    
		    $remarkInfo = array();
		    $remkQuery =$this->ScgxRemarkModel->get_remark($sPorderId);
		    if ($remkQuery->num_rows() > 0) {
			    $rowRmk = $remkQuery->row_array();
// 			    Remark,creator,created
				$dateOne = $rowRmk['created'];
			    $created_ct = $this->datehandler->GetDateTimeOutString($dateOne,"");
				$rmkOper=$this->StaffMainModel->get_staffname(element('creator',$rowRmk,'0'));
			    $gxtypeid=$this->ProcessSheetModel->get_gxtypeid(element('ProcessId',$rowRmk,'0'));
				$remarkInfo = array('content'=>element('Remark',$rowRmk,''),
									'oper'=>$created_ct.' '.$rmkOper,
									'img'=>$gxtypeid==''?'':('title'.$gxtypeid)
									
									);
		    }
		    
		    $stuffImg=$rows['Picture']==1?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
		    
// 		    $rem = element('Remark',$rows,'');
		    
		    if ($is415Version) {
			    
			    if ($scqty >= $Qty) {
				    continue;
			    }
			   
			    
			    $dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'order',
				'remarkInfo' =>$remarkInfo,
				'Picture'    =>''.$rows['Picture'],
				'iconImg'    =>$stuffImg,
				'Id'         =>$rows['sPOrderId'],
				'showArrow'  =>'1',
				'open'       =>'0',
				'actions'    =>$actions,
				'arrowImg'   =>'UpAccessory_gray',
				'StuffId'    =>$rows['StuffId'],
			  'FrameCapacity'=>$frameCapacity,
			    'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'week'       =>$rows['DeliveryWeek'],
				'title'      =>$rows['StuffCname'],
				'created'    =>array('Text'=>$rows['created'],'DateType'=>'day'),
				'col1'         =>array('Text'=>number_format($Qty),'Color'=>"$qtycolor"),
				'col1Img'    =>'scdj_11',
				
				'col2'       =>
				array('Text'=>number_format($scqty),'Color'=>$scqty >= $Qty ? '#01be56':'#358fc1','beling'=>$beling),
				
				'col2Img'    =>'scdj_12',
				
				'col3'       =>array('Text'=>'¥'.round($rows['Price'],4),'Color'=>'#3b3e41'),
				'gprice'     =>'¥'.round($rows['Price'],4),
				'wsFmImg'    =>'pc_'.$wsid,
				'col3Img'    =>'',
				
				'col4'       =>array('Text'=>''.$scedInterval,'Color'=>$timeColor),
				'Process'    =>$processArray,
				'gxRmk'		 =>'1',
				'completeImg'=>'',
				'sctime'     =>$minus,
				'modified' =>element('modified',$rows,''),
				'modifier' =>element('modifier',$rows,'')
				
			);
		    } else {
			    $dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'order',
				'Picture'    =>''.$rows['Picture'],
				'iconImg'    =>$stuffImg,
				'Id'         =>$rows['sPOrderId'],
				'showArrow'  =>'1',
				'open'       =>'0',
				'actions'    =>$actions,
				'arrowImg'   =>'UpAccessory_gray',
				'StuffId'    =>$rows['StuffId'],
			  'FrameCapacity'=>$frameCapacity,
			    'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'week'       =>$rows['DeliveryWeek'],
				'title'      =>array('Text'=>$rows['StuffCname'],'Color'=>"$nameColor"),
				'created'    =>array('Text'=>$rows['created'],'DateType'=>'day'),
				'col1'       =>$rows['OrderPO'],
				'col2'       =>number_format($Qty),
				'col3'       =>$segmentIndex<1?($scqty>0?number_format($scqty):''):'',
				'col4'       =>$rows['sPOrderId'],//array('Text'=>$rows['lldate'],'DateType'=>'time'),
				'Process'    =>$processArray,
				'completeImg'=>'',
				'sctime'     =>$minus
			);
		    }
			
		}
		
		
		usort($dataArray, function($a, $b) {
		            $al = ($a['sctime']);
		            $bl = ($b['sctime']);
		            if ($al == $bl)
		                return 0;
		            return ($al > $bl) ? -1 : 1;
		        });
		     
		     
		        
/*
		      
*/

/*
  $dataArrayTrue = array();
		        foreach ($dataArray as $rows) {
			        
			        $remark = element('rem',$rows,'');
			        if ($remark!='' && $remark!='新单重置') {
				        $rows['hideLine'] = '1';
				        $modifier = element('modifier',$rows,'');
				        $modified = element('modified',$rows,'');
				        $operator=$this->StaffMainModel->get_staffname($modifier);   
						 $times =  $this->GetDateTimeOutString($modified,$this->DateTime);
						 $remarkArray=array(
									'tag'      =>'remarkNew',
									'headline' =>'备注: ',
									'Record'   =>$remark,
									'Recorder' =>$times . ' ｜ '. $operator,
									'bgcolor'  =>'#FFFFFF',
									'left_sper'=>'15',
									'RID'      =>'1' 
					         );
					         
							 $dataArrayTrue[]=$rows;
							 $dataArrayTrue[]=$remarkArray;
			        } else {
				        $dataArrayTrue[]=$rows;
			        }
			        
			        
		        }
		        
		        return $dataArrayTrue;
*/
		     
		             return $dataArray;
	}
	
	public function get_subList_stuff($wsid,$segmentIndex,$sPorderId){
	    
	    $segIndex=$this->get_scing_segmentindex($segmentIndex);
	    
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		
		$rowArray=$this->ScSheetModel->get_semi_stocksheet($sPorderId,$segIndex);
		$rownums =count($rowArray);
		$qtycolor     =$this->colors->get_color('qty');
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
		     
		      
		    
	    	$halfImg = '';
			
			$checkBom = $this->ScSheetModel->semi_bomhead($rows['StockId']);
			if ($checkBom->num_rows() > 0) {
				$halfImg = 'halfProd';
			
			}
		    
		    $actions=array();
		    if ($segmentIndex==-1){
			   $actions=$this->pageaction->get_actions('feeding');
		    }
		    
		    $Decimals=$rows['Decimals'];
		     
			$dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'stuff',
				'actions'    =>$actions,
				'Id'         =>$rows['sPOrderId'],
				'title'      =>$rows['StuffCname'],
				'col1'       =>number_format($rows['OrderQty'],$Decimals),
				'col2'       =>number_format($rows['tStockQty'],$Decimals),
				'col3'       =>array('Text'=>number_format($rows['llQty'],$Decimals),'Color'=>$qtycolor) ,
				'col4'       =>'',
				'col3Img'    =>'',
				'Picture'    =>$rows['Picture'],
				'stuffImg'   =>$stuffImg,
				'completeImg'=>'',
				'StockId'    =>$rows['StockId'].'',
				'StuffId'    =>$rows['StuffId'].'',
				'halfImg'    =>$halfImg
			);
		}
		
	   if ($wsid==106){
			$rowArray2=$this->ScSheetModel->get_sc_stocksheet($sPorderId);
			if (count($rowArray2)>0){
				$rows =$rowArray2[0];
				
				
	    	$halfImg = '';
			
			$checkBom = $this->ScSheetModel->semi_bomhead($rows['StockId']);
			if ($checkBom->num_rows() > 0) {
				$halfImg = 'halfProd';
			
			}
			
			
				$dataArray[]=array(
					    'type'       =>$wsid,
					    'segIndex'   =>$segmentIndex,
						'tag'        =>'stuff',
						'Id'         =>$rows['sPOrderId'],
						'title'      =>$rows['StuffCname'],
						'col1'       =>number_format($rows['OrderQty']),
						'col2'       =>'',
						'col3'       =>'',
						'col3Img'    =>'',
						'col4'       =>'',
						'completeImg'=>'',
						'StockId'    =>$rows['StockId'].'',
				'halfImg'    =>$halfImg
					);
		   }
		}	
        return $dataArray;
	}
	
	
	//已备料
	public function get_feeding_bled($wsid,$segmentIndex)
	{
		
	   $this->load->model('CkreplenishModel');
	   
	   $rowArray=$this->CkreplenishModel->get_month_feedings($wsid);
	   $rownums =count($rowArray);
	   
		$redcolor   = $this->colors->get_color('red');
		$superdarkcolor   = $this->colors->get_color('superdark');
		$grayfontcolor  = $this->colors->get_color('grayfont');
		
	    $open = 0;
		
		$rownums =count($rowArray);
		$subdataArray = array();
		for ($i = 0; $i < $rownums; $i++) {
		         $rows    =$rowArray[$i]; 
		         $month = $rows['Month'];
		         $Counts = $rows['Counts'];
		         $Qty = $rows['Qty'];
                 $timeMon = strtotime($month);
		
				 $dataArray[]=array(
					    'tag'         =>'yscMon',
					    'method' =>'bleddate',
					    'type'       =>$wsid,
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
						'col2'        =>array(
					                    'isAttribute'=>'1',
										'attrDicts'  =>array(
										 array('Text'=> number_format($Qty),'Color'=>"$lightgray",'FontSize'=>"12"),
										 array('Text'=>"(" . number_format($Counts) . ")",'Color'=>"$lightgray",'FontSize'=>"10")
										)
									),		
						);
		}
			
		return $dataArray;
	}

	public function djRecord() 
	{
	    $params     = $this->input->post();
	    $sPOrderId  = element('Id',$params,'0');
	    $ProcessId  = element('ProcessId',$params,'');
	    
		$this->load->model('ScGxtjModel');
		$rowArray=$this->ScGxtjModel->get_gx_records($sPOrderId,$ProcessId);
		$rownums =count($rowArray);
		 
		$dataArray=array();
		
		$deleteAction = $this->pageaction->get_action('delete');
		$deleteAction['Action']='deleteGx'; 
		
		
		
		$min30 = 60 *30;
		$hour4 = $min30*2 *4;
		$hour12 = $hour4 * 3;
		
		
		$this->load->model('StaffMainModel');
		$this->load->model('LabelPrintModel'); 
		$this->load->model('ScSheetModel');
		$this->load->model('ProcessSheetModel');
		
		$records=$this->ScSheetModel->get_records($sPOrderId);
	    $stockId=$records['StockId'];
        $records=null;
        
	    $lastProcessId=$this->ProcessSheetModel->get_lastProcessId($stockId);
		
		$scedqty=0;
		if ($lastProcessId==$ProcessId){
			$this->load->model('ScCjtjModel');
		    $scedqty= $this->ScCjtjModel->get_scqty($sPOrderId);
		}
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $operator=$this->StaffMainModel->get_staffname($rows['Leader']);
		    
		    //标签打印设置 
		   $actions=array();
		   if ($lastProcessId==$ProcessId){
		       $prints=null;
			   $prints[]=$this->LabelPrintModel->get_gxrecord_print($sPOrderId,$scedqty,$rows['Qty']);
			   $actions=$this->PrintAction;
			   $actions[0]['data']= $prints; 
			   $scedqty-=$rows['Qty']; 
		   }
		   
		   
		   if ($this->LoginNumber == 11965) {
			   $actions[]=$deleteAction;
		   }
		   
		   	$thistime = $rows['OPdatetime'];
			$snailA = $snailB = $snailC = '';
			if ($i< ($rownums - 1)) {
				$nextTime = $rowArray[$i+1]['OPdatetime'];
				$minus = floor(strtotime($thistime)- strtotime($nextTime));
				if ($minus > $hour12) {
					$snailA = 'snailA0';
					$snailB = 'snailB0';
					$snailC = 'snailC1';
				} else if ($minus > $hour4) {
					$snailA = 'snailA0';
					$snailB = 'snailB1';
					$snailC = 'snailC0';
				} else if ($minus > $min30) {
					$snailA = 'snailA1';
					$snailB = 'snailB0';
					$snailC = 'snailC0';
				} else {
					$snailA = $snailB = $snailC = '';
				}
				
			}

		   
		   
		   $dataArray[]=array(
			    'tag'    =>'djRecord',
			    'Id'     =>$rows['Id'],
			    'actions'=>$actions,
			    'snail1'=>$snailA,
				'snail2'=>$snailB,
				'snail3'=>$snailC,
			    'index'=>'' . ($rownums-$i),
				'col1' =>$rows['Qty'],
				'col2' =>array('Text'=>$rows['OPdatetime'],'DateType'=>'time'),
				'col3' =>"$operator",
				'scedqty'=>$scedqty
			);
			
		}
		$records[]=array("data"=>$dataArray);
	
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$records);
		$this->load->view('output_json',$data);
	}
	
	
	//生产工单产量登记记录
	public function kldjRecord() 
	{
	    $params     = $this->input->post();
	    $sPOrderId  = element('Id',$params,'0');
	    
		$this->load->model('ScCjtjModel');
		$rowArray=$this->ScCjtjModel->get_records($sPOrderId);
		$rownums =count($rowArray);
		 $min30 = 60 *30;
		$hour4 = $min30*2 *4;
		$hour12 = $hour4 * 3;
		
		$dataArray=array();
		
		$this->load->model('StaffMainModel');
		$this->load->model('LabelPrintModel'); 
		$this->load->model('ScSheetModel');
		
		$deleteAction = $this->pageaction->get_action('delete');
		$deleteAction['Action']='deleteSc'; 
		
		
		$scedqty= $this->ScCjtjModel->get_scqty($sPOrderId);
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $Operator=$this->StaffMainModel->get_staffname($rows['Operator']);
		    
		    //标签打印设置 
		   $actions=array();
		   $prints=null;
		   $prints=$this->LabelPrintModel->get_gxregister_print($sPOrderId,$rows['Qty']);
		   $actions=$this->PrintAction;
		   $actions[0]['data']=$prints; 
		   
		   if ($this->LoginNumber == 11965 ) {
			   $actions[]=$deleteAction;
		   }
		   
		   $scedqty-=$rows['Qty']; 
		   
		   
		    $thistime = $rows['created'];
			$snailA = $snailB = $snailC = '';
			if ($i< ($rownums - 1)) {
				$nextTime = $rowArray[$i+1]['created'];
				$minus = floor(strtotime($thistime)- strtotime($nextTime));
				if ($minus > $hour12) {
					$snailA = 'snailA0';
					$snailB = 'snailB0';
					$snailC = 'snailC1';
				} else if ($minus > $hour4) {
					$snailA = 'snailA0';
					$snailB = 'snailB1';
					$snailC = 'snailC0';
				} else if ($minus > $min30) {
					$snailA = 'snailA1';
					$snailB = 'snailB0';
					$snailC = 'snailC0';
				} else {
					$snailA = $snailB = $snailC = '';
				}
				
			}

		   
		   $dataArray[]=array(
			    'tag'    =>'djRecord',
			    'Id'     =>$rows['Id'],
			    'actions'=>$actions,
			    'snail1'=>$snailA,
				'snail2'=>$snailB,
				'snail3'=>$snailC,
			    'index'=>'' . ($rownums-$i),
				'col1' =>$rows['Qty'],
				'col2' =>array('Text'=>$rows['created'],'DateType'=>'time'),
				'col3' =>"$Operator",
				'scedqty'=>$scedqty
			);
			
		}
		$records[]=array("data"=>$dataArray);
	
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$records);
		$this->load->view('output_json',$data);
	}
	
	//扫描QR读取生产工单信息
	public function prepareRegister() {
		$rows = array();
	    $params = $this->input->post();
	    $sPOrderId = element('sPOrderId',$params,'-1');
	    
	    $this->load->model('ScSheetModel');
	    $this->load->model('StuffdataModel');
	    $row = $this->ScSheetModel->get_records($sPOrderId);
	    //$id = $row['sPOrderId'];
	    $stuffid = $row['StuffId'];
		$StockId = $row['StockId'];
	    $Qty = $row['Qty'];
	    
	    
	    
	    $stuffRow = $this->StuffdataModel->get_records($stuffid);
	    $stuffImg = $this->StuffdataModel->get_stuff_picture($stuffid);
	    
	    $FrameCapacity = $stuffRow['FrameCapacity'];
	    $StuffCname = $stuffRow['StuffCname'];
	    $this->load->model('ScCjtjModel');
		$scedqty= $this->ScCjtjModel->get_scqty($sPOrderId);
	    $maxqty = $Qty - $scedqty;
	    
	    $inforow = $this->ScSheetModel->get_ll_info($sPOrderId);
	    if ($inforow['CanStock'] == 3) {
		    
	    } else {
		    $maxqty = 0;
	    }
	    
		$rows[]=array(
			'Id'           =>"$sPOrderId",
			'sPOrderId'    =>"$sPOrderId",
			'StuffId'      =>"$stuffid",
			'StockId'      =>"$StockId",
			'FrameCapacity'=>"$FrameCapacity",
			'DjQty'        =>"$scedqty",
			'MaxQty'       =>"$maxqty",
			'cName'        =>"$StuffCname",
			'stuffImg'     =>"$stuffImg"
		);
		
		$data['jsondata']=array('status'=>'','message'=>'','rows'=>$rows);
	    
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

//保存工序登记
	public function register()
	{
		$params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $fromQr     = element('fromQr',$params,'');
	    
	    $speak = '保存失败';
	    $addedComplete = '';
/*
	    if ($this->LoginNumber == 11965) {
		     $Qty        = element('Qty',$params,'0');
		    $speak = '登记成功，数量'.$Qty;
		     $data['jsondata']=array('status'=>'1','speak'=>$speak,'totals'=>1,'rows'=>null);

			$this->load->view('output_json',$data);
			return ;
	    }
*/
	    
	    if ($fromQr == '1') {
		    $this->qr_register();
		    return;
	    }
	    $djtype     = element('djType',$params,'');
	    
	    if ($action=='register'){
	     $sPOrderId  = element('Id',$params,'0');
	     $ProcessId  = element('ProcessId',$params,'');
	     $Qty        = element('Qty',$params,'0');
	     
	     $rowArray=array();
	     $actions =array();
	     $status = 0; 
	     $rownums = 0;
	     if ($djtype=='gx'){
	        
	         
	        
	        $this->load->model('ScGxtjModel');
	        $this->load->model('ScSheetModel');
            $records=$this->ScSheetModel->get_records($sPOrderId);
            $stockId=$records['StockId'];
        
		    $this->load->model('ProcessSheetModel');
		    $processArray =$this->ProcessSheetModel->get_sc_processlist($stockId,$sPOrderId,1);
		    
		    $canRegister = true;
		    $lastGxIndex = -1;
			$GxQty = 0;
			
			
		    foreach ($processArray as $processDict) {
			    if ($processDict['ProcessId'] == $ProcessId) {
				    $QtyLow = $processDict['Qty'];
				    $GxQty = $processDict['GxQty'];
				    if ($GxQty + $Qty > $QtyLow) {
					    $canRegister = false;
				    } else if ($GxQty+ $Qty == $QtyLow) {
					    $addedComplete = '工序'. $processDict['TypeId'] .'完成';
				    }
				    break;
			    }
			    $lastGxIndex ++;
		    }
		    if ($lastGxIndex >= 0) {
			    
			    $processDict = $processArray[$lastGxIndex];
			    $QtyLow = $processDict['GxQty'];
			    if ($GxQty + $Qty > $QtyLow) {
				    $canRegister = false;
			    } else if ($GxQty+ $Qty == $QtyLow && $addedComplete=='') {
				    $addedComplete = '工序'. $processDict['TypeId'] .'完成';
			    }
			    
		    }
		    
		    if ($lastGxIndex == (count($processArray)-1) && $addedComplete!='') {
			    $addedComplete.='生产完成';
		    }
			$rownums = 0;
			if ($canRegister == true) {
				$rownums=$this->ScGxtjModel->save_records($params);
			} else {
				$status = -1;
			}
		    
		    if ($rownums>0){
               $status = 1;
               
               $speak = '登记'.$Qty.','.$addedComplete;
			   $this->load->model('ScSheetModel');
	           $records=$this->ScSheetModel->get_records($sPOrderId);
	           $stockId=$records['StockId'];
	        
			   $this->load->model('ProcessSheetModel');
			   $processArray =$this->ProcessSheetModel->get_sc_processlist($stockId,$sPOrderId,1);
			   $lastProcessId=$this->ProcessSheetModel->get_lastProcessId($stockId);
			   
			   $this->load->model('ScCjtjModel');
			   $scqty  =$this->ScCjtjModel->get_scqty($sPOrderId);
			   
			   if ($lastProcessId==$ProcessId){
			        //标签打印设置
	                $actions=$this->get_action_print($sPOrderId,$Qty);
			   }
		       $rowArray=array(
		                'col3'   =>array('Text'=>number_format($scqty)),
		                'Process'=>$processArray
		                );
		    }
		 }
		 else{
			  $this->load->model('ScSheetModel');
			  $records=$this->ScSheetModel->get_records($sPOrderId);
			  $OrderQty = $records ['Qty']; 
			  
			  $this->load->model('ScCjtjModel');
			  $scqty  =$this->ScCjtjModel->get_scqty($sPOrderId);
			  
			  if ($scqty+$Qty<=$OrderQty){
				 $rownums=$this->ScCjtjModel->save_records($params); 
			  if ($scqty + $Qty == $OrderQty) {
				   $addedComplete.='生产完成';
			  }
			     if ($rownums>0){
			       $status = 1;  
			        $speak = '登记'.$Qty.','.$addedComplete;
				   if ($records['ActionId']!=104){
		             //标签打印设置,开料不印标签
	                 $actions=$this->get_action_print($sPOrderId,$Qty); 
	               }
	              
	               $rowArray=array(
			            'col3' =>array('Text'=>number_format($scqty+$Qty))
			          );
			     }
			  }
			  else{
				  $status = -1;
			  }
		 }
		 
		  $dataArray=array(
		            'actions'=>$actions,
		            'data'   =>$rowArray
		            );

          switch($status){
	          case  0: $message='登记失败!'; break;
	          case -1: $status=0;$message='登记数大于订单数!'; break;
	          default: $message='登记成功!';  break;
          } 
		 
	      $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$rownums,'rows'=>$dataArray,'speak'=>$speak);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array(),'speak'=>'非法操作!');
	    }
	    
		$this->load->view('output_json',$data);
	}
	
	//获取标签打印内容
	public function get_action_print($sPOrderId,$Qty){
	
	    $this->load->model('LabelPrintModel');
        $prints[]=$this->LabelPrintModel->get_gxregister_print($sPOrderId,$Qty);
        $actions=$this->PrintAction;
        $actions[0]['data']= $prints; 
        
        return $actions;
	}
	
	
	public function deleteGx() {
		$this->load->model('ScGxtjModel');
		
		$params   = $this->input->post();
		$del = $this->ScGxtjModel->delete_item($params);
		$status = $del > 0 ? 1 : 0;
		$message=$status==1?'删除成功！':'删除失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$status==1?array('Action'=>'delete'):null);
		$this->load->view('output_json',$data);
	}
	
	
	public function deleteSc() {
		$this->load->model('ScCjtjModel');
		$params   = $this->input->post();
		$del = $this->ScCjtjModel->delete_item($params);
		$status = $del > 0 ? 1 : 0;
		$message=$status==1?'删除成功！':'删除失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$status==1?array('Action'=>'delete'):null);
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
// 	    echo("$action=='remark' && $Id>0 &&  $remark!=''");
	    if ($action=='remark' && $Id>0 &&  $remark!='')
	    {
		    $this->load->model('ScgxRemarkModel');
		    $status=$this->ScgxRemarkModel->save_item($params);
		    if ($status==1){
			   
			    $this->load->model('StaffMainModel');
			      $this->load->model('ProcessSheetModel');
			    $operator=$this->StaffMainModel->get_staffname($this->LoginNumber);
			    $gxtypeid=$this->ProcessSheetModel->get_gxtypeid(element('ProcessId',$params,'0'));
			    
			   $rowArray =array('remarkInfo'=>
			    			array(
			    				'img'=>$gxtypeid!=''?('title'.$gxtypeid):'',
			    				'content'=>$remark,
			    				'oper'=>'1分前 '.$operator
			    				 )) ;
		    }
		    
		    /*
			    
			     if ($status==1){
			    $gxtitles = array('N','①','②','③','④');
			   
			    $this->load->model('StaffMainModel');
			      $this->load->model('ProcessSheetModel');
			    $operator=$this->StaffMainModel->get_staffname($this->LoginNumber);
			    $gxtypeid=$this->ProcessSheetModel->get_gxtypeid(element('ProcessId',$params,'0'));
			    
			   $rowArray =array('remarkInfo'=>
			    			array(
			    				'content'=>$gxtypeid==''?$remark:
			    						array('isAttribute'=>'1',
			    							'attrDicts'  =>array(
									   array(
									   'Text'=>''.$gxtitles[$gxtypeid],
									   'Color'=>"#358fc1",
									   'FontSize'=>"11"),
									   array(
									   'Text'=>' '.$remark,
									   'Color'=>"#727171",
									   'FontSize'=>"11")
									   )
			    							),
			    				'oper'=>'1分钟前｜'.$operator
			    				 )) ;
		    }

		    */
		    
	    }
	    $dataArray=array("data"=>$rowArray);
	    
	    $message=$status==1?'备注成功！':'备注失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);

	}
	
	public function get_subList_scgx($wsid,$segmentIndex,$gxid,$dyweek='',$spid=''){
	
	    $black      =$this->colors->get_color('black');
	    $superdark      =$this->colors->get_color('superdark');
	    $bluefont      =$this->colors->get_color('bluefont');
	    $orange     =$this->colors->get_color('orange');
		$versionNum = $this->versionToNumber($this->AppVersion);
		$is415Version = $versionNum >= 415 ? true : false;
        
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	    
		$this->load->model('ScSheetModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('ScGxtjModel');
		$this->load->library('dateHandler');
		
		

		$segIndex=$this->get_scing_segmentindex($segmentIndex);
		
		
		
		
		
		if ($dyweek == 'gx1') {
			$rowArray=$this->ScSheetModel->get_semi_bledsheet($wsid,$actionid,33,$spid);
			
		
		} else {
			$rowArray=$this->ScSheetModel->get_semi_bledsheet($wsid,$actionid,$segIndex);
		}
		
		$rownums =count($rowArray);
		
		
		$dataArray=array();
		
		$this->load->model('StuffdataModel');
		$this->load->model('StaffMainModel');
		$this->load->model('ScgxRemarkModel');

		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $scqty = $rows['ScQty'];
		    $Qty = $rows['Qty'];
		    $processArray=array();
		    $actions=array();
			//$actions=$this->RemarkAction;
		    $actions=$this->MenuAction;
		    $scedInterval = '';
		    $minus = 0;
		    $sPorderId = $rows['sPOrderId'];
		    
		    if ($actionid==102){//皮套加工
			   $stockId=$rows['StockId'];  
			   $this->load->model('ProcessSheetModel');
			   $processArray=$this->ProcessSheetModel->get_sc_processlist($stockId,$sPorderId,1);
			   $actions=array();
			   
			   
		    }
		    
		    $col1Qty = '';
		    $col1QtyDj = '';
		    $needContinue = false;
		    $boolfinded = false;
		    $lsoperator = '--';
		    $lstime = '--';
		    $sortted = 0;
		    $iteri = 0;
		    foreach ($processArray as $processRow) {
			    if ($processRow['TypeId'] == $gxid) {
				    $boolfinded = true;
				    $gxqty = $processRow['Qty'];
				    $gxscqty = $processRow['GxQty'];
				    $col1Qty = number_format($gxqty);
				    $col1QtyDj = number_format($gxscqty);
				    if ($gxscqty > 0 && $gxscqty>=$gxqty) {
					   $needContinue = true;
				    }
				    $ProcessId =  $processRow['ProcessId'];
				    $latestRe = $this->ScGxtjModel->get_gx_recordlatest($sPorderId,$ProcessId);
				    if (count($latestRe) > 0) {
					    $latestRow = $latestRe[0];
					    $lsoperator=$this->StaffMainModel->get_staffname($latestRow['Leader']);
					    $sortted = strtotime( $latestRow['OPdatetime']);  
						$lstime = $this->datehandler->GetDateTimeOutString($latestRow['OPdatetime'],"");
// 					    Leader,OPdatetime
				    }
				    
				    break;
				   
			    }
			    
			     $iteri ++;
// 			   $dataArray[]=array('ProcessId'=>"$ProcessId",'TypeId'=>"$gxTypeId",'Qty'=>"$gxLowQty",'GxQty'=>"$scQty",
// 			                      'MaxQty'=>"$gxMaxQty","DjQty"=>"$scQty",'url'=>"$url");  
		    }
		    
		    if ($iteri>0) {
			    if ($processArray[$iteri-1]['GxQty']  <= 0) {
				    continue;
			    }
		    }
		    if ($needContinue || $boolfinded==false) {
			    continue;
		    }
		    
		    
		    $frameCapacity=$this->StuffdataModel->get_framecapacity($rows['StuffId']);
		    
		   		    
		    $stuffImg=$rows['Picture']==1?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
		    

			    $dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'order',
				'bg_color'   =>'clear',
				'Picture'    =>''.$rows['Picture'],
				'iconImg'    =>$stuffImg,
				'Id'         =>$rows['sPOrderId'],
				'actions'    =>$actions,
				'StuffId'    =>$rows['StuffId'],
			  'FrameCapacity'=>$frameCapacity,
			    'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'week'       =>$rows['DeliveryWeek'],
				'title'      =>$rows['StuffCname'],
				'created'    =>array('Text'=>$rows['created'],'DateType'=>'day'),
				'col1'       =>array(
							'isAttribute'=>'1',
							'attrDicts'  =>array(
									   array(
									   'Text'=>' '.$col1Qty,
									   'Color'=>$superdark,
									   'FontSize'=>"11.5"),
									   array(
									   'Text'=>'/'.$col1QtyDj,
									   'Color'=>$bluefont,
									   'FontSize'=>"11.5")
									   )
							 ),
				'col1Img'    =>'title'.$gxid,
				'col2Img'    =>'',
				'col3Img'    =>'',
				'col4'       =>$lstime.' '.$lsoperator,
				'Process'    =>$processArray,
				'hidePro'    =>''.$gxid,
				'completeImg'=>'',
				'sctime'     =>$sortted
				
			);
		    
			
		}
				usort($dataArray, function($a, $b) {
		            $al = ($a['sctime']);
		            $bl = ($b['sctime']);
		            if ($al == $bl)
		                return 0;
		            return ($al > $bl) ? -1 : 1;
		        }); 
			     
		             return $dataArray;
	}

	public function check_ws() {
		$params       = $this->input->post();

		$sPOrderId   = element('qrstr',$params,'');
		
		$message = '';
	    $this->load->model('ScSheetModel');
       $records=$this->ScSheetModel->get_records($sPOrderId);
       $WorkShopId=element('WorkShopId',$records,'');
       if ($WorkShopId == 102 || $WorkShopId == 103) {
	       $message = $WorkShopId.'|1|2016';
	       
	       if (element('ScQty',$records,'')<=0) {
		       $this->load->model('ScGxtjModel');
		      if ($this->ScGxtjModel->get_begin_time($sPOrderId)=='')
		       $message = $WorkShopId."|1|$sPOrderId|gx1";
	       }
       }
       
	    
		$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>'1','rows'=>null);
		$this->load->view('output_json',$data);
	}
	
	
		public function qr_djlist() {
		$params       = $this->input->post();
		$segmentIndex = -1;
		$scanString   = element('qrstr',$params,'');
		$scanStringArr = explode('|', $scanString);
		$gxid = '0';
		$gx1 = $spid = '';
		if (count($scanStringArr)>=2) {
			$type         = $scanStringArr[0];
			$gxid         = $scanStringArr[1];
		}
		
		if (count($scanStringArr)>=4) {
			$gx1         = $scanStringArr[3];
			$spid         = $scanStringArr[2];
		}
		
		
		$dataArray=array();
		$dataArray=$this->get_subList_scgx($type,$segmentIndex,$gxid,$gx1,$spid);
		$section = array();
		$section[]=array('data'=>$dataArray);
	    
		$data['jsondata']=array('status'=>'1','message'=>$gx1,'totals'=>'1','rows'=>$section);
		$this->load->view('output_json',$data);

	}
	
	
	public function qr_register()
	{
		$params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $fromQr     = element('fromQr',$params,'');
	    
	    if ($fromQr != '1') {
		    
		    return;
	    }
	    $speak = '保存失败';
	    $addedComplete = '';
	    
	    
	    $djtype     = element('djType',$params,'');
	    
	    if ($action=='register'){
	     $sPOrderId  = element('Id',$params,'0');
	     $ProcessId  = element('ProcessId',$params,'');
	     $Qty        = element('Qty',$params,'0');
	     
	     $rowArray=array();
	     $actions =array();
	     $status = 0; 
	     $rownums = 0;
	     $needdelete = false;
	     if ($djtype=='gx'){
	        
	        $this->load->model('ScGxtjModel');
	        $this->load->model('ScSheetModel');
            $records=$this->ScSheetModel->get_records($sPOrderId);
            $stockId=$records['StockId'];
        
		    $this->load->model('ProcessSheetModel');
		    $processArray =$this->ProcessSheetModel->get_sc_processlist($stockId,$sPOrderId,1);
		    
		    $canRegister = true;
		    $lastGxIndex = -1;
			$GxQty = 0;
		    foreach ($processArray as $processDict) {
			    if ($processDict['ProcessId'] == $ProcessId) {
				    $QtyLow = $processDict['Qty'];
				    $GxQty = $processDict['GxQty'];
				    if ($GxQty + $Qty > $QtyLow) {
					    $canRegister = false;
				    } else if ($GxQty+ $Qty == $QtyLow) {
					    $addedComplete = '工序'. $processDict['TypeId'] .'完成';
				    }
				    break;
			    }
			    $lastGxIndex ++;
		    }
		    if ($lastGxIndex >= 0) {
			    
			    $processDict = $processArray[$lastGxIndex];
			    $QtyLow = $processDict['GxQty'];
			    if ($GxQty + $Qty > $QtyLow) {
				    $canRegister = false;
			    } else if ($GxQty+ $Qty == $QtyLow) {
					    $addedComplete = '工序'. $processDict['TypeId'] .'完成';
				    }
			    
		    }
			$rownums = 0;
			if ($canRegister == true) {
				$rownums=$this->ScGxtjModel->save_records($params);
			} else {
				$status = -1;
			}
		    
		    if ($rownums>0){
               $status = 1;
               
                $speak = '登记'.$Qty.','.$addedComplete;
               
			   $this->load->model('ScSheetModel');
	           $records=$this->ScSheetModel->get_records($sPOrderId);
	           $stockId=$records['StockId'];
	        
	        $this->load->model('StaffMainModel');
			   $this->load->model('ProcessSheetModel');
			   $processArray =$this->ProcessSheetModel->get_sc_processlist($stockId,$sPOrderId,1);
			   $lastProcessId=$this->ProcessSheetModel->get_lastProcessId($stockId);
			   $col1Qty = '';
			   $col1QtyDj = '';
			   
			   foreach ($processArray as $processRow) {
				    if ($processRow['ProcessId'] == $ProcessId) {
	
					    $gxqty = $processRow['Qty'];
					    $gxscqty = $processRow['GxQty'];
					    $col1Qty = number_format($gxqty);
					    $col1QtyDj = number_format($gxscqty);
					    if ($gxscqty > 0 && $gxscqty>=$gxqty) {
							$needdelete = true;
					    }
					    break;
				    }
				    
			    }
			   
			   if ($lastProcessId==$ProcessId){
			        //标签打印设置
	                $actions=$this->get_action_print($sPOrderId,$Qty);
			   }
			   $operator=$this->StaffMainModel->get_staffname($this->LoginNumber);
			    $superdark      =$this->colors->get_color('superdark');
			    $bluefont      =$this->colors->get_color('bluefont');
		       $rowArray=array(
		                'col1'       =>array(
							'isAttribute'=>'1',
							'attrDicts'  =>array(
									   array(
									   'Text'=>' '.$col1Qty,
									   'Color'=>$superdark,
									   'FontSize'=>"11.5"),
									   array(
									   'Text'=>'/'.$col1QtyDj,
									   'Color'=>$bluefont,
									   'FontSize'=>"11.5")
									   )
							 ),
					    'col4'   =>'1分前 '.$operator,
		                'Process'=>$processArray
		                );
		    }
		 }
		 
		 
		  $dataArray=array(
		            'actions'=>$actions,
		            'data'   =>$rowArray
		            );
		  if ($needdelete) {
			  $dataArray['Action'] = 'delete';
		  }
		            
          switch($status){
	          case  0: $message='登记失败!'; break;
	          case -1: $status=0;$message='登记数大于订单数!'; break;
	          default: $message='登记成功!'; break;
          } 
		 
	      $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$rownums,'rows'=>$dataArray,'speak'=>$speak);
	    }
	    else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array(),'speak'=>$speak);
	    }
	    
		$this->load->view('output_json',$data);
	}
	
	//补料保存
   public function feeding()
    {
        $params     = $this->input->post();
	    $action       = element('Action',$params,'');
	    $sPOrderId = element('Id',$params,'');
	    $StockId     = element('StockId',$params,'');
	    $Qty            = element('qty',$params,'');
	    $Remark     = element('remark',$params,'');
	    
	    
	    $data=array();
	    
	    if ($action=='feeding'){
	          $this->load->model('CkreplenishModel');
	          $status = $this->CkreplenishModel->save_records($sPOrderId,$StockId,$Qty,$Remark);
	          
	          $message=$status==0?'新增补料记录失败！':'新增补料记录成功。';
	           $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'1','rows'=>array());
	    }
	     else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
        $this->load->view('output_json',$data);
	    
	}
	
	//补料记录删除
	public function delete_feed(){
		$params     = $this->input->post();
	    $action       = element('Action',$params,'');
	    $Id = element('Id',$params,'');
	    
	    if ($action=='delete_feed'){
	          $this->load->model('CkreplenishModel');
	          $status = $this->CkreplenishModel->delete_records($Id);
	          
	          $rowArray=array();
	          if ($status==1){
				    $rowArray=array(
				            'Action' =>'delete' 
				          );
			    }
			    
	          $message=$status==0?'删除补料记录失败！':'删除补料记录成功。';
	           $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'1','rows'=>$rowArray);
	    }
	     else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
        $this->load->view('output_json',$data);
	}
	
	public function affirm(){
		$params     = $this->input->post();
	    $action       = element('Action',$params,'');
	    $Id = element('Id',$params,'');
	    
	    if ($action=='affirm'){
	          $this->load->model('CkreplenishModel');
	          $status = $this->CkreplenishModel->set_estate($Id,0,$this->LoginNumber,'');
	          
	          $rowArray=array();
	          if ($status==1){
				    $rowArray=array(
				            'Action' =>'delete' 
				          );
			    }
			    
	          $message=$status==0?'补料完成确认失败！':'补料完成确认成功。';
	           $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'1','rows'=>$rowArray);
	    }
	     else{
		    $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
        $this->load->view('output_json',$data);
	}
}
