<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CgStockmainModel extends MC_Model {

     function get_maxPurchaseID()
     {
          $DateTemp=date('Y');
	      $sql = "SELECT MAX(PurchaseID) AS maxID FROM cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'"; 	
	      $query=$this->db->query($sql);
	      $rows = $query->first_row('array');
	      $PurchaseID =$rows['maxID'];
	      
          if ($PurchaseID>0){
				    $PurchaseID =$PurchaseID+1;
		   }else{
				  $PurchaseID =$DateTemp."0001";
			}
			return $PurchaseID;
     }
     
	 function save_outbl_tomain($sPOrderId)
	 {
		   $this->load->model('ScSheetModel');
		   $this->load->model('CgstocksheetModel');
		   $this->load->model('CgSemifinishedModel');
		   
	       $records=$this->ScSheetModel->get_records($sPOrderId);
	       $StockId =$records['mStockId'];
	       $records=null;
	       
	       $records=$this->CgstocksheetModel->get_records($StockId);
	       $CompanyId=$records["CompanyId"];
           $BuyerId      =$records["BuyerId"];
           
           $PurchaseID= $this->get_maxPurchaseID();
  
	       $data=array(
	              'CompanyId'=>$CompanyId,  
	                    'BuyerId'=>$BuyerId,
	              'PurchaseID'=>$PurchaseID,
	            'DeliveryDate'=>'0000-00-00',
	                     'Remark'=>'半成品备料自动生成采购单',
	                         'Date'=>$this->Date,
	                  'Operator'=>$this->LoginNumber, 
	                     'creator'=>$this->LoginNumber,
	                    'created'=>$this->DateTime  
		       );
		       
		      $this->db->insert('cg1_stockmain', $data); 
		      $Mid = $this->db->insert_id();
		      if ($Mid>0){
		          $Price = $this->CgSemifinishedModel->get_processing_price($StockId);
		          
			      $data=array( 'Mid' =>$Mid,'Price'=>$Price,'Locks'=>0);
	              $this->db->update('cg1_stocksheet',$data, array('StockId' =>$StockId));
	              return $Mid;
		      }
		      return 0;
   }
	 	 
}