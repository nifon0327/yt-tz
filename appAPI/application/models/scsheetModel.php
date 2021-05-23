<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScSheetModel extends MC_Model {

    function __construct()
    {
        parent::__construct();
    }

    function check_issemi_bomed($StockId) {

	    $sql = "
	    select 
  CG.Id from  cg1_semifinished CG  

where CG.mStockId=$StockId and CG.Id is not null limit 1
		
;";

		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return true;
		}
		return false;


    }

    //返回指定Id的记录
	function get_records($sPOrderId){

	   $sql = "SELECT S.sPOrderId,S.POrderId,S.mStockId,S.StockId,S.ActionId,S.WorkShopId,W.Name AS WorkShopName,
	                  S.scLineId,S.Type,S.Level,S.ScFrom,S.Qty,
	                  S.ScQty,S.FinishDate,S.Estate,S.Remark,S.Date,S.Operator,
	                  D.Picture,D.StuffCname,
	                  G.StuffId,G.Price,G.CostPrice,G.DeliveryWeek,(G.AddQty+G.FactualQty) AS OrderQty   
	                  FROM yw1_scsheet S 
	                  LEFT JOIN  cg1_stocksheet G ON G.StockId=S.mStockId 
	                  LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
	                  LEFT JOIN workshopdata W ON W.Id=S.WorkShopId 
	                  WHERE S.sPOrderId=?";
	   $query=$this->db->query($sql,$sPOrderId);

	   return  $query->first_row('array');
	}

	//返回指定Id的记录(按半成品配件)
	function get_stuffId_mstock($mStockId, $likesign = ''){

	$condition = "S.mStockId=$mStockId";
		if (strlen($mStockId) == 14 && $likesign == 1) {
			$condition = "S.mStockId like '$mStockId%' limit 1";
		}
	   $sql = "SELECT S.sPOrderId,S.POrderId,S.mStockId,S.StockId,S.ActionId,S.WorkShopId,W.Name AS WorkShopName,
	                  S.scLineId,S.Type,S.Level,S.ScFrom,S.Qty,
	                  S.ScQty,S.FinishDate,S.Estate,S.Remark,S.Date,S.Operator,T.mainType,
	                  M.PurchaseID AS OrderPO,D.StuffId,D.Picture,D.StuffCname,
	                  G.StuffId,G.Price,G.CostPrice,G.DeliveryWeek,(G.AddQty+G.FactualQty) AS OrderQty   
	                  FROM yw1_scsheet S 
	                  LEFT JOIN  cg1_stocksheet G ON G.StockId=S.mStockId 
	                  LEFT JOIN cg1_stockmain M ON M.Id=G.Mid 
	                  INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
	                  INNER JOIN stufftype T ON T.TypeId=D.TypeId
	                  LEFT JOIN workshopdata W ON W.Id=S.WorkShopId 
	                  WHERE $condition ";
	   $query=$this->db->query($sql);
	   if ($query->num_rows() > 0)
	   return  $query->first_row('array');

	   return null;
	}

	//返回指定Id的记录(按半成品配件)
	function get_records_mstock($mStockId){

	   $sql = "SELECT S.sPOrderId,S.POrderId,S.mStockId,S.StockId,S.ActionId,S.WorkShopId,W.Name AS WorkShopName,
	                  S.scLineId,S.Type,S.Level,S.ScFrom,S.Qty,
	                  S.ScQty,S.FinishDate,S.Estate,S.Remark,S.Date,S.Operator,T.mainType,
	                  M.PurchaseID AS OrderPO,D.StuffId,D.Picture,D.StuffCname,
	                  G.StuffId,G.Price,G.CostPrice,G.DeliveryWeek,(G.AddQty+G.FactualQty) AS OrderQty   
	                  FROM yw1_scsheet S 
	                  LEFT JOIN  cg1_stocksheet G ON G.StockId=S.mStockId 
	                  LEFT JOIN cg1_stockmain M ON M.Id=G.Mid 
	                  INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
	                  INNER JOIN stufftype T ON T.TypeId=D.TypeId
	                  LEFT JOIN workshopdata W ON W.Id=S.WorkShopId 
	                  WHERE S.mStockId=?";
	   $query=$this->db->query($sql,$mStockId);
	   if ($query->num_rows() > 0)
	   return  $query->first_row('array');

	   return null;
	}

    //全部未生产生产单数量
    function get_UnscSheetQty() {
		$sql = "SELECT SUM(A.Qty-A.ScQty) AS Qty 
				FROM (
				      SELECT S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty 
					  FROM   yw1_scsheet S 
					  LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
				      WHERE FIND_IN_SET(S.ActionId,getSysConfig(104))=0 AND S.ActionId!=101 
					AND S.ScFrom>0 AND S.Estate>0 GROUP BY S.sPOrderId 
				)A ";
		$query=$this->db->query($sql);

		$row = $query->first_row();
		$Qty=$row->Qty;
		$Qty=$Qty==""?0:round($Qty/10000,0);
	    $Qty.="W";
        return $Qty;
	}

	//全部未生产单加工费
    function get_unsccost($WorkShopId)
    {
		$sql = "SELECT SUM((A.Qty-A.ScQty)*Price) AS Amount 
				FROM (
				      SELECT S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty,G.Price  
					  FROM   yw1_scsheet S 
					  LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
                      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
				      WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0  
					  GROUP BY S.sPOrderId 
				)A";
		$query=$this->db->query($sql);

		$row = $query->first_row();
		return $row->Amount;
	}

	function get_unscqty($WorkShopId,$scLine='')
	{
	    $Searchs=$scLine==''?'':' AND  S.scLineId=' . $scLine;

		$sql = "SELECT SUM(IFNULL(B.Qty-B.ScQty,0)) AS Qty 
		        FROM ( 
					SELECT A.sPOrderId,A.Qty,A.ScQty,getCanStock(A.sPOrderId,3) AS canSign  
					FROM (
						    SELECT S.sPOrderId,S.Qty,IFNULL(SUM(C.Qty),0) AS ScQty   
							FROM      yw1_scsheet    S 
							LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
                            LEFT JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId 
							WHERE S.WorkShopId='$WorkShopId' $Searchs AND S.ScFrom>0 AND S.Estate>0  AND S.scLineId>0  
                            GROUP BY   S.sPOrderId
					)A WHERE 1
		         )B WHERE B.canSign=3";

	    $query=$this->db->query($sql);
	    $row = $query->row_array();

	    return $row['Qty'];
	}



	//统计可备、可配、待领、待备的工单的生产数量（按生产单位）
	function get_canstock_qty($WorkShopId,$ActionId,$CheckSign='KBL'){

	    $StockSign=1; //$StockSign参数：1.可占用  2.已占用，待领料  3.已备料，待生产  4.未备配件数量

	    $SearchRows=''; $Searchs=''; $groupby='';
	    switch($CheckSign){
		    case 'KBL'://可备料
		        $StockSign  =1;
		        $this->load->model('WorkShopdataModel');
	            $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($WorkShopId);//取得可备料周数
	            $SearchRows=$canWeeks>0?" AND A.LeadWeek<=$canWeeks ":" AND  A.LeadWeek>0 ";
		      break;
		   case 'DFP'://待分配
		       $StockSign  =2;
		       $SearchRows =" AND  A.LeadWeek>0 ";
		       $Searchs    =' AND  S.scLineId=0 ';
		      break;
		   case 'DBL'://待备料
		       $StockSign  =2;
		       $Searchs    =' AND  S.scLineId>0 ';

		      break;
		   case 'DSC'://待生产
		       $StockSign  =3;
		       $Searchs    =' AND  S.scLineId>0 ';
		      break;
	       case 'Overdue':
	           $StockSign  =3;
	           $SearchRows =' AND  A.LeadWeek<'.$this->ThisWeek;
		      break;
		   case 'Line'://按生产线分线统计
		       $StockSign  =3;
		       $Searchs    =' AND  S.scLineId>0 ';
		       $groupby    =' GROUP BY B.scLineId ';
		      break;
	    }

		switch($ActionId){
		  case '101'://包装
		      $sql="SELECT COUNT(1) AS Counts,B.scLineId,SUM(IFNULL(B.Qty,0)) AS Qty 
		        FROM ( 
					SELECT A.sPOrderId,A.Qty,A.scLineId,getCanStock(A.sPOrderId,$StockSign) AS canSign  
					FROM (
						    SELECT S.POrderId,S.sPOrderId,S.Qty,S.scLineId,IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek    
							FROM      yw1_scsheet    S 
							LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
							LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id
			                LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
							WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 $Searchs  
					)A WHERE 1 $SearchRows 
		         )B WHERE B.canSign=$StockSign  $groupby";
		      break;
		  default:
		     $sql="SELECT COUNT(1) AS Counts,SUM(IFNULL(B.Qty,0)) AS Qty 
		        FROM ( 
					SELECT A.sPOrderId,A.Qty ,getCanStock(A.sPOrderId,$StockSign) AS canSign  
					FROM (
						    SELECT S.POrderId,S.sPOrderId,S.Qty,G.DeliveryWeek AS LeadWeek    
							FROM      yw1_scsheet    S 
							LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId  
							WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 
					)A WHERE 1 $SearchRows 
		         )B WHERE B.canSign=$StockSign";
		      break;
	     }

	    $query=$this->db->query($sql);

	    if ($groupby==''){
		     $row = $query->first_row('array');
		     return $row['Qty']==''?0:$row['Qty'];
	    }else{
		     return $query->result_array();
	    }
	}

	//半成品可备料数量(按生产单位)
	function get_semi_canstock($WorkShopId)
	{
		$StockSign  =1;
        $this->load->model('WorkShopdataModel');
        $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($WorkShopId);//取得可备料周数
        $SearchRows=$canWeeks>0?" AND G.DeliveryWeek<=$canWeeks   AND  G.DeliveryWeek>0":" AND  G.DeliveryWeek>0 ";

        $sql="SELECT B.WorkShopId,W.Name,SUM(IFNULL(B.Qty,0)) AS Qty,COUNT(1) AS Counts  
		        FROM ( 
					SELECT A.WorkShopId,A.sPOrderId,A.Qty ,getCanStock(A.sPOrderId,1) AS canSign  
					FROM (
						    SELECT S.WorkShopId,S.sPOrderId,S.Qty     
							FROM      yw1_scsheet    S 
							LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId  
							WHERE S.WorkShopId IN($WorkShopId) AND S.ScFrom>0 AND S.Estate>0 $SearchRows 
					)A WHERE 1
		         )B
                 LEFT JOIN workshopdata W ON W.Id=B.WorkShopId 
                 WHERE B.canSign=$StockSign  GROUP BY B.WorkShopId";
        $query=$this->db->query($sql);
        return $query->result_array();
	}

	//生产工单订单明细（按生产单位）
	function get_canstock_list($WorkShopId,$ActionId,$CheckSign,$scLine=''){
       $SearchRows=''; $Searchs='';
	    switch($CheckSign){
		    case 'KBL'://可备料
		        $StockSign  =1;
		        //$StockSign  =$this->LoginNumber==10868?0:1;
		        $this->load->model('WorkShopdataModel');
		        //取得可备料周数
	            $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($WorkShopId);
	            $SearchRows=$canWeeks>0?" AND A.LeadWeek<=$canWeeks AND  A.LeadWeek>0":' AND  A.LeadWeek>0 ';
	            $SearchRowA=$canWeeks>0?" AND GS.DeliveryWeek<=$canWeeks AND  GS.DeliveryWeek>0":' AND  GS.DeliveryWeek>0 ';
		      break;
		    case 'DFP'://待分配
		       $StockSign  =2;
		       $SearchRows =' AND  A.LeadWeek>0 ';
		       $Searchs    =' AND  S.scLineId=0 ';
		      break;
		   case 'DBL'://待备料
		       $StockSign  =2;
		       $Searchs    =' AND  S.scLineId>0 ';
		      break;
		   case 'DSC'://待生产
		       $StockSign  =3;
		       $Searchs=$scLine==''?'':' AND  S.scLineId=' . $scLine;
		      // $Searchs    =' AND  S.scLineId>0 ';
		      break;
		   case 'SCZ'://生产中
		       $StockSign  =3;
		       $SerarchRows=' AND EXISTS( SELECT T.sPOrderId FROM sc1_cjtj T WHERE T.sPOrderId=A.sPOrderId)';
		      break;
	    }

	    $dataArray=array();
		switch($ActionId){
		  case '101'://包装
		      $sql="SELECT B.POrderId,B.ProductId,B.sPOrderId,B.Qty,B.LeadWeek,B.ShipType,B.Operator,B.Remark,B.modifier,B.modified,
		      L.Letter AS Line,M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,
              IFNULL(IF($StockSign=1,T.ableDate,getLastStockTime(B.sPOrderId)),NOW()) AS created,'0' AS mStockId 
		        FROM( 
					SELECT A.POrderId,A.ProductId,A.OrderNumber,A.sPOrderId,A.Qty,A.LeadWeek,A.ShipType,A.scLineId,A.Remark,A.modifier,A.modified,A.Operator,
					getCanStock(A.sPOrderId,$StockSign) AS canSign 
					FROM (
						    SELECT S.POrderId,S.sPOrderId,S.Qty,S.scLineId,S.Remark,S.modifier,S.modified, Y.ProductId,Y.OrderNumber,Y.ShipType,Y.Operator,
						    IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek    
							FROM yw1_scsheet    S 
							LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
							LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id
			                LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
							WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 $Searchs  
					)A WHERE 1 $SearchRows  
		        )B 
               INNER JOIN yw1_ordermain M ON M.OrderNumber=B.OrderNumber
               INNER JOIN trade_object A ON A.CompanyId=M.CompanyId 
               INNER JOIN productdata P ON P.ProductId=B.ProductId  
               LEFT JOIN  workscline  L ON L.Id=B.scLineId 
               LEFT JOIN  ck_bldatetime T ON T.sPOrderId=B.sPOrderId AND T.Estate=1
               WHERE B.canSign=$StockSign ORDER BY LeadWeek,created,OrderDate";
		      break;//IFNULL(getOrderStockTime(B.sPOrderId)) AS created
		    case '104'://开料 DATE_ADD( G.DeliveryDate, INTERVAL - WEEKDAY( G.DeliveryDate ) DAY )
		      //IF($StockSign=1,IFNULL(T.ableDate,NOW()),IFNULL(G.created,''))
		      $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.cgSign,G.DeliveryWeek,D.StuffCname,D.Picture,
		                      IF($StockSign=1,IFNULL(T.ableDate,NOW()),IFNULL(G.created,'')) AS created,C.CutName,G.created AS OrderDate    
				   FROM ( 
                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,getCanStock(S.sPOrderId,$StockSign) AS canStockSign      
					     FROM   yw1_scsheet    S 
					     LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					     WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 $SearchRowA 
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN slice_cutdie    E ON E.StuffId=D.StuffId 
				LEFT JOIN pt_cut_data     C ON  C.Id  = E.CutId 
				LEFT JOIN  ck_bldatetime T ON T.sPOrderId=S.sPOrderId AND T.Estate=1 
				WHERE S.canStockSign=$StockSign ORDER BY DeliveryWeek,sPOrderId";
		     break;
		  default:
		       $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.cgSign,G.DeliveryWeek,D.StuffCname,D.Picture,
		                      IFNULL(Y.OrderPO,'') AS OrderPO,IF($StockSign=1,IFNULL(T.ableDate,NOW()),IFNULL(G.created,'')) AS created,
		                      G.created AS OrderDate    
				    FROM ( 
                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,getCanStock(S.sPOrderId,$StockSign) AS canStockSign      
					     FROM   yw1_scsheet    S 
					     LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					     WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 $SearchRowA 
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
				LEFT JOIN  ck_bldatetime T ON T.sPOrderId=S.sPOrderId AND T.Estate=1 
				WHERE S.canStockSign=$StockSign ORDER BY DeliveryWeek,sPOrderId";
		    break;
	     }


	    if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}

	//生产中的工单明细（按生产单位）
	function get_scing_list($WorkShopId,$ActionId)
	{
	   $dataArray=array();

	   switch($ActionId){
		  case '101'://包装
		     $sql="SELECT  S.POrderId,S.sPOrderId,S.Qty,S.scLineId,Y.ProductId,Y.OrderNumber,Y.ShipType,L.Letter AS Line,
		              IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek,M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,
		              getLastScTime(S.sPOrderId) AS created,Y.Operator ,S.Remark,S.modifier ,S.modified   
					FROM (
					        SELECT S.POrderId,S.sPOrderId,S.Qty,S.scLineId,IFNULL(SUM(T.Qty),0) as ScQty ,S.Remark,ifnull(S.modifier,S.Operator) as modifier ,S.modified  
					        FROM  yw1_scsheet   S 
                            INNER JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
                            WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 GROUP BY S.sPOrderId
                     )S 
					INNER JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
					INNER JOIN  yw1_ordermain M ON M.OrderNumber=Y.OrderNumber
	                INNER JOIN   trade_object A ON A.CompanyId=M.CompanyId 
	                INNER JOIN    productdata P ON P.ProductId=Y.ProductId  
					LEFT JOIN    yw3_pisheet PI ON PI.oId=Y.Id
	                LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
	                LEFT JOIN     workscline  L ON L.Id=S.scLineId 
					WHERE (S.ScQty<S.Qty AND S.Qty>0) OR EXISTS (SELECT C.Id FROM sc_currentmission C WHERE C.sPOrderId=S.sPOrderId) 
					ORDER BY LeadWeek,OrderDate";
              // echo $sql;
		      break;
		  default:
		      break;
	     }
	    if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}

	//统计待入库数量
	function get_stockin_qty($WorkShopId)
	{
		//$sql='SELECT SUM(Qty) AS Qty FROM yw1_scsheet WHERE Estate=1 AND  WorkShopId=? AND ScFrom=0 ';
		//$query=$this->db->query($sql,array($WorkShopId));
		$sql="SELECT SUM(S.ScQty-K.rkQty) AS Qty 
			FROM (
			      SELECT S.sPOrderId,SUM(IFNULL(C.Qty,0)) AS scQty   
				  FROM  yw1_scsheet   S 
                  INNER JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId
                  WHERE S.WorkShopId='$WorkShopId'  AND S.Estate=1 GROUP BY S.sPOrderId
            )S 
			LEFT JOIN (
			      SELECT  S.sPOrderId,SUM(IFNULL(K.Qty,0)) AS rkQty   
				  FROM  yw1_scsheet    S 
                  LEFT JOIN yw1_orderrk K ON K.sPOrderId=S.sPOrderId
                  WHERE S.WorkShopId='$WorkShopId'  AND S.Estate=1 GROUP BY S.sPOrderId
			 )K ON K.sPOrderId=S.sPOrderId 
			WHERE S.ScQty>K.rkQty";
		$query=$this->db->query($sql);
		$row = $query->first_row('array');
		return $row['Qty']>0?$row['Qty']:0;
	}

	function get_drk_blings($WorkShopId) {
		$sql = "SELECT  count(*) as Nums 
							FROM (
							      SELECT  S.POrderId,S.sPOrderId,S.Qty,S.scLineId,SUM(IFNULL(C.Qty,0)) AS scQty,
							      Max(C.created) AS created,Min(C.created) AS creates    
								  FROM  yw1_scsheet   S 
                                  INNER JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId
                                  WHERE S.WorkShopId='$WorkShopId'  AND S.Estate=1 GROUP BY S.sPOrderId
                            )S 
							LEFT JOIN (
							      SELECT  S.sPOrderId,SUM(IFNULL(K.Qty,0)) AS rkQty   
								  FROM  yw1_scsheet    S 
                                  LEFT JOIN yw1_orderrk K ON K.sPOrderId=S.sPOrderId
                                  WHERE S.WorkShopId='$WorkShopId'  AND S.Estate=1 GROUP BY S.sPOrderId
							 )K ON K.sPOrderId=S.sPOrderId 
							
							WHERE 1 AND S.ScQty>K.rkQty and S.ScQty=S.Qty  ;";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row()->Nums;
		}
		return 0;
	}
	//统计待入库数量
	function get_stockin_list($WorkShopId,$ActionId,$oneSid='')
	{
	    $dataArray=array();
		switch($ActionId){
		  case '101'://包装(可按生产数量入库)
		  case 'suspend':
		  $SearchRowsInner = '';
		        $SearchRows = $ActionId=='suspend'?" AND S.Qty>S.scQty ":" AND S.ScQty>K.rkQty ";

		        if ($oneSid != '') {
			        $SearchRowsInner =
			        $SearchRows = " and S.sPOrderId='$oneSid' ";
		        }

		        $sql="SELECT S.POrderId,S.sPOrderId,S.Qty,Y.ProductId,Y.OrderNumber,Y.ShipType,Y.Operator,
						    IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek,S.scQty,(S.scQty-K.rkQty) AS rkQty,S.created,S.creates,
						    M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,L.Letter AS Line ,S.boxs      
							FROM (
							      SELECT  S.POrderId,S.sPOrderId,S.Qty,S.scLineId,SUM(IFNULL(C.Qty,0)) AS scQty,sum(1) as boxs, 
							      Max(C.created) AS created,Min(C.created) AS creates    
								  FROM  yw1_scsheet   S 
                                  INNER JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId
                                  WHERE S.WorkShopId='$WorkShopId' $SearchRowsInner AND S.Estate=1 GROUP BY S.sPOrderId
                            )S 
							LEFT JOIN (
							      SELECT  S.sPOrderId,SUM(IFNULL(K.Qty,0)) AS rkQty   
								  FROM  yw1_scsheet    S 
                                  LEFT JOIN yw1_orderrk K ON K.sPOrderId=S.sPOrderId
                                  WHERE S.WorkShopId='$WorkShopId' $SearchRowsInner AND S.Estate=1 GROUP BY S.sPOrderId
							 )K ON K.sPOrderId=S.sPOrderId 
							INNER JOIN yw1_ordersheet Y  ON Y.POrderId=S.POrderId 
							INNER JOIN yw1_ordermain M   ON M.OrderNumber=Y.OrderNumber
                            INNER JOIN trade_object A    ON A.CompanyId=M.CompanyId 
                            INNER JOIN productdata P     ON P.ProductId=Y.ProductId 
							LEFT  JOIN yw3_pisheet PI    ON PI.oId=Y.Id
			                LEFT  JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
			                LEFT  JOIN workscline L      ON L.Id=S.scLineId 
							WHERE 1 $SearchRows ORDER BY LeadWeek,created,OrderDate";
		     /* $sql="SELECT S.POrderId,S.sPOrderId,S.Qty,Y.ProductId,Y.OrderNumber,Y.ShipType,
						    IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek,
						    M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,'' AS created,L.Letter AS Line
							FROM  (
							      SELECT  S.POrderId,S.sPOrderId,S.Qty    yw1_scsheet    S
							INNER JOIN yw1_ordersheet Y  ON Y.POrderId=S.POrderId
							INNER JOIN yw1_ordermain M   ON M.OrderNumber=Y.OrderNumber
                            INNER JOIN trade_object A    ON A.CompanyId=M.CompanyId
                            INNER JOIN productdata P     ON P.ProductId=Y.ProductId
							LEFT  JOIN yw3_pisheet PI    ON PI.oId=Y.Id
			                LEFT  JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId
			                LEFT  JOIN workscline L      ON L.Id=S.scLineId
							WHERE S.WorkShopId='$WorkShopId'  AND S.Estate=1 ORDER BY LeadWeek,OrderDate";
				*/
               //echo $sql;
		      break;
		  default:
		      break;
	     }
	    if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}


	//未生产生产单数量（按生产单位）
	function  get_semi_unscqtyweb($workshopid,$index=0){
	    $SerarchRows='';$sql='';
	    switch($index){
	        case -2://逾期
	             $SerarchRows=" AND G.DeliveryWeek>0 AND G.DeliveryWeek<" . $this->ThisWeek;
			case -1://逾期及本周生产
			     $SerarchRows=$SerarchRows==''?" AND G.DeliveryWeek>0 AND G.DeliveryWeek<=" . $this->ThisWeek:$SerarchRows;
			case 1://本周生产
			     $SerarchRows=$SerarchRows==''?" AND G.DeliveryWeek>0 AND G.DeliveryWeek=" . $this->ThisWeek:$SerarchRows;
			case 3://xia周生产
			     $SerarchRows=$SerarchRows==''?" AND G.DeliveryWeek>0 AND G.DeliveryWeek>=" . ($this->ThisWeek+1):$SerarchRows;
		    default://
		         $sql = "SELECT sum(S.Qty) as  qty,count(*) as cts   
				   FROM ( 
						SELECT A.sPOrderId,A.mStockId,A.StockId,A.Qty,SUM(A.OrderQty) AS blQty,SUM(IFNULL(A.llSign,0)) AS llSign,SUM(A.llQty) AS llQty  
						FROM (
							SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,G.OrderQty,
							       SUM(IFNULL(L.Qty,0)) AS llQty,SUM(IFNULL(L.Estate,0)) AS llSign 
								FROM(
									SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,G.DeliveryWeek  
									FROM yw1_scsheet S
									LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
								    WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0  $SerarchRows
								)S 
								INNER JOIN yw1_stocksheet G ON G.sPOrderId=S.sPOrderId  
								LEFT JOIN  ck5_llsheet    L ON L.StockId=G.StockId 
								WHERE 1 GROUP BY G.StockId 
						)A GROUP BY A.sPOrderId
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
				WHERE S.blQty=S.llQty AND S.llSign=0 ORDER BY DeliveryWeek,sPOrderId";
		      break;
		}

		$query=$this->db->query($sql);
		$row = $query->first_row();
		return $row;
	}




	//未生产生产单数量（按生产单位）
	function  get_semi_unscqty($workshopid,$index=0){
	    $SerarchRows='';$sql='';
	    switch($index){
	        case -2://逾期
	             $SerarchRows=" AND G.DeliveryWeek>0 AND G.DeliveryWeek<" . $this->ThisWeek;
			case -1://逾期及本周生产
			     $SerarchRows=$SerarchRows==''?" AND G.DeliveryWeek>0 AND G.DeliveryWeek<" . $this->ThisWeek:$SerarchRows;
		    default://未出
		         $sql = "SELECT SUM(A.Qty-A.ScQty) AS Qty 
						 FROM (
							SELECT S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty 
							FROM   yw1_scsheet S 
							LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId 
							LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
							WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 $SerarchRows GROUP BY S.sPOrderId 
						)A ";
		      break;
		}

		$query=$this->db->query($sql);
		$row = $query->first_row();
		$Qty=$row->Qty;
		$Qty=$Qty==""?0:$Qty;
		return $Qty;
	}

	//待备料生产单数量（按生产单位）
	function get_semi_blqty($WorkShopId)
	{
	    $this->load->model('WorkShopdataModel');
	    $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($WorkShopId);
	    $CanWeekStr    = " AND GS.DeliveryWeek<=$canWeeks ";

	   $sql = "SELECT SUM(IFNULL(A.Qty,0)) AS Qty 
			FROM (
				SELECT S.Qty,getCanStock(S.sPOrderId,1) AS canStockSign   
					FROM   yw1_scsheet    S 
					LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					WHERE S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 AND GS.DeliveryWeek>0  $CanWeekStr 
			)A  WHERE A.canStockSign=1 ";
	    $query=$this->db->query($sql);
		$row = $query->first_row();
		$Qty=$row->Qty;
		$Qty=$Qty==""?0:$Qty;
		return $Qty;
	}

	function get_lockedqty($workshopid,$dyweek='') {
		if ($dyweek=='current'){
		    $SearchRows="  AND G.DeliveryWeek>0 AND G.DeliveryWeek<=" . $this->ThisWeek;
	    }
	    else{
		    $SearchRows=$dyweek===''?'':" AND G.DeliveryWeek='$dyweek'";
	    }


/*
	    $sql = " select sum(S.Qty*S.Locks) , sum(S.Locks*S.Amount) from (
		SELECT S.Qty,(S.Qty*GS.Price) AS Amount  ,if((ifnull(E.Id,0)+ifnull(GL.Id,0))>0,1,0) as Locks
						FROM yw1_scsheet    S
						LEFT JOIN cg1_stocksheet G  ON G.StockId=S.mStockId

						LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId
						LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId and GL.Locks=0
left join yw2_orderexpress  E  ON G.POrderId=E.POrderId and E.Type=2
						WHERE S.WorkShopId='$workshopid' AND S.Estate>0 AND S.ScFrom>0   $SearchRows ) S where S.Locks=1; ";
*/

$sql = " select sum(S.Qty*S.Locks) as Qty from (
		SELECT S.Qty,if((ifnull(E.Id,0)+ifnull(GL.Id,0))>0,1,0) as Locks
						FROM yw1_scsheet    S
						LEFT JOIN cg1_stocksheet G  ON G.StockId=S.mStockId
						
						LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
						LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId and GL.Locks=0
left join yw2_orderexpress  E  ON G.POrderId=E.POrderId and E.Type=2
						WHERE S.WorkShopId='$workshopid' AND S.Estate>0 AND S.ScFrom>0   $SearchRows ) S where S.Locks=1; ";

		$query=$this->db->query($sql);
	    if ($query->num_rows() > 0) {
		    return $query->row()->Qty;
	    }
	    return 0;

	}

	//已备料生产单数量，含待领料确认（按生产单位）
	function get_semi_bledqty($workshopid,$dyweek=''){
	    if ($dyweek=='current'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek<=" . $this->ThisWeek;
	    }
	    else{
		    $SearchRows=$dyweek===''?'':" AND GM.DeliveryWeek='$dyweek'";
	    }

	     $StockSign  =3;
		 $sql = "SELECT SUM(A.Qty) AS Qty 
			   FROM ( 
					   SELECT GM.DeliveryWeek,GS.Price,S.sPOrderId,S.Qty 
						      FROM       yw1_scsheet    S 
                              LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId
                              LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
						      WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 $SearchRows  GROUP BY S.sPOrderId 
				  )A  WHERE  getCanStock(A.sPOrderId,$StockSign)=$StockSign ";

	    $query=$this->db->query($sql);
		$row = $query->first_row();
		$Qty=$row->Qty;
		$Qty=$Qty==""?0:$Qty;
		return $Qty;
	}

	//已备料生产单数量，含待领料确认（按生产单位）
	function get_semi_bledqtyweb($workshopid,$dyweek=''){
	    if ($dyweek=='current'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek<=" . $this->ThisWeek;
	    } else if ($dyweek=='over'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek<" . $this->ThisWeek;
	    } else if ($dyweek=='over+'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek>=" . ($this->ThisWeek+1);
	    }
	    else{
		    $SearchRows=$dyweek===''?'':" AND GM.DeliveryWeek=$dyweek";
	    }

	     $StockSign  =3;
		 $sql = "SELECT SUM(A.Qty) AS qty ,COUNT(*) cts,SUM(A.Qty*A.Price) AS amount 
			   FROM ( 
					   SELECT GM.DeliveryWeek,GS.Price,S.sPOrderId,S.Qty 
						      FROM       yw1_scsheet    S 
                              LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId
                              LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
						      WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 $SearchRows  GROUP BY S.sPOrderId 
				  )A  WHERE  getCanStock(A.sPOrderId,$StockSign)=$StockSign ";

	    $query=$this->db->query($sql);
		$row = $query->first_row();
		return $row;
	}

	//已备料生产单数量，含待领料确认（按生产单位）
	function get_semi_bledqtyweb_s($workshopid,$dyweek=''){
	    if ($dyweek=='current'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek<=" . $this->ThisWeek;
	    } else if ($dyweek=='over'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek<" . $this->ThisWeek;
	    } else if ($dyweek=='over+'){
		    $SearchRows="  AND GM.DeliveryWeek>0 AND GM.DeliveryWeek>=" . ($this->ThisWeek+1);
	    }
	    else{
		    $SearchRows=$dyweek===''?'':" AND GM.DeliveryWeek=$dyweek";
	    }

	     $StockSign  =3;
		 $sql = "SELECT SUM(A.Qty-A.ScQty) AS qty ,COUNT(*) cts,SUM((A.Qty-A.ScQty)*A.Price) AS amount 
			   FROM ( 
					   SELECT GM.DeliveryWeek,GS.Price,S.sPOrderId,S.Qty ,S.ScQty 
						      FROM       yw1_scsheet    S 
                              LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId
                              LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
						      WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 $SearchRows  GROUP BY S.sPOrderId 
				  )A  WHERE  getCanStock(A.sPOrderId,$StockSign)=$StockSign ";

	    $query=$this->db->query($sql);
		$row = $query->first_row();
		return $row;
	}

	//待领料确认生产单数量（按生产单位）
	function get_semi_llqty($WorkShopId){
		$sql = "SELECT SUM(IFNULL(S.Qty,0)) AS Qty 
		             FROM yw1_scsheet S  
		             WHERE 1 AND S.WorkShopId='$WorkShopId' AND S.ScFrom>0 AND S.Estate>0 AND getCanStock(S.sPOrderId,2)=2 ";
	    $query=$this->db->query($sql);
		$row = $query->first_row();
		$Qty=$row->Qty;
		$Qty=$Qty==""?0:$Qty;
		return $Qty;
	}

	//按周统计生产单数量（按生产单位）
	function get_semi_weekqty($workshopid,$index){

	    $SerarchRows='';$sql='';
	    $dataArray=array();
		switch($index){
			case -1://逾期及本周生产
			     $SerarchRows=' AND G.DeliveryWeek>0 AND G.DeliveryWeek<=' . $this->ThisWeek;
		    case  0://未出
		         $sql="SELECT G.DeliveryWeek,SUM(S.Qty) AS Qty,SUM(S.Qty*GS.Price) AS Amount,COUNT(*) AS Counts  
						FROM yw1_scsheet    S
						LEFT JOIN cg1_stocksheet G  ON G.StockId=S.mStockId
						LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
						WHERE S.WorkShopId='$workshopid' AND S.Estate>0 AND S.ScFrom>0  $SerarchRows 
						GROUP BY G.DeliveryWeek";
				   break;
		    case  21://已备料
		         $StockSign  =3;
				 $sql = "SELECT A.DeliveryWeek,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount,COUNT(*) AS Counts 
					   FROM ( 
							   SELECT GM.DeliveryWeek,GS.Price,S.sPOrderId,S.Qty 
								      FROM       yw1_scsheet    S 
                                      LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId
                                      LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
								      WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 GROUP BY S.sPOrderId 
						  )A  WHERE  getCanStock(A.sPOrderId,$StockSign)=$StockSign    GROUP BY A.DeliveryWeek";
		      break;
		}
		if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}

	//按周统计生产单数量（按生产单位）
	function get_semi_weekqty_s($workshopid,$index){

	    $SerarchRows='';$sql='';
	    $dataArray=array();
		switch($index){
			case -1://逾期及本周生产
			     $SerarchRows=' AND G.DeliveryWeek>0 AND G.DeliveryWeek<=' . $this->ThisWeek;
		    case  0://未出
		         $sql="SELECT G.DeliveryWeek,SUM(S.Qty-S.ScQty) AS Qty,SUM((S.Qty-S.ScQty)*GS.Price) AS Amount,COUNT(*) AS Counts  
						FROM yw1_scsheet    S
						LEFT JOIN cg1_stocksheet G  ON G.StockId=S.mStockId
						LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
						WHERE S.WorkShopId='$workshopid' AND S.Estate>0 AND S.ScFrom>0  $SerarchRows 
						GROUP BY G.DeliveryWeek";
				   break;
		    case  21://已备料
		         $StockSign  =3;
				 $sql = "SELECT A.DeliveryWeek,SUM(A.Qty) AS Qty,SUM(A.Qty*A.Price) AS Amount,COUNT(*) AS Counts 
					   FROM ( 
							   SELECT GM.DeliveryWeek,GS.Price,S.sPOrderId,(S.Qty-S.ScQty) Qty 
								      FROM       yw1_scsheet    S 
                                      LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId
                                      LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
								      WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 GROUP BY S.sPOrderId 
						  )A  WHERE  getCanStock(A.sPOrderId,$StockSign)=$StockSign    GROUP BY A.DeliveryWeek";
		      break;
		}
		if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}


	//按周显示生产单明细（按生产单位）
	function get_semi_weeksheet($workshopid,$index,$dyweek=0){

	    $sql='';
	    $dataArray=array();
		switch($index){
			case -1://逾期及本周生产
		    case  0://未出
		         $sql="SELECT A.sPOrderId,A.ScFrom,A.Estate,A.Qty,A.StockId,A.mStockId,
		                      G.POrderId,G.StuffId,G.DeliveryWeek,G.cgSign,D.StuffCname,D.Picture,
		                      IFNULL(Y.OrderPO,'') AS OrderPO,GS.Price,IFNULL(G.created,'') AS created,SUM(IFNULL(T.Qty,0)) AS ScQty ,A.Remark ,A.modified  ,A.modifier    
						FROM(
							SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,S.ScFrom,S.Estate,S.Remark  ,S.modified,S.modifier      
							      FROM yw1_scsheet S
						    WHERE S.WorkShopId='$workshopid' AND S.Estate>0  AND S.ScFrom>0   
						)A
						LEFT JOIN cg1_stocksheet  G ON G.StockId=A.mStockId 
						LEFT JOIN cg1_stocksheet GS ON GS.StockId=A.StockId
						LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
						LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
						LEFT JOIN sc1_cjtj T ON T.sPOrderId=A.sPOrderId 
						WHERE  G.DeliveryWeek='$dyweek' 
						GROUP BY A.sPOrderId ORDER BY DeliveryWeek";
		         break;
		    case 21://已备料
		         $StockSign  =3;
		         $sql="SELECT S.sPOrderId,S.ScFrom,S.Estate,S.Qty,S.ScQty,G.POrderId,S.StockId,S.mStockId,
	                    G.StuffId,G.DeliveryWeek,G.cgSign,D.StuffCname,D.Picture,IFNULL(Y.OrderPO,'') AS OrderPO,G.Price,
                        getLastStockTime(S.sPOrderId) AS created   ,S.Remark ,  S.modified ,S.modifier   
				   FROM (	
					    SELECT S.ScFrom,S.Estate,S.sPOrderId,S.ScQty,S.mStockId,S.StockId,S.Qty,G.DeliveryWeek,S.Remark,S.modified ,S.modifier   
					    FROM yw1_scsheet S
					    LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
					    WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0  AND S.ScFrom>0  AND G.DeliveryWeek='$dyweek' 
				    )S 
				    LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				    LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				   LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
				   WHERE getCanStock(S.sPOrderId,$StockSign)=$StockSign  ORDER BY DeliveryWeek,sPOrderId";


		      break;
		}
		if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}

	//待备料生产单明细（按加工类型）
	function get_semi_dblsheet($workshopid,$actionid)
	{
	   $StockSign=1;

	   $this->load->model('WorkShopdataModel');
	   $canWeeks=$this->WorkShopdataModel->get_canstock_weeks($workshopid);
	   $CanWeekStr    = " AND GS.DeliveryWeek<=$canWeeks ";

	   switch($actionid){
		  case '104'://开料
		      $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,G.cgSign,D.StuffCname,D.Picture,
		                      IFNULL(G.created,'') AS created,C.CutName   
				   FROM ( 
                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,getCanStock(S.sPOrderId,$StockSign) AS canStockSign      
					     FROM   yw1_scsheet    S 
					     LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					     WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 AND GS.DeliveryWeek>0 $CanWeekStr 
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN slice_cutdie    E ON E.StuffId=D.StuffId 
				LEFT JOIN pt_cut_data     C ON  C.Id  = E.CutId 
				WHERE S.canStockSign=$StockSign ORDER BY DeliveryWeek,sPOrderId";
		     break;
		  default:
		       $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,G.cgSign,D.StuffCname,D.Picture,
		                      IFNULL(Y.OrderPO,'') AS OrderPO,IFNULL(G.created,'') AS created   
				    FROM ( 
                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,getCanStock(S.sPOrderId,$StockSign) AS canStockSign      
					     FROM   yw1_scsheet    S 
					     LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					     WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 AND GS.DeliveryWeek>0 $CanWeekStr 
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
				WHERE S.canStockSign=$StockSign ORDER BY DeliveryWeek,sPOrderId";
		    break;
	   }
	  // echo $sql;
	   if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}

	function getyll_list($workshopid)
	{
			// $limits = "";
			// if($limitLine == 'body'){
			// 	$limits = " Limit 11,30";
			// }

		    $StockSign=3;
			$sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.Price,G.StuffId,G.DeliveryWeek,D.StuffCname,D.Picture,
		                      IFNULL(Y.OrderPO,'') AS OrderPO,Max(IFNULL(L.created,'')) AS created      
				FROM  yw1_scsheet S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN  ck5_llsheet    L ON L.sPOrderId=S.sPOrderId
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId
				WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 AND G.DeliveryWeek>0 AND getCanStock(S.sPOrderId,$StockSign)=$StockSign  GROUP BY S.sPOrderId ORDER BY DeliveryWeek,created";

			$query=$this->db->query($sql);
			return $query;
	}

	//待领料生产单明细（按加工类型）
	function get_semi_dllsheet($workshopid,$actionid)
	{
	   $StockSign=2;
	   switch($actionid){
	       case '104'://开料
		      $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,G.cgSign,D.StuffCname,D.Picture,
		                      getLastStockTime(S.sPOrderId) AS created,C.CutName   
				   FROM ( 
                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,getCanStock(S.sPOrderId,$StockSign) AS canStockSign      
					     FROM   yw1_scsheet    S 
					     LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					     WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 AND GS.DeliveryWeek>0 
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN slice_cutdie    E ON E.StuffId=D.StuffId 
				LEFT JOIN pt_cut_data     C ON  C.Id  = E.CutId 
				WHERE S.canStockSign=$StockSign ORDER BY DeliveryWeek,sPOrderId";
		     break;
		  default:
		       $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,G.cgSign,D.StuffCname,D.Picture,
		                      IFNULL(Y.OrderPO,'') AS OrderPO,getLastStockTime(S.sPOrderId) AS created     
				    FROM ( 
                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,getCanStock(S.sPOrderId,$StockSign) AS canStockSign      
					     FROM   yw1_scsheet    S 
					     LEFT  JOIN cg1_stocksheet GS ON GS.StockId=S.mStockId 
					     WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0 AND GS.DeliveryWeek>0 
				 )S 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
				LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
				LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
				WHERE S.canStockSign=$StockSign ORDER BY DeliveryWeek,sPOrderId";
		    break;
	   }

	   if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}



	//已备料生产单明细（按加工类型）
	function get_semi_bledsheet($workshopid,$actionid,$index=0,$spid=''){

	   $StockSign=3;
	   $hascheck = " AND getCanStock(S.sPOrderId,$StockSign)=$StockSign ";
	   switch($index){
		   case 20://当前生产中
		       $SerarchRows=$actionid==102?' AND EXISTS( SELECT T.sPOrderId FROM sc1_gxtj T WHERE T.sPOrderId=S.sPOrderId) AND S.scFrom>0':" AND S.ScQty>0  AND S.scFrom>0";
		       break;
	       case 22://本周+逾期生产中
	           $SerarchRows=' AND G.DeliveryWeek<=' . $this->ThisWeek;
	           break;
	       case 33:
	       		$SerarchRows=' AND S.sPOrderId=' . $spid;
	       		$hascheck = '';
	       		break;
	       default:
	           $SerarchRows='';

	           break;
	   }

	   switch($actionid){
		  case '104'://开料
		      $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,S.ScQty,G.POrderId,G.cgSign,S.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,
		                   D.Picture,IFNULL(Max(L.created),'') AS created,C.CutName,IFNULL(Y.OrderPO,'') AS OrderPO,G.Price,S.Remark  ,S.modified,S.modifier     
		            FROM  yw1_scsheet S
		            LEFT  JOIN cg1_stocksheet  G ON G.StockId=S.mStockId
		            LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
				    LEFT JOIN slice_cutdie     E ON E.StuffId=D.StuffId 
			    	LEFT JOIN pt_cut_data      C ON  C.Id  = E.CutId
			    	LEFT JOIN  ck5_llsheet     L ON L.sPOrderId=S.sPOrderId 
			    	LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId  
		            WHERE S.WorkShopId='$workshopid' AND S.Estate>0  $SerarchRows 
		                 $hascheck 
				    GROUP BY S.sPOrderId ORDER BY DeliveryWeek,created,sPOrderId";
		     break;
		  default:

		       $sql="SELECT S.sPOrderId,S.mStockId,S.Qty,S.ScQty,G.POrderId,G.cgSign,S.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,
		                    D.Picture,IFNULL(Max(L.created),'') AS created,IFNULL(Y.OrderPO,'') AS OrderPO,G.Price,S.Remark, S.modified ,S.modifier     
		            FROM  yw1_scsheet S
		            LEFT  JOIN cg1_stocksheet  G ON G.StockId=S.mStockId
		            LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
			    	LEFT JOIN  ck5_llsheet     L ON L.sPOrderId=S.sPOrderId 
			    	LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId  
		            WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  $SerarchRows  
		               $hascheck
				    GROUP BY S.sPOrderId ORDER BY DeliveryWeek,created,sPOrderId";
		    break;
	   }

	   if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}

	//获取是否为最后一张未生产工单
	function get_last_scorder($POrderId,$mStockId)
	{
	   if ($mStockId==''){
		    $sql = "SELECT COUNT(1) AS Counts,SUM(scSign) AS scSign
				FROM (
				    SELECT 1 AS Counts,IF(C.sPOrderId IS NULL,0,1) AS scSign
				    FROM yw1_scsheet S 
				    LEFT JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId
				    WHERE S.POrderId='$POrderId'  and LEVEL=1 GROUP BY S.sPOrderId 
				)A";
		}else{
		   $sql = "SELECT COUNT(1) AS Counts,SUM(scSign) AS scSign
				FROM (
				    SELECT 1 AS Counts,IF(C.sPOrderId IS NULL,0,1) AS scSign
				    FROM yw1_scsheet S 
				    LEFT JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId
				    WHERE S.mStockId='$mStockId' GROUP BY S.sPOrderId 
				)A";
		}
		$query = $this->db->query($sql);
		$row   = $query->first_row('array');
		$lastSign = $row['Counts']-$row['scSign'];

		return $lastSign>1?$lastSign:1;
	}

	//生产工单配件明细(新)
	function get_scorder_stocksheet($sPOrderId,$CheckSign)
	{
		$records  = $this->get_records($sPOrderId);
		$ActionId = $records['ActionId'];
		$mStockId = $records['mStockId'];
		$POrderId = $records['POrderId'];

		$lastSign = $this->get_last_scorder($POrderId,$mStockId);
		switch($ActionId){
			case 101://组装
			     $sql="SELECT S.POrderId,G.StockId,G.StuffId,IF($lastSign=1,G.OrderQty-IFNULL(LA.lledQty,0),ROUND(G.OrderQty*(S.Qty/Y.Qty),U.Decimals)) AS OrderQty,D.StuffCname,D.SendFloor,D.Picture,K.tStockQty,
	 L.Qty as llQty,L.Estate as llEstate,G.CompanyId,T.mainType,TM.blSign,IFNULL(W.Name,C.Forshort) AS Forshort,U.Decimals,S.ActionId    
				FROM yw1_scsheet S 
                INNER JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
				INNER JOIN cg1_stocksheet G ON G.POrderId=S.POrderId 
				INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
				INNER JOIN stufftype T ON T.TypeId=D.TypeId
				INNER JOIN stuffmainType TM ON TM.Id=T.mainType 
                INNER JOIN stuffunit U ON U.Id=D.Unit 
				INNER JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
                LEFT  JOIN yw1_scsheet SC  ON G.StockId = SC.mStockId 
                LEFT JOIN  workshopdata W ON W.Id = SC.WorkShopId
				LEFT JOIN  trade_object C ON C.CompanyId=G.CompanyId 
				 LEFT JOIN (
                     SELECT StockId,SUM(Qty) AS Qty,SUM(Estate) AS Estate FROM ck5_llsheet WHERE POrderId='$POrderId' AND sPOrderId='$sPOrderId' GROUP BY StockId 
                 )L ON L.StockId=G.StockId
                LEFT JOIN (
                     SELECT StockId,SUM(Qty) AS lledQty FROM ck5_llsheet WHERE POrderId='$POrderId' AND sPOrderId!='$sPOrderId' GROUP BY StockId 
                 )LA ON LA.StockId=G.StockId 
				WHERE S.sPOrderId='$sPOrderId' AND G.Level=1 AND ( TM.blSign=1  OR EXISTS(
				                        SELECT N.StuffId FROM  cg1_stuffunite N WHERE N.POrderId='$POrderId' AND N.StuffId=G.StuffId)
				                        )
				GROUP BY G.StockId ORDER BY TM.SortId,StockId";
			  break;

			default:
			   $sql="SELECT S.POrderId,G.StockId,G.StuffId,IF($lastSign=1,M.OrderQty-IFNULL(LA.lledQty,0),ROUND(M.OrderQty*(S.Qty/MG.OrderQty),U.Decimals)) AS OrderQty,D.StuffCname,D.SendFloor,D.Picture,K.tStockQty,
	   L.Qty as llQty,L.Estate as llEstate,G.CompanyId,T.mainType,TM.blSign,IFNULL(W.Name,C.Forshort) AS Forshort,U.Decimals,S.ActionId    
				FROM yw1_scsheet S 
                INNER JOIN cg1_semifinished M ON M.mStockId=S.mStockId 
				INNER JOIN cg1_stocksheet G ON G.StockId=M.StockId  
				INNER JOIN cg1_stocksheet MG ON MG.StockId=S.mStockId 
				INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
				INNER JOIN stufftype T ON T.TypeId=D.TypeId
				INNER JOIN stuffmainType TM ON TM.Id=T.mainType 
                INNER JOIN stuffunit U ON U.Id=D.Unit 
				INNER JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
                LEFT  JOIN yw1_scsheet SC  ON G.StockId = SC.mStockId 
                LEFT JOIN  workshopdata W ON W.Id = SC.WorkShopId
				LEFT JOIN  trade_object C ON C.CompanyId=G.CompanyId 
				LEFT JOIN (
                     SELECT StockId,SUM(Qty) AS Qty,SUM(Estate) AS Estate FROM ck5_llsheet WHERE POrderId='$POrderId' AND sPOrderId='$sPOrderId' GROUP BY StockId 
                 )L ON L.StockId=G.StockId
                LEFT JOIN (
                      SELECT L.StockId,SUM(L.Qty) AS lledQty 
                      FROM yw1_scsheet S 
                      LEFT JOIN ck5_llsheet L ON S.sPOrderId=L.sPOrderId 
                      WHERE S.mStockId='$mStockId' AND L.sPOrderId!='$sPOrderId' GROUP BY L.StockId 
                 )LA ON LA.StockId=G.StockId 
				WHERE S.sPOrderId='$sPOrderId'  AND TM.blSign=1  
				GROUP BY G.StockId ORDER BY TM.SortId,StockId";
			  break;
		}

		$query=$this->db->query($sql);
		return $query->result_array();
	}

	//生产单配件明细
	function get_semi_stocksheet($sPOrderId,$index){

	   switch($index){
		  case 2://待领料
		    $sql="SELECT S.sPOrderId,S.mStockId,S.scFrom,S.Estate,G.StuffId,G.OrderQty,D.StuffCname,D.Picture,K.tStockQty,U.Decimals,
		                 L.Id,L.StockId,IFNULL(L.Qty,0) AS llQty,L.Estate AS llEstate   
				FROM yw1_scsheet S 
				INNER JOIN ck5_llsheet L ON L.sPOrderId=S.sPOrderId  
                INNER JOIN cg1_stocksheet G ON G.StockId=L.StockId 
				INNER JOIN stuffdata D ON D.StuffId=L.StuffId 
				INNER JOIN stuffunit U ON U.Id=D.Unit 
				INNER JOIN ck9_stocksheet K ON  K.StuffId=L.StuffId 
				WHERE S.sPOrderId='$sPOrderId'";
				//echo $sql;
			break;

		default:
			$sql="SELECT S.sPOrderId,S.mStockId,S.scFrom,S.Estate,Y.StuffId,Y.OrderQty,
			    D.StuffCname,D.Picture,K.tStockQty,U.Decimals,
			    L.Id,G.StockId,SUM(IFNULL(L.Qty,0)) as llQty,IFNULL(SUM(IF (L.Estate>0,1,0)),0) as llEstate 
				FROM yw1_scsheet S 
				LEFT JOIN cg1_semifinished Y ON Y.mStockId=S.mStockId 
				LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId 
				LEFT JOIN cg1_stocksheet  G ON G.StockId=Y.StockId
				LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN stuffunit U ON U.Id=D.Unit  
				LEFT JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
				LEFT JOIN ck5_llsheet L ON L.sPOrderId=S.sPOrderId AND L.StockId=Y.StockId 
				WHERE S.sPOrderId='$sPOrderId' GROUP BY Y.StockId";
			break;
		}
	    $query=$this->db->query($sql);
		return $query->result_array();
	}

	function get_sc_stocksheet($sPOrderId){
		$sql="SELECT S.sPOrderId,S.mStockId,S.scFrom,S.Estate,G.StuffId,G.OrderQty,D.StuffCname,D.Picture,K.tStockQty,
			             L.Id,G.StockId,0 as llQty,0 as llEstate 
				FROM yw1_scsheet S 
				LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
				LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
				LEFT JOIN ck5_llsheet L ON L.sPOrderId=S.sPOrderId AND L.StockId=G.StockId 
				WHERE S.sPOrderId='$sPOrderId' GROUP BY G.StockId ";

		$query=$this->db->query($sql);
		return $query->result_array();
	}

	function get_stuff_stocksheet($sPOrderId){
		$sql="SELECT S.POrderId,S.mStockId,S.sPOrderId,S.scFrom,S.Estate,G.Id AS CGId,G.StuffId,G.OrderQty,D.StuffCname,D.SendFloor,D.Picture,K.tStockQty,
			             L.Id,G.StockId,SUM(L.Qty) as llQty,L.Estate as llEstate,G.CompanyId,C.Forshort,T.mainType,M.blSign ,ifnull(W.Name,C.Forshort ) ForName,
			             W.Id wsId,R.LocationId ,GROUP_CONCAT(DISTINCT(concat(O.Region,O.Location)) separator ' ')  as location,M.SortId 
				FROM yw1_scsheet S 
				INNER JOIN cg1_stocksheet G ON G.POrderId=S.POrderId 
				INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
				INNER JOIN stufftype T ON T.TypeId=D.TypeId
				INNER JOIN stuffmainType M ON M.Id=T.mainType 
				INNER JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
				LEFT  JOIN yw1_scsheet    SC  ON G.StockId = SC.mStockId 
                LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
				LEFT JOIN trade_object C ON C.CompanyId=G.CompanyId 
				LEFT JOIN ck5_llsheet L ON L.sPOrderId=S.sPOrderId AND L.StockId=G.StockId 
                LEFT JOIN ck1_rksheet R ON L.RkId=R.Id  
                LEFT JOIN ck_location O ON R.LocationId=O.Id 
				WHERE S.sPOrderId='$sPOrderId' AND G.Level=1 AND T.mainType!=2 
				              AND NOT EXISTS(SELECT CG.StockId FROM cg1_stuffcombox CG WHERE CG.POrderId=S.POrderId  AND CG.mStockId=G.StockId ) 
				GROUP BY G.StockId
 UNION ALL 
               SELECT S.POrderId,S.mStockId,S.sPOrderId,S.scFrom,S.Estate,MG.Id AS CGId,G.StuffId,G.OrderQty,D.StuffCname,D.SendFloor,D.Picture,K.tStockQty,
			             L.Id,G.StockId,SUM(L.Qty) as llQty,L.Estate as llEstate,MG.CompanyId,C.Forshort,T.mainType,M.blSign ,ifnull(W.Name,C.Forshort ) ForName,
			             W.Id wsId  ,R.LocationId ,GROUP_CONCAT(DISTINCT(concat(O.Region,O.Location)) separator ' ')  as location,M.SortId 
				FROM yw1_scsheet S 
				INNER JOIN cg1_stocksheet MG ON MG.POrderId=S.POrderId 
                INNER JOIN cg1_stuffcombox G ON G.POrderId=S.POrderId AND G.mStockId=MG.StockId
				INNER JOIN stuffdata D ON D.StuffId=G.StuffId 
				INNER JOIN stufftype T ON T.TypeId=D.TypeId
				INNER JOIN stuffmainType M ON M.Id=T.mainType 
				INNER JOIN ck9_stocksheet K ON  K.StuffId=G.StuffId 
				LEFT  JOIN yw1_scsheet    SC  ON G.StockId = SC.mStockId 
                LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
				LEFT JOIN trade_object C ON C.CompanyId=MG.CompanyId 
				LEFT JOIN ck5_llsheet L ON L.sPOrderId=S.sPOrderId AND L.StockId=G.StockId 
                LEFT JOIN ck1_rksheet R ON L.RkId=R.Id  
                LEFT JOIN ck_location O ON R.LocationId=O.Id 
				WHERE S.sPOrderId='$sPOrderId' AND MG.Level=1 AND T.mainType!=2
				GROUP BY G.StockId
		ORDER BY SortId,CGId";

		$query=$this->db->query($sql);
		return $query->result_array();
	}

	//生产单月准时率
	function get_scsheet_punctuality($workshopid,$month=''){

	   $month=$month==''?date('Y-m',strtotime($this->Date)):$month;

	   $sql = "SELECT COUNT(*) AS Counts,SUM(IF(A.DeliveryWeek<A.FinishWeek,1,0)) AS OverCounts 
						 FROM (
							SELECT G.DeliveryWeek,YEARWEEK(S.FinishDate,1) AS FinishWeek 
							FROM yw1_scsheet S  
							LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId 
							WHERE DATE_FORMAT(S.FinishDate,'%Y-%m')='$month' AND  S.WorkShopId='$workshopid' AND S.ScFrom=0 
							GROUP BY S.sPOrderId 
				)A ";

	   $query=$this->db->query($sql);
	   $row = $query->first_row();
	   $counts=$row->Counts;
	   $overCounts=$row->OverCounts;

	   return $counts>0?round(($counts-$overCounts)/$counts*100):100;
	 }

	//已生产工单月统计（按生产单位）
	function get_month_qty($workshopid,$month=''){
	    $month=$month==''?date('Y-m',strtotime($this->Date)):$month;
	    $sql = "SELECT SUM(S.Qty) AS Qty 
	                   FROM  yw1_scsheet S 
	                   WHERE S.WorkShopId='$workshopid' AND DATE_FORMAT(S.FinishDate,'%Y-%m')=? AND S.ScFrom=0  ";
	   $query=$this->db->query($sql,$month);
	   $row = $query->first_row();
	   return $row->Qty;
	}

	//已生产工单月分类统计（按生产单位）
	function get_month_sced($workshopid){
	    $sql = "SELECT DATE_FORMAT(S.FinishDate,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,SUM(IFNULL(G.Price*S.Qty,0)) AS Amount 
	                  FROM  yw1_scsheet S 
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
	                  WHERE S.WorkShopId='$workshopid' AND S.ScFrom=0  
	                  GROUP BY DATE_FORMAT(S.FinishDate,'%Y-%m') ORDER BY Month DESC";
	   $query=$this->db->query($sql);
	   return $query->result_array();
	}

	//已出货工单月分类统计（按生产单位）
	function get_month_scsh($workshopid){
	    $sql = "select DATE_FORMAT(Y.shDate,'%Y-%m') AS Month,SUM(Y.Qty) AS Qty,SUM(IFNULL(G.Price*Y.Qty,0)) AS Amount 
			                 from 
	                  yw1_scsheet S
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId 
	                  LEFT JOIN gys_shsheet Y ON Y.StockId = G.StockId 
	                  where s.workshopid=$workshopid and Y.shDate is not null
                           GROUP BY DATE_FORMAT(Y.shDate,'%Y-%m') ORDER BY Month DESC";
	   $query=$this->db->query($sql);
	   return $query->result_array();
	}

	//已生产工单月分类统计（按生产单位）
	function get_month_ysced($workshopid){
	    $sql = "SELECT DATE_FORMAT(S.FinishDate,'%Y-%m') AS Month,SUM(S.Qty) AS Qty
	                  FROM  yw1_scsheet S 
	                  WHERE S.WorkShopId=$workshopid AND S.ScFrom=0  
	                  GROUP BY DATE_FORMAT(S.FinishDate,'%Y-%m') ORDER BY Month DESC";
	   $query=$this->db->query($sql);
	   return $query->result_array();
	}

	//已生产工单日分类统计（按生产单位）
	function get_day_sced($workshopid){
	    $sql = "SELECT DATE_FORMAT(S.FinishDate,'%Y-%m-%d') AS Date,SUM(S.Qty) AS Qty,SUM(IFNULL(G.Price*S.Qty,0)) AS Amount 
	                  FROM  yw1_scsheet S 
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
	                  WHERE S.WorkShopId='$workshopid' AND S.ScFrom=0  
	                  GROUP BY DATE_FORMAT(S.FinishDate,'%Y-%m-%d') ORDER BY Date DESC";
	   $query=$this->db->query($sql);
	   return $query->result_array();
	}

		//已出货工单月分类统计（按生产单位）
	function get_day_scsh_inMon($workshopid,$mon){
	    $sql = "select DATE_FORMAT(Y.shDate,'%Y-%m-%d') Date,SUM(Y.Qty) AS Qty,SUM(IFNULL(G.Price*Y.Qty,0)) AS Amount 
			                 from 
	                  yw1_scsheet S
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId 
	                  LEFT JOIN gys_shsheet Y ON Y.StockId = G.StockId 
	                  where s.workshopid=$workshopid and Y.shDate is not null
	                  AND DATE_FORMAT(Y.shDate,'%Y-%m')='$mon'
                           GROUP BY DATE_FORMAT(Y.shDate,'%Y-%m-%d') ORDER BY Date DESC";
	   $query=$this->db->query($sql);
	   return $query->result_array();
	}

	//已生产工单日分类统计（按生产单位）
	function get_day_sced_inmonth($workshopid,$mon){
	    $sql = "SELECT DATE_FORMAT(S.FinishDate,'%Y-%m-%d') AS Date,SUM(S.Qty) AS Qty,SUM(IFNULL(G.Price*S.Qty,0)) AS Amount 
	                  FROM  yw1_scsheet S 
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
	                  WHERE S.WorkShopId='$workshopid' AND S.ScFrom=0  
	                  AND DATE_FORMAT(S.FinishDate,'%Y-%m')='$mon'
	                  GROUP BY DATE_FORMAT(S.FinishDate,'%Y-%m-%d') ORDER BY Date DESC";
	   $query=$this->db->query($sql);
	   return $query->result_array();
	}

	function get_nosend_inwork($workshopid) {
		$sql = "	SELECT SUM(A.Qty-A.rkQty-A.shQty) NoSendQty FROM (
						SELECT  S.Qty,sum(ifnull(R.Qty,0)) rkQty,sum(ifnull(G.Qty,0)) shQty
	                  FROM  yw1_scsheet S 
	                  
				LEFT JOIN ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId
				LEFT JOIN gys_shsheet G ON S.mStockId=G.StockId AND S.sPOrderId=G.sPOrderId AND G.Estate>0
				WHERE S.WorkShopId=?  AND S.Estate>0  AND S.ScQty>0 
				GROUP BY s.sPOrderId
				) A;";
		$query=$this->db->query($sql,$workshopid);
		$row = $query->first_row();
	    $NoSendQty=$row->NoSendQty;
		return $NoSendQty>0?$NoSendQty:0;
	}

	function get_scingnosend_inwork($workshopid) {
		$sql = "	SELECT SUM(A.Qty-A.rkQty-A.shQty) qty,COUNT(*) cts FROM (
						SELECT  S.Qty,sum(ifnull(R.Qty,0)) rkQty,sum(ifnull(G.Qty,0)) shQty
	                  FROM  yw1_scsheet S 
	                  
				LEFT JOIN ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId
				LEFT JOIN gys_shsheet G ON S.mStockId=G.StockId AND S.sPOrderId=G.sPOrderId AND G.Estate>0
				WHERE S.WorkShopId=?  AND S.Estate>0  AND S.ScQty>0 AND S.ScFrom>0
				GROUP BY s.sPOrderId
				) A;";
		$query=$this->db->query($sql,$workshopid);
		$row = $query->first_row();
	    return $row;
	}

	function get_scednosend_inwork($workshopid) {
		$sql = "SELECT SUM(A.Qty-A.rkQty-A.shQty) qty,COUNT(*) cts FROM (
				SELECT  S.Qty,sum(ifnull(R.Qty,0)) rkQty,sum(ifnull(G.Qty,0)) shQty
	            FROM  yw1_scsheet S 
	                  
				LEFT JOIN ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId
				LEFT JOIN gys_shsheet G ON S.mStockId=G.StockId AND S.sPOrderId=G.sPOrderId AND G.Estate>0
				WHERE S.WorkShopId=?  AND S.Estate>0  AND S.ScQty>0 AND S.ScFrom=0
				GROUP BY s.sPOrderId
				) A;";
		$query=$this->db->query($sql,$workshopid);
		$row = $query->first_row();
	    return $row;
	}

	//TV 已生产未送货明细 按生产单位
	function  get_scorder_nosendweb($workshopid=0){
		if ($workshopid == '102' || $workshopid=='103') {
			$sql = "SELECT  S.sPOrderId,S.Qty,IFNULL(A.rkQty,0) rkQty,IFNULL(B.shQty,0) shQty,S.ScFrom,S.ScQty,
				G.DeliveryWeek,G.StuffId,D.StuffCname,Max(T.OPdatetime) lasttime,Min(T.OPdatetime) firsttime
	            FROM  yw1_scsheet S  
                LEFT JOIN (
                       SELECT S.sPOrderId,sum(R.Qty) AS rkQty 
                       FROM yw1_scsheet S  
	                   LEFT JOIN ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId  
                       WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0 GROUP BY S.sPOrderId
                  )A ON A.sPOrderId=S.sPOrderId 
				LEFT JOIN (
                       SELECT S.sPOrderId,sum(GS.Qty) AS shQty
                       FROM yw1_scsheet S  
	                   LEFT JOIN gys_shsheet GS ON S.mStockId=GS.StockId AND S.sPOrderId=GS.sPOrderId AND GS.Estate>0
                       WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0 GROUP BY S.sPOrderId
                  )B ON B.sPOrderId=S.sPOrderId 
                LEFT JOIN  sc1_gxtj T ON T.sPOrderId = S.sPOrderId
	            LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId
	            LEFT JOIN stuffdata D ON D.StuffId = G.StuffId
				WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0 
				GROUP BY S.sPOrderId";
		} else {
			$sql = "SELECT  S.Qty,IFNULL(A.rkQty,0) rkQty,IFNULL(B.shQty,0) shQty,S.ScFrom,S.ScQty,
				G.DeliveryWeek,G.StuffId,D.StuffCname,Max(T.created) lasttime,Min(T.created) firsttime
	            FROM  yw1_scsheet S  
                LEFT JOIN (
                       SELECT S.sPOrderId,sum(R.Qty) AS rkQty 
                       FROM yw1_scsheet S  
	                   LEFT JOIN ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId  
                       WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0 GROUP BY S.sPOrderId
                  )A ON A.sPOrderId=S.sPOrderId 
				LEFT JOIN (
                       SELECT S.sPOrderId,sum(GS.Qty) AS shQty
                       FROM yw1_scsheet S  
	                   LEFT JOIN gys_shsheet GS ON S.mStockId=GS.StockId AND S.sPOrderId=GS.sPOrderId AND GS.Estate>0
                       WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0 GROUP BY S.sPOrderId
                  )B ON B.sPOrderId=S.sPOrderId 
                LEFT JOIN  sc1_cjtj T ON T.sPOrderId = S.sPOrderId
	            LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId
	            LEFT JOIN stuffdata D ON D.StuffId = G.StuffId
				LEFT JOIN ck1_rksheet R ON S.mStockId=R.StockId AND S.sPOrderId=R.sPOrderId
				LEFT JOIN gys_shsheet GS ON S.mStockId=GS.StockId AND S.sPOrderId=GS.sPOrderId AND GS.Estate>0
				WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0 
				GROUP BY S.sPOrderId
				order by DeliveryWeek,lasttime";
		}
		$query=$this->db->query($sql);
		return $query;

	}

	//已生产未送货明细 按生产单位
	function  get_scorder_nosend($workshopid){

		$sql = "SELECT  S.sPOrderId,S.mStockId,S.StockId,S.Qty,D.StuffCname,D.Picture,M.PurchaseID,
		         G.Price,G.DeliveryWeek,S.Estate,S.ScFrom,D.Picture,G.StuffId 
	                  FROM  yw1_scsheet S 
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId
	                  LEFT JOIN cg1_stockmain M ON M.Id = G.Mid
	                  LEFT JOIN stuffdata D ON D.StuffId = G.StuffId
	                  WHERE S.WorkShopId='$workshopid'  AND S.Estate>0  AND S.ScQty>0";
	   $query=$this->db->query($sql);
	   $this->load->model('GysshsheetModel');
	   $this->load->model('CkrksheetModel');
	   $this->load->model('ScCjtjModel');
	   $dataArray = array();
	   $k=0;
	   foreach ($query->result_array() as $row){
             $sPOrderId  = $row['sPOrderId'];
             $mStockId   = $row['mStockId'];
             $Qty        = $row['Qty'];
             $StuffCname = $row['StuffCname'];
             $PurchaseID = $row['PurchaseID'];
             $DeliveryWeek = $row['DeliveryWeek'];
             $scQty = $this->ScCjtjModel->get_scqty($sPOrderId);
             $lastOper = $this->ScCjtjModel->get_sc_lastoper($sPOrderId);
             $shQty = $this->GysshsheetModel->get_scorder_shqty($sPOrderId,$mStockId);
             $rkQty = $this->CkrksheetModel->get_scorder_rkqty($sPOrderId,$mStockId);
             $totalQty = $shQty + $rkQty;

             if($scQty-$totalQty>0){
                 $dataArray[$k]["sPOrderId"]   =$row['sPOrderId'];
                 $dataArray[$k]["StockId"]     =$row['StockId'];
                 $dataArray[$k]["mStockId"]    =$row['mStockId'];
                 $dataArray[$k]["StuffId"]     =$row['StuffId'];
                 $dataArray[$k]["Picture"]     =$row['Picture'];
	             $dataArray[$k]["StuffCname"]  =$StuffCname;
	             $dataArray[$k]["PurchaseID"]  =$PurchaseID;
	             $dataArray[$k]["DeliveryWeek"]=$DeliveryWeek;
	             $dataArray[$k]["djDate"]      =$this->ScCjtjModel->get_scdjtime($sPOrderId);
	             $dataArray[$k]["Qty"]         = $Qty;
	             $dataArray[$k]["ScQty"]       = $scQty;
	             $dataArray[$k]["lastOper"]       = "$lastOper";

	             $dataArray[$k]["ShedQty"]       = $totalQty;
	             $dataArray[$k]["Price"]       = $row['Price'];
	             $dataArray[$k]["Estate"]      = $row['Estate'];
	             $dataArray[$k]["ScFrom"]      = $row['ScFrom'];
	             $k++;
             }
		}
		return $dataArray;
	}


	//更新生产工单状态
	function update_estate($sPOrderId,$Estate){

	   $data=array('Estate'  =>$Estate,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );

	   $this->db->update('yw1_scsheet',$data, array('sPOrderId' => $sPOrderId));

	   return $this->db->affected_rows();
   }

   //设置生产工单备注
	function set_remark($sPOrderId,$remark){

	   $data=array('Remark'  =>$remark,
	               'modifier'=>$this->LoginNumber,
	               'modified'=>$this->DateTime
	              );

	   $this->db->update('yw1_scsheet',$data, array('sPOrderId' => $sPOrderId));

	   return $this->db->affected_rows();
   }


   function get_line_letter($sporderid) {
	   $sql = "SELECT  L.Letter  FROM  
			yw1_scsheet S 
			LEFT JOIN workscline L ON L.Id = S.scLineId
			WHERE S.sPOrderId=$sporderid";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->Letter;
		}
		return '';

   }

   //date生产单明细（按加工类型）
	function get_datesheet($workshopid,$actionid,$date=''){

	   switch($actionid){


		  case '101'://包装


			$sql = "SELECT SUM( C.Qty ) AS scdayQty, S.ScQty, S.Qty, G.Price, S.sPOrderId, S.mStockId, S.POrderId, S.StockId, IFNULL( PI.LeadWeek, PL.LeadWeek ) AS LeadWeek, P.cName, P.TestStandard, P.ProductId, L.Letter AS Line,L.GroupId,M.OrderPO 
			FROM sc1_cjtj C
			LEFT JOIN yw1_scsheet S ON S.sPOrderId = C.sPOrderId
			LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
			      LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
			INNER JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId
			INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
			INNER JOIN productdata P ON P.ProductId = Y.ProductId
			LEFT JOIN yw3_pisheet PI ON PI.oId = Y.Id
			LEFT JOIN yw3_pileadtime PL ON PL.POrderId = Y.POrderId
			LEFT JOIN workscline L ON L.Id = S.scLineId
			WHERE S.WorkShopId =  '$workshopid'
			AND C.Date =  '$date'
			GROUP BY S.sPOrderId
			ORDER BY Line,LeadWeek";

		  break;

		  default:

		       $sql=" SELECT SUM(C.Qty) AS scdayQty,S.ScQty, S.Qty, G.Price ,S.sPOrderId,
      S.mStockId,G.POrderId,S.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,
		                    D.Picture 
      FROM  sc1_cjtj C
      LEFT JOIN yw1_scsheet S ON S.sPOrderId=C.sPOrderId  
      LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId 
      LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
      WHERE S.WorkShopId='$workshopid' AND C.Date='$date' 
      GROUP BY S.sPOrderId ";
		    break;
	   }

	   if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}


//date生产单明细（按加工类型）
/*
	select Y.Date,SUM(S.Qty) AS Qty,SUM(IFNULL(G.Price*S.Qty,0)) AS Amount
			                 from
	                  yw1_scsheet S
	                  LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId
	                  LEFT JOIN gys_shsheet Y ON Y.StockId = G.StockId
	                  where s.workshopid=102 and Y.Date is not null
	                  AND DATE_FORMAT(Y.Date,'%Y-%m')='$mon'
                           GROUP BY Y.Date ORDER BY Y.Date DESC
*/
		function get_sh_datesheet($workshopid,$actionid,$date=''){





	   switch($actionid){

		  default:

		       $sql=" SELECT S.ScQty, S.Qty, G.Price ,S.sPOrderId,Y.shDate,
      S.mStockId,G.POrderId,S.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,
		                    D.Picture,M.Name Operator ,Y.Qty as shQty
      FROM yw1_scsheet S

      LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId 
      LEFT JOIN gys_shsheet Y ON Y.StockId = G.StockId
       LEFT JOIN staffmain       M ON M.Number=Y.Operator
      LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
      WHERE S.WorkShopId='$workshopid' AND DATE_FORMAT(Y.shDate,'%Y-%m-%d')='$date' 
      GROUP BY S.sPOrderId  order by  G.DeliveryWeek ,Y.Id ";
		    break;
	   }

	   if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}



   //date生产单明细（按加工类型）
	function mon_yscsheet($workshopid,$actionid,$mon=''){
	   switch($actionid){
		   case '101'://包装

		   $sql = "SELECT  SUM(C.Qty) as ScQty, S.Qty, G.Price, S.sPOrderId, S.mStockId, S.POrderId, S.StockId, IFNULL( PI.LeadWeek, PL.LeadWeek ) AS LeadWeek, P.cName, P.TestStandard, P.ProductId, L.Letter AS Line,L.GroupId,M.OrderPO 
				FROM yw1_scsheet S 
				LEFT JOIN sc1_cjtj C ON S.sPOrderId = C.sPOrderId
				LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId 
				      LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
				INNER JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId
				INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
				INNER JOIN productdata P ON P.ProductId = Y.ProductId
				LEFT JOIN yw3_pisheet PI ON PI.oId = Y.Id
				LEFT JOIN yw3_pileadtime PL ON PL.POrderId = Y.POrderId
				LEFT JOIN workscline L ON L.Id = S.scLineId
				WHERE S.WorkShopId =  '$workshopid'
				AND S.ScFrom=0 AND DATE_FORMAT(S.FinishDate,'%Y-%m')='$mon' 
				GROUP BY S.POrderId
				ORDER BY LeadWeek";

		  break;

		  default:

		       $sql=" SELECT S.ScQty, S.Qty, G.Price ,S.sPOrderId,
      S.mStockId,G.POrderId,S.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,
		                    D.Picture
      FROM yw1_scsheet S 
      LEFT JOIN cg1_stocksheet G ON G.StockId = S.mStockId 
      LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
      WHERE S.WorkShopId='$workshopid' AND S.ScFrom=0 AND DATE_FORMAT(S.FinishDate,'%Y-%m')='$mon' 
      GROUP BY S.sPOrderId ";
		    break;
	   }

	   if ($sql!=''){
			$query=$this->db->query($sql);
			$dataArray= $query->result_array();
		}
		return $dataArray;
	}



	function get_ll_info($sporderid) {
		$sql = "SELECT getCanStock('$sporderid',3) as CanStock";

		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->first_row('array');
		}
		return 0;
	}

	function semi_bomhead($mStockId) {
		$sql = "SELECT S.OrderPo,D.StuffCname,K.tStockQty ,W.Name,SC.WorkShopId,CG.Price,CG.StuffId,SC.Qty ,CG.DeliveryWeek 
		FROM yw1_scsheet SC
		LEFT JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
		LEFT JOIN cg1_stocksheet CG ON CG.StockId = SC.mStockId
		LEFT JOIN stuffdata D ON D.StuffId = CG.StuffId
		LEFT JOIN ck9_stocksheet K ON K.StuffId = D.StuffId
		LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
		WHERE SC.mStockId =  '$mStockId'
		";
		$query = $this->db->query($sql);
		return $query;
	}

	function semi_bomlist($mStockId) {
		$this->load->model('StuffdataModel');
		$sql = "SELECT SC.Qty,(CG.addQty+CG.FactualQty) AS xdQty,SC.mStockId,SC.POrderId 
		FROM  yw1_scsheet SC 
		LEFT  JOIN yw1_ordersheet    S  ON S.POrderId = SC.POrderId 
		LEFT  JOIN cg1_stocksheet    CG ON CG.StockId = SC.mStockId WHERE SC.mStockId ='$mStockId'";

		$query = $this->db->query($sql);
		$POrderId = 0;
		$Qty = $xdQty= $Relation ='';
		$Relation = 1;
		if ($query->num_rows() > 0) {
			$checkOrderRow = $query->row_array();
			$Qty = $checkOrderRow["Qty"];
			$xdQty=$checkOrderRow["xdQty"];
			$Relation=$Qty/$xdQty;
			$POrderId=$checkOrderRow["POrderId"];
		}



	$sListSql ="SELECT G.POrderId,ROUND(A.OrderQty*1,U.Decimals) AS OrderQty,A.StockId,
		        G.CompanyId,G.BuyerId,D.StuffId,D.StuffCname,D.Picture,D.Gfile,D.Gstate,D.TypeId,D.Price,F.Remark AS Position,M.Name,ifnull(W.Name,P.Forshort ) Forshort ,W.Id wsId
		         ,C.Prechar,T.mainType,K.tStockQty ,T.mainType ,A.Relation 
	            FROM  cg1_semifinished   A 
                INNER JOIN cg1_stocksheet G  ON G.StockId = A.StockId
                LEFT  JOIN yw1_scsheet    SC  ON A.StockId = SC.mStockId 
                LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
				INNER JOIN ck9_stocksheet K ON K.StuffId=A.StuffId 
				INNER JOIN stuffdata D ON D.StuffId=A.StuffId 
				INNER JOIN stufftype T ON T.TypeId=D.TypeId
				INNER JOIN stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN stuffunit U ON U.Id=D.Unit
				LEFT JOIN  staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN  trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  currencydata C ON C.Id = P.Currency
				LEFT JOIN  base_mposition F ON F.Id=D.SendFloor
				WHERE  A.POrderId='$POrderId' AND A.mStockId='$mStockId' ORDER BY G.blsign DESC,G.StockId";

	$query = $this->db->query($sListSql);

	$dataArray = array();
	$taxPrice = 0;
	if ($query->num_rows() > 0) {
		foreach ($query->result_array() as $row) {
			$StuffId = $row['StuffId'];
			$tStockQty = $row['tStockQty'];
			$OrderQty = $row['OrderQty'];
			$StuffCname = $row['StuffCname'];
			$Price = $row['Price'];
			$wsid = $row['wsId'];
			$Prechar = $row['Prechar'];
			$Picture = $row['Picture'];
			$Forshort = $row['Forshort'];
			$mainType = $row['mainType'];
			$halfImg = '';
			if ($mainType == 7) {

				$checkBom = $this->semi_bomhead($row['StockId']);
				if ($checkBom->num_rows() > 0) {
					$halfImg = 'halfProd';
					if ($this->check_issemi_bomed($row['StockId']) == false) {
					$halfImg='half_grayed';
					}
				}

			}
			if ($Forshort == '研砼加工') {
				$Forshort = '';
			}
			$url = '';
			if ($Picture > 0)
			$url = $this->StuffdataModel->get_stuff_icon($StuffId);

			$Price=$row["Price"];


			$Relation=$row["Relation"];
			$RelArray=explode("/", $Relation);
			$mRelation=count($RelArray)==2?$RelArray[0]/$RelArray[1]:$RelArray[0];
			$taxPrice+=round($Price*$mRelation,4);

			$dataArray[]=array(
				'isSemi'=>'1',
				'col1'=>number_format($OrderQty),
				'col2'=>number_format($tStockQty),
				'col3'=>$Prechar.$Price,
				'col3Img'=>'',
				'wsImg'=>'ws_'.$wsid,
				'col4'=>($Forshort),
				'title'=>array('Text'=>$StuffCname,'Color'=>'#3b3e41'),
				'Picture'=>''.$Picture,
				'halfImg'=>$halfImg,
				'mainType'=>$row['mainType'].'',
				'StuffId'=>''.$row['StuffId'],
				'StockId'=>''.$row['StockId'],
				'url'=>$url

			);
		}
	}
	$taxPrice=round($taxPrice,4);

	return  array('taxPrice'=>$taxPrice,'list'=>$dataArray);

	}


	function get_nosc_abnormal_all($workshopid) {


		$sql = "
			
			SELECT sum(1) as Counts,
	   sum(A.Qty-A.ScQty) AS Qty, 
	   sum((A.Qty-A.ScQty)*A.Price) AS Amount, 
       sum(if(A.SemiId is null or A.Locks>=1 or A.Picture>1,1,0)) AbCounts,
	   sum(if(A.SemiId is null or A.Locks>=1 or A.Picture>1,(A.Qty-A.ScQty),0)) AbQty,
	   sum(if(A.SemiId is null or A.Locks>=1 or A.Picture>1,((A.Qty-A.ScQty)*A.Price),0)) AbAmount
						 FROM (
							SELECT S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty,CG.Id as SemiId ,
if((ifnull(E.Id,0)+ifnull(GL.Id,0))>0,1,0) as Locks,D.Picture,GS.Price
							FROM   yw1_scsheet S 
							LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
							LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId
							LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId  
							LEFT JOIN cg1_semifinished CG ON CG.mStockId = G.StockId
							LEFT JOIN cg1_lockstock GL  ON G.StockId=GL.StockId and GL.Locks=0
							LEFT JOIN yw2_orderexpress  E  ON G.POrderId=E.POrderId and E.Type=2
							LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
							WHERE S.WorkShopId='$workshopid' AND S.ScFrom>0 AND S.Estate>0  GROUP BY S.sPOrderId 
						)A ;
		
		";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		return null;

	}





}