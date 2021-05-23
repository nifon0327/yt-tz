<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Ch1shipsheetModel extends MC_Model{

    
    function __construct()
    {
        parent::__construct();
    }
    
    
     function get_overday_frommode(&$PayMode) {
	    $overValue = -2;
   
	    switch($PayMode){
			case 11:
				$overValue = 0;
				break;	
			case 12:
				$overValue = 15;
				break;	
			case 13:
			case 15:
			case 16:
				$PayMode = 13;
				$overValue = 30;
			break;	
			case 17:
			case 18:
				$PayMode = 17;
				$overValue = 60;
			break;	
			case 19:
			
				$overValue = 90;
			break;			
			default:
			break;
					
		}

		return $overValue;
    }
    
    function company_month_unrecived($CompanyId) {
	    
	    
	    $this->load->model('TradeObjectModel');
	    $PreChar = $this->TradeObjectModel->get_prechar($CompanyId);
        $PayMode = '';
        $ChinaSafe = '';
	    $CompanyInfo = $this->TradeObjectModel->get_records($CompanyId);
	    if ($CompanyInfo != null) {
		    $ChinaSafe = $CompanyInfo['ChinaSafe'];
		    $PayMode = $CompanyInfo['PayMode'];
	    }
	    
	    
	    $sql = "SELECT SUM(S.Price*S.Qty*M.Sign) AS Amount,DATE_FORMAT(M.Date,'%Y-%m') AS Month   
        FROM ch1_shipmain M
        LEFT JOIN ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
        LEFT JOIN currencydata D ON D.Id = C.Currency
        WHERE M.Estate=0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' 
        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month;";
        
        $query=$this->db->query($sql);
        
        
        $allAmount = 0;
        $allOverAmount = 0;
        
        $nowMonth = date('Y-m');
		$nowMonthInterval = strtotime($nowMonth);
		$oneDaySeconds = 60*60*24*1;
        if ($query->num_rows() > 0) {
	        $rs = $query->result_array();
	        
	        $rows = $query->row_array();
	        
	        
	        
	        $overValue = $this->get_overday_frommode($PayMode);
	        
	        foreach ($rs as $rows) {
		        
		        $Month   = $rows['Month'];
		        $Amount  = $rows['Amount'];
		        
		        
		        
		        //部分收款
		        $partRecived = $this->db->query("SELECT SUM(P.Amount*M.Sign) AS GatheringSUM 
					FROM cw6_orderinsheet P
					LEFT JOIN ch1_shipmain M ON M.Id=P.chId 
					LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
					LEFT JOIN currencydata D ON D.Id = C.Currency
					WHERE M.cwSign='2' and  DATE_FORMAT(M.Date,'%Y-%m')='$Month' AND M.CompanyId=$CompanyId;");
				if ($partRecived->num_rows() > 0) {
					$Amount -= $partRecived->row()->GatheringSUM;
					
				}
				
				if ($Amount > 0 && $overValue > -2 ) {
		      
					$singleInterval = strtotime($Month);
			  		$betweentInterval = $nowMonthInterval - $singleInterval - $oneDaySeconds*$overValue;
	    //逾期
			        if ($betweentInterval >= $oneDaySeconds) {
				        $allOverAmount += $Amount;
			        }
		        }
				
			    $allAmount +=  $Amount;
		        
	        }
	        
	        
/*
	         //预收货款
			$CheckPreJY = $this->db->query("SELECT IFNULL(SUM(Amount),0) FK_JY FROM cw6_advancesreceived
							WHERE CompanyId='$CompanyId' AND Mid='0'");
							
			if ($CheckPreJY->num_rows() > 0) {
				$allAmount -= $CheckPreJY->row()->FK_JY;
					
			}
*/
	        
        }
	    
	    return array('all'=>$allAmount, 'over'=>$allOverAmount, 'prechar'=>$PreChar, 'safe'=>$ChinaSafe, 'mode'=>$PayMode);
	    
    }
    
    function get_ch_imgs($Mid) {
	    $sql ="select  Id,Remark,Picture  from ch7_shippicture where Mid=$Mid order by id";
	    $query=$this->db->query($sql);
		return  $query;
    }
    
    function get_order_punctuality($PuncSelectType,$info1='',$info2='') {
	    //订单准时率统计
		$Punc_Percent=0;
		$Punc_Color = '#358fc1';
		$Punc_FilterTerms="";
		switch($PuncSelectType){
			case 1:
			        $Punc_FilterTerms=" M.Estate='0' and  DATE_FORMAT(M.Date,'%Y-%m')='$info1' ";
			       break;
		    case 2:
		          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$info2'  and DATE_FORMAT(M.Date,'%Y-%m')='$info1' ";
		           break;
		    case 3:
		              $Punc_FilterTerms=" M.Id='$info1' ";
				 break;
		    case 4:
			        $Punc_FilterTerms=" M.Estate='0' and  M.Date='$info1' ";
			       break;
		    case 5:
		          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$info2'  and M.Date='$info1'  ";
		           break;
		   case 6:
		          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$info1' AND YEAR(M.Date)=YEAR(CURDATE()) ";
		           break;
		    case 7:
		          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$info1'   ";
		           break;
		}

		if ($Punc_FilterTerms!=""){
		     $Punc_FilterTerms.="  AND M.ShipType<>'credit' AND M.ShipType<>'debit' ";
			$NumsResult=$this->db->query("SELECT SUM(A.Qty) AS Nums,SUM(IF(A.Leadtime<scDate,A.Qty,0)) AS OverNums 
					             FROM (
									SELECT YEARWEEK(substring(PI.Leadtime,1,10),1) AS Leadtime, YEARWEEK(MAX(C.Date),1) AS scDate,S.Qty   
											FROM ch1_shipmain M
											LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
										    LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
										    LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id 
										    LEFT JOIN sc1_cjtj C ON C.POrderId=Y.POrderId 
											WHERE  $Punc_FilterTerms  GROUP BY S.Id 
									)A ");
			if($NumsResult->num_rows() > 0){
				$NumsRow = $NumsResult->row_array();
				$P_Nums=$NumsRow["Nums"];
				if ($P_Nums>0){
				  $P_OverNums=$NumsRow["OverNums"];
				  $Punc_Value=($P_Nums-$P_OverNums)/$P_Nums*100;
				  $Punc_Color=$Punc_Value<80?"#ff0000":$Punc_Color;
				  $Punc_Percent=round($Punc_Value);
 
				}
			}
		}

		return array('percent'=>$Punc_Percent,'color'=>$Punc_Color);
    }
    
    function shipped_months($CompanyId='') {
	    
	    $condition = '';
	   
	    if ($CompanyId != '') {
		    
		    $condition .= " AND M.CompanyId=$CompanyId "; 
	    }
	    $sql = "SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,SUM(IF(S.Type=2,0,S.Qty)) AS Qty,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS Amount,
        SUM(IF(M.cwSign=0,0,S.Price*S.Qty*M.Sign*D.Rate)) AS NoPayAmount ,'¥' AS PreChar ,D.Rate   
        FROM ch1_shipmain M
        LEFT JOIN ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN currencydata D ON D.Id = C.Currency 
        WHERE  M.Estate='0' $condition GROUP BY  DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month DESC;";
		$query=$this->db->query($sql);
		return  $query;
		
    }
    
    function shipped_companys() {
	    $sql = "SELECT M.CompanyId,SUM(IF(S.Type=2,0,S.Qty)) AS Qty,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS Amount,SUM(S.Price*S.Qty*D.Rate*M.Sign) AS SortAmount,
        SUM(IF(M.cwSign=0,0,S.Price*S.Qty*M.Sign)) AS NoPayAmount ,'¥' PreChar ,C.Logo,C.Forshort  
        FROM ch1_shipmain M
        LEFT JOIN ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN currencydata D ON D.Id = C.Currency 
        WHERE  M.Estate='0'  GROUP BY M.CompanyId ORDER BY SortAmount DESC;";
		$query=$this->db->query($sql);
		return  $query;
		
    }
    
    function invoice_list($Mid) {
	    $sql = "SELECT S.POrderId,O.OrderPO,S.Qty,S.Price,S.Type,P.cName,P.eCode,P.TestStandard,P.ProductId,M.Sign,N.OrderDate,M.Date AS chDate,M.CompanyId,PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1) AS Weeks,YEARWEEK(M.Date,1) AS chWeeks,S.Mid     
	FROM ch1_shipsheet S 
    LEFT JOIN ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN yw1_ordermain N ON N.OrderNumber=O.OrderNumber
	LEFT JOIN productdata P ON P.ProductId=O.ProductId 
	LEFT JOIN yw3_pisheet PI ON PI.oId=O.Id   
	WHERE  S.Mid='$Mid'   AND S.Type='1'
UNION ALL
	SELECT S.POrderId,O.SampPO AS OrderPO,S.Qty,S.Price,S.Type,O.SampName AS cName,O.Description AS eCode,'0' AS TestStandard,0 AS ProductId,M.Sign,'0000-00-00' AS  OrderDate,M.Date AS chDate,M.CompanyId,'' AS Leadtime,'' AS Weeks,YEARWEEK(M.Date,1) AS chWeeks  ,S.Mid 
	FROM ch1_shipsheet S 
    LEFT JOIN ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$Mid' AND S.Type='2'
UNION ALL
	SELECT S.POrderId,'' AS OrderPO,S.Qty,S.Price,S.Type,O.Description AS cName,O.Description AS eCode,'0' AS TestStandard,0 AS ProductId,M.Sign,'0000-00-00' AS  OrderDate,M.Date AS chDate,M.CompanyId,'' AS Leadtime,'' AS Weeks,YEARWEEK(M.Date,1) AS chWeeks     ,S.Mid 
	FROM ch1_shipsheet S 
    LEFT JOIN ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$Mid' AND S.Type='3'";
	
		$query=$this->db->query($sql);
		return  $query;
    }
    
    function month_company_list($Month,$CompanyId,$aInvoiceNO='') {
	    $condition = '';
	    if ($Month != '') {
		    
		    $condition .= " AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' "; 
	    }
	    if ($CompanyId != '') {
		    
		    $condition .= " AND M.CompanyId=$CompanyId "; 
	    }
	    if ($aInvoiceNO != '') {
		    
		    $condition .= " AND M.InvoiceNO='$aInvoiceNO' "; 
	    }
	    $sql = "SELECT M.Id,M.Date,M.InvoiceNO,M.InvoiceFile,M.CompanyId,M.Ship,M.ShipType,S.Type,SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*M.Sign)  AS Amount,T.Type       
		                FROM ch1_shipmain M
		                LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
		                LEFT JOIN ch1_shiptypedata T ON T.Mid=M.Id  
				        WHERE M.Estate='0' $condition 
				        GROUP BY M.InvoiceNO ORDER BY M.Date DESC";
		$query=$this->db->query($sql);
		return  $query;				
    }
    
    function get_payed($Month,$CompanyId='',$Mid='') {
	    
		//全部已收款
	      
	    $condition = '';
	    
	    if ($Month != '') {
		    
		    $condition .= " AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' "; 
	    }
	    if ($CompanyId != '') {
		    
		    $condition .= " AND M.CompanyId=$CompanyId "; 
	    }
	    if ($Mid != '') {
		    
		    $condition .= " AND M.Id=$Mid "; 
	    }
	    $sql = "SELECT SUM(IFNULL(S.Amount*M.Sign,0)) AS Amount 
			         FROM ch1_shipmain M
					 LEFT JOIN  cw6_orderinsheet S ON S.chId=M.Id 
					WHERE  M.Estate='0'     $condition ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->Amount;
		}
		return  0;
    }
    
    function month_payed($Month,$CompanyId='') {
	      //部分已收款
	      
	    $condition = '';
	    
	    if ($Month != '') {
		    
		    $condition .= " AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' "; 
	    }
	    if ($CompanyId != '') {
		    
		    $condition .= " AND M.CompanyId=$CompanyId "; 
	    }
	    $sql = "SELECT SUM(IFNULL(S.Amount*M.Sign,0)) AS Amount 
	         FROM ch1_shipmain M
			 LEFT JOIN  cw6_orderinsheet S ON S.chId=M.Id 
			 LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId 
			WHERE  M.Estate='0' AND M.cwSign IN (1,2)  $condition ";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->Amount;
		}
		return  0;

    }
    
    function month_amount($Month,$CompanyId='') {
	     $condition = '';
	    
	    if ($Month != '') {
		    
		    $condition .= " AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' "; 
	    }
	    if ($CompanyId != '') {
		    
		    $condition .= " AND M.CompanyId=$CompanyId "; 
	    }

	    $sql = "SELECT SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount    
        FROM ch1_shipmain M
        LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN currencydata D ON D.Id = C.Currency 
        WHERE M.Estate='0'  $condition ";
        $query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->Amount;
		}
		return  0;
        
    }
    
    function month_subcompanys($Month) {
	    
	    $sql = "SELECT M.CompanyId,C.Forshort,C.Estate,C.Logo,D.Rate,'¥' PreChar,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS SortAmount,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS Amount,SUM(S.Qty) AS Qty     
        FROM ch1_shipmain M
        LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN currencydata D ON D.Id = C.Currency 
        WHERE 1 and M.Estate='0' and DATE_FORMAT(M.Date,'%Y-%m')='$Month' 
        GROUP BY M.CompanyId ORDER BY SortAmount DESC;";
	    $query=$this->db->query($sql);
		return  $query;
    }
    
    
}