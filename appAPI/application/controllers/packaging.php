 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Packaging extends MC_Controller {
/*
	功能:组装生产
*/
    public $ActionId=null;
    public $MenuAction= null;
    public $WorkShipId= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 1;
        $this->WorkShipId   = 101;
        $this->MenuAction   = $this->pageaction->get_actions('allot');//分配
    }

	public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	public function main()
	{
		$params = $this->input->post();
		
		$types    = element('types',$params,'');
		$isShadow = element('isShadow',$params,'');
		
		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		$superdark =$this->colors->get_color('superdark');
		$grayfont =$this->colors->get_color('grayfont');
		
		$this->load->model('staffMainModel'); 
		$this->load->model('StaffWorkStatusModel');
		$this->load->model('WorkShopdataModel');
		$this->load->model('ScSheetModel'); 
		$this->load->model('ScCjtjModel');
		$this->load->model('CheckinoutModel');
		$this->load->model('CkreplenishModel');
		
		$versionNum = $this->versionToNumber($this->AppVersion);
		
		$is415Version = $versionNum >= 415 ? true : false;
		
        $this->load->library('dateHandler');
		
		$hourArr = $this->datehandler->get_worktimes();
		$hoursNow = $hourArr[1];
		
		$rows=$this->WorkShopdataModel->get_records($this->WorkShipId);
		$groups  =$rows['GroupId'];
		$ActionId=$rows['ActionId'];
		
		$groupnums=$groups==''?0:$this->staffMainModel->date_checkInNums_ingroup($groups,$this->Date);
		//$groupnumsMon=$groups==''?0:$this->staffMainModel->mon_checkInNums_ingroup($groups,date('Y-m'));
		
		$month_worktime = $this->CheckinoutModel->get_month_worktimes($groups,'');
		
		$worknums =$this->CheckinoutModel->get_group_worknumbers($groups,$this->Date);
		$noworknums=$groupnums-$worknums;
		
		
		$day_output   =$this->ScCjtjModel->get_workshop_day_output($this->WorkShipId);
		$month_output =$this->ScCjtjModel->get_workshop_month_output($this->WorkShipId);
			   
	    $day_valuation  =$groupnums*$laborCost*$worktime;
	    
	    $day_color      =$day_output>=$day_valuation?$lightgreen:$red;
	   // $month_valuation=$day_valuation * 25;//需更改
	    $month_valuation=$this->ScCjtjModel->get_month_valuation($groups);
	    
	    $percent        =$day_valuation>0?round($day_output*100/$day_valuation):100;
	    
	    $allot_qty =$this->ScSheetModel->get_canstock_qty($this->WorkShipId,$ActionId,'DFP');
	    $bledqty   =$this->ScSheetModel->get_canstock_qty($this->WorkShipId,$ActionId,'DSC');
	    $overqty   =$this->ScSheetModel->get_canstock_qty($this->WorkShipId,$ActionId,'Overdue');
	    
		$dayqty   =$this->ScCjtjModel->get_day_scqty($this->WorkShipId);
		
		$unqty    =$this->ScSheetModel->get_unscqty($this->WorkShipId);
		//$overqty  =$this->ScSheetModel->get_semi_bledqty($this->WorkShipId,'current');
		$newDay = $hoursNow<$worktime?$groupnums*$laborCost *$hoursNow:$groupnums*$laborCost *$worktime;
		 
		$newMonth = $month_worktime*$laborCost+$newDay;
		
	  $feedrows = $this->CkreplenishModel->get_not_feedings($this->WorkShipId);
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
			   
		
		$sleepRabit = '';
		$scale = '1';
		$phidden = '0';
		if ($day_output <= 0 && $newDay <= 0) {
			$sleepRabit = 'sleepRabit';
			$scale = '1.2';
			$phidden = '1';
		}
		 
	    $dayDict = array(
			   'darkVal'    =>$day_valuation>0?''.($day_output/ $day_valuation):'0',
			   'pointVal'   =>$day_valuation>0?''.($newDay / $day_valuation):'0',
			   'title'      =>'今日',
			   'fix'        =>'%',
			   'pHidden'    =>$phidden,
			    'sleep'     =>$sleepRabit,
			    'scale'     =>$scale,
			   'percent'    =>$newDay>0?''.(round(($day_output-$newDay)/$newDay*100)):'0'
		   );
		$sleepRabit = '';
		$scale = '1';
		$phidden = '0';
		if ($month_output <= 0 && $newMonth <= 0) {
			$sleepRabit = 'sleepRabit';
			$scale = '1.2';
			$phidden = '1';
		}
		 
		   //$newMonth = $groupnumsMon*$laborCost*$worktime ;
		   $monDict = array(
			   'darkVal'    =>$month_valuation>0?''.($month_output/$month_valuation):'',
			   'pointVal'   =>$month_valuation>0?''.($newMonth/$month_valuation):'0',
			   'title'      =>'本月',
			   'fix'        =>'%',
			   'pHidden'    =>$phidden,
			    'sleep'     =>$sleepRabit,
			    'scale'     =>$scale,
			   'percent'    =>$newMonth>0?''.round(($month_output-$newMonth)/$newMonth*100):'100'
		   );
		   
	    $dataArray  = array();
	    $listdatas=$this->get_subList_order($this->WorkShipId,-1);
	   $trigger    =array(
						'3'=>array('title'=>'今日组装','api'=>'todayzz','noseg'=>'1'),
						'4'=>array('title'=>'已组装','api'=>'yzz','noseg'=>'1')
					);
	    
		$dataArray[]=array(
					'tag'        =>'zuzhuang',
					'hidden'     =>'0',
					'segIndex'   =>'-1',
					'method'     =>'segment',
					'dayDict'    =>$dayDict,
					'monDict'    =>$monDict,
					'trigger'    =>$versionNum>=416?$trigger:array('n'=>'n'),
					'title'      =>
						$is415Version ? 
						array(
							'isAttribute'=>'1',
							'attrDicts'  =>array(
									   array(
									   'Text'=>'组装',
									   'Color'=>$superdark,
									   'FontSize'=>"14"),
									   array(
									   'Text'=>'  '.$groupnums .'人',
									   'Color'=>$grayfont,
									   'FontSize'=>"11")
									   )
							 )
							 :
							'待组装',
					'titleImg'   =>'ws_dzz',// . $this->WorkShipId
					//'subtitle'   =>$groupnums .'人｜' . $noworknums . '人',
					'subtitle'   =>array(
					                'isAttribute'=>'1',
									'attrDicts'  =>array(
									   array('Text'=>"$groupnums" . ' 人｜' , 'Color'=>"$superdark",'FontSize'=>"13"),
									   array('Text'=>"$noworknums",'Color'=>"$red",'FontSize'=>"13"),
									   array('Text'=>'人','Color'=>"$superdark",'FontSize'=>"13")
									   )
									),
					'amount'     =>
					$is415Version?
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
									      array('Text'=>"/" . number_format($unqty),'Color'=>"$superdark",'FontSize'=>"11")
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
					'value1'     =>out_format(number_format($allot_qty),'--'),
					'value2'     =>array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>number_format($overqty).' ','Color'=>"$red",'FontSize'=>"12"),
									      array('Text'=> number_format($bledqty),'Color'=>"$superdark",'FontSize'=>"12")
									   )
									),
					'value3'     =>$feedVal,
					'data'       =>$listdatas
			  );
			  
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>'1','rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	public function segment() 
	{
		$params       = $this->input->post();
		$segmentIndex = intval( element("segmentIndex",$params,0));
		
		if ($segmentIndex==2)
		{
			 $dataArray=$this->get_segment_feeding($this->WorkShipId,$segmentIndex);
		}else{
			 $dataArray=$this->get_subList_order($this->WorkShipId,$segmentIndex);
		}
		
		$totals   =count($dataArray);
		
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
		
		
	}
	
	public function subList() 
	{
		$params       = $this->input->post();
		$segmentIndex = intval( element("segmentIndex",$params,-1));
		$upTag        = ( element("upTag",$params,'zorder'));
		$type         = ( element("type",$params,$this->WorkShipId));
		$sPOrderId    = element("Id",$params,'');
		
		$dataArray=array();
		
		
		
		if ($upTag=='zorder'){
		    $dataArray=$this->get_subList_stuff($sPOrderId,$segmentIndex);
		 }
		 else if ($upTag=='wtotal') {

			  $dataArray=$this->feeding_month_dates($type, $sPOrderId, $segmentIndex);
		 }
		  else if ($upTag=='zzDay') {

			  $dataArray=$this->get_date_feeding($type, $segmentIndex, $sPOrderId) ;
		 }
		 else{
			$dataArray=$this->get_subList_order($this->WorkShipId,$segmentIndex);
		}
		$totals=count($dataArray);
		
		if ($totals > 0) {
			$dataArray[$totals - 1]['deleteTag']=''.$upTag;
		}
		
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
		
		
	}
	
	public function get_subList_order($wsid,$segmentIndex,$scLine=''){
	    
	    $this->load->model('WorkShopdataModel');
	    $this->load->model('ScSheetModel');
	    $this->load->model('staffMainModel'); 
	     $this->load->model('ScgxRemarkModel');
	    $factoryCheck=$this->config->item('factory_check');
	    
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	    
	    $qtycolor   =$this->colors->get_color('qty');
	    $black      =$this->colors->get_color('black');
	    $lightgreen =$this->colors->get_color('lightgreen');
	    $superdark =$this->colors->get_color('superdark');
	    $purple  =$this->colors->get_color('purple');
	   
	    $completeImg='';
	    $actions    =array();
	    switch($segmentIndex){
	        case 0:
			    $actions = $this->MenuAction;
			    break;
			case 1:
			    if ($scLine!='') {
			        $actions  = $this->pageaction->get_actions('operate');//操作
			        $listArray= $this->pageaction->get_actions('settasks,remark');
			        $addlists = $this->pageaction->get_actions('allot');
			        if (count($addlists)>0){
			             $addlists[0]['Name']='更改拉线';
		                 array_push($listArray,$addlists[0]);
		            }
		            
		            $addlists = $this->pageaction->get_actions('register');
		            if (count($addlists)>0){
			             $addlists[0]['Name']='生产登记';
		                 array_push($listArray,$addlists[0]);
		            }
		            
		            $actions[0]['list']=$listArray;
			     }else{
				     $actions = $this->MenuAction;
			     }
			    break;
			case -1:
			     $rowArray =$this->ScSheetModel->get_scing_list($wsid,$actionid);
			    break;
			case 4:
			    $rowArray   =$this->ScSheetModel->get_stockin_list($wsid,$actionid);
			    $completeImg='flag_gray'; 
			    break;
		}
		
		
		if ($segmentIndex!=4 && $segmentIndex!=-1){
			$checkSign=$this->get_checkSign($segmentIndex);
		    $rowArray =$this->ScSheetModel->get_canstock_list($wsid,$actionid,$checkSign,$scLine);
		}
		
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$this->load->model('ProductdataModel');
		$this->load->model('ScCjtjModel');
		
		$this->load->library('dateHandler');
		$nowdateTime = strtotime($this->DateTime) ;

		$hour12 = 3600 *12;
		$twoDay = $hour12 * 4;
		$beling = '';
		$this->load->model('ScCurrentMissionModel');
		$sPOrderIds =$this->ScCurrentMissionModel->get_all_records();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $processArray=array();
		    
		    $TestStandardState = $this->ProductdataModel->get_teststandard_state($rows['POrderId']);
		    
		    $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
		    $shipImg   =$rows['ShipType']>0?'ship' .$rows['ShipType']:''; 
		   // $cnameColor=$rows['TestStandard']>1?$this->ProductdataModel->get_cname_color($rows['TestStandard']):$black;
           $cnameColor = $TestStandardState ==1?$superdark:$purple;
           if (isset($rows['Operator'])){
               $Operator=$this->staffMainModel->get_staffname($rows['Operator']);
	           //$Operator=$rows['TestStandard']==2?$this->staffMainModel->get_staffname($rows['Operator']):'';
           }else{
	           $Operator='';
           }
             
            $createdtime =  $rows['created'];
            
            
		   if ($segmentIndex==0){
			   if ($this->LoginNumber == 11965) {
				   $rows['TestStandard']  = 3;
			   }
		           $dataArray[]=array(
					    'type'       =>$wsid,
					    'segIndex'   =>$segmentIndex,
						'tag'        =>'zorder',
						'Id'         =>$rows['sPOrderId'],
						'showArrow'  =>'1',
						'open'       =>'0',
						'actions'    =>($rows['TestStandard']==1 && $TestStandardState==1 && $rows['Line']=='')?$actions:array(),
						'arrowImg'   =>'UpAccessory_gray',
						'POrderId'   =>$rows['POrderId'],
						'ProductId'  =>$rows['ProductId'],
						'Picture'    =>$rows['TestStandard']==1 && $TestStandardState==1?1:0,
						'standard'   =>$rows['TestStandard'].'',
						'waitBeling'=>array('beling'=>($rows['TestStandard']>1) ?'1':'1',
							'blingVals' =>array(1,0.55,0.15,0.55,1),
							'belingtime'=>'3'),
					    'productImg' =>$productImg,
					    'shipImg'    =>$shipImg,
					    'line'       =>($scLine=='' && isset($rows['Line']))?($rows['Line']==''?'':$rows['Line']):'',
						'week'       =>$rows['LeadWeek'],
						'title'      =>array('Text'=>$rows['cName'],'Color'=>"$cnameColor"),
						'created'    =>array('Text'=>$rows['OrderDate'],'DateType'=>'day'),
						'Operator'   =>$Operator,
						'col1'       =>array('Text'=>$rows['Forshort'],'Color'=>"$superdark",'light'=>'11'),
						'col2'       =>$rows['OrderPO'],
						'col2Img'    =>'',
						'col3'       =>array('Text'=>number_format($rows['Qty']),'Color'=>"$superdark"),
						'col3Img'    =>'scdj_11',
						'col4'       =>$factoryCheck==1?'':array('Text'=>$createdtime,'DateType'=>'time'),
						'completeImg'=>$completeImg,
						'inspectImg' =>''
					);
		   }
		   else{
			   
			    $beling = '';
			   
		            if (strpos($sPOrderIds,$rows['sPOrderId'])){
			            $inspectImg='coding';
// 			            $beling = '2';
		            }
		            else{
			            $inspectImg=''; 
		            }
		            $scQty=$this->ScCjtjModel->get_scqty($rows['sPOrderId']);
		            $scQty=$scQty>0?number_format($scQty):'';
		            
		            $remark='';
/*
		            if (isset($rows['Remark'])){
			            $remark=trim($rows['Remark']);
			            if ($remark=='' || $remark=='新增业务订单' || $remark=='新增重置' || $remark=='生产工单设置更新')
			            {
				           $remark=''; 
			            }
		            }
*/
		            $modified = element('modified',$rows,'');
		            $modifier = element('modifier',$rows,'');
		            $remarkquery = $this->ScgxRemarkModel->get_remark(element('sPOrderId',$rows,'0'));
		            if ($remarkquery->num_rows() > 0) {
			            $remarkRow = $remarkquery->row_array();
			            $remark = $remarkRow['Remark'];
			            $modified =  $remarkRow['created'];
			             $modifier =  $remarkRow['creator'];
			            
			            //Remark,creator,created
		            }
		            
		            
		            $scedInterval = '';
		            $minus = 0;
		            
		            $needCompute = $scQty > 0 ? true : false;
		            
		            if ($needCompute) {
			            $lasttime=strtotime($this->ScCjtjModel->get_begin_time($rows['sPOrderId']));
			            $minus = floor($nowdateTime-$lasttime);
			            
			            if ($minus <= 30 * 60) {
				            $beling = '2';
			            }
			            $scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
		            }
		            $timeColor = '#727171';
				    if ($minus > $twoDay)
			        {
				        $timeColor = '#ff0000';
			        } else if ($minus > $hour12) {
				        $timeColor = '#ff9946';
			        }
             
             
					$col4 = $needCompute ? array('Text'=>$scedInterval,'Color'=>$timeColor) : array('Text'=>$createdtime,'DateType'=>'time','Color'=>$timeColor);
							
		            $blingVals = array(1,0.35,0.01,0.35,1,1);
		            $datas =array(
					    'type'       =>$wsid,
					    'segIndex'   =>$segmentIndex,
						'tag'        =>'zorder',
						'Id'         =>$rows['sPOrderId'],
						'showArrow'  =>'1',
						'open'       =>'0',
						'actions'    =>($segmentIndex==1 && $rows['Line']!='' && $scLine=='')?array():$actions,
						'arrowImg'   =>'UpAccessory_gray',
						'POrderId'   =>$rows['POrderId'],
						'ProductId'  =>$rows['ProductId'],
						'Picture'    =>$rows['TestStandard']==1?1:0,
					    'productImg' =>$productImg,
					    'shipImg'    =>$shipImg,
					    'line'       =>($scLine=='' && isset($rows['Line']))?($rows['Line']==''?'':$rows['Line']):'',
						'week'       =>$rows['LeadWeek'],
						'title'      =>array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>$rows['Forshort'] . "-",'Color'=>"$qtycolor",'FontSize'=>"12"),
									      array('Text'=>$rows['cName'],'Color'=>"$superdark",'FontSize'=>"12")
									   )
									 ),
						'created'    =>array('Text'=>$rows['OrderDate'],'DateType'=>'day'),
						'Operator'   =>$Operator,
						'col1'       =>array('Text'=>$rows['OrderPO'],'Color'=>"$superdark",'light'=>'11'),
						'col2'       =>number_format($rows['Qty']),
						'col3'       =>array('Text'=>$scQty,'Color'=>$beling!=''?'#01a02c': "$lightgreen",'beling'=>$beling,'belingtime'=>'4','FontWeight'=>$beling!=''?'light':'light','FontSize'=>'12','blingVals'=>$blingVals),
						'col3Img'    =>'scdj_12',
						'col2Img'    =>'scdj_11',
						'col4'       =>$factoryCheck==1?'':$col4,
						'completeImg'=>$completeImg,
						'inspectImg' =>$inspectImg,
						'rem'     =>$remark ,
						'modified' =>$modified,
						'modifier' =>$modifier,
						'sctime'     =>$minus,
						'_Line' => element('Line',$rows,'')
					);
					
					if (strpos($sPOrderIds,$rows['sPOrderId'])){
			            array_unshift($dataArray,$datas);
		            }
		            else{
			            $dataArray[]=$datas;
		            }
				}
		}
		
		
		if ($segmentIndex == -1 ) {
			usort($dataArray, function($a, $b) {
	            $al = ($a['sctime']);
	            $bl = ($b['sctime']);
	            if ($al == $bl)
	                return 0;
	            return ($al > $bl) ? -1 : 1;
	        });
	        
	        
	        

		}
		
		$dataArrayTrue = array();
		
		$inerter = 0;
        foreach ($dataArray as $rows) {
	        
	        $remark = element('rem',$rows,'');
	        $spid = element('Id',$rows,'');
	        if ($remark!=''  && $remark!='新单重置' ) {
		        
		        
		        $rows['hideLine'] = '1';
		        $modifier = element('modifier',$rows,'');
		        $modified = element('modified',$rows,'');
		        $_line = element('_Line',$rows,'');
		        $operator=$this->staffMainModel->get_staffname($modifier);   
				 $times =  $this->GetDateTimeOutString($modified,$this->DateTime);
				 $remarkArray=array(
							'tag'      =>'remark2',
							'content'   =>$remark,
							'img'=>'line_'.$_line,
							'margin_left'=>'60',
							'oper' =>$times . ' '. $operator,
							'bgcolor'  =>'#FFFFFF'
			         );
			         
/*
			         if ($this->LoginNumber == 11965 && $spid !='') {
				         $paramt = array('rmk'=>$remark,
				         				 'time'=>$modified,
				         				 'oper'=>$modifier,
				         				 'id'=>$spid,
				         				 'date'=>date('Y-m-d',strtotime($modified))
				         				 );
					    $this->ScgxRemarkModel->save_item_auto($paramt);
			         }
*/
			         
			         $inerter ++;
				 $dataArrayTrue[]=$rows;
				$dataArrayTrue[]=$remarkArray;
	        } else {
		        $dataArrayTrue[]=$rows;
	        }
	        
	        
        }
        
        return $dataArrayTrue;
		        
		        
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
	   $this->load->model('ProductdataModel');
	   
	   $this->load->library('dateHandler');
	   	$dataArray = array();
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
		             
		             $yw_records = $this->YwOrderSheetModel->get_records($records['POrderId']);
		              $productImg=$yw_records['TestStandard']==1?$this->ProductdataModel->get_picture_path($yw_records['ProductId']):'';
			         $OrderPO= $yw_records['OrderPO'];
			         
			         $scQtyRow = $this->ScCjtjModel->get_order_scqty($records['POrderId'], 1);
			         
			         
			         
			         $lineName = $scQtyRow['line'];
		            
		              $dataArray[]=array(
							    'type'       =>$wsid,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'zorder',
								'showArrow'  =>'0',
								'open'       =>'0',
								'Id'         =>$records['sPOrderId'],
								'TestStandard'  =>''.$yw_records['TestStandard'],
								 'productImg' =>$productImg,
								 'Picture'=>$yw_records['TestStandard']==1 ? '1':'',
								//'iconImg'    =>$stuffImg,
								'ProductId' =>$yw_records['ProductId'],
								'week'     =>$yw_records['Leadweek'],
								'title'       =>$yw_records['cName'],
								'col1Img' =>'',
								'col1'       =>'' . $OrderPO,
								'col2Img'  =>'',
								'line'=>''.$lineName,
								'col2'        => '',
								'col3Img'  =>'scdj_11',
								'col3'         =>array('Text'=>number_format($records['Qty']),'Color'=>$black) ,
								'inspectImg' =>'',
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


// 		  if ($this->LoginNumber == 11965) {
	
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
	   $this->load->model('ScCjtjModel');
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
		             
		             $yw_records = $this->YwOrderSheetModel->get_records($records['POrderId']);
		              $productImg=$yw_records['TestStandard']==1?$this->ProductdataModel->get_picture_path($yw_records['ProductId']):'';
			         $OrderPO= $yw_records['OrderPO'];
			         
			         $scQtyRow = $this->ScCjtjModel->get_order_scqty($records['POrderId'], 1);
			              
			         $lineName = $scQtyRow['line'];
		            
		              $dataArray[]=array(
							    'type'       =>$wsid,
							    'segIndex'   =>$segmentIndex,
								'tag'        =>'zorder',
								'showArrow'  =>'0',
								'open'       =>'0',
								
								'POrderId'=>$records['POrderId'],
								'Id'         =>$records['sPOrderId'],
								'TestStandard'  =>''.$yw_records['TestStandard'],
								 'productImg' =>$productImg,
								 'line'=>''.$lineName,
								 'Picture'=>$yw_records['TestStandard']==1 ? '1':'',
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
								'inspectImg' =>'',
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
	

		
	public function djRecord() 
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
		
		$scedqty= $this->ScCjtjModel->get_scqty($sPOrderId);
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $Operator=$this->StaffMainModel->get_staffname($rows['Operator']);
		    
		    //标签打印设置 
		   $actions=array();
		   //$prints=null;
		  // $prints=$this->LabelPrintModel->get_gxregister_print($sPOrderId,$rows['Qty']);
		   //$actions=$this->PrintAction;
		   //$actions[0]['data']=$prints; 
		   
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
	

	
	public function get_subList_stuff($sPOrderId,$segmentIndex){
	    
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		$this->load->model('stuffpropertyModel');
		
		$qtycolor=$this->colors->get_color('qty');
		$black   =$this->colors->get_color('black');
		$superdark   =$this->colors->get_color('superdark');
		 
		$rowArray=$this->ScSheetModel->get_stuff_stocksheet($sPOrderId);
		$rownums =count($rowArray);
		
		$mySupplier=explode(',', $this->getSysConfig(106));
		$semiType  =$this->getSysConfig(103); //半成品类型
		$dataArray=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
		    $Propertys =$this->stuffpropertyModel->get_property($rows['StuffId']); 
		    
		    $col3color=$rows['OrderQty']==$rows['llQty']?"$qtycolor":"$superdark";
		    $half=($semiType==$rows['mainType'] && in_array($rows['CompanyId'], $mySupplier))?1:0;
		    
		    $location = $rows['OrderQty']==$rows['llQty'] && $rows['llEstate']==0?"":$rows['location'];
	    	$halfImg = '';
			
			$checkBom = $this->ScSheetModel->semi_bomhead($rows['StockId']);
			if ($checkBom->num_rows() > 0) {
				$halfImg = 'halfProd';
			}
			
		  $actions=array();
		    if ($segmentIndex==-1){
			   $actions=$this->pageaction->get_actions('feeding');
		    }
		    
		    $stuffRow = $this->stuffdataModel->get_records($rows['StuffId']);
		    $Decimals = element('Decimals', $stuffRow, '0');

			$col3Attr = array(
						   		'isAttribute'=>'1',
						   		'attrDicts'=>array(
							   		array('Text'    =>($rows['llEstate']>0 && $rows['blSign']>0)?'★':'   ',
							   			  'FontSize'=>'12',
							   			  'FontWeight'=>'bold',
							   			  'Color'   =>"$col3color"),
							   		array('Text'    =>number_format($rows['llQty'],$Decimals),
							   			  'FontSize'=>'12',
							   			  'Color'   =>"$col3color")
							   		)
						   		);
		    
			$dataArray[]=array(
			    'type'       =>'',
			    'segIndex'   =>'',
				'tag'        =>'stuff',
				'actions'  =>$actions,
				'half'       =>"$half",
				'Id'         =>$rows['sPOrderId'],
				'StockId'    =>$rows['StockId'],
				'title'          =>$rows['StuffCname'],
				"Property" =>$Propertys,
				'col1'       =>number_format($rows['OrderQty'],$Decimals),
				'col2'       =>number_format($rows['tStockQty'],$Decimals),
				'col3Img'    =>'',
				'col3'       =>$col3Attr,
				'location'=>$location,
				'col4'       =>$rows['ForName'],
				'completeImg'=>'',
				'Picture'    =>$rows['Picture'],
				'stuffImg'   =>$stuffImg,
				'wsImg'      =>'ws_'.$rows['wsId'],
				'halfImg'    =>$halfImg
			);
		}
        return $dataArray;
	}
	
	//待入库总数
	function drkValue()
	{
	    $this->load->model('ScSheetModel');
	    
	    $rkQty=$this->ScSheetModel->get_stockin_qty($this->WorkShipId);
		
		$status = array('val'=>number_format($rkQty));
		
		 {
			  $beling=$this->ScSheetModel->get_drk_blings($this->WorkShipId);
			 
			$status['beling']=$beling.'';
			$blingVals = array(1,0.65,0.3,0.65,1);
			$status['blingVals']=$blingVals;
		}
		
		
		
		$data['jsondata']=array('status'=>$status,'message'=>number_format($rkQty),'totals'=>1,'rows'=>array());
		$this->load->view('output_json',$data);    			

	}
	
	
	
	
	public function get_location()
	{
	    
	    $params = $this->input->post();
	    $Sid    = element("Id",$params,'');
	    $Ids    = element("Ids",$params,'');
	     
	    $this->load->model('ScSheetModel');
	    $this->load->model('CkrksheetModel');
	    $this->load->model('StuffdataModel');
	    
	    $this->load->model('ProductdataModel');
	    $this->load->model('OrderSheetModel');
	    
	    
	    $recordsa =
	    $records = $this->ScSheetModel->get_records($Sid);
	    $Floor   = 3;
	    $StuffId = '';
	    $ProductId = $this->OrderSheetModel->get_productid($records['POrderId']);
	    
	    $records = null;
	    
	    $locations = '';
	    $this->load->model('YwOrderRkModel');

		$records=$this->YwOrderRkModel->get_product_location($ProductId);

	    

	    
	    foreach ($records as $row){
	       $rkloc     = '未设置';
	       $rkQty     = number_format($row['rkQty']);
	       
	       if ($row['Identifier']!=''){
		      $idents=explode('-', $row['Identifier']);
	          $rkloc=$idents[count($idents)-1]; 
	       }
	       
	       $locations .= $locations==''?$rkQty . "($rkloc)":"," . $rkQty . "($rkloc)";
	    }
	    

	    $this->load->model('CkLocationModel');
	     
	    $unrkRows = $this->ScSheetModel->get_stockin_list($recordsa['WorkShopId'],$recordsa['ActionId'],$Sid);
	    $unrkQty = $unrkCounts = '';
	    if (count($unrkRows) >= 1) {
		    $unrkQty = $unrkRows[0]['rkQty'];
		    
		    $rkQty = $unrkQty;
			$relation = $this->ProductdataModel->get_box_relation($unrkRows[0]['POrderId'],$unrkRows[0]['ProductId']);
			if ($relation>0 && $rkQty>0) {
				$unrkCounts = ceil($rkQty/$relation); 
				
			}
	    }
	    

    
	  
	    
        $dataArray=$this->CkLocationModel->get_locations('','','2');
	    $status = array('zaiku' =>$locations,
	    				'qty'   =>"$unrkQty",
	    				'frameImg'=>'frame_box',
	    				'frameNum'=>"$unrkCounts");
	    
	   $data['jsondata']=array('status'=>$status,'message'=>'','user'=>'','rows'=>$dataArray);
	    
		$this->load->view('output_json',$data);
	}
	
		
	//待入库主页
	public function scedlist(){
	
	    $factoryCheck=$this->config->item('factory_check');
	    
	    $qtycolor=$this->colors->get_color('qty');
		$black   =$this->colors->get_color('black');
		$superdark=$this->colors->get_color('superdark');
		$lightgreen =$this->colors->get_color('lightgreen');
		
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($this->WorkShipId);
	    
	    $actions = $this->pageaction->get_actions('stockin');//入库

		$this->load->model('ScSheetModel');
		$rowArray   =$this->ScSheetModel->get_stockin_list($this->WorkShipId,$actionid);
		$completeImg='';
		
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$this->load->model('ProductdataModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('YwOrderRkModel');
		$overqty=0; $sumqty=0;
		$versionNum = $this->versionToNumber($this->AppVersion);
		$listdatas=array();
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $processArray=array();
		    
		    $productImg=$rows['TestStandard']==1?$this->ProductdataModel->get_picture_path($rows['ProductId']):'';
		    $shipImg   =$rows['ShipType']>0?'ship' .$rows['ShipType']:''; 
		    $cnameColor=$rows['TestStandard']>1?$this->ProductdataModel->get_cname_color($rows['TestStandard']):'';
		    
		    $completeImg=$rows['Qty']==$rows['scQty']?'flag_gray':'';
		    $rkcolor   =$rows['Qty']==$rows['scQty']?$lightgreen:$qtycolor;
			$adata=array(
			    'type'       =>$this->WorkShipId,
			    'segIndex'   =>0,
				'tag'        =>'zorder',
				'Id'         =>$rows['sPOrderId'],
				'showArrow'  =>'1',
				'open'       =>'0',
				'actions'    =>$actions,
				'arrowImg'   =>'UpAccessory_gray',
				'POrderId'   =>$rows['POrderId'],
				'ProductId'  =>$rows['ProductId'],
				'Picture'    =>$rows['TestStandard'],
			    'productImg' =>$productImg,
			    'shipImg'    =>$shipImg,
			    "line"       =>isset($rows['Line'])?($rows['Line']==''?'':$rows['Line']):'',
				'week'       =>$rows['LeadWeek'],
				'title'      =>array(
									'isAttribute'=>'1',
									'attrDicts'  =>array(
									      array('Text'=>$rows['Forshort'] . "-",'Color'=>"$qtycolor",'FontSize'=>"12"),
									      array('Text'=>$rows['cName'],'Color'=>$superdark,'FontSize'=>"12")
									   )
									),
				'created'    =>array('Text'=>$rows['OrderDate'],'DateType'=>'day'),
				'col1'       =>array('Text'=>$rows['OrderPO'],'Color'=>$superdark,'light'=>'11'),
				'col2'       =>number_format($rows['Qty']),
				'col3'       =>array('Text'=>number_format($rows['rkQty']),'Color'=>"$rkcolor"),
				'col3Img'    =>'scdj_12',
				'col4'       =>$factoryCheck==1?'':array('Text'=>$rows['created'],'DateType'=>'time'),
				'completeImg'=>$completeImg,
				'inspectImg' =>''
			);
			 {
				
				$rkQty = $rows['rkQty'];
				$relation = $this->ProductdataModel->get_box_relation($rows['POrderId'],$rows['ProductId']);
				
				
				
				if ($relation>0 && $rkQty>0) {
					$adata['frameNum'] = ceil($rkQty/$relation);
					$adata['frames_r'] = 2;
					$adata['frameImg'] = 'frame_box';
					$adata['addedTime'] = '';
					$adata['is_10']=1;
					$adata['addedHeight'] = '8';
					
					
				}
				
				$qtyinstock = $this->YwOrderRkModel->get_region_productqty($rows['ProductId'],'');
				
				if ($this->LoginNumber == 11965 && $qtyinstock<=0) {
				$qtyinstock = '2000';	
				}
					
					$adata['in_stuff'] = array(
				    	'Text'=>$qtyinstock>0?(number_format(intval($qtyinstock))):'',
				    	'Color'=>'#01be56',
// 				    	'FontSize'=>'12',
'beling'=>'1',
				    	'OutLineColor'=>'#FFFFFF',
				    	'OutLine'=>'3',
				    	'lbl_alpha'=>'1',
	                );

				
				
				
			}
			$listdatas[]=$adata;
			
			
			$sumqty+=$rows['rkQty'];
			
			if ($rows['LeadWeek'] <$this->ThisWeek)
			{
				$overqty+=$rows['rkQty'];
			}
		}
		
		$red     =$this->colors->get_color('red');
		$totalArray=array(
				"tag"=>"wtotal",
				"col1"=>"合计",
				"col2"=>array('Text'=>number_format($overqty),'Color'=>"$red"),
				"col3"=>number_format($sumqty)
			);
	    
	    array_unshift($listdatas,$totalArray);
	
		$dataArray[]=array('data'       =>$listdatas);
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
   
   //生产线选择
   public function top_seg() {
		$params = $this->input->post();
		
		$dataArray=array();
		$this->load->model('WorksclineModel');
		$rowsArray=	$this->WorksclineModel->get_scline($this->WorkShipId);
		
		$this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($this->WorkShipId);
	    
		$this->load->model('ScSheetModel');
		$linedatas=$this->ScSheetModel->get_canstock_qty($this->WorkShipId,$actionid,'Line');
		$linenums=count($linedatas);
		$linerows=array();
		 for ($j = 0; $j < $linenums; $j++) {
		       $lines=$linedatas[$j];
		       $scLineId=$lines['scLineId'];
		       $linerows[$scLineId]=$lines['Counts'];
		 }
		    
		$rownums =count($rowsArray);
		
		$lineId=0; $n=0;
		for ($i = 0; $i < $rownums; $i++) {
		    $rows   = $rowsArray[$i];
		    $lineId = $rows['Id'];
		    $rows['title'] =$rows['line'];
		    
		    if (isset($linerows[$lineId])){
			    $rows['amount']=isset($linerows[$lineId])?$linerows[$lineId]:'--';
			    $lineId=$lineId==''?($rows['GroupLeader']==$this->LoginNumber?$i:''):$lineId;
		        $dataArray[$n] =$rows;
		        $n++;
		    }
		}
		
		
	
		$data['jsondata']=array('status'=>'1','message'=>"$lineId",'totals'=>1,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	//生产线按组显示
	public function scinglist(){
	    $params = $this->input->post();
	    $lineId   = element('top_segId',$params,'0');
	        
	    $segmentIndex=1; //指定值
       
	    $listdatas=$this->get_subList_order($this->WorkShipId,$segmentIndex,$lineId);
		$totals=count($listdatas);
		
		$dataArray[]=array('data'       =>$listdatas);
		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	public function optionlist(){
		$params = $this->input->post();
		$upTag  = element("upTag",$params,'');
		
		$this->load->model('WorksclineModel');
		$dataArray=	$this->WorksclineModel->get_scline($this->WorkShipId);
		
		 $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);
	}
	
	public function sortboxlist(){
		$params = $this->input->post();
		$upTag  = element("upTag",$params,'');
		
		$this->load->model('WorksclineModel');
		$dataArray=	$this->WorksclineModel->get_scline_sortbox($this->WorkShipId);
		
		 $data['jsondata']=array('status'=>'1','message'=>'','totals'=>1,'rows'=>$dataArray);
		 $this->load->view('output_json',$data);
	}

	

	//分配拉线保存
	public function allot()
	{
		$params   = $this->input->post();
		$action   = element('Action',$params,'allot');
		$sPOrderId= element('Id',$params,'0');
		$lineId   = element('lineId',$params,'0');
	
	    $status=0;
	    $rowArray=array();
	    if ($action=='allot' && $sPOrderId!='' &&  $lineId>0)
	    {
		    $this->load->model('ScMissionModel');
		    $status=$this->ScMissionModel->set_scline($sPOrderId,$lineId);
		    
		    if ($status==1){
		        $this->load->model('worksclineModel');
		        $records=$this->worksclineModel->get_records($lineId);
		        $line   =$records['Letter'];
			    $rowArray=array(
			            'line' =>"$line"
			          );
		    }
	    }
	    $dataArray=array("data"=>$rowArray);
	    
	    $message=$status==1?'设置成功！':'设置失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	//待入库确认
	public function stockin()
	{
		$params   = $this->input->post();
		$action   = element('Action',$params,'');
		$sPOrderId= element('Id',$params,'0');
		$LocationId= element('LocationId',$params,'0');
	
	    $status=0;
	    $rowArray=array();
	    if ($sPOrderId!='')
	    {
	        $this->load->model('ScCjtjModel');
	        $scQty=$this->ScCjtjModel->get_scqty($sPOrderId);//生产数量
	        
	        $this->load->model('YwOrderRkModel');
	        $rkQty=$this->YwOrderRkModel->get_rkqty($sPOrderId);//入库数量
	        
	        $this->load->model('ScSheetModel');
	        $records = $this->ScSheetModel->get_records($sPOrderId);
	        
	        $Qty=$records['Qty'];
	        if ($Qty==$scQty){
	           //生产数量等于工单数量，直接更新工单状态入库
		       $status=$this->ScSheetModel->update_estate($sPOrderId,0);
	        }
	        else{
		       if ($rkQty<$scQty){
		          $POrderId=$records['POrderId'];
		          $StockId =$records['StockId'];
		          $this->YwOrderRkModel->save_records($sPOrderId,$POrderId,$StockId,$LocationId);
		          
		          $rkQty=$this->YwOrderRkModel->get_rkqty($sPOrderId);//入库数量
		          $status=$scQty==$rkQty?1:0;
	           } 
	        }
	        
		    if ($status==1){
			    $rowArray=array(
			            'Action' =>'delete' 
			          );
		    }
	    }
	    
	    $message=$status==1?'保存入库状态成功！':'保存入库状态失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$rowArray);
		$this->load->view('output_json',$data);
	}
    
    //设置当前任务
    public function settasks()
    {
        $params = $this->input->post();
		$action      = element('Action',$params,'');
		$sPOrderId   = element('Id',$params,'');
		$upTag       = element('upTag',$params,'');
        $lineId      = element('lineId',$params,'');
        $sortboxId   = element('sortBoxId',$params,'');
        
	   $status=0;
	   $rowArray=array();
	   $actions=array();
	   if ($sPOrderId!='' && $upTag=='scinglist')
	   {
	        $this->load->model('worksclineModel');
			$records=$this->worksclineModel->get_records($lineId);
		    $line   =$records['Letter'];
		        
		    $this->load->model('ScCurrentMissionModel');
		    $status=$this->ScCurrentMissionModel->set_currentscline($sPOrderId,$lineId,$sortboxId,$line);
		    if ($status>0){
			   //设置喷码参数
			    $status=1;
			    $actions=$this->get_action_markemset($sPOrderId,$lineId,$sortboxId);
			   
		        //刷新检验标准图TV
		        $refreshTvs=$this->pageaction->get_actions('refreshtv');
		        $this->load->model('OtdisplayModel');
		        $tvips=$this->OtdisplayModel->get_packaging_tvip($line . '-pic');
		        $tvips[0]['data']=$sPOrderId;
		        $refreshTvs[0]['tv_IP']=$tvips;
		        if (count($tvips)>0)
		             $actions[]=$refreshTvs[0];
		        
			    $rowArray=array(
			            'line' =>"$line"
			          );		    
			 }
	    }
	    
	    $dataArray=array(
		            'actions'=>$actions,
		            'data'   =>$rowArray
		            );
		            
        $message=$status==1?'设置当前任务成功！':'设置当前任务失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'1','rows'=>$dataArray);
		$this->load->view('output_json',$data);
    }
    
    //生产备注信息
    public function remark()
    {
        $params   = $this->input->post();
		$action   = element('Action',$params,'');
		$sPOrderId= element('Id',$params,'0');
	    $remark   = element('remark',$params,'0');
	    
	    $status=0;
	    $rowArray=array('n'=>'');
	    $newaction = '';
	    $newdata = array('n'=>'');
	    if ($action=='remark' && $sPOrderId!='')
	    {
		    $this->load->model('ScSheetModel');
		    $status=$this->ScSheetModel->set_remark($sPOrderId,$remark);
			 $this->load->model('ScgxRemarkModel');
		    $status=$this->ScgxRemarkModel->save_item($params);
		    if ($status==1){
			   
			     $rowArray=array(
			        'hideLine'=>'1'
			    );
			    $newaction = 'insert';
			    $get_line_letter = $this->ScSheetModel->get_line_letter($sPOrderId);
			    $this->load->model('StaffMainModel');
			    $operator=$this->StaffMainModel->get_staffname($this->LoginNumber);
			    $newdata = array(
			    				'tag'=>'remark2',
			    				'content'=>$remark,
			    				'img'=>'line_'.$get_line_letter,
			    				'oper'=>'1分前 '.$operator,
			    				'margin_left'=>'60',
			    				'bgcolor'  =>'#FFFFFF'
			    				 );
  
			}
	    }
	 
		            
		 $dataArray=array("data"=>$rowArray,'Action'=>$newaction,'newdata'=>$newdata);
	    
	    $message=$status==1?'保存备注信息成功！':'保存备注信息失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);
    }
    
    //生产登记
    public function register()
    {
	    $params   = $this->input->post();
		$action   = element('Action',$params,'');
		$sPOrderId= element('Id',$params,'0');
	    $Qty      = element('Qty',$params,'0');
	    
	    $status=0;
	    $rowArray=array();
	    if ($action=='register' && $sPOrderId!='')
	    {
		    $this->load->model('ScCjtjModel');
		    $status=$this->ScCjtjModel->save_records($params);
		    if ($status==1){
			    $rowArray=array(
			            'col3' =>number_format($Qty),
			          );
		    }
	    }
	    
	    $dataArray=array(
		            'data'   =>$rowArray
		            );
	    
	    $message=$status==1?'生产登记保存成功！':'生产登记保存失败!';
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$status,'rows'=>$dataArray);
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
    
    //获取喷码设置内容
	public function get_action_markemset($sPOrderId,$lineId,$sortboxId){
	
	    $this->load->model('markemsetModel');
        $markemsets[]=$this->markemsetModel->get_markem_setting($sPOrderId,$lineId,$sortboxId);
        
        $actions=$this->pageaction->get_actions('markemset');
        $actions[0]['data']= $markemsets; 
        
        return $actions;
	}
    

	
	function get_checkSign($segmentIndex)
	{
	    $checkSign=0;
		switch($segmentIndex){
		   case -1: $checkSign= 'SCZ';break;
		   case  0: $checkSign= 'DFP';break;
		   case  1: $checkSign= 'DSC';break;
		   case  2: $checkSign= 2;break;
		   case  3: $checkSign= 3;break;
		}
		return $checkSign;
	}

}
