<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CkreplenishModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    function get_records($Id)
    {
	     $sql = "SELECT * FROM ck13_replenish WHERE Id=?"; 	
	     $query=$this->db->query($sql,$Id);
	   
	      return  $query->first_row('array');
    }
    
    //统计未补数量(按生产车间分类)
    function get_not_feedings($WorkShopIds,$blSign=0)
    {
      if ($blSign==1){
	          $sql="SELECT SUM(A.Qty) AS Qty,COUNT(*) AS Counts 
	             FROM (
	                   SELECT  S.Id, S.Qty,SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty    
						FROM ck13_replenish S 
						INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
						LEFT JOIN ck5_llsheet L ON L.FromId=S.Id AND L.StockId=S.StockId   AND L.Qty>0  
                        LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
						WHERE S.Estate=1 AND Y.WorkShopId  IN ($WorkShopIds) GROUP BY S.Id 
						)A WHERE A.Qty>A.llQty AND A.tStockQty>=(A.Qty-A.llQty) ";
	  }else{
		     $sql="SELECT  SUM(S.Qty) AS Qty,COUNT(*) AS Counts   
						FROM ck13_replenish S 
						INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
						WHERE S.Estate IN (1,2) AND Y.WorkShopId  IN ($WorkShopIds)";
	  }
					
		$query=$this->db->query($sql);
	    return  $query->first_row('array');			
    }
    
    //获取未补数量(按生产车间分类)
    function get_not_feeding_sheet($WorkShopIds,$blSign=0)
    {
         if ($blSign==1){
	          $sql="SELECT * FROM (
	                SELECT  S.Id,S.POrderId,S.sPOrderId,S.StockId,S.StuffId,S.Qty,S.Remark,S.Estate,S.creator,S.created,S.Auditor,S.AuditTime,S.ReturnReasons, 
		                            D.StuffCname,D.Picture,U.Decimals,K.tStockQty,Y.ActionId,
		                            N.Name AS StaffName,SUM(IFNULL(L.Qty,0)) AS llQty  
					FROM ck13_replenish S 
					INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
					INNER JOIN stuffdata D ON D.StuffId=S.StuffId
					INNER JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
					INNER JOIN stuffunit U ON U.Id=D.Unit 
					LEFT JOIN staffmain N ON N.Number=S.creator 
					LEFT JOIN ck5_llsheet L ON L.FromId=S.Id AND L.StockId=S.StockId   AND L.Qty>0  
					WHERE S.Estate=1  AND Y.WorkShopId IN ($WorkShopIds) GROUP BY S.Id 
					)A  WHERE A.Qty>A.llQty AND A.tStockQty>=(A.Qty-A.llQty) ORDER BY POrderId,Id ";
         }else{
	          $sql="SELECT  S.Id,S.POrderId,S.sPOrderId,S.StockId,S.StuffId,S.Qty,S.Remark,S.Estate,S.creator,S.created,S.Auditor,S.AuditTime,S.ReturnReasons, 
		                            D.StuffCname,D.Picture,U.Decimals,K.tStockQty,
		                            N.Name AS StaffName,SUM(IFNULL(L.Qty,0)) AS llQty  
					FROM ck13_replenish S 
					INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
					INNER JOIN stuffdata D ON D.StuffId=S.StuffId
					INNER JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
					INNER JOIN stuffunit U ON U.Id=D.Unit 
					LEFT JOIN staffmain N ON N.Number=S.creator 
					LEFT JOIN ck5_llsheet L ON L.FromId=S.Id AND L.StockId=S.StockId AND L.Qty>0 
					WHERE S.Estate IN (1,2) AND Y.WorkShopId IN ($WorkShopIds) GROUP BY S.Id ORDER BY POrderId,Id ";
         }	
			$query=$this->db->query($sql);
		    if ($query->num_rows() > 0) {
			     return $query->result_array();
			}else{
				 return array();
			}
    }
    
    //已补料单分月统计
    function get_month_feedings($WorkShopIds)
    {
	    
	    // S.Estate=0 
     $sql="SELECT    DATE_FORMAT(S.Date,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,COUNT(*) AS Counts   
						FROM ck13_replenish S 
						INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
						WHERE S.Estate=0 AND Y.WorkShopId  IN ($WorkShopIds) GROUP BY  DATE_FORMAT(S.Date,'%Y-%m') ORDER BY Month DESC";
					
		$query=$this->db->query($sql);
		    if ($query->num_rows() > 0) {
			     return $query->result_array();
			}else{
				 return array();
			}
    }
    
    
     //已补料单分日统计
    function get_month_dates($WorkShopIds, $Month)
    {
	    //S.Estate=0 
     $sql="SELECT    S.Date,SUM(S.Qty) AS Qty,COUNT(*) AS Counts   
						FROM ck13_replenish S 
						INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
						WHERE S.Estate=0 AND Y.WorkShopId  IN ($WorkShopIds) AND  DATE_FORMAT(S.Date,'%Y-%m')='$Month'  GROUP BY  S.Date ORDER BY Date DESC";
					
		$query=$this->db->query($sql);
		    if ($query->num_rows() > 0) {
			     return $query->result_array();
			}else{
				 return array();
			}
    }

    
    
    //已补料单分日统计明细
     function get_date_sheet($WorkShopIds,$date)
     {
	     //S.Estate=0
            $sql="SELECT  S.Id,S.POrderId,S.sPOrderId,S.StockId,S.StuffId,S.Qty,S.Remark,S.Estate,S.creator,S.created,S.Auditor,S.AuditTime,S.ReturnReasons, 
		                            D.StuffCname,D.Picture,U.Decimals,K.tStockQty,
		                            N.Name AS StaffName,SUM(IFNULL(L.Qty,0)) AS llQty  
					FROM ck13_replenish S 
					INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
					INNER JOIN stuffdata D ON D.StuffId=S.StuffId
					INNER JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
					INNER JOIN stuffunit U ON U.Id=D.Unit 
					LEFT JOIN staffmain N ON N.Number=S.creator 
					LEFT JOIN ck5_llsheet L ON L.FromId=S.Id AND L.StockId=S.StockId AND L.Qty>0 
					WHERE S.Estate=0 AND Y.WorkShopId IN ($WorkShopIds) AND S.Date='$date' GROUP BY S.Id ORDER BY S.Id DESC";
        
			$query=$this->db->query($sql);
		    if ($query->num_rows() > 0) {
			     return $query->result_array();
			}else{
				 return array();
			}
   }	

     //已补料单分月统计明细
     function get_month_sheet($WorkShopIds,$Month)
     {
            $sql="SELECT  S.Id,S.POrderId,S.sPOrderId,S.StockId,S.StuffId,S.Qty,S.Remark,S.Estate,S.creator,S.created,S.Auditor,S.AuditTime,S.ReturnReasons, 
		                            D.StuffCname,D.Picture,U.Decimals,K.tStockQty,
		                            N.Name AS StaffName,SUM(IFNULL(L.Qty,0)) AS llQty  
					FROM ck13_replenish S 
					INNER JOIN yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
					INNER JOIN stuffdata D ON D.StuffId=S.StuffId
					INNER JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
					INNER JOIN stuffunit U ON U.Id=D.Unit 
					LEFT JOIN staffmain N ON N.Number=S.creator 
					LEFT JOIN ck5_llsheet L ON L.FromId=S.Id AND L.StockId=S.StockId AND L.Qty>0 
					WHERE S.Estate=0 AND Y.WorkShopId IN ($WorkShopIds) AND  DATE_FORMAT(S.Date,'%Y-%m')='$Month' GROUP BY S.Id ORDER BY S.Id DESC";
        
			$query=$this->db->query($sql);
		    if ($query->num_rows() > 0) {
			     return $query->result_array();
			}else{
				 return array();
			}
   }	
    
    //新增记录
   function save_records($sPOrderId,$StockId,$Qty,$Remark)
   {
	     $this->load->model('CgstocksheetModel');
	     $records = $this->CgstocksheetModel->get_records($StockId);
	     $POrderId = $records['POrderId'];
		 $StuffId=$records['StuffId'];
		 $records = null;
		 if ($StuffId==''){
			 $this->load->model('CgStuffcomboxModel');
			 $records = $this->CgStuffcomboxModel->get_records($StockId);
			 $POrderId = $records['POrderId'];
		     $StuffId=$records['StuffId'];
		 }
		 
		 if ($StuffId>0 && $Qty>0){
		     $data=array(
	              'POrderId'=>$POrderId, 
	             'sPOrderId'=>$sPOrderId,
	                 'StockId'=>$StockId,
	                  'StuffId'=>$StuffId,
	                       'Qty'=>$Qty,
	                 'Remark'=>$Remark,
	                 'Estate'=>'2',
	                  'Locks'=>'0',
	                   'Date'=>$this->Date,
	               'Operator'=>$this->LoginNumber, 
	               'creator'=>$this->LoginNumber,
	               'created'=>$this->DateTime
		       );
		       $this->db->insert('ck13_replenish', $data); 
		       
		       return $this->db->affected_rows();
		 }else{
			  return 0;
		 }      
   }
   
   //审核
   function set_estate($Id,$Estate,$Operator,$Reasons='')
   {
           $upSign = 0;
           switch($Estate){
	            case 1:
	               $this->db->trans_begin();
	               $data=array('Estate' =>$Estate,
                                       'Auditor'=>$Operator,
                                  'AuditTime'=>$this->DateTime
                    );
	             $this->db->update('ck13_replenish',$data, array('Id' =>$Id));//更新状态
	             $upSign = $this->db->affected_rows();
	       
	             $records = $this->get_records($Id);
	             $POrderId  = $records['POrderId'];
	             $StockId = $records['StockId'];
	             $StuffId  = $records['StuffId'];
	             $Qty  = $records['Qty'];
	             $Remark = $records['Remark'];
	             $records  = null;
	             
	             $this->load->model('CkllsheetModel'); 
	             $records = $this->CkllsheetModel->get_records(0,$StockId);
	             $Price     = $records['Price'];
	             $records  = null;
	              
	             $data=array(
			                'POrderId'=>$POrderId, 
			              'sPOrderId'=>'',
			                  'StockId'=>$StockId,
			                   'StuffId'=>$StuffId,
			                     'Price'=>$Price,
			                       'Qty'=>$Qty*-1,
			                     'Type'=>'6',
			                'FromId'=>$Id,
			     'FromFunction'=>'车间补料',
			                    'RkId'=>'0',
			                  'Estate'=>'0',
			                   'Locks'=>'0',
			                   'Date'=>$this->Date,
			             'Operator'=>$Operator, 
			               'creator' =>$Operator,
			               'created'=>$this->DateTime
				       );
				 $this->db->insert('ck5_llsheet', $data);  //新增退料记录
	             $upSign = $this->db->affected_rows();
	             
	             if ($upSign>0){
			              $this->load->model('Ck9stocksheetModel');
			              $records = $this->Ck9stocksheetModel->get_records($StuffId);
			              $oStockQty = $records['oStockQty'];
			              $records = null;
			             
			              $NewStockId = '';$mStuffId='';
			              if ($oStockQty<$Qty){
			                     $FactualQty=$Qty-$oStockQty;
			                     $newoStockQty= 0;
			                     
			                     $mStuffId=$StuffId;
			                     
			                     $this->load->model('CgStuffcomboxModel');
			                     $records = $this->CgStuffcomboxModel->get_records($StockId);
			                     if (isset($records['mStuffId']))
			                     {
				                     $mStuffId=$records['mStuffId']>0?$records['mStuffId']:$StuffId;
			                     }
		                         $records = null;
			                     
			                     $sql="CALL proc_cg1_stocksheet_add('',$mStuffId,$FactualQty,'1','0',$Operator)";
					             $query = $this->db->query($sql);//生成特采记录
		                         $row = $query->first_row('array');  
					             $upSign =  $row['OperationResult']=='Y'?1:0;
					             $NewStockId = $row['NewStockId'];
					             $query=null; $row=null;
			            }else{
				               $newoStockQty=$oStockQty-$Qty;
			            }
			            
			            if ($upSign>0 && $NewStockId!=''){
				            $Remark = "订单补料:$POrderId,原因:$Remark";
					                   
			                $data=array(
					            'ProposerId'=>$Operator, 
					                   'StuffId'=>$StuffId,
					                       'Qty'=>$Qty,
					                 'Remark'=>$Remark,
					                     'Type'=>'7',
					                       'Bill'=>'0',
					                  'Estate'=>'0',
					                   'Locks'=>'0',
					          'DealResult'=>'0',
					                   'Date'=>$this->Date,
					             'Operator'=>$Operator, 
					               'creator' =>$Operator,
					               'created'=>$this->DateTime
						       );
						      $this->db->insert('ck8_bfsheet', $data); 
						      
						      $upSign = $this->db->affected_rows();
						      if ($upSign>0)
						      {
							         //更新备注
						              $remarks=array(
							                   'AddRemark' =>$Remark
					                    );
					                   $this->db->update('cg1_stocksheet',$remarks, array('StockId' =>$NewStockId)); 
						      }		
			           }
	            }
	           
		       if ($this->db->trans_status() === FALSE || $upSign==0){
				    $this->db->trans_rollback();
				}
				else{
				    $this->db->trans_commit();
				    
				     $data=array(
							                   'oStockQty' =>$newoStockQty
					                    );
					$this->db->update('ck9_stocksheet',$data, array('StuffId' =>$StuffId)); //更新订单库存    
					
					//更新母配件库存数据
					if ($mStuffId!='' && $mStuffId!=$StuffId){
		                    $checkSql = "SELECT ROUND(MIN(K.oStockQty/S.Relation)) AS oStockQty,ROUND(MIN(K.tStockQty/S.Relation))  AS tStockQty
											FROM stuffcombox_bom S 
											LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
											WHERE S.mStuffId='$mStuffId' ";
							 $query=$this->db->query($checkSql);	
							  if ($query->num_rows() > 0){
							        $rows= $query->result_array();
							        $m_oStockQty=$rows['oStockQty'];
							        $m_tStockQty =$rows['tStockQty'];
							        $mdata=array(
			                               'oStockQty' =>$m_oStockQty,
			                                'tStockQty' =>$m_tStockQty
		                          );
		                           	$this->db->update('ck9_stocksheet',$mdata, array('StuffId' =>$mStuffId)); //更新订单库存
							  }			
		             }
				} 
			
	           break;
	      case 3:
	               $data=array('Estate' =>$Estate,
	                       'ReturnReasons'=>$Reasons,
                                       'Auditor'=>$Operator,
                                  'AuditTime'=>$this->DateTime
                    );
	             $this->db->update('ck13_replenish',$data, array('Id' =>$Id));
	             $upSign = $this->db->affected_rows();
	           break;
	      default:
	           $data=array('Estate' =>$Estate,
                                  'modifier'=>$Operator,
                                 'modified'=>$this->DateTime
                    );
	             $this->db->update('ck13_replenish',$data, array('Id' =>$Id));
	             $upSign = $this->db->affected_rows();
	           break;
	       }
	       return $upSign;
   }
   
   function delete_records($Id) 
   {
            $this->db->trans_begin();
            
            $this->db->where('Id', $Id);
			$this->db->delete('ck13_replenish'); 
			
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			     $OperationResult = 'N';
			}
			else{
			    $this->db->trans_commit();
			    $OperationResult = 'Y';
			}
			
			 $Log = $OperationResult=='Y'?"Id: $Id 的记录删除成功": "Id: $Id 的记录删除失败";
			
			$this->load->model('oprationlogModel');
			$this->oprationlogModel->save_item(array('LogItem'=>'车间补料','LogFunction'=> '删除记录','Log'=>$Log,'OperationResult'=>$OperationResult));
			
			return $OperationResult=='Y'?1:0;
 
	}

    
}