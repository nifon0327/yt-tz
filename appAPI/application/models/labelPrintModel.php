<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  LabelPrintModel extends MC_Model {

    function __construct()
    {
        parent::__construct();
    }
    

    function get_printer_IP($WorkAdd,$Floor){
    
	    $this->db->select('IP');
	    $query = $this->db->get_where('ot5_printer', array('WorkAdd' =>$WorkAdd,'Floor' => $Floor,'Estate'=>1));
	    $rows=$query->first_row('array');
	    return $rows['IP'];
    }
    
    function get_ckprinter_IP($Floor,$Identifier){
    
	    $this->db->select('IP');
	    $query = $this->db->get_where('ot5_printer', array('Floor' => $Floor,'Identifier' =>$Identifier,'Estate'=>1));
	    $rows=$query->first_row('array');
	    return $rows['IP'];
    }
    
    function get_ckmainprinter_IP($Floor){
    
	    $this->db->select('IP');
	    $query = $this->db->get_where('ot5_printer', array('Floor' => $Floor,'PrintSign'=>'1'));
	    $rows=$query->first_row('array');
	    return $rows['IP'];
    }
    
    //工序登记半成品标签打印
    function get_gxregister_print($sPOrderId,$djQty)
    {
        $dataArray=$this->get_printlayout('Default');
        
        $this->load->model('ScSheetModel');
        $this->load->model('WorkShopdataModel');
        $this->load->model('StuffdataModel');
        $this->load->model('staffMainModel');
        $this->load->model('sccjtjModel');
        
	    $scedQty  =$this->sccjtjModel->get_scqty($sPOrderId);
	    
        $records=$this->ScSheetModel->get_records($sPOrderId);
        $WsId         =$records['WorkShopId'];
        $mStockId     =$records['mStockId'];
        $StuffId      =$records['StuffId'];
        $OrderQty     =$records['Qty'];
        $DeliveryWeek =$records['DeliveryWeek'];
        $Forshort     =$records['WorkShopName'];
        
        $DeliveryWeek =$DeliveryWeek>0?substr($DeliveryWeek,4,2):'00';
        
        $records       =null;
        $records       =$this->StuffdataModel->get_records($StuffId);
	    $cName         =$records['StuffCname'];
	    $FrameCapacity =$records['FrameCapacity'];
	    $Forshort      =$Forshort==''?$records['Forshort']:$Forshort;

	    $records  =null;
	    $records  =$this->WorkShopdataModel->get_records($WsId);
	    $WorkAdd  =$records['WorkAddId'];
	    $Floor    =$records['Floor'];
	    
	    $IP=    $this->get_printer_IP($WorkAdd,$Floor);
	    $oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
	    $Frames=$this->get_frametotals($OrderQty,$FrameCapacity);
	    
	    $month = date('Y-m');
	    
	    $dataArray['IP']   =$IP;
	    $dataArray['Pages']=$this->get_printer_pages($scedQty,$djQty,$OrderQty,$FrameCapacity);
	    $dataArray['Data'] =array(
			                           'CGPO'    =>"M$mStockId",
			                           'noN'=>'1',
			                           'Week'    =>"$DeliveryWeek",
			                           'cName'   =>"$StuffId-$cName",
			                           'OrderQty'=>"$OrderQty",
			                           'Frame'   =>'1/'.$Frames,
			                           'oper'    =>"$oper",
			                           'Forshort'=>"$Forshort",
			                           'Qty'     =>"$FrameCapacity",
			                           'datee'   =>"$month    ",
  			                           'hidetime'=>''
			                   );
			                   
	    return $dataArray;
    }
    
    //工序已登记数量标签打印 （补印）
    function get_gxrecord_print($sPOrderId,$scedQty,$djQty)
    {
        $dataArray=$this->get_printlayout('Default');
        
        $this->load->model('ScSheetModel');
        $this->load->model('WorkShopdataModel');
        $this->load->model('StuffdataModel');
        $this->load->model('staffMainModel');
        $this->load->model('sccjtjModel');
	    
        $records=$this->ScSheetModel->get_records($sPOrderId);
        $WsId         =$records['WorkShopId'];
        $mStockId     =$records['mStockId'];
        $StuffId      =$records['StuffId'];
        $OrderQty     =$records['Qty'];
        $DeliveryWeek =$records['DeliveryWeek'];
        $Forshort     =$records['WorkShopName'];
        
        $DeliveryWeek =$DeliveryWeek>0?substr($DeliveryWeek,4,2):'00';
        
        $records       =null;
        $records       =$this->StuffdataModel->get_records($StuffId);
	    $cName         =$records['StuffCname'];
	    $FrameCapacity =$records['FrameCapacity'];
	    $Forshort      =$Forshort==''?$records['Forshort']:$Forshort;

	    $records  =null;
	    $records  =$this->WorkShopdataModel->get_records($WsId);
	    $WorkAdd  =$records['WorkAddId'];
	    $Floor    =$records['Floor'];
	    
	    $IP=    $this->get_printer_IP($WorkAdd,$Floor);
	    $oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
	    $Frames=$this->get_frametotals($OrderQty,$FrameCapacity);
	    
	    $month = date('Y-m');
	    $dataArray['IP']   =$IP;
	    $dataArray['Pages']=$this->get_printer_pages($scedQty,$djQty,$OrderQty,$FrameCapacity);
	    $dataArray['Data'] =array(
			                           'CGPO'    =>"M$mStockId",
			                           'noN'=>'1',
			                           'Week'    =>"$DeliveryWeek",
			                           'cName'   =>"$StuffId-$cName",
			                           'OrderQty'=>"$OrderQty",
			                           'Frame'   =>'1/'.$Frames,
			                           'oper'    =>"$oper",
			                           'Forshort'=>"$Forshort",
			                           'Qty'     =>"$FrameCapacity",
			                           'datee'   =>"$month    ",
			                           'hidetime'=>''
			                   );
			                   
	    return $dataArray;
    }
    
    //品检登记标签打印
    function get_qcregister_print($Sid,$newId,$djQty,$bpQty=0)
    {
        $dataArray=$this->get_printlayout('Qc');
        
        $this->load->model('GysshsheetModel');
        $this->load->model('StuffdataModel');
        $this->load->model('staffMainModel');
        $this->load->model('QcCjtjModel');
        $this->load->model('QcMissionModel');
        $this->load->model('QcsclineModel');
        
        $records=$this->GysshsheetModel->get_records($Sid);
        $Floor        =$records['Floor'];
        $StockId      =$records['StockId'];
        $StuffId      =$records['StuffId'];
        $Qty          =$records['Qty'];
        $Forshort     =$records['Forshort'];
        $cName        =$records['StuffCname'];
	    $FrameCapacity=$records['FrameCapacity'];
	    $CheckSign    =$records['CheckSign'];
	    
        //$DeliveryWeek =$records['DeliveryWeek'];
        
	    $scedQty  =$this->QcCjtjModel->get_qcqty($Sid);
	    
	    $DeliveryWeek=$this->ThisWeek;
        $DeliveryWeek =substr($DeliveryWeek,4,2);
        
        if ($Forshort=='' || $cName==''){
	        $records       =null;
	        $records       =$this->StuffdataModel->get_records($StuffId);
		    $cName         =$records['StuffCname'];
		    $FrameCapacity =$records['FrameCapacity'];
		    $Forshort      =$records['Forshort'];
		    $CheckSign     =$records['CheckSign'];
        }
        
	    $records       =null;
		$records=$this->QcMissionModel->get_records($Sid);
		$LineId= $records['LineId'];
		
		$records       =null;
		$records=$this->QcsclineModel->get_records($LineId);
		$Line= $records['Line'];

	    $IP=    $this->get_ckprinter_IP($Floor,$Line);
	    $oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
	    
	    $Qty=$Qty-intval($Qty)<0.1?number_format($Qty):number_format($Qty,1);
	    $CheckSign=$CheckSign==0?'抽检':'全检';
	    
	    $month = date('Y-m');
	    $dataArray['IP']   =$IP;
	    $dataArray['Pages']=$this->get_qcprinter_pages($scedQty,$djQty,$bpQty,$FrameCapacity);
	    $dataArray['Data'] =array( 
                               'qrcode'  =>"$StuffId|$newId",
	                           'stuffid' =>"$StuffId",
	                           'cName'   =>"$cName",
	                           'order'   =>"$Qty",
	                           'way'     =>"$CheckSign",
	                           'oper'    =>"$oper",
	                           'Forshort'=>"$Forshort",
	                           'Qty'     =>"$FrameCapacity",
	                           'datee'   =>" $month"
	                           );
			                   
	    return $dataArray;
    }
    
    //退料标签打印  
    function get_qcbadrecords_print($Bid)
    {
        $dataArray=array(); 
        $this->load->model('QcBadrecordModel');
        
        $records = $this->QcBadrecordModel->get_records($Bid);
        $Id      =$records['Id'];
        $Sid     =$records['Sid'];
        $StockId =$records['StockId'];
        $StuffId =$records['StuffId'];
        $Qty     =$records['Qty'];
        $Weeks   =substr($records['Weeks'],4);
        
        if ($Qty>0){
            $dataArray=$this->get_printlayout('thBad');
            
            $this->load->model('GysshsheetModel');
            $this->load->model('StuffdataModel');
            $this->load->model('staffMainModel');
            $this->load->model('StuffPropertyModel');

	        $Propertys = $this->StuffPropertyModel->get_property_array($StuffId);
	        
	        $cuases  = $this->QcBadrecordModel->get_causesarray($Id);
	        
	        $records = null;
	        if ($Sid>0){
		        $records = $this->GysshsheetModel->get_records($Sid);
		        $Forshort     =$records['Forshort'];
		        $cName        =$records['StuffCname'];
			    $FrameCapacity=$records['FrameCapacity'];
			    $CheckSign    =$records['CheckSign'];
			    $Floor        =$records['Floor']; 
	        }else{
		        $records = $this->StuffdataModel->get_records($StuffId);
		        $Forshort     =$records['Forshort'];
		        $cName        =$records['StuffCname'];
			    $FrameCapacity=$records['FrameCapacity'];
			    $CheckSign    =$records['CheckSign'];
			    $Floor        =$records['SendFloor']; 
	        }
	        
		    
		    $IP=    $this->get_ckmainprinter_IP($Floor);
		    $oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
		    
		    $CheckSign=$CheckSign==0?'抽检':'全检';
		    $dataArray['IP']   =$IP;
		    $dataArray['Pages']=$this->get_qcbadrecordsprinter_pages($Qty,$FrameCapacity);
		    $dataArray['FrameDIY']   ='1';
		    $Qty=$Qty-intval($Qty)<0.1?number_format($Qty):number_format($Qty,1);
		    $qcurl=$this->QcBadrecordModel->get_qualityreport_url($Id,$StockId);
		    
		    $month = date('Y-m');
		    $dataArray['Data'] =array( 
	                               'badurl'  =>"$qcurl",
	                               '$Floor'=>$Floor,
		                           'Week'    =>"$Weeks",
		                           'stuffid' =>"$StuffId",
		                           'cName'   =>"$cName",
		                           'props'   =>$Propertys,
		                           'Qty'     =>"$Qty",
		                           'frame1'  =>'',
		                           'frame2'  =>'',
		                           'oper'    =>"$oper",
		                           'Forshort'=>"$Forshort",
		                           'hidetime'=>'',
		                           'datee'   =>"      $month",
		                           'bads'    =>$cuases,
					                 );
        }
	    return $dataArray;
    }
    
     //品检登记记录标签补打印
    function get_qcrecord_print($Sid,$newId,$djQty,$bpQty=0,$scedQty=0)
    {
        $dataArray=$this->get_printlayout('Qc');
        
        $this->load->model('GysshsheetModel');
        $this->load->model('StuffdataModel');
        $this->load->model('staffMainModel');
        $this->load->model('QcCjtjModel');
        $this->load->model('QcMissionModel');
        $this->load->model('QcsclineModel');
        
        $records=$this->GysshsheetModel->get_records($Sid);
        $Floor        =$records['Floor'];
        $StockId      =$records['StockId'];
        $StuffId      =$records['StuffId'];
        $Qty          =$records['Qty'];
        $Forshort     =$records['Forshort'];
        $cName        =$records['StuffCname'];
	    $FrameCapacity=$records['FrameCapacity'];
	    $CheckSign    =$records['CheckSign'];
	    
        //$DeliveryWeek =$records['DeliveryWeek'];
	    
	    $DeliveryWeek=$this->ThisWeek;
        $DeliveryWeek =substr($DeliveryWeek,4,2);
        
        if ($Forshort=='' || $cName==''){
	        $records       =null;
	        $records       =$this->StuffdataModel->get_records($StuffId);
		    $cName         =$records['StuffCname'];
		    $FrameCapacity =$records['FrameCapacity'];
		    $Forshort      =$records['Forshort'];
		    $CheckSign     =$records['CheckSign'];
        }
        
	    $records       =null;
		$records=$this->QcMissionModel->get_records($Sid);
		$LineId= $records['LineId'];
		
		$records       =null;
		$records=$this->QcsclineModel->get_records($LineId);
		$Line= $records['Line'];

	    $IP=    $this->get_ckprinter_IP($Floor,$Line);
	    $oper=  $this->staffMainModel->get_staffname($this->LoginNumber);
	    
	    $Qty=$Qty-intval($Qty)<0.1?number_format($Qty):number_format($Qty,1);
	    $CheckSign=$CheckSign==0?'抽检':'全检';
	    
	    $month = date('Y-m');
	    $dataArray['IP']   =$IP;
	    $dataArray['Pages']=$this->get_qcprinter_pages($scedQty,$djQty,$bpQty,$FrameCapacity);
	    $dataArray['Data'] =array( 
                               'qrcode'  =>"$StuffId|$newId",
	                           'stuffid' =>"$StuffId",
	                           'cName'   =>"$cName",
	                           'order'   =>"$Qty",
	                           'way'     =>"$CheckSign",
	                           'oper'    =>"$oper",
	                           'Forshort'=>"$Forshort",
	                           'Qty'     =>"$FrameCapacity",
	                           'datee'   =>" $month"
	                           );
			                   
	    return $dataArray;
    }
    

    
    //获取打印页数
    function get_printer_pages($scedQty,$djQty,$OrderQty,$FrameCapacity)
    {
        $pages=array();
        
        if ($FrameCapacity==0) return $pages;
        
	    $Qty0 =$scedQty-$djQty;
	    
	    $n=$Qty0<=0?1:intval($Qty0/$FrameCapacity)+1;//开始箱号
	    
	    $Frames=$this->get_frametotals($OrderQty,$FrameCapacity);
	    
	    $m=$FrameCapacity>0?intval($scedQty/$FrameCapacity):0;  //结束箱号
        
        $Qty=0; 
	    for($i=$n;$i<=$m;$i++){
	        $frame=$i .'/' .$Frames;
	        $pages[]=array('Qty'=>"$FrameCapacity",'Frame'=>"$frame"); 
	    }
	    
	    if ($scedQty>=$OrderQty){
	        $lastQty=$OrderQty-$m*$FrameCapacity;//尾数箱
	        if ($lastQty>0){
	           $frame=$Frames .'/' .$Frames;
		       $pages[]=array('Qty'=>"$lastQty",'Frame'=>"$frame");  
	        }
	    
	       $mQty=$scedQty-$OrderQty;
	       if ($mQty>0){
		      $pages[]=array('Qty'=>"$mQty",'subTitle'=>'备品','Frame'=>''); 
	       }
	    }
	    return $pages;
    }
    
    function get_qcprinter_pages($scedQty,$djQty,$bpQty,$FrameCapacity)
    {
        $pages=array();
        
        if ($FrameCapacity==0) return $pages;
	    
	    $n=intval($djQty/$FrameCapacity);
	    for($i=0;$i<$n;$i++){
	        $pages[]=array('Qty'=>"$FrameCapacity",'Frame'=>""); 
	    }
	    
	    $lastQty=$djQty-$n*$FrameCapacity;
	    
	    if ($lastQty>0){
		    $pages[]=array('Qty'=>"$lastQty",'Frame'=>"");  
	    }
	    
	     if ($bpQty>0){
		    $pages[]=array('Qty'=>"$bpQty",'subTitle'=>'备品','Frame'=>'');  
	    }

	    
	    return $pages;
    }
    
    function get_qcbadrecordsprinter_pages($Qty,$FrameCapacity)
    {
        $pages=array();
        
        if ($FrameCapacity==0) {
           $pages[]=array('Qty'=>"$Qty",'Frame'=>"1/1");
	    }
	    else{
		   
		   $Frames=$this->get_frametotals($Qty,$FrameCapacity);
		   
		   $n=intval($Qty/$FrameCapacity);
	       for($i=1;$i<=$n;$i++){
	           $frame=$i .'/' .$Frames;
	           $pages[]=array('Qty'=>"$FrameCapacity",'Frame'=>"$frame"); 
	       }
	       
	       $lastQty=$Qty-$n*$FrameCapacity;//尾数箱
           if ($lastQty>0){
	           $frame=$Frames .'/' .$Frames;
		       $pages[]=array('Qty'=>"$lastQty",'Frame'=>"$frame");  
	        }
	    }
	    return $pages;
    }


    
    //获取总箱数
    function get_frametotals($OrderQty,$FrameCapacity)
    {
          if ($FrameCapacity==0) return;
          
	      $Frames=intval($OrderQty/$FrameCapacity);
	      
	      return ($OrderQty%$FrameCapacity)>0?$Frames+1:$Frames;
    }
    
    function get_printlayout($layout){
       
       $Layouts=array();
       switch($layout){
	       case 'Default':
	          $Layouts= array('IP'   =>'IP地址',
			                  'Type' =>'Default',
			                  'Pages'=>array(
			                           array('Qty'=>'100'),
			                           array('Qty'=>'200')
			                          ),
			                   'Data'=>array(
			                           'CGPO'    =>'M采购流水号',
			                           'noN'=>'1',
			                           'Week'    =>'交货期（周）',
			                           'cName'   =>'配件名称',
			                           'OrderQty'=>'采购数量',
			                           'Frame'   =>'第几框/框总数',
			                           'oper'    =>'操作员',
			                           'Forshort'=>'供应商简称',
			                           'Qty'     =>'数量',
			                           'hidetime'=>''
			                   )      
			               );
	       break;
	     case 'Qc':
	        $Layouts= array('IP'   =>'IP地址',
			                  'Type' =>'QcNew',
			                  'Pages'=>array(
			                           array('Qty'=>'100'),
			                           array('Qty'=>'20')
			                          ),
			                   'Data'=>array(
			                           'qrcode'  =>'产量登记Id',
			                           'stuffid' =>'配件Id',
			                           'cName'   =>'配件名称',
			                           'order'   =>'采购数量',
			                           'way'     =>'全检',
			                           'oper'    =>'操作员',
			                           'Forshort'=>'供应商简称',
			                           'Qty'     =>'数量'
			                   )      
			               );
			break;
		 case 'thBad':
	        $Layouts= array('IP'   =>'IP地址',
			                  'Type' =>'Bad',
			                  'Pages'=>array(
			                           array('Qty'=>'100'),
			                           array('Qty'=>'20')
			                          ),
			                   'Data'=>array(
			                           'badurl'  =>'品检报告URL',//http://qcurl
			                           'Week'    =>'退货周',
			                           'stuffid' =>'配件Id',
			                           'cName'   =>'配件名称',
			                           'props'   =>array('配件属性1','配件属性2'),
			                           'Qty  '   =>'退货数量',
			                           'frame1'  =>'第几框',
			                           'frame2'  =>'框总数',
			                           'oper'    =>'操作员',
			                           'Forshort'=>'供应商简称',
			                           'hidetime'=>'',
			                           'bads'    =>array(
						                           array('原因1','6'),
						                           array('原因2','4')
						                          ),
			                   )      
			               );
			break;	              
       }
	   return $Layouts;
    }
  
}