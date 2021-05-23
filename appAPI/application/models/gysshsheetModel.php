<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  GysshsheetModel extends MC_Model {
    function __construct()
    { 
        parent::__construct();
    }
    
    function get_last_wsch_time($wsid) {
	    $sql = "
	    SELECT M.created FROM gys_shsheet S 
inner join gys_shmain M on S.Mid=M.Id
inner join yw1_scsheet C on S.sPOrderId=C.sPOrderId and C.mStockId=S.StockId
where C.Workshopid=$wsid and  S.sPOrderId is not null and S.StockId is not null  order by M.created  desc limit 1;";
		$query= $this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->row()->created;
		}
		return null;
	    
    }
    
    function get_aql_infos($AQL, $Qty) {
	    
	     $checkResult = $this->db->query("SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize 
                 FROM qc_levels L
                 LEFT JOIN  qc_lotsize S ON S.Code=L.Code     
                 WHERE L.AQL='$AQL' AND S.Start<='$Qty' AND S.End>='$Qty'");
               
               if ($checkResult->num_rows() > 0){
	               
	               $checkRow=$checkResult->row_array();
                   $SampleSize=$checkRow["SampleSize"]; 
                   $Lotsize=$checkRow["Lotsize"]; 
                   $ReQty=$checkRow["Re"]==""?1:$checkRow["Re"];
                   if ($Lotsize>0) {$CheckQty=$Lotsize;}else{$CheckQty=$SampleSize;}
               }
               else{  //低于最低抽样数量，全检
                    $CheckQty=$Qty;
                    $ReQty=1;
                }


				return array('check'=>$CheckQty, 'reqty'=>$ReQty);
    }
    
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT S.Id,S.Mid,S.sPOrderId,S.StockId,S.StuffId,S.Qty,S.SendSign,S.Estate,
	                  M.BillNumber,M.GysNumber,M.CompanyId,M.Date,M.Floor,M.created,IFNULL(W.Name,B.Forshort) AS Forshort,
	                  D.TypeId,D.StuffCname,D.Picture,D.FrameCapacity,D.CheckSign,A.ActionId,U.Decimals,ST.AQL  
	           FROM gys_shsheet S 
	           LEFT JOIN gys_shmain M ON M.Id=S.Mid
	           LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
	            LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId  
	           LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	           LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
	           LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3)
	           LEFT JOIN  stuffunit U ON U.Id=D.Unit 
	           WHERE S.Id=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	function get_sh_companyid($Mid=0)
	{
	   $sql  = "SELECT CompanyId FROM gys_shmain WHERE Id=?"; 	
	   $query=$this->db->query($sql,array($Mid));
	   $rows = $query->first_row('array');
	   return $rows['CompanyId'];
	}
	
    //获取送货单数量
    function get_order_counts($Floor,$Estate)
    {
        $sql = "SELECT COUNT(1) AS Counts FROM  gys_shsheet S 
                LEFT JOIN gys_shmain M ON M.Id=S.Mid  
                WHERE S.Estate=? AND M.Floor=? AND SendSign<2 ";
		$query = $this->db->query($sql,array($Estate,$Floor));
		$row = $query->first_row();
		$counts=$row->Counts;
		return $counts==""?0:$counts;
    }
    
    	 //获取送货单数量堆积
    function get_overed_order_counts($Floor,$Estate)
    {
        $sql = "SELECT count(*) AS Counts 

FROM  gys_shsheet S 
                LEFT JOIN gys_shmain M ON M.Id=S.Mid  
                WHERE S.Estate=? AND M.Floor=? AND SendSign<2 and TIMESTAMPDIFF(HOUR,S.shDate,now())>48 ";
		$query = $this->db->query($sql,array($Estate,$Floor));
		$row = $query->first_row();
		$counts=$row->Counts;
		return $counts==""?0:$counts;
    }

    //获取已送货待检数量
    function get_shed_qty($StockId){
        $sql = "SELECT SUM(Qty) AS shQty FROM  gys_shsheet  WHERE  StockId=? AND Estate>0 AND SendSign<2 ";
		$query = $this->db->query($sql,$StockId);
		$row = $query->first_row();
		$shQty=$row->shQty;
		$shQty=$shQty==""?0:$shQty;
		return $shQty;
    }
    
    //生产单位当天送货数
    function get_workshop_shqty($wsid,$date='') {
	    $date = $date==''?$this->Date:$date;
	    $sql = "
				   SELECT SUM(R.Qty) AS shQty FROM 
				   yw1_scsheet S
				   
				  left join gys_shsheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId
				  where S.WorkShopId=? and R.Date=? AND R.Estate>0";
		$query = $this->db->query($sql,array($wsid,$date));
		$row = $query->first_row();
		$shQty=$row->shQty;
		$shQty=$shQty==""?0:$shQty;
		return $shQty;
    }

    
    //工单的送货待检数量
	function get_scorder_shqty($sPOrderId,$StockId) {
	
		$sql = "SELECT sum(Qty) AS shQty FROM  gys_shsheet  WHERE  sPOrderId=? AND StockId=?  AND Estate>0 ";
		$query = $this->db->query($sql,array($sPOrderId,$StockId));
		$row = $query->first_row();
		$shQty=$row->shQty;
		$shQty=$shQty==""?0:$shQty;
		return $shQty;
	}
	
	//获取同一张送货单的备品记录Id
	function get_scorder_bpids($Mid,$StuffId)
	{
		$sql ="SELECT GROUP_CONCAT(Id) AS Ids FROM gys_shsheet WHERE Mid=? AND StuffId=? AND SendSign=2 AND Estate>0 ";
		$query = $this->db->query($sql,array($Mid,$StuffId));
		$row = $query->first_row('array');
		return $row['Ids']==''?'':$row['Ids'];
	}
	
	//获取自动送货单号
	function get_new_gysnumber($CompanyId)
	{
	    $thisYear=date('Y');
	    $this->db->select_max('GysNumber');
	    $this->db->where('CompanyId', $CompanyId); 
	    $this->db->like('GysNumber', "$thisYear", 'after'); 
        $query = $this->db->get('gys_shmain');
        $row = $query->first_row('array');
        
        return $row['GysNumber']==''?($thisYear . '00001'):$row['GysNumber']+1;
	}
	
	//统计未收送货单分类（按供应商、送货地点）
	function get_supplier_list($Floor,$Estate)
	{
	   $thisWeek=$this->ThisWeek;
	   
	   $sql = "SELECT S.Id,M.CompanyId,B.Forshort,SUM(S.Qty) AS Qty,SUM(S.Qty*IFNULL(G.Price,0)) AS Amount,count(*) as Cts,
	           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign    ,Max(M.created) as time 
	           FROM gys_shsheet S 
	           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
	           LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
	           LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
	           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND FIND_IN_SET(M.CompanyId,getSysConfig(106))=0 GROUP  BY M.CompanyId
	        UNION ALL
	           SELECT S.Id,IFNULL(A.WorkShopId,M.CompanyId) AS CompanyId,IFNULL(W.Name,B.Forshort) AS Forshort,
	           SUM(S.Qty) AS Qty,SUM(S.Qty*IFNULL(G.CostPrice,0)) AS Amount,count(*) as Cts,
	           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign    ,Max(M.created) as time 
	           FROM gys_shsheet S 
	           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
	           LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId  
	           LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	           LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
	           LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3)
	           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND FIND_IN_SET(M.CompanyId,getSysConfig(106))>0 GROUP BY A.WorkShopId 
	        ORDER BY Amount DESC"; 
	        
	        $query=$this->db->query($sql);
		    return $query->result_array();
	}
	
	//统计未收送货单数量（按状态）
	function get_sh_counts($Floor,$Estate,$checkSign=0)
	{
       $thisWeek=$this->ThisWeek;
       $SearchRows='';
       if ($Estate==2){
	      $SearchRows=$checkSign==1?' AND NOT EXISTS(SELECT N.Sid FROM qc_mission N WHERE N.Sid=S.Id) ':' AND NOT EXISTS(SELECT Q.StockId FROM qc_currentcheck Q WHERE Q.StockId=S.StockId) '; 
	       $SearchRows='  AND NOT EXISTS(SELECT N.Sid FROM qc_mission N WHERE N.Sid=S.Id)  '; 
       }
       
	   $sql = "SELECT SUM(A.Counts) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty,SUM(A.OverSign) AS OverSign,SUM(A.OverQty) AS OverQty
			FROM (
			SELECT 1 AS Counts,S.Qty,
	           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign,
	           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0),0) AS OverQty     
	           FROM gys_shsheet S 
	           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
	           LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
               LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
	           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND D.ComboxSign!=1  $SearchRows
			UNION ALL
			SELECT 1 AS Counts,S.Qty,
	           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign,
	           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0),0) AS OverQty     
	           FROM gys_shsheet S 
	           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
			   LEFT JOIN cg1_stuffcombox B ON B.StockId=S.StockId 
			   LEFT JOIN cg1_stocksheet G ON G.StockId=B.mStockId 
               LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
	           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND D.ComboxSign=1  $SearchRows
			)A "; 
	           
	    $query=$this->db->query($sql);
		return $query->first_row('array');        
	}
	
	//获取送货单明细(按楼层、状态)
	function get_floor_order($Floor,$Estate,$checkSign=0)
	{
	    $thisWeek=$this->ThisWeek;
	    $SearchRows='';$orderby='';
        if ($Estate==2){
	       $SearchRows=$checkSign==1?' AND NOT EXISTS(SELECT N.Sid FROM qc_mission N WHERE N.Sid=S.Id) ':' AND NOT EXISTS(SELECT Q.StockId FROM qc_currentcheck Q WHERE Q.StockId=S.StockId) '; 
	       
	        $SearchRows=' AND NOT EXISTS(SELECT N.Sid FROM qc_mission N WHERE N.Sid=S.Id) '; 
        }
        
        $OrderBy =$Estate==2?'ORDER BY shDate':'ORDER BY DeliveryWeek';
        
        $sql = "SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.shDate,(G.AddQty+G.FactualQty) AS OrderQty,G.POrderId,G.DeliveryWeek,D.StuffCname,D.Picture,
	              D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$thisWeek',1,0) AS OveSign ,A.WorkShopId,U.Decimals 
			      FROM  gys_shmain M 
			      LEFT JOIN gys_shsheet S ON M.Id=S.Mid
			      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			      LEFT JOIN  stuffunit U ON U.Id=D.Unit 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
			      WHERE M.Floor='$Floor' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign!=1 $SearchRows 
             UNION ALL
               SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.shDate,(G.AddQty+G.FactualQty) AS OrderQty,G.POrderId,G.DeliveryWeek,D.StuffCname,D.Picture,
	              D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$thisWeek',1,0) AS OveSign ,A.WorkShopId,U.Decimals  
			      FROM  gys_shmain M 
			      LEFT JOIN gys_shsheet S ON M.Id=S.Mid
				  LEFT JOIN cg1_stuffcombox C ON C.StockId=S.StockId 
			      LEFT JOIN cg1_stocksheet G ON G.StockId=C.mStockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			      LEFT JOIN  stuffunit U ON U.Id=D.Unit 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
			      WHERE M.Floor='$Floor' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign=1 $SearchRows 
               $OrderBy ";
	          
        $query=$this->db->query($sql);
		return $query->result_array();       
	}
	
	//统计品检中送货单数量（按楼层）
	function get_checking_counts($Floor,$checkSign=0)
	{
       $thisWeek=$this->ThisWeek;
	   $Estate=2;
	   switch($checkSign){
	     case 1:
			   $sql = "SELECT SUM(A.Counts) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty,SUM(A.OverSign) AS OverSign,SUM(A.OverQty) AS OverQty
					FROM (
					SELECT 1 AS Counts,S.Qty,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0),0) AS OverQty     
			           FROM  gys_shsheet S  
			           INNER JOIN qc_mission N ON N.Sid=S.Id  
			           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
			           LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
		               LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND D.ComboxSign!=1 
					UNION ALL
					SELECT 1 AS Counts,S.Qty,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0),0) AS OverQty     
			           FROM  gys_shsheet S  
			           INNER JOIN qc_mission N ON N.Sid=S.Id  
			           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
					   LEFT JOIN cg1_stuffcombox B ON B.StockId=S.StockId 
					   LEFT JOIN cg1_stocksheet G ON G.StockId=B.mStockId 
		               LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND D.ComboxSign=1
					)A  "; 
	          break;
            default:
              $sql = "SELECT SUM(A.Counts) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty,SUM(A.OverSign) AS OverSign,SUM(A.OverQty) AS OverQty
					FROM (
					SELECT 1 AS Counts,S.Qty,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0),0) AS OverQty     
			           FROM qc_currentcheck A
		               LEFT JOIN gys_shsheet S ON S.StockId=A.StockId 
			           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
			           LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
		               LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND D.ComboxSign!=1
					UNION ALL
					SELECT 1 AS Counts,S.Qty,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',1,0),0) AS OverSign,
			           IF(S.SendSign=0 AND G.DeliveryWeek>0,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0),0) AS OverQty     
			           FROM qc_currentcheck A
		               LEFT JOIN gys_shsheet S ON S.StockId=A.StockId 
			           LEFT JOIN gys_shmain M ON M.Id=S.Mid 
					   LEFT JOIN cg1_stuffcombox B ON B.StockId=S.StockId 
					   LEFT JOIN cg1_stocksheet G ON G.StockId=B.mStockId 
		               LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			           WHERE S.Estate='$Estate' AND S.SendSign<2  AND M.Floor='$Floor' AND D.ComboxSign=1
					)A  "; 
          break;
        }     
	    $query=$this->db->query($sql);
		return $query->first_row('array');        
	}

	
	//获取品检中明细(按楼层、状态)
	function get_checking_order($Floor,$checkSign)
	{
	    $thisWeek=$this->ThisWeek;
	    $Estate=2;
	    switch($checkSign){
	     case 1:
		   $sql = "SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.POrderId,G.DeliveryWeek,
		          D.StuffCname,D.Picture,
	              N.LineId,D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals ,A.WorkShopId ,MAX(IFNULL(T.Date,0)) AS lasttime
	              FROM  gys_shsheet S  
	              LEFT JOIN  qc_cjtj T on T.Sid=S.Id 
			      INNER JOIN qc_mission N ON N.Sid=S.Id 
			      LEFT JOIN gys_shmain M ON M.Id=S.Mid 
			      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN stuffunit U ON U.Id=D.Unit
			      WHERE M.Floor='$Floor' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign!=1 
			      GROUP BY S.Id
             UNION ALL
               SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.POrderId,G.DeliveryWeek,
                  D.StuffCname,D.Picture,
	              N.LineId,D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals ,A.WorkShopId  ,MAX(IFNULL(T.Date,0)) AS lasttime
			      FROM  gys_shsheet S  
			      LEFT JOIN  qc_cjtj T on T.Sid=S.Id 
			      INNER JOIN qc_mission N ON N.Sid=S.Id  
			      LEFT JOIN gys_shmain M ON M.Id=S.Mid 
				  LEFT JOIN cg1_stuffcombox MB ON MB.StockId=S.StockId 
			      LEFT JOIN cg1_stocksheet G ON G.StockId=MB.mStockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN stuffunit U ON U.Id=D.Unit
			      WHERE M.Floor='$Floor' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign=1 
			      GROUP BY S.Id
               ORDER BY lasttime DESC,DeliveryWeek";
              break;
            default:
              $sql = "SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.POrderId,G.DeliveryWeek,
                  D.StuffCname,D.Picture,
	              D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals,A.WorkShopId    
	              FROM qc_currentcheck C 
			      LEFT JOIN gys_shsheet S ON C.StockId=S.StockId 
			      LEFT JOIN gys_shmain M ON M.Id=S.Mid 
			      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN  stuffunit U ON U.Id=D.Unit 
			      WHERE M.Floor='$Floor' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign!=1 
             UNION ALL
               SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.POrderId,G.DeliveryWeek,
                  D.StuffCname,D.Picture,
	              D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals ,A.WorkShopId    
			      FROM qc_currentcheck C 
			      LEFT JOIN gys_shsheet S ON C.StockId=S.StockId 
			      LEFT JOIN gys_shmain M ON M.Id=S.Mid 
				  LEFT JOIN cg1_stuffcombox BM ON BM.StockId=S.StockId 
			      LEFT JOIN cg1_stocksheet G ON G.StockId=BM.mStockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN  stuffunit U ON U.Id=D.Unit 
			      WHERE M.Floor='$Floor' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign=1 
               ORDER BY DeliveryWeek";
              break;
         }
        $query=$this->db->query($sql);
		return $query->result_array();       
	}
	
	//品检报告(按楼层)
	function get_checked_counts($Floor){

	    $Estate=2;
	   
		$sql ="SELECT COUNT(*) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty FROM (
			           SELECT  S.Qty AS shQty,SUM(C.Qty) AS Qty,MAX(C.Date) AS scDate   
						FROM qc_mission H 
						INNER JOIN gys_shsheet S ON H.Sid=S.Id
						INNER JOIN gys_shmain M ON S.Mid=M.Id 
						LEFT JOIN cg1_stocksheet  G ON G.StockId=S.StockId 
						LEFT JOIN qc_cjtj  C ON C.Sid=S.Id  
						WHERE  H.rkSign=1 AND S.Estate='$Estate' AND S.SendSign IN (0,1) AND M.Floor='$Floor' AND C.Qty>0
					    GROUP BY S.Id
			)A  WHERE A.Qty>0 ";//OR TIMESTAMPDIFF(minute,A.scDate,Now())>=30
	   $query=$this->db->query($sql);
	   return $query->first_row('array'); 				
	}
	
	//品检报告记录(按楼层)
	function get_checked_order($Floor){
	   $Estate=2;
	   $thisWeek=$this->ThisWeek;
	   $sql = "SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OrderQty,S.DeliveryWeek,S.StuffCname,S.Picture,S.scQty,S.scDate,
	              S.LineId,S.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(S.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals ,A.WorkShopId 
	              FROM (
                      SELECT  S.Id,S.sPOrderId,S.StockId,S.StuffId,S.Qty,M.CompanyId,H.LineId,SUM(C.Qty) AS scQty,MAX(C.Date) AS scDate,
                        (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture, D.CheckSign,D.Unit
						FROM qc_mission H
						INNER JOIN gys_shsheet S ON H.Sid=S.Id
						INNER JOIN gys_shmain M ON S.Mid=M.Id 
                        INNER JOIN qc_cjtj  C ON C.Sid=S.Id 
						LEFT JOIN cg1_stocksheet  G ON G.StockId=S.StockId 
                        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
						WHERE  H.rkSign=1 AND S.Estate='$Estate' AND S.SendSign<2 AND M.Floor='$Floor' AND C.Qty>0 AND D.ComboxSign!=1 
					    GROUP BY S.Id
                  )S 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=S.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN  stuffunit U ON U.Id=S.Unit 
			      WHERE  S.scQty>0  
             UNION ALL
                 SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OrderQty,S.DeliveryWeek,S.StuffCname,S.Picture,S.scQty,S.scDate,
	              S.LineId,S.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(S.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals,A.WorkShopId   
	              FROM (
                      SELECT  S.Id,S.sPOrderId,S.StockId,S.StuffId,S.Qty,M.CompanyId,H.LineId,SUM(C.Qty) AS scQty,MAX(C.Date) AS scDate,
                        (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture, D.CheckSign,D.Unit 
						FROM qc_mission H
						INNER JOIN gys_shsheet S ON H.Sid=S.Id
						INNER JOIN gys_shmain M ON S.Mid=M.Id 
                        INNER JOIN qc_cjtj  C ON C.Sid=S.Id 
                        LEFT JOIN cg1_stuffcombox MB ON MB.StockId=S.StockId 
			            LEFT JOIN cg1_stocksheet G ON G.StockId=MB.mStockId 
                        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
						WHERE  H.rkSign=1 AND S.Estate='$Estate' AND S.SendSign<2 AND M.Floor='$Floor' AND C.Qty>0 AND D.ComboxSign=1 
					    GROUP BY S.Id
                  )S 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=S.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN  stuffunit U ON U.Id=S.Unit  
			      WHERE  S.scQty>0  
			 ORDER BY scDate";//OR TIMESTAMPDIFF(minute,S.scDate,Now())>=30 
  
        $query=$this->db->query($sql);
		return $query->result_array();
	}
	
	
	function get_drk_blings($Floor) {
		$sql = "select count(*) as  Nums from (
SELECT SUM(IFNULL(C.Qty,0)) AS Qty,S.Qty as oQty 
				     FROM qc_cjtj C 
				     INNER JOIN gys_shsheet S ON C.Sid=S.Id 
				     INNER JOIN gys_shmain M ON S.Mid=M.Id 
				     INNER JOIN qc_badrecord B ON B.Sid=S.Id 
				     WHERE  C.Estate=1 and  M.Floor='$Floor'  GROUP BY S.Id ) A where A.oQty=A.Qty ;";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row()->Nums;
		}
		return 0;
	}
	//待入库数量(按楼层)
	function get_qcrk_counts($Floor){
		$sql ="SELECT  COUNT(*) AS Counts,SUM(IFNULL(A.Qty,0)) AS Qty FROM ( 
				     SELECT SUM(IFNULL(C.Qty,0)) AS Qty 
				     FROM qc_cjtj C 
				     INNER JOIN gys_shsheet S ON C.Sid=S.Id 
				     INNER JOIN gys_shmain M ON S.Mid=M.Id 
				     INNER JOIN qc_badrecord B ON B.Sid=S.Id 
				     WHERE  C.Estate=1 and  M.Floor='$Floor'  GROUP BY S.Id 
				)A ";
	   $query=$this->db->query($sql);
	   return $query->first_row('array'); 				
	}
	
	
	//待入库记录(按楼层)
	function get_qcrk_order($Floor){
	   $thisWeek=$this->ThisWeek;
	   $sql = "SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OrderQty,S.DeliveryWeek,S.StuffCname,S.Picture,S.scQty,S.scDate,
	              S.LineId,S.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(S.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals  ,A.WorkShopId 
	              FROM (
                      SELECT  S.Id,S.sPOrderId,S.StockId,S.StuffId,S.Qty,M.CompanyId,H.LineId,SUM(C.Qty) AS scQty,MAX(C.Date) AS scDate,
                        (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture, D.CheckSign,D.Unit
						FROM qc_cjtj  C 
						INNER JOIN gys_shsheet S ON C.Sid=S.Id
						INNER JOIN gys_shmain M ON S.Mid=M.Id 
						INNER JOIN qc_badrecord B ON B.Sid=S.Id  
						LEFT JOIN cg1_stocksheet  G ON G.StockId=S.StockId 
                        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
                        LEFT JOIN qc_mission H ON H.Sid=S.Id 
						WHERE  C.Estate=1  AND M.Floor='$Floor' AND D.ComboxSign!=1 
					    GROUP BY S.Id
                  )S 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=S.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN  stuffunit U ON U.Id=S.Unit 
			      WHERE  1   
             UNION ALL
                 SELECT S.Id,S.StockId,S.StuffId,S.Qty,S.OrderQty,S.DeliveryWeek,S.StuffCname,S.Picture,S.scQty,S.scDate,
	              S.LineId,S.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(S.DeliveryWeek<'$thisWeek',1,0) AS OveSign,U.Decimals  ,A.WorkShopId
	              FROM (
                      SELECT  S.Id,S.sPOrderId,S.StockId,S.StuffId,S.Qty,M.CompanyId,H.LineId,SUM(C.Qty) AS scQty,MAX(C.Date) AS scDate,
                        (G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture, D.CheckSign,D.Unit 
						FROM qc_cjtj  C 
						INNER JOIN gys_shsheet S ON C.Sid=S.Id 
						INNER JOIN gys_shmain M ON S.Mid=M.Id 
						INNER JOIN qc_badrecord B ON B.Sid=S.Id  
                        LEFT JOIN cg1_stuffcombox MB ON MB.StockId=S.StockId 
			            LEFT JOIN cg1_stocksheet G ON G.StockId=MB.mStockId 
                        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
                        LEFT JOIN qc_mission H ON H.Sid=S.Id  
						WHERE  C.Estate=1 AND M.Floor='$Floor'  AND D.ComboxSign=1 
					    GROUP BY S.Id
                  )S 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	              LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
			      LEFT JOIN trade_object B ON B.CompanyId=S.CompanyId  AND B.ObjectSign IN (1,3) 
			      LEFT JOIN  stuffunit U ON U.Id=S.Unit
			      WHERE  1  
			 ORDER BY scDate";
  
        $query=$this->db->query($sql);
		return $query->result_array();
	}
	
	
	//获取供应商送货单(按供应商/按状态)
	function get_sh_order($Floor,$CompanyId,$Estate)
	{
	   $thisWeek=$this->ThisWeek;
	   if ($CompanyId>=1000){
	       //供应商
		   $sql = "SELECT M.BillNumber,M.created,SUM(S.Qty) AS Qty,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0) AS OverQty,COUNT(1) AS Counts,U.Decimals   
			      FROM gys_shsheet S 
			       LEFT JOIN stuffdata D ON D.StuffId=S.StuffId
			       LEFT JOIN  stuffunit U ON U.Id=D.Unit
			      LEFT JOIN gys_shmain M ON M.Id=S.Mid
			      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      WHERE S.Estate='$Estate'  AND M.Floor='$Floor' AND M.CompanyId='$CompanyId' AND S.SendSign<2 
			      GROUP BY M.BillNumber ORDER BY BillNumber"; 
	   }else{
		 //半成品
		   $sql = "SELECT M.BillNumber,M.created,SUM(S.Qty) AS Qty,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0) AS OverQty,COUNT(1) AS Counts ,U.Decimals   
			      FROM gys_shsheet S 
			        LEFT JOIN stuffdata D ON D.StuffId=S.StuffId
			       LEFT JOIN  stuffunit U ON U.Id=D.Unit
			      LEFT JOIN gys_shmain M ON M.Id=S.Mid
			      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
			      WHERE S.Estate='$Estate'  AND M.Floor='$Floor' AND A.WorkShopId='$CompanyId' AND S.SendSign<2 
			      GROUP BY M.BillNumber ORDER BY BillNumber";  
	   }
			      
		$query=$this->db->query($sql);
		return $query->result_array();	      
	}
	
	//获取送货单明细(按送货单号)
    function get_billnumber_sheet($BillNumber,$Estate)
    {
        $thisWeek=$this->ThisWeek;
	    $sql = "SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture,
	              D.CheckSign,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0) AS OverQty,U.Decimals 
			      FROM  gys_shmain M 
			      LEFT JOIN gys_shsheet S ON M.Id=S.Mid
			      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			       LEFT JOIN  stuffunit U ON U.Id=D.Unit
			      WHERE M.BillNumber='$BillNumber' AND S.Estate='$Estate' AND S.SendSign<2 AND D.ComboxSign!=1 
			UNION ALL
               SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture,
	              D.CheckSign,IF(G.DeliveryWeek<'$thisWeek',S.Qty,0) AS OverQty,U.Decimals 
			      FROM  gys_shmain M 
			      LEFT JOIN gys_shsheet S ON M.Id=S.Mid
				  LEFT JOIN cg1_stuffcombox B ON B.StockId=S.StockId 
			      LEFT JOIN cg1_stocksheet G ON G.StockId=B.mStockId 
			      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
			        LEFT JOIN  stuffunit U ON U.Id=D.Unit
			      WHERE M.BillNumber='$BillNumber' AND S.Estate='$Estate'  AND S.SendSign<2 AND D.ComboxSign=1
			   ORDER BY DeliveryWeek";
			      
		$query=$this->db->query($sql);
	    return $query->result_array();		      
    }

		
	//生产工单送货记录保存
    function  save_scorder_shsheet($sPOrderId,$shQty,$Mid=0)
    {
	     $this->load->model('ScSheetModel');
	     $this->load->model('StuffdataModel');
	     $this->load->model('CkrksheetModel');
	     $this->load->model('oprationlogModel');
	     $this->load->model('CgstocksheetModel');
	     
	     $records=$this->ScSheetModel->get_records($sPOrderId);
	     $StockId=$records['mStockId'];
	     $StuffId=$records['StuffId'];
	     $cgQty  =$records['Qty'];
	        
	     
	     $stuffs   =$this->StuffdataModel->get_records($StuffId); 
	     $SendFloor=$stuffs['SendFloor'];
	     
	     $stocks   =$this->CgstocksheetModel->get_records($StockId); 
	     $CompanyId=$stocks['CompanyId'];
	     
	     $shedQty=$this->get_scorder_shqty($sPOrderId,$StockId);
	     $rkQty  =$this->CkrksheetModel->get_scorder_rkqty($sPOrderId,$StockId);
         
         $NoQty=$cgQty-$shedQty-$rkQty;
         
         $GysNumber=$this->get_new_gysnumber($CompanyId);
         $Log='';
         $OperationResult='N';
         
         //if ($shQty<=$NoQty){
         if ($NoQty>0){
	         $this->db->trans_begin();
              
             $remark='';
             //送货数量大于收货数量，多于部分转为备品
             if ($shQty>$NoQty){
                 $bpQty=$shQty-$bpQty;
                 $shQty=$NoQty;
                 $remark='含备品数量:' . $bpQty . ' pcs';
             }

             if ($Mid==0) {
	              $inRecode = array(
	                    'BillNumber'=>'0',
	                     'GysNumber'=>"$GysNumber",
	                     'CompanyId'=>"$CompanyId",
	                          'Date'=>$this->Date,
	                        'Remark'=>"$remark",
	                         'Floor'=>"$SendFloor",
	                      'Operator'=>$this->LoginNumber,
	                      'created'=>$this->DateTime,
	                       'creator'=>$this->LoginNumber
				          ); 
				          
				  $this->db->insert('gys_shmain',$inRecode); 
				  $Mid = $this->db->insert_id();
             }
             
             if ($Mid>0){
                                     
	              $addRecodes = array(
						         'Mid'=>"$Mid",
						   'sPOrderId'=>"$sPOrderId",
						     'StockId'=>"$StockId",
						     'StuffId'=>"$StuffId",
						         'Qty'=>"$shQty",
						    'SendSign'=>'0',
						      'Estate'=>'1',
						       'Locks'=>'1',
						       'created'=>$this->DateTime,
						       'creator'=>$this->LoginNumber,
						    'Operator'=>$this->LoginNumber
						   );
				  $this->db->insert('gys_shsheet', $addRecodes);
				  
				  if ($shQty>=$NoQty){
					  $this->ScSheetModel->update_estate($sPOrderId,0);
				  }
             }
             
			if ($this->db->trans_status() === FALSE || $Mid==0){
			    $this->db->trans_rollback();
			    $OperationResult = "N";
			    $Log="采购流水号:$StockId / $StuffId 送货记录($GysNumber)保存失败！";
			}
			else{
			    $this->db->trans_commit();
			    $OperationResult = "Y";
			    $Log="采购流水号:$StockId / $StuffId 送货记录($GysNumber)保存成功！";
			} 
        }
        else{
	        $Log="采购流水号:$StockId / $StuffId 不符合送货条件！送货数量: $shQty 未收数量:$NoQty";
        }
        
        $Logs=array('LogItem'=>'送货单','LogFunction'=>'新增','Log'=>$Log,'OperationResult'=>"$OperationResult");
        $this->oprationlogModel->save_item($Logs);	
        
        return $OperationResult;  
    }
    
    
    //更新送货单状态
	function set_estate($Ids,$Estate)
	{
	   if ($Estate==2){
		  $data=array(
	               'Estate'  =>$Estate,
	               'shDate'  =>$this->DateTime,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              ); 
	   }
	   else{
		  $data=array(
	               'Estate'  =>$Estate,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              ); 
	   }
	           
	   $this->db->update('gys_shsheet',$data, "Id IN ($Ids)");
	   
	   return $this->db->affected_rows();
   }
   
   
   function save_shdate($Ids){
	   
   }
   
    /*以下为旧代码*/
	public function kd_save($params=array()) {
	/*
		"add_img" = 1;
    cSign = 7;
    date = "2015-07-24";
    ordernumber = 1551545;
    provider = 2584;
    stuffs = "122313,:2040-0-0";
	*/
		 $this->load->model('oprationlogModel');
		 $Mid=0;$j=1;
		 $LogItem = "送货单";
		 $LogFunction = "新增";
		 $OperationResult = "N";
		 $Log = "";
		 
		 $addedImg = element('add_img',$params,0);
		 
		 $CompanyId2 = element('provider',$params,-1);
		 $dateToday = element('date',$params,$this->Date);
		$TempBillNumber = $ordernumber = element('ordernumber',$params,-1);
		 $allStuffs = element('stuffs',$params,'');
		 $checkArray = explode('|', $allStuffs);
		 $DateTime = $this->DateTime;
        $Lens=count($checkArray);
        for($i=0;$i<$Lens;$i++){
        
        
	        $ValueArray=explode(":",$checkArray[$i]);
	        $StuffId=$ValueArray[0];
	        $QtyArray=explode("-",$ValueArray[1]);
	        $SumQty=$QtyArray[0];
	        
	        //获取配件送货楼层
	        $floor = '';
	        
	        $floorResult =$this->db->query("Select SendFloor From stuffdata Where StuffId='$StuffId' LIMIT 1");
	        if ($floorResult->num_rows() > 0) {
	        	$floorResultRow = $floorResult->row_array();
		        $floor = $floorResultRow["SendFloor"];
	        }
	        
	        
	        if($SumQty>0){
	         //检查该配件全部未收货的记录
	          $checkSql=$this->db->query("
	           SELECT  S.Id,S.StockId,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryWeek,M.Date  
			           FROM cg1_stocksheet S
		           	   LEFT JOIN stuffdata D ON D.StuffId=S.StuffId
		           	   LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
			           WHERE 1 AND S.StuffId='$StuffId' AND S.rkSign>0 AND S.CompanyId='$CompanyId2' AND S.Mid>0  AND S.DeliveryWeek>0  
           UNION ALL 
               SELECT S.Id,M.StockId,  (M.AddQty + M.FactualQty) AS Qty,S.DeliveryWeek,SM.Date    
					   FROM  cg1_stuffcombox  M 
                       LEFT JOIN cg1_stocksheet S  ON S.StockId =M.mStockId
                        LEFT JOIN cg1_stockmain SM ON SM.Id=S.Mid  
					   WHERE 1 AND M.StuffId ='$StuffId' AND S.CompanyId='$CompanyId2' AND S.rkSign >0	AND S.Mid>0 AND S.DeliveryWeek>0 	   
	           ORDER BY DeliveryWeek,Date,Id");          
	          if($checkSql->num_rows() > 0){
		        foreach($checkSql->result_array() as $checkRow){
			       $StockId=$checkRow["StockId"];
			       $Qty=$checkRow["Qty"];
			       //已收货总数
			       $rkTemp=$this->db->query("SELECT SUM(Qty) AS Qty FROM ck1_rksheet 
			       WHERE StuffId='$StuffId' AND StockId=$StockId AND Type=1");
			       $rkQty = 0;
			       if ($rkTemp->num_rows() > 0) {
				       $rkTempRow = $rkTemp->row_array();
				       $rkQty = $rkTempRow["Qty"]==''?0:$rkTempRow["Qty"];
			       }

			       //待送货数量
			       $shSql=$this->db->query("SELECT SUM(Qty) AS Qty FROM gys_shsheet 
			       WHERE 1 AND Estate>0 AND StuffId=$StuffId AND StockId=$StockId"); 
			       $shQty = 0;
			       if ($shSql->num_rows() > 0) {
				       $shSqlRow  = $shSql->row_array();
				       $shQty = $shSqlRow["Qty"]==''?0:$shSqlRow["Qty"];
			       }
			       
			       //待入库数量
                   $drkSql=$this->db->query("SELECT SUM(Qty) AS Qty FROM qc_cjtj  
			       WHERE 1 AND Estate>0 AND StuffId=$StuffId AND StockId=$StockId"); 
			       $drkQty = 0;
			       if ($drkSql->num_rows() > 0) {
				       $drkRow  = $drkSql->row_array();
				       $drkQty = $drkRow["Qty"]==''?0:$drkRow["Qty"];
			       }

			       $NoQty=$Qty-$rkQty-$shQty-$drkQty;  //减掉未送货的单，省得出错

			      if($NoQty>0 && $SumQty>0){//该单未送完货
				      if($Mid==0){//如果没生成主送货单就先生成主送货单
				      $inRecode = array(
						      'BillNumber'=>"0",
						      'GysNumber'=>"$TempBillNumber",
						      'CompanyId'=>"$CompanyId2",
						      'Locks'=>'1',
						      'Date'=>"$DateTime",
						      'Remark'=>"",
						      'Floor'=>"$floor",
						      'Operator'=>$this->LoginNumber
				      );
				      
				      	$inAction = $this->db->insert('gys_shmain', $inRecode); 
					  	$Mid = $this->db->insert_id();
					    /*
 $inRecode="INSERT INTO gys_shmain (Id,BillNumber,CompanyId,Locks,Date,Remark,Floor) 
					      VALUES (NULL,'$TempBillNumber','$CompanyId2','1','$DateTime','$Remark','$floor')";
					      $inAction=$this->db->query($inRecode);
					      $Mid=mysql_insert_id();
*/
				       }
				   //分析：送货数量与该数量的比较
				   if($SumQty>=$NoQty && $Mid!=0){//可以全部送货
					    $SumQty-=$NoQty;
					   $Log.= "$j - 全部送货 $StockId - $NoQty \n";
					   $addRecodes = array(
					   'Mid'=>"$Mid",
					   'StockId'=>"$StockId",
					   'StuffId'=>"$StuffId",
					   'Qty'=>"$NoQty",
					   'SendSign'=>"0",
					   'Estate'=>"1",'Locks'=>"1",'Operator'=>$this->LoginNumber
					   );
					   $addAction = $this->db->insert('gys_shsheet', $addRecodes);
					$OperationResult = 'Y';
					    }
				    else{//部分送货
				    $Log.= "$j - 部分送货 $StockId - $SumQty \n";
				     $addRecodes = array(
					   'Mid'=>"$Mid",
					   'StockId'=>"$StockId",
					   'StuffId'=>"$StuffId",
					   'Qty'=>"$SumQty",
					   'SendSign'=>"0",
					   'Estate'=>"1",'Locks'=>"1",'Operator'=>$this->LoginNumber
					   );
					    $addAction = $this->db->insert('gys_shsheet', $addRecodes);
					 $OperationResult = 'Y';
					    break;//当该送货数量已经分配完，则跳出
					    }
					  }
			        $j++;
			      }
		        }
	         }  //if($SumQty>0)
	
	
	     //*****************************************************取得补货总数
	      $tmpBSQty=$QtyArray[2];  
	      //退货的总数量 
	      $thSql=$this->db->query("SELECT SUM( S.Qty ) AS thQty  FROM ck2_thmain M  
								   LEFT JOIN ck2_thsheet S ON S.Mid = M.Id
								   WHERE M.CompanyId = '$CompanyId2' AND S.StuffId = '$StuffId' ");
		   $thQty = 0;
		   if ($thSql->num_rows() > 0) {
		       $thSqlRow = $thSql->row_array();
		         
			   $thQty = $thSqlRow['thQty'];
		   }
	      //补货的数量 
	      $bcSql=$this->db->query("SELECT SUM( S.Qty ) AS bcQty  FROM ck3_bcmain M 
								   LEFT JOIN ck3_bcsheet S ON S.Mid = M.Id
								   WHERE M.CompanyId = '$CompanyId2' AND S.StuffId = '$StuffId' ");
	   $bcQty = 0;
	      if ($bcSql->num_rows() > 0) {
		       $bcSqlRow = $bcSql->row_array();
		         
			   $bcQty = $bcSqlRow['bcQty'];
		   }
	      //待送货数量
	      $shQty=0;
	    
		  $shSql=$this->db->query("SELECT SUM( S.Qty ) AS Qty FROM gys_shmain M
						LEFT JOIN gys_shsheet S ON S.Mid = M.Id
						WHERE 1 AND M.CompanyId = '$CompanyId2' 
						AND S.Estate>0 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')");
					
		 $shQty = 0;
	      if ($shSql->num_rows() > 0) {
		       $shSqlRow = $shSql->row_array();
		         
			   $shQty = $shSqlRow['Qty'];
		   }
	      //待入库数量
	      $drkQty=0;
		  $drkSql=$this->db->query("SELECT SUM(C.Qty) AS Qty FROM qc_cjtj C
                                       LEFT JOIN gys_shsheet S ON S.Id=C.Sid 
									   LEFT JOIN gys_shmain M ON M.Id=S.Mid
									   WHERE C.Estate>0 AND C.StuffId='$StuffId' AND M.CompanyId = '$CompanyId2' AND (S.StockId='-1' or S.SendSign='1')");
		   if ($drkSql->num_rows() > 0) {
				$drkRow  = $drkSql->row_array();
				$drkQty = $drkRow["Qty"]==''?0:$drkRow["Qty"];
		   }
	
	      $webQty=$thQty-$bcQty-$shQty-$drkQty; //未补数量	
	      //echo "$webQty=$thQty-$bcQty-$shQty"; //未补数量	
	      if($tmpBSQty>$webQty){
		      $tmpBSQty=$webQty;  //最多只能送未补数量
                         }
	     if($tmpBSQty>0 ) {     //
		     if($Mid==0){//如果没生成主送货单就先生成主送货单
		     
		     $inRecode = array(
		     'BillNumber'=>"$TempBillNumber",
		     'CompanyId'=>"$CompanyId2",
		     'Locks'=>"1",
		     'Date'=>"$DateTime",
		     'Remark'=>'',
		     'Floor'=>"$floor",
		     'Operator'=>$this->LoginNumber);
		      $inAction = $this->db->insert('gys_shmain', $inRecode);
			    $Mid = $this->db->insert_id();
		      }
		   if($Mid!=0){//可以全部补货
		    $addRecodes = array(
		     'Mid'=>"$Mid",
		     "StockId"=>"-1",
		     'StuffId'=>"$StuffId",
		     'Locks'=>"1",
		     'Estate'=>'1',
		     'SendSign'=>'1',
		     'Qty'=>"$tmpBSQty",'Operator'=>$this->LoginNumber);
		     
		      $addAction = $this->db->insert('gys_shsheet', $addRecodes);
			 $OperationResult = 'Y';
		      }
		       		
	    }
	  //*******************************************取得备品总数
	  $tmpBPQty=$QtyArray[1];  
	  if($tmpBPQty>0) {     //
		if($Mid==0){
		 $inRecode = array(
		     'BillNumber'=>"$TempBillNumber",
		     'CompanyId'=>"$CompanyId2",
		     'Locks'=>"1",
		     'Date'=>"$DateTime",
		     'Remark'=>'',
		     'Floor'=>"$floor",'Operator'=>$this->LoginNumber);
		     
		        $inAction = $this->db->insert('gys_shmain', $inRecode);
			    $Mid = $this->db->insert_id(); 
		   }
		if($Mid!=0){//可以全部补货
		 $addRecodes = array(
		     'Mid'=>"$Mid",
		     "StockId"=>"-2",
		     'StuffId'=>"$StuffId",
		     'Locks'=>"1",
		     'Estate'=>'1',
		     'SendSign'=>'2',
		     'Qty'=>"$tmpBPQty",'Operator'=>$this->LoginNumber);
		      $addAction = $this->db->insert('gys_shsheet', $addRecodes);
		      $OperationResult = 'Y';
		         }				
	        }
         }
            
         //  $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
           
           if ($addedImg==1) {
	           	 // 上传文件配置放入config数组
			        $config['upload_path'] = '../download/ckshbill';
			        $config['allowed_types'] = 'gif|jpg|png';
			        $config['max_size'] = '1024000';
			         $config['max_width']  = '1024000';
  $config['max_height']  = '10240000';
			        $config['file_name'] = "S".$Mid;
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
			           
			           if ($filenames!=""){
				               
								$OperationResult = 'Y'; 
			           }
		          }

           }
           
           
         //上传文件
        $this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log,'OperationResult'=>"$OperationResult"));	
        
        return $OperationResult;	
	}

	//所有收料 所有已到达
	public function get_all_reach() {
		
		$sql = "select sum(S.Qty) Qty from gys_shsheet S where S.Estate = 2 and  S.SendSign IN (0,1) ";
		$query = $this->db->query($sql);
		return  $query;
	}
	
	//所有待到达
	public function get_all_willreach() {
		
		$sql = "select sum(S.Qty) Qty from gys_shsheet S where S.Estate = 1 and  S.SendSign IN (0,1) ";
		$query = $this->db->query($sql);
		return  $query;
	}
	
	//供应商送货
	public function get_all_willreach_over() {
		
     $sql = "SELECT  COUNT(*) AS Nums,SUM(S.Qty) AS shQty,
		     SUM(IF(YEARWEEK(G.DeliveryDate,1)<YEARWEEK(CURDATE(),1),S.Qty,0)) AS OverQty  
		     FROM    gys_shsheet S 
		     LEFT JOIN    cg1_stocksheet G ON G.StockId=S.StockId 
		     WHERE S.Estate=1  AND S.SendSign IN(0,1) ";  
     return $this->db->query($sql);
	}
	
	
	//差最后一个配的数量 供应商送货
	public function get_all_lastqty() {
	  $LastQty=0;
     $sql = "SELECT S.StuffId,S.StockId,S.Qty 
	     FROM   gys_shsheet S 
	     WHERE  S.Estate=1  AND S.SendSign=0";  
     
     $query= $this->db->query($sql);
      $this->load->model('ck9stocksheetModel');
     foreach ($query->result_array() as $LastBlRow) {
	        $StuffId=$LastBlRow["StuffId"];
            $StockId=$LastBlRow["StockId"];
            $POrderId=substr($StockId,0,12);
            $Qty=$LastBlRow["Qty"];
            $checkSignAndColor= $this->ck9stocksheetModel->stuff_blcheck('',$StuffId,$POrderId);
			$LastBlSign = $checkSignAndColor[0];
		       if ($LastBlSign==1) $LastQty+=$Qty;	
	     
     }
     return $LastQty;
	}
	
	//来料品检
	public function get_all_qc($useCondiction="") {
		
     $sql = "SELECT  COUNT(*) AS Nums,SUM(S.Qty) AS shQty,
     SUM(IF(YEARWEEK(G.DeliveryDate,1)<YEARWEEK(CURDATE(),1),S.Qty,0)) AS OverQty  
     FROM    gys_shsheet S 
     LEFT JOIN gys_shmain M ON S.Mid=M.Id 
     LEFT JOIN    cg1_stocksheet G ON G.StockId=S.StockId 
     WHERE S.Estate=1  AND S.SendSign IN(0,1) $useCondiction  
     AND NOT EXISTS(SELECT L.Id FROM qc_mission L WHERE L.Sid=S.Id )";  
     return $this->db->query($sql);
	}
	
	
	//差最后一个配的数量 
	public function get_all_lastqty_con($useCondiction="") {
		  $LastQty=0;
     $sql = "SELECT S.StuffId,S.StockId,S.Qty  FROM gys_shsheet S
     LEFT JOIN stuffdata D ON D.StuffId=S.StuffId  
      LEFT JOIN gys_shmain M ON S.Mid=M.Id 
     LEFT JOIN cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0 
     WHERE  S.Estate=2  AND S.SendSign=0  AND GL.StockId IS NULL $useCondiction 
     AND NOT EXISTS(SELECT L.Id FROM qc_mission L WHERE L.Sid=S.Id )";  
     
     $query= $this->db->query($sql);
      $this->load->model('ck9stocksheetModel');
     foreach ($query->result_array() as $LastBlRow) {
	        $StuffId=$LastBlRow["StuffId"];
            $StockId=$LastBlRow["StockId"];
            $POrderId=substr($StockId,0,12);
            $Qty=$LastBlRow["Qty"];
            $checkSignAndColor= $this->ck9stocksheetModel->stuff_blcheck('',$StuffId,$POrderId);
			$LastBlSign = $checkSignAndColor[0];
		       if ($LastBlSign==1) $LastQty+=$Qty;	
	     
     }
     return $LastQty;
	}
	

	
	
	/*
		   //差最后一个配件
     $LastQty=0;
     $LastBlResult=mysql_query("SELECT S.StuffId,S.StockId,S.Qty  FROM gys_shsheet S 
     LEFT JOIN gys_shmain M ON S.Mid=M.Id 
     WHERE  S.Estate=1  AND M.Floor='$Floor' AND S.SendSign=0",$link_id);
     while ($LastBlRow = mysql_fetch_array($LastBlResult)){
            $StuffId=$LastBlRow["StuffId"];
            $StockId=$LastBlRow["StockId"];
            $POrderId=substr($StockId,0,12);
            $Qty=$LastBlRow["Qty"];
            include "../../model/subprogram/stuff_blcheck.php";
            if ($LastBlSign==1) $LastQty+=$Qty;
            //if ($LoginNumber==10868) echo $LastQty;
    }

	*/
		 
		 
    public function get_item_usestockid($StockId) {
		$sql = "select S.* from gys_shsheet S where S.StockId=? ";
		$query = $this->db->query($sql,$StockId);
		return $query;
	}
	
	
	
		 
}