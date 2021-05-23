 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quality_report extends MC_Controller {
/*
	功能:来料品检
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
    
        
	public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
		$this->load->view('output_json',$data);
	}
	
	
	//品检报告页面
	public function main()
	{
		$params = $this->input->post();
		
		$Floor = element('types',$params,'6');
		
	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	   $this->load->model('QcBadrecordModel');
	   
	   $oneSecData = $this->get_segment_list($Floor,3);
	   
       
	   
	   
	   
	   
	   
	   
	   $sheets=$this->QcBadrecordModel->get_badrecord_month($Floor);
		 
	   $dataArray=array();
	   $dataArray[]=array('data'=>$oneSecData);
	   
	    
	   $m=1; 
	   foreach ($sheets as $rows){
	                 

           
           
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
				'tag'      =>'not_total',
				'method'   =>'report_dates',
				'Id'=>$rows['Month'],
				'isTotal'  =>'1',
				'type'     =>$Floor,
                'hidden'   =>'0',
                'showArrow'=>'1',
                'open'     =>'',
				'segIndex' =>$rows['Month'],
				'title'    =>$titleAttr,
				'col4marginR'=>'40',
				'row1Y'=>'8',
				'col1'     =>array('Text'=>number_format($rows['Qty']),'Color'=>$red),
				'col1R'     =>array('Text'=>number_format($rows['badCounts']),'Color'=>$lightgray),
			    'col2'     =>array('Text'=>number_format($rows['shQty']),'Color'=>$black),
			    'col2R'     =>array('Text'=>number_format($rows['Counts']),'Color'=>$lightgray),
			    
          
				);
			$m++;
	  }
	    
	  $data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($dataArray),'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data);  
	}


	function get_monthlistdates($Floor,$month)
	{
		$lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');
        $red       =$this->colors->get_color('red');
        
        $factoryCheck=$this->config->item('factory_check');
        
	    $dataArray=array();

        $this->load->model('QcBadrecordModel');
        $this->load->model('StuffdataModel');
        
        $sheets=$this->QcBadrecordModel->get_badrecord_month_dates($Floor,$month);
   
	    foreach ($sheets as $rows){
          
        	 
           $date = $rows['Date'];
          
           $istoday = $date==$this->Date ? true : false;
           $weekday = date('w',strtotime($date));
           
            if ($factoryCheck  &&  ($weekday==0 || $weekday==6)) continue;//验厂设置
           $dateCom = explode('-', substr($date, 5));
           
           
           
		   $titleAttr = array('Text'=>substr($date, 5),'Color'=>($weekday==0 ||$weekday==6)?'#ff665f':'#9a9a9a','dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?'#ffa5a0':'#cfcfcf','dateBg'=> $istoday?'#ffff2e':'');
           
		   $dataArray[]=array(
				'tag'      =>'not_date',
				'method'   =>'report_date_sub',
				'Id'=>$rows['Date'],
				'isTotal'  =>'1',
				'type'     =>$Floor,
                'hidden'   =>'0',
                'showArrow'=>'1',
                'open'     =>'',
				'segIndex' =>$rows['Date'],
				'title'    =>$titleAttr,
				'col4marginR'=>'40',
				'row1Y'=>'8',
				'col1'     =>array('Text'=>number_format($rows['Qty']),'Color'=>$red),
				'col1R'     =>array('Text'=>number_format($rows['badCounts']),'Color'=>$lightgray),
			    'col2'     =>array('Text'=>number_format($rows['shQty']),'Color'=>$black),
			    'col2R'     =>array('Text'=>number_format($rows['Counts']),'Color'=>$lightgray),
			    
          
				);
  
        }
		 return $dataArray;
	}


	public function search_m() {
		
		$params = $this->input->post();
		$Floor = element('types', $params , '');
		$searched = element('search', $params , '');
		$searched = trim($searched);
		
		$sectionList = array();
		if ($searched != '') {

			$dataArray = $this->get_monthlist($Floor,'','', $searched);
			$sectionList[]=array('data'=>$dataArray);
			
		}
		
		
	    $data['jsondata']=array('status'=>'1','message'=>'1','totals'=>1,'rows'=>$sectionList);
	    $this->load->view('output_json',$data);
	}


	function report_date_sub() {
		$params = $this->input->post();
	    $date  = element('Id',$params,'');//月份
	    $Floor  = element('type',$params,'');//送货楼层
	    
	    
		$dataArray = $this->get_monthlist($Floor,'',$date);
       	    
	   $totals=count($dataArray); 
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
	}
	function report_dates() {
		$params = $this->input->post();
	    $month  = element('segmentIndex',$params,'');//月份
	    $Floor  = element('type',$params,'');//送货楼层
	    
	    
		$dataArray = $this->get_monthlistdates($Floor,$month);
       	    
	   $totals=count($dataArray); 
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
	}
	//品检报告页面
	public function report_main()
	{
		$params = $this->input->post();
		
		$Floor = element('types',$params,'6');
		
	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	   $this->load->model('QcBadrecordModel');
	   
	   $sheets=$this->QcBadrecordModel->get_badrecord_month($Floor);
		 
	   $dataArray=array();
	    
	   $m=1; 
	   foreach ($sheets as $rows){
	       $col2='';
		   if ($rows['Qty']>0){
			   $col2=array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['Qty']),'Color'=>$red,'FontSize'=>'13'),
			                 array('Text'=>'(' . $rows['badCounts']. ')','Color'=>$lightgray,'FontSize'=>'10')
			                )
                         );
		   }
           
           $rowArray=$m==1?$this->get_monthlist($Floor,$rows['Month']):array();
           
           
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
				'tag'      =>'qcllj',
				'method'   =>'report_sub',
				'isTotal'  =>'1',
				'type'     =>$Floor,
                'hidden'   =>'0',
                'showArrow'=>'1',
                'open'     =>$m==1?'1':'0',
				'segIndex' =>$rows['Month'],
				'title'    =>$titleAttr,
				'col2'     =>$col2,
			    'col3'     =>array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['shQty']),'Color'=>$black,'FontSize'=>'13'),
			                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
			                )
                         ),
                'data'    =>$rowArray              
				);
			$m++;
	  }
	    
	  $data['jsondata']=array('status'=>'1','message'=>'','totals'=>count($dataArray),'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data);  
	}
	
	//品检明细
	public function report_sub()
	{
		$params = $this->input->post();
	    $month  = element('segmentIndex',$params,'');//月份
	    $Floor  = element('type',$params,'');//送货楼层
	    
	    
		$dataArray = $this->get_monthlist($Floor,$month);
       	    
	   $totals=count($dataArray); 
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data);  
	    
	}
	
	function get_monthlist($Floor,$month, $dateOne = '', $searched = '')
	{
		$lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');
        $red       =$this->colors->get_color('red');
        
        $factoryCheck=$this->config->item('factory_check');
        
	    $dataArray=array();

        $this->load->model('QcBadrecordModel');
        $this->load->model('StuffdataModel');
        
        $sheets=$this->QcBadrecordModel->get_badrecord_monthlist($Floor,$month,$dateOne, $searched);
   
   
	    foreach ($sheets as $rows){
           $Decimals = isset($rows['Decimals'])?$rows['Decimals']:0;
           
           $checkQty = $rows['checkQty'];
           
           $stuffImg = $rows['Picture']>0?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
           
           
           
           $badPercent = $checkQty>0?round($rows['Qty']/$checkQty*100,1):0;
           
           $badimgs = $this->QcBadrecordModel->get_badpictures($rows['Id']);
           $qcurl   = $this->QcBadrecordModel->get_qualityreport_url($rows['Id']);
           
           $reports=array('url'  =>$qcurl,'title'=>' 品检报告','Type'=>'html','static'=>'qchtml');
           array_unshift($badimgs,$reports);
           
           $imgurl = $rows['Picture']>0?$this->StuffdataModel->get_stuff_picture($rows['StuffId']):'';
           
		   $dataArray[] = array(
				'tag'     =>'stuffImgs',
				'actions' =>array(),
				'StuffId'=>$rows['StuffId'],
				'imgurl'=>$imgurl,
				'Id'      =>$rows['Id'],
				'title'   =>$rows['StuffId'] . '-' . $rows['StuffCname'],
				'col1'    =>number_format($checkQty,$Decimals),
				'col2'    =>array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['Qty'],$Decimals),'Color'=>$red,'FontSize'=>'12'),
			                 array('Text'=>'(' . $badPercent. '%)','Color'=>$lightgray,'FontSize'=>'8')
			                )
                         ),
                'Picture' =>$rows['Picture'],
				'selected'=>'',
			    'stuffImg'=>$stuffImg,
                'time'    =>$factoryCheck==1?'':date('m-d',strtotime($rows['created'])) . '',
                'forshort'=>$rows['Forshort'],
				'operator'=>$rows['Operator'],
				'imgs'    =>$badimgs
				);//'qcurl'   =>$qcurl
           /*
           $rtIcon=$this->get_checksign_image($rows['CheckSign']);
           
		   $dataArray[]= array(
					'tag'     =>'qcStuff',
					'hideLine'=>'0',
					'actions' =>array(),
					'Id'      =>$rows['Id'],
					'StockId' =>strlen($rows['StockId']<15)?$rows['StuffId']:$rows['StockId'],
					'StuffId' =>$rows['StuffId'],
					'week'    =>$rows['DeliveryWeek'],
					'rtIcon'  =>$rtIcon,
					'title'   =>$rows['StuffId'] . '-' . $rows['StuffCname'],
					'col1'    =>out_format($rows['Forshort']),
				    'col2'    =>number_format($rows['OrderQty'],$Decimals),
					'col3'    =>number_format($rows['shQty'],$Decimals),
					'col4'    => array('Text'=>number_format($rows['djQty'],$Decimals),'Color'=>$red),
					'Picture' =>$rows['Picture'],
					'selected'=>'',
			        'stuffImg'=>$stuffImg,
			        'process' =>array()
				);
			*/

		 }
		 return $dataArray;
	}
	
	
	function get_checksign_image($checkSign)
	{
		return 'qc_check_' . $checkSign;
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
       
	   $sheets=array();
	   $actions=array();
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
		   $stuffImg= $rows['Picture']!=1 && $segmentIndex==1?'':$stuffImg;
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
				 $remarkArray=array(
							'tag'      =>'remarkNew',
							'headline' =>'备注: ',
							'Record'   =>$remarks['Remark'],
							'Recorder' =>$times . ' ｜ '. $remarks['Operator'],
							'bgcolor'  =>'#FFFFFF',
							'left_sper'=>'15',
							'RID'      =>$remarks['Id'] 
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
		        $listArray = $djQty>0? $this->pageaction->get_actions('register,returnstock,qcreport') : $this->pageaction->get_actions('register,returnstock,qcreport');
	           
		        $listArray[0]['MaxQty']=$rows['Qty']-$djQty;
		        $listArray[0]['DjQty'] =$djQty;
		        
		         $actions[0]['list']=$listArray;
		   }
		   $Decimals=isset($rows['Decimals'])?$rows['Decimals']:0;
		   if ($segmentIndex == 2) {
			   
			   
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
					'Picture' =>$rows['Picture'],
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
		              $rowArray['time']=$factoryCheck==1?'':array('Text'=>$lasttime,'DateType'=>'time','Color'=>$black);
	              }
	              
	              
		          break;
		       case 3:
		          $rowArray['col4']=number_format($rows['scQty'],$Decimals);
		          unset($rows['refreshTV']);
		          unset($rows['tv_IP']);
		          break;
		      case 4:
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
	   
	  	
	   return $dataArray;
	}

	
}
