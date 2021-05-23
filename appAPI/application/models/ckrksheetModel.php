<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
* @class CkrksheetModel  
* 仓库入库记录
* 
*/ 
class  CkrksheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
	
	
	function get_inware_amount() {


		$records = $this->get_stock_amount('all','');

        $stockAmount = $records['Amount'];
		$badge = '';
		if ($stockAmount > 0) {
			$badge = round( intval($stockAmount) /10000);
			$badge = $badge; //.'M'
		}
		return $badge;
	}
	
	function get_last_state_time($StuffId) {
		
		$lasttime = '';
		$lasttype = ''; // 0备料 －1其他出库 -2报废 1入库
		$img = '';
		//check rk 
		$sql = "
			select max(L.created) as lasttime from ck1_rksheet L where L.StuffId=?;
		";
		$query = $this->db->query($sql, $StuffId);
		if ($query->num_rows() > 0) {
			$temptime = $query->row()->lasttime;
			if ($temptime != '') {
				$lasttype = 1;
				$lasttime = $temptime;
				$img = 'stk_rk';
			}
		}
		
		if ($lasttime != '') {
			$sql = "
select (L.created) as lasttime,L.POrderId from ck5_llsheet L where L.StuffId=? and L.created>'$lasttime' order by L.created desc limit 1;";
		} else {
			$sql = "
select (L.created) as lasttime,L.POrderId from ck5_llsheet L where L.StuffId=? order by L.created desc limit 1;";
		}
		$query = $this->db->query($sql, $StuffId);
		if ($query->num_rows() > 0) {
			$temptime = $query->row()->lasttime;
			if ($temptime != '') {
				$po = $query->row()->POrderId;
				$lasttype = $po>0 ? 0 : -1;
				$lasttime = $temptime;
				$img = $po>0 ? 'stk_bl' : 'stk_other'; 
			}
			
		}
		
		if ($lasttime != '') {
			$sql = " select  max(L.created) as lasttime from ck8_bfsheet L where L.stuffid=? and L.created>'$lasttime' ; ";
		} else {
			$sql = " select  max(L.created) as lasttime from ck8_bfsheet L where L.stuffid=?   ; ";
		}
		$query = $this->db->query($sql, $StuffId);
		if ($query->num_rows() > 0) {
			$temptime = $query->row()->lasttime;
			if ($temptime != '') {
				$lasttype = -2;
				$lasttime = $temptime;
				$img = 'stk_bf';
			}
		}
		
		if ($lasttime != '') {
			$sql = " select  max(ifnull(L.created,L.Date)) as lasttime from ck7_bprk L where L.stuffid=? and ifnull(L.created,L.Date)>'$lasttime' ; ";
		} else {
			$sql = " select  max(ifnull(L.created,L.Date)) as lasttime from ck7_bprk L where L.stuffid=?   ; ";
		}
		$query = $this->db->query($sql, $StuffId);
		if ($query->num_rows() > 0) {
			$temptime = $query->row()->lasttime;
			if ($temptime != '') {
				$lasttype = 2;
				$lasttime = $temptime;
				$img = 'stk_bp';
			}
		}
		
		if ($lasttime!=''  ) {
			
			return array('time'=>$lasttime, 'type'=>$lasttype, 'img'=>$img);
		}
		return null;
		
		
	}
	
	function get_stuff_io_records($StuffId) {
		$sql = "
			select  if(Y.Id is null,'-2','0') as keyval,ifnull(concat('客人:',P.Forshort,'/PO:',M.OrderPO,'/备料:',format(L.Qty,0)), concat(L.FromFunction,':',format(L.Qty,0))) as title,
	L.created date ,R.LocationId,N.Name as oper,if(Y.Id is null,'wh_other','ibl_gray') as img,'' otherid 
	from ck5_llsheet L 
		left join ck1_rksheet R on R.Id=L.RkId
		left join yw1_ordersheet Y on Y.POrderId=L.POrderId
		left join yw1_ordermain M on M.OrderNumber=Y.OrderNumber
		left join trade_object P on P.CompanyId=M.CompanyId
		left join staffmain N on N.Number=L.Operator
		where L.StuffId=$StuffId  
	union all 
		
	select  '1' as keyval,concat('入库:',format(R.Qty,0)) as title,
	R.created date ,R.LocationId,N.Name as oper,'cg_rked' as img,'' otherid 
	from   ck1_rksheet R   
		left join staffmain N on N.Number=R.Operator
		where R.StuffId=$StuffId  
	union all
select  '-1' as keyval,concat('报废:',format(R.Qty,0)) as title,
	R.created date ,R.LocationId,N.Name as oper,'wh_op_bf' as img,R.Remark otherid 
	from   ck8_bfsheet R   
		left join staffmain N on N.Number=R.Operator
		where R.StuffId=$StuffId  
		
		union all 
select  '2' as keyval,concat('备品转入:',format(R.Qty,0)) as title,
	ifnull(R.created,R.Date) date ,R.LocationId,N.Name as oper,'stk_bp_s' as img,R.Remark otherid 
	from   ck7_bprk R   
		left join staffmain N on N.Number=R.Operator
		where R.StuffId=$StuffId   
	 
	
	order by date desc;

		";
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return null;
	}
	
	function get_location_overs($LocationId) {
		
		$sql = "
		SELECT  SUM(A.Qty) AS Qty,
		SUM(A.Counts) AS Counts,
		SUM(A.Qty*A.Price) AS Amount,

		SUM(A.OverQty) AS OverQty,
		SUM(A.OverCounts) AS OverCounts,
		SUM(A.OverQty*A.Price) AS OverAmount,

		SUM(A.OverQty1-A.OverQty) AS OverQty1,
		SUM(A.OverCounts1-A.OverCounts) AS OverCounts1,
		SUM((A.OverQty1-A.OverQty)*A.Price) AS OverAmount1
		
					FROM (
					SELECT 1 AS Counts,
							SUM(S.Qty-S.llQty) AS Qty,
							IF(D.CostPrice=0,D.Price,D.CostPrice) as Price,
					        SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,S.Qty-S.llQty,0)) AS OverQty,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,1,0)) AS OverCounts,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,S.Qty-S.llQty,0)) AS OverQty1,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,1,0)) AS OverCounts1
					        
							FROM ck1_rksheet S
							INNER JOIN ck_location L ON L.Id=S.LocationId 
							INNER JOIN  stuffdata D ON D.StuffId = S.StuffId
							WHERE   S.Qty-S.llQty>0  and (L.Id=? OR L.Mid=?) GROUP BY S.StuffId,S.LocationId   
					)A 
					
					WHERE A.Qty>0 ;
		";
		$query = $this->db->query($sql, array($LocationId,$LocationId));
		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		return null;
	}
	
	function get_stock_locationid($StockId, $getName='') {
		
		$sql = "select LocationId from ck1_rksheet where StockId='$StockId' order by id desc limit 1 ";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$LocationId = $query->row()->LocationId;
			
			if ($getName=='1') {
				return $this->get_location_name($LocationId, '');
			}
			return $LocationId;
		}
		return '';
	}
	
	function get_location_name($LocationId, $Identifier='') {
		
		
		$sql = "
			select concat(Region,Location) as lName,Id from ck_location where Id=$LocationId limit 1;
		";
		if ($Identifier!='') {
			$sql = "
			select concat(Region,Location) as lName,Id from ck_location where Identifier='$Identifier' limit 1;
		";
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->row_array();
			}
			return null;
		}
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row()->lName;
		}
		return '';
	}
		
    //已入库数量
    function get_rked_qty($stockid){
	    $sql = "SELECT SUM(Qty) AS rkQty FROM ck1_rksheet WHERE StockId=?";
	    $query = $this->db->query($sql,$stockid);
		$row = $query->first_row();
		$rkQty=$row->rkQty;
		$rkQty=$rkQty==""?0:$rkQty;
		return $rkQty;
    }
    
    //今日入库数量
    function get_today_rked($sendfloor){
	    $today = date('Y-m-d');
	    $sql = "select count(*) counts ,SUM(A.rkQty) AS rkQty from ( SELECT SUM(K.Qty) AS rkQty FROM ck1_rksheet K
	    LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
	    WHERE D.SendFloor=? AND DATE_FORMAT(K.Date,'%Y-%m-%d')='$today' group by D.StuffId) A";
	    $query = $this->db->query($sql,$sendfloor);
		$row = $query->first_row('array');
		return $row;
    }
    
    //获取当日入库数量(按入库记录统计)
     function get_rk_daycount($WarehouseId,$date='')
     {
	    $date=$date==''?$this->Date:$date;
	   
	    $sql = "SELECT COUNT(*) AS Counts,IFNULL(SUM(A.Qty),0) AS Qty  
	          FROM (
	           SELECT B.StuffId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck1_rksheet B 
               INNER JOIN ck_location L ON L.Id=B.LocationId   
	           WHERE   L.WarehouseId =?  AND B.Date = ? GROUP BY B.StuffId)A ";
	     $query=$this->db->query($sql,array($WarehouseId,$date));
	     return $query->first_row('array');
  } 
    
    //生产单位当天入库数
    function get_workshop_rkqty($wsid,$date='') {
	    $date = $date==''?$this->Date:$date;
	    $sql = "
				   SELECT SUM(R.Qty) AS rkQty FROM 
				   yw1_scsheet S
				  left join ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId
				  where S.WorkShopId=? and R.Date=?";
		$query = $this->db->query($sql,array($wsid,$date));
		$row = $query->first_row();
		$rkQty=$row->rkQty;
		$rkQty=$rkQty==""?0:$rkQty;
		return $rkQty;
    }
    
	//工单的已入库数量
	function get_scorder_rkqty($sPOrderId,$StockId) {
	
		$sql = "SELECT SUM(Qty) AS rkQty FROM  ck1_rksheet  WHERE  sPOrderId=? AND StockId=?";
		
		$query = $this->db->query($sql,array($sPOrderId,$StockId));
		$row = $query->first_row();
		$rkQty=$row->rkQty;
		$rkQty=$rkQty==""?0:$rkQty;
		
		return $rkQty;
	}
	
	//查找配件已存在的库存位置
	function get_stuff_location($StuffId)
	{
		$sql = "SELECT SUM(S.Qty-S.llQty) AS rkQty,S.LocationId,L.Identifier  
			FROM  ck1_rksheet S 
			LEFT JOIN ck_location L ON L.Id=LocationId 
			WHERE  S.StuffId=? AND S.Qty>S.llQty GROUP BY S.LocationId";
		$query = $this->db->query($sql,array($StuffId));
		return $query->result_array();
	}
	
	//获取配件库存位置
	function get_stuff_locations($StuffId)
	{
		$sql = "SELECT GROUP_CONCAT(A.Region,A.Location) AS Locations 
		    FROM (
                SELECT IF(L.Mid>0,CL.Region,L.Region) AS Region ,IF(L.Mid>0,CL.Location,L.Location) AS  Location
				FROM  ck1_rksheet S 
				LEFT JOIN ck_location L ON L.Id=S.LocationId 
				LEFT JOIN ck_location CL ON CL.Id=L.Mid   
				WHERE  S.StuffId='$StuffId' AND S.Qty>S.llQty GROUP BY S.LocationId
			)A"; 
		$query = $this->db->query($sql);
		$rows  = $query->first_row('array');
		
		return  $rows['Locations']==''?'':str_replace(',', ' ', $rows['Locations']);
	}
	//获取工单配件未领料确认的位置
	function get_stuff_picklocation($sPOrderId,$StockId)
	{
	    $locations=''; $workadd='1';
	    $dataArray = array();
		$sql = "SELECT S.Id,IF(L.Mid>0,CONCAT(CL.Region,CL.Location),CONCAT(L.Region,L.Location)) AS Location,SUM(A.Qty) AS Qty,
		        U.Name AS UnitName,U.Decimals,L.WorkAdd   
				FROM  ck5_llsheet A 
                INNER JOIN ck1_rksheet S ON S.Id=A.RkId 
				INNER JOIN Stuffdata D ON D.StuffId=S.StuffId
				INNER JOIN stuffunit U ON U.Id=D.Unit 
				LEFT JOIN ck_location L ON L.Id=S.LocationId 
				LEFT JOIN ck_location CL ON CL.Id=L.Mid  
				WHERE  A.sPOrderId='$sPOrderId' AND A.StockId='$StockId' AND A.Estate>0  GROUP BY S.LocationId ORDER BY Id";
		 		
		 $query=$this->db->query($sql);
		 foreach($query->result_array() as $row){
		   $pickQty=$row['Qty'];
		    $locations.=$locations==''?$row['Location']:' ' . $row['Location'];
		     $workadd= $row['WorkAdd'];
		    $dataArray[]=array(
                   'tag'     => 'location',
                   'Id'      => $row['Id'],
                   'location'=> $row['Location']==''?'未设置':$row['Location'],
                   'qty'     =>number_format($pickQty,$row['Decimals']),
                   'unit'    => $row['UnitName'],
                   'isSelect'=> '0'
                 );
		 }
		 return array('location'=>$locations,'workadd'=>$workadd,'data'=>$dataArray);
	}
	
	//获取配件库存位置及数量
	function get_stuff_locationqty($StuffId,$llQty=0)
	{
	    $locations=''; $workadd='1';
	    $dataArray = array();
		$sql = "SELECT S.Id,group_concat(S.Id) as Ids,IF(L.Mid>0,CONCAT(CL.Region,CL.Location),CONCAT(L.Region,L.Location)) AS Location,SUM(S.Qty-S.llQty) AS Qty,S.LocationId,
		        U.Name AS UnitName,U.Decimals,L.WorkAdd   
				FROM  ck1_rksheet S
				INNER JOIN Stuffdata D ON D.StuffId=S.StuffId
				INNER JOIN stuffunit U ON U.Id=D.Unit 
				LEFT JOIN ck_location L ON L.Id=S.LocationId 
                LEFT JOIN ck_location CL ON CL.Id=L.Mid 
				WHERE  S.StuffId='$StuffId' AND S.Qty>S.llQty GROUP BY S.LocationId ORDER BY Id";
		 		
		 $query=$this->db->query($sql);
		 $enoughQty = 0;
		 foreach($query->result_array() as $row){
		    if ($llQty>0 &&  $enoughQty>=$llQty) break;
		    $enoughQty+=$row['Qty'];
		    $pickQty = ($llQty>0 && $enoughQty>$llQty)?$row['Qty']-($enoughQty-$llQty):$row['Qty'];
		    $locations.=$locations==''?$row['Location']:' ' . $row['Location'];
		     $workadd= $row['WorkAdd'];
		    $dataArray[]=array(
                   'tag'     => 'location',
                   'Id'      => $row['Id'],
                   'LocationId'      => $row['LocationId'],
                   'Ids'      => $row['Ids'],
                   'location'=> $row['Location']==''?'未设置':$row['Location'],
                   'qty'     =>number_format($pickQty,$row['Decimals']),
                   'unit'    => $row['UnitName'],
                   'isSelect'=> '0'
                 );
		 }
		 return array('location'=>$locations,'workadd'=>$workadd,'data'=>$dataArray);
	}
	
	//查找配件的数量（按区位）
	function get_region_stuffqty($StuffId,$Region)
	{
	    $rkQty=0;
	   
	    if ($Region=='') {
		    $sql = "SELECT SUM(S.Qty-S.llQty) AS rkQty  
				 FROM  ck1_rksheet S 
				 LEFT JOIN ck_location L ON L.Id=LocationId 
				 WHERE  S.StuffId=? AND S.Qty>S.llQty  ";
		$query = $this->db->query($sql,array($StuffId));
	    } else {
		    $sql = "SELECT SUM(S.Qty-S.llQty) AS rkQty  
				 FROM  ck1_rksheet S 
				 LEFT JOIN ck_location L ON L.Id=LocationId 
				 WHERE  S.StuffId=? AND S.Qty>S.llQty AND L.Region=?";
		$query = $this->db->query($sql,array($StuffId,$Region));
	    }
		
		$rows  = $query->first_row('array');
		return  $rows['rkQty']==''?'':$rows['rkQty'];
	}
	
	//按月统计实物库存数量
	function get_stock_month_amount($Month,$warehouseId='')
	{
	      $SystemOpened=$this->config->item('system_opened');

		  switch($warehouseId){
			   case  'all':
			   default:
			       $sql = "SELECT COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount
					FROM (
								SELECT  SUM(S.Qty) AS Qty,
								IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								FROM (
										SELECT U.StuffId,SUM(U.Qty) AS Qty,MAX(U.Date) AS Date  
										FROM (
										     SELECT  S.StuffId,SUM(S.Qty) AS Qty,MAX(S.Date) AS Date  
										     FROM ck1_rksheet S  WHERE DATE_FORMAT(S.Date,'%Y-%m')='$Month' GROUP BY S.StuffId
										UNION ALL
										     SELECT  L.StuffId,IFNULL(SUM(L.Qty),0)*-1 AS Qty,'$SystemOpened' AS Date 
										     FROM ck5_llsheet L  WHERE DATE_FORMAT(L.Date,'%Y-%m')='$Month' GROUP BY L.StuffId
										)U  GROUP BY U.StuffId 
								)S 
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10 
								WHERE S.Qty>0  GROUP BY S.StuffId
					)A"; 
		   break;
	  }
	 $query = $this->db->query($sql);
	 return $query->first_row('array');
}
	
	
	//统计实物库存数量
	function get_stock_amount($warehouseId,$SendFloor='',$noTaxSign=0)
	{
			switch($warehouseId){
			   case  'all':
			        if ($noTaxSign==1){
				        $sql = "SELECT COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount
			                    FROM (
					                    SELECT SUM(S.Qty-S.llQty) AS Qty,
					                           IF(T.mainType=getSysConfig(103),IFNULL(E.costPrice,D.costPrice),D.Price/(1+IFNULL(X.Value,0.17)))*IFNULL(C.Rate,1)  AS Price  
										 FROM  ck1_rksheet S 
						                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                 INNER JOIN stuffmaintype M ON M.Id=T.mainType 
						                 INNER JOIN bps A ON A.StuffId=S.StuffId 
		                                 LEFT  JOIN stuffcostprice E ON E.StuffId=S.StuffId 
						                 LEFT  JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
						                 LEFT  JOIN currencydata C ON B.Currency=C.Id
		                                 LEFT JOIN providersheet V ON V.CompanyId = A.CompanyId
				                         LEFT JOIN provider_addtax X ON X.Id = V.AddValueTax 
		                                 LEFT  JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
										 WHERE  S.llSign>0  AND M.blSign=1 AND P.StuffId IS NULL   GROUP BY S.StuffId
							     )A";
			        }else{
				          $sql = "SELECT COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount
				               FROM (
				                    SELECT  SUM(S.Qty-S.llQty) AS Qty,
				                                  IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
									 FROM  ck1_rksheet S 
					                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
					                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
					                 INNER JOIN stuffmaintype M ON M.Id=T.mainType 
					                 INNER JOIN bps A ON A.StuffId=S.StuffId 
					                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
					                 LEFT JOIN currencydata C ON B.Currency=C.Id
					                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
									 WHERE  S.llSign>0  AND M.blSign=1 AND P.StuffId IS NULL   GROUP BY S.StuffId
						     )A ";
					   }
			       break;
				default:
				   if ($SendFloor==''){
				      $sql = "SELECT COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount
			               FROM (
			                    SELECT  SUM(S.Qty-S.llQty) AS Qty,
			                                  IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                  INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								 WHERE  S.llSign>0  AND M.blSign=1  AND L.WarehouseId='$warehouseId'  AND P.StuffId IS NULL   GROUP BY S.StuffId 
						     )A ";
				    }else{
				       $sql = "SELECT COUNT(*) AS Counts,SUM(B.Qty) AS Qty,SUM(B.Amount) AS Amount 
				         FROM (
				               SELECT A.StuffId,SUM(IFNULL(A.Qty,0)) AS Qty,SUM(IFNULL(A.Qty*A.Price,0)) AS Amount 
				               FROM( 
				                SELECT  S.StuffId,SUM(S.Qty-S.llQty) AS Qty,
				                IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                   LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								 WHERE  S.llSign>0  AND M.blSign=1  AND L.WarehouseId='$warehouseId'  AND P.StuffId IS NULL   GROUP BY S.StuffId 
				    UNION ALL
				                SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,
				                 IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                   LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								 WHERE  S.llSign>0  AND M.blSign=1  AND S.LocationId=0 AND D.SendFloor IN($SendFloor)  AND P.StuffId IS NULL   GROUP BY S.StuffId 
								)A  GROUP BY A.StuffId 
						 )B";
					 }
				     break;
			}
			
			$query = $this->db->query($sql);
		    return $query->first_row('array');
	}
	
	function get_order_amount($warehouseId,$SendFloor='')
	{
			$thisDateTime = $this->DateTime;
			$lastMonth = date("Y-m-d",strtotime("$thisDateTime -12 month"));
			switch($warehouseId){
			   case  'all':
			          $sql = "SELECT SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)) AS OrderQty,
			                                    SUM(A.Counts) AS Counts,SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)*A.Price) AS Amount,
												SUM(A.M1Qty) AS M1Qty,SUM(A.OverCounts) AS OverCounts,SUM(A.M1Qty*A.Price) AS M1Amount,
												SUM(A.M3Qty) AS M3Qty,SUM(A.OverCounts1) AS OverCounts1,SUM((A.M3Qty)*A.Price) AS M3Amount
								FROM(
								       SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.Counts) AS Counts,SUM(S.StockQty) AS StockQty,
											       SUM(S.OverCounts) AS OverCounts,SUM(M1Qty) AS M1Qty,
											       SUM(S.OverCounts1) AS OverCounts1,SUM(M3Qty) AS M3Qty,
											       IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
										FROM (
												   SELECT A.StuffId,0 AS OrderQty,1 AS Counts,SUM(A.Qty) AS StockQty,
														      SUM(IF (A.rkMonth>=1,1,0)) AS OverCounts,SUM(IF (A.rkMonth>=1,A.Qty,0)) AS M1Qty,
														      SUM(IF (A.rkMonth>3,1,0)) AS OverCounts1,SUM(IF (A.rkMonth>3,A.Qty,0)) AS M3Qty
												 FROM(
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now())  AS rkMonth 
												    FROM ck1_rksheet S  
												    WHERE  S.llSign>0 GROUP BY S.StuffId,S.Date
												)A WHERE A.Qty>0 GROUP BY A.StuffId
										UNION ALL 
											SELECT A.StuffId,(A.OrderQty-A.llQty) AS OrderQty,0 AS Counts,0 AS StockQty,
											      0 as OverCounts,0 AS M1Qty,0 AS OverCounts1,0 AS M3Qty 
											FROM(
											  SELECT G.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty
											    FROM (SELECT StuffId FROM ck1_rksheet WHERE llSign>0 GROUP BY StuffId) S
											    LEFT JOIN  cg1_stocksheet G ON S.StuffId=G.StuffId  
											    LEFT JOIN  ck5_llsheet L ON L.StockId=G.StockId 
											    WHERE G.cgSign=0 GROUP BY G.StuffId  
											)A GROUP BY A.StuffId 
								)S
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								WHERE  M.blSign=1  AND P.StuffId IS NULL
								GROUP BY S.StuffId  
					   )A";
			   /*
			          $sql = "SELECT SUM(IFNULL(A.OrderQty,0)) AS OrderQty,SUM(IFNULL(A.OrderQty*A.Price,0)) AS Amount,
			                                    SUM(IFNULL(A.M1Qty,0)) AS M1Qty,SUM(IFNULL(A.M1Qty*A.Price,0)) AS M1Amount,
			                                    SUM(IFNULL(A.M3Qty,0)) AS M3Qty,SUM(IFNULL(A.M3Qty*A.Price,0)) AS M3Amount  
			          FROM (
			                  SELECT SUM(IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty)) AS OrderQty,
			                                 SUM(IF (S.xdMonth>=1,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M1Qty,
			                                 SUM(IF (S.xdMonth>3,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M3Qty,
                                             IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
						FROM (
						      SELECT A.StuffId,A.Qty,SUM(A.OrderQty-A.llQty) AS OrderQty,A.xdMonth 
                                   FROM (
									SELECT S.StuffId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,TIMESTAMPDIFF(MONTH,(S.RKDate),Now())  AS xdMonth   
									FROM( 
										   SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,
										   max(S.Date) AS RKDate 
										    FROM ck1_rksheet S  WHERE  S.llSign>0    GROUP BY S.StuffId
									)S 
									INNER JOIN  cg1_stocksheet G ON G.StuffId=S.StuffId AND G.cgSign=0
									LEFT JOIN   ck5_llsheet L ON L.StockId=G.StockId  
									WHERE  1  GROUP BY G.StockId 
								 )A GROUP BY A.StuffId 	
						)S 
						INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						INNER JOIN stuffmaintype M ON M.Id=T.mainType 
						INNER JOIN bps A ON A.StuffId=S.StuffId 
						LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
						LEFT JOIN currencydata C ON B.Currency=C.Id
						LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
						WHERE S.OrderQty>0  AND M.blSign=1  AND P.StuffId IS NULL   GROUP BY S.StuffId
						)A";
						
						
						$sql = "SELECT  SUM(A.Qty) AS OrderQty,
		SUM(A.Counts) AS Counts,
		SUM(A.Qty*A.Price) AS Amount,

		SUM(A.OverQty) AS M3Qty,
		SUM(A.OverCounts) AS OverCounts,
		SUM(A.OverQty*A.Price) AS M3Amount,

		SUM(A.OverQty1) AS M1Qty,
		SUM(A.OverCounts1) AS OverCounts1,
		SUM((A.OverQty1)*A.Price) AS M1Amount
		
					FROM (
					SELECT 1 AS Counts,
							SUM(S.Qty-S.llQty) AS Qty,
							IF(D.CostPrice=0,D.Price,D.CostPrice) as Price,
					        SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,S.Qty-S.llQty,0)) AS OverQty,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,1,0)) AS OverCounts,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,S.Qty-S.llQty,0)) AS OverQty1,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,1,0)) AS OverCounts1
					        
							FROM ck1_rksheet S
							-- INNER JOIN ck_location L ON L.Id=S.LocationId 
							INNER JOIN  stuffdata D ON D.StuffId = S.StuffId
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						INNER JOIN stuffmaintype M ON M.Id=T.mainType 
							WHERE   S.Qty-S.llQty>0   AND M.blSign=1  GROUP BY S.StuffId   
					)A 
					WHERE A.Qty>0 ;";
					*/
			       break;
				default:
				  if ($SendFloor==''){
				       $sql = "SELECT SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)) AS OrderQty,
			                                    SUM(A.Counts) AS Counts,SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)*A.Price) AS Amount,
												SUM(A.M1Qty) AS M1Qty,SUM(A.OverCounts) AS OverCounts,SUM(A.M1Qty*A.Price) AS M1Amount,
												SUM(A.M3Qty) AS M3Qty,SUM(A.OverCounts1) AS OverCounts1,SUM((A.M3Qty)*A.Price) AS M3Amount
								FROM(
								       SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.Counts) AS Counts,SUM(S.StockQty) AS StockQty,
											       SUM(S.OverCounts) AS OverCounts,SUM(M1Qty) AS M1Qty,
											       SUM(S.OverCounts1) AS OverCounts1,SUM(M3Qty) AS M3Qty,
											       IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
										FROM (
												   SELECT A.StuffId,0 AS OrderQty,1 AS Counts,SUM(A.Qty) AS StockQty,
														      SUM(IF (A.rkMonth>=1,1,0)) AS OverCounts,SUM(IF (A.rkMonth>=1,A.Qty,0)) AS M1Qty,
														      SUM(IF (A.rkMonth>3,1,0)) AS OverCounts1,SUM(IF (A.rkMonth>3,A.Qty,0)) AS M3Qty
												 FROM(
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now())  AS rkMonth 
												    FROM ck1_rksheet S  
                                                    INNER JOIN ck_location L ON L.Id=S.LocationId
												    WHERE  S.llSign>0 AND L.WarehouseId='$warehouseId' GROUP BY S.StuffId,S.Date
												)A WHERE A.Qty>0 GROUP BY A.StuffId
										UNION ALL 
											SELECT A.StuffId,(A.OrderQty-A.llQty) AS OrderQty,0 AS Counts,0 AS StockQty,
											      0 as OverCounts,0 AS M1Qty,0 AS OverCounts1,0 AS M3Qty 
											FROM(
											  SELECT G.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty
											    FROM (SELECT S.StuffId FROM ck1_rksheet S
											          INNER JOIN ck_location L ON L.Id=S.LocationId
                                                      WHERE S.llSign>0 AND L.WarehouseId='$warehouseId' GROUP BY S.StuffId) S
											    LEFT JOIN  cg1_stocksheet G ON S.StuffId=G.StuffId  
											    LEFT JOIN  ck5_llsheet L ON L.StockId=G.StockId 
											    WHERE G.cgSign=0 GROUP BY G.StuffId  
											)A GROUP BY A.StuffId 
								)S
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								WHERE  M.blSign=1  AND P.StuffId IS NULL
								GROUP BY S.StuffId 
					   )A";
				         /*
					     $sql = "SELECT SUM(IFNULL(A.OrderQty,0)) AS OrderQty,SUM(IFNULL(A.OrderQty*A.Price,0)) AS Amount,
					                              SUM(IFNULL(A.M1Qty,0)) AS M1Qty,SUM(IFNULL(A.M1Qty*A.Price,0)) AS M1Amount,
			                                      SUM(IFNULL(A.M3Qty,0)) AS M3Qty,SUM(IFNULL(A.M3Qty*A.Price,0)) AS M3Amount     
			               FROM (
			                     SELECT SUM(IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty)) AS OrderQty,
			                                 SUM(IF (S.xdMonth>=1,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M1Qty,
			                                 SUM(IF (S.xdMonth>3,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M3Qty,
                                             IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
							FROM (
                                     SELECT A.StuffId,A.Qty,SUM(A.OrderQty-A.llQty) AS OrderQty,A.xdMonth  
                                     FROM (
										SELECT S.StuffId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,TIMESTAMPDIFF(MONTH,(S.RKDate),Now())  AS xdMonth   
										FROM( 
											   SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,max(S.Date) AS RKDate  FROM ck1_rksheet S INNER JOIN ck_location L ON L.Id=S.LocationId 
												         WHERE  S.llSign>0  AND L.WarehouseId='$warehouseId'  GROUP BY S.StuffId
										)S 
										INNER JOIN  cg1_stocksheet G ON G.StuffId=S.StuffId AND G.cgSign=0
										LEFT JOIN   ck5_llsheet L ON L.StockId=G.StockId  
										WHERE  G.ywOrderDTime>='$lastMonth'  GROUP BY G.StockId 
								 )A GROUP BY A.StuffId 		
							)S 
							INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
							INNER JOIN stuffmaintype M ON M.Id=T.mainType 
							INNER JOIN bps A ON A.StuffId=S.StuffId 
							LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
							LEFT JOIN currencydata C ON B.Currency=C.Id
							LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
							WHERE S.OrderQty>0    AND M.blSign=1  AND P.StuffId IS NULL   GROUP BY S.StuffId)A ";
							
							$sql = "SELECT  SUM(A.Qty) AS OrderQty,
		SUM(A.Counts) AS Counts,
		SUM(A.Qty*A.Price) AS Amount,

		SUM(A.OverQty) AS M3Qty,
		SUM(A.OverCounts) AS OverCounts,
		SUM(A.OverQty*A.Price) AS M3Amount,

		SUM(A.OverQty1) AS M1Qty,
		SUM(A.OverCounts1) AS OverCounts1,
		SUM((A.OverQty1)*A.Price) AS M1Amount
		
					FROM (
					SELECT 1 AS Counts,
							SUM(S.Qty-S.llQty) AS Qty,
							IF(D.CostPrice=0,D.Price,D.CostPrice) as Price,
					        SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,S.Qty-S.llQty,0)) AS OverQty,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,1,0)) AS OverCounts,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,S.Qty-S.llQty,0)) AS OverQty1,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,1,0)) AS OverCounts1
					        
							FROM ck1_rksheet S
							-- INNER JOIN ck_location L ON L.Id=S.LocationId 
							INNER JOIN  stuffdata D ON D.StuffId = S.StuffId
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						INNER JOIN stuffmaintype M ON M.Id=T.mainType 
							WHERE   S.Qty-S.llQty>0   AND M.blSign=1  GROUP BY S.StuffId   
					)A 
					
					WHERE A.Qty>0 ;";
                    */
					  }else{
					      $sql = "SELECT SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)) AS OrderQty,
			                                    SUM(A.Counts) AS Counts,SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)*A.Price) AS Amount,
												SUM(A.M1Qty) AS M1Qty,SUM(A.OverCounts) AS OverCounts,SUM(A.M1Qty*A.Price) AS M1Amount,
												SUM(A.M3Qty) AS M3Qty,SUM(A.OverCounts1) AS OverCounts1,SUM((A.M3Qty)*A.Price) AS M3Amount
								FROM(
								       SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.Counts) AS Counts,SUM(S.StockQty) AS StockQty,
											       SUM(S.OverCounts) AS OverCounts,SUM(M1Qty) AS M1Qty,
											       SUM(S.OverCounts1) AS OverCounts1,SUM(M3Qty) AS M3Qty,
											       IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
										FROM (
												   SELECT A.StuffId,0 AS OrderQty,1 AS Counts,SUM(A.Qty) AS StockQty,
														      SUM(IF (A.rkMonth>=1,1,0)) AS OverCounts,SUM(IF (A.rkMonth>=1,A.Qty,0)) AS M1Qty,
														      SUM(IF (A.rkMonth>3,1,0)) AS OverCounts1,SUM(IF (A.rkMonth>3,A.Qty,0)) AS M3Qty
												 FROM(
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now())  AS rkMonth 
												    FROM ck1_rksheet S  
                                                    INNER JOIN ck_location L ON L.Id=S.LocationId
												    WHERE  S.llSign>0 AND L.WarehouseId='$warehouseId' GROUP BY S.StuffId,S.Date
												 UNION ALL
												     SELECT  S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now()) AS rkMonth  
												     FROM ck1_rksheet S 
												     INNER JOIN stuffdata D ON D.StuffId=S.StuffId  
												     WHERE  S.llSign>0 AND S.LocationId=0  AND D.SendFloor IN($SendFloor)  GROUP BY S.StuffId,S.Date 
												)A WHERE A.Qty>0 GROUP BY A.StuffId
										UNION ALL 
											SELECT A.StuffId,(A.OrderQty-A.llQty) AS OrderQty,0 AS Counts,0 AS StockQty,
											      0 as OverCounts,0 AS M1Qty,0 AS OverCounts1,0 AS M3Qty 
											FROM(
											  SELECT G.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty
											    FROM (
											       SELECT A.StuffId FROM (
											          SELECT S.StuffId FROM ck1_rksheet S
											          INNER JOIN ck_location L ON L.Id=S.LocationId
                                                      WHERE S.llSign>0 AND L.WarehouseId='$warehouseId' GROUP BY S.StuffId
                                                   UNION ALL
                                                      SELECT  S.StuffId  FROM ck1_rksheet S 
												      INNER JOIN stuffdata D ON D.StuffId=S.StuffId  
												      WHERE  S.llSign>0 AND S.LocationId=0  AND D.SendFloor IN($SendFloor)  GROUP BY S.StuffId
												     )A) S
											    LEFT JOIN  cg1_stocksheet G ON S.StuffId=G.StuffId  
											    LEFT JOIN  ck5_llsheet L ON L.StockId=G.StockId 
											    WHERE G.cgSign=0 GROUP BY G.StuffId  
											)A GROUP BY A.StuffId 
								)S
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								WHERE  M.blSign=1  AND P.StuffId IS NULL
								GROUP BY S.StuffId  
					   )A";
					      /*
						   $sql = "SELECT SUM(IFNULL(A.OrderQty,0)) AS OrderQty,SUM(IFNULL(A.OrderQty*A.Price,0)) AS Amount,
					                              SUM(IFNULL(A.M1Qty,0)) AS M1Qty,SUM(IFNULL(A.M1Qty*A.Price,0)) AS M1Amount,
			                                      SUM(IFNULL(A.M3Qty,0)) AS M3Qty,SUM(IFNULL(A.M3Qty*A.Price,0)) AS M3Amount    
			               FROM (
			                      SELECT SUM(IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty)) AS OrderQty,
			                                 SUM(IF (S.xdMonth>=1,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M1Qty,
			                                 SUM(IF (S.xdMonth>3,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M3Qty,
                                             IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
							   FROM (
                                     SELECT A.StuffId,A.Qty,SUM(A.OrderQty-A.llQty) AS OrderQty,A.xdMonth  
                                     FROM (
										SELECT S.StuffId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,TIMESTAMPDIFF(MONTH,(S.RKDate),Now())  AS xdMonth   
										FROM( 
											   SELECT A.StuffId,SUM(A.Qty) AS Qty,(A.RKDate) AS RKDate 
												FROM  (
												     SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,max(S.Date) AS RKDate  FROM ck1_rksheet S 
												     INNER JOIN ck_location L ON L.Id=S.LocationId 
												     WHERE  S.llSign>0  AND L.WarehouseId='$warehouseId'  GROUP BY S.StuffId
												UNION ALL
												     SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,max(S.Date) AS RKDate  FROM ck1_rksheet S 
												     INNER JOIN stuffdata D ON D.StuffId=S.StuffId  
												     WHERE  S.llSign>0 AND S.LocationId=0  AND D.SendFloor IN($SendFloor)  GROUP BY S.StuffId 
												  ) A GROUP BY A.StuffId
										)S 
										INNER JOIN  cg1_stocksheet G ON G.StuffId=S.StuffId AND G.cgSign=0
										LEFT JOIN   ck5_llsheet L ON L.StockId=G.StockId  
										WHERE  G.ywOrderDTime>='$lastMonth'  GROUP BY G.StockId 
								  )A GROUP BY A.StuffId 	
							)S 
							INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
							INNER JOIN stuffmaintype M ON M.Id=T.mainType 
							INNER JOIN bps A ON A.StuffId=S.StuffId 
							LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
							LEFT JOIN currencydata C ON B.Currency=C.Id
							LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
							WHERE S.OrderQty>0   AND M.blSign=1  AND P.StuffId IS NULL   GROUP BY S.StuffId)A";
							
							$sql = "SELECT  SUM(A.Qty) AS OrderQty,
		SUM(A.Counts) AS Counts,
		SUM(A.Qty*A.Price) AS Amount,

		SUM(A.OverQty) AS M3Qty,
		SUM(A.OverCounts) AS OverCounts,
		SUM(A.OverQty*A.Price) AS M3Amount,

		SUM(A.OverQty1) AS M1Qty,
		SUM(A.OverCounts1) AS OverCounts1,
		SUM((A.OverQty1)*A.Price) AS M1Amount
		
					FROM (
					SELECT 1 AS Counts,
							SUM(S.Qty-S.llQty) AS Qty,
							IF(D.CostPrice=0,D.Price,D.CostPrice) as Price,
					        SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,S.Qty-S.llQty,0)) AS OverQty,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>3,1,0)) AS OverCounts,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,S.Qty-S.llQty,0)) AS OverQty1,
							SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=1,1,0)) AS OverCounts1
					        
							FROM ck1_rksheet S
							-- INNER JOIN ck_location L ON L.Id=S.LocationId 
							INNER JOIN  stuffdata D ON D.StuffId = S.StuffId
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						INNER JOIN stuffmaintype M ON M.Id=T.mainType 
							WHERE   S.Qty-S.llQty>0  AND D.SendFloor IN($SendFloor)  AND M.blSign=1  GROUP BY S.StuffId   
					)A 
					
					WHERE A.Qty>0 ;";
                  */
					}
				     break;
			}
			$query = $this->db->query($sql);
		    return $query->first_row('array');
	}
	
	//获取配件分类订单库存
	function get_order_stufftype_amount($warehouseId,$SendFloor='',$TypeId='',$CompanyId='')
	{
		    $thisDateTime = $this->DateTime;
			$lastMonth = date("Y-m-d",strtotime("$thisDateTime -12 month"));
			$SearchRows = $TypeId==''?'':" AND T.TypeId='$TypeId' "; 
			$SearchRows.= $CompanyId==''?'':" AND B.CompanyId='$CompanyId' "; 
			switch($warehouseId){
			   case  'all':
			         $sql = "SELECT SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)) AS OrderQty,
			                                    SUM(A.Counts) AS Counts,SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)*A.Price) AS Amount,
												SUM(A.M1Qty) AS M1Qty,SUM(A.OverCounts) AS OverCounts,SUM(A.M1Qty*A.Price) AS M1Amount,
												SUM(A.M3Qty) AS M3Qty,SUM(A.OverCounts1) AS OverCounts1,SUM((A.M3Qty)*A.Price) AS M3Amount
								FROM(
								       SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.Counts) AS Counts,SUM(S.StockQty) AS StockQty,
											       SUM(S.OverCounts) AS OverCounts,SUM(M1Qty) AS M1Qty,
											       SUM(S.OverCounts1) AS OverCounts1,SUM(M3Qty) AS M3Qty,
											       IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
										FROM (
												   SELECT A.StuffId,0 AS OrderQty,1 AS Counts,SUM(A.Qty) AS StockQty,
														      SUM(IF (A.rkMonth>=1,1,0)) AS OverCounts,SUM(IF (A.rkMonth>=1,A.Qty,0)) AS M1Qty,
														      SUM(IF (A.rkMonth>3,1,0)) AS OverCounts1,SUM(IF (A.rkMonth>3,A.Qty,0)) AS M3Qty
												 FROM(
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now())  AS rkMonth 
												    FROM ck1_rksheet S  
                                                    INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                            INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                            INNER JOIN bps B ON B.StuffId=D.StuffId 
												    WHERE  S.llSign>0 $SearchRows GROUP BY S.StuffId,S.Date
												)A WHERE A.Qty>0 GROUP BY A.StuffId
										UNION ALL 
											SELECT A.StuffId,(A.OrderQty-A.llQty) AS OrderQty,0 AS Counts,0 AS StockQty,
											      0 as OverCounts,0 AS M1Qty,0 AS OverCounts1,0 AS M3Qty 
											FROM(
											  SELECT G.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty
											    FROM (SELECT S.StuffId FROM ck1_rksheet S 
                                                      INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                              INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                              INNER JOIN bps B ON B.StuffId=D.StuffId 
                                                       WHERE S.llSign>0 $SearchRows GROUP BY S.StuffId) S
											    LEFT JOIN  cg1_stocksheet G ON S.StuffId=G.StuffId  
											    LEFT JOIN  ck5_llsheet L ON L.StockId=G.StockId 
											    WHERE G.cgSign=0 GROUP BY G.StuffId  
											)A GROUP BY A.StuffId 
								)S
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								WHERE  M.blSign=1  AND P.StuffId IS NULL
								GROUP BY S.StuffId  
					   )A";
			        /*
			          $sql = "SELECT SUM(IFNULL(A.OrderQty,0)) AS OrderQty,SUM(IFNULL(A.OrderQty*A.Price,0)) AS Amount,COUNT(*) AS Counts,
					                              SUM(IFNULL(A.M1Qty,0)) AS M1Qty,SUM(IFNULL(A.M1Qty*A.Price,0)) AS M1Amount,
			                                      SUM(IFNULL(A.M3Qty,0)) AS M3Qty,SUM(IFNULL(A.M3Qty*A.Price,0)) AS M3Amount   
			                  FROM (
			                       SELECT SUM(IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty)) AS OrderQty,
			                                 SUM(IF (S.xdMonth>1,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M1Qty,
			                                 SUM(IF (S.xdMonth>3,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M3Qty,
                                             IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
							    FROM (
                                     SELECT A.StuffId,A.Qty,SUM(A.OrderQty-A.llQty) AS OrderQty,A.xdMonth  
                                     FROM (
									SELECT S.StuffId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,TIMESTAMPDIFF(MONTH,MAX(G.ywOrderDTime),Now())  AS xdMonth   
									FROM( 
										    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty FROM ck1_rksheet S  
										    INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                    INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                    INNER JOIN bps B ON B.StuffId=D.StuffId 
										   WHERE  S.llSign>0  $SearchRows  GROUP BY S.StuffId
									)S 
									INNER JOIN  cg1_stocksheet G ON G.StuffId=S.StuffId AND G.cgSign=0
									LEFT JOIN   ck5_llsheet L ON L.StockId=G.StockId  
									WHERE  G.ywOrderDTime>='$lastMonth'  GROUP BY G.StockId 
							    )A GROUP BY A.StuffId
							)S 
							INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
							INNER JOIN stuffmaintype M ON M.Id=T.mainType 
							INNER JOIN bps A ON A.StuffId=S.StuffId 
							LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
							LEFT JOIN currencydata C ON B.Currency=C.Id
							LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
							WHERE S.OrderQty>0    AND M.blSign=1  AND P.StuffId IS NULL   GROUP BY S.StuffId)A ";
							*/
			       break;
				default:
				  if ($SendFloor==''){
				         $sql = "SELECT SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)) AS OrderQty,
			                                    SUM(A.Counts) AS Counts,SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)*A.Price) AS Amount,
												SUM(A.M1Qty) AS M1Qty,SUM(A.OverCounts) AS OverCounts,SUM(A.M1Qty*A.Price) AS M1Amount,
												SUM(A.M3Qty) AS M3Qty,SUM(A.OverCounts1) AS OverCounts1,SUM((A.M3Qty)*A.Price) AS M3Amount
								FROM(
								       SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.Counts) AS Counts,SUM(S.StockQty) AS StockQty,
											       SUM(S.OverCounts) AS OverCounts,SUM(M1Qty) AS M1Qty,
											       SUM(S.OverCounts1) AS OverCounts1,SUM(M3Qty) AS M3Qty,
											       IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
										FROM (
												   SELECT A.StuffId,0 AS OrderQty,1 AS Counts,SUM(A.Qty) AS StockQty,
														      SUM(IF (A.rkMonth>=1,1,0)) AS OverCounts,SUM(IF (A.rkMonth>=1,A.Qty,0)) AS M1Qty,
														      SUM(IF (A.rkMonth>3,1,0)) AS OverCounts1,SUM(IF (A.rkMonth>3,A.Qty,0)) AS M3Qty
												 FROM(
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now())  AS rkMonth 
												    FROM ck1_rksheet S  
                                                    INNER JOIN ck_location L ON L.Id=S.LocationId
                                                    INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                            INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                            INNER JOIN bps B ON B.StuffId=D.StuffId 
												    WHERE  S.llSign>0 AND L.WarehouseId='$warehouseId' $SearchRows GROUP BY S.StuffId,S.Date
												)A WHERE A.Qty>0 GROUP BY A.StuffId
										UNION ALL 
											SELECT A.StuffId,(A.OrderQty-A.llQty) AS OrderQty,0 AS Counts,0 AS StockQty,
											      0 as OverCounts,0 AS M1Qty,0 AS OverCounts1,0 AS M3Qty 
											FROM(
											  SELECT G.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty
											    FROM (SELECT S.StuffId FROM ck1_rksheet S
											          INNER JOIN ck_location L ON L.Id=S.LocationId
											          INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                              INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                              INNER JOIN bps B ON B.StuffId=D.StuffId 
                                                      WHERE S.llSign>0 AND L.WarehouseId='$warehouseId'  $SearchRows GROUP BY S.StuffId) S
											    LEFT JOIN  cg1_stocksheet G ON S.StuffId=G.StuffId  
											    LEFT JOIN  ck5_llsheet L ON L.StockId=G.StockId 
											    WHERE G.cgSign=0 GROUP BY G.StuffId  
											)A GROUP BY A.StuffId 
								)S
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								WHERE  M.blSign=1  AND P.StuffId IS NULL
								GROUP BY S.StuffId 
					   )A";
				         /*
					     $sql = "SELECT SUM(IFNULL(A.OrderQty,0)) AS OrderQty,SUM(IFNULL(A.OrderQty*A.Price,0)) AS Amount,COUNT(*) AS Counts,
					                              SUM(IFNULL(A.M1Qty,0)) AS M1Qty,SUM(IFNULL(A.M1Qty*A.Price,0)) AS M1Amount,
			                                      SUM(IFNULL(A.M3Qty,0)) AS M3Qty,SUM(IFNULL(A.M3Qty*A.Price,0)) AS M3Amount    
			                  FROM (
			                      SELECT SUM(IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty)) AS OrderQty,
			                                 SUM(IF (S.xdMonth>1,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M1Qty,
			                                 SUM(IF (S.xdMonth>3,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M3Qty,
                                             IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
							    FROM (
                                     SELECT A.StuffId,A.Qty,SUM(A.OrderQty-A.llQty) AS OrderQty,A.xdMonth  
                                     FROM (
										SELECT S.StuffId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,TIMESTAMPDIFF(MONTH,MAX(G.ywOrderDTime),Now())  AS xdMonth   
										FROM( 
											   SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty FROM ck1_rksheet S 
											             INNER JOIN ck_location L ON L.Id=S.LocationId 
											             INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                                 INNER JOIN bps B ON B.StuffId=D.StuffId 
												 WHERE  S.llSign>0    AND M.blSign=1  AND L.WarehouseId='$warehouseId'  $SearchRows  GROUP BY S.StuffId
										)S 
										INNER JOIN  cg1_stocksheet G ON G.StuffId=S.StuffId AND G.cgSign=0
										LEFT JOIN   ck5_llsheet L ON L.StockId=G.StockId  
										WHERE  G.ywOrderDTime>='$lastMonth'  GROUP BY G.StockId 
								 )A GROUP BY A.StuffId
							)S 
							INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
							INNER JOIN stuffmaintype M ON M.Id=T.mainType  
							INNER JOIN bps A ON A.StuffId=S.StuffId 
							LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
							LEFT JOIN currencydata C ON B.Currency=C.Id
							LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
							WHERE S.OrderQty>0    AND M.blSign=1  AND P.StuffId IS NULL   GROUP BY S.StuffId)A";
					   */
					  }else{
					    $sql = "SELECT SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)) AS OrderQty,
			                                    SUM(A.Counts) AS Counts,SUM(IF(A.OrderQty>A.StockQty,A.StockQty,A.OrderQty)*A.Price) AS Amount,
												SUM(A.M1Qty) AS M1Qty,SUM(A.OverCounts) AS OverCounts,SUM(A.M1Qty*A.Price) AS M1Amount,
												SUM(A.M3Qty) AS M3Qty,SUM(A.OverCounts1) AS OverCounts1,SUM((A.M3Qty)*A.Price) AS M3Amount
								FROM(
								       SELECT SUM(S.OrderQty) AS OrderQty,SUM(S.Counts) AS Counts,SUM(S.StockQty) AS StockQty,
											       SUM(S.OverCounts) AS OverCounts,SUM(M1Qty) AS M1Qty,
											       SUM(S.OverCounts1) AS OverCounts1,SUM(M3Qty) AS M3Qty,
											       IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
										FROM (
												   SELECT A.StuffId,0 AS OrderQty,1 AS Counts,SUM(A.Qty) AS StockQty,
														      SUM(IF (A.rkMonth>=1,1,0)) AS OverCounts,SUM(IF (A.rkMonth>=1,A.Qty,0)) AS M1Qty,
														      SUM(IF (A.rkMonth>3,1,0)) AS OverCounts1,SUM(IF (A.rkMonth>3,A.Qty,0)) AS M3Qty
												 FROM(
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now())  AS rkMonth 
												    FROM ck1_rksheet S  
                                                     INNER JOIN ck_location L ON L.Id=S.LocationId
											         INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                             INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                             INNER JOIN bps B ON B.StuffId=D.StuffId 
												    WHERE  S.llSign>0 AND L.WarehouseId='$warehouseId' $SearchRows GROUP BY S.StuffId,S.Date
												 UNION ALL
												     SELECT  S.StuffId,SUM(S.Qty-S.llQty) AS Qty,TIMESTAMPDIFF(MONTH,(S.Date),Now()) AS rkMonth  
												     FROM ck1_rksheet S 
											          INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                              INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                              INNER JOIN bps B ON B.StuffId=D.StuffId 
												     WHERE  S.llSign>0 AND S.LocationId=0  AND D.SendFloor IN($SendFloor)  $SearchRows GROUP BY S.StuffId,S.Date 
												)A WHERE A.Qty>0 GROUP BY A.StuffId
										UNION ALL 
											SELECT A.StuffId,(A.OrderQty-A.llQty) AS OrderQty,0 AS Counts,0 AS StockQty,
											      0 as OverCounts,0 AS M1Qty,0 AS OverCounts1,0 AS M3Qty 
											FROM(
											  SELECT G.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty
											    FROM (
											       SELECT A.StuffId FROM (
											          SELECT S.StuffId FROM ck1_rksheet S
											          INNER JOIN ck_location L ON L.Id=S.LocationId
											          INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                              INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                              INNER JOIN bps B ON B.StuffId=D.StuffId 
                                                      WHERE S.llSign>0 AND L.WarehouseId='$warehouseId' $SearchRows GROUP BY S.StuffId
                                                   UNION ALL
                                                      SELECT  S.StuffId  FROM ck1_rksheet S 
												      INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                              INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                              INNER JOIN bps B ON B.StuffId=D.StuffId  
												      WHERE  S.llSign>0 AND S.LocationId=0  AND D.SendFloor IN($SendFloor)  $SearchRows GROUP BY S.StuffId
												     )A) S
											    LEFT JOIN  cg1_stocksheet G ON S.StuffId=G.StuffId  
											    LEFT JOIN  ck5_llsheet L ON L.StockId=G.StockId 
											    WHERE G.cgSign=0 GROUP BY G.StuffId  
											)A GROUP BY A.StuffId 
								)S
								INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
								INNER JOIN stufftype T ON T.TypeId=D.TypeId 
								INNER JOIN stuffmaintype M ON M.Id=T.mainType 
								INNER JOIN bps A ON A.StuffId=S.StuffId 
								LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
								LEFT JOIN currencydata C ON B.Currency=C.Id
								LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
								WHERE  M.blSign=1  AND P.StuffId IS NULL
								GROUP BY S.StuffId  
					   )A";
					  /*
						   $sql = "SELECT SUM(IFNULL(A.OrderQty,0)) AS OrderQty,SUM(IFNULL(A.OrderQty*A.Price,0)) AS Amount,COUNT(*) AS Counts,
					                              SUM(IFNULL(A.M1Qty,0)) AS M1Qty,SUM(IFNULL(A.M1Qty*A.Price,0)) AS M1Amount,
			                                      SUM(IFNULL(A.M3Qty,0)) AS M3Qty,SUM(IFNULL(A.M3Qty*A.Price,0)) AS M3Amount    
			                  FROM (
			                        SELECT SUM(IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty)) AS OrderQty,
			                                 SUM(IF (S.xdMonth>1,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M1Qty,
			                                 SUM(IF (S.xdMonth>3,IF(S.Qty>S.OrderQty,S.OrderQty,S.Qty),0)) AS M3Qty,
                                             IF(T.mainType=getSysConfig(103) AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
							    FROM (
                                     SELECT A.StuffId,A.Qty,SUM(A.OrderQty-A.llQty) AS OrderQty,A.xdMonth  
                                     FROM (
										SELECT S.StuffId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,TIMESTAMPDIFF(MONTH,MAX(G.ywOrderDTime),Now())  AS xdMonth   
										 FROM( 
											   SELECT A.StuffId,SUM(A.Qty) AS Qty   
												FROM  (
												    SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty FROM ck1_rksheet S 
											             INNER JOIN ck_location L ON L.Id=S.LocationId 
											             INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						                                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
						                                 INNER JOIN bps B ON B.StuffId=D.StuffId 
												        WHERE  S.llSign>0  AND L.WarehouseId='$warehouseId'  $SearchRows GROUP BY S.StuffId
												UNION ALL
												     SELECT S.StuffId,SUM(S.Qty-S.llQty) AS Qty FROM ck1_rksheet S 
												                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId  
												                 INNER JOIN stufftype T ON T.TypeId=D.TypeId
												                 INNER JOIN bps B ON B.StuffId=D.StuffId   
												                 WHERE  S.llSign>0 AND S.LocationId=0  AND D.SendFloor IN($SendFloor)  $SearchRows   GROUP BY S.StuffId 
												  ) A GROUP BY A.StuffId
										)S 
										INNER JOIN  cg1_stocksheet G ON G.StuffId=S.StuffId AND G.cgSign=0
										LEFT JOIN   ck5_llsheet L ON L.StockId=G.StockId  
										WHERE  G.ywOrderDTime>='$lastMonth'  GROUP BY G.StockId 
								 )A GROUP BY A.StuffId
							)S 
							INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
							INNER JOIN stufftype T ON T.TypeId=D.TypeId 
							INNER JOIN stuffmaintype M ON M.Id=T.mainType  
							INNER JOIN bps A ON A.StuffId=S.StuffId 
							LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
							LEFT JOIN currencydata C ON B.Currency=C.Id
							LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10   
							WHERE S.OrderQty>0    AND M.blSign=1  AND P.StuffId IS NULL    GROUP BY S.StuffId)A";
							*/
					}
				     break;
			}
		//	if ($this->LoginNumber==10868) echo $sql;
			$query = $this->db->query($sql);
		    return $query->first_row('array');
	}
	
	//配件库存分类统计
	function get_stufftype_amount($warehouseId,$SendFloor='',$limits =0,$CompanyId='')
	{

			$LimitsRows = $limits>0 ?" LIMIT $limits":"";
			$SearchRows= $CompanyId==''?'':" AND A.CompanyId='$CompanyId' "; 
			switch($warehouseId){
			    case  'all':
			         $sql = " SELECT A.TypeId,A.TypeName,COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount     
                         FROM (
                                 SELECT  T.TypeId,T.TypeName,SUM(S.Qty-S.llQty) AS Qty,
                                               IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                  LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
								 WHERE  S.llSign>0    AND M.blSign=1  AND P.StuffId IS NULL  $SearchRows GROUP BY  S.StuffId
						  )A GROUP BY A.TypeId ORDER BY Amount DESC $LimitsRows";
			         break;
				default:
				  if ($SendFloor==''){
				      $sql = "SELECT A.TypeId,A.TypeName,COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount     
                           FROM (
                                 SELECT  T.TypeId,T.TypeName,SUM(S.Qty-S.llQty) AS Qty,
                                               IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                  LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
								 WHERE  S.llSign>0    AND M.blSign=1  AND L.WarehouseId='$warehouseId' AND P.StuffId IS NULL  $SearchRows GROUP BY  S.StuffId
						   )A GROUP BY A.TypeId ORDER BY Amount DESC $LimitsRows";
				   }else{
				     $sql = "SELECT A.TypeId,A.TypeName,COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount   
				         FROM (
				                SELECT  T.TypeId,T.TypeName,SUM(S.Qty-S.llQty) AS Qty,
                                               IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                  LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
								 WHERE  S.llSign>0    AND M.blSign=1  AND L.WarehouseId='$warehouseId' AND P.StuffId IS NULL  $SearchRows GROUP BY  S.StuffId  
				    UNION ALL
				                SELECT  T.TypeId,T.TypeName,SUM(S.Qty-S.llQty) AS Qty,
                                               IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                  LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10  
								 WHERE  S.llSign>0   AND M.blSign=1  AND S.LocationId=0 AND D.SendFloor IN($SendFloor)  AND P.StuffId IS NULL  $SearchRows  GROUP BY  S.StuffId  
								)A GROUP BY A.TypeId ORDER BY Amount DESC $LimitsRows";
					 }
				     break;
			}
			
			$query = $this->db->query($sql);
		    return $query->result_array();
	}
	
	
	//配件库存按供应商分类
	function get_company_amount($warehouseId,$SendFloor='',$TypeId='')
	{
	      $SearchRows = $TypeId==''?'':" AND T.TypeId='$TypeId' ";
			switch($warehouseId){
			    case  'all':
			        $sql = "SELECT A.CompanyId,A.Forshort,COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount  
						FROM(
						SELECT  B.CompanyId,B.Forshort,SUM(S.Qty-S.llQty) AS Qty,
						              IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10 
								 WHERE  S.llSign>0 AND M.blSign=1   $SearchRows AND P.StuffId IS NULL GROUP BY S.StuffId
						 ) A WHERE A.CompanyId>0 GROUP BY A.CompanyId  ORDER BY Amount DESC  ";
			       break;
				default:
				  if ($SendFloor==''){
				     $sql = "SELECT A.CompanyId,A.Forshort,COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount  
						     FROM(
						        SELECT  B.CompanyId,B.Forshort,SUM(S.Qty-S.llQty) AS Qty,
						                    IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
								 FROM  ck1_rksheet S 
				                 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10 
								 WHERE  S.llSign>0  AND M.blSign=1   AND L.WarehouseId='$warehouseId' AND P.StuffId IS NULL $SearchRows GROUP BY S.StuffId
						 ) A GROUP BY A.CompanyId ORDER BY Amount DESC ";
				 }else{
				     $sql = "SELECT A.CompanyId,A.Forshort,COUNT(*) AS Counts,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount 
				           FROM (
				                SELECT  B.CompanyId,B.Forshort,SUM(S.Qty-S.llQty) AS Qty,
						                      IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
								 FROM  ck1_rksheet S 
				                 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3)
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10 
								 WHERE  S.llSign>0  AND M.blSign=1   AND L.WarehouseId='$warehouseId' AND P.StuffId IS NULL $SearchRows GROUP BY S.StuffId  
				    UNION ALL
				               SELECT  B.CompanyId,B.Forshort,SUM(S.Qty-S.llQty) AS Qty,
						                     IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price 
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10 
								 WHERE  S.llSign>0  AND M.blSign=1  AND S.LocationId=0 AND D.SendFloor IN($SendFloor)  AND P.StuffId IS NULL $SearchRows  GROUP BY S.StuffId   
								)A GROUP BY A.CompanyId ORDER BY Amount DESC";
					}
				     break;
			}
			
			$query = $this->db->query($sql);
		    return $query->result_array();
	}
	
	//配件明细
	function get_company_sheet($warehouseId,$SendFloor,$CompanyId,$TypeId='')
	{
	   $SearchRows  = $CompanyId==''?'':" AND A.CompanyId='$CompanyId' "; 
		$SearchRows.= $TypeId==''?'':" AND T.TypeId='$TypeId' ";
		
		switch($warehouseId){
			    case  'all':
			        $sql = "SELECT A.StuffId,A.StuffCname,A.Picture,A.oStockQty,A.Price,A.PreChar,A.Decimals,getCgLastOrderDTime(A.StuffId) AS xdDate,
			                                  SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount  
						FROM(
						SELECT  S.StuffId,D.StuffCname,D.Picture,K.oStockQty,C.PreChar,U.Decimals,SUM(S.Qty-S.llQty) AS Qty,
						              IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN  stuffunit U ON U.Id=D.Unit 
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10
								 WHERE  S.llSign>0  AND M.blSign=1   AND P.StuffId IS NULL $SearchRows GROUP BY S.StuffId
						 ) A WHERE 1 GROUP BY A.StuffId ORDER BY Amount DESC ";
			       break;
			   default:
				  if ($SendFloor==''){
				     $sql = "SELECT A.StuffId,A.StuffCname,A.Picture,A.oStockQty,A.Price,A.PreChar,A.Decimals,getCgLastOrderDTime(A.StuffId) AS xdDate,
			                                  SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount  
						FROM(
						SELECT  S.StuffId,D.StuffCname,D.Picture,K.oStockQty,C.PreChar,U.Decimals,SUM(S.Qty-S.llQty) AS Qty,
						              IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
								 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN  stuffunit U ON U.Id=D.Unit 
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10
								 WHERE  S.llSign>0   AND M.blSign=1    AND L.WarehouseId='$warehouseId'  AND P.StuffId IS NULL  $SearchRows GROUP BY S.StuffId
						 ) A GROUP BY A.StuffId ORDER BY Amount DESC";
				 }else{
				     $sql = "SELECT A.StuffId,A.StuffCname,A.Picture,A.oStockQty,A.Price,A.PreChar,A.Decimals,getCgLastOrderDTime(A.StuffId) AS xdDate,
			                                  SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount  
						FROM(
						SELECT  S.StuffId,D.StuffCname,D.Picture,K.oStockQty,C.PreChar,U.Decimals,SUM(S.Qty-S.llQty) AS Qty,
						              IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
								 INNER JOIN ck_location L ON L.Id=S.LocationId  
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN  stuffunit U ON U.Id=D.Unit 
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10
								 WHERE  S.llSign>0   AND M.blSign=1   AND L.WarehouseId='$warehouseId'  AND P.StuffId IS NULL  $SearchRows GROUP BY S.StuffId  
				    UNION ALL
				        SELECT  S.StuffId,D.StuffCname,D.Picture,K.oStockQty,C.PreChar,U.Decimals,SUM(S.Qty-S.llQty) AS Qty,
						              IF(T.mainType=getSysConfig(103)  AND B.CompanyId IN(getSysConfig(106)),D.costPrice,D.Price)*IFNULL(C.Rate,1)  AS Price  
								 FROM  ck1_rksheet S 
				                 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				                 INNER JOIN stufftype T ON T.TypeId=D.TypeId 
				                 INNER JOIN stuffmaintype M ON M.Id=T.mainType  
				                 INNER JOIN bps A ON A.StuffId=S.StuffId 
				                 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				                 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
				                 LEFT JOIN currencydata C ON B.Currency=C.Id
				                 LEFT JOIN  stuffunit U ON U.Id=D.Unit  
				                 LEFT JOIN stuffproperty P ON P.StuffId=S.StuffId AND P.Property=10 
								 WHERE  S.llSign>0  AND M.blSign=1   AND S.LocationId=0 AND D.SendFloor IN($SendFloor)  AND P.StuffId IS NULL  $SearchRows  GROUP BY S.StuffId   
								)A GROUP BY A.StuffId ORDER BY Amount DESC";
					}
					
				     break;
			 }
			 $query = $this->db->query($sql);
		     return $query->result_array();
	}
		
	
	//出入库
	function get_stuff_outinstock($SendFloor,$InOutSign='1',$Date='')
	{
		$SearchRows = $Date==''?" AND S.Date='" . $this->Date . "' ":" AND S.Date='$Date' ";
		$SearchRows.= $SendFloor==''?'':" AND D.SendFloor IN($SendFloor) ";
		switch($InOutSign){
			case "31"://入库
		
			 $sql = "SELECT  '31' AS Sign,D.StuffId,D.StuffCname,D.Picture,K.tStockQty,D.Price,S.Type,S.Qty,S.creator,S.created,S.LocationId,L.Region,L.Location,U.Decimals
				 FROM ck1_rksheet S 
				 INNER JOIN ck_location L ON L.Id=S.LocationId 
				 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				 INNER JOIN  stuffunit U ON U.Id=D.Unit 
				 WHERE  1 $SearchRows  ORDER BY S.created DESC";
			 break;
			case "21"://出库
		
			 $sql = "SELECT '21' AS Sign,D.StuffId,D.StuffCname,D.Picture,K.tStockQty,D.Price,S.Type,S.Qty,S.creator,S.created,R.LocationId,L.Region,L.Location,U.Decimals 
				 FROM ck5_llsheet S 
				 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				 INNER JOIN  stuffunit U ON U.Id=D.Unit  
				 LEFT JOIN ck1_rksheet R ON R.Id = S.RkId
				 LEFT JOIN ck_location L ON L.Id=R.LocationId 
				 WHERE 1  AND S.Qty>0  $SearchRows  ORDER BY S.created DESC";
			 break;
		
		  default:
			 $sql = "SELECT  * FROM (
				 SELECT  '31' AS Sign,D.StuffId,D.StuffCname,D.Picture,K.tStockQty,D.Price,S.Type,S.Qty,S.creator,S.created,S.LocationId,L.Region,L.Location,U.Decimals 
				 FROM ck1_rksheet S 
				 INNER JOIN ck_location L ON L.Id=S.LocationId 
				 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				 INNER JOIN  stuffunit U ON U.Id=D.Unit  
				 WHERE   1  $SearchRows 
				 UNION ALL
				 SELECT '21' AS Sign,D.StuffId,D.StuffCname,D.Picture,K.tStockQty,D.Price,S.Type,S.Qty,S.creator,S.created,R.LocationId,L.Region,L.Location,U.Decimals 
				 FROM ck5_llsheet S 
				 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
				 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
				 INNER JOIN  stuffunit U ON U.Id=D.Unit  
				 LEFT JOIN ck1_rksheet R ON R.Id = S.RkId
				 LEFT JOIN ck_location L ON L.Id=R.LocationId 
				 WHERE   1  AND S.Qty>0   $SearchRows 
		     ) A ORDER BY A.created DESC";
	         break;
	     }
	     //echo $sql;
		 $query = $this->db->query($sql);
		 return $query->result_array();
	}
	
	
	//按库位
	function get_stuff_postion($warehouseId='',$orderbySign='')
	{
	
	   $SearchRows= $warehouseId=='all' || $warehouseId==''?'':" AND L.WarehouseId ='$warehouseId' ";
	   $OrderbyStr  = $orderbySign=='qty'? " ORDER BY OverQty DESC,Qty DESC,Region,LocationId ":" ORDER BY Region,LocationId,shelfSign ";
	   
	   $sql = "SELECT A.LocationId,SUM(A.Qty) AS Qty,SUM(A.OverQty) AS OverQty,SUM(A.Counts) AS Counts,L.Region,L.Location,L.ShelfSign 
					FROM (
					SELECT 1 AS Counts,SUM(S.Qty-S.llQty) AS Qty,
					       SUM(IF(TIMESTAMPDIFF(MONTH,S.Date,CURDATE())>=3,S.Qty-S.llQty,0)) AS OverQty,
					        IF(L.Mid>0,L.Mid,L.Id) AS LocationId 
							FROM ck1_rksheet S
							INNER JOIN ck_location L ON L.Id=S.LocationId 
							WHERE   S.Qty-S.llQty>0  GROUP BY S.StuffId,S.LocationId   
					)A 
					INNER JOIN ck_location L ON L.Id=A.LocationId 
					WHERE A.Qty>0  $SearchRows GROUP BY A.LocationId $OrderbyStr"; 
							  
		 $query = $this->db->query($sql);
		 return $query->result_array();
	}
	
	//库位配件明细
	function get_postion_sheet($LocationId, $srt='')
	{
		
		$sortstr = ' ORDER BY created ';
		if ($srt == '1') {
			$sortstr = ' ORDER BY created desc';
		}
		
		$newFrameTypeId = "'9176','9188','9201','9202','9203','9206'";
		
		$sql = "SELECT  '31' AS Sign,D.StuffId,D.StuffCname,D.Picture,D.TypeId,K.oStockQty,K.tStockQty,D.Price,D.FrameCapacity,D.basketType,S.Type,SUM(S.Qty-S.llQty) AS Qty,sum(if(D.TypeId in ($newFrameTypeId) and D.FrameCapacity>0, ceil((S.Qty-S.llQty)/D.FrameCapacity) ,0)) as newFrames,S.creator,MIN(S.Date) AS created,S.LocationId,U.Decimals,B.Forshort,C.PreChar,TIMESTAMPDIFF(MONTH,MIN(S.Date),CURDATE()) as Months    ,W.Name wsname 
						 FROM ck1_rksheet S 
						 INNER JOIN ck_location L ON L.Id=S.LocationId 
						 INNER JOIN stuffdata D ON D.StuffId=S.StuffId 
						 INNER JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
						 INNER JOIN stuffunit U ON U.Id=D.Unit  
		                 INNER JOIN bps A ON A.StuffId=D.StuffId 
		                 
		                 
LEFT JOIN  yw1_scsheet     SC  ON S.StockId=SC.mStockId
                        LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
                        
						 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId AND B.ObjectSign IN (1,3) 
						 LEFT JOIN currencydata C ON B.Currency=C.Id
				         WHERE S.Qty-S.llQty>0  AND  (L.Id='$LocationId' OR L.Mid='$LocationId') GROUP BY StuffId  $sortstr";
		  $query = $this->db->query($sql);
		  return $query->result_array();		 
	}
	
	//获取入库主表Id
	function get_rkmid($CompanyId,$BillNumber)
	{
	   $sql = "SELECT Id  FROM ck1_rkmain  WHERE CompanyId =?  AND Date = ?  AND  BillNumber=?  ORDER BY BillNumber DESC LIMIT 1";
	   $query = $this->db->query($sql,array($CompanyId,$this->Date,$BillNumber));
	   
	   if ($query->num_rows() > 0){
		   $rows = $query->first_row('array');
		   $rkId = $rows['Id'];
	   }
	   else{
		   $data=array(
		         'BillNumber'=>$BillNumber, 
		          'CompanyId'=>$CompanyId,
		                'Remark'=>'采购单品检的入库记录',
		                   'Locks'=>'0',
		                  'Estate'=>'1',
		             'rkDate'=>$this->DateTime,
		               'Date'=>$this->Date,
		           'Operator'=>$this->LoginNumber,
		            'creator'=>$this->LoginNumber,
		            'created'=>$this->DateTime 
			       );
		       
           $this->db->insert('ck1_rkmain', $data);
           $rkId = $this->db->insert_id();
	   }
	   return $rkId;
	} 
	
	
	 //获取当天仓库的备品转入数量(按入库记录统计)
     function get_bprk_daycount($WarehouseId,$date='')
     {
	    $date=$date==''?$this->Date:$date;
	   
	    $sql = "SELECT COUNT(*) AS Counts,IFNULL(SUM(A.Qty),0) AS Qty  
	          FROM (
	           SELECT B.StuffId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck1_rksheet B 
               INNER JOIN ck_location L ON L.Id=B.LocationId   
	           WHERE B.Type=2 AND  L.WarehouseId =?  AND B.Date = ? GROUP BY B.StuffId)A ";
	     $query=$this->db->query($sql,array($WarehouseId,$date));
	     return $query->first_row('array');
  } 
  
   //获取当月仓库的备品转入数量(按入库记录统计)
   function get_bprk_monthcount($WarehouseId,$month='')
   {
	    $month=$month==''?date("Y-m"):$month;
	   
	   $sql = "SELECT COUNT(*) AS Counts,IFNULL(SUM(A.Qty),0) AS Qty  
	          FROM (
	           SELECT B.StuffId,IFNULL(SUM(B.Qty),0) AS Qty 
	           FROM ck1_rksheet B 
               INNER JOIN ck_location L ON L.Id=B.LocationId   
	           WHERE  B.Type=2  AND  L.WarehouseId =?  AND DATE_FORMAT(B.Date,'%Y-%m') = ? GROUP BY B.StuffId)A";
	    $query=$this->db->query($sql,array($WarehouseId,$month));
	    return $query->first_row('array');
   }
	
	
	//保存入库记录
	function save_records($Ids)
    {
	    $IdArray=explode(',', $Ids);
		$saveSign=0;
		
		$this->load->model('QcCjtjModel');
		
		foreach($IdArray as $Id){
		
		   $qty=$this->QcCjtjModel->get_unrkqty($Id);
		   if ($qty>0){
			   $query=$this->db->query("CALL proc_ck1_rksheet_location_save('$Id','$qty','0','0','0','" .$this->LoginNumber ."');");
	           $row = $query->first_row('array');
	           if ($row['OperationResult']=='Y'){
		           $saveSign++;
	           }
	           $query = null;
	           $row   = null;
           }
	    }
	    
	    if ($saveSign==count($IdArray)){
		    return 1;
	    }else{
		    return 0; 
	    }
    }
   
    //保存入库记录(带库位)
	function save_location_records($Ids,$frameCount,$LocationId)
	{
		$IdArray=explode(',', $Ids);
		$saveSign=0;
		
		foreach($IdArray as $Id){
		   $query=$this->db->query("CALL proc_ck1_rksheet_location_save('$Id','0','$frameCount','$LocationId','" .$this->LoginNumber ."');");
		   //echo "CALL proc_ck1_rksheet_location_save('$Id','0','$frameCount','$LocationId','" .$this->LoginNumber ."');";
           $row = $query->first_row('array');
           if ($row['OperationResult']=='Y'){
	           $saveSign++;
           }
           $query = null;
           $row   = null;
	    }
	    
	    if ($saveSign==count($IdArray)){
		    return 1;
	    }else{
		    return 0; 
	    }
   }
   
    function edit_location($Id, $LocationId) {
	    
	    $editid = explode(',', $Id) ;
	   if (count($editid)>0) {




		$editid = explode(',', $Id) ;
		
		$data = array(
			'LocationId'     => $LocationId,
		    'modified'  => $this->DateTime,
		    'modifier'  => $this->LoginNumber
		);
			    
		
		$this->db->where_in('Id',$editid);
		$this->db->trans_begin();
		$query=$this->db->update('ck1_rksheet', $data);
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    
				return -1;
		} else {
			    $this->db->trans_commit();

			    return 1;
		}

	   }
	   return -1;
   }
  
    
}