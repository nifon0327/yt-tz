<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ProductstockModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    //获取产品库存数量(所有)
	public function get_allstockqty() 
	{
	    
		$sql = "SELECT SUM(B.rkQty-B.shipQty) AS tStockQty,SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS Amount  
				 FROM(
					SELECT A.CompanyId,A.POrderId,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty
					FROM (
					    SELECT M.CompanyId,S.POrderId,S.Price,SUM(R.Qty) AS rkQty  
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
				WHERE B.rkQty>B.shipQty ";
		$query = $this->db->query($sql);
		if ($query->num_rows()>0){
		   $dataArray = $query->first_row('array');
		}
		
		return $dataArray;
	}
  
    //按未出获取产品库存数量
	public function get_order_stockqty()
	{
	    $dataArray=array();
	    
	    $thisDate=$this->Date;
	    $overdate=date("Y-m-d",strtotime("$thisDate  -6 day"));
	    
		$sql = "SELECT B.CompanyId,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS tStockQty,
		               SUM(IF (overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(OverSign) AS OverCounts,
		               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS Amount,M.Name AS StaffName,P.Forshort   
				 FROM(
					SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty,
					       IF(rkDate<'$overdate',1,0) AS OverSign  
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
				WHERE B.rkQty>B.shipQty GROUP BY B.CompanyId ORDER BY Amount DESC ";
		 $query = $this->db->query($sql);
		
		if ($query->num_rows()>0){
		   $dataArray = $query->result_array();
		}
		
		return $dataArray;
	}
    
    
    //获取产品库存数量(未使用)
	public function get_stockqty() 
	{
	    $dataArray=array();
	    
	    $thisDate=$this->Date;
	    $overdate=date("Y-m-d",strtotime("$thisDate  -6 day"));
	    
		$sql = "SELECT P.CompanyId,K.ProductId,SUM(K.tStockQty) AS tStockQty,IFNULL(R.Qty,0) AS rkQty 
				FROM  productstock K 
				LEFT JOIN productdata P ON P.ProductId=K.ProductId
				LEFT JOIN (
				      SELECT ProductId,SUM(Qty) AS Qty FROM yw1_orderrk WHERE Date<'$overdate' GROUP BY ProductId
				) R ON R.ProductId=K.ProductId  
				WHERE K.tStockQty>0  GROUP BY P.CompanyId  ORDER BY tStockQty DESC";
		$query = $this->db->query($sql);
		
		if ($query->num_rows()>0){
		   $dataArray = $query->result_array();
		}
		
		return $dataArray;
	}
	
	
 }