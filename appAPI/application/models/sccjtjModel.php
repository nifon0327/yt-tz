<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScCjtjModel extends MC_Model {
   
    public $StartDate=null;
    public $ValuationDates=null;
    public $ValuationMonths=null;
    
    function __construct()
    {
        parent::__construct();
        $this->StartDate      = strtotime($this->config->item('system_opened'));
        $this->ValuationDates = 10;
        $this->ValuationMonths= 3;
    }
    
    
/*
    function get_order_scqty($POrderId) {
		
		$sql = "   
		SELECT C.boxId,SUM(C.Qty) AS Qty 
		                FROM sc1_cjtj C
		                INNER JOIN yw1_scsheet S ON S.sPOrderId=C.sPOrderId AND S.ActionId=101 
		                WHERE C.POrderId='$POrderId' "; 
          $query=$this->db->query($sql,$POrderId);
	      $row = $query->first_row();
	      return $row->Qty==""?0:$row->Qty;
	}
*/
 function get_order_scqty($POrderId, $needsScLine=0) {
		
		$sql = "   
		SELECT C.boxId,SUM(C.Qty) AS Qty 
		                FROM sc1_cjtj C
		                INNER JOIN yw1_scsheet S ON S.sPOrderId=C.sPOrderId AND S.ActionId=101 
		                WHERE C.POrderId='$POrderId' "; 
          $query=$this->db->query($sql,$POrderId);
	      $row = $query->first_row();
	      
	      if ($needsScLine > 0) {
		      $ScLine = '';
		      if ($row->boxId!='') {
			      $ScLine=substr($row->boxId, 0,1);
		      }
		      if ($ScLine == '') {
			      $ScLineResult=$this->db->query("SELECT G.GroupName FROM sc1_mission S
							   LEFT JOIN staffgroup G ON G.Id=S.Operator 
							   WHERE S.POrderId='$POrderId' AND G.Id>0");
				if($ScLineResult->num_rows() >0){
					$ScLineRow = $ScLineResult->row();
				      $GroupName=$ScLineRow->GroupName;
				      $ScLine=substr($GroupName,-1);
				}
				
		      }
		      
		      return array('qty'=>$row->Qty, 'line'=>$ScLine);
		      

	      }
	      
	      return $row->Qty==""?0:$row->Qty;
	}
    
    function get_begin_time($sPOrderId=''){
	      $sql = "SELECT  created  FROM  sc1_cjtj  WHERE sPOrderId=? ORDER BY created Limit 1";
          $query=$this->db->query($sql,$sPOrderId);
	       if ($query->num_rows() > 0) {
		      $row = $query->first_row();
		       return $row->created==''?'':$row->created;
	      }
	     return '';
	      
    }
    
    
      //已生产工单月分类统计（按生产单位）
	function get_month_sced($workshopid){
	    $sql = "SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,SUM(IFNULL(G.Price*S.Qty,0)) AS Amount 
	                  FROM  sc1_cjtj S 
                      LEFT JOIN yw1_scsheet A ON S.sPOrderId=A.sPOrderId  
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
	                  WHERE A.WorkShopId='$workshopid'  
	                  GROUP BY DATE_FORMAT(S.Date,'%Y-%m') ORDER BY Month DESC"; 
	   $query=$this->db->query($sql);
	   return $query->result_array();                
	}
    
    //已生产工单日分类统计（按生产单位）
	function get_day_sced_inmonth($workshopid,$mon){
	    $sql = "SELECT S.Date,SUM(S.Qty) AS Qty,SUM(IFNULL(G.Price*S.Qty,0)) AS Amount 
	                  FROM  sc1_cjtj S 
                      LEFT JOIN yw1_scsheet A ON S.sPOrderId=A.sPOrderId  
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
	                  WHERE A.WorkShopId='$workshopid' 
	                  AND DATE_FORMAT(S.Date,'%Y-%m')='$mon' 
	                  GROUP BY S.Date ORDER BY Date DESC"; 
	   $query=$this->db->query($sql);
	   return $query->result_array();                
	}
    
 
	function get_last_time($sPOrderId=''){
	        $sql = "SELECT  created  FROM  sc1_cjtj  WHERE sPOrderId=? ORDER BY created DESC Limit 1";
          $query=$this->db->query($sql,$sPOrderId);
	       if ($query->num_rows() > 0) {
		      $row = $query->first_row();
		       return $row->created==''?'':$row->created;
	      }
	     return '';
    }
    function get_am_scqty($wsid,$date='') {
	     $date=$date==''?$this->Date:$date;
       
       $time = $date.' 12:00:00';
       $time2 = $date.' 00:00:00';
       $sql="SELECT SUM(IFNULL(A.Qty*G.Price,0)) AS Qty 
       		  FROM sc1_cjtj A  
              LEFT JOIN yw1_scsheet S ON S.sPOrderId=A.sPOrderId 
              LEFT JOIN cg1_stocksheet G ON G.StockId=A.StockId 
              WHERE  A.created between ? AND ?  AND S.WorkShopId=? ";
       $params = array($time2,$time,$wsid);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   $qty=$row->Qty;
       return $qty==''?0:$qty;
    }
    
    function get_pm_scqty($wsid,$date='') {
	     $date=$date==''?$this->Date:$date;
       
       $time = $date.' 12:00:00';
       $time2 = $date.' 17:00:00';
       $sql="SELECT SUM(IFNULL(A.Qty*G.Price,0)) AS Qty 
       		  FROM sc1_cjtj A  
              LEFT JOIN yw1_scsheet S ON S.sPOrderId=A.sPOrderId 
              LEFT JOIN cg1_stocksheet G ON G.StockId=A.StockId 
              WHERE A.created between ? AND ?  AND S.WorkShopId=? ";
       $params = array($time,$time2,$wsid);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   $qty=$row->Qty;
       return $qty==''?0:$qty;
    }
    
     function get_eve_scqty($wsid,$date='') {
	     $date=$date==''?$this->Date:$date;
       
       $time = $date.' 17:00:00';
       $time2 = $date.' 23:59:59';
       $sql="SELECT SUM(IFNULL(A.Qty*G.Price,0)) AS Qty 
       		  FROM sc1_cjtj A  
              LEFT JOIN yw1_scsheet S ON S.sPOrderId=A.sPOrderId 
              LEFT JOIN cg1_stocksheet G ON G.StockId=A.StockId  
              WHERE A.created between ? AND ? AND S.WorkShopId=? ";
       $params = array($time,$time2,$wsid);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   $qty=$row->Qty;
       return $qty==''?0:$qty;
    }
 
    //日生产数量(按生产单位)
    function get_day_scqty($wsid,$date='',$groupid='') 
    { 
       $date=$date==''?$this->Date:$date;
       
       $SearchRows=$groupid==''?'':' AND A.GroupId=' . $groupid;
       
       $sql="SELECT SUM(A.Qty) AS Qty FROM sc1_cjtj A  
              LEFT JOIN yw1_scsheet S ON S.sPOrderId=A.sPOrderId 
              WHERE A.Date=? AND S.WorkShopId=? $SearchRows";
       $params = array($date,$wsid);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   $qty=$row->Qty;
       return $qty==''?0:$qty;
	}
	
	//月生产数量(按生产单位)
    function get_month_scqty($wsid,$month='') 
    { 
       $month=$month==''?date('Y-m',strtotime($this->Date)):$month;
       $sql="SELECT SUM(A.Qty) AS Qty FROM sc1_cjtj A  
              LEFT JOIN yw1_scsheet S ON S.sPOrderId=A.sPOrderId 
              WHERE DATE_FORMAT(A.Date,'%Y-%m')=? AND S.WorkShopId=? ";
       $params = array($month,$wsid);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   $qty=$row->Qty;
       return $qty==''?0:$qty;
	}
	
	 //日产值
    function get_day_output($groupid,$date='') 
    { 
       $date=$date==''?$this->Date:$date;
       $sql = "SELECT S.Qty,SUM(IFNULL(S.Qty*G.Price,0)) AS Amount  
			   FROM   sc1_cjtj S 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			   WHERE S.GroupId IN(?) AND S.Date=? ";
					
       $params = array($groupid,$date);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   return $row->Amount;
	}
	
	 //月产值
    function get_month_output($groupid,$month='') 
    { 
       $month=$month==''?date('Y-m',strtotime($this->Date)):$month;
       $sql = "SELECT S.Qty,SUM(IFNULL(S.Qty*G.Price,0)) AS Amount  
			   FROM   sc1_cjtj S 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			   WHERE S.GroupId IN(?) AND DATE_FORMAT(S.Date,'%Y-%m')=? ";
					
       $params = array($groupid,$month);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   return $row->Amount;
	}
	
	 //日产值(按生产单位)
    function get_workshop_day_output($wsid,$date='') 
    { 
       $date=$date==''?$this->Date:$date;
       $sql = "SELECT S.Qty,SUM(IFNULL(S.Qty*G.Price,0)) AS Amount  
			   FROM   sc1_cjtj S 
			   LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			   WHERE  A.WorkShopId=? AND S.Date=? ";
       $params = array($wsid,$date);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   return $row->Amount;
	}
	
	 //月产值(按生产单位)
    function get_workshop_month_output($wsid,$month='') 
    { 
       $month=$month==''?date('Y-m',strtotime($this->Date)):$month;
       $sql = "SELECT S.Qty,SUM(IFNULL(S.Qty*G.Price,0)) AS Amount  
			   FROM   sc1_cjtj S 
			   LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			   WHERE A.WorkShopId=? AND DATE_FORMAT(S.Date,'%Y-%m')=? ";
					
       $params = array($wsid,$month);
       $query=$this->db->query($sql,$params);
	   $row = $query->first_row();
	   return $row->Amount;
	}

		
	//日估值
	function get_day_valuation($groupid) 
    { 
       $i=1;$n=0;$sumAmount=0;
       do{
	      $date=date('Y-m-d',strtotime("-$i day"));
	      $sql = "SELECT S.Qty,SUM(IFNULL(S.Qty*G.Price,0)) AS Amount  
			   FROM   sc1_cjtj S 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			   WHERE S.GroupId=? AND S.Date=?";
		   $params = array($groupid,$date);
           $query=$this->db->query($sql,$params);
           $row = $query->first_row();
           if ($row->Amount>0){
	           $sumAmount+=$row->Amount;
	           $n++;
           }
           $i++;
       }while($i<=$this->ValuationDates && $i<30 && $date>=date('Y-m-d',strtotime($this->StartDate)));
	   return $n>0?round($sumAmount/$n):0;
	}
	
	//月估值
	function get_month_valuation($groupid) 
    { 
       $i=1;$n=0;$sumAmount=0;$minAmount=0;
       do{
	      $month=date('Y-m',strtotime("-$i month"));
          $sql = "SELECT S.Qty,SUM(IFNULL(S.Qty*G.Price,0)) AS Amount  
			   FROM   sc1_cjtj S 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			   WHERE S.GroupId IN($groupid) AND DATE_FORMAT(S.Date,'%Y-%m')='$month' ";	
           //echo $sql;
           $query=$this->db->query($sql);
           $row = $query->first_row();
           if ($row->Amount>0){
	           $sumAmount+=$row->Amount;
	           $minAmount=$minAmount==0?$row->Amount:($row->Amount<$minAmount?$row->Amount:$minAmount);
	           $n++;
           }
           $i++;
       }while($i<=$this->ValuationMonths && $i<5 && $month>=date('Y-m',strtotime($this->StartDate)));
       if ($minAmount>0 && $n>1){
	       $sumAmount-=$minAmount;
	       $n--;
       }
	   return $n>0?round($sumAmount/$n):0;
	}
	
	//10日平均产值
	function get_day_average($groupid)
	{
	    $days = 10;
		$edate=date('Y-m-d',strtotime("-31 day"));
		
		if ($groupid!='0'){
			$sql = "SELECT SUM(IFNULL(A.Amount,0)) AS Amount FROM (
		            SELECT  SUM(S.Qty*G.Price) AS Amount  
		                 FROM sc1_cjtj S 
		                 LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
		                 WHERE S.Date<CURDATE() and S.DATE>'$edate' AND S.GroupId IN ($groupid)  
			             GROUP BY S.Date  ORDER BY S.Date DESC LIMIT 10
			    )A";
		}else{
			 $sql = "SELECT SUM(IFNULL(A.Amount,0)) AS Amount FROM (
		             SELECT  SUM(S.Qty*G.Price) AS Amount  
		                 FROM sc1_cjtj S 
                         INNER JOIN cg1_stocksheet G ON G.StockId=S.StockId 
		                 LEFT JOIN yw1_scsheet A  ON A.sPOrderId=S.sPOrderId 
		                 WHERE S.Date<CURDATE() and S.DATE>'$edate' AND A.ActionId IN (102,103,104) 
			             GROUP BY S.Date  ORDER BY S.Date DESC LIMIT 10
			    )A";
		}
			      
	     $query=$this->db->query($sql);
         $row = $query->first_row();
         
         return round($row->Amount/$days);
	}   
	
	//工单生产数量
	function get_scqty($sPOrderId){
	      $sql = "SELECT  SUM(Qty) AS Qty  FROM  sc1_cjtj  WHERE sPOrderId=?"; 
          $query=$this->db->query($sql,$sPOrderId);
	      $row = $query->first_row();
	      return $row->Qty==""?0:$row->Qty;
    }
    
    //生产登记人
	function get_sc_lastoper($sPOrderId){
	      $sql = "SELECT  M.Name  
	      FROM  sc1_cjtj C 
	      left join staffmain M on M.Number=IF(C.Operator=0,C.Leader,C.Operator) 
	      WHERE C.sPOrderId=? order by C.Id desc limit 1"; 
          $query=$this->db->query($sql,$sPOrderId);
	      
	      if ($query->num_rows() > 0) {
		      $row = $query->first_row();
		      return $row->Name;
	      }
	      return '';
    }
    
   //工单生产登记的最新时间
    function get_scdjtime($sPOrderId){
	      $sql = "SELECT  created  FROM  sc1_cjtj  WHERE sPOrderId=? ORDER BY created DESC Limit 1"; 
          $query=$this->db->query($sql,$sPOrderId);
	       if ($query->num_rows() > 0) {
		      $row = $query->first_row();
		       return $row->created;
	      }
	     return '';
    }
    
    //读取记录
    function get_records($sPOrderId){
	      $sql = "SELECT Id,GroupId,sPOrderId,POrderId,StockId,Qty,Remark,boxId,Date,IFNULL(creator,Leader) as Operator,created 
			      FROM  sc1_cjtj 
			      WHERE sPOrderId =?  ORDER BY Id DESC"; 
          $params = array($sPOrderId);
          $query=$this->db->query($sql,$params);
	      return $query->result_array();
    }
    
    //获取生产记录
    function get_workshop_screcords($wsid,$date='',$limits=0)
    {
	    $date=$date==''?$this->Date:$date;
	    $limits=$limits==0?'':" LIMIT $limits ";
	    
	    switch($wsid){
	       case 101:
	          $sql = "SELECT S.Id,S.GroupId,S.sPOrderId,S.POrderId,S.StockId,S.Qty,S.Remark,P.ProductId,P.cName,P.TestStandard,
	                        IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek,G.Price ,C.Forshort  ,L.Letter as Line,S.created 
			      FROM  sc1_cjtj S 
                  INNER JOIN yw1_scsheet A  ON A.sPOrderId=S.sPOrderId 
                  INNER JOIN yw1_ordersheet Y  ON Y.POrderId=A.POrderId 
                  INNER JOIN yw1_ordermain M ON M.OrderNumber=Y.OrderNumber
	              INNER JOIN trade_object C ON C.CompanyId=M.CompanyId 
                  INNER JOIN productdata P     ON P.ProductId=Y.ProductId 
				  LEFT  JOIN yw3_pisheet PI    ON PI.oId=Y.Id
			      LEFT  JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
			      LEFT  JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      LEFT  JOIN workscline  L ON L.Id=A.scLineId
			      WHERE S.Date='$date' AND S.Qty>0 AND A.WorkShopId='$wsid' ORDER BY Id DESC $limits"; 
			 break;
		   default:
		      $sql = "SELECT S.Id,S.GroupId,S.sPOrderId,S.POrderId,S.StockId,S.Qty,S.Remark,D.StuffId,D.StuffCname,D.Picture,
	                        M.DeliveryWeek AS LeadWeek,G.Price,S.created,F.Name AS creator  
			      FROM  sc1_cjtj S 
                  INNER JOIN yw1_scsheet A  ON A.sPOrderId=S.sPOrderId 
                  LEFT  JOIN cg1_stocksheet M ON M.StockId=A.mStockId 
			      LEFT  JOIN cg1_stocksheet G ON G.StockId=S.StockId 
                  LEFT  JOIN stuffdata D ON D.StuffId=M.StuffId 
                  LEFT  JOIN staffmain F ON F.Number=S.creator  
			       WHERE S.Date='$date' AND S.Qty>0 AND A.WorkShopId='$wsid' ORDER BY Id DESC $limits"; 
		     break;
		}
  
	    $query=$this->db->query($sql);
	    
	    if ($query->num_rows() > 0) {
		     return $query->result_array();
		}else{
			 return array();
		}
		 
    }

    
    //已生产异常单数量 月统计
    public function mon_workshop_abnormals($wsid,$mon='') {
	    $sql = "  select sum(if(B.hours<12,B.Qty,0)) qty1, 
			               sum(if(B.hours>=12 and B.hours<=48,B.Qty,0)) qty2, 
			               sum(if(B.hours>48,B.Qty,0)) qty3
			               from (
				               select A.Qty ,TIMESTAMPDIFF(HOUR,A.begintime,A.lasttime) hours
	                  from (
		                  select S.Qty ,Max(C.created) lasttime,Min(C.created) begintime
	                 
	                  from 
	                  yw1_scsheet S
	                  left join sc1_cjtj C ON S.sPOrderId=C.sPOrderId  
	                  WHERE   S.ScFrom=0 and s.WorkShopId=$wsid  and DATE_FORMAT(S.FinishDate,'%Y-%m')='$mon'
	                  GROUP BY S.sPOrderId
	                  ) A
			               ) B
";

			$query=$this->db->query($sql);
	      return $query->first_row('array');
    }
    
    
    
    public function delete_item($params) {
		$del_id   = element('Id', $params, -1);
		if ($del_id <= 0) {
			return -1;	
		} else {
			$this->load->model('oprationlogModel');
			$LogItem = '生产登记';
			$LogFunction = '删除纪录';
			$Log = '生产登记表Id为:'.$del_id.'的记录';
			$this->db->where('Id', $del_id);
			$this->db->trans_begin();
			$query = $this->db->delete('sc1_cjtj'); 
			$OP = 'N';
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $Log .= '删除失败';
			}
			else{
			    $this->db->trans_commit();
			    $Log .= '删除成功';
			    $OP = 'Y';
			}
			$this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log,'OperationResult'=>$OP));
			return $query;
		}
		
	}
	
	
    
	//保存生产记录
	function save_records($params){
	
	    $sPOrderId=element('Id',$params,'0');
	    $djtype     = element('djType',$params,'');
	    $idcard     = element('idcard',$params,'');
	    $djQty      = element('Qty',$params,'0');
	         
	    if ($sPOrderId>0){
	        $this->load->model('staffMainModel');
	        $GroupId=$this->staffMainModel->get_groupid($this->LoginNumber);
	        
	        $Operator=$this->LoginNumber;
	        if ($djtype=='qrdj'){
		        if ($idcard!='')
		        $Operator=$this->staffMainModel->get_number_fromidcard($idcard);
		        
		        $Operator=$Operator==''?$this->LoginNumber:$Operator;
	        }
	        
	        
	        $personQuery = $this->staffMainModel->get_record($Operator,'Estate');
	        $boolCanSave = false;
	        if ($personQuery->num_rows() > 0) {
// 			        $GroupIdArr = explode(',', $GroupId);
		        $personRow = $personQuery->row_array();
// 			        in_array($personRow['GroupId'], $GroupIdArr) && 
		        if ($personRow['Estate']==1) {
			        $boolCanSave = true;
		        }
	        }
	        
	        if ($boolCanSave == false ) {
		        return 0;
	        }


		    $this->load->model('ScSheetModel');
	        $records=$this->ScSheetModel->get_records($sPOrderId);
	        $POrderId = $records['POrderId'];
	        $StockId  = $records['StockId'];
	        $OrderQty = $records['Qty'];
	        
	        $djedQty = $this->get_scqty($sPOrderId);
	        
	        if (($djedQty+$djQty)<=$OrderQty){
		        $data=array(
	                'GroupId'=>$GroupId,  
	              'sPOrderId'=>$sPOrderId,
	               'POrderId'=>$POrderId,
	                'StockId'=>$StockId,
	                    'Qty'=>$djQty,
	                 'Remark'=>'',
	                 'Estate'=>'1',
	                  'Locks'=>'0',
	                   'Date'=>$this->Date,
	                 'Leader'=>'0', 
	               'Operator'=>$Operator 
		       );
		       $this->db->insert('sc1_cjtj', $data); 
		       
		       return $this->db->affected_rows();
	      }else{
		       return 0;
	      }
	   }else{
		   return 0;
	   }
	}
	
	
	
	
	
	
	
	
}