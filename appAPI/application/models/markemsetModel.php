<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MarkemSetModel extends MC_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    
    function get_markem_setting($sPOrderId,$LineId,$SortBoxId)
    {
	    $dataArray=$this->get_config_format('Default');
	    
	    $this->load->model('WorksclineModel');
	    $this->load->model('ScSheetModel');
	    $this->load->model('YwOrderSheetModel');
	    $this->load->model('staffgroupModel');
	    $this->load->model('YwclientOutDataModel');
	    
	    $sclines= $this->WorksclineModel->get_records($LineId);
	    $IP     = $sclines['IP'];
	    $GroupId= $sclines['GroupId'];
	    $line   = $sclines['Letter'];
	    
	    $groups=$this->staffgroupModel->get_records($GroupId);
        $GroupLeader= $groups['GroupLeader'];
	    
	    $records=$this->ScSheetModel->get_records($sPOrderId);
        $WorkShopId = $records['WorkShopId'];
        $POrderId   = $records['POrderId'];
        
        $orders=$this->YwOrderSheetModel->get_records($POrderId);
        $ProductId  = $orders['ProductId'];
        $cName      = $orders['cName'];
        $eCode      = $orders['eCode'];
        $OrderPO    = $orders['OrderPO'];
        $Forshort   = $orders['Forshort'];
        $codedate   = date('YmdHis');
        
        $toout_name = $this->YwclientOutDataModel->toout_name($POrderId);
        if ($toout_name != '') {
	        $toout_name = ' '.$toout_name;
        }
                
        $dataArray['IP']   =$IP;
        $dataArray['Data'] =array(
			                           'productName'    =>"$cName",
			                           'productCode'    =>"$eCode",
			                           'line'           =>"$line",
			                           'OrderPO'        =>"$OrderPO($Forshort".$toout_name.')',
			                           'POrderId'       =>"$sPOrderId",
			                           'pick'           =>"$SortBoxId",
			                           'date'           =>$codedate,
			                           'codedate'       =>$this->Date,
			                           'ProductId'      =>"$ProductId",
			                           'staffNumber'    =>"$GroupLeader"
			                   );   
		return 	$dataArray;                   
    }
    
     function get_config_format($format){
       $formats=array();
       switch($format){
	       case 'Default':
	          $formats= array('IP'   =>'IP??????',
			                  'Type' =>'Default',
			                  'Data' =>array(
			                           'productName'    =>'????????????',
			                           'productCode'    =>'??????????????????',
			                           'line'           =>'??????(A/B...)',
			                           'OrderPO'        =>'PO',
			                           'POrderId'       =>'???????????????',
			                           'pick'           =>'?????????(1-7)',
			                           'date'           =>'??????(yyyy-MM-dd hh:mm:ss)',
			                           'codedate'       =>'??????(yyyyMMddhhmmss)',
			                           'ProductId'      =>'??????Id',
			                           'staffNumber'    =>'????????????Id'
			                   )      
			               );
	       break;
       }
	   return $formats;
    }
}