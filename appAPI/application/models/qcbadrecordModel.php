<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  QcBadrecordModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_all_badcauses($StuffId, $Limits='', $MidNeed='') {
	    if ($Limits != '') {
		    $Limits = " limit $Limits";
	    }
	    $sql = "
	    SELECT   IF (S.CauseId=-1,S.Reason,T.Cause) AS Cause ,sum(S.Qty) as Qty
	                    FROM qc_badrecordsheet S 
	                    LEFT JOIN qc_causetype T ON T.Id=S.CauseId
LEFT JOIN qc_badrecord B ON B.Id=S.Mid  
						WHERE  B.StuffId=$StuffId group by IF (S.CauseId=-1,S.Reason,T.Cause) order by Qty desc $Limits";
						
		if ($MidNeed!='') {
			$sql = "
	 SELECT   B.Id ,B.Qty,B.created,M.Name 
	                    FROM qc_badrecord  B 
	                  left join staffmain M on B.Operator=M.Number
							WHERE  B.StuffId=$StuffId and B.Qty>0  order by B.created desc $Limits";
		}
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
    }
    
    function get_allandbad($StuffId) {
	    
	    $sql = "select sum(B.shQty) as allQty, sum(B.Qty) as badQty from qc_badrecord B where B.StuffId=$StuffId;";
	    $query=$this->db->query($sql);
	    return $query->first_row('array');
    }
    
    //返回指定Id的记录
	function get_records($Id){
	
	   $sql = "SELECT Id,Sid,shMid,StockId,StuffId,shQty,checkQty,Qty,AQL,Remark,Estate,Locks,Date,YEARWEEK(Date,1) AS Weeks,Operator 
	           FROM qc_badrecord  WHERE Id=?";
	   $query=$this->db->query($sql,array($Id));
	   
	   return $query->first_row('array');
	}
	
	//返加未退货数量
	function get_unth_counts($Floor,$Estate)
	{
	    $sql ="SELECT  SUM(S.Qty) AS Qty,COUNT(*) AS Counts  
						FROM qc_badrecord S 
						LEFT JOIN gys_shmain M ON S.shMid=M.Id
						LEFT JOIN ck12_thsheet K ON K.Bid=S.Id 
						WHERE S.Estate=? AND S.Qty>0 AND M.Floor=? AND K.Bid IS NULL  ";
		$query = $this->db->query($sql,array($Estate,$Floor));
		return $query->first_row('array');
	}
	
	//未退货记录(按供应商分类)
	function get_unth_companylist($Floor,$Estate)
	{
	   $sql = "SELECT   IFNULL(A.WorkShopId,M.CompanyId) AS CompanyId,IFNULL(W.Name,B.Forshort) AS Forshort,
	                    SUM(S.Qty) AS Qty,COUNT(*) AS Counts 
	                    FROM qc_badrecord S 
						LEFT JOIN gys_shmain M ON S.shMid=M.Id
						LEFT JOIN gys_shsheet G ON G.Id=S.Sid 
						LEFT JOIN yw1_scsheet A ON A.sPOrderId=G.sPOrderId 
						LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
	                    LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
	                    LEFT JOIN ck12_thsheet K ON K.Bid=S.Id 
						WHERE S.Estate='$Estate' AND S.Qty>0 AND M.Floor='$Floor' AND K.Bid IS NULL 
			   GROUP BY M.CompanyId  ORDER BY M.CompanyId,S.Date";
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}
	
	//未退货记录明细
	function get_unth_list($Floor,$CompanyId)
	{
	   $sql = "SELECT   S.Id,S.StuffId,S.shQty,S.Qty,IFNULL(S.created,S.Date) AS created,D.StuffCname,D.Picture,F.Name AS Operator
	                    FROM qc_badrecord S 
	                    LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
						LEFT JOIN gys_shmain M ON S.shMid=M.Id
						LEFT JOIN gys_shsheet G ON G.Id=S.Sid 
						LEFT JOIN staffmain F ON F.Number=S.Operator 
						LEFT JOIN ck12_thsheet K ON K.Bid=S.Id 
						LEFT JOIN yw1_scsheet A ON A.sPOrderId=G.sPOrderId 
						WHERE S.Estate='1' AND S.Qty>0 AND M.Floor='$Floor' AND (M.CompanyId='$CompanyId' OR A.WorkShopId='$CompanyId') AND K.Bid IS NULL 
					    ORDER BY S.Id";
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}
	
	
	
	//品检月统计记录（按送货楼层显示）
	function get_badrecord_month($Floor,$mCount='')
	{
	   $Limits=$mCount>0?" LIMIT $mCount":'';
	   $sql = "SELECT   DATE_FORMAT(S.Date,'%Y-%m') AS Month,SUM(S.shQty) AS shQty,SUM(S.Qty) AS Qty,COUNT(*) AS Counts,
	                    SUM(IF(S.Estate=1,S.Qty,0)) AS unQty,SUM(IF(S.Estate=1,1,0)) AS unCounts,
	                    SUM(IF(S.Qty>0,1,0)) AS badCounts 
	                    FROM qc_badrecord S 
						LEFT JOIN gys_shmain GM ON GM.Id=S.shMid 
						WHERE GM.Floor='$Floor'  GROUP BY DATE_FORMAT(S.Date,'%Y-%m')  ORDER BY Month DESC $Limits";// S.Qty>0 AND
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}
	
	function get_badrecord_company($Floor,$Month)
	{
		$sql = "SELECT   M.CompanyId,IFNULL(W.Name,P.Forshort) AS Forshort,SUM(B.shQty) AS shQty,SUM(B.Qty)AS Qty,
		                COUNT(*) AS Counts,IF(B.Qty>0,1,0) AS badCounts,B.Date  
	                    FROM      qc_badrecord B 
	                    LEFT JOIN gys_shmain   M ON M.Id=B.shMid 
	                    LEFT JOIN gys_shsheet  S ON S.Id=B.Sid  
                        LEFT JOIN yw1_scsheet  A ON A.sPOrderId=S.sPOrderId
	                    LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			            LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  AND P.ObjectSign IN (1,3) 
						WHERE  M.Floor='$Floor' AND  DATE_FORMAT(B.Date,'%Y-%m')='$Month' 
			    GROUP BY M.CompanyId ORDER BY B.Date DESC";//B.Qty>0 AND 
		 $query=$this->db->query($sql);
	     return $query->result_array();				
	}
			      
	//品检月统计记录（按送货楼层显示）
	function get_badrecord_monthlist($Floor,$Month, $date = '', $searched='')
	{
	   /*
	   $sql = "SELECT   B.Id,B.StockId,B.StuffId,B.shQty,B.Qty,IFNULL(B.created,B.Date) AS created,D.StuffCname,D.Picture,D.CheckSign,
	                    (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,SUM(T.Qty) AS djQty,M.CompanyId,IFNULL(W.Name,P.Forshort) AS Forshort
	                    FROM qc_badrecord B 
	                    LEFT JOIN gys_shmain M ON M.Id=B.shMid 
	                    LEFT JOIN gys_shsheet S ON S.Id=B.Sid  
	                    LEFT JOIN stuffdata D ON D.StuffId=B.StuffId 
	                    LEFT JOIN cg1_stocksheet  G ON G.StockId=B.StockId  
	                    LEFT JOIN qc_cjtj T ON T.Sid=B.Sid 
                        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	                    LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			            LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  AND P.ObjectSign IN (1,3) 
						WHERE B.Qty>0 AND M.Floor='$Floor' AND  DATE_FORMAT(B.Date,'%Y-%m')='$Month'  AND D.ComboxSign!=1  GROUP BY B.Id 
			UNION ALL 
                SELECT   B.Id,B.StockId,B.StuffId,B.shQty,B.Qty,IFNULL(B.created,B.Date) AS created,D.StuffCname,D.Picture,D.CheckSign,
	                    G.OrderQty,GS.DeliveryWeek,SUM(T.Qty) AS djQty,M.CompanyId,IFNULL(W.Name,P.Forshort) AS Forshort
	                    FROM qc_badrecord B 
	                    LEFT JOIN gys_shmain M ON M.Id=B.shMid 
                        LEFT JOIN gys_shsheet S ON S.Id=B.Sid   
	                    LEFT JOIN stuffdata D ON D.StuffId=B.StuffId 
	                    LEFT JOIN cg1_stuffcombox G ON G.StockId=B.StockId 
                        LEFT JOIN cg1_stocksheet  GS ON GS.StockId=G.mStockId
                        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	                    LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			            LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  AND P.ObjectSign IN (1,3) 
	                    LEFT JOIN qc_cjtj T ON T.Sid=B.Sid 
						WHERE B.Qty>0 AND M.Floor='$Floor' AND  DATE_FORMAT(B.Date,'%Y-%m')='$Month' AND D.ComboxSign=1 GROUP BY B.Id 
					    ORDER BY Id DESC";
        */
        $Limits = '';
        $condition = " M.Floor='$Floor' AND  DATE_FORMAT(B.Date,'%Y-%m')='$Month'  ";
        if ($date != '') {
	        $condition = " M.Floor='$Floor' AND  DATE_FORMAT(B.Date,'%Y-%m-%d')='$date'  ";
        }
        
        
        if ($searched != '') {
	         $condition = " M.Floor='$Floor' AND ( D.StuffId='$searched' or D.StuffCname like '%$searched%') ";
	         $Limits = 'LIMIT 300';
        }
        $sql = "SELECT   B.Id,B.StockId,B.StuffId,B.shQty,B.Qty,B.checkQty,IFNULL(B.created,B.Date) AS created,D.StuffCname,D.Picture,
	                    M.CompanyId,IFNULL(W.Name,P.Forshort) AS Forshort,F.Name AS Operator,U.Decimals 
	                    FROM qc_badrecord B 
	                    LEFT JOIN gys_shmain M ON M.Id=B.shMid 
	                    LEFT JOIN gys_shsheet S ON S.Id=B.Sid  
	                    LEFT JOIN stuffdata D ON D.StuffId=B.StuffId 
	                    LEFT JOIN  stuffunit U ON U.Id=D.Unit
                        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	                    LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			            LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  AND P.ObjectSign IN (1,3) 
                        LEFT JOIN staffmain F ON F.Number=B.Operator  
						WHERE   $condition  AND D.ComboxSign!=1  GROUP BY B.Id 
			UNION ALL 
                SELECT   B.Id,B.StockId,B.StuffId,B.shQty,B.Qty,B.checkQty,IFNULL(B.created,B.Date) AS created,D.StuffCname,D.Picture,
	                    M.CompanyId,IFNULL(W.Name,P.Forshort) AS Forshort,F.Name AS Operator ,U.Decimals 
	                    FROM qc_badrecord B 
	                    LEFT JOIN gys_shmain M ON M.Id=B.shMid 
                        LEFT JOIN gys_shsheet S ON S.Id=B.Sid   
	                    LEFT JOIN stuffdata D ON D.StuffId=B.StuffId 
	                    LEFT JOIN  stuffunit U ON U.Id=D.Unit
                        LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	                    LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			            LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  AND P.ObjectSign IN (1,3) 
                        LEFT JOIN staffmain F ON F.Number=B.Operator 
						WHERE  $condition AND D.ComboxSign=1 GROUP BY B.Id 
					    ORDER BY Id DESC  $Limits";//B.Qty>0 AND
        $query=$this->db->query($sql);
        
      
	    return $query->result_array();
	}


	function get_badrecord_month_dates($Floor,$Month) {

	   $sql = "SELECT   DATE_FORMAT(S.Date,'%Y-%m-%d') AS Date,SUM(S.shQty) AS shQty,SUM(S.Qty) AS Qty,COUNT(*) AS Counts,
	                    SUM(IF(S.Estate=1,S.Qty,0)) AS unQty,SUM(IF(S.Estate=1,1,0)) AS unCounts,
	                    SUM(IF(S.Qty>0,1,0)) AS badCounts 
	                    FROM qc_badrecord S 
						LEFT JOIN gys_shmain GM ON GM.Id=S.shMid 
						WHERE GM.Floor='$Floor' AND  DATE_FORMAT(S.Date,'%Y-%m')='$Month' GROUP BY DATE_FORMAT(S.Date,'%Y-%m-%d')  ORDER BY Date DESC ";
	        
        $query=$this->db->query($sql);
	    return $query->result_array();
	}

	//获取不良原因
	function get_causes($Mid)
	{
	    $causes='';
	    
	    $sql = "SELECT  S.Id,IF (S.CauseId=-1,S.Reason,T.Cause) AS Cause 
	                    FROM qc_badrecordsheet S 
	                    LEFT JOIN qc_causetype T ON T.Id=S.CauseId  
						WHERE S.Mid='$Mid' ORDER BY S.Id";
        $query=$this->db->query($sql);
        
        foreach ($query->result_array() as $rows){
           $causes.=$causes==''?$rows['Cause']:'/' . $rows['Cause'];
       	}
		return $causes;
	}
	
	//获取不良原因
	function get_causesarray($Mid)
	{
	    $causes=array();
	    
	    $sql = "SELECT  S.Id,S.Qty,IF (S.CauseId=-1,S.Reason,T.Cause) AS Cause 
	                    FROM qc_badrecordsheet S 
	                    LEFT JOIN qc_causetype T ON T.Id=S.CauseId  
						WHERE S.Mid='$Mid' ORDER BY S.Id";
        $query=$this->db->query($sql);
        
        foreach ($query->result_array() as $rows){
           $causes[]=array($rows['Cause'],$rows['Qty']);
       	}
		return $causes;
	}
	
	//获取不良图片数组
	function get_badpictures($Mid)
	{
	    $imgs=array();
	    $url = $this->config->item('download_path') . "/qcbadpicture/";
	    
	    $sql = "SELECT  S.Id,IF (S.CauseId=-1,S.Reason,T.Cause) AS Cause,S.Picture 
	                    FROM qc_badrecordsheet S 
	                    LEFT JOIN qc_causetype T ON T.Id=S.CauseId  
						WHERE S.Mid='$Mid' ORDER BY S.Id";
        $query=$this->db->query($sql);
        
        foreach ($query->result_array() as $rows){
           $Id     =$rows['Id'];
           $Picture=$rows['Picture'];
           $Cause  =$rows['Cause'];
           
           if ($Picture==1){
	         $imgs[]=array('url'  =>$url . 'Q' . $Id . '.jpg','title'=>' '.$Cause);  
           }
           else{
	          if ($Picture>1){
	             $imgs[]=array('url'  =>$url . 'Q' . $Id . '.jpg','title'=>$Cause . '-1');
	             for ($i=1;$i<$Picture;$i++){
		             $imgs[]=array('url'  =>$url . 'Q' . $Id . '-' . $i . '.jpg','title'=>$Cause . '-' . ($i+1));
	             }
	          }
          }
	    }
	    
		return $imgs;
	}
	
	function get_qualityreport_url($Id,$StockId='')
	{
	/*
	  $len = strlen($Id . '');
	  $reviseChar = '';
	  for ($i=$len;$i<12;$i++){
		  $reviseChar.='*';
	  }*/
	  
	   return "http://www.ashcloud.com/qr.php?F=QC&I=$Id";
	}

	
	//保存品检记录
	function save_records($params)
	{
		 $Sid       = element("Id",$params,'');
		 $counts    = element("counts",$params,'');
		
		 $status=0;
	     
	     $this->load->model('GysshsheetModel');
	     $records=$this->GysshsheetModel->get_records($Sid);
	     $StockId=$records['StockId'];
	     $StuffId=$records['StuffId'];
	     $Qty  =$records['Qty'];
	     $Mid    =$records['Mid'];
	     $records=null;
	     
	     $this->load->model('QcCjtjModel');
	     $checkQty=$this->QcCjtjModel->get_qcqty($Sid);
	     
	     $sheetId=0;$badQtys=0;
	    
	     for ($i=0;$i<$counts;$i++){
	        $badqtyStr= 'badqty_' . $i;
		    $badQty   = element($badqtyStr,$params,'0');
		    $badQtys += $badQty;
	     }
			

			
		 $noaccept = element('noaccept', $params,'');
		 $remark = '';
		 if ($noaccept == '1') {
			 $remark = '抽检不合格';
			 
		 }
		 
		 $this->db->trans_begin();     
	     if ($StuffId>0){
	         $data=array(
	                'Sid'=>$Sid, 
	              'shMid'=>$Mid,
	            'StockId'=>$StockId,
	            'StuffId'=>$StuffId,  
	              'shQty'=>$Qty, 
	           'checkQty'=>$checkQty,
	                'Qty'=>$badQtys,
	                'AQL'=>'0',
	             'Remark'=>$remark,
	               'Date'=>$this->DateTime,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
	       
	       $this->db->insert('qc_badrecord', $data);
	       $inMid = $this->db->insert_id();
	       
	       if ($inMid>0 && $counts>0){
	           for ($i=0;$i<$counts;$i++){
	               $reasonIdStr='reasonId_' . $i;
	               $reasonStr  ='reason_' . $i;
	               $badqtyStr  ='badqty_' . $i;
	               $hasimgStr  ='hasimg_' . $i;
	               
	               $CauseId  = element($reasonIdStr,$params,'-1');
	               $Reason   = element($reasonStr,$params,'未定义');
	               $badqty   = element($badqtyStr,$params,'0');
	               $hasimg   = element($hasimgStr,$params,'0');
	               
			       $data=array(
		                'Mid'=>$inMid, 
		            'CauseId'=>$CauseId,
		                'Qty'=>$badqty,
		             'Reason'=>$Reason,
		            'Picture'=>$hasimg,
		               'Date'=>$this->DateTime,
		           'Operator'=>$this->LoginNumber,
		            'creator'=>$this->LoginNumber,
		            'created'=>$this->DateTime 
			       );
		       
		          $this->db->insert('qc_badrecordsheet', $data);
		          $sheetId = $this->db->insert_id();
		          
		           if ($sheetId>0 && $hasimg>0){
		               //上传文件
		               $upfileStr = 'upfile_' . $i;
		               $this->save_upfile($sheetId,$upfileStr);
		           } 
		             
	          }
	       }
	         
		  //更新送货单状态
	      $this->GysshsheetModel->set_estate($Sid,0);
	          
          //更新备品送货单状态
          $bpIds=$this->GysshsheetModel->get_scorder_bpids($Mid,$StuffId);
          if ($bpIds!=''){
	         $this->GysshsheetModel->set_estate($bpIds,0);  
          }
          
          if ($this->db->trans_status() === FALSE || $inMid==0){
			    $this->db->trans_rollback();
		  } 
		  else{
			    $this->db->trans_commit();
			   // $this->db->trans_rollback();
			    $status=$inMid;
		  }
	  }
	  return $status;
  }
	
		
		
		
  	function save_multi_returnstock($params) {
	  	 $ListIds   = element("ListIds",$params,'');
         $Reason    = element("Reason",$params,''); 
	     $imgCount  = element("imgCount",$params,''); 
	     
	     $status=0;
	     
	     $this->load->model('GysshsheetModel');
	     
	     $ListIdsArr = explode(',', $ListIds);
	     foreach ($ListIdsArr as $Sid) {
		     $records=$this->GysshsheetModel->get_records($Sid);
		     $CompanyId=$records['CompanyId'];
		     $StockId  =$records['StockId'];
		     $StuffId  =$records['StuffId'];
		     $Qty      =$records['Qty'];
		     $Mid      =$records['Mid'];
		     $records=null;
			 
			 $sheetId=0;
			 $this->db->trans_begin();     
		     if ($StuffId>0){
		         $data=array(
		                'Sid'=>$Sid, 
		              'shMid'=>$Mid,
		            'StockId'=>$StockId,
		            'StuffId'=>$StuffId,  
		              'shQty'=>$Qty, 
		           'checkQty'=>'0',
		                'Qty'=>$Qty,
		                'AQL'=>'0',
		             'Remark'=>'退料操作',
		               'Date'=>$this->DateTime,
		           'Operator'=>$this->LoginNumber,
		            'creator'=>$this->LoginNumber,
		            'created'=>$this->DateTime 
			       );
		       
		       $this->db->insert('qc_badrecord', $data);
		       $inMid = $this->db->insert_id();
		       
		       if ($inMid>0){
			       $data=array(
		                'Mid'=>$inMid, 
		            'CauseId'=>'-1',
		                'Qty'=>$Qty,
		             'Reason'=>$Reason,
		            'Picture'=>$imgCount,
		               'Date'=>$this->DateTime,
		           'Operator'=>$this->LoginNumber,
		            'creator'=>$this->LoginNumber,
		            'created'=>$this->DateTime 
			       );
		       
		          $this->db->insert('qc_badrecordsheet', $data);
		          $sheetId = $this->db->insert_id();
		          
		          
		          //更新送货单状态
		          $this->GysshsheetModel->set_estate($Sid,0);
		          
		          //更新备品送货单状态
		          $bpIds=$this->GysshsheetModel->get_scorder_bpids($Mid,$StuffId);
		          if ($bpIds!=''){
			         $this->GysshsheetModel->set_estate($bpIds,0);  
		          }
		          
		          if ($sheetId>0 && $imgCount>0){
			          //上传文件
			          $files=$this->save_upfiles($sheetId,$imgCount);
		          }
		       }
		       
		      if ($this->db->trans_status() === FALSE || $inMid==0){
				    $this->db->trans_rollback();
			  } 
			  else{
				    $this->db->trans_commit();
				    $status=1;
			  }
		   }
	     }
	     
	   
	   return $status;
  	}
	//保存退料记录
	function save_returnstock($params)
	{
	     $Sid       = element("Id",$params,'');
         $Reason    = element("Reason",$params,''); 
	     $imgCount  = element("imgCount",$params,''); 
	     
	     $status=0;
	     
	     $this->load->model('GysshsheetModel');
	     $records=$this->GysshsheetModel->get_records($Sid);
	     $CompanyId=$records['CompanyId'];
	     $StockId  =$records['StockId'];
	     $StuffId  =$records['StuffId'];
	     $Qty      =$records['Qty'];
	     $Mid      =$records['Mid'];
	     $records=null;
		 
		 $sheetId=0;
		 $this->db->trans_begin();     
	     if ($StuffId>0){
	         $data=array(
	                'Sid'=>$Sid, 
	              'shMid'=>$Mid,
	            'StockId'=>$StockId,
	            'StuffId'=>$StuffId,  
	              'shQty'=>$Qty, 
	           'checkQty'=>'0',
	                'Qty'=>$Qty,
	                'AQL'=>'0',
	             'Remark'=>'退料操作',
	               'Date'=>$this->DateTime,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
	       
	       $this->db->insert('qc_badrecord', $data);
	       $inMid = $this->db->insert_id();
	       
	       if ($inMid>0){
		       $data=array(
	                'Mid'=>$inMid, 
	            'CauseId'=>'-1',
	                'Qty'=>$Qty,
	             'Reason'=>$Reason,
	            'Picture'=>$imgCount,
	               'Date'=>$this->DateTime,
	           'Operator'=>$this->LoginNumber,
	            'creator'=>$this->LoginNumber,
	            'created'=>$this->DateTime 
		       );
	       
	          $this->db->insert('qc_badrecordsheet', $data);
	          $sheetId = $this->db->insert_id();
	          
	          
	          //更新送货单状态
	          $this->GysshsheetModel->set_estate($Sid,0);
	          
	          //更新备品送货单状态
	          $bpIds=$this->GysshsheetModel->get_scorder_bpids($Mid,$StuffId);
	          if ($bpIds!=''){
		         $this->GysshsheetModel->set_estate($bpIds,0);  
	          }
	          
	          if ($sheetId>0 && $imgCount>0){
		          //上传文件
		          $files=$this->save_upfiles($sheetId,$imgCount);
	          }
	       }
	       
	      if ($this->db->trans_status() === FALSE || $inMid==0){
			    $this->db->trans_rollback();
		  } 
		  else{
			    $this->db->trans_commit();
			    $status=1;
		  }
	   }
	   
	   return $status;
	}
	
	//更新品检报告状态
	function set_estate($Ids,$Estate){
	   
	   $data=array('Estate'  =>$Estate,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
	              
	   $this->db->update('qc_badrecord',$data, "Id IN ($Ids)");
	   
	   return $this->db->affected_rows();
   }
	
	//保存上传文件
	function save_upfile($Sid,$upfile)
	{
		// 上传文件配置放入config数组
	    $config['upload_path']   = '../download/qcbadpicture';
	    $config['allowed_types'] = 'jpg';
	    $config['max_size']      = '1024000';
	    $config['max_width']     = '1024000';
	    $config['max_height']    = '1024000';
        $config['file_name']     = 'Q' . $Sid;
        
        $this->load->library('upload', $config);
        $this->upload->initialize($config);//多次加载需重新初始化数据
        
		if ( ! $this->upload->do_upload($upfile)){
	        return 0;
	    } 
	    else{
	        $files =$this->upload->data();
	        if ($files['full_path']!=''){
	           $images=array();
		       $images[]=$files['full_path'];
	           $this->load->library('graphics');
               $this->graphics->create_thumb($images); 
	        }
	        
	        return 1;
	    }
    }
	
	//保存多个上传文件
	function save_upfiles($Sid,$imgCount)
	{
		// 上传文件配置放入config数组
	    $config['upload_path']   = '../download/qcbadpicture';
	    $config['allowed_types'] = 'jpg';
	    $config['max_size']      = '1024000';
	    $config['max_width']     = '1024000';
	    $config['max_height']    = '1024000';
        $config['file_name']     = 'Q' . $Sid;
     
	    $this->load->library('multiupload');
	    $result=$this->multiupload->multi_upload('upfiles',$config);
			       
	    //取得上传文件名
		$files=0; $images=array();
		if ($result){
           foreach($result['files'] as $files){
	              $files++;
	              $images[]=$files['full_path'];
           }
			           
           $this->load->library('graphics');
           $this->graphics->create_thumb($images);
			           
         }
         return $files;
    }
}