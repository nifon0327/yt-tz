<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  YwOrderSheetModel extends MC_Model{

	
    
    function __construct()
    {
	    /*
		    $config['ash_in_supplier'] = '2270,100300';

//半成品主类别
$config['ash_semi_maintype'] = '7';

//验厂模式
	    */
	    
		$this->semi_maintype   = $this->config->item('ash_semi_maintype');
		$this->ash_in_supplier = $this->config->item('ash_in_supplier');
        parent::__construct();
    }
    
     
    
    // 新单一天内某companyid下 锁单单数统计 小锁
    function check_lock_date_company_groups($date, $companyid, $Month = '', $by='') {
	    

	    $condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    if ($date!='') {
		    $condition .= " and  M.OrderDate='$date'";
	    }
	    
	    
	     $month = "''";
	     $monthCompany = "";
	     
	    $groupstr = '';
	   
	    if ($by == 'com') {
		    $groupstr = 'group by M.CompanyId';
		    $monthCompany = ' ,M.CompanyId  ';
		    ;
	    } else if ($by == 'month') {
		    
		    
		    $month = "DATE_FORMAT(M.OrderDate,'%Y-%m')";
		    $groupstr = "group by DATE_FORMAT(M.OrderDate,'%Y-%m')";
	    }else if ($by == 'date') {
		    
		    
		    $month = "M.OrderDate ";
		    $groupstr = "group by M.OrderDate ";
	    }

	    
	    $sql = "SELECT count(*) AS Locks,0 AS gLocks ,$month as Month $monthCompany 
		FROM cg1_stocksheet G 
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
        LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId
		WHERE  1 $condition AND GL.Locks=0   $groupstr ";
		$query=$this->db->query($sql);
		if($query->num_rows() > 0){
			return  $query->result_array();
		}
		return array();
    }
    
    
     // 新单一个月内 成本统计
    function order_mon_cost_groups($Month, $companyid='',$by='',$OrderDate='') {
	    $condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($OrderDate!='') {
		    $condition .= " AND M.OrderDate='$OrderDate' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	     $month = "''";
	     $monthCompany = "";
	     
	    $groupstr = '';
	   
	    if ($by == 'com') {
		    $groupstr = 'group by M.CompanyId';
		    $monthCompany = ' ,M.CompanyId  ';
		    ;
	    } else if ($by == 'month') {
		    
		    
		    $month = "DATE_FORMAT(M.OrderDate,'%Y-%m')";
		    $groupstr = "group by DATE_FORMAT(M.OrderDate,'%Y-%m')";
	    }else if ($by == 'date') {
		    
		    
		    $month = "M.OrderDate ";
		    $groupstr = "group by M.OrderDate ";
	    }
 
		$ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
	    $sql = " SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost ,$month as Month $monthCompany
		        FROM yw1_ordermain M
                LEFT JOIN yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE 1 $condition and  A.Level=1  $groupstr";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return  $query->result_array();
		}
		return  array();
		
    }
    

    
    // 新单一月内 没有利润的单 金额统计 _groups
	public function noprof_month_amount_groups($Month, $companyid='',$by='',$OrderDate='') {
		$condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    
	    if ($OrderDate!='') {
		    $condition .= " AND M.OrderDate='$OrderDate' ";
	    }
	     $month = "''";
	     $monthCompany = "";
	    
	    $groupstr = '';
	   
	    if ($by == 'com') {
		    $groupstr = 'group by C.CompanyId';
		    $monthCompany = ' ,C.CompanyId  ';
		    ;
	    } else if ($by == 'month') {
		    
		    
		    $month = "DATE_FORMAT(A.OrderDate,'%Y-%m')";
		    $groupstr = "group by DATE_FORMAT(A.OrderDate,'%Y-%m')";
	    }else if ($by == 'date') {
		    
		    
		    $month = "A.OrderDate ";
		    $groupstr = "group by A.OrderDate ";
	    }

	    
		$sql = "SELECT SUM(A.Qty*A.Price*D.Rate) AS Amount, COUNT(*) as Nums,$month as Month $monthCompany  
		FROM    (
         SELECT S.POrderId,S.Qty,S.Price,M.CompanyId,M.OrderDate    
	        FROM  yw1_ordermain M  
	        LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	        LEFT JOIN cg1_stocksheet G  ON S.POrderId=G.POrderId
			LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
			LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
			LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
			WHERE  1 $condition   and S.Estate>0   and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
	        group by S.POrderId
    ) A
	LEFT JOIN trade_object C ON C.CompanyId=A.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency  $groupstr";
	$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return  $query->result_array();

		}
		return  array();
	}

    
    // 新单一个月内 没有利润的单 成本统计 groups 
	public function noprof_month_cost_groups($Month, $companyid='',$by='',$OrderDate='') {
		
		$condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    
	    if ($OrderDate!='') {
		    $condition .= " AND M.OrderDate='$OrderDate' ";
	    }
	     $month = "''";
	     $monthCompany = "";
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
		    
	    }
	    $groupstr = '';
	   
	    if ($by == 'com') {
		    $groupstr = 'group by B.CompanyId';
		    $monthCompany = ' ,B.CompanyId  ';
		    ;
	    } else if ($by == 'month') {
		    
		    
		    $month = "DATE_FORMAT(B.OrderDate,'%Y-%m')";
		    $groupstr = "group by DATE_FORMAT(B.OrderDate,'%Y-%m')";
	    }else if ($by == 'date') {
		    
		    
		    $month = "B.OrderDate ";
		    $groupstr = "group by B.OrderDate ";
	    }
	    
	    $ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
	    
		$sql = "SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND P.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost,$month as Month $monthCompany
 FROM    (
			SELECT S.POrderId,S.Qty,S.Price,M.CompanyId,M.OrderDate  
	         FROM  yw1_ordermain M  
	        LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	        LEFT JOIN cg1_stocksheet G  ON S.POrderId=G.POrderId
			LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
			LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
			LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
			WHERE  1 $condition  and S.Estate>0   and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
	        group by S.POrderId) B
   LEFT JOIN cg1_stocksheet A  ON B.POrderId=A.POrderId 
	LEFT JOIN trade_object P ON A.CompanyId=P.CompanyId
	 LEFT JOIN currencydata C ON P.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 $groupstr";
		     
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return  $query->result_array();

		}
		return  array();
	}

    
    function get_order_deliveryweek($porderid) {
	    
	    $sql = "
	    
	    	SELECT IFNULL(PI.LeadWeek,PL.LeadWeek)  AS Weeks 
	    	FROM yw1_ordersheet S
	    	LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
            LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
            WHERE  S.POrderId=$porderid 
	    ";
	    $query=$this->db->query($sql);
	    if ($query->num_rows() > 0) {
		    return $query->row()->Weeks;
	    }
	    
	    return '';
    }
    
    function notout_orderlist($Weeks, $companyid) {
	    
	    $condition = '';
	    if ($Weeks!='') {
		    
		    if ($Weeks=='TBC') {
		        $condition = " AND  IFNULL(PI.LeadWeek,0)=0 AND  IFNULL(PL.LeadWeek,0)=0  ";
		    } else {
			    $condition = " AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$Weeks' ";
		    }
	    } 
	    if ($companyid != '') {
		     $condition .= " AND  M.CompanyId=$companyid  ";
	    }

	    
	    $sql = "SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,S.Estate,S.scFrom,P.ProductId,P.cName,P.TestStandard,C.Forshort,PI.Leadtime,IFNULL(PI.LeadWeek,PL.LeadWeek)  AS Weeks,S.PackRemark ,D.PreChar,SC.sPOrderId  
			FROM yw1_ordermain M
			LEFT JOIN yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
			LEFT JOIN yw1_scsheet SC ON SC.POrderId = S.POrderId
            LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
            LEFT JOIN currencydata D ON D.Id = C.Currency  
            LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId  
            LEFT JOIN productdata P ON P.ProductId=S.ProductId
		    WHERE  S.Estate=1  $condition GROUP BY S.POrderId ORDER BY CompanyId,Leadtime  ";
		    
		    
		 $query=$this->db->query($sql);
	    
	    return $query;
    }
    
    function order_remark($POrderId,$mModuleId='') {
	    
/*
 功能模块:订单备注显示
 传入参数:$POrderId
 输出参数:$Remark,$RemarkDate,$RemarkOperator;
 */

 	if ($POrderId!=""){
		//客人页面，显示英文备注
		 if ($mModuleId=="Client"){
		    $RemarkResult=$this->db->query("SELECT S.Remark,S.Date,M.Name FROM yw2_orderremark S 
		       LEFT JOIN staffmain  M ON M.Number=S.Operator 
		      WHERE S.POrderId='$POrderId' AND S.Type=1 ORDER BY S.Id DESC LIMIT 1");
		    if($RemarkResult->num_rows() > 0){
	            return $RemarkResult->row_array();
		    }
		}
		else{

			$RemarkResult=$this->db->query("SELECT S.Remark,S.Date,M.Name FROM yw2_orderremark S 
		    LEFT JOIN staffmain  M ON M.Number=S.Operator 
		    WHERE S.POrderId='$POrderId' AND S.Type=2  ORDER BY S.Id DESC LIMIT 1");
		    if($RemarkResult->num_rows() > 0){
		        return $RemarkResult->row_array();
		    }
		   
			 
		}
	}
	return null;
}
    
    //已备料数量
    function get_bled_qty_id($POrderId) {
	     $condition = " AND S.POrderId=$POrderId ";
	    
	    $sql = "
SELECT SUM(A.Qty) AS Qty FROM (
							SELECT M.CompanyId,S.Id,S.POrderId,S.ProductId,(S.Qty-S.shipQty) AS Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty 
							FROM (
							   SELECT S.Id,S.POrderId,S.OrderNumber,S.ProductId,S.Estate,S.Qty,S.Price,
							       SUM(IFNULL(C.Qty,0)) AS shipQty 
					               FROM yw1_ordersheet S 
					               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
					               WHERE S.scFrom>0 $condition GROUP BY S.POrderId
					        )S  
							LEFT JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber
							LEFT JOIN cg1_stocksheet G ON G.POrderId=S.POrderId
							LEFT JOIN ck9_stocksheet K ON K.StuffId=G.StuffId
							LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
							LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
							LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id   
						    LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
							LEFT JOIN (
										 SELECT L.StockId,SUM(L.Qty) AS Qty 
										 FROM yw1_ordersheet S 
										 LEFT JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
										 LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
										 LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
										 LEFT JOIN cg1_stocksheet G ON S.POrderId=G.POrderId
										 LEFT JOIN ck5_llsheet L ON G.StockId=L.StockId 
										 WHERE  S.scFrom>0 AND S.Estate=1 $condition  GROUP BY L.StockId
									 ) L ON L.StockId=G.StockId
							WHERE  ST.mainType<2  $condition   
							AND NOT EXISTS(SELECT T.StuffId FROM stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
							 GROUP BY S.POrderId 
							) A  WHERE  A.blQty=A.llQty;";
							
		$query=$this->db->query($sql);
	    
	    if ($query->num_rows() > 0) {
		    return $query->row()->Qty;
	    }
	    return 0;

    }
    
    function get_bled_qty_week($Weeks='', $companyid='') {
	    $condition = " AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$Weeks' ";
	    if ($Weeks=='TBC') {
		    $condition = " AND  IFNULL(PI.LeadWeek,0)=0 AND  IFNULL(PL.LeadWeek,0)=0  ";
	    }
	    
	    
	    if ($companyid != '') {
		     $condition .= " AND  M.CompanyId=$companyid  ";
	    }
	    $sql = "
SELECT SUM(A.Qty) AS Qty FROM (
							SELECT M.CompanyId,S.Id,S.POrderId,S.ProductId,(S.Qty-S.shipQty) AS Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty 
							FROM (
							   SELECT S.Id,S.POrderId,S.OrderNumber,S.ProductId,S.Estate,S.Qty,S.Price,
							       SUM(IFNULL(C.Qty,0)) AS shipQty 
					               FROM yw1_ordersheet S 
					               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
					               WHERE S.scFrom>0 GROUP BY S.POrderId
					        )S  
							LEFT JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber
							LEFT JOIN cg1_stocksheet G ON G.POrderId=S.POrderId
							LEFT JOIN ck9_stocksheet K ON K.StuffId=G.StuffId
							LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
							LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
							LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id   
						    LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
							LEFT JOIN (
										 SELECT L.StockId,SUM(L.Qty) AS Qty 
										 FROM yw1_ordersheet S 
										 LEFT JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
										 LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
										 LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
										 LEFT JOIN cg1_stocksheet G ON S.POrderId=G.POrderId
										 LEFT JOIN ck5_llsheet L ON G.StockId=L.StockId 
										 WHERE  S.scFrom>0 AND S.Estate=1 $condition  GROUP BY L.StockId
									 ) L ON L.StockId=G.StockId
							WHERE  ST.mainType<2  $condition   
							AND NOT EXISTS(SELECT T.StuffId FROM stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
							 GROUP BY S.POrderId 
							) A  WHERE  A.blQty=A.llQty;";
							
		$query=$this->db->query($sql);
	    
	    if ($query->num_rows() > 0) {
		    return $query->row()->Qty;
	    }
	    return 0;
    }
      //未出订单按week 统计
    function get_notout_weeks($companyid='',$weeks='') 
    {
	    $SearchRows= '';
	    
	    if ($companyid != '') {
		    $SearchRows = "AND M.CompanyId=$companyid";
	    } 
	    $WeekRows = '';
	    
	    if ($weeks != '') {
		    $WeekRows= " AND  IFNULL(PI.LeadWeek,PL.LeadWeek)='$weeks' ";
	    } 
	    
	    $sql = "SELECT IFNULL(PI.LeadWeek,PL.LeadWeek) AS Weeks,Count(*) AS Counts,SUM(S.Qty-S.shipQty -ifnull(B.rkQty-B.shipQty,0) ) AS Qty,SUM(IF(S.Estate>1,(S.Qty-S.shipQty -ifnull(B.rkQty-B.shipQty,0) ),0) ) AS WaitQty, SUM( S.Price * (S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0)  )* D.Rate ) AS Amount,SUM(IF(E.Type=2,(S.Qty-S.shipQty -ifnull(B.rkQty-B.shipQty,0) ),0) ) AS LockQty 
	     FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM yw1_ordersheet S 
               LEFT JOIN yw1_ordermain M ON S.OrderNumber = M.OrderNumber 
               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate=1 $SearchRows GROUP BY S.POrderId
         )S  
         
        LEFT JOIN (
					SELECT  A.POrderId,A.rkQty,A.rkDate,SUM(IFNULL(C.Qty,0)) AS shipQty 
					FROM (
					    SELECT S.POrderId,SUM(R.Qty) AS rkQty,Max(R.Date) AS rkDate 
					    FROM yw1_ordersheet S 
					    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber  
					    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
					    WHERE S.Estate>0  $SearchRows GROUP BY S.POrderId 
					)A 
					LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
					GROUP BY A.POrderId
				) B on B.POrderId=S.POrderId  and B.rkQty>B.shipQty 
	     LEFT JOIN yw1_ordermain M ON S.OrderNumber = M.OrderNumber
	     LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
	     LEFT JOIN currencydata D ON D.Id = C.Currency
	     LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id  
	     LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
	     LEFT JOIN yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
	    WHERE 1  $SearchRows $WeekRows  GROUP BY IFNULL(PI.LeadWeek,PL.LeadWeek)  ORDER BY Weeks ;";
	    $query=$this->db->query($sql);
	    
	    return $query;
    }
    
    //未出逾期金额 按comapnyid 统计
    function get_overnotout_companys($limitnums='') {
	     if ($limitnums != '') {
		    $limitnums = 'limit  '.$limitnums;
	    }
	    
	    $sql = "
	   SELECT M.CompanyId,C.Forshort, SUM( S.Price * (S.Qty-S.shipQty)* D.Rate ) AS Amount 
	     FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM yw1_ordersheet S 
               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate=1 GROUP BY S.POrderId
         )S  
	     LEFT JOIN yw1_ordermain M ON S.OrderNumber = M.OrderNumber
	     LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
	     LEFT JOIN currencydata D ON D.Id = C.Currency
LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id  
	     LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
	     LEFT JOIN yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 WHERE 
 IFNULL(PI.LeadWeek,PL.LeadWeek)<YEARWEEK(NOW(),1) AND  IFNULL(PI.LeadWeek,PL.LeadWeek)>0 group by M.CompanyId  Order by Amount desc  $limitnums;
	    ";
	    
	    $query=$this->db->query($sql);
	    
	    return $query;

    }
    
    
     //成品金额 按comapnyid 统计
    function get_waitcp_companys($limitnums = '', $readDetail='') {
	    if ($limitnums != '') {
		    $limitnums = 'limit  '.$limitnums;
	    }
	    
	     $sql = "
	    SELECT B.CompanyId,P.Forshort,
               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS Amount  
		 FROM(
			SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty
			FROM (
			    SELECT M.CompanyId,S.POrderId,S.Qty,S.Price,SUM(R.Qty) AS rkQty,MAX(R.Date) AS rkDate   
			    FROM yw1_ordersheet S 
			    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
			    WHERE S.Estate>0  GROUP BY S.POrderId 
			)A 
			LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
			GROUP BY A.POrderId
		) B 
		INNER JOIN  trade_object P ON P.CompanyId=B.CompanyId 
		INNER JOIN  currencydata D ON D.Id=P.Currency 
		LEFT  JOIN  staffmain M ON M.Number=Staff_Number 
		WHERE B.rkQty>B.shipQty group by B.CompanyId order by Amount desc $limitnums;
	    ";
	    

	    
	    if ($readDetail=='1') {
		    $thisDate = $this->Date;
		    $overdate=date("Y-m-d",strtotime("$thisDate  -11 days"));
		    
		    $sql = "SELECT B.CompanyId,P.Logo,P.Forshort,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS tStockQty,
               SUM(IF (B.overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(OverSign) AS OverCounts,
               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS sAmount ,
               SUM((B.rkQty-B.shipQty)*B.Price) AS Amount,D.PreChar  ,
               SUM(IF(B.overSign=1,(B.rkQty-B.shipQty)*B.Price,0)) AS OverAmount   
		 FROM(
			SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty,
			        if( rkDate<'$overdate',1,0)  AS OverSign  
			FROM (
			    SELECT M.CompanyId,S.POrderId,S.Qty,S.Price,SUM(R.Qty) AS rkQty,MAX(R.Date) AS rkDate   
			    FROM yw1_ordersheet S 
			    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
			    WHERE S.Estate>0  GROUP BY S.POrderId 
			)A 
			LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId GROUP BY A.POrderId
		) B 
		INNER JOIN  trade_object P ON P.CompanyId=B.CompanyId 
		INNER JOIN  currencydata D ON D.Id=P.Currency 
		LEFT  JOIN  staffmain M ON M.Number=Staff_Number 
		WHERE B.rkQty>B.shipQty  group by B.CompanyId order by sAmount desc";
	    }
	    
	   	$query=$this->db->query($sql);
	    
	    return $query;
    }

    
    //成品数量金额 超10天 统计
    function get_waitcp_sum() {
	    
	    $thisDate = $this->Date;
	    $overdate=date("Y-m-d",strtotime("$thisDate  -11 days"));
	    $sql = "
	    SELECT B.CompanyId,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS tStockQty,
               SUM(IF (B.overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(OverSign) AS OverCounts,
               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS Amount, 
               SUM(IF(B.overSign=1,(B.rkQty-B.shipQty)*B.Price*D.Rate,0)) AS OverAmount   
		 FROM(
			SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty,
			        if( rkDate<'$overdate',1,0)  AS OverSign  
			FROM (
			    SELECT M.CompanyId,S.POrderId,S.Qty,S.Price,SUM(R.Qty) AS rkQty,MAX(R.Date) AS rkDate   
			    FROM yw1_ordersheet S 
			    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
			    WHERE S.Estate>0  GROUP BY S.POrderId 
			)A 
			LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
			GROUP BY A.POrderId
		) B 
		INNER JOIN  trade_object P ON P.CompanyId=B.CompanyId 
		INNER JOIN  currencydata D ON D.Id=P.Currency 
		LEFT  JOIN  staffmain M ON M.Number=Staff_Number 
		WHERE B.rkQty>B.shipQty ;

	    ";
	    
	    $query=$this->db->query($sql);
	    
	    if ($query->num_rows() > 0) {
		    return $query->row_array();
	    }
	    return null;
    }
    
    
    
    
    //未出统计 未完成部分
    function get_notout_sum($overweekSign='') {
	    
	    if ($overweekSign!='') {
		    //逾期统计
		    $overweekSign = '
		 LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id  
	     LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
	     LEFT JOIN yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 WHERE 
 IFNULL(PI.LeadWeek,PL.LeadWeek)<YEARWEEK(NOW(),1) AND  IFNULL(PI.LeadWeek,PL.LeadWeek)>0 ';
	    }
	    
	    $sql = "SELECT Count(*) AS Counts,SUM(S.Qty-S.shipQty) AS Qty, SUM( S.Price * (S.Qty-S.shipQty)* D.Rate ) AS Amount 
	     FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM yw1_ordersheet S 
               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate=1 GROUP BY S.POrderId
         )S  
	     LEFT JOIN yw1_ordermain M ON S.OrderNumber = M.OrderNumber
	     LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
	     LEFT JOIN currencydata D ON D.Id = C.Currency
	     $overweekSign ";
	    $query=$this->db->query($sql);
	    
	    if ($query->num_rows() > 0) {
		    return $query->row_array();
	    }
	    return null;
    }
    
    function notout_week_companys($Weeks) {
	    
	    $overSelect = '';
	    $condition = " AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$Weeks' ";
	    if ($Weeks=='TBC') {
		    $condition = " AND  IFNULL(PI.LeadWeek,0)=0 AND  IFNULL(PL.LeadWeek,0)=0  ";
	    } else if ($Weeks=='') {
		    $condition = '';
		    
		    $overSelect = ", sum(if( IFNULL(PI.LeadWeek,PL.LeadWeek)<YEARWEEK(NOW(),1) AND  IFNULL(PI.LeadWeek,PL.LeadWeek)>0 ,1,0)) AS OverCounts
, sum(if( IFNULL(PI.LeadWeek,PL.LeadWeek)<YEARWEEK(NOW(),1) AND  IFNULL(PI.LeadWeek,PL.LeadWeek)>0 ,S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0),0)) AS OverQty
, sum(if( IFNULL(PI.LeadWeek,PL.LeadWeek)<YEARWEEK(NOW(),1) AND  IFNULL(PI.LeadWeek,PL.LeadWeek)>0 ,(S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0))*S.Price,0)) AS OverAmount,SUM(S.Qty*S.Price*D.Rate) AS realAmount
";
	    }
	    $sql = "SELECT M.CompanyId,COUNT(*) AS Counts,SUM(S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0)) AS Qty,SUM((S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0))*S.Price) AS Amount,
            SUM((S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0))*S.Price*D.Rate) AS RmbAmount,C.Forshort,C.Logo,D.PreChar,
            SUM(IF(E.Type=2,1,0)) AS Locks,SUM(IF(E.Type=2,(S.Qty-S.shipQty-ifnull(B.rkQty-B.shipQty,0)),0)) AS LockQty 
            
            $overSelect
            
			FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Estate,S.Qty,S.Price,SUM(IFNULL(C.Qty,0)) AS shipQty  
			  
               FROM yw1_ordersheet S 
               LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate=1 GROUP BY S.POrderId
            )S  
            
            left join (
					SELECT  A.POrderId,A.rkQty,A.rkDate,SUM(IFNULL(C.Qty,0)) AS shipQty 
					FROM (
					    SELECT S.POrderId,SUM(R.Qty) AS rkQty,Max(R.Date) AS rkDate 
					    FROM yw1_ordersheet S 
					    
					    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber  
					    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
					    
					    LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId  
             
					    WHERE S.Estate>0  $condition GROUP BY S.POrderId 
					)A 
					LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
					GROUP BY A.POrderId
				) B on B.POrderId=S.POrderId  and B.rkQty>B.shipQty
			LEFT JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId  
             LEFT JOIN currencydata D ON D.Id=C.Currency 
             LEFT JOIN yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
		    WHERE S.Estate=1  $condition  GROUP BY M.CompanyId ORDER BY RmbAmount DESC;";
		    
	    $query=$this->db->query($sql);
		return  $query;

    }
    
    // 相同产品的成品单列表
    function get_wait_cp($ProudctId,$companyid='') {
	    
	    $condition = " and S.ProductId=$ProudctId  ";
	    
	    if ($companyid != '') {
		    $condition = " and M.CompanyId =$companyid  ";
	    }
	    
	    $sql = "
SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,
          (S.Qty-B.shipQty) AS Qty,S.Price,(B.rkQty-B.shipQty) AS tStockQty,B.rkDate,
          S.ShipType,S.Estate,S.scFrom,P.cName,P.TestStandard,C.Forshort,D.PreChar, 
          PI.Leadtime,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks,P.TestStandard      
				 FROM(
					SELECT  A.POrderId,A.rkQty,A.rkDate,SUM(IFNULL(C.Qty,0)) AS shipQty 
					FROM (
					    SELECT S.POrderId,SUM(R.Qty) AS rkQty,Max(R.Date) AS rkDate 
					    FROM yw1_ordersheet S 
					    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber  
					    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
					    WHERE S.Estate>0  $condition GROUP BY S.POrderId 
					)A 
					LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
					GROUP BY A.POrderId
				) B 
                INNER JOIN yw1_ordersheet S ON S.POrderId=B.POrderId
                INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber  
				INNER JOIN  trade_object C ON C.CompanyId=M.CompanyId 
				INNER JOIN  currencydata D ON D.Id=C.Currency 
                INNER JOIN  productdata P ON P.ProductId=S.ProductId
                LEFT JOIN  yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
				LEFT JOIN  yw3_pisheet PI ON PI.oId=S.Id 
                LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId 
				WHERE B.rkQty>B.shipQty Order By rkDate Desc;";
		$query=$this->db->query($sql);
		return  $query;


    }
    
        // 相同产品的送货列表
    
    function get_rk_shipped($ProudctId) {
	    $sql = "
SELECT   P.Price, Y.POrderId,Y.Qty AS OrderQty,
MAX(S.Date) as rkDate,C.Mid,IFNULL( PI.Leadtime, PL.Leadtime ) AS Leadtime,
IFNULL( PI.LeadWeek, PL.LeadWeek ) AS LeadWeek, P.cName, P.TestStandard, P.ProductId
,M.OrderPO ,M.OrderDate,CM.Id as chMid,C.created as chDate,CM.InvoiceNO,C.Qty as chQty,CM.Ship ,CM.ShipType 
			
			from ch1_shipsheet C 
left join ch1_shipmain CM on CM.Id=C.Mid
left join yw1_orderrk S on S.POrderId=C.POrderId
			INNER JOIN yw1_ordersheet Y ON Y.POrderId = C.POrderId
			INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
			INNER JOIN productdata P ON P.ProductId = Y.ProductId


			LEFT JOIN yw3_pisheet PI ON PI.oId = Y.Id
			LEFT JOIN yw3_pileadtime PL ON PL.POrderId = Y.POrderId
			
			WHERE  Y.ProductId=?
			GROUP BY C.Id
			ORDER BY chDate DESC;";
		$query=$this->db->query($sql,$ProudctId);
		return  $query;

    }
    
        // 相同产品的未完成列表
    function get_unfinished($ProudctId) {
	    
	    /*
		    SELECT  S.ScQty ,P.Price, Y.POrderId,Y.Qty, 
IFNULL( PI.LeadWeek, PL.LeadWeek ) AS LeadWeek ,IFNULL( PI.Leadtime, PL.Leadtime ) AS Leadtime, P.cName, P.TestStandard, P.ProductId, 
L.Letter AS Line,L.GroupId,M.OrderPO ,M.OrderDate,Y.ShipType  
			
			FROM yw1_scsheet S 
			LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
			LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
			INNER JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId
			INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
			INNER JOIN productdata P ON P.ProductId = Y.ProductId
			LEFT JOIN yw3_pisheet PI ON PI.oId = Y.Id
			LEFT JOIN yw3_pileadtime PL ON PL.POrderId = Y.POrderId
			LEFT JOIN workscline L ON L.Id = S.scLineId
			WHERE S.Estate>0  AND Y.ProductId=?
			GROUP BY S.POrderId
			ORDER BY OrderDate DESC;
	    */
	    $sql = " 
			
			SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,S.Estate,S.scFrom,P.ProductId,P.cName,P.TestStandard,C.Forshort,PI.Leadtime,IFNULL(PI.LeadWeek,PL.LeadWeek)  AS LeadWeek,S.PackRemark ,D.PreChar  
			FROM yw1_ordermain M
			LEFT JOIN yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
            LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
            LEFT JOIN currencydata D ON D.Id = C.Currency  
            LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  yw3_pileadtime PL ON PL.POrderId=S.POrderId  
            LEFT JOIN productdata P ON P.ProductId=S.ProductId
		    WHERE  S.Estate=1  and S.ProductId=?  ORDER BY CompanyId,Leadtime 
			
			";
			//AND S.ScFrom>0
	    
	    $query=$this->db->query($sql,$ProudctId);
		return  $query;
    }
    
        // 新单月统计
    function order_new_mon($companyid='') {
	    $condition = '';
	    
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    
	    
	    $sql = "SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,COUNT(*) as Counts, 
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount   ,SUM(IF(F.Percent<3,1,0)) AS LowProfit 
	FROM yw1_ordermain M
	INNER JOIN  yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	INNER JOIN trade_object C ON C.CompanyId=M.CompanyId
	INNER JOIN currencydata D ON D.Id=C.Currency
	LEFT JOIN yw1_orderprofit  F ON F.POrderId=S.POrderId 
	WHERE 1 $condition GROUP BY  DATE_FORMAT(M.OrderDate,'%Y-%m')  ORDER BY Month DESC";
		$query=$this->db->query($sql);
		return  $query;
		
    }
    
    
    // 新单一个月下的日统计
    function order_new_date($Month, $date='') {
	    
	    
	    
	    $sql = "SELECT M.OrderDate,SUM(S.Qty) AS Qty,COUNT(*) as Counts,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount    ,SUM(IF(F.Percent<3,1,0)) AS LowProfit 
	FROM yw1_ordermain M
    LEFT JOIN  yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN yw1_orderprofit  F ON F.POrderId=S.POrderId  
	LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency
	WHERE  DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' GROUP BY  M.OrderDate  ORDER BY  M.OrderDate DESC";
		if ($date != '') {
			 $sql = "SELECT M.OrderDate,SUM(S.Qty) AS Qty,COUNT(*) as Counts,SUM(S.Qty*S.Price*D.Rate) AS Amount,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount    ,SUM(IF(F.Percent<3,1,0)) AS LowProfit 
	FROM yw1_ordermain M
    LEFT JOIN  yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN yw1_orderprofit  F ON F.POrderId=S.POrderId  
	LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency
	WHERE  M.OrderDate ='$date' ";
		}
		$query=$this->db->query($sql);
		return  $query;
		
    }
    
    // 新单按companyid统计
    function order_new_company($date='') {
	    
	    $condition = '1';
	    if ($date!='') {
		    $condition = " M.OrderDate='$date' ";
	    }
	    
	    $sql = "SELECT C.Forshort,M.CompanyId,D.PreChar,C.Logo,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,SUM(S.Qty*S.Price) AS realAmount,COUNT(*) as Counts,
SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(IF(S.Estate>0,S.Qty*S.Price*D.Rate,0)) AS NoChAmount ,SUM(IF(F.Percent<3,1,0)) AS LowProfit 
	FROM yw1_ordersheet S
	LEFT JOIN yw1_orderprofit  F ON F.POrderId=S.POrderId  
	LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency
	WHERE $condition  GROUP BY  C.CompanyId  ORDER BY  Amount DESC";
		$query=$this->db->query($sql);
		return  $query;
		
    }
    
    
    // 新单一个月内 成本统计
    function order_mon_cost($Month, $companyid='') {
	    $condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    
	    /*
		     SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM yw1_ordermain M
                LEFT JOIN yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 and M.OrderDate='$date' and M.CompanyId=$companyid
	    */
	    
	    $ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
	    
	    $sql = " SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM yw1_ordermain M
                INNER JOIN yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                INNER JOIN cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        INNER JOIN trade_object B ON A.CompanyId=B.CompanyId
		        INNER JOIN currencydata C ON B.Currency=C.Id	
		        INNER JOIN stuffdata D ON D.StuffId=A.StuffId
                INNER JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE 1 $condition and  A.Level=1 ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
		
    }
    
    function nooutorder_company_cost($companyid) {
	    
	     $ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
	    $sql = " SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM yw1_ordermain M
                LEFT JOIN yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 

                LEFT JOIN cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE S.Estate=1 and  M.CompanyId=$companyid and  A.Level=1 ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
		
    }

     // 新单一天内 成本统计
    
     function order_date_cost($date) {
	     $ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
	    $sql = " SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM yw1_ordermain M
                LEFT JOIN yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE M.OrderDate='$date' and  A.Level=1 ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
		
    }
    
    
     // 新单一天内 没有利润的单 成本统计
	public function noprof_date_cost($date) {
		
		$ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
		$sql = "
			SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 

			  from    (select A.POrderId,A.OrderNumber from (
	               select G.StockId,G.POrderId,CG.Id,S.OrderNumber from 
		cg1_stocksheet G
		LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
		LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
		LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 

		LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
		WHERE  M.OrderDate='$date' and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
               ) A
  group by A.POrderId) B
  LEFT JOIN yw1_ordermain M  ON B.OrderNumber=M.OrderNumber 
   LEFT JOIN cg1_stocksheet A  ON B.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 and M.OrderDate='$date'
			
		";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
	}
	
	// 新单一个月内 没有利润的单 成本统计
	public function noprof_month_cost($Month, $companyid='') {
		$ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
		$condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
		$sql = "SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
 FROM    (
			SELECT S.POrderId,S.Qty,S.Price,M.CompanyId   
	         FROM  yw1_ordermain M  
	        LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	        LEFT JOIN cg1_stocksheet G  ON S.POrderId=G.POrderId
			LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
			LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
			LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
			WHERE  1 $condition  and S.Estate>0   and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
	        group by S.POrderId) B
   LEFT JOIN cg1_stocksheet A  ON B.POrderId=A.POrderId 
	LEFT JOIN trade_object P ON A.CompanyId=P.CompanyId
	 LEFT JOIN currencydata C ON P.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
	}

	// 未出内某companyid下 没有利润的单 成本统计
	public function noprof_noout_company_cost( $companyid) {
		$ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
		$sql = "
			SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 

			  from    (select A.POrderId,A.OrderNumber from (
	               select G.StockId,G.POrderId,CG.Id,S.OrderNumber from 
		cg1_stocksheet G
		LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
		LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
		LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 

		LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
		WHERE  S.Estate=1 and M.CompanyId=$companyid and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
               ) A
  group by A.POrderId) B
  LEFT JOIN yw1_ordermain M  ON B.OrderNumber=M.OrderNumber 
   LEFT JOIN cg1_stocksheet A  ON B.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 
			
		";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
	}

	// 新单一天内某companyid下 没有利润的单 成本统计
	public function noprof_date_company_cost($date, $companyid) {
		$ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
		$sql = "
			SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 

			  from    (select A.POrderId,A.OrderNumber from (
	               select G.StockId,G.POrderId,CG.Id,S.OrderNumber from 
		cg1_stocksheet G
		LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
		LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
		LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 

		LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
		WHERE  M.OrderDate='$date'  and M.CompanyId=$companyid and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
               ) A
  group by A.POrderId) B
  LEFT JOIN yw1_ordermain M  ON B.OrderNumber=M.OrderNumber 
   LEFT JOIN cg1_stocksheet A  ON B.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 
			
		";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
	}

	
	// 新单一天内 没有利润的单 金额统计
	public function noprof_date_amount($date) {
		$sql = "select SUM(S.Qty*S.Price*D.Rate) AS Amount, COUNT(*) as Nums 
		        from    (select A.POrderId,A.OrderNumber from (
	               select G.StockId,G.POrderId,CG.Id,S.OrderNumber from 
		cg1_stocksheet G
		LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
		LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
		LEFT JOIN stuffdata D ON D.StuffId=G.StuffId
		 
		LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
		WHERE  M.OrderDate='$date' and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
               ) A
  group by A.POrderId) B
	LEFT JOIN yw1_ordersheet S  ON S.POrderId=B.POrderId 
	LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency";
	$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row;
		}
		return  null;
	}
	
	// 新单一月内 没有利润的单 金额统计
	public function noprof_month_amount($Month, $companyid='') {
		$condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    
		$sql = "SELECT SUM(A.Qty*A.Price*D.Rate) AS Amount, COUNT(*) as Nums 
		FROM    (
         SELECT S.POrderId,S.Qty,S.Price,M.CompanyId   
	        FROM  yw1_ordermain M  
	        LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	        LEFT JOIN cg1_stocksheet G  ON S.POrderId=G.POrderId
			LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
			LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
			LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
			WHERE  1 $condition   and S.Estate>0   and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
	        group by S.POrderId
    ) A
	LEFT JOIN trade_object C ON C.CompanyId=A.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency";
	$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row;
		}
		return  null;
	}
	
	// 未出某companyid下 没有利润的单 金额统计
	public function noout_noprof_company_amount($companyid) {
		$sql = "SELECT SUM(A.Qty*A.Price*D.Rate) AS Amount, COUNT(*) as Nums 
		FROM    (
         SELECT S.POrderId,S.Qty,S.Price,M.CompanyId   
	        FROM  yw1_ordermain M  
	        LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	        LEFT JOIN cg1_stocksheet G  ON S.POrderId=G.POrderId
			LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
			LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
			LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
			WHERE  M.CompanyId=$companyid   and S.Estate=1  and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
	        group by S.POrderId
    ) A
	LEFT JOIN trade_object C ON C.CompanyId=A.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency
	";
	$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row;
		}
		return  null;
	}

	
	// 新单一天内某companyid下 没有利润的单 金额统计
	public function noprof_date_company_amount($date, $companyid) {
		$sql = "SELECT SUM(A.Qty*A.Price*D.Rate) AS Amount, COUNT(*) as Nums 
		FROM    (
         SELECT S.POrderId,S.Qty,S.Price,M.CompanyId   
	        FROM  yw1_ordermain M  
	        LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	        LEFT JOIN cg1_stocksheet G  ON S.POrderId=G.POrderId
			LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
			LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
			LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
			WHERE  M.OrderDate='$date'  and  M.CompanyId=$companyid   and S.Estate=1  and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
	        group by S.POrderId
    ) A
	LEFT JOIN trade_object C ON C.CompanyId=A.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency";
	
	$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row;
		}
		return  null;
	}
	
    // 新单一天内某companyid下  成本统计
    function order_date_company_cost($date,$companyid) {
	    
	    /*
		    
		     SELECT  SUM(A.OrderQty*IF(T.mainType=$semi_maintype  AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS costAmount
		 FROM  cg1_stocksheet A
		 LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId 
		 LEFT JOIN currencydata C ON B.Currency=C.Id	
         LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
         LEFT JOIN stufftype T ON T.TypeId=D.TypeId 
		 WHERE  A.POrderId=IN_POrderId AND A.Level=1
		    
	    */
	    $ash_in_supplier = $this->ash_in_supplier;
	    $semi_maintype = $this->semi_maintype;
	    $sql = " SELECT SUM(A.OrderQty*IF(T.mainType=$semi_maintype   AND B.CompanyId IN($ash_in_supplier),D.Price,A.Price)*IFNULL(C.Rate,1)) AS oTheCost 
		        FROM yw1_ordermain M
                LEFT JOIN yw1_ordersheet S  ON S.OrderNumber=M.OrderNumber 
                LEFT JOIN cg1_stocksheet A  ON S.POrderId=A.POrderId 
		        LEFT JOIN trade_object B ON A.CompanyId=B.CompanyId
		        LEFT JOIN currencydata C ON B.Currency=C.Id	
		        LEFT JOIN stuffdata D ON D.StuffId=A.StuffId
                LEFT JOIN stufftype T ON T.TypeId=D.TypeId
		        WHERE A.Level=1 and M.OrderDate='$date' and M.CompanyId=$companyid ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->oTheCost;
		}
		return  0;
		
    }
    //返回指定Id的记录
	function get_records($id=0){
	
	   $sql = "SELECT S.Id,S.POrderId,S.ProductId,S.Qty,M.OrderPO,M.OrderDate,P.cName,P.eCode,P.TestStandard,A.Forshort,
	               IFNULL(PI.Leadweek,PL.Leadweek) AS  Leadweek
	           FROM yw1_ordersheet S
	           INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	           INNER JOIN productdata P ON P.ProductId=S.ProductId 
	           INNER JOIN trade_object A ON A.CompanyId=M.CompanyId 
	           LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
               LEFT JOIN yw3_pileadtime PL ON PL.POrderId = S.POrderId
	           WHERE S.POrderId=?"; 	
	   $query=$this->db->query($sql,array($id));
	   return  $query->first_row('array');
	}
	
	
	// 新单一天内某companyid下 订单列表
	function order_date_company_list($date,$companyid, $Month='') {
		
		$condition = '';
	    if ($Month!='') {
		    $condition = " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    } else if ($date!='') {
		    $condition = " AND M.OrderDate='$date' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }

		
	    $sql = "SELECT S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.Qty*S.Price AS Amount,S.ShipType,S.Estate,S.scFrom,S.cgRemark,SM.Name AS Operator,P.cName,P.TestStandard, C.PreChar,
                             PI.Leadtime,YEARWEEK(PI.Leadtime,1)  AS Weeks 
			FROM yw1_ordermain M
			LEFT JOIN yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
			LEFT JOIN staffmain SM ON S.Operator=SM.Number
            LEFT JOIN yw3_pisheet PI ON PI.oId=S.Id 
            LEFT JOIN productdata P ON P.ProductId=S.ProductId 
            LEFT JOIN trade_object B ON M.CompanyId=B.CompanyId
            LEFT JOIN currencydata C ON B.Currency=C.Id	
		    WHERE S.Id>0  $condition ORDER BY Amount DESC";
		$query=$this->db->query($sql);
		
		return $query->result_array();
		
    }
    
    // 新单一天内 锁单单数统计
    function check_lock_date($date) {
	    $sql = "SELECT count(*) AS Locks,0 AS gLocks 
		FROM cg1_stocksheet G 
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
        LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId 
		WHERE  M.OrderDate='$date' AND GL.Locks=0 ";
		$query=$this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->Locks;
		}
		return 0;
    }
    
    // 新单一天内某companyid下 锁单单数统计 小锁
    function check_lock_date_company($date, $companyid, $Month = '') {
	    

	    $condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    if ($date!='') {
		    $condition .= " and  M.OrderDate='$date'";
	    }
	    
	    
	    $sql = "SELECT count(*) AS Locks,0 AS gLocks 
		FROM cg1_stocksheet G 
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
        LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId
		WHERE  1 $condition AND GL.Locks=0 ";
		$query=$this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->Locks;
		}
		return 0;
    }
    
    // 新单一天内  锁单单数统计 大锁
    function check_explock_date($date) {
	    $sql = "SELECT count(*) as Locks
		FROM yw2_orderexpress  E 
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=E.POrderId
        LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
        WHERE  M.OrderDate='$date' AND E.Type=2";
        $query=$this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->Locks;
		}
		return 0;
	    
    }
    
    // 新单一天内某companyid下 锁单单数统计 大锁
    function check_explock_date_company($date, $companyid,$Month='') {
	    
	    $condition = '';
	    if ($Month!='') {
		    $condition .= " AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ";
	    }
	    if ($companyid != '') {
		    $condition .= " AND M.CompanyId='$companyid' ";
	    }
	    if ($date!='') {
		    $condition .= " and  M.OrderDate='$date'";
	    }
	    
	    
	    $sql = "SELECT count(*) as Locks
		FROM yw2_orderexpress  E 
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=E.POrderId
        LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
        WHERE  1 $condition AND E.Type=2";
        $query=$this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->Locks;
		}
		return 0;
	    
    }
    function check_lock($POrderId, $stockid='') {
	      //检查BOM表配件是否锁定
        $OrderSignColor=0;$cgRemark="";$RemarkDate="";$Remark="";
        
        $sql = "SELECT count(*) AS Locks,0 AS gLocks,GL.Remark,GL.Date,M.Name  FROM cg1_stocksheet G 
		LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId 
		LEFT JOIN staffmain  M ON M.Number=GL.Operator 
		WHERE  G.POrderId='$POrderId' AND GL.Locks=0 ";
		if ($stockid != '') {
			$sql = "SELECT count(*) AS Locks,0 AS gLocks,GL.Remark,GL.Date,M.Name  FROM   
		cg1_lockstock GL   
		LEFT JOIN staffmain  M ON M.Number=GL.Operator 
		WHERE  GL.StockId='$stockid' AND GL.Locks=0 ";
		}
		$query=$this->db->query($sql);
		$OrderSignColor = 0;
		$Locks = 0;
		$RemarkOperator = '';
	    if($query->num_rows() > 0){
		    $checkcgLockRow = $query->row_array();
	        $cgRemark=$checkcgLockRow["Remark"];
	        $RemarkDate=$checkcgLockRow["Date"];
            $RemarkOperator=$checkcgLockRow["Name"];
            
			if ($checkcgLockRow["Locks"]>0){
			    $OrderSignColor=$checkcgLockRow["gLocks"]>0?2:6;  
			    $Locks = 2; 
			}
		}
		
		$sql="SELECT S.Type,S.Remark,S.Date,M.Name FROM yw2_orderexpress  S 
        LEFT JOIN staffmain  M ON M.Number=S.Operator
        WHERE S.POrderId='$POrderId' AND S.Type=2 LIMIT 1";
        $query=$this->db->query($sql);
			if($query->num_rows() > 0){
				$checkExpressRow = $query->row_array();
			    $OrderSignColor=4;
			    $Remark=trim($checkExpressRow["Remark"])==""?"未填写原因":$checkExpressRow["Remark"];
			     $RemarkDate=$checkExpressRow["Date"];
			     $Locks = 1;
                 $RemarkOperator=$checkExpressRow["Name"];
			}
		$Remark.=$cgRemark;	
		  if ($OrderSignColor==4 || $OrderSignColor==2 || $OrderSignColor==6)
      {
           $Locks=$OrderSignColor==4?1:2;
           $Locks=$OrderSignColor==6?3:$Locks;
      }


		return array('lock'=>$Locks, 'oper'=>$RemarkOperator, 'remark'=>$Remark, 'date'=>$RemarkDate);
    }
    
    //单利润
    function getOrderProfit($porderid) {
	    
	    $sql = "SELECT getOrderProfit($porderid) AS Profit";
	    $query=$this->db->query($sql);
	    $CostValue = '';
	    if ($query->num_rows() > 0) {
			$row = $query->row();
			$CostValue = $row->Profit;
			$CostArray=explode('|', $CostValue);
			$profitRMB2=$CostArray[0];
			$profitRMB2PC=$CostArray[1];
			$GrossProfit=$CostArray[2];
			$profitColor=$CostArray[3];
			return array('rmb'    =>$profitRMB2,
						 'percent'=>$profitRMB2PC,
						 'gross'  =>$GrossProfit,
						 'color'  =>$profitColor);
		}
		return null;
		
    }

	//检查是否是没有利润的单
	function checkIsNoProfit($porderid) {
		$sql = "select A.POrderId,A.OrderNumber from (
	               select G.StockId,G.POrderId,CG.Id,S.OrderNumber from 
		cg1_stocksheet G
		LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
		LEFT JOIN yw1_ordersheet S  ON S.POrderId=G.POrderId
		LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber
		LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 

		LEFT JOIN stufftype ST ON ST.TypeId=D.TypeId
		WHERE  S.POrderid='$porderid'   and  ( (G.Price=0 and D.PriceDetermined=1 ) or (ST.mainType=7 and G.CompanyId in (2270,100300) and CG.Id is null) ) 
               ) A";
        $query=$this->db->query($sql);
	    if ($query->num_rows() > 0) {
		    return 1;
		} else {
			return 0;
		}
               
	}

	function getcolor_profit($index) {
		if ($index > 10) {
			return '#01be56';
		} else if ($index>=3) {
			return '#f09300';
		} else  {
			return '#ff0000';
		}
	}
	
	
	public function get_company_sheet($companyIds){
       $dataArray=array();
       $sql="SELECT M.* FROM yw1_ordermain M 
			 WHERE  M.CompanyId IN ($companyIds)";
       $query = $this->db->query($sql);
       
       if ($query->num_rows()>0){
	       $dataArray['main'] = $query->result_array();
	       
	       	       
	       $sql2="SELECT S.* FROM yw1_ordermain M 
			 LEFT JOIN yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
			 WHERE  M.CompanyId IN ($companyIds)";
		   $query2 = $this->db->query($sql2); 
		   $dataArray['sheet'] = $query2->result_array();
       }    
       return $dataArray;
    }
    
    public function get_company_pisheet($companyIds){
       $dataArray=array();
       $sql="SELECT * FROM yw3_pisheet  WHERE  CompanyId IN ($companyIds)";
       $query = $this->db->query($sql);   
       $dataArray = $query->result_array();  
       
       return $dataArray;  
    }
    
    
}