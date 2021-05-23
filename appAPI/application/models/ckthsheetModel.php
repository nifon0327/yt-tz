<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class CkthsheetModel  
* 仓库退货记录  sql: ac.ck12_thsheet 
* 
*/ 
class  CkthsheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    function get_th_monthlist($Floor,$mCount='')
	{
	   $Limits=$mCount>0?" LIMIT $mCount":'';
	   $sql = "SELECT   DATE_FORMAT(M.Date,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,COUNT(*) AS Counts,
	                    SUM(IF(S.Estate=1,S.Qty,0)) AS unQty,SUM(IF(S.Estate=1,1,0)) AS unCounts 
	                    FROM ck12_thsheet S 
	                    LEFT JOIN ck12_thmain M ON M.Id=S.Mid 
	                    LEFT JOIN qc_badrecord B ON B.Id=S.Bid  
						LEFT JOIN gys_shmain GM ON GM.Id=B.shMid 
						WHERE GM.Floor='$Floor' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')  ORDER BY Month DESC $Limits";
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}
	
	//已退货记录(按退货单分类)
	function get_th_billnumberlist($Floor,$Month)
	{
	   $sql = "SELECT   M.Id,M.BillNumber,M.Estate,M.Type,IFNULL(A.WorkShopId,M.CompanyId) AS CompanyId,
	                    IFNULL(W.Name,B.Forshort) AS Forshort,
	                    SUM(S.Qty) AS Qty,COUNT(*) AS Counts,IFNULL(M.created,M.Date) AS created,SM.Name AS Operator  
	                    FROM ck12_thsheet S 
	                    LEFT JOIN ck12_thmain M ON M.Id=S.Mid 
	                    LEFT JOIN qc_badrecord R ON R.Id=S.Bid   
						LEFT JOIN gys_shsheet G  ON G.Id=R.Sid 
                        LEFT JOIN gys_shmain GM ON GM.Id=R.shMid 
						LEFT JOIN yw1_scsheet A ON A.sPOrderId=G.sPOrderId 
						LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
	                    LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
	                    LEFT JOIN staffmain SM ON SM.Number=M.Operator 
						WHERE GM.Floor='$Floor'  AND  DATE_FORMAT(M.Date,'%Y-%m')='$Month' 
			   GROUP BY M.BillNumber  ORDER BY S.Date DESC";
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}
	
	//已退货记录明细
	function get_billnumber_list($Mid)
	{
	   $sql = "SELECT   M.BillNumber,S.Id,S.Bid,S.StuffId,S.Qty,S.Estate,IFNULL(M.created,M.Date) AS created,D.StuffCname,D.Picture,F.Name AS Operator,B.shQty 
	                    FROM ck12_thmain M
	                    LEFT JOIN ck12_thsheet S ON M.Id=S.Mid 
	                    LEFT JOIN qc_badrecord B ON B.Id=S.Bid 
	                    LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
						LEFT JOIN staffmain F ON F.Number=M.Operator 
						WHERE  M.Id='$Mid' 
					    ORDER BY S.Id";
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}


	function get_billnumber($Mid)
	{
		$sql = "SELECT BillNumber FROM ck12_thmain WHERE Id=?";
        $query = $this->db->query($sql,array($Mid));
        
        $row=$query->first_row('array');
        return $row['BillNumber'];
	}
	
	function get_new_billnumber()
	{
          $thisYear=date("Y");
          $sql="SELECT MAX(A.BillNumber) AS BillNumber FROM(
				SELECT MAX(BillNumber) AS BillNumber FROM ck2_thmain WHERE BillNumber LIKE '$thisYear%'
				UNION ALL
				SELECT MAX(BillNumber) AS BillNumber FROM ck12_thmain WHERE BillNumber LIKE '$thisYear%' 
				)A";
         $query = $this->db->query($sql);
         if ($query->num_rows() > 0){
            $row=$query->first_row('array');
            $BillNumber=$row['BillNumber'];
            $BillNumber=$BillNumber+1;
         }
         else{
            $BillNumber=$thisYear."00001";
         }
         return $BillNumber;
    }
    
    function get_threport_url($BillNumber)
	{
	   return "http://www.ashcloud.com/qr.php?F=TH&I=$BillNumber";
	}
	
	           
	function save_records($Ids)
	{
        $IdsArray=explode(',', $Ids);
        
        $this->load->model('QcBadrecordModel');
        $this->load->model('GysshsheetModel');
        
        $records= $this->QcBadrecordModel->get_records($IdsArray[0]);
        $shMid  = $records['shMid'];
        
	    $CompanyId  = $this->GysshsheetModel->get_sh_companyid($shMid);
	    
	    $BillNumber = $this->get_new_billnumber();
	    
	    $status = 0;
	    $this->db->trans_begin();
	    
        $data=array(
	         'BillNumber'=>$BillNumber, 
	          'CompanyId'=>$CompanyId,
	           'Attached'=>'0',  
	              'Locks'=>'0', 
	             'Estate'=>'1',
	               'Date'=>$this->DateTime,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
	    $this->db->insert('ck12_thmain', $data);
	    $Mid = $this->db->insert_id();
	    
	    
	
	    for($i=0,$counts=count($IdsArray);$i<$counts;$i++){
	        $records = null;
		    $records = $this->QcBadrecordModel->get_records($IdsArray[$i]);
			$StuffId = $records['StuffId'];
			$Qty     = $records['Qty'];
			
	        $Remark=$this->QcBadrecordModel->get_causes($IdsArray[$i]);
	        
		   $data=array(
	                'Mid'=>$Mid, 
	                'Bid'=>$IdsArray[$i],
	            'StuffId'=>$StuffId,  
	                'Qty'=>$Qty, 
	             'Remark'=>$Remark,
	             'Estate'=>'1',
	               'Date'=>$this->DateTime,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
	       
	        $this->db->insert('ck12_thsheet', $data);
	    }
	    
	    //更新品检报告状态
	    $this->QcBadrecordModel->set_estate($Ids,0);
	    
	    if ($this->db->trans_status() === FALSE || $i==0){
			    $this->db->trans_rollback();
		  } 
		  else{
			    $this->db->trans_commit();
			   // $this->db->trans_rollback();
			    $status=1;
		  }
	    
	    return $status;
	}
	
	//更新退货单状态
	function set_estate($Ids,$Estate,$Type)
	{
	   $data=array(
	               'Estate'  =>$Estate,
	               'Type'    =>$Type,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              ); 
	           
	   $this->db->update('ck12_thmain',$data, "Id IN ($Ids)");
	   
	   return $this->db->affected_rows();
   }
   
   //退货单签名退回
	function save_signature($params)
	{
	   $Mid       = element("Id",$params,'');
	   $signature = element("signature",$params,'');
	   
	   $status=0;
	   $this->db->trans_begin();
	    
	   $BillNumber=$this->get_billnumber($Mid);

	   $data=array(
	                'Mid'=>$Mid,
	         'BillNumber'=>$BillNumber, 
	          'Signature'=>$signature,
	               'Date'=>$this->Date,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
		       
	    $this->db->insert('ck12_thsignature', $data);
	    
	    $this->set_estate($Mid,0,1);
	    
	    if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
		  } 
		  else{
			    $this->db->trans_commit();
			    $status=1;
		  }
	    
	    return $status;
   }

}