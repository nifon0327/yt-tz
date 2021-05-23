 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quality_tlmain extends MC_Controller {
/*
	功能:退料记录页面
*/
    public $SetTypeId= null;
    public $MenuAction= null;
    public $PrintAction= null;
    
    function __construct()
    {
        parent::__construct();
        
        //用户App设置参数类型
        $this->SetTypeId    = 3;//单选设置
    }
    
        
	public function index()
	{
	    $data['jsondata']=array('status'=>'','message'=>'','rows'=>'');
	    
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
                'Picture' =>$rows['Picture'],
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

	public function main() {
		$params = $this->input->post();
		
		$Floor = element('types',$params,'6');
		$this->load->model('BaseMPositionModel'); 
		
	    $selected=1;
		$typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$Floor);       

	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	    $navtitle='选择楼层';
	    $dataArray=array();
		
		$this->load->model('QcBadrecordModel');
		$sheets=$this->QcBadrecordModel->get_unth_companylist($Floor,1); 
		
		$oneSecData=array();
		
		foreach ($sheets as $rows){
		
		   $oneSecData[]=array(
				'tag'      =>'edit',
				'showArrow'=>'1',
				'method'   =>'tl_sub',
				'type'     =>"$Floor",
				'open'     =>'0',
				'edit'     =>'0',
				'Id'       =>$rows['CompanyId'],
				'title'    =>$rows['Forshort'],
				'center'=>number_format($rows['Qty']),
				'centerR'=>''.$rows['Counts'],
				'centerX'=>'-22',
			    'col1'     =>array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['Qty']),'Color'=>$black,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         )
				);
		}
	    $dataArray[]=array('data'=>$oneSecData);
	    $i=0; 
	   // $Titles=$this->get_titles();
	    
	    
	    
	    
        $numsOfTypes=count($typesArray); 
        
        
        if ($numsOfTypes>0){
            for ($j = 0; $j < $numsOfTypes; $j++){
	                 $oneTypes=$typesArray[$j];
	                if ($oneTypes['Id']==$Floor){
		                break;
	                }
	        } 
	        $Floor=$oneTypes['Id'];
	        $navtitle=$oneTypes['title'];
	        
	        $this->load->model('CkthsheetModel');
	        $sheets=$this->CkthsheetModel->get_th_monthlist($Floor);
	        
	        $m=1;
		    foreach ($sheets as $rows){
		    
			   $col2='';
			   if ($rows['unQty']>0){
				   $col2=array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['unQty']),'Color'=>$red,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['unCounts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         );
			   }
			   //$rowArray=$m==1?$this->get_billnumberlist($Floor,$rows['Month']):array();
			   
			   $rowArray = array();
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
							   		
							   		
							   		/*
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
			    
                'data'    =>$rowArray              
				);
			$m++;

							   		*/
			   $dataArray[]=array(
					'tag'      =>'not_total',
					'method'   =>'tlmain_sub',
					'type'     =>$Floor,
	                'hidden'   =>'0',
	                'showArrow'=>'1',
	                'open'     =>'0',
	                'col4marginR'=>'40',
	                'row1Y'=>'8',
					'segIndex' =>$rows['Month'],
					'title'    =>$titleAttr,
					'col1'     =>array('Text'=>number_format($rows['unQty']),'Color'=>$red),
				'col1R'     =>array('Text'=>number_format($rows['unCounts']),'Color'=>$lightgray),
			    'col2'     =>array('Text'=>number_format($rows['Qty']),'Color'=>$black),
			    'col2R'     =>array('Text'=>number_format($rows['Counts']),'Color'=>$lightgray),

	                 'data'    =>$rowArray        
					);
				$m++;
			 }
			
        }
	    
	    
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$i,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data); 
	}

	
	
	//退料页面
	public function tl_main(){
		$params = $this->input->post();
		
		$Floor = element('types',$params,'6');
		$this->load->model('BaseMPositionModel'); 
		
	    $selected=1;
		$typesArray=$this->BaseMPositionModel->get_warehouse(2,1,$this->SetTypeId,$Floor);       

	    $red        =$this->colors->get_color('red');
		$lightgreen =$this->colors->get_color('lightgreen');
        $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');

	    $navtitle='选择楼层';
	    $dataArray=array();
	    $dataArray[]=array('hidden'=>'');
	    
	    $i=0; 
	   // $Titles=$this->get_titles();
	    
        $numsOfTypes=count($typesArray); 
        
        
        if ($numsOfTypes>0){
            for ($j = 0; $j < $numsOfTypes; $j++){
	                 $oneTypes=$typesArray[$j];
	                if ($oneTypes['Id']==$Floor){
		                break;
	                }
	        } 
	        $Floor=$oneTypes['Id'];
	        $navtitle=$oneTypes['title'];
	        
	        $this->load->model('CkthsheetModel');
	        $sheets=$this->CkthsheetModel->get_th_monthlist($Floor);
	        
	        $m=1;
		    foreach ($sheets as $rows){
		    
			   $col2='';
			   if ($rows['unQty']>0){
				   $col2=array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['unQty']),'Color'=>$red,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['unCounts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         );
			   }
			   $rowArray=$m==1?$this->get_billnumberlist($Floor,$rows['Month']):array();
			   
			   
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
					'method'   =>'tlmain_sub',
					'type'     =>$Floor,
	                'hidden'   =>'0',
	                'showArrow'=>'1',
	                'open'     =>$m==1?'1':'0',
					'segIndex' =>$rows['Month'],
					'title'    =>$titleAttr,
                    'col1'     =>'',
                    'col2'     =>$col2,
				    'col3'     =>array(
	                            'isAttribute'=>'1',
			                    'attrDicts'  =>array(
	                             array('Text'=>number_format($rows['Qty']),'Color'=>$black,'FontSize'=>'13'),
				                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
				                )
	                         ),
	                 'data'    =>$rowArray        
					);
				$m++;
			 }
			
        }
	    
	    
	    $data['jsondata']=array('status'=>'1','message'=>'','totals'=>$i,'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data);  
	}
	
	//退料单明细
	public function tlmain_sub()
	{
		$params = $this->input->post();
	    $month  = element('segmentIndex',$params,'');//月份
	    $Floor  = element('type',$params,'');//送货楼层

	    
        $dataArray=$this->get_billnumberlist($Floor,$month);
        	    
	   $totals=count($dataArray)/2; 
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	    
	   $this->load->view('output_json',$data);  
	    
	}
	
	function get_billnumberlist($Floor,$month)
	{
	    $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');
        
        $factoryCheck=$this->config->item('factory_check');
        
        $dataArray=array();
		$this->load->model('CkthsheetModel');
        $sheets=$this->CkthsheetModel->get_th_billnumberlist($Floor,$month);
   
	    foreach ($sheets as $rows){
	    
	       $actions   = array();
		   if ($rows['Estate']==1){
			  $actions=$this->pageaction->get_actions('signature,destroy');
			  $actions[0]['Name']='退回';
		   }
		   
           $dataArray[]=array('tag'=>'margin');
		   $dataArray[]=array(
				'tag'      =>'tlHead',
				'method'   =>'tlmain_sub_list',
				'type'     =>$Floor,
				'actions'  =>$actions,
				'Id'       =>$rows['Id'],
				'name'     =>$rows['Forshort'],
				"order"    =>$rows['BillNumber'],
				'estateImg'=>'qc_tl_' . ($rows['Type']+1),
			    'qty'      =>array(
                            'isAttribute'=>'1',
		                    'attrDicts'  =>array(
                             array('Text'=>number_format($rows['Qty']),'Color'=>$black,'FontSize'=>'13'),
			                 array('Text'=>'(' . $rows['Counts']. ')','Color'=>$lightgray,'FontSize'=>'10')
			                )
                         ),
                'time'    =>$factoryCheck==1?'':array('Text'=>$rows['created'],'DateType'=>'time'),
				'operator'=>$rows['Operator'] 
				);
		 }
		 $dataArray[]=array('tag'=>'margin');
		 return $dataArray;

	}
	
	//退料记录明细
	public function tlmain_sub_list()
	{
		$params     = $this->input->post();
	    $Mid        = element('Id',$params,'');//送货单号
	    $Floor      = element('type',$params,'');//送货楼层
	    $upTag      = element('upTag',$params,'');
	    
	    $lightgray  =$this->colors->get_color('lightgray');
        $black     =$this->colors->get_color('black');
        
        $factoryCheck=$this->config->item('factory_check');
        
	    $dataArray = array();
        
        $this->load->model('CkthsheetModel');
        $this->load->model('StuffdataModel');
	    $this->load->model('QcBadrecordModel');
	    
        $sheets=$this->CkthsheetModel->get_billnumber_list($Mid);
   
	    $dataArray=array();
	    $actions   = array();
	    
	    foreach ($sheets as $rows){
		   $stuffImg=$rows['Picture']>0?$this->StuffdataModel->get_stuff_icon($rows['StuffId']):'';
		   
		   $badimgs =$this->QcBadrecordModel->get_badpictures($rows['Bid']);
		   
           $qcurl = $this->CkthsheetModel->get_threport_url($rows['BillNumber']);
           $reports=array('url'  =>$qcurl,'title'=>' 退料单','Type'=>'html','static'=>'tlhtml');
           array_unshift($badimgs,$reports);
           
           $imgurl = $rows['Picture']>0?$this->StuffdataModel->get_stuff_picture($rows['StuffId']):'';
           
           
		   $dataArray[]=array(
				'tag'     =>'stuffImgs',
				'actions' =>$actions,
				'Id'      =>$rows['Id'],
				'StuffId'=>$rows['StuffId'],
				'imgurl'=>$imgurl,
				'title'   =>$rows['StuffId'] . '-' . $rows['StuffCname'],
				'col1'    =>number_format($rows['shQty']),
				'col2'    =>number_format($rows['Qty']) . 'pcs',
                'Picture' =>$rows['Picture'],
				'selected'=>'',
			    'stuffImg'=>$stuffImg,
                'time'    =>$factoryCheck==1?'':date('m-d',strtotime($rows['created'])) . '',
				'operator'=>$rows['Operator'],
				'imgs'    =>$badimgs 
				);//'qcurl'   =>$qcurl
		}
		$totals=count($dataArray);
		
		if (count($dataArray)>0){
			$dataArray[$totals-1]['isLast']='1';
			$dataArray[$totals-1]['deleteTag']='tlHead';
		}
		
	   
	   $data['jsondata']=array('status'=>'1','message'=>'','totals'=>"$totals",'rows'=>$dataArray);
	   $this->load->view('output_json',$data); 
	    
	}
	
	//销毁功能
	public function destroy()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id    = element("Id",$params,'');
	    
	    $rowArray=array();
	    $status=0;
	    
	     if ($action=='destroy'){
		  $this->load->model('CkthsheetModel');
		    $status=$this->CkthsheetModel->set_estate($Id,0,2);
		    if ($status==1){
			    $rowArray=array(
			            'estateImg'=>'qc_tl_0',
			            'actions'=>array()
			          );
		    }
	    }
	    
	    $dataArray=array(
		            'data'   =>$rowArray
		            );
	    
	    $message=$status==1?'销毁操作成功！': '销毁操作失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
		$this->load->view('output_json',$data);  
	}
	
	//退回签名保存
	public function signature()
	{
		$params = $this->input->post();
	    $action = element("Action",$params,'');
	    $Id    = element("Id",$params,'');
	    
	    $rowArray=array();
	    $status=0;
	    
	     if ($action=='signature'){
		    $this->load->model('CkthsheetModel');
		    
		    $status=$this->CkthsheetModel->save_signature($params);
		    if ($status==1){
			    $rowArray=array(
			            'estateImg'=>'qc_tl_2',
			            'actions'=>array()
			          );
		    }
	    }
	    
	    $dataArray=array(
		            'data'   =>$rowArray
		            );
	    
	    $message=$status==1?'退回操作成功！': '退回操作失败!';

	    $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>'0','rows'=>$dataArray);
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
             $actions[0]['Action']= 'print';
		}
		$dataArray=array(
		            'actions'=>$actions,
		            'data'   =>array()
		            );
		            
	    $message=$status==1?'成功！': '失败!';
	    
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
