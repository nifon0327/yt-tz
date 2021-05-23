<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewForwardModel  
* 备品转入类  sql: ac.ck7_bprk 
* 
*/ 
class  Ck7bprkModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    
    function pass_item($Id) {
	    
	    
	    $data=array(
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
	              
	   $this->db->update('ck7_bprk',$data, "Id IN ($Id)");
	   return  $this->save_inrk($Id);
	  // return $this->db->affected_rows();
    }
    
    function back_item($Id, $reason='') {
	    
	    
	    $data=array('Estate'  =>'2',
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
	    
	   $this->db->update('ck7_bprk',$data, "Id IN ($Id)");
	   $rs = $this->db->affected_rows();
	   if ($rs > 0) {
		   $DateTime = $this->DateTime;
		   $Operator = $this->LoginNumber;
		   $returnReasonSql = "Insert Into returnreason (Id, tableId, targetTable, Reason, DateTime,Operator) Values (NULL, '$Id', 'ac.ck7_bprk','$reason', '$DateTime','$Operator')";
		   
		   $this->db->query($returnReasonSql);
		   
	   }
	   return $rs;
    }
    function get_locationid($Id) {
	    
	    $sql = "select LocationId from ck7_bprk where Id=$Id";
	    $query = $this->db->query($sql);
	    if ($query->num_rows() > 0) {
		    return $query->row()->LocationId;
	    }
	    return '';
	    
	    
    }
    
    
    function get_model_pick($editid=-1) {
		
	   $rows = array();
	   $editids = explode("|", $editid);
	   if (count($editids) >1) {
		   $editid = $editids[0];
	   }
		$date = $StuffId = $StuffCname = $num = $Type = $typeName = $reason = $url = "";
		$sql = "select S.Id,S.StuffId,S.Qty,S.Remark,S.Date,D.StuffCname
from ck7_bprk S 
left join stuffdata D on S.StuffId=D.StuffId
where S.Id=? limit 1;";
		$query = $this->db->query($sql,$editid);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$date = $row["Date"];
			$StuffId = $row["StuffId"];
			$num = $row["Qty"];
			$reason = $row["Remark"];


			$StuffCname = $row["StuffCname"];
			
			$StuffId = $row["StuffId"];
			
		}

		
		
		$totalArray = array();
		$totalArray[]=array("FieldVal"=>"$date","ContentTxt"=>"$date");
		$totalArray[]=array("FieldVal"=>"$StuffId",
							"ContentTxt"=>"$StuffId"."-"."$StuffCname");
		$totalArray[]=array("FieldVal"=>"$num","ContentTxt"=>"$num");

		$totalArray[]=array("FieldVal"=>"$reason","ContentTxt"=>"");


		/*
			 @"FieldVal";
NSString *const Key_Dict_FieldVals    = @"FieldVals";
NSString *const Key_Dict_Content     = @"ContentTxt";
		*/
		return  $totalArray;
	}

    
    
    public function delete_item($del_id) {

		if ($del_id <= 0) {
			return -1;	
		} else {
			$this->load->model('oprationlogModel');
			$LogItem = '备品转入';
			$LogFunction = '删除纪录';
			$Log = '备品转入表Id为:'.$del_id.'的记录';
			$this->db->where('Id', $del_id);
			$this->db->trans_begin();
			$query = $this->db->delete('ck7_bprk'); 
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

    
    function get_returnreason_row($Id) {
	    
	    $targetTable = 'ac.ck7_bprk';
	    $checkReason = $this->db->query("select R.Reason,R.DateTime,M.Name from returnreason R
left join staffmain M on R.Operator=M.Number
 where  R.targetTable ='$targetTable' and R.tableId=$Id order by R.Id desc limit 1;");
		if ($checkReason->num_rows() > 0) {
			return $checkReason->row_array();
		}
		return null;
	    
    }
    
    
    function searched_list($searched) {
	    $searched = trim($searched);
	    if ($searched!='') {
		    $sql = "SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,C.PreChar,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price*C.Rate) AS Amount  ,L.Region,L.Location 
FROM ck7_bprk B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
LEFT JOIN ck_location L ON L.Id=B.LocationId 
WHERE  1 AND (B.Remark like '%$searched%' or D.StuffCname like '%$searched%' or B.StuffId='$searched') ORDER BY  field(B.Estate,1,3,2,0), B.Date DESC LIMIT 500";
		$query = $this->db->query($sql);
		return $query;
	    }
	    return null;
    }
    
    
    function notoklist() {
	    $sql = "SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,C.PreChar,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price*C.Rate) AS Amount  ,L.Region,L.Location 
FROM ck7_bprk B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
LEFT JOIN ck_location L ON L.Id=B.LocationId 
WHERE  1 AND B.Estate>=1 ORDER BY  field(B.Estate,1,3,2), B.Date DESC";
		$query = $this->db->query($sql);
		return $query;
    }
    function month_type_sublist($month, $TypeId) {
	    
	    $sql = "SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,M.Name AS Operator,C.PreChar,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price*C.Rate) AS Amount  ,L.Region,L.Location 
FROM ck7_bprk B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
LEFT JOIN ck_location L ON L.Id=B.LocationId 
WHERE 1 AND DATE_FORMAT(B.Date,'%Y-%m')='$month' AND D.TypeId='$TypeId' ORDER BY Amount DESC";
		$query = $this->db->query($sql);
		return $query;
    }
    
    
    function month_subtypes($month) {
	   
	   $sql = "SELECT T.TypeId,T.TypeName,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,C.PreChar 
FROM ck7_bprk B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN  stufftype  T ON T.TypeId=D.TypeId 
WHERE DATE_FORMAT(B.Date,'%Y-%m')='$month'  GROUP BY T.TypeId ORDER BY Amount DESC";
		$query = $this->db->query($sql);
		return $query;
   }
     function get_months_record() {
	   
	   $sql = "
SELECT DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount 
FROM ck7_bprk B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
WHERE 1 AND B.Estate=0 GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC;";
		$query = $this->db->query($sql);
		return $query;
   }
   
    
    //当天的备品数量，按送货楼层分类统计(已弃用)
    function get_sendfloor_dayqty($date='')
    {
       $date=$date==''?$this->Date:$date;
	   $sql = "SELECT D.SendFloor,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck7_bprk B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           WHERE B.Estate= 0 AND B.Date = ? GROUP BY D.SendFloor";
	    $query=$this->db->query($sql,array($date));
	    
	    $rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]=$row['Qty'];
	    }
	    return $rowsArray;
   }
   
    //当月的备品数量，按送货楼层分类统计(已弃用)
   function get_sendfloor_monthqty($month='')
    {
       $month=$month==''?date("Y-m"):$month;
	   $sql = "SELECT D.SendFloor,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck7_bprk B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           WHERE B.Estate= 0 AND DATE_FORMAT(B.Date,'%Y-%m') = ? GROUP BY D.SendFloor";
	    $query=$this->db->query($sql,array($month));
	    
	    $rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $SendFloor=$row['SendFloor'];
	        $rowsArray[$SendFloor]=$row['Qty'];
	    }
	    return $rowsArray;
   }

     //当天的备品数量，按送货楼层分类统计
    function get_warehouse_dayqty($date='')
    {
       $date=$date==''?$this->Date:$date;
	   $sql = "SELECT M.WarehouseId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck7_bprk B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           INNER JOIN base_mposition M ON M.Id=D.SendFloor 
	           WHERE B.Estate= 0 AND B.Date = ? GROUP BY M.WarehouseId";
	    $query=$this->db->query($sql,array($date));
	    
	    $rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $WarehouseId=$row['WarehouseId'];
	        $rowsArray[$WarehouseId]=$row['Qty'];
	    }
	    return $rowsArray;
   }
   
   //当月的备品数量，按仓库分类统计
   function get_warehouse_monthqty($month='')
    {
       $month=$month==''?date("Y-m"):$month;
	   $sql = "SELECT M.WarehouseId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck7_bprk B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
               INNER JOIN base_mposition M ON M.Id=D.SendFloor 
	           WHERE B.Estate= 0 AND DATE_FORMAT(B.Date,'%Y-%m') = ? GROUP BY M.WarehouseId";
	    $query=$this->db->query($sql,array($month));
	    
	    $rowsArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $WarehouseId=$row['WarehouseId'];
	        $rowsArray[$WarehouseId]=$row['Qty'];
	    }
	    return $rowsArray;
   }
   
 
    //保存备品入库记录
    function save_records($StuffId,$bpQty,$Remark,$rkSign=0, $locationId=0)
    {
       $data = array(
			'StuffId' => $StuffId,
            'Remark'  => $Remark,
			'Qty'     => $bpQty,
			'Estate'  => '1',
            'Locks'   => '0',
			'Date'    => $this->Date,
			'Operator'=> $this->LoginNumber,
		    'created' => $this->DateTime,
		    'creator' => $this->LoginNumber,
		    'LocationId'=>$locationId
		);
		$this->db->trans_begin();
		
		$this->db->insert('ck7_bprk', $data); 
		
		$newId= $this->db->insert_id();
		/*
		if ($newId>0 && $rkSign==1){
			$this->save_inrk($newId);
		}
		*/
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return 0;
		} else {
		    $this->db->trans_commit();
		    return $newId;
		}
    }
    
    //保存至入库表
    function save_inrk($Id)
    {
        $query=$this->db->query("CALL proc_ck7_bprk_updatedestate('$Id','" .$this->LoginNumber ."');");
        $row = $query->first_row('array');
        return $row['OperationResult']=='Y'?1:0;
    }

    
    /*------------以下为旧代码-----------*/
	
	/** 
	* get_month_qty  
	* 当月的备品数量，按送货楼层来计算
	* 
	* @access public 
	* @param  $SendFloor
	* @return qty
	
	*/  
   public  function get_month_qty($sendfloor)
   {
	   $thismonth = date("Y-m");
	   $sql = "SELECT SUM(B.Qty) AS Qty 
	           FROM ck7_bprk B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           WHERE B.Estate= 0 AND DATE_FORMAT(B.Date,'%Y-%m') = ? AND D.SendFloor = ?";
	    $query=$this->db->query($sql,array($thismonth,$sendfloor));
	    $row = $query->first_row();
	    $qty=$row->Qty;
        return $qty==''?0:$qty; 
   }	
	

      public  function get_month_qtycount($sendfloor)
   {
	   
	    $thismonth = date("Y-m");
	    $sql = "select count(*) counts ,SUM(A.Qty) AS Qty from 
	    ( SELECT SUM(B.Qty) AS Qty FROM ck7_bprk B
	    LEFT JOIN  stuffdata D ON D.StuffId = B.StuffId
	    WHERE D.SendFloor=? AND DATE_FORMAT(B.Date,'%Y-%m') = ? group by D.StuffId) 
	    A";
	    $query = $this->db->query($sql,array($sendfloor,$thismonth));
		$row = $query->first_row('array');
		return $row;
   }

	/** 
	* get_day_qty  
	* 当天的备品数量，按送货楼层来计算
	* 
	* @access public 
	* @param  $SendFloor
	* @return qty
	
	
	*/  
   public  function get_day_qty($sendfloor)
   {
	   $sql = "SELECT SUM(B.Qty) AS Qty 
	           FROM ck7_bprk B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           WHERE B.Estate= 0 AND B.Date = ? AND D.SendFloor = ?";
	    $query=$this->db->query($sql,array($this->Date,$sendfloor));
	    $row = $query->first_row();
	    $qty=$row->Qty;
        return $qty==''?0:$qty; 
   }
   
      public  function get_day_qtycount($sendfloor)
   {
	   
	    $sql = "select count(*) counts ,SUM(A.Qty) AS Qty from 
	    ( SELECT SUM(B.Qty) AS Qty FROM ck7_bprk B
	    LEFT JOIN  stuffdata D ON D.StuffId = B.StuffId
	    WHERE D.SendFloor=? AND B.Date=? group by D.StuffId) 
	    A";
	    $query = $this->db->query($sql,array($sendfloor,$this->Date));
		$row = $query->first_row('array');
		return $row;
   }

   
	/** 
	* save_item  
	* 插入一条记录
	* 
	* @access public 
	* @param  params array 一条纪录所需数据
	* @return int 返回生成的主键(失败返回-1) auto_generated_keyid
	
	
	*/  

	public function save_item($params) {
		
			
		$this->load->model('oprationlogModel');
		$LogItem = '仓库备品';
		$LogFunction = '备品转入';
		$Log = '';
		
		$data = array(
			'StuffId' => element('stuffid', $params, '-1'),
            'Remark'  => element('remark', $params, ''),
			'Qty'   => element('num',  $params, '0'),
			'Date'     => element('indate', $params, '0'),
			'LocationId'     => element('location', $params, '0'),
		    'created'  => $this->DateTime,
		    'creator'  => $this->LoginNumber,
            'Operator' => $this->LoginNumber,
            'Estate'   => '1',
            'Locks'=>"0"
		);
		$this->db->trans_begin();
		$query     = $this->db->insert('ck7_bprk', $data); 
		$insert_id = $this->db->insert_id();
		$OP = 'N';
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $Log.='备品转入失败！';
		} else {
			    $this->db->trans_commit();
			    $Log.='备品转入成功！ID：$insert_id';
			    $OP = 'Y';
		}
		
		 $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,
		                                          'LogFunction'=>$LogFunction,
		                                          'Log'=>$Log,
		                                          'OperationResult'=>$OP)
		                                      );
		if ($insert_id > 0){  						  
		     return $insert_id;
		} else {
			return -1;	
		}
		
	}
	
	public function edit_item($params) {
		
			
		$this->load->model('oprationlogModel');
		$LogItem = '仓库备品';
		$LogFunction = '备品转入';
		$Log = '';
		$editid = element('editid', $params, '-1');
		if ($editid < 0) {
			return -1;
		}
		$data = array(
			'StuffId' => element('stuffid', $params, '-1'),
            'Remark'  => element('remark', $params, ''),
			'Qty'   => element('num',  $params, '0'),
			'Date'     => element('indate', $params, '0'),
		    'modified'  => $this->DateTime,
		    'modifier'  => $this->LoginNumber,
            'Operator' => $this->LoginNumber,
            'Estate'   => '1',
            'Locks'=>"0"
		);
		
		if ( element('location', $params, '') != '') {
		    $data['LocationId'] =element('location', $params, '0');
	    }
			    
		
		$this->db->where('Id',$editid);
		$this->db->trans_begin();
		$query=$this->db->update('ck7_bprk', $data);

		
		
		

		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    
				return -1;
		} else {
			    $this->db->trans_commit();
			    
			    return 1;
		}
		
		
	}
	 
	public function  getPrintDict($params) {
		$stuffid = element('stuffid', $params, '-1');
		$num = element('num',  $params, '0');
		$companyid = '-1';
		$StuffCname = "";
		$Forshort = "";
		$query = $this->db->query("select B.CompanyId,D.FrameCapacity,C.Forshort,
		D.CheckSign,
		D.StuffCname from
		stuffdata D
		left join bps B on B.StuffId=D.StuffId
		left join trade_object C on C.CompanyId=B.CompanyId
		where D.StuffId=?",$stuffid);
		$FrameCapacity = 0;
		$CheckSign = '--';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$companyid = $row->CompanyId;
			$FrameCapacity =  $row->FrameCapacity;
			$StuffCname = $row->StuffCname; 
			$Forshort = $row->Forshort; 
			$CheckSign= $row->CheckSign;
		}
		
		 
         switch($CheckSign){
                  case "0":$CheckSign="抽";break;
                  case "1":$CheckSign="全";break;
                  case "99":$CheckSign="--";break;
              }
		
		 $stuffProp = array();
		 $PropertyResult=$this->db->query("SELECT T.TypeName  FROM stuffproperty P 
			   left join stuffpropertytype T on P.Property=T.Id 
			   WHERE P.StuffId=? ORDER BY Property",$stuffid);
			   
		 foreach( $PropertyResult->result_array() as $PropertyRow ){
		                  $stuffProp[]=$PropertyRow['TypeName'];
		                  
		 }

         $Oper = '---';
         $this->load->model('StaffMainModel');
         $Oper = $this->StaffMainModel->get_staffname($this->LoginNumber);
         
		 $ip = '192.168.30.101';
		 $Weeks = date('W');
		 $CGPO = "$stuffid|$companyid|$num";
		 $printDict= array('CGPO'=>"$CGPO",'Week'=>"$Weeks",'cName'=>"$StuffCname",'OrderQty'=>'',
		                   'Forshort'=>"$Forshort",'GXQty'=>"$num",'stuffid'=>"$stuffid",'time'=>'','oper'=>"$Oper",
		                   'props'=>$stuffProp,'way'=>$CheckSign,'Frame'=>"$FrameCapacity",'Qty'=>"$num",'ip'=>"$ip");						 
		 return $printDict ;											
	 }
}