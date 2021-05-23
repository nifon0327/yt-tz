<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_bpbf extends MC_Controller {
	
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
    
    
    function get_print_fmt() {
	    $printDict=array(
	    "CGPO"=>"",
	    "Week"=>"$WeeksNow",
	    "cName"=>"$StuffCname",
	    "tstock"=>"$tStockQty",
	    "Forshort"=>"$Forshort",
	    "GXQty"=>"",
	    "stuffid"=>"$StuffId",
	    "datee"=>"$lastRK",
	    "oper"=>"",
	    'props'=>$stuffProp,
	    "way"=>$CheckSign,
	    'Frame'=>"$FrameCapacity",
	    'Qty'=>"$num",
	    'ip'=> '',
	    'companyid'=>"$aCompanyId",
	    "time"=>"");
	    //stuffid  cName props  Forshort datee Qty way oper newcode
			  
			  
		return $printDict; 
    }
    
    function get_print_bp() {
	    $this->load->model('staffMainModel');
        $this->load->model('StuffPropertyModel');
        $this->load->model('StuffdataModel');

	    $params        = $this->input->post();

		$indate = element('indate',$params,'');
	    $StuffId       = element('stuffid',$params,'');

// 	    $frameqty = element('frameqty',$params,'');



	    

	    $label = 'all';
	    $tstockqty = element('num',$params,0);
	    
	    $records       =$this->StuffdataModel->get_records($StuffId);
	    $cName         =$records['StuffCname'];
	    $frameqty =$records['FrameCapacity'];
	    $Forshort      =$records['Forshort'];
	    $CompanyId = $records['CompanyId'];
	    $CheckSign     =$records['CheckSign'];
		$CheckSign=$CheckSign==0?'抽检':'全检';
		$oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
		$Propertys = $this->StuffPropertyModel->get_property_array($StuffId);
		
		$SendFloor = $records['SendFloor'];

		$ip = "192.168.30.101";
		$is47_1F_ip = "192.168.30.115";
		
		$IP = ($SendFloor == 17 || $SendFloor == 14) ?   $is47_1F_ip :$ip;
		if ($this->LoginNumber == 11965) {
			$IP = $ip;
		}
	    $printData = array(
		    'stuffid'=>''.$StuffId,
		    'cName' => ''.$cName,
		    'props'=>$Propertys,
		    'datee'=>$indate,
		    'Forshort'=>''.$Forshort,
		    'Qty'=>$frameqty.'',
		    'way'=>''.$CheckSign,
		    'Week'=>''. substr($this->getWeek($indate), 4, 2),
		    'oper'=>''.$oper,
		    'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$frameqty"
	    );
	    
	    $pages = array();
	    
	    
	    
	    switch ($label) {
		    case 'all':{
			    if ($frameqty > 0) {
				    $int = intval( $tstockqty / $frameqty);
				    for ($i=0; $i<$int; $i ++) {
					    $pages[]=array(
					    	'Qty'=>number_format($frameqty),
					    	'qrcode'=>"$CompanyId".'|'."$StuffId" .'|'."$frameqty"
					    );
				    }
				    $left = intval($tstockqty % $frameqty );
				    if ($left > 0) {
					    $pages[]=array(
					    	'Qty'=>number_format($left),
					    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$left"
					    );
				    }
			    } else {
				    $pages[]=array(
				    	'Qty'=>number_format($tstockqty),
				    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$tstockqty"
				    );
			    }
		    }
		    break;
		    case 'int':{
			    $int = intval($frameNums);
			    if ($frameqty > 0)
			    for ($i=0; $i<$int; $i ++) {
				    $pages[]=array(
				    	'Qty'=>number_format($frameqty),
				    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$frameqty"
				    );
			    }
		    }
		    break;
		    case 'left':{
			    $left = $tstockqty;
			    if ($frameqty > 0) {
				    $left=intval($tstockqty % $frameqty );
			    }
			     
			    $int = intval($frameNums);
			    if ($frameqty > 0)
			    for ($i=0; $i<$int; $i ++) {
				    $pages[]=array(
				    	'Qty'=>number_format($left),
				    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$left"
				    );
			    }
			    
		    }
		    break;
	    }
	    
	    
	    $printDict = array(	
	    	'IP'=>''.$IP,
	    	'Data'=>$printData,
	    	'Type'=>'QcNew',
	    	'Pages'=>$pages
	    );
	    
	    $Action = array();
	    
	    $Action[]=array('Action'=>'print', 'data'=>$printDict);
	    
	    return $Action;
    }
    
    function ck_print() {
	    
	    $this->load->model('staffMainModel');
        $this->load->model('StuffPropertyModel');
        $this->load->model('StuffdataModel');

	    $params        = $this->input->post();
		$frameNums        = element('frames',$params,'');
		$indate = element('indate',$params,'');
	    $StuffId       = element('stuffid',$params,'');
	    $infos       = element('infos',$params,'');
// 	    $frameqty = element('frameqty',$params,'');
	    $infos = explode('|', $infos);
	    $tstockqty = $infos[0];
	    $tstockqty = intval( str_replace(',', '', $tstockqty));
	    
	    $FrameCapacity = element('qty',$params,'');
	    $label = element('label',$params,'');
	    
	    $records       =$this->StuffdataModel->get_records($StuffId);
	    $cName         =$records['StuffCname'];
	    $frameqty =$records['FrameCapacity'];
	    $Forshort      =$records['Forshort'];
	    $CompanyId = $records['CompanyId'];
	    $CheckSign     =$records['CheckSign'];
		$CheckSign=$CheckSign==0?'抽检':'全检';
		$oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
		$Propertys = $this->StuffPropertyModel->get_property_array($StuffId);
		
		$SendFloor = $records['SendFloor'];

		$ip = "192.168.30.101";
		$is47_1F_ip = "192.168.30.115";
		
		$IP = ($SendFloor == 17 || $SendFloor == 14) ?   $is47_1F_ip :$ip;
		if ($this->LoginNumber == 11965) {
			$IP = $ip;
		}
	    $printData = array(
		    'stuffid'=>''.$StuffId,
		    'cName' => ''.$cName,
		    'props'=>$Propertys,
		    'datee'=>''.$indate,
		    'Forshort'=>''.$Forshort,
		    'Qty'=>$frameqty.'',
		    'way'=>''.$CheckSign,
		    'Week'=>''. substr($this->getWeek($indate), 4, 2),
		    'oper'=>''.$oper,
		    'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$frameqty"
	    );
	    
	    $pages = array();
	    
	    
	    
	    switch ($label) {
		    case 'all':{
			    if ($frameqty > 0) {
				    $int = intval( $tstockqty / $frameqty);
				    for ($i=0; $i<$int; $i ++) {
					    $pages[]=array(
					    	'Qty'=>number_format($frameqty),
					    	'qrcode'=>"$CompanyId".'|'."$StuffId" .'|'."$frameqty"
					    );
				    }
				    $left = intval($tstockqty % $frameqty );
				    if ($left > 0) {
					    $pages[]=array(
					    	'Qty'=>number_format($left),
					    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$left"
					    );
				    }
			    } else {
				    $pages[]=array(
				    	'Qty'=>number_format($tstockqty),
				    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$tstockqty"
				    );
			    }
		    }
		    break;
		    case 'int':{
			    $int = intval($frameNums);
			    if ($frameqty > 0)
			    for ($i=0; $i<$int; $i ++) {
				    $pages[]=array(
				    	'Qty'=>number_format($frameqty),
				    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$frameqty"
				    );
			    }
		    }
		    break;
		    case 'left':{
			    $left = $tstockqty;
			    if ($frameqty > 0) {
				    $left=intval($tstockqty % $frameqty );
			    }
			     
			    $int = intval($frameNums);
			    if ($frameqty > 0)
			    for ($i=0; $i<$int; $i ++) {
				    $pages[]=array(
				    	'Qty'=>number_format($left),
				    	'qrcode'=>"$CompanyId" .'|'."$StuffId" .'|'."$left"
				    );
			    }
			    
		    }
		    break;
	    }
	    
	    
	    $printDict = array(	
	    	'IP'=>''.$IP,
	    	'Data'=>$printData,
	    	'Type'=>'QcNew',
	    	'Pages'=>$pages
	    );
	    
	    $Action = array();
	    
	    $Action[]=array('Action'=>'print', 'data'=>$printDict);
	    
	    
	    $data['jsondata']=array('status'=>110,'message'=>'开始打印','totals'=>'0','rows'=>array(
		    'actions'=>$Action
	    ));
		$this->load->view('output_json',$data);
    }
    
    
    
    function set_frameqty() {
	    
	    $params        = $this->input->post();
		$action        = element('Action',$params,'');
	    $StuffId       = element('stuffid',$params,'');
	    $FrameCapacity = element('qty',$params,'');
	
	    $status=0;
	    $rowArray=array();
	    if ($action=='set_frameqty' && $StuffId>0 &&  $FrameCapacity>0)
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
    
    function shift_location() {
	    $message='';

        $params = $this->input->post();
        $rkId = element('rkId', $params, '');
        
        $this->load->model('CkrksheetModel');
        $location = element('location', $params, '');
        
        $status = $this->CkrksheetModel->edit_location($rkId, $location);
        

	    $message = $status > 0 ? '移库成功':'移库失败';
	    
	    $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array());
	   $this->load->view('output_json',$data);
    }
    
    //*******************备品转入
    
    	public function fixedStuff() {
			$message='';

	        $params = $this->input->post();
	        $stuffid = element('stuffid', $params, '');
	        
	        
	        $this->load->model('stuffdataModel');
	        $query = $this->stuffdataModel->getdata_usestuffid($stuffid);
	        $rows = $query->result();
	        $data['jsondata']=
	        array(
	        	  'status' => '1',
	        	  'message'=> $message,
	        	  'totals' => '1',
	        	  'rows'   => $rows
	        	  );
		    $this->load->view('output_json',$data);
       }

	
	public function bp_save() {
		
	   $message='';
	   $this->load->model('ck7bprkModel');
	   $params = $this->input->post();
/*
	   if (element('LoginNumber',$params,'-1') == 11965) {
		  $insert_id = 1; 
	   } else 
*/
	   
	   {
		   $insert_id=$this->ck7bprkModel->save_item($params);
	   }
	   
	   $status=$insert_id>0?1:0;
	   
	   $rows = array();
	   if ($insert_id>0) {
		   $status = 110;
		   $rows= array('actions'=>$this->get_print_bp());
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
	public function bp_edit() {
		
	   $message='';
	   $this->load->model('ck7bprkModel');
	   $rows=$this->ck7bprkModel->edit_item($this->input->post());
	   $status=$rows>0?1:0;
	   
	   $rows = array();
	   if ($status>0) {
// 		   $rows[]= $this->ck7bprkModel->getPrintDict($this->input->post());
$status = 110;
		   $rows= array('actions'=>$this->get_print_bp());
	   }
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>$rows);
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
	
	public function bp_modify_pick() {
		$message='';
	    $this->load->model('ck7bprkModel');
	    $editid = element('editid',$this->input->post(),'-1');
	    $rows=$this->ck7bprkModel->get_model_pick($editid); 
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


	function returnback() {
		
		$params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $remark     = element('remark',$params,'');
	    $menu_id     = element('menu_id',$params,'-1');
	    $rowId  = element('Id',$params,'0');

	    
	    if ($action=='returnback' && $menu_id>=0){
	    
	       $status=0;
	       $model ='';
	       switch ($menu_id) {
		       case 0:
		       $model= 'Ck7bprkModel';
		       break;
		       
		       case 1:
			   $model = 'Ck8bfsheetModel';
			   break;
			   default:
			   break;
	       }
	       
	       
	       $rowArray = array();
	       $this->load->model($model);
 	       $del = $this->$model->back_item($rowId, $remark);
$del = 1;
	       $status = $del > 0 ? 1:0;
	       $message=$status==1?'退回成功!':'退回失败!';
	       if ($status==1) {
		       
		       $estateImg = 'vacation_state2';
					$actions = array();	
						
				$actions[]=array('Name'=>'删除','Action'=>'delete_row','Color'=>$this->color_red);
				$actions[]=array('Name'=>'修改','Action'=>'modify','Color'=>$this->color_bluefont);

		       $rowArray=array(
			             'data'      =>array('actions'=>$actions,'shipImg'=>$estateImg)
			          );  
	       }
	               
	       $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$status,'rows'=>$rowArray);
	    }
	    else{
		   $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    $this->load->view('output_json',$data);
		
	}
    function pass() {
	    $params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $menu_id     = element('menu_id',$params,'-1');
	    $rowId  = element('Id',$params,'0');

	    
	    if ($action=='pass' && $menu_id>=0){
	    
	       $status=0;
	       $model ='';
	       switch ($menu_id) {
		       case 0:
		       $model= 'Ck7bprkModel';
		       break;
		       
		       case 1:
			   $model = 'Ck8bfsheetModel';
			   break;
			   default:
			   break;
	       }
	       
	       
	       $rowArray = array();
	       $this->load->model($model);
	       $del = 1;
	       $del = $this->$model->pass_item($rowId);
	       $status = $del > 0 ? 1:0;
	       $message=$status==1?'审核成功!':'审核失败!';
	       if ($status==1) {
		       $rowArray=array(
			             'Action'      =>'delete'
			          );  
	       }
	               
	       $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$status,'rows'=>$rowArray);
	    }
	    else{
		   $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    $this->load->view('output_json',$data);
    }
    
    
    function delete_row()  {
	    
	    
	    $params     = $this->input->post();
	    $action     = element('Action',$params,'');
	    $menu_id     = element('menu_id',$params,'-1');
	    $rowId  = element('Id',$params,'0');

	    
	    if ($action=='delete_row' && $menu_id>=0){
	    
	       $status=0;
	       $model ='';
	       switch ($menu_id) {
		       case 0:
		       $model= 'Ck7bprkModel';
		       break;
		       
		       case 1:
			   $model = 'Ck8bfsheetModel';
			   break;
			   default:
			   break;
	       }
	       
	       
	       $rowArray = array();
	       $this->load->model($model);
	       $del = $this->$model->delete_item($rowId);
	       $status = $del > 0 ? 1:0;
	       $message=$status==1?'删除成功!':'删除失败!';
	       if ($status==1) {
		       $rowArray=array(
			             'Action'      =>'delete'
			          );  
	       }
	               
	       $data['jsondata']=array('status'=>$status,'message'=>$message,'totals'=>$status,'rows'=>$rowArray);
	    }
	    else{
		   $data['jsondata']=array('status'=>'0','message'=>'非法操作!','totals'=>'0','rows'=>array());
	    }
	    $this->load->view('output_json',$data);
    }
    
    
    
    
    
     function get_location_bf() {
	    
	     $params = $this->input->post();
	     
	     $editid = element("Ids",$params,'');
		$stuffid = element('stuffid', $params , '0');
		$status = array('none'=>'');
		
		$oldLocationId = -1;
		if ($editid != '') {
			    $this->load->model('Ck8bfsheetModel');
			    $oldLocationId = $this->Ck8bfsheetModel->get_locationid($editid);
			    

		    }
		$this->load->model('StuffDataModel');
		$this->load->model('CkrksheetModel');
		
		
		$waresInfo = $this->CkrksheetModel->get_stuff_locationqty($stuffid);
		$wares = array();
		$waresInfo = $waresInfo['data'];
		$tstockqty = 0;
		
		foreach ($waresInfo as $waresInfoRow) {
			//$tstockqty += intval( str_replace(',', '', $waresInfoRow['qty']) );
			$wares[]=array(
				'title'=>$waresInfoRow['location'],
				'qty'=> $waresInfoRow['qty'],

				'oldSelect'=>$waresInfoRow['qty']!=''?'1':'',
				'select'=>$oldLocationId == $waresInfoRow['LocationId']?'1':'',
				'Id'=>$waresInfoRow['LocationId'].''
			);
		}
		
		$rows = array(array(
			"Id"=>"",
            "title"=>"",
            "qty"=> "",
            "floor"=>"1",
            "sub"=>$wares
		));
		$status = array('pos'=>'0');
		
		
	    $data['jsondata']=array('status'=>$status,'message'=>'1','totals'=>1,'rows'=>$rows);
	    $this->load->view('output_json',$data);

	    
    }

         function get_location_bp() {
		$params = $this->input->post();
	    $StuffId    = element("stuffid",$params,'');
	    
	    $editid = element("Ids",$params,'');
	    
	    $this->load->model('CkLocationModel');
	    $this->load->model('BaseMPositionModel');
	    
	    $bforbpSign = element("bforbpSign",$params,'');
	    
	    
	    $dataArray=$this->CkLocationModel->get_locations('',$StuffId);
	    $iter = 0;
	    foreach ($dataArray as $rows) {
		    if ($rows['floor'] != '') {
			    
			    $toptitle = $this->BaseMPositionModel->get_name($rows['floor']);
			    $toptitle = str_replace('(3B)', '-3', $toptitle);
/*
			    $toptitle = str_replace('(3A)', '-3A', $toptitle);
			    $toptitle = str_replace('(2A)', '-2A', $toptitle);
			    $toptitle = str_replace('(2B)', '-2B', $toptitle);
*/
			    $toptitle = str_replace('(1)', '-1', $toptitle);
			    $dataArray[$iter]['toptitle']= $toptitle;
			    
			    
		    }
		    
		    $iter ++;
	    }
	    
	    $model = $bforbpSign == 'bf' ? 'Ck8bfsheetModel':'Ck7bprkModel';
	    $oldLocationId = '';
	    $oldRegion = '';
	    $oldRegionIndex = -1;
	    $regionCount = 0;
	    if ($bforbpSign == 'bf') {
		    
		    /**/
		    
		    if ($this->LoginNumber == 11965) {
			    
		    }
		    if ($editid != '') {
			    $this->load->model('Ck8bfsheetModel');
			    $oldLocationId = $this->Ck8bfsheetModel->get_locationid($editid);
			    
			    $oldRegionRow = $this->CkLocationModel->get_records($oldLocationId);
			    $oldRegion = $oldRegionRow['Region'].'区';

		    }
		    $newData = array();
		    
		    
		    foreach ($dataArray as $newRow) {
			    if ($newRow['qty']!= '' || $oldRegion == $newRow['title']) {
				    
				    $gettedsub = $newRow['sub'];
				    $newGetsub = array();
				    foreach($gettedsub as $subRow) {
					    if ($subRow['qty'] != '' || $subRow['Id']==$oldLocationId) {
						    $subRow['select'] = 1;
						    $newGetsub[]=$subRow;
					    }
				    }
				    if ($oldRegion != '' &&  $oldRegion == $newRow['title']) {
// 					    $newRow['select'] = '1';
						$oldRegionIndex = $regionCount;
				    }
				    $newRow['sub'] = $newGetsub;
				    $newData[]=$newRow;
				    $regionCount ++;
				    
			    }
		    }
		    
		    $dataArray= $newData;
		    
	    } else if ($bforbpSign =='bp' && $editid != '') {
		    $this->load->model('Ck7bprkModel');
		    $oldLocationId = $this->Ck7bprkModel->get_locationid($editid);
		    
		    $oldRegionRow = $this->CkLocationModel->get_records($oldLocationId);
		    $oldRegion = $oldRegionRow['Region'].'区';
		    
		    foreach ($dataArray as $newRow) {
			    if ($oldRegion == $newRow['title']) {
				    $oldRegionIndex = $regionCount;
				    $gettedsub = $newRow['sub'];
				    $jinner = 0;
				    foreach($gettedsub as $subRow) {
					    if ($subRow['Id']==$oldLocationId) {
						   $dataArray[$regionCount]['sub'][$jinner]['select'] = 1;
					    }
					    $jinner ++;
				    }
			    }
			    $regionCount ++;
		    }


	    }
	    $status = null;
	    if ($oldRegionIndex >= 0) {
		    $status  = array('pos'=>$oldRegionIndex);

	    }
	   $data['jsondata']=array('status'=>$status,'message'=>'','user'=>'','rows'=>$dataArray);
	    
		$this->load->view('output_json',$data);

	    
	    
    }

	
    
    function ware_abc() {
	    $params = $this->input->post();

		$this->load->model('StuffDataModel');
		$this->load->model('CkLocationModel');
		$records = null;
		$query = $this->CkLocationModel->get_region_keybord();
		if ($query->num_rows() > 0) {
			$rs = $query->result_array();
			$records = array();
			
			foreach ($rs as $rows) {
				$records[]=$rows['Region'];
			}
		}
		
	    $data['jsondata']=array('status'=>'','message'=>'1','totals'=>1,'rows'=>$records);
	    $this->load->view('output_json',$data);
    }
    
    
    function get_location_fromscan($scaninfo='') {
	    
	    $this->load->model('QcCjtjModel');
	    $this->load->model('CkrksheetModel');
	    
	    $location = '';
	    $length = strlen($scaninfo);
	    if ($length > 0) {
		    $firstChar = substr($scaninfo, 0, 1);
		    
		    $scaninfo = substr($scaninfo, 1, $length-1);
		    
		    $scaninfo = str_replace('*', '', $scaninfo);
		    
		    $infos = explode('|', $scaninfo);
		    $infosCount = count($infos);
		    
		    switch ($firstChar) {
			    case 'C': 
			    	if ($infosCount >= 2)
					$stockid = $this->QcCjtjModel->get_stockid('',$infos[1],$infos[0]);

			    break;
			    case 'N': 
			    	if ($infosCount >= 2)
			    	$stockid = $this->QcCjtjModel->get_stockid($infos[1]);
			    break;
			    case 'M': 
			    	$stockid = $infos[0];
			    
			    break;
			    default: break;
		    }
		    
		    if ($stockid != '') {
			    $location = $this->CkrksheetModel->get_stock_locationid($stockid,'1');
		    }
	    }
	    return $location;
    }
    
    function wareinfos() {
	    
	    
	    $params = $this->input->post();
		$stuffid = element('stuffid', $params , '0');
		$scaninfo = element('scaninfo', $params , '');
		$status = array('none'=>'');
		
		$this->load->model('StuffDataModel');
		$this->load->model('CkrksheetModel');
		$records =  $this->StuffDataModel->get_records($stuffid);
		
		$picture = $records['Picture'];
		if ($picture > 0) {
			$imgurl = $this->StuffDataModel->get_stuff_icon($stuffid);
		} else {
			$imgurl = '';
		}
		
		$waresInfo = $this->CkrksheetModel->get_stuff_locationqty($stuffid);
		$wares = array();
		$waresInfo = $waresInfo['data'];
		$tstockqty = 0;
		
		$selectWare = -1;
		if ($scaninfo!='') {
			$selectWare = $this->get_location_fromscan($scaninfo);
			$status['$selectWare'] = $selectWare;
		}
		$iter = 0;
		$selectWareIndex = -1;
		foreach ($waresInfo as $waresInfoRow) {
			$qtynum = intval( str_replace(',', '', $waresInfoRow['qty']) );
			$tstockqty += $qtynum;
			
			$alocation = $waresInfoRow['location']=='未设置'?'':$waresInfoRow['location'];
			if ($selectWareIndex == -1 && $alocation == $selectWare) {
				$selectWareIndex = $iter;
			}
/*
			if ($this->LoginNumber == 11965) {
// 				$qtynum = $qtynum*40;
			}
*/
			$wares[]=array(
				'title'=>$waresInfoRow['location']."\n".$waresInfoRow['qty'],
				'location' =>$alocation,
				'Id'=>$waresInfoRow['Ids'].'',
				'qtynum'=>$qtynum
			);
			$iter ++;
		}
		$status['img']=$imgurl;
		$status['Picture']=$picture;
		$status['inware']= number_format($tstockqty);
		$status['wares'] = $wares;
		if ($selectWareIndex >= 0) {
			$status['select'] = $selectWareIndex;
		}
		
	    $data['jsondata']=array('status'=>$status,'message'=>'1','totals'=>1,'rows'=>null);
	    $this->load->view('output_json',$data);

	    
    }
       
      
    function month_subtypes($month) {
	    
	    $this->load->model('Ck7bprkModel');

	    $query = $this->Ck7bprkModel->month_subtypes($month);
		$subList = array();
		$prechar = '¥';
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {

				$TypeName = $rows['TypeName'];
				$TypeId = $rows['TypeId'];
				
				$prechar = $rows['PreChar'];
				$Amount = $rows['Amount'];

				$subList[]=array(
					'tag'=>'shtotal',
					'Id'=>''.$TypeId,
					'nopayed'=>'0',
					'type'=>$month,
					'segIndex'=>'-1',
					'open'=>'',
					'showArrow'=>'1',
					'title'=>array('Text'=>$TypeName, 'Color'=>$this->color_bluefont),
					'col3'=>$prechar.number_format($Amount),
					'col2'=>number_format($rows['Qty']),
					'faceImg'=>''
				);

				
			}
	    }
	    return $subList;
	    
    }
    function bf_month_subtypes($month) {
	    
	    $this->load->model('Ck8bfsheetModel');

	    $query = $this->Ck8bfsheetModel->month_subtypes($month);
		$subList = array();
		$prechar = '¥';
		if ($query->num_rows() > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {

				$TypeName = $rows['TypeName'];
				$TypeId = $rows['TypeId'];
				
				$prechar = $rows['PreChar'];
				$Amount = $rows['Amount'];

				$subList[]=array(
					'tag'=>'shtotal',
					'Id'=>''.$TypeId,
					'nopayed'=>'0',
					'type'=>$month,
					'segIndex'=>'-1',
					'open'=>'',
					'showArrow'=>'1',
					'title'=>array('Text'=>$TypeName, 'Color'=>$this->color_bluefont),
					'col3'=>$prechar.number_format($Amount),
					'col2'=>number_format($rows['Qty']),
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
			case 0: $subList = $this->month_subtypes($Id);
			break;
			case 1: $subList = $this->bf_month_subtypes($Id);
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

	function type_month_list($TypeId, $month, $searched='') {
		
		$this->load->model('Ck7bprkModel');
	    $this->load->model('stuffdataModel');
	    
	    
	    
	    $query = $this->Ck7bprkModel->month_type_sublist($month,$TypeId);
		$subList = array();
		$rowNums = $query->num_rows();
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
	    
			$iterator = 0;
			foreach ($rs as $rows) {

				$iterator ++;

				$StuffId = $rows['StuffId'];
				
				$prechar = $rows['PreChar'];
				

				$imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';		
				
				
				     
				     
				$subList[]=array(
					'tag'=>'z_order',
					'Id'=>''.$rows['Id'],
					'hideLine'=>'1',
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'Picture'    =>$rows['Picture'],
					'titleX'=>'-20',
					'type'=>$TypeId,
					'col4'=>$prechar.number_format($rows['Qty']*$rows['Price']),
					'col4X'=>'-5',
					'col3X'=>'17',
					'completeImg'=>'',
					'col2Img'=>'ibl_gray.png',
					'col3Img'=>'',
					'col3'=>$prechar.number_format(round($rows['Price'],2),2),
					'productImg'     =>$imgurl,
	                'location'   =>array('Text'=>$rows['Region'] . $rows['Location'],'border'=>'1','Color'=>$this->color_bluefont),
					'col2'=>number_format($rows['Qty']),
				);

				$subList[]=array(
					'tag'=>'remarkNew',
					'margin_left'=>'70',
					'separ_left'=>$iterator== $rowNums ? '0':'70',
					'content'=>$rows['Remark'],
					'oper'=>date('m-d', strtotime($rows['Date'])).'  '.$rows['Operator']
					
				);
				
			}
	    }
	    return $subList;
		
	}
	
	
	function get_notok_record($searched='') {
		
		$this->load->model('Ck7bprkModel');
	    $this->load->model('stuffdataModel');
	    
	    
	    if ($searched!='') {
		    $query = $this->Ck7bprkModel->searched_list($searched);
	    } else 
	    $query = $this->Ck7bprkModel->notoklist();
		$subList = array();
		$rowNums = $query->num_rows();
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
	    
			$iterator = 0;
			foreach ($rs as $rows) {

				$iterator ++;

				$StuffId = $rows['StuffId'];
				
				$prechar = $rows['PreChar'];
				

				$imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';		     
				     
				$estateImg = '';
				$actions = array();
				$backed='';
				switch ($rows['Estate']) {
					case 2:{
						$estateImg = '';
						$backed = 'backed';
						
						
						$actions[]=array('Name'=>'删除','Action'=>'delete_row','Color'=>$this->color_red);
						$actions[]=array('Name'=>'修改','Action'=>'modify','Color'=>$this->color_bluefont);
						
					}
					break;
					case 1:{
						$estateImg = 'vacation_state1';
						$actions[]=array('Name'=>'删除','Action'=>'delete_row','Color'=>$this->color_red);
						$actions[]=array('Name'=>'修改','Action'=>'modify','Color'=>$this->color_bluefont);

/*
						
						$actions[]=array('Name'=>'退回','Action'=>'returnback','Color'=>$this->color_red);
						$actions[]=array('Name'=>'通过','Action'=>'pass','Color'=>$this->color_lightgreen);
					
*/
					}
					break;
					case 0:{
						if ($searched != '') {
							$estateImg = 'vacation_state0';
						}
					}
					break;
				}

				$subList[]=array(
					'tag'=>'z_order',
					'Id'=>''.$rows['Id'],
					'stuffid'=>"$StuffId",
					'actions'=>$actions,
					'completeImg'=>$backed,
					'hideLine'=>'1',
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'Picture'    =>$rows['Picture'],
					'titleX'=>'-20',
					'type'=>'',
					'col4'=>$prechar.number_format($rows['Qty']*$rows['Price']),
					'col4X'=>'-5',
					'col3X'=>'17',
					'col2Img'=>'ibl_gray.png',
					'shipImg'=>$estateImg,
					'shipImgX'=>'-5',
					'shipImgY'=>'20',
					'col3Img'=>'',
					'col3'=>$prechar.number_format(round($rows['Price'],2),2),
					'productImg'     =>$imgurl,
	                'location'   =>array('Text'=>$rows['Region'] . $rows['Location'],'border'=>'1','Color'=>$this->color_bluefont),
					'col2'=>number_format($rows['Qty']),
				);


				$content2 = null;
				$oper2 = null;
				
				if ($rows['Estate']==2) {
	               $checkReason = $this->Ck7bprkModel->get_returnreason_row($rows['Id']);
 
 					if ($checkReason != null) {
	 					$content2 = $checkReason['Reason'];
	 					$checkReason['Name'] = $checkReason['Name']==''?'system':$checkReason['Name'];
						$content2 = $content2 == '' ? '已退回':$content2;
						$content2 = array('Text'=>$content2, 'Color'=>$this->color_red);
						$oper2 = date('m-d', strtotime($checkReason['DateTime'])).'  '.$checkReason['Name'];
 					}
               }

				$subList[]=array(
					'tag'=>'remarkNew',
					'margin_left'=>'70',
					'separ_left'=>$iterator== $rowNums ? '0':'70',
					'content2'=>$content2,
					'oper2'=>$oper2,
					'content'=>$rows['Remark'],
					'oper'=>date('m-d', strtotime($rows['Date'])).'  '.$rows['Operator']
					
				);
				
			}
	    }
	    return $subList;

		
	}
	
	
	function bf_get_notok_record($searched='') {
		
		$this->load->model('Ck8bfsheetModel');
	    $this->load->model('stuffdataModel');
	    
	    
	    if ($searched!='') {
		    $query = $this->Ck8bfsheetModel->searched_list($searched);
	    } else 
	    $query = $this->Ck8bfsheetModel->notoklist();
		$subList = array();
		$rowNums = $query->num_rows();
		
		$baseurl = $this->config->item('download_path') . "/ckbf/";
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
	    
			$iterator = 0;
			foreach ($rs as $rows) {

				$iterator ++;

				$StuffId = $rows['StuffId'];
				
				$prechar = $rows['PreChar'];
				
				$estateImg = '';
				$backed = '';
				$actions = array();
				switch ($rows['Estate']) {
					case 2:{
						$estateImg = 'vacation_state2';
						$backed = 'backed';
						$estateImg = '';
						$actions[]=array('Name'=>'删除','Action'=>'delete_row','Color'=>$this->color_red);
						$actions[]=array('Name'=>'修改','Action'=>'modify','Color'=>$this->color_bluefont);
						
					}
					break;
					case 1:{
						$estateImg = 'vacation_state1';
						if ($this->LoginNumber == 11965) {
							$actions[]=array('Name'=>'修改','Action'=>'modify','Color'=>$this->color_bluefont);
						
						}
						/*
						$actions[]=array('Name'=>'退回','Action'=>'returnback','Color'=>$this->color_red);
						$actions[]=array('Name'=>'通过','Action'=>'pass','Color'=>$this->color_lightgreen);
*/
						$actions[]=array('Name'=>'删除','Action'=>'delete_row','Color'=>$this->color_red);
						$actions[]=array('Name'=>'修改','Action'=>'modify','Color'=>$this->color_bluefont);
					
					}
					break;
					case 0:{
						if ($searched != '') {
							$estateImg = 'vacation_state0';
						}
					}
					break;
				}
				

				$imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';		     
				     
				$subList[]=array(
					'tag'=>'z_order',
					'Id'=>''.$rows['Id'],
					'stuffid'=>"$StuffId",
					'actions'=>$actions,
					'hideLine'=>'1',
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'Picture'    =>$rows['Picture'],
					'titleX'=>'-20',
					'completeImg'=>$backed,
					'type'=>'',
					'col4'=>$prechar.number_format($rows['Qty']*$rows['Price']),
					'col4X'=>'-5',
					'col3X'=>'17',
					'col2Img'=>'irubbish.png',
					'shipImg'=>$estateImg,
					'shipImgX'=>'-5',
					'shipImgY'=>'20',
					'col3Img'=>'',
					'col3'=>$prechar.number_format(round($rows['Price'],2),2),
					'productImg'     =>$imgurl,
	                'location'   =>array('Text'=>$rows['Region'] . $rows['Location'],'border'=>'1','Color'=>$this->color_bluefont),
					'col2'=>number_format($rows['Qty']),
				);


				$content2 = null;
				$oper2 = null;
				
				if ($rows['Estate']==2) {
	               $checkReason = $this->Ck8bfsheetModel->get_returnreason_row($rows['Id']);
 
 					if ($checkReason != null) {
	 					$content2 = $checkReason['Reason'];
	 					$checkReason['Name'] = $checkReason['Name']==''?'system':$checkReason['Name'];
						$content2 = $content2 == '' ? '已退回':$content2;
						$content2 = array('Text'=>$content2, 'Color'=>$this->color_red);
						$oper2 = date('m-d', strtotime($checkReason['DateTime'])).'  '.$checkReason['Name'];
 					}
               }				
				
				$hasBill = $rows["Bill"];
		        $FileArr = array();
		        $Id = $rows['Id'];
		        if ($hasBill>0) {
			        $FileArr[]=array(
			        "Type"=>"img",
			        "url"=>"$baseurl"."B$Id".".jpg",
			        "sType"=>"jpg"
			        );
		        }

				
				$subList[]=array(
					'tag'=>'remarkNew',
					'margin_left'=>'70',
					'imgs'=>$FileArr,
					'separ_left'=>$iterator== $rowNums ? '0':'70',
					'content'=>$rows['Remark'],
					'oper'=>date('m-d', strtotime($rows['Date'])).'  '.$rows['Operator'],
					'content2'=>$content2,
					'oper2'=>$oper2
					
				);
				
			}
	    }
	    return $subList;
		

		
	}
	
	function bf_type_month_list($TypeId, $month, $aInvoiceNO='') {
		
		$this->load->model('Ck8bfsheetModel');
	    $this->load->model('stuffdataModel');
	    
	    
	    
	    $query = $this->Ck8bfsheetModel->month_type_sublist($month,$TypeId);
		$subList = array();
		$rowNums = $query->num_rows();
		
		$baseurl = $this->config->item('download_path') . "/ckbf/";
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
	    
			$iterator = 0;
			foreach ($rs as $rows) {

				$iterator ++;

				$StuffId = $rows['StuffId'];
				
				$prechar = $rows['PreChar'];
				

				$imgurl =$rows['Picture']==1?$this->stuffdataModel->get_stuff_icon($StuffId):'';		     
				     
				$subList[]=array(
					'tag'=>'z_order',
					'Id'=>''.$rows['Id'],
					'hideLine'=>'1',
					'title'=>$StuffId.'-'.$rows['StuffCname'],
					'Picture'    =>$rows['Picture'],
					'titleX'=>'-20',
					'type'=>$TypeId,
					'col4'=>$prechar.number_format($rows['Qty']*$rows['Price']),
					'col4X'=>'-5',
					'col3X'=>'17',
					'completeImg'=>'',
					'col2Img'=>'irubbish.png',
					'col3Img'=>'',
					'col3'=>$prechar.number_format(round($rows['Price'],2),2),
					'productImg'     =>$imgurl,
	                'location'   =>array('Text'=>$rows['Region'] . $rows['Location'],'border'=>'1','Color'=>$this->color_bluefont),
					'col2'=>number_format($rows['Qty']),
				);

				$bfInfo = $this->Ck8bfsheetModel->get_bf_remark($rows['Id']);
				$content2 = null;
				$oper2 = null;
				if ($bfInfo != null) {
					$content2 = $bfInfo['Remark'];
					$content2 = $content2 == '' ? '已做报废处理':$content2;
					$content2 = array('Text'=>$content2, 'Color'=>$this->color_bluefont);
					$oper2 = date('m-d', strtotime($bfInfo['Date'])).'  '.$bfInfo['Name'];
				}
				
				
				$hasBill = $rows["Bill"];
		        $FileArr = array();
		        $Id = $rows['Id'];
		        if ($hasBill>0) {
			        $FileArr[]=array(
			        "Type"=>"img",
			        "url"=>"$baseurl"."B$Id".".jpg",
			        "sType"=>"jpg"
			        );
		        }

				
				$subList[]=array(
					'tag'=>'remarkNew',
					'margin_left'=>'70',
					'imgs'=>$FileArr,
					'separ_left'=>$iterator== $rowNums ? '0':'70',
					'content'=>$rows['Remark'],
					'oper'=>date('m-d', strtotime($rows['Date'])).'  '.$rows['Operator'],
					'content2'=>$content2,
					'oper2'=>$oper2
					
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
						$subList = $this->type_month_list($Id, $type);
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
						$subList = $this->bf_type_month_list($Id, $type);
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
	function bf_month_list() {
		
		$this->load->model('Ck8bfsheetModel');
		
		$query = $this->Ck8bfsheetModel->get_months_record();
		$sectionList = array();
		
		$subNotOk = $this->bf_get_notok_record();
		$sectionList[]=array('data'=>$subNotOk);
		
		$sortAmount = array();
		$sortAmount[]=0;
		$rowNums = $query->num_rows();
		$thisMonth = date('Y-m');
		$prechar = '¥';
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {
				
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



				
				$opened = 0;
				$datasub = array();
/*
				if ($month == $thisMonth) {
					$opened = 1;
					$datasub = $this->month_subtypes($month);
				}
*/

				$sectionList[]=array(
					'tag'=>'shhead',
					'Id'=>$month,
					'segIndex'=>'-1',
					'open'=>''.$opened,
					'data'=>$datasub,
					'nopayed'=>'0',
					'showArrow'=>'1',
					'method'=>'segements',
					'title'=>$titleAttr,
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
		    $sectionList[$key]['faceImg']='face_2';
		    if ($j==3) break;
		}

		/*
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
		*/
				
		return $sectionList;
	}
	function month_list() {
		
		$this->load->model('Ck7bprkModel');
		
		$query = $this->Ck7bprkModel->get_months_record();
		$sectionList = array();
		$sortAmount = array();
		$rowNums = $query->num_rows();
		$thisMonth = date('Y-m');
		$prechar = '¥';
		
		$subNotOk = $this->get_notok_record();
		$sectionList[]=array('data'=>$subNotOk);
		$sortAmount[]=0;
		if ($rowNums > 0) {
			
			$rs = $query->result_array();
			
			foreach ($rs as $rows) {
				
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



				
				$opened = 0;
				$datasub = array();
/*
				if ($month == $thisMonth) {
					$opened = 1;
					$datasub = $this->month_subtypes($month);
				}
*/

				$sectionList[]=array(
					'tag'=>'shhead',
					'Id'=>$month,
					'segIndex'=>'-1',
					'open'=>''.$opened,
					'data'=>$datasub,
					'nopayed'=>'0',
					'showArrow'=>'1',
					'method'=>'segements',
					'title'=>$titleAttr,
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
		    if ($j==3) break;
		}

		/*
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
		*/
				
		return $sectionList;
	}
	
	public function search_m() {
		
		$params = $this->input->post();
		$menu_id = element('menu_id', $params , '0');
		$searched = element('search', $params , '');
		$sectionList = array();
		if ($searched != '') {
			switch ($menu_id) {
			case 0: 
				
				
				$sectionList []=array('data'=>$this->get_notok_record($searched)) ;
			break;
			case 1: 
			$sectionList []=array('data'=>$this->bf_get_notok_record($searched)) ;
			break;
		}
		}
		
	    $data['jsondata']=array('status'=>'1','message'=>'1','totals'=>1,'rows'=>$sectionList);
	    $this->load->view('output_json',$data);
	}
	
	public function main() {
		
		$params = $this->input->post();
		$menu_id = element('menu_id', $params , '0');
		$sectionList = array();
		switch ($menu_id) {
			case 0: 
				
				
				$sectionList = $this->month_list();
			break;
			case 1: $sectionList = $this->bf_month_list();
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
	
	
}