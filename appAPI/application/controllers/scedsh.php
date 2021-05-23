<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scedsh extends MC_Controller {
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
        $isShadow = element('isShadow',$params,'0');
        
        $versionNum = $this->versionToNumber($this->AppVersion);
		$is415Version = $versionNum >= 415 ? true : false;
        
        if ($isShadow==1){ //翻转页面
           $this->segment();
		   return; 
		}
		
		
		$this->load->model('AppUserSetModel');
	    $types=$this->AppUserSetModel->get_parameters($this->LoginNumber,$this->SetTypeId);
       
        $this->load->model('WorkShopdataModel'); 
      
	    if ($types==''){
	    
		    $typesArray=$this->WorkShopdataModel->get_workshop(0,1); 
	    }
	    else{
		    $typesArray=$this->WorkShopdataModel->get_workshop(2,1,$types); 
	    }
	    
	    $numsOfTypes=count($typesArray); 
	    
		$this->load->model('ScSheetModel'); 
		$this->load->model('ScCjtjModel');
		$this->load->model('staffMainModel'); 
		
		$qtycolor=$this->colors->get_color('qty');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$lightgreen =$this->colors->get_color('lightgreen');
		$lightgray  =$this->colors->get_color('lightgray');
		
		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');
		
		$totals=0;
        $dataArray  = array();
		$dataArray[]=array('hidden'=>''); 
		$thisMonth = date('M');
		for ($i = 0; $i < $numsOfTypes; $i++) {
		
		   $oneTypes=$typesArray[$i];
		   if ($oneTypes['selected']==0) continue;
           //$nosendqty  =$this->ScSheetModel->get_nosend_qty($oneTypes['Id']);
	       $monthqty   =$this->ScSheetModel->get_month_qty($oneTypes['Id']);
	        
		   $month_output   =$this->ScCjtjModel->get_workshop_month_output($oneTypes['Id']);
		   
		   $groupnums=$oneTypes['GroupId']==''?0:$this->staffMainModel->get_staffTotals('GroupId',$oneTypes['GroupId']);
		   $day_valuation=$groupnums*$laborCost*$worktime;
		   $month_valuation=$day_valuation*25;//需更改
		  
		   
		   $percent=$this->ScSheetModel->get_scsheet_punctuality($oneTypes['Id']);
		   
		   $percentcolor=$percent>=85?$lightgreen:$red;
		   if ($monthqty==0){
			   $percentcolor=$lightgray;
			   $percent='--';
		   }
   
  
		   $listdatas=$this->get_subList_order($oneTypes['Id'],-1);
		   $nosendqty=$this->NoSendQty;
		   
		   if ($is415Version) {
			   $dataArray[]=array(
				'tag'        =>'sced',
				'type'       =>$oneTypes['Id'],
				'hidden'     =>'0',
				'segIndex'   =>'-1',
				'method'     =>'segment',
				'title'      =>$oneTypes['title'],
				'titleImg'   =>$oneTypes['headImage'],
				'amount'     =>''.number_format($nosendqty),
				"monthValue"  =>''.number_format($monthqty),
				'month'      =>"$thisMonth",		
				"chartValue" =>array(
							   		'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>"$percent",
								   			  'FontSize'=>'33',
								   			  'Color'   =>"$percentcolor",
								   			  'FontName'=>'AshCloud61'),
								   		array('Text'    =>'%',
								   			  'FontSize'=>'8',
								   			  'Color'   =>"$percentcolor",
								   			  'FontName'=>'AshCloud61')
								   		
								   		)
							   		),
				"pieValue"=>
					array(
						array("value"=>"$percent","color"=>"$percentcolor"),
						array("value"=>"".(100-$percent),"color"=>"clear")
					),
				'data'       =>$listdatas
		      );
		   } else {
			   $dataArray[]=array(
				'tag'        =>'sced',
				'type'       =>$oneTypes['Id'],
				'hidden'     =>'0',
				'segIndex'   =>'-1',
				'method'     =>'segment',
				'title'      =>$oneTypes['title'],
				'titleImg'   =>$oneTypes['headImage'],
				'amount'     =>array(
								'isAttribute'=>'1',
								'attrDicts'  =>array(
								      array('Text'=>number_format($nosendqty),
								             'Color'=>"$qtycolor",
								             'FontSize'=>"24"),
								      array('Text'=>"/" . number_format($monthqty),
								             'Color'=>"$black",
								             'FontSize'=>"11")
								   )
								),
				"monthValue"  =>array(
				                'isAttribute'=>'1',
								'attrDicts'  =>array(
								        array('Text'=>"¥" . number_format($month_output),
								              'Color'=>"$lightgreen",
								              'FontSize'=>"10"),
								        array('Text'=>"/¥" . number_format($month_valuation),
								              'Color'=>"$lightgray",
								              'FontSize'=>"10")
								   )
								),		
				"chartValue" =>array(
									array("$percent","$percentcolor","27","","regular"),
									array("%","$percentcolor","10")
								),
				"pieValue"=>
					array(
						array("value"=>"$percent","color"=>"$percentcolor"),
						array("value"=>"".(100-$percent),"color"=>"clear")
					),
				'data'       =>$listdatas
		      );
		   }
		  $totals++;
      }
       
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$totals,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
   }
	
	
	
	public function top_seg(){
	
	    $this->load->model('AppUserSetModel');
	    $params   = $this->input->post();
	    $selectType     = element('selectType',$params,'0');
	    $types=$this->AppUserSetModel->get_parameters($this->LoginNumber,$this->SetTypeId);
	    
	    $this->load->model('WorkShopdataModel');
		$typesArray=$this->WorkShopdataModel->get_workshop(2,1,$types);

		$numsOfTypes=count($typesArray); 
		$dataArray  = array();
		
		$totals=0;
		
		$message = 0;
		for ($i = 0; $i < $numsOfTypes; $i++) {
		   $oneTypes=$typesArray[$i];
		   if ($oneTypes['selected']==0) continue;
		   
		   if ($oneTypes['Id'] == $selectType) {
			   $message = $i;
		   }
		   
		   $dataArray[]=array(
				'tag'        =>'scedMonth',
				'method'     =>'segment',
				'hidden'     =>'0',
				'segIndex'   =>'0',
				'Id'         =>$oneTypes['Id'],
				'title'      =>$oneTypes['title'],
				'titleImg'   =>$oneTypes['headImage'],
				'img_0'      =>$oneTypes['img_0'],
			    'img_1'      =>$oneTypes['img_1']
			);
			$totals++;
		}
		 //输出JSON格式数据
		$data['jsondata']=array('status'=>'1','message'=>''.$message,'totals'=>$totals,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	
	}
	
	
	public function segment()
	{
		$params   = $this->input->post();
		$wsid     = element('top_segId',$params,'0');
		$type     = element('type',$params,'');//生产单位ID
		
		$this->load->model('ScSheetModel');
		$this->load->model('StaffMainModel');
		$this->load->model('WorkShopdataModel');
		
		$record = $this->WorkShopdataModel->get_records($wsid);
		$gpId = $record['GroupId'];
		
		$rowArray=$this->ScSheetModel->get_month_scsh($wsid);
		$rownums =count($rowArray);

		$totals=0;
        $dataArray  = array();
        $sortAmount = array();
		//$dataArray[]=array('hidden'=>''); 
		
		//
		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');
		$lastMon = '';
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $percent     = '';
		    $stateImg    = '';
		    $month       = $rows['Month'];
		    
		    if ($month == '') {

			    continue;
		    }
		    
		    $monthAmount = $rows['Amount'];
		    $monthQty    = $rows['Qty'];
		    
		    $percentcolor = '';
 
 $percent=$this->ScSheetModel->get_scsheet_punctuality($wsid,$month);  
			    
if ($percent > 90) {
	$percentcolor = '#358fc1';
} else {
	$percentcolor = '#ff0000';
}
		    
		    
		    $open = 0;
		    $subdata = array();
		    if ($i == 0) {
			    $open  = 1;
			    $subdata =$this->get_mon_subList($wsid,$month);
		    }
		    
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
						   		
			$dataArray[]=array(
			    'tag'         =>'scedMon',
			    'method'      =>'subList',
			    'type'        =>$wsid,
			    'segmentIndex'=>'1',
				'Id'          =>$month,
				'showArrow'   =>'1',
				'open'        =>''.$open,
				'data'        =>$subdata,
				'title'       =>$titleAttr,
				'col2'        =>'¥' . number_format($monthAmount,0),
				'col1'        =>number_format($monthQty),
				'charPercent' =>array(
							   		'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>"$percent",
								   			  'FontSize'=>'10',
								   			  'Color'   =>"$percentcolor"
								   			  ),
								   		array('Text'    =>'%',
								   			  'FontSize'=>'6',
								   			  'Color'   =>"$percentcolor"
								   			  )
								   		
								   		)
							   		),
				'pieValue'    =>array(
					array('value'=>''.$percent,'color'=>''.$percentcolor),
					array('value'=>''.(100-$percent),'color'=>'clear')
				),
				'marginR'     =>'10',
				'stateImg'    =>'',
				'faceImg'     =>''
			);
			
			$sortAmount[]=$monthAmount;
			$lastMon = $month;
		}


			//排序
		arsort($sortAmount,SORT_NUMERIC);
		$j=0;
		while(list($key,$val)= each($sortAmount)) 
		{
		    $j++;
		    $dataArray[$key]['faceImg']='face_1';
		    if ($j==6) break;
		}

		if ($rownums > 12) {
			asort($sortAmount,SORT_NUMERIC);
			$j=0;
			while(list($key,$val)= each($sortAmount)) 
			{
			    $j++;
			    $dataArray[$key]['faceImg']='face_2';
			    if ($j==6) break;
			}
		}


		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
	}
	
	public function get_mon_subList($wsid,$idMon) {
		$this->load->model('ScSheetModel');
		$this->load->model('StaffMainModel');
		$this->load->model('WorkShopdataModel');

		$laborCost=$this->config->item('standard_labor_cost');
		$worktime =$this->config->item('standard_work_hour');
		
		$rowArray = $this->ScSheetModel->get_day_scsh_inMon($wsid,$idMon);
		$record = $this->WorkShopdataModel->get_records($wsid);
		$gpId = $record['GroupId'];
		$rownums =count($rowArray);
		
		$dataArray = array();
		$lastMon = '';
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $percent     = '';
		    $stateImg    = '';
		    $date       = $rows['Date'];
		    
		    if ($date == '') {
continue;
			    $date = date('Y-m', strtotime("- 1 day",strtotime($lastMon.' 06:00:00')));
		    }
		    
		    $groupnums = $this->StaffMainModel->date_checkInNums_ingroup($gpId,$date);
		    $dayAmountNext = $groupnums *$laborCost *$worktime;
		    
		    $dateAmount = $rows['Amount'];
		    $dateQty    = $rows['Qty'];
		    
		    $percentcolor = '';
 

		    if ($dayAmountNext > 0) {
				if ($dayAmountNext >= $dateAmount) {
				    $percentcolor = '#ff0000';
				    $stateImg = 'arr_red';
				    $percent = round(($dayAmountNext-$dateAmount)/$dayAmountNext*100,0).'%';
			    } else {
				    $percentcolor = '#01be56';
				    $stateImg = 'arr_green';
				    $percent = round(($dateAmount-$dayAmountNext)/$dayAmountNext*100,0).'%';
			    }  
		    }
			    
			    
			$weekday = date('w',strtotime($date));
		    $dateCom = explode('-', substr($date, 5));
		    
			$dataArray[]=array(
			    'tag'         =>'scedDay',
			    'method'      =>'subList',
			    'type'        =>$wsid,
			    'segmentIndex'=>'1',
				'Id'          =>$date,
				'showArrow'   =>'1',
				'open'        =>'0',
				'title'       =>array('Text'=>substr($date, 5),'Color'=>($weekday==0 ||$weekday==6)?'#ff0000':'#9a9a9a','dateCom'=>$dateCom,'comImg'=>($weekday==0 ||$weekday==6)?'fmRed':'dateFm'),
				'col2'        =>'¥' . number_format($dateAmount,0),
				'col1'        =>number_format($dateQty),
				'stateImg'    =>'',
				'marginR'     =>'10'
			);
			$lastDate = $date;
		}

		return $dataArray;
	}
	
	public function get_day_subList($wsid,$iddate) {
		
		
		$black      =$this->colors->get_color('black');
	    $orange     =$this->colors->get_color('orange');

		$is415Version = true;
        
	    $this->load->model('WorkShopdataModel');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	    
		$this->load->model('ScSheetModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('ScGxtjModel');
		$this->load->library('dateHandler');
		
		$min30 = 60 *30;
		$hour12 = $min30*2 *4*3;
		$twoDay = $hour12 * 4;
		$nowdateTime = strtotime($this->DateTime) ;
		$rowArray=$this->ScSheetModel->get_sh_datesheet($wsid,$actionid,$iddate);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$this->load->model('StuffdataModel');
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $scqty = $rows['ScQty'];

		    $Qty = $rows['Qty'];
		    $boolFlag = $scqty>=$Qty?true : false;
			$completeImg=$boolFlag?'flag_over':'';
		    $processArray=array();

		    $scedInterval = '';
		    $minus = 0;
		    $sPorderId = $rows['sPOrderId'];
		    
		    if ($actionid==102){//皮套加工
			   $stockId=$rows['StockId'];  
			   $this->load->model('ProcessSheetModel');
			   $processArray=$this->ProcessSheetModel->get_sc_processlist($stockId,$sPorderId);
			   $beginTime = strtotime($this->ScGxtjModel->get_begin_time($sPorderId));
			   if ($beginTime != '') {
			   		$lastTime = $boolFlag ? strtotime($this->ScGxtjModel->get_last_time($sPorderId)) : $nowdateTime;
			   		
			   		$minus = floor($lastTime-$beginTime);
			   		$scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
			   		
			   }
			   
		    }else{
		      // $actions[0]['MaxQty']= $rows['Qty']-$rows['ScQty']; 
		      // $actions[0]['DjQty'] = $rows['ScQty'];
		      $beginTime = strtotime($this->ScCjtjModel->get_begin_time($sPorderId));
		        if ($beginTime != '') {
			   		$lastTime = $boolFlag ? strtotime($this->ScCjtjModel->get_last_time($sPorderId)) : $nowdateTime;
			   		
			   		$minus = floor($lastTime-$beginTime);
			   		$scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
			   		
			   }
		    }
		    
		      $timeColor = '#727171';
			    $completeImg = 'flag_gray';
			    if ($minus > $twoDay)
		        {
			        $completeImg = 'flag_red';
			        $timeColor = '#ff0000';
		        } else if ($minus > $hour12) {
			        $completeImg = 'flag_orange';
			        $timeColor = '#ff9946';
		        }
			   
			   
		    
		    $frameCapacity=$this->StuffdataModel->get_framecapacity($rows['StuffId']);
		    $nameColor=$rows['Picture']==1?$orange:$black;
		    
		    $stuffImg=$rows['Picture']==1?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';

			    $dataArray[]=array(
			    'type'       =>$wsid,
				'tag'        =>'order',
				'Picture'    =>''.$rows['Picture'],
				'iconImg'    =>$stuffImg,
				'Id'         =>$rows['sPOrderId'],
				'StuffId'    =>$rows['StuffId'],
			  'FrameCapacity'=>$frameCapacity,
			    'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'week'       =>$rows['DeliveryWeek'],
				'title'      =>$rows['StuffCname'],
				'created'    =>array(
					'Text'=>date('H:i',strtotime($rows['shDate'])),
					'Color'=>'#3b3e41'
				),
				'col1'       =>number_format($Qty),
				'col1Img'    =>'scdj_11',
				
				'col2'       =>
				array('Text'=>number_format($rows['shQty']),'Color'=>'#3b3e41'),
				
				'col2Img'    =>'scdj_12',
				'flagBtn'    =>'1',
				'col3'       =>array('Text'=>'¥'.round($rows['Price'],2),'Color'=>'#3b3e41'),
				'col3Img'    =>'',
				'flagBtn'    =>'1',
				'col4'       =>array('Text'=>''.$rows['Operator'],'Color'=>'#3b3e41'),
				'Process'    =>$processArray,
				'completeImg'=>$boolFlag?$completeImg:''
			);
		}
		
		return $dataArray;
	}
	public function get_subList_collect($wsid,$segmentIndex,$sPOrderId){
		
		
		
		$this->load->model('ScSheetModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('ScGxtjModel');
		$this->load->model('ProcessSheetModel');
		$this->load->model('WorkShopdataModel');
		$this->load->model('StaffMainModel');
		$actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);


		$min30 = 60 *30;
		$hour4 = $min30*2 *4;
		$hour12 = $hour4 * 3;
		
		$dataArray = array();
		
		$gxRecords = array();
		
		$records = array();
		
		
		$alltitle = array();
		//echo $wsid.'.'.$actionid;
		if ($actionid == 102 ) {
			

			$row = $this->ScSheetModel->get_records($sPOrderId);
			$stockId = $row['StockId'];
			
			$gxList = $this->ProcessSheetModel->get_sc_processlist($stockId,$sPOrderId);
			foreach ($gxList as $gxOne) {
				$ProcessId = $gxOne['ProcessId'];
				$TypeId = $gxOne['TypeId'];
				
				$alltitle[]=$TypeId.'';
				$records = array();
				$recordOnes = $this->ScGxtjModel->get_gx_records($sPOrderId,$ProcessId);
				$rownums = count($recordOnes);
				for ($i = 0; $i < $rownums; $i ++) {
					$rows = $recordOnes[$i];
					$operator=$this->StaffMainModel->get_staffname($rows['Leader']);
					$thistime = $rows['OPdatetime'];
					$snailA = $snailB = $snailC = '';
					if ($i< ($rownums - 1)) {
						$nextTime = $recordOnes[$i+1]['OPdatetime'];
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
					
					$records[]=array(
					    'tag'    =>'djRecord',
					    
					    'index'=>'' . ($rownums-$i),
						'col1' =>$rows['Qty'],
						'type'   =>'gx',
						'Id'    =>$rows['Id'],
						'snail1'=>$snailA,
						'snail2'=>$snailB,
						'snail3'=>$snailC,
						'col2' =>array('Text'=>$thistime,'DateType'=>'time'),
						'col3' =>"$operator",
						'showLine'=>'1',
						'remarkImg'=>''
					);
				}
				
				$gxRecords[]=$records;
				
				
			}
			
		} else {
			$recordOnes=$this->ScCjtjModel->get_records($sPOrderId);
			$rownums = count($recordOnes);
				for ($i = 0; $i < $rownums; $i ++) {
					$rows = $recordOnes[$i];
					$operator=$this->StaffMainModel->get_staffname($rows['Operator']);
					$thistime = $rows['created'];
					$snailA = $snailB = $snailC = '';
					if ($i< ($rownums - 1)) {
						$nextTime = $recordOnes[$i+1]['created'];
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
					
					$records[]=array(
					    'tag'    =>'djRecord',
					    'index'=>'' . ($rownums-$i),
					    'type'   =>'sc',
					    'Id'    =>$rows['Id'],
						'col1' =>$rows['Qty'],
						'snail1'=>$snailA,
						'snail2'=>$snailB,
						'snail3'=>$snailC,
						'col2' =>array('Text'=>$thistime,'DateType'=>'time'),
						'col3' =>"$operator",
						'showLine'=>'1',
						'remarkImg'=>''
					);
				}
				
				$gxRecords[]=$records;
				if ($rownums <= 5) {
					return $records;
				}
				//return $records;
		}
		
		$dataArray[]=array(
			'tag'=>'collect',
			'records'=>$gxRecords,
			'titles'=>$alltitle
		);
		
		return  $dataArray;
		
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
			case  'order': 
			         $listTag = 'collect';
			         $dataArray=$this->get_subList_collect($wsid,$segmentIndex,$id);  
			         break;
			case   'scedMon':
					$listTag = 'scedDay';
			        $dataArray=$this->get_mon_subList($wsid,$id);
			        break;
			case   'scedDay':
					$listTag = 'order';
			        $dataArray=$this->get_day_subList($wsid,$id);
					break;
		}
		
		$rownums=count($dataArray);
		if ($rownums>0){
			$dataArray[$rownums-1]["deleteTag"] = $upTag;
		}

		$data['jsondata']=array('status'=>'1','message'=>'','totals'=>$rownums,'rows'=>$dataArray);
		$this->load->view('output_json',$data);
		
	}
	
    public function get_subList_order($wsid,$segmentIndex){
	    
	    $factoryCheck=$this->config->item('factory_check');
	    
	    $this->load->model('WorkShopdataModel');
	    $this->load->library('dateHandler');
	    $actionid=$this->WorkShopdataModel->get_workshop_actionid($wsid);
	    
		$this->load->model('ScSheetModel');
		$this->load->model('StuffdataModel');
		$this->load->model('ScCjtjModel');
		$this->load->model('ScGxtjModel');

		
		$min30 = 60 *30;
		$hour12 = $min30*2 *4*3;
		$twoDay = $hour12 * 4;
		$nowdateTime = strtotime($this->DateTime) ;
		
		$versionNum = $this->versionToNumber($this->AppVersion);
		$is415Version = $versionNum >= 415 ? true : false;
        
		
		if ($segmentIndex==-1){
		   $this->NoSendQty    = 0;
		   $rowArray=$this->ScSheetModel->get_scorder_nosend($wsid);  
	    }else{
		   $rowArray=$this->ScSheetModel->get_semi_weeksheet($wsid,$segmentIndex,$dyweek); 
	    }
		
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $scqty = $rows['ScQty'];
		    $Qty = $rows['Qty'];
		    $scedInterval = '';
		    $minus = 0;
		    $sPorderId = $rows['sPOrderId'];
		    $processArray=array();
		    if ($actionid==102){//皮套加工
			   $stockId=$rows['StockId'];  
			   $this->load->model('ProcessSheetModel');
			   $processArray=$this->ProcessSheetModel->get_sc_processlist($stockId,$sPorderId);
			   
			    $beginTime = strtotime($this->ScGxtjModel->get_begin_time($sPorderId));

		   		$lastTime = $scqty >= $Qty ? strtotime($this->ScGxtjModel->get_last_time($sPorderId)) : $nowdateTime;
		   		
		   		$minus = floor($lastTime-$beginTime);
		   		$scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
		   		

		    } else {
			    $beginTime = strtotime($this->ScCjtjModel->get_begin_time($sPorderId));

		   		$lastTime = $scqty >= $Qty ? strtotime($this->ScCjtjModel->get_last_time($sPorderId)) : $nowdateTime;
		   		
		   		$minus = floor($lastTime-$beginTime);
		   		$scedInterval = '...'.$this->datehandler->GetTimeInterval($minus);
		   		

		    }
		    $completeImg='';
		    $actions=array();
		    $lastOper = '';
		    $created_ct = "";
		    $boolFlag = false;
		    if ($segmentIndex==-1){
			    $lastOper = $rows['lastOper'];
			    $lastOper = $lastOper == '' ? '--':$lastOper;
			    $dateOne = $rows['djDate'];
			    $created_ct = $this->datehandler->GetDateTimeOutString($dateOne,"");
			    $boolFlag = $scqty>=$Qty?true : false;
			    $completeImg=$boolFlag?'flag_over':'';
			    $actions=$this->MenuAction;
			    $actions[0]['MaxQty']=$scqty-$rows['ShedQty'];
			    $actions[0]['DjQty'] =$rows['ShedQty'];
		    }
		    
		    if ($factoryCheck==1){
				$rows['djDate'] = '';
				$created_ct     = '';
			}
			$stuffImg=$rows['Picture']==1?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
		   
		   if ($is415Version) {
			    $timeColor = '#727171';
			    $completeImg = 'flag_gray';
			    if ($minus > $twoDay)
		        {
			        $completeImg = 'flag_red';
			        $timeColor = '#ff0000';
		        } else if ($minus > $hour12) {
			        $completeImg = 'flag_orange';
			        $timeColor = '#ff9946';
		        }
			   
			   
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
				'week'       =>$rows['DeliveryWeek'],
				'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'title'      =>$rows['StuffCname'],
				'col1'       =>number_format($Qty),
				'col1Img'    =>'scdj_11',
				'col2'       =>
				array('Text'=>''.number_format($scqty),'Color'=>$boolFlag?'#01be56':'#358fc1'),
				'col2Img'    =>'scdj_12',
				'col3'       =>array(
					'Text'=>'¥'.round($rows['Price'],2),
					'Color'=>'#3b3e41'
					),
				'col3Img'    =>'',
				'col4'       =>array('Text'=>''.$scedInterval,'Color'=>$timeColor),
				"hasTime"    =>"1",
			    "time"       =>
			    $segmentIndex==-1 ? "$created_ct".'|'."$lastOper" :
			    array('Text'=>$rows['djDate'],'DateType'=>'time'),
				'Process'    =>$processArray,
				'completeImg'=>$boolFlag?$completeImg:''
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
				'week'       =>$rows['DeliveryWeek'],
				'mStockId'   =>isset($rows['mStockId'])?$rows['mStockId']:'',
				'title'      =>$rows['StuffCname'],
				'col1'       =>$rows['PurchaseID'],
				'col2'       =>number_format($Qty),
				'col3'       =>$segmentIndex<1?($scqty>0?number_format($scqty):''):'',
				'col4'       =>'¥' . number_format($rows['Price'],2),
				"hasTime"    =>"1",
			    "time"       =>
			    $segmentIndex==-1 ? "$created_ct".'|'."$lastOper" :
			    array('Text'=>$rows['djDate'],'DateType'=>'time'),
				'Process'    =>$processArray,
				'completeImg'=>$completeImg
			);

		   }
		   
						
			
			if ($segmentIndex==-1){
			    $nosendqty=$Qty-$rows['ShedQty'];
			    $this->NoSendQty+=$nosendqty>0?$nosendqty:0;
			}
		}
        return $dataArray;
	}
	
	 public function get_subList_stuff($wsid,$segmentIndex,$sPorderId){
	    
		$this->load->model('ScSheetModel');
		$this->load->model('stuffdataModel');
		
		$rowArray=$this->ScSheetModel->get_semi_stocksheet($sPorderId,0);
		$rownums =count($rowArray);
		
		$dataArray=array();
		
		$black=$this->colors->get_color('black');
		$red  =$this->colors->get_color('red');

		for ($i = 0; $i < $rownums; $i++) {
		    $rows =$rowArray[$i];
		    
		    $llcolor=$rows['OrderQty']==$rows['llQty']?$black:$red;
		    $stuffImg=$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($rows['StuffId']):'';
		    
			$dataArray[]=array(
			    'type'       =>$wsid,
			    'segIndex'   =>$segmentIndex,
				'tag'        =>'stuff',
				'Id'         =>$rows['sPOrderId'],
				'title'      =>$rows['StuffCname'],
				'col1'       =>number_format($rows['OrderQty']),
				'col2'       =>number_format($rows['tStockQty']),
				'col3'       =>array('Text'=>number_format($rows['llQty']),'Color'=>$llcolor),
				'col4'       =>'',
				'completeImg'=>'',
				'Picture'    =>$rows['Picture'],
				'stuffImg'   =>$stuffImg
			);
		}
        return $dataArray;
	}

	
	//出货生成送货单
	public function shipping()
	{
		$params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $sPOrderId  = element('Id',$params,'0');
	    $Qty        = element('Qty',$params,'0');
	    
	    if ($action=='shipping'){
	    
	       $status='';
	       if ($Qty>0 && $sPOrderId>0){
	       
		      $this->load->model('GysshsheetModel');
              $status=$this->GysshsheetModel->save_scorder_shsheet($sPOrderId,$Qty,$Mid=0); 
	       }
	       
	       $status=$status=='Y'?1:0;
	       $message=$status==1?'登记成功!':'登记失败!';
	       
	       $rowArray=array(
			             'Action'      =>'delete'
			          );          
	       $data['jsondata']=array('status'=>$status,'message'=>'登记成功!','totals'=>$status,'rows'=>$rowArray);
	    }
	    else{
		   $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    $this->load->view('output_json',$data);
    } 
}
