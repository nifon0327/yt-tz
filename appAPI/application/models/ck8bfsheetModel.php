<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class NewForwardModel  
* 新品转发记录类  sql: ac.new_forward 
* 
*/ 
class  Ck8bfsheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    
    function get_locationid($Id) {
	    
	    $sql = "select LocationId from ck8_bfsheet where Id=$Id";
	    $query = $this->db->query($sql);
	    if ($query->num_rows() > 0) {
		    return $query->row()->LocationId;
	    }
	    return '';
	    
	    
    }
    
    
    //保存至入库表
    function save_inrk($Id)
    {
        $query=$this->db->query("CALL proc_ck8_bfsheet_updatedestate('$Id','" .$this->LoginNumber ."');");
        $row = $query->first_row('array');
        return $row['OperationResult']=='Y'?1:0;
    }
    
    
    function pass_item($Id) {
	    
	    
	    $data=array(
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
	              
	   $this->db->update('ck8_bfsheet',$data, "Id IN ($Id)");
	   return  $this->save_inrk($Id);
	  // return $this->db->affected_rows();
    }
    
    function back_item($Id, $reason='') {
	    
	    
	    $data=array('Estate'  =>'2',
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );
	    
	   $this->db->update('ck8_bfsheet',$data, "Id IN ($Id)");
	   $rs = $this->db->affected_rows();
	   if ($rs > 0) {
		   $DateTime = $this->DateTime;
		   $Operator = $this->LoginNumber;
		   $returnReasonSql = "Insert Into returnreason (Id, tableId, targetTable, Reason, DateTime,Operator) Values (NULL, '$Id', 'ac.ck8_bfsheet','$reason', '$DateTime','$Operator')";
		   
		   $this->db->query($returnReasonSql);
		   
	   }
	   return $rs;
    }
    
    
    
    public function delete_item($del_id) {

		if ($del_id <= 0) {
			return -1;	
		} else {
			$this->load->model('oprationlogModel');
			$LogItem = '仓库报废';
			$LogFunction = '删除纪录';
			$Log = '仓库表Id为:'.$del_id.'的记录';
			$this->db->where('Id', $del_id);
			$this->db->trans_begin();
			$query = $this->db->delete('ck8_bfsheet'); 
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
	    
	    $targetTable = 'ac.ck8_bfsheet';
	    $checkReason = $this->db->query("select R.Reason,R.DateTime,M.Name from returnreason R
left join staffmain M on R.Operator=M.Number
 where  R.targetTable ='$targetTable' and R.tableId=$Id order by R.Id desc limit 1;");
		if ($checkReason->num_rows() > 0) {
			return $checkReason->row_array();
		}
		return null;
	    
    }
    
    function get_bf_remark($Id) {
	    $sql = "SELECT A.Date,A.Remark,M.Name   
		                                FROM  ck8_bfremark  A
		                                LEFT JOIN staffmain M ON M.Number=A.Operator
										WHERE  A.Mid='$Id' ORDER BY A.Date DESC,A.Id DESC LIMIT 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		return null;
    }
    
    
    
    function searched_list($searched) {
	    $searched = trim($searched);
	    if ($searched!='') {
		    $sql = "SELECT B.Id,B.Bill,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,C.PreChar ,L.Region,L.Location    
FROM ck8_bfsheet B 
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
	    $sql = "SELECT B.Id,B.Bill,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,C.PreChar ,L.Region,L.Location    
FROM ck8_bfsheet B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
LEFT JOIN ck_location L ON L.Id=B.LocationId 
WHERE  1 AND B.Estate>=1 ORDER BY  field(B.Estate,1,3,2), B.Date DESC";

if ($this->LoginNumber==11965) {
/*
	$sql = "SELECT B.Id,B.Bill,B.StuffId,B.Qty,B.Remark,B.Date,1 Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,C.PreChar ,L.Region,L.Location    
FROM ck8_bfsheet B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
LEFT JOIN ck_location L ON L.Id=B.LocationId 
WHERE  1   ORDER BY  field(B.Estate,1,3,2), B.Date DESC LIMIT 500";
*/
}
		$query = $this->db->query($sql);
		return $query;
    }
     
    function month_type_sublist($month, $TypeId) {
	    
	    $sql = "SELECT B.Id,B.Bill,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,C.PreChar ,L.Region,L.Location    
FROM ck8_bfsheet B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
LEFT JOIN ck_location L ON L.Id=B.LocationId 
WHERE 1 AND DATE_FORMAT(B.Date,'%Y-%m')='$month' AND D.TypeId='$TypeId' ORDER BY Estate DESC";
		$query = $this->db->query($sql);
		return $query;
    }
    
    
    function month_subtypes($month) {
	   
	   $sql = "SELECT T.TypeId,T.TypeName,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,C.PreChar 
FROM ck8_bfsheet B 
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
	    SELECT DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,SUM(IF(B.Estate=3,1,0)) AS Estates 
FROM ck8_bfsheet B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN  trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  currencydata C ON C.Id=P.Currency
WHERE 1 AND B.Estate=0 GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC
	    ";
	    $query = $this->db->query($sql);
		return $query;
    }
	
	//当天的报废数量，按送货楼层分类统计(已弃用)
	function get_sendfloor_dayqty($date='')
    {
       $date=$date==''?$this->Date:$date;
	   $sql = "SELECT D.SendFloor,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck8_bfsheet  B 
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
   
   //当月的报废数量，按送货楼层分类统计(已弃用)
   function get_sendfloor_monthqty($month='')
    {
       $month=$month==''?date("Y-m"):$month;
       $thisMonth = date("Y-m");
	   $sql = "SELECT D.SendFloor,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM  ck8_bfsheet  B 
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
   
   //当天的报废数量，按送货楼层分类统计
	function get_warehouse_dayqty($date='')
    {
       $date=$date==''?$this->Date:$date;
	   $sql = "SELECT M.WarehouseId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck8_bfsheet  B 
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
   
   //当月的报废数量，按送货楼层分类统计
   function get_warehouse_monthqty($month='')
    {
       $month=$month==''?date("Y-m"):$month;
       $thisMonth = date("Y-m");
	   $sql = "SELECT M.WarehouseId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM  ck8_bfsheet  B 
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
   


    /** 
	* get_month_qty  
	* 当月的报废数量，按送货楼层来计算
	* 
	* @access public 
	* @param  $SendFloor
	* @return qty
	*/  
   public  function get_month_qty($sendfloor)
   {
	   $thismonth = date("Y-m");
	   $sql = "SELECT SUM(B.Qty) AS Qty 
	           FROM ck8_bfsheet B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           WHERE B.Estate= 0 AND  DATE_FORMAT(B.Date,'%Y-%m') = ?  AND D.SendFloor = ?";
	    $query=$this->db->query($sql,array($thismonth,$sendfloor));
	    $row = $query->first_row();
	    $qty=$row->Qty;
        return $qty==''?0:$qty; 
   } 
    public  function get_month_qtycount($sendfloor)
   {
	   
	    $thismonth = date("Y-m");
	    $sql = "select count(*) counts ,SUM(A.Qty) AS Qty from 
	    ( SELECT SUM(B.Qty) AS Qty FROM ck8_bfsheet B
	    LEFT JOIN  stuffdata D ON D.StuffId = B.StuffId
	    WHERE D.SendFloor=? AND DATE_FORMAT(B.Date,'%Y-%m') = ? group by D.StuffId) 
	    A";
	    $query = $this->db->query($sql,array($sendfloor,$thismonth));
		$row = $query->first_row('array');
		return $row;
   }

   
	/** 
	* get_day_qty  
	* 当天的报废数量，按送货楼层来计算
	* 
	* @access public 
	* @param  $SendFloor
	* @return qty
	*/  
   public  function get_day_qty($sendfloor)
   {
	   $sql = "SELECT SUM(B.Qty) AS Qty 
	           FROM ck8_bfsheet B 
	           INNER JOIN stuffdata D ON D.StuffId = B.StuffId
	           WHERE B.Estate= 0 AND  B.Date = ? AND D.SendFloor = ?";
	    $query=$this->db->query($sql,array($this->Date,$sendfloor));
	    $row = $query->first_row();
	    $qty=$row->Qty;
        return $qty==''?0:$qty; 
   } 
        public  function get_day_qtycount($sendfloor)
   {
	   
	    $sql = "select count(*) counts ,SUM(A.Qty) AS Qty from 
	    ( SELECT SUM(B.Qty) AS Qty FROM ck8_bfsheet B
	    LEFT JOIN  stuffdata D ON D.StuffId = B.StuffId
	    WHERE D.SendFloor=? AND B.Date=? group by D.StuffId) 
	    A";
	    $query = $this->db->query($sql,array($sendfloor,$this->Date));
		$row = $query->first_row('array');
		return $row;
   }

   
	
	function get_model_pick($editid=-1) {
		
	   $rows = array();
	   $editids = explode("|", $editid);
	   if (count($editids) >1) {
		   $editid = $editids[0];
	   }
		$date = $StuffId = $StuffCname = $num = $Type = $typeName = $reason = $url = "";
		$sql = "select S.Id,S.StuffId,S.Qty,S.Remark,S.Type,S.Bill,S.Date,D.StuffCname
from ck8_bfsheet S 
left join stuffdata D on S.StuffId=D.StuffId
where S.Id=? limit 1;";
		$query = $this->db->query($sql,$editid);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$date = $row["Date"];
			$StuffId = $row["StuffId"];
			$num = $row["Qty"];
			$reason = $row["Remark"];
			$Type = $row["Type"];
			$Bill = $row["Bill"];
			$StuffCname = $row["StuffCname"];
			$url = $Bill>=1 ? "http://www.ashcloud.com/download/ckbf/B".$editid.".jpg":"";
			
			$StuffId = $row["StuffId"];
			
		}
		$this->load->model('ck8bftypeModel');
		$types=$this->ck8bftypeModel->get_for_selectcell();
		$i=0;
		
		foreach($types as $single) {
			
			if ($Type>0 && $single["Id"]==$Type) {
				$types[$i]["selected"]="1";
				$typeName = $single["title"];
			}
			$i++;
			
		}
		
		$totalArray = array();
		$totalArray[]=array("FieldVal"=>"$date","ContentTxt"=>"$date");
		$totalArray[]=array("FieldVal"=>"$StuffId",
							"ContentTxt"=>"$StuffId"."-"."$StuffCname");
		$totalArray[]=array("FieldVal"=>"$num","ContentTxt"=>"$num");
		$totalArray[]=array("FieldVal"=>"$Type","ContentTxt"=>"$typeName");
		$totalArray[]=array("FieldVal"=>"$reason","ContentTxt"=>"");
		$totalArray[]=array("FieldVal"=>"","ContentTxt"=>"","url"=>"$url");
		$totalArray[]=array("sql"=>"$sql");
		$totalArray[]=$types;
		/*
			 @"FieldVal";
NSString *const Key_Dict_FieldVals    = @"FieldVals";
NSString *const Key_Dict_Content     = @"ContentTxt";
		*/
		return  $totalArray;
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
		
			$this->load->model('ck9stocksheetModel');
			$this->load->model('oprationlogModel');
			$insert_id = -1;
		$LogItem = '配件报废';
		$LogFunction = '插入记录';
		$Log = '';
		$StuffId = element('stuffid', $params, '-1');
		$num = element('num',  $params, '0');
		if ($StuffId<0 || $num<=0) return -1;
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
			$data = array(
				'ProposerId'=>$this->LoginNumber,
			'StuffId' => $StuffId,
			'Type'=>element('category', $params, ''),
			'Bill'=>'0',
			'DealResult'=>'',
			'OPdatetime'=>$this->DateTime,
               'Remark'  => element('reason', $params, '系统'),
			   'Qty'   => $num,
			    'Date'     => element('indate', $params, '0'),
			    'LocationId'     => element('location', $params, '0'),
				 'created'  => $this->DateTime,
				 'creator'=> $this->LoginNumber,
               'Operator' => $this->LoginNumber,
               'Estate'   => '1',
               'Locks'=>"0",
               'PLocks'=>'0'
			    );
				$this->db->trans_begin();
				$query     = $this->db->insert('ck8_bfsheet', $data); 
				$insert_id = $this->db->insert_id();
			
				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback();
					$Log.= $LogItem.$LogFunction.'失败！';
				} else {
			  	    $this->db->trans_commit();
			  			$Log.= $LogItem.$LogFunction.'成功！ID：$insert_id';
			  			
			  			$addImg = element('add_img',$params,-1);
			  			if ($addImg>0) {
				  			
				  			
				  			
				  			  	 // 上传文件配置放入config数组
			        $config['upload_path'] = '../download/ckbf';
			        $config['allowed_types'] = 'jpg';
			        $config['max_size'] = '60240';
			         $config['max_width']  = '1024000';
  $config['max_height']  = '10240000';
			        $config['file_name'] = "B".$insert_id;
			        $this->load->library('multiupload');
			        
			        $result=$this->multiupload->multi_upload('upfiles',$config);
			       
			        //取得上传文件名更新字段
		            $filenames=''; $images=array();
		            if ($result){
		               
			           foreach($result['files'] as $files){
				              $filenames.=$filenames==""?$files['file_name']:"|" . $files['file_name'];
				              $images[]=$files['full_path'];
			           }
			           
			           $this->load->library('graphics');
			           $this->graphics->create_thumb($images);
			           
			           $this->db->query("update ck8_bfsheet set Bill='1' where Id=$insert_id");
			           $Log.= "\n报废图档上传成功！";
		          } else {
			          $Log.= "\n报废图档上传失败！";
		          }
 
				  			
				  			
				  			
				  			
			  			}
			  			
			  			
			  		$OP = 'Y';
				}

		}
		
					
		$this->oprationlogModel->save_item(array('LogItem'=>$LogItem,
		                                         'LogFunction'=>$LogFunction,
		                                         'Log'=>$Log,
		                                         'OperationResult'=>$OP));
		if ($insert_id > 0){
			  						  
				  return $insert_id;
		} else {
			return -1;	
		}
		
	}
	
	
	public function edit_item($params) {
		
		$this->load->model('ck9stocksheetModel');
		$this->load->model('oprationlogModel');
		$insert_id = -1;
		$LogItem = '配件报废';
		$LogFunction = '修改记录';
		$Log = '';
		$editid = element('editid', $params, '-1');
		   $editids = explode("|", $editid);
	   if (count($editids) >1) {
		   $editid = $editids[0];
	   }
		$StuffId = element('stuffid', $params, '-1');
		$num = element('num',  $params, '0');
		if ($StuffId<0 || $num<=0) return -1;
		$checkQtyQuery = $this->ck9stocksheetModel->get_item_stuffid($StuffId);
		$oStockQty = $mStockQty = $tStockQty = 0;
		if ($checkQtyQuery->num_rows()>0) {
			$row = $checkQtyQuery->row_array();
			$oStockQty = $row['oStockQty'];
			$mStockQty = $row['mStockQty'];
			$tStockQty = $row['tStockQty'];
		}
			$OP = 'N';
			$success = 0;
		if (($oStockQty-$mStockQty)>=$num && $tStockQty>=$num) {
			$data = array(
				'ProposerId'=>$this->LoginNumber,
			'StuffId' => $StuffId,
			'Type'=>element('category', $params, ''),
			'DealResult'=>'',
			'OPdatetime'=>$this->DateTime,
			
               'Remark'  => element('reason', $params, '系统'),
			   'Qty'   => $num,
			    'Date'     => element('indate', $params, '0'),
				 'modified'  => $this->DateTime,
				 'modifier'=> $this->LoginNumber,
               'Operator' => $this->LoginNumber,
               'Estate'   => '1'
			    );
			    
			    
			    if ( element('location', $params, '') != '') {
				    $data['LocationId'] =element('location', $params, '0');
			    }
			    
			    $this->db->where('Id',$editid);
		$this->db->trans_begin();
		$query=$this->db->update('ck8_bfsheet', $data);
						         
		if ($this->db->trans_status() === FALSE){
			$Log.= $LogItem.$LogFunction.'失败！';
			    $this->db->trans_rollback();
		} else {
			$success = 1;
			$Log.= $LogItem.$LogFunction.'成功！ID：$editid';

			    $this->db->trans_commit();
		}
			   {
				   $insert_id = $editid;
			  	    
			  			$pathDelThumb = "../download/ckbf/B".$editid."_thumb.jpg";
			  			$pathDel = "../download/ckbf/B".$editid.".jpg";		  			
			  			$addImg = element('add_img',$params,-1);
			  			$delImg = element('del_img',$params,-1);
			  			if ($addImg >0 || $delImg>0) {
				  			
				  			unlink($pathDelThumb);
				  			unlink($pathDel);
				  			$this->db->query("update ck8_bfsheet set Bill='0' where Id=$insert_id");
 $Log.= "\n报废图档删除！";
			  			}
			  			if ($addImg>0) {
				  			
				  			
				  			
				  			  	 // 上传文件配置放入config数组
			        $config['upload_path'] = '../download/ckbf';
			        $config['allowed_types'] = 'jpg';
			        $config['max_size'] = '60240';
			         $config['max_width']  = '1024000';
  $config['max_height']  = '10240000';
			        $config['file_name'] = "B".$insert_id;
			        $this->load->library('multiupload');
			        
			        $result=$this->multiupload->multi_upload('upfiles',$config);
			       
			        //取得上传文件名更新字段
		            $filenames=''; $images=array();
		            if ($result){
		               
			           foreach($result['files'] as $files){
				              $filenames.=$filenames==""?$files['file_name']:"|" . $files['file_name'];
				              $images[]=$files['full_path'];
			           }
			           
			           $this->load->library('graphics');
			           $this->graphics->create_thumb($images);
			           
			           $this->db->query("update ck8_bfsheet set Bill='1' where Id=$insert_id");
			           $Log.= "\n报废图档上传成功！";
		          } else {
			          $Log.= "\n报废图档上传失败！";
		          }
 
				  			
				  			
				  			
				  			
			  			}
			  			
			  			
			  		$OP = 'Y';
				}

		}
		
					
		$this->oprationlogModel->save_item(array('LogItem'=>$LogItem,
		                                         'LogFunction'=>$LogFunction,
		                                         'Log'=>$Log,
		                                         'OperationResult'=>$OP));
		if ($success > 0){
			  						  
				  return $success;
		} else {
			return -1;	
		}
		
	}
 
}