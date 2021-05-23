<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Cg1stocksheetModel extends MC_Model {
	
	
	function stuff_unrecieve_weekqtys($StuffId) {
		
		
		$sql = "SELECT  YEARWEEK(S.DeliveryDate,1) AS Weeks, 
       sum(A.Qty-A.rkQty) as Qty
	     FROM (
					    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty 
					          FROM cg1_stocksheet S 
					          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
					          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
					         WHERE   S.rkSign>0 AND M.CompanyId NOT IN (getSysConfig(106))    and S.StuffId=$StuffId  GROUP BY S.StockId
					)A 
		LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId   
		WHERE A.Qty>A.rkQty group by YEARWEEK(S.DeliveryDate,1) order by Weeks;";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;	
	}
	
	 function getCgOperateDate($StockId,$Opcode)
 {
     $returnArray=null;

     $Result=$this->db->query("SELECT IFNULL(S.created,S.Date) AS Date,M.Name FROM cg1_stocksheet_log S 
           LEFT JOIN staffmain M ON M.Number=S.Operator 
           WHERE S.Opcode='$Opcode' AND S.StockId='$StockId' ORDER BY Date DESC LIMIT 1");
		 if($Result->num_rows() > 0){
			 $myRow = $Result->row_array();
		     $returnArray=array("date"=>$myRow["Date"],"oper"=>$myRow["Name"]);
		 }
	  return $returnArray;
 }
	
	function set_deliverydate($params) {
		
		$this->db->trans_start();
		$Id = element('edit_id', $params, '-1');
		$stockid = element('stockid', $params, '-1');
		$oldDate = element('olddate', $params, '');
		$oldWeek = element('oldweek', $params, '');
		$newDate = element('deliverydate', $params, '');
		$remark = element('remark', $params, '');
		$data=array(
           'StockId'=>$stockid, 
           'Remark'=>$remark,
           'Date'=>$this->Date,
           'Operator'=>$this->LoginNumber,
           'creator'=>$this->LoginNumber,
           'created'=>$this->DateTime,
           'DeliveryDate'=>$oldDate,
           'DeliveryWeek'=>$oldWeek
        );
        $this->db->insert('cg1_deliverydate', $data); 
	
		$this->db->query("update cg1_stocksheet set DeliveryDate='$newDate' where Id=$Id");
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		} else {
			
			return 1;
		}
		
		
	}
	
	function get_week_selections($COUNTWEEK=20,$dateNow='', $maxWeek='') {
		
		$this->load->library('datehandler');
		
		$weekList = array();
		$dateNow = $dateNow==''?date("Y-m-d"):$dateNow;
		
		for ($i = 0; $i < $COUNTWEEK; $i++) {
		$hasS = $i==1 ? "":"s";
		$aDate=date("Y-m-d",strtotime("+$i week$hasS",strtotime($dateNow)));
		
		$query = $this->db->query("select yearweek('$aDate',1) as Weeks limit 1;");
		$aWeekRow = $query->row_array();
		$weekTitle = $aWeekRow["Weeks"];	
		
		$dateArray=$this->datehandler->getWeekToDate_getdate($weekTitle,"m/d");
		$dateSTR=$dateArray['title'];
		$iddate = $dateArray['id'];
		
		$weekString = substr($weekTitle,4,2);
		if ($maxWeek!='' && $weekTitle > $maxWeek) {
			break;
		}
		$weekList[]=array(
					   "headImage"=>"",
					   "WeekTitle"    =>"$dateSTR",
					   "w_title"    =>"$dateSTR",
					   "Id"       =>"$iddate",
					   "Week"     =>"$weekString",
					   "week"     =>"$weekTitle",
					   "CellType" =>"4",
					   'weekColor'=>'#54bce5',
					   "infos"    =>"$weekString"."周"
					   ); 

}

return $weekList;
	}
	
	
	function resetstock($Id) {
		$Operator = $this->LoginNumber;
		$sql = "
			CALL proc_cg1_stocksheet_resetqty('$Id','',$Operator);
		";
		$this->db->query($sql);
		$aff = $this->db->affected_rows();
		return $aff > 0 ? 1 : 0;
	}
	
	function check_week_changed($stockid) {
		$sql = "
			SELECT D.Id FROM cg1_deliverydate D 
			LEFT JOIN cg1_stocksheet S ON S.StockId=D.StockId 
			WHERE  D.StockId='$stockid' and D.DeliveryWeek!=S.DeliveryWeek LIMIT 1;

		";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return true;	
		}
		return false;
	}
	
	
	
	function save_cg_remark($params) {
		
		$remark = trim( element('remark',$params,''));
		
	    if ($remark!=''){
	        $data=array(
	           'StockId'=>element('StockId',$params,''), 
               'Remark'=>$remark,
               'Date'=>$this->Date,
               'Operator'=>$this->LoginNumber,
               'creator'=>$this->LoginNumber,
               'created'=>$this->DateTime
	       );
	       $this->db->insert('cg_remark', $data); 
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }

	}
	
	function cg_ordered_sublist($BuyerId='', $CheckMonth='', $CompanyId='') {
		// $buyerId, $weeks, $companyId
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }
	    if ($CheckMonth!='') {
		    $condition .= " and DATE_FORMAT(M.Date,'%Y-%m')='$CheckMonth' ";
	    }
	    if ($CompanyId!='') {
		    $condition .= " and S.CompanyId=$CompanyId ";
		    
	    }
	    
	    $sql ="SELECT A.StockId,S.StuffId,S.POrderId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       A.Qty,A.rkQty,S.Price,D.StuffCname,D.DevelopState,D.Picture,C.Rate,C.PreChar,R.Mid,YEARWEEK(A.rkDate,1) AS rkWeeks   
	     FROM (
					    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,MAX(RM.rkDate) AS rkDate  
					          FROM cg1_stocksheet S 
					          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
					          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
					          LEFT JOIN ck1_rkmain RM  ON RM.Id=S.Mid 
					          LEFT JOIN stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2 
					         WHERE   1 $condition   GROUP BY S.StockId
					)A 
		LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId   
		LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
		LEFT JOIN stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN staffmain N ON N.Number=M.BuyerId 
		LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
		LEFT JOIN currencydata C ON C.Id=P.Currency  
		LEFT JOIN cg1_stockreview R ON  R.Mid=S.Mid 
		WHERE 1 $condition  ORDER BY M.CompanyId,S.DeliveryDate";
		$query=$this->db->query($sql);
		//echo($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;	
		
		
	}
	function cg_ordered_companys($BuyerId='',$CheckMonth='') {
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }
	    if ($CheckMonth!='') {
		    $condition .= " and DATE_FORMAT(M.Date,'%Y-%m')='$CheckMonth' ";
	    }
		$sql = "SELECT 
			           A.CompanyId ,P.Forshort,P.Logo,
			           COUNT(*) AS Count,
			            SUM(A.Qty) AS Qty,
		               SUM((A.Qty)*S.Price*D.Rate) AS Amount,
			           SUM(if((A.Qty-A.rkQty)>0,1,0)) AS noCount,
		               SUM(A.Qty-A.rkQty) AS noQty,
		               SUM(if(A.Qty>S.OrderQty,1,0)) AS abQty,
		               SUM((A.Qty-A.rkQty)*S.Price*D.Rate) AS noAmount
					     FROM (
									    SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM cg1_stocksheet S 
									          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									         WHERE S.Mid>0 AND M.CompanyId NOT IN (getSysConfig(106)) $condition GROUP BY S.StockId 
									  
									)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId
							LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN trade_object P ON P.CompanyId=A.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
						WHERE 1 $condition  GROUP BY  A.CompanyId ORDER BY Amount DESC";
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;	
		
	}
	
	function cg_ordered_months($BuyerId='') {
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }
		$sql = "
SELECT 
			           DATE_FORMAT(M.Date,'%Y-%m') Month,
			           COUNT(*) AS Count,
			            SUM(A.Qty) AS Qty,
		               SUM((A.Qty)*S.Price*D.Rate) AS Amount,
			           SUM(if((A.Qty-A.rkQty)>0,1,0)) AS noCount,
		               SUM(A.Qty-A.rkQty) AS noQty,
		               SUM(if(A.Qty>S.OrderQty,1,0)) AS abQty,
		               SUM((A.Qty-A.rkQty)*S.Price*D.Rate) AS noAmount
					     FROM (
									    SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM cg1_stocksheet S 
									          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									         WHERE S.Mid>0 AND M.CompanyId NOT IN (getSysConfig(106))  GROUP BY S.StockId 
									  
									)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId
							LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN trade_object P ON P.CompanyId=A.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
						WHERE 1 $condition  GROUP BY  Month ORDER BY Month DESC;";
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
						
	}
	
	function cg_unpay_sublist($BuyerId='', $CheckMonth='', $CompanyId='') {
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and G.BuyerId=$BuyerId ";
	    }
	    if ($CheckMonth!='') {
		    $condition .= "  AND S.Month='$CheckMonth'   ";
	    }
		if ($CompanyId != '') {
			$condition .= "  AND S.CompanyId='$CompanyId'   ";
		}
		
		$sql = "SELECT S.StockId,S.Month,S.StuffId,(S.FactualQty+S.AddQty) AS Qty,S.Price,YEARWEEK(G.DeliveryDate,1) AS Weeks,G.POrderId,
M.PurchaseID,P.Forshort,D.StuffCname,D.Picture,C.Rate,C.PreChar ,D.DevelopState 
	     FROM  cw1_fkoutsheet S  
		LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId   
		LEFT JOIN cg1_stockmain M ON M.Id=G.Mid  
		LEFT JOIN stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN trade_object P ON P.CompanyId=S.CompanyId  
		LEFT JOIN currencydata C ON C.Id=P.Currency  
		WHERE S.Estate=3 $condition  ORDER BY S.Id";
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
	}
	
	function cg_unpay_companys($BuyerId='', $CheckMonth='') {
		$LastMonth1=date("Y-m",strtotime("-1 month"));
		$LastMonth2=date("Y-m",strtotime("-2 month"));

		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and G.BuyerId=$BuyerId ";
	    }
	    if ($CheckMonth!='') {
		    $condition .= "  AND S.Month='$CheckMonth'   ";
	    }
		
		$sql = "SELECT S.CompanyId,P.Forshort,P.GysPayMode,P.Logo,P.Prepayment,SUM(S.Amount*C.Rate) AS Amount,
		                    SUM((CASE WHEN P.GysPayMode=0 AND S.Month<'$LastMonth1' THEN S.Amount
                                     WHEN P.GysPayMode=1 THEN S.Amount 
                                     WHEN P.GysPayMode=2 AND S.Month<'$LastMonth2' THEN S.Amount
                                     
                                     ELSE 0 END)*C.Rate) AS OverAmount 
							FROM cw1_fkoutsheet S
							LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
							LEFT JOIN trade_object P ON P.CompanyId=S.CompanyId
							LEFT JOIN currencydata C ON C.Id=P.Currency
							WHERE S.Estate=3 AND S.Amount>0  $condition 
							GROUP BY S.CompanyId ORDER BY Amount DESC";
							
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
		
	}
	function cg_unpay_months($BuyerId='') {
		
		
		$LastMonth1=date("Y-m",strtotime("-1 month"));
		$LastMonth2=date("Y-m",strtotime("-2 month"));


		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and G.BuyerId=$BuyerId ";
	    }
		
		$sql = "SELECT S.Month,P.Forshort,P.GysPayMode,P.Prepayment,SUM(S.Amount*C.Rate) AS Amount,
            SUM((
            CASE WHEN P.GysPayMode=0 AND S.Month<'$LastMonth1' THEN S.Amount
                 WHEN P.GysPayMode=1 THEN S.Amount 
                 WHEN P.GysPayMode=2 AND S.Month<'$LastMonth2' THEN S.Amount
            ELSE 0 
            END)*C.Rate) AS OverAmount 
			FROM cw1_fkoutsheet S
			LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
			LEFT JOIN trade_object P ON P.CompanyId=S.CompanyId
			LEFT JOIN currencydata C ON C.Id=P.Currency
			WHERE S.Estate=3 AND S.Amount>0  
			GROUP BY S.Month ORDER BY S.Month DESC;";
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
	}
	
	function cg_send_sublist($CheckMonth='', $BuyerId='', $CompanyId='') {
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and G.BuyerId=$BuyerId ";
	    }
	    if ($CheckMonth!='') {
		    $condition .= " and DATE_FORMAT(M.rkDate,'%Y-%m')='$CheckMonth' ";
	    }
	    if ($CompanyId!='') {
		    $condition .= " and M.CompanyId='$CompanyId' ";
	    }
	    
		$sql = "SELECT S.StockId,G.StuffId,S.Qty,(G.FactualQty+G.AddQty) AS cgQty,G.Price,YEARWEEK(G.DeliveryDate,1) AS Weeks,G.POrderId,
YEARWEEK(M.rkDate,1) AS  rkWeeks,
DATE_FORMAT(M.rkDate,'%m-%d') rkDateTitle,
GM.PurchaseID,P.Forshort,D.StuffCname,D.Picture,D.DevelopState,C.Rate,C.PreChar  
	    FROM ck1_rksheet S
	    LEFT JOIN ck1_rkmain M  ON M.Id=S.Mid 
		inner JOIN cg1_stocksheet G ON G.StockId=S.StockId   
		LEFT JOIN cg1_stockmain GM ON GM.Id=G.Mid  
		LEFT JOIN stuffdata D ON D.StuffId=G.StuffId  
		LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
		LEFT JOIN currencydata C ON C.Id=P.Currency  
		WHERE 1 $condition  ORDER BY S.Id DESC";
		
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
	}
	
	function cg_send_companys($CheckMonth='', $BuyerId='') {
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and G.BuyerId=$BuyerId ";
	    }
	    if ($CheckMonth!='') {
		    $condition .= " and DATE_FORMAT(M.rkDate,'%Y-%m')='$CheckMonth' ";
	    }

		$sql = "SELECT SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty,SUM(S.Qty*G.Price*D.Rate) AS Amount,C.Forshort,M.CompanyId,C.Logo 
	FROM ck1_rksheet S
	LEFT JOIN ck1_rkmain M  ON M.Id=S.Mid 
	inner JOIN cg1_stocksheet G  ON G.StockId=S.StockId  
	LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency
	WHERE  1 $condition GROUP BY  M.CompanyId ORDER BY Amount DESC";
	
	
		//echo($sql);
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
		
	}
	
	function cg_send_months($BuyerId='',$afterMonth='') {
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and G.BuyerId=$BuyerId ";
	    }
	    if ($afterMonth!='') {
		    $condition .= " and DATE_FORMAT(M.rkDate,'%Y-%m')>='$afterMonth' ";
	    }

		
		$sql = "SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*G.Price*D.Rate) AS Amount ,
DATE_FORMAT(M.rkDate,'%Y-%m') Month   
	FROM ck1_rksheet S
	LEFT JOIN ck1_rkmain M  ON M.Id=S.Mid 
	inner JOIN cg1_stocksheet G  ON G.StockId=S.StockId  
	LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN currencydata D ON D.Id=C.Currency
	WHERE 1 $condition  GROUP BY  Month ORDER BY Month DESC";
		$query=$this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
		
	}
	
	function cg_remarkinfo($StockId) {
		$sql = "
			SELECT R.Remark,R.Date,M.Name FROM cg_remark  R 
				              LEFT JOIN staffmain M ON M.Number=R.Operator  
				              WHERE R.StockId='$StockId' ORDER BY R.Id DESC LIMIT 1
		";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row_array();	
		}
	    return null;
	}
	

	
	function cg_unrecieved_list($BuyerId, $Weeks='', $CompanyId = '',$rowId='') {
		
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }
	    
	    if ($Weeks != '') {
		    
		    
		    if ($Weeks == 'notsure') {
			    $condition .= " and YEARWEEK(S.DeliveryDate,1) is null  ";
		    } else {
			    $condition .= " and YEARWEEK(S.DeliveryDate,1)='$Weeks'  ";
		    }
		    
	    }
	    if ($CompanyId != '') {
		    $condition .= " and S.CompanyId=$CompanyId  ";
	    }
	    
	    if ($rowId!='') {
		    $condition = " and S.Id=$rowId  ";
	    }
		
		$sql ="
		
SELECT A.StockId,S.StuffId,S.Id,S.POrderId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
if(CK.noBled is not null and CK.noBled=1,1,0) LastNoBL,
       A.Qty,A.rkQty,S.Price,D.StuffCname,D.Picture,D.DevelopState,C.Rate,C.PreChar,R.Mid  
	     FROM (
					    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty 
					          FROM cg1_stocksheet S 
					          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
					          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
					         WHERE   S.rkSign>0 AND M.CompanyId NOT IN (getSysConfig(106))    $condition GROUP BY S.StockId
					)A 
		LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId   
		
		LEFT JOIN (
								SELECT * FROM (
								SELECT group_concat( D.StuffId) lastStuff,S.POrderId,count(*) as noBled
										   FROM 
										   
										   (select S.POrderId from cg1_stocksheet S where  S.Mid>0 AND  S.rkSign>0  AND S.CompanyId NOT IN (getSysConfig(106))   $condition  group by S.POrderId  ) XX 
										   
										   INNER JOIN yw1_ordersheet Y ON Y.POrderId=XX.POrderId and Y.Estate>=1
										   LEFT JOIN cg1_stocksheet S  ON S.POrderId=Y.POrderId
										   LEFT JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
										   LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
										   LEFT JOIN stufftype T ON T.TypeId = D.TypeId
										   LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType 
										
										   WHERE 1  
										   AND TM.blSign=1 
										   AND K.tStockQty < S.OrderQty 
GROUP BY S.POrderId ) X WHERE noBled=1 
							)	CK ON CK.POrderId =S.POrderId AND CK.lastStuff=S.StuffId 
		
		LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
		LEFT JOIN stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN staffmain N ON N.Number=M.BuyerId 
		LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
		LEFT JOIN currencydata C ON C.Id=P.Currency  
		LEFT JOIN cg1_stockreview R ON  R.Mid=S.Mid 
		WHERE A.Qty>A.rkQty  ORDER BY M.CompanyId,S.DeliveryDate;";
		

		
		$query=$this->db->query($sql);
		
		//echo($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
		
	}
	
	
	function cg_unrecieved_companys($BuyerId) {
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }
		
		$sql = "
			SELECT A.CompanyId,P.Forshort,P.Logo,COUNT(*) AS Count,
			sum(if(CK.noBled=1,1,0)) LastCount,
			sum(if(CK.noBled=1,A.Qty-A.rkQty,0)) LastQty,
			sum(if(CK.noBled=1,(A.Qty-A.rkQty)*S.Price*D.Rate,0)) LastAmount,
		               SUM(A.Qty-A.rkQty) AS Qty,SUM((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount,
		               SUM(IF(YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCount,
		               SUM(IF(YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),A.Qty-A.rkQty,0)) AS OverQty,
					   SUM(IF(YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),(A.Qty-A.rkQty)*S.Price*D.Rate,0)) AS OverAmount 
					     FROM (
									    SELECT M.CompanyId,S.StockId,S.POrderId,S.StuffId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM cg1_stocksheet S 
									          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.rkSign>0 AND S.Mid>0 AND M.CompanyId NOT IN (getSysConfig(106)) $condition GROUP BY S.StockId 
									     UNION ALL 
									        SELECT M.CompanyId,S.StockId,S.POrderId,S.StuffId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM cg1_stocksheet S 
									          LEFT JOIN bps M ON M.StuffId=S.StuffId 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									           LEFT JOIN stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2 
									         WHERE  S.rkSign>0 AND S.Mid=0 AND OP.Property=2  $condition GROUP BY S.StockId       
									)A 
									
									
								
									
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId
						LEFT JOIN trade_object P ON P.CompanyId=A.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency 
						
						
						LEFT JOIN (
								SELECT * FROM (
								SELECT group_concat( D.StuffId) lastStuff,S.POrderId,count(*) as noBled
										   FROM 
										   
										   (select S.POrderId from cg1_stocksheet S where  S.Mid>0 AND  S.rkSign>0  AND S.CompanyId NOT IN (getSysConfig(106))   $condition  group by S.POrderId  ) XX 
										   
										   INNER JOIN yw1_ordersheet Y ON Y.POrderId=XX.POrderId and Y.Estate>=1
										   LEFT JOIN cg1_stocksheet S  ON S.POrderId=Y.POrderId
										   LEFT JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
										   LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
										   LEFT JOIN stufftype T ON T.TypeId = D.TypeId
										   LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType 
										
										   WHERE 1  
										   AND TM.blSign=1 
										   AND K.tStockQty < S.OrderQty 
GROUP BY S.POrderId ) X WHERE noBled=1 
							)	CK  ON CK.POrderId =A.POrderId AND CK.lastStuff=A.StuffId 

						WHERE A.Qty>A.rkQty GROUP BY  A.CompanyId ORDER BY Amount DESC
		";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
	    
	}
	
	function cg_unrecieved_weeks($BuyerId, $Weeks='', $CompanyId = '') {
		
		$condition = '';
		
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }
	    
	    if ($CompanyId != '') {
		    $condition .= " and S.CompanyId=$CompanyId  ";
	    }
	    
	    if ($Weeks != '') {
		    
		    
		    if ($Weeks == 'notsure') {
			    $condition .= " and YEARWEEK(S.DeliveryDate,1) is null  ";
		    } else {
			    $condition .= " and YEARWEEK(S.DeliveryDate,1)='$Weeks'  ";
		    }
		    
	    }
	    
	    
	    
	    if ($Weeks == '') {
		    $sql = "
			
SELECT count(*) AS Counts,YEARWEEK(S.DeliveryDate,1) AS Weeks,

sum(if(CK.noBled=1,1,0)) LastCount,
			sum(if(CK.noBled=1,A.Qty-A.rkQty,0)) LastQty,
			sum(if(CK.noBled=1,(A.Qty-A.rkQty)*S.Price*D.Rate,0)) LastAmount,

		               sum(A.Qty-A.rkQty) AS Qty,sum((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount 
					     FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
									          FROM cg1_stocksheet S 
									          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0  AND M.CompanyId NOT IN (getSysConfig(106))  $condition  GROUP BY S.StockId
									)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId  
						
						
						
						LEFT JOIN (
								SELECT * FROM (
								SELECT group_concat( D.StuffId) lastStuff,S.POrderId,count(*) as noBled
										   FROM 
										   
										   (select S.POrderId from cg1_stocksheet S where  S.Mid>0 AND  S.rkSign>0  AND S.CompanyId NOT IN (getSysConfig(106))   $condition   group by S.POrderId ) XX 
										   
										   INNER JOIN yw1_ordersheet Y ON Y.POrderId=XX.POrderId and Y.Estate>=1
										   LEFT JOIN cg1_stocksheet S  ON S.POrderId=Y.POrderId
										   LEFT JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
										   LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
										   LEFT JOIN stufftype T ON T.TypeId = D.TypeId
										   LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType 
										
										   WHERE 1  
										   AND TM.blSign=1 
										   AND K.tStockQty < S.OrderQty 
GROUP BY S.POrderId ) X WHERE noBled=1 
							)	CK  ON CK.POrderId =S.POrderId AND CK.lastStuff=S.StuffId 

						 
						LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN staffmain N ON N.Number=M.BuyerId 
						LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
					    LEFT JOIN cg1_stockreview R ON  R.Mid=S.Mid 
						WHERE A.Qty>A.rkQty group by YEARWEEK(S.DeliveryDate,1) ORDER BY Weeks;
		";

	    } else {
		    $sql = "SELECT count(*) AS Counts,P.Forshort,P.Logo,M.CompanyId ,
		    sum(if(CK.noBled=1,1,0)) LastCount,
			sum(if(CK.noBled=1,A.Qty-A.rkQty,0)) LastQty,
			sum(if(CK.noBled=1,(A.Qty-A.rkQty)*S.Price*D.Rate,0)) LastAmount,

		               sum(A.Qty-A.rkQty) AS Qty,sum((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount  
					     FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
									          FROM cg1_stocksheet S 
									          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0  AND M.CompanyId NOT IN (getSysConfig(106)) $condition GROUP BY S.StockId
									)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId  
						
						LEFT JOIN (
								SELECT * FROM (
								SELECT group_concat( D.StuffId) lastStuff,S.POrderId,count(*) as noBled
										   FROM 
										   
										   (select S.POrderId from cg1_stocksheet S where  S.Mid>0 AND  S.rkSign>0  AND S.CompanyId NOT IN (getSysConfig(106))   $condition  group by S.POrderId  ) XX 
										   
										   INNER JOIN yw1_ordersheet Y ON Y.POrderId=XX.POrderId and Y.Estate>=1
										   LEFT JOIN cg1_stocksheet S  ON S.POrderId=Y.POrderId
										   LEFT JOIN ck9_stocksheet K ON K.StuffId = S.StuffId
										   LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
										   LEFT JOIN stufftype T ON T.TypeId = D.TypeId
										   LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType 
										
										   WHERE 1  
										   AND TM.blSign=1 
										   AND K.tStockQty < S.OrderQty 
GROUP BY S.POrderId ) X WHERE noBled=1 
							)	CK ON CK.POrderId =S.POrderId AND CK.lastStuff=S.StuffId 
									 
						LEFT JOIN cg1_stockmain M ON M.Id=S.Mid   
						LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
						WHERE A.Qty>A.rkQty   group by M.CompanyId ORDER BY Amount DESC;
;";
	    }
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
	}
	
	function cg_months_group($StuffId) {
		$sql = "
		SELECT 
                           SUM(CG.FactualQty + CG.AddQty) AS Qty, DATE_FORMAT(M.Date,'%Y-%m') as month
                        FROM cg1_stocksheet CG
						 
						LEFT JOIN cg1_stockmain M ON CG.Mid=M.Id 

						where CG.StuffId=$StuffId  and CG.Mid>0 group by  DATE_FORMAT(M.Date,'%Y-%m') order by month desc;
;";

		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		}
	    return null;
	}
	
	
	
	
	function get_state_last($StockId, $afterDate='') {
		
		$sql = "
			
select '0' as keyval, 'cg_doorder' as img, ifnull(Y.created,M.OrderDate) as date,
concat('客人:',P.Forshort,'/PO:',M.OrderPO,'/数量:',format(Y.Qty,0)) as title,
N.Name as operator,'' as otherid
	from yw1_ordersheet Y 
	left join cg1_stocksheet S on Y.POrderId=S.POrderId
	left join yw1_ordermain M on M.OrderNumber=Y.OrderNumber
	left join trade_object P on P.CompanyId=M.CompanyId
	left join staffmain N on N.Number=M.Operator
where S.StockId='$StockId' 

union all 
select '0.5' as keyval, 'cg_develop' as img, DP.DesignDate as date,
'开发设计完成' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffdevelop DP on DP.StuffId=D.StuffId
	left join staffmain N on N.Number=DP.Number
where S.StockId='$StockId' and D.DevelopState=1 and DP.DesignDate is not null and   DP.DesignDate>S.ywOrderDtime  

union all 
select '1' as keyval, '' as img, if(DP.Estate!=0 and DP.created<S.ywOrderDtime,S.ywOrderDTime,DP.created) as date,
'需开发' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffdevelop DP on DP.StuffId=D.StuffId
	left join staffmain N on N.Number=DP.Number
where S.StockId='$StockId' and D.DevelopState=1 and not (DP.created<S.ywOrderDtime and DP.Estate=0)
union all
select '2' as keyval, 'cg_develop' as img, DP.Finishdate  as date,
'已完成' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffdevelop DP on DP.StuffId=D.StuffId
	left join staffmain N on N.Number=DP.Number
where S.StockId='$StockId' and D.DevelopState=1 and DP.Estate=0 and DP.Finishdate>S.ywOrderDtime

union all
select '3' as keyval, 'cg_cgorder' as img, S.ywOrderDtime as date,
'下采单' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S  
	left join staffmain N on N.Number=S.BuyerId
where S.StockId='$StockId'   and S.Mid>0

union all
select '4' as keyval, 'cg_ordered' as img, GS.created as date,
concat('开单',format(GS.Qty,U.Decimals),U.Name) as title,
ifnull(N.Name,'系统') as operator,'' as otherid
	from gys_shsheet GS  
	left join gys_shmain SM on SM.Id=GS.Mid
	left join cg1_stocksheet S on GS.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffunit U ON U.Id=D.Unit 
	left join staffmain N on N.Number=SM.Operator
where S.StockId='$StockId' 


union all
select '5' as keyval, 'cg_bad_rec' as img, B.created as date,
concat('不良',format(B.Qty,U.Decimals),U.Name) as title,
ifnull(N.Name,'未设定') as operator,B.Id as otherid
	from qc_badrecord B 
	left join cg1_stocksheet S on B.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffunit U ON U.Id=D.Unit 
	left join staffmain N on N.Number=B.Operator
where S.StockId='$StockId'  

union all
select '6' as keyval, 'cg_rked' as img, R.created as date,
concat('入库',format(R.Qty,U.Decimals),U.Name) as title,
ifnull(N.Name,'未设定') as operator,R.LocationId as otherid
	from ck1_rksheet R 
	left join cg1_stocksheet S on R.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffunit U ON U.Id=D.Unit 
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId' 

union all
select '7' as keyval, '' as img, R.Lockdate as date,
'采购配件锁定' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_lockstock R 
	left join cg1_stocksheet S on R.StockId=S.StockId 
	 
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId'  

union all
select '8' as keyval, 'cg_unlock' as img, R.Date as date,
'配件解锁' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_lockstock R
	left join cg1_stocksheet S  on R.StockId=S.StockId 
	 
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId' and R.Locks=1


union all
select '9' as keyval, '' as img, R.created as date,
'订单锁定' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join yw2_orderexpress R on R.POrderId=S.POrderId and R.Type=2
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId' and R.Id is not null 

union all
select '10' as keyval, 'cg_unlock' as img, R.created as date,
'解锁' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join yw2_orderexpress_log R on R.POrderId=S.POrderId and R.Type=2
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId'  and R.Id is not null


union all 
select '11' as keyval, 'cg_weekalter' as img, R.created as date,
R.DeliveryWeek as title,
ifnull(N.Name,'未设定') as operator,S.DeliveryWeek as otherid
	from cg1_deliverydate R 
	left join cg1_stocksheet S on R.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId 
	left join staffmain N on N.Number=R.Operator
where R.StockId='$StockId' and R.DeliveryWeek!=S.DeliveryWeek 


union all
select '12' as keyval, 'cg_weekalter' as img, R.Date as date,
if(S.AddRemark is null or S.AddRemark='', '因订单数量减少产生的增购。',S.AddRemark) as title,
ifnull(N.Name,NS.Name) as operator,R.Id as otherid
	from cg1_stocksheet S 
	left join cg1_stocksheet_log R on R.StockId=S.StockId and R.opcode=3
	left join stuffdata D on D.StuffId=S.StuffId 
	left join staffmain N on N.Number=R.Operator
left join staffmain NS on NS.Number=S.BuyerId
where S.StockId='$StockId' and S.OrderQty<(S.FactualQty+S.AddQty)



order by  date desc, keyval desc limit 1;

		";
		
		
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->row_array();
	    }
	    
	    return null;
	}

	
	
	
	function get_porder_weekremark($POrderId) {
		
		
		$sql = "
		select Remark, (yearweek(OldLeadtime,1)+OldReduceWeeks) as OldWeek,(yearweek(UpdateLeadtime,1)+ReduceWeeks) as NewWeek from yw3_pileadtimechange where POrderId='$POrderId';

";

// echo($sql);
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->result_array();
	    }
	    return null;
	}
	
	
	
	
		
	function get_stock_process_all($StockId) {
		
		$sql = "
			
select '0' as keyval, 'cg_doorder' as img, ifnull(Y.created,M.OrderDate) as date,
concat('客人:',P.Forshort,'/PO:',M.OrderPO,'/数量:',format(Y.Qty,0)) as title,
N.Name as operator,'' as otherid
	from yw1_ordersheet Y 
	left join cg1_stocksheet S on Y.POrderId=S.POrderId
	left join yw1_ordermain M on M.OrderNumber=Y.OrderNumber
	left join trade_object P on P.CompanyId=M.CompanyId
	left join staffmain N on N.Number=M.Operator
where S.StockId='$StockId' 

union all 
select '0.5' as keyval, 'cg_develop' as img, DP.DesignDate as date,
'开发设计完成' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffdevelop DP on DP.StuffId=D.StuffId
	left join staffmain N on N.Number=DP.Number
where S.StockId='$StockId' and D.DevelopState=1 and DP.DesignDate is not null and   DP.DesignDate>S.ywOrderDtime  

union all 
select '1' as keyval, '' as img, if(DP.Estate!=0 and DP.created<S.ywOrderDtime,S.ywOrderDTime,DP.created) as date,
'需开发' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffdevelop DP on DP.StuffId=D.StuffId
	left join staffmain N on N.Number=DP.Number
where S.StockId='$StockId' and D.DevelopState=1 and not (DP.created<S.ywOrderDtime and DP.Estate=0)
union all
select '2' as keyval, 'cg_develop' as img, DP.Finishdate  as date,
'已完成' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffdevelop DP on DP.StuffId=D.StuffId
	left join staffmain N on N.Number=DP.Number
where S.StockId='$StockId' and D.DevelopState=1 and DP.Estate=0 and DP.Finishdate>S.ywOrderDtime

union all
select '3' as keyval, 'cg_cgorder' as img, S.ywOrderDtime as date,
'下采单' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S  
	left join staffmain N on N.Number=S.BuyerId
where S.StockId='$StockId'   and S.Mid>0

union all
select '4' as keyval, 'cg_ordered' as img, GS.created as date,
concat('开单',format(GS.Qty,U.Decimals),U.Name) as title,
ifnull(N.Name,'系统') as operator,'' as otherid
	from gys_shsheet GS  
	left join gys_shmain SM on SM.Id=GS.Mid
	left join cg1_stocksheet S on GS.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffunit U ON U.Id=D.Unit 
	left join staffmain N on N.Number=SM.Operator
where S.StockId='$StockId' 


union all
select '5' as keyval, 'cg_bad_rec' as img, B.created as date,
concat('不良',format(B.Qty,U.Decimals),U.Name) as title,
ifnull(N.Name,'未设定') as operator,B.Id as otherid
	from qc_badrecord B 
	left join cg1_stocksheet S on B.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffunit U ON U.Id=D.Unit 
	left join staffmain N on N.Number=B.Operator
where S.StockId='$StockId'  

union all
select '6' as keyval, 'cg_rked' as img, R.created as date,
concat('入库',format(R.Qty,U.Decimals),U.Name) as title,
ifnull(N.Name,'未设定') as operator,R.LocationId as otherid
	from ck1_rksheet R 
	left join cg1_stocksheet S on R.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId
	left join stuffunit U ON U.Id=D.Unit 
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId' 

union all
select '7' as keyval, '' as img, R.Lockdate as date,
'采购配件锁定' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_lockstock R 
	left join cg1_stocksheet S on R.StockId=S.StockId 
	 
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId'  

union all
select '8' as keyval, 'cg_unlock' as img, R.Date as date,
'配件解锁' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_lockstock R
	left join cg1_stocksheet S  on R.StockId=S.StockId 
	 
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId' and R.Locks=1


union all
select '9' as keyval, '' as img, R.created as date,
'订单锁定' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join yw2_orderexpress R on R.POrderId=S.POrderId and R.Type=2
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId' and R.Id is not null 

union all
select '10' as keyval, 'cg_unlock' as img, R.created as date,
'解锁' as title,
ifnull(N.Name,'未设定') as operator,'' as otherid
	from cg1_stocksheet S 
	left join yw2_orderexpress_log R on R.POrderId=S.POrderId and R.Type=2
	left join staffmain N on N.Number=R.Operator
where S.StockId='$StockId'  and R.Id is not null


union all 
select '11' as keyval, 'cg_weekalter' as img, R.created as date,
R.DeliveryWeek as title,
concat(ifnull(R.Remark,''),'|||', ifnull(N.Name,'未设定')) as operator,S.DeliveryWeek as otherid
	from cg1_deliverydate R 
	left join cg1_stocksheet S on R.StockId=S.StockId
	left join stuffdata D on D.StuffId=S.StuffId 
	left join staffmain N on N.Number=R.Operator
where R.StockId='$StockId' 


union all
select  * from (
select '0' as keyval, 'cg_special' as img, concat(date_format(ifnull(R.Date,S.ywOrderDtime),'%Y-%m-%d'),' 23:59:59') as date,
if(S.AddRemark is null or S.AddRemark='', '因订单数量减少产生的增购。',S.AddRemark) as title,
ifnull(N.Name,NS.Name) as operator,R.Id as otherid
	from cg1_stocksheet S 
	left join cg1_stocksheet_log R on R.StockId=S.StockId and R.opcode=3
	left join stuffdata D on D.StuffId=S.StuffId 
	left join staffmain N on N.Number=R.Operator
left join staffmain NS on NS.Number=S.BuyerId
where S.StockId='$StockId' and S.OrderQty<(S.FactualQty+S.AddQty) 
order by R.Id limit 1
) SP


order by  date desc, keyval desc ;

		";
		
		
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->result_array();
	    }
	    
	    return null;
	}
	
	
	function get_lock_remarkinfo($StockId, $StuffId, $POrderId, $DevelopState='',$nosemi=0) {
		
		//1 开发状态
		
		$Locks = 0;
		$RemarkOperator = $Remark = $RemarkDate = '';
		if ($DevelopState == '') {
			$this->load->model('stuffDataModel');
			$row = $this->stuffDataModel->get_records($StuffId);
			$DevelopState = $row['DevelopState'];
			
		}
		
		if ($DevelopState == 1) {
			$sql = "
				select M.Name,DP.created from stuffdevelop DP 
				left join staffmain M on DP.Number=M.Number
				where DP.StuffId=$StuffId and DP.Estate!=0
			";
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$RemarkOperator = $row->Name;
				$RemarkDate = $row->created;
				$Remark = '开发锁定，未提供数据';
				
				$Locks = 9;
			} 
		}
		if ($Locks<=0) {
			// 2 订单和配件锁
			$this->load->model('YwOrderSheetModel');
			$infos = $this->YwOrderSheetModel->check_lock($POrderId, $StockId);
			if ($infos['lock'] > 0) {
				return $infos;
			}
		}
		
		if ($Locks<=0) {
			// 3 下单需求锁
			$row = $this->stuffDataModel->get_forcepic_info($StuffId);
			
			$ForcePicSpe=$row["ForcePicSpe"];
			$ForcePicSign=$row["ForcePicSign"];
			$Picture=$row["Picture"];
			$Gstate=$row["Gstate"];
			$Gfile=$row["Gfile"];
			if ($ForcePicSpe!=-1){  //-1表示用stufftype用的，否则用它指定
				$ForcePicSign=$ForcePicSpe;  
			}
			
			// SELECT S.ForcePicSpe, T.ForcePicSign, S.Gstate,S.Gfile,S.Picture,S.created,M.Name
			$needLock = false;
			if ($ForcePicSign > 0 ) {
				
				switch($ForcePicSign){
					case 1: 
						$ForcePicSign="图片";
						if ($Picture != 1) {
							$needLock = true;
							$Remark = '需要'.$ForcePicSign.'?上传'.$ForcePicSign.'中';
							if ($Picture > 1) {
								$Remark = $Remark.'?正在审核';
							}
						}
							
					break;
					case 2: 
						$ForcePicSign="图档";
						if ($Gfile == '' || $Gstate != 1) {
							$needLock = true;
							$Remark = '需要'.$ForcePicSign.'?上传'.$ForcePicSign.'中';
							if ($Gfile!='') {
								$Remark = $Remark.'?正在审核';
							}
						}
							
						
					break;
					case 3:
						$ForcePicSign="图片/图档";
						if (($Picture != 1) || ($Gfile == '' || $Gstate != 1)) {
							$needLock = true;
							$Remark = '需要'.$ForcePicSign.'?上传'.$ForcePicSign.'中';
							if ($Gfile!='' || $Picture > 1) {
								$Remark = $Remark.'?正在审核';
							}
						}
							
						
					break;
							
				}
				
				
				if ($needLock == true) {
					$Locks = 8;
					// PicNumber,S.GicNumber 
					$PicNumber = $row['PicNumber'];
					$GicNumber = $row['GicNumber'];
					if ($PicNumber=='') {
						$PicNumber = $GicNumber;
					}
					$this->load->model('StaffMainModel');
					
					$RemarkOperator = $this->StaffMainModel->get_staffname($PicNumber);
					
					$RemarkDate = $row['created'];
				}		
			}
			
		}
		if ($Locks<=0 && $nosemi!=1) {
			$Locks = $this->get_stockid_lock($StockId);
			$Locks = $Locks>0?1:0;
			
			$Remark = '半成品锁定';
			$RemarkDate = null;
		}
		
		return array('lock'=>$Locks, 'oper'=>$RemarkOperator, 'remark'=>$Remark, 'date'=>$RemarkDate);
		
	}
	
	function get_waitcg_companys($BuyerId='') {
	    
	    
	     
						
		$condition = '';
		$lockadded = ' or getStockIdLock(S.StockId)=1 ';
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
		    if ($BuyerId == '10882') {
			    $lockadded = '';
		    }
	    }

		$sql = "select S.Forshort,S.Logo, S.CompanyId,sum(S.Amount) as Amount,sum(S.Qty) as Qty,sum(1) as Count,sum(S.locks) as LockCount,sum(if(S.Locks=1,S.Qty,0)) as LockQty,sum(if(S.Locks=1,S.Amount,0)) as LockAmount from (
SELECT S.CompanyId,(S.FactualQty+S.AddQty)*S.Price*E.Rate AS Amount,(S.FactualQty+S.AddQty) as Qty,
P.Forshort,P.Logo,
 (if(
 ( A.DevelopState=1 and DP.Estate!=0 ) 
or (H.Type=2 AND H.Type is NOT NULL )  

or 
(	 
	 
	((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1) 
	or ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
	or ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1))



   )
    
or  ((I.Locks=0 AND I.Locks is NOT NULL ) $lockadded ) -- getStockIdLock(S.StockId)=1 
   ,1,0) ) as Locks
			FROM cg1_stocksheet S 
LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN stuffdevelop DP on DP.StuffId=A.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.Mid=0   and S.CompanyId!=2270 AND S.CompanyId NOT IN (getSysConfig(106))  $condition  and  S.blSign=1 and CG.Id is null and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4 and S.CompanyId<>'2166'   
			 AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)    
			  
) S group by S.CompanyId Order by Amount Desc;";

		 $query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->result_array();
	    }
	    
	    return null;

		
		
	}
	
	
	
	function waitcg_list($BuyerId='', $CompanyId='',$EditId='',$checklocksign='') {
		
		$condition = '';
		
		$condition = '';
		$lockadded = ' or getStockIdLock(S.StockId)=1 ';
		if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
		    if ($BuyerId == '10882') {
			    $lockadded = '';
		    }
	    }
 
	    if ($CompanyId!='') {
		    $condition .= " and S.CompanyId=$CompanyId ";
	    }

		if ($EditId!='') {
		    $condition .= " and S.Id=$EditId ";
	    }
		
		$locklimit = '';
		if ($checklocksign!='') {
			
			if ($checklocksign==0) {
				//get unlock 
				
				$locklimit = " 
					and (if(
 ( A.DevelopState=1 and DP.Estate!=0 ) 
or (H.Type=2 AND H.Type is NOT NULL )  

or 
(	 
	 
	((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1) 
	or ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
	or ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1))



   )
    
or  ((I.Locks=0 AND I.Locks is NOT NULL )  ) -- getStockIdLock(S.StockId)=1 
   ,1,0) )=0
				";
				
			} else if ($checklocksign>=1) { // get locked 
				$locklimit = " 
					and (if(
 ( A.DevelopState=1 and DP.Estate!=0 ) 
or (H.Type=2 AND H.Type is NOT NULL )  

or 
(	 
	 
	((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1) 
	or ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
	or ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1))



   )
    
or  ((I.Locks=0 AND I.Locks is NOT NULL )  ) -- getStockIdLock(S.StockId)=1 
   ,1,0) )=1
				";
				
			}
			
		}
		
		$sql = "
SELECT S.Id,S.StockId,S.StuffId,S.CompanyId,SE.CompanyId dCompanyId,SE.Forshort dForshort,(S.FactualQty+S.AddQty) AS Qty,S.Price,S.ywOrderDTime AS Date,S.BuyerId,
           A.StuffCname,A.Picture,A.DevelopState,A.Price AS dPrice,P.Forshort,M.Name,E.Rate,E.PreChar,S.ywOrderDTime,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,NOW()) as Hours,

           S.Estate,S.StockRemark,S.AddRemark,S.OrderQty,S.AddQty,S.POrderId,YEARWEEK(S.DeliveryDate,1) as Weeks ,CK.oStockQty 
FROM cg1_stocksheet S 
LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN bps SB ON SB.StuffId=A.StuffId 
	           LEFT JOIN trade_object SE ON SE.CompanyId=SB.CompanyId  AND  SE.ObjectSign IN (1,3) 

			
		LEFT JOIN stuffdevelop DP on DP.StuffId=A.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN ck9_stocksheet CK ON CK.StuffId=S.StuffId 
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.Mid=0  $condition   $locklimit 
and  S.blSign=1  and CG.Id is null and S.CompanyId!=2270  AND S.CompanyId NOT IN (getSysConfig(106))
and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4    
 AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)  ;
";
		
		
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->result_array();
	    }
	    
	    return null;
	}
	
	 function get_params_usestockid($StockId) {
	 
	     if (strlen($StockId)<14)
	     {
            $sql = "select A.StuffId,'' AS POrderId,A.StuffCname from stuffdata A
	    		where A.StuffId=$StockId";
	     }else{
	        $sql = "select S.StuffId,S.POrderId,A.StuffCname from cg1_stocksheet S
	    		LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
	    		where S.StockId=$StockId 
	    	UNION ALL 
                select S.StuffId,S.POrderId,A.StuffCname from cg1_stuffcombox S
	    		LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
	    		where S.StockId=$StockId
	        ";
	     }   
	    
	    return $this->db->query($sql);
    }
    
    function get_unrecived_count($BuyerId='') {
	    
	    
	     
						
		$condition = '';
		
		 if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }

	    $sql = "
	     SELECT  count(*)  as Counts
					     FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
									          FROM cg1_stocksheet S 
									          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 $condition AND M.CompanyId NOT IN (getSysConfig(106))  GROUP BY S.StockId
									)A 
						WHERE A.Qty>A.rkQty   ";
			
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->row()->Counts;
	    }
	    
	    return 0;

    }
    
    function get_waitcg_count($BuyerId='') {
	    
	    $condition = '';
		
		 if ($BuyerId!='') {
		    $condition .= " and S.BuyerId=$BuyerId ";
	    }

/*
		
			LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			 S.Mid=0  $condition   
and  MT.blSign=1  and CG.Id is null 
and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4   

LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN bps SB ON SB.StuffId=A.StuffId 
	           LEFT JOIN trade_object SE ON SE.CompanyId=SB.CompanyId  AND  SE.ObjectSign IN (1,3) 

			
		LEFT JOIN stuffdevelop DP on DP.StuffId=A.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.Mid=0  $condition   
and  MT.blSign=1  and CG.Id is null 
and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4    
 AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)
*/
	    $sql = "
	    SELECT count(*) as Counts 
			FROM cg1_stocksheet S 
			LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId

			WHERE S.Mid=0   and S.CompanyId!=2270 AND S.CompanyId NOT IN (getSysConfig(106)) and MT.blSign=1  and CG.Id is null  and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4 and S.CompanyId<>'2166'   $condition   AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)    
			AND  ( A.DevelopState=0 OR (A.DevelopState=1  AND  EXISTS (SELECT StuffId FROM stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate=0)))
			
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)) ";
			
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->row()->Counts;
	    }
	    
	    return 0;

			
    }
    
    public function all_cgmain_new($nextWeeks) {
		$TotalQty=$TotalAmount=$TotalOverQty=$TotalOverCount=0;
		
		$query= $this->db->query("SELECT YEARWEEK(CURDATE(),1) AS week");
	    $rows=$query->row(0);
        $curWeeks =  $rows->week;
		
        $BuyerQty=$BuyerAmount=$BuyerOverCount=$BuyerCurCount=$BuyerOverAmount=$BuyerCurAmount=$BuyerNextAmount=
        $TotalOverAmount = $TotalOverCount =
        $TotalWeekAmount = $TotalWeekCount = 0;
        $ReviewQty=0;
        
        // AND P.ObjectSign IN (1,3)  AND M.CompanyId NOT IN (getSysConfig(106)) 
		$sql = "SELECT M.BuyerId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
		               (A.Qty-A.rkQty) AS Qty,((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount  
					     FROM (
							  SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
					          FROM cg1_stocksheet S 
					          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
					          WHERE  S.Mid>0 AND  S.rkSign>0 AND M.CompanyId NOT IN (getSysConfig(106)) GROUP BY S.StockId
						)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN staffmain N ON N.Number=M.BuyerId 
						LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
						WHERE A.Qty>A.rkQty  AND M.BuyerId>0 ORDER BY BuyerId";
											
		$query = $this->db->query($sql);
		$personsArr = array();
		if ($query->num_rows()>0) {
			$myRow = $query->row_array();
	    	$oldBuyerId=$myRow["BuyerId"];
			$BuyerName=$myRow["Name"];
			
			foreach ($query->result_array() as $myRow) {
				
		        $BuyerId=$myRow["BuyerId"];
		        
		        
		        
		        if ($BuyerId!=$oldBuyerId){
			        
			      	$val3 = $BuyerAmount-$BuyerOverAmount-$BuyerCurAmount-$BuyerNextAmount;
			      	$val3 = $val3>0?$val3:0;
			      	$val4 = $BuyerNextAmount;
					$val2 = $BuyerCurAmount;
					$val1 = $BuyerOverAmount;
			        
			        $BuyerQty = number_format($BuyerQty);
			        $BuyerAmount = number_format($BuyerAmount);
			        
			        $buyerWaitCgAmount = $buyerWaitCgCount = 0;
			        $waitQuery= $this->waitcg_usebuyer_row($oldBuyerId);
			        $buyerWaitCgAmount = $waitQuery['amt'];
			        $buyerWaitCgAmount = number_format($buyerWaitCgAmount);
			        $buyerWaitCgCount = $waitQuery['cts'];

			        $BuyerOverAmount = number_format($BuyerOverAmount);
			        $BuyerCurAmount = number_format($BuyerCurAmount);
			    
			    	$personsArr[]=array(
				    					'tag'=>'cg_person',
				    					"buyer"=>"$oldBuyerId",
			    						"name"=>"$BuyerName",
			    						"amount"=>"¥$BuyerAmount",
			    						"allqty"=>"$BuyerQty".'pcs',
			    						'valX'=>'-28',
			    						'count1'=>''.$buyerWaitCgCount,
			    						'count2'=>''.$BuyerOverCount,
			    						'count3'=>''.$BuyerCurCount,
			    						"value1"=>"¥$buyerWaitCgAmount",
			    						"value2"=>"¥$BuyerOverAmount",
			    						"value3"=>"¥$BuyerCurAmount",
			    						'v_1'=>$val1,
			    						'v_2'=>$val2,
			    						'v_3'=>$val3,
			    						'v_4'=>$val4,
			    						'chartVals'=>array(
			    							array('value'=>$val1,'color'=>'#fd0300'),
			    							array('value'=>$val2,'color'=>'#358fc1'),
			    							array('value'=>$val4,'color'=>'#c7e0ed'),
			    							array('value'=>$val3,'color'=>'#d5d5d5')
			    						),
			    						
			    						);
			    						
					$oldBuyerId = $BuyerId;
					$BuyerName=$myRow["Name"];
			    	    
			        $BuyerOverAmount =$BuyerOverCount = 0;
			        $BuyerCurAmount =$BuyerCurCount = 0;
			        $BuyerCurAmount = 0;
			        $BuyerAmount = $BuyerQty = 0;
			    } 
			    
			     
				    
				    
			    $eachQty = $myRow["Qty"];
			    $eachAmount = $myRow["Amount"];
			    $Weeks=$myRow["Weeks"];
			    if ($Weeks != "") {
				     if ($Weeks < $curWeeks) {
				    $BuyerOverAmount += $eachAmount;
				    $TotalOverAmount += $eachAmount;
				    $TotalOverCount ++;
				    $BuyerOverCount ++;
			    	} else if ($Weeks == $curWeeks) {
				    $BuyerCurAmount += $eachAmount;
				    $TotalWeekAmount += $eachAmount;
				    $TotalWeekCount ++;
				    $BuyerCurCount ++;
			    	} else if ($Weeks == $nextWeeks) {
				    	$BuyerNextAmount += $eachAmount;

			    	}
			    }
			    
			    $TotalQty += $eachQty;
			    $TotalAmount += $eachAmount;
			   
			    $BuyerAmount += $eachAmount;
			    $BuyerQty += $eachQty;
			}
			
			$val3 = $BuyerAmount-$BuyerOverAmount-$BuyerCurAmount;
			$val3 = $val3>0?$val3:0;
			$val4 = $BuyerNextAmount;
			$val2 = $BuyerCurAmount;
			$val1 = $BuyerOverAmount;
			        
			
		    $BuyerQty = number_format($BuyerQty);
		    $BuyerAmount = number_format($BuyerAmount);
		        
		    $buyerWaitCgAmount = $buyerWaitCgCount = 0;

	        
	         $waitQuery= $this->waitcg_usebuyer_row($oldBuyerId);
			        $buyerWaitCgAmount = $waitQuery['amt'];
			        $buyerWaitCgAmount = number_format($buyerWaitCgAmount);
			        $buyerWaitCgCount = $waitQuery['cts'];
			        
	        
	        $BuyerOverAmount = number_format($BuyerOverAmount);
	        $BuyerCurAmount = number_format($BuyerCurAmount);
	    
	    	$personsArr[]=array(
		    	'tag'=>'cg_person',
		    					"buyer"=>"$oldBuyerId",
	    						"name"=>"$BuyerName",
	    						"amount"=>"¥$BuyerAmount",
	    						"allqty"=>"$BuyerQty".'pcs',
	    						'valX'=>'-28',
	    						'count1'=>''.$buyerWaitCgCount,
	    						'count2'=>''.$BuyerOverCount,
	    						'count3'=>''.$BuyerCurCount,
	    						"value1"=>"¥$buyerWaitCgAmount",
	    						"value2"=>"¥$BuyerOverAmount",
	    						"value3"=>"¥$BuyerCurAmount",
	    						'v_1'=>$val1,
	    						'v_2'=>$val2,
	    						'v_3'=>$val3,
	    						'v_4'=>$val4,
	    						'chartVals'=>array(
			    							array('value'=>$val1,'color'=>'#fd0300'),
			    							array('value'=>$val2,'color'=>'#358fc1'),
			    							array('value'=>$val4,'color'=>'#c7e0ed'),
			    							array('value'=>$val3,'color'=>'#d5d5d5')
			    						),

	    						
	    						);

	    						

			
			
			
		}
		
		
		
		return $personsArr ;
	}
    function get_weeks_before($week, $BuyerId='') {
	    
	    $condition = '';
	    if ($BuyerId!='') {
		    $condition = " and M.BuyerId=$BuyerId ";
	    }
	    
	    $sql = "
	    SELECT YEARWEEK(S.DeliveryDate,1) AS Weeks,
		               sum(A.Qty-A.rkQty) AS Qty,sum((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount  ,Count(*) as Counts
					     FROM (
							  SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
					          FROM cg1_stocksheet S 
					          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
					          WHERE  S.Mid>0 AND  S.rkSign>0 $condition AND M.CompanyId NOT IN (getSysConfig(106)) GROUP BY S.StockId
						)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
						
						LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
						WHERE 1 $condition and  A.Qty>A.rkQty  AND M.BuyerId>0 group by YEARWEEK(S.DeliveryDate,1)  
";
//YEARWEEK(S.DeliveryDate,1)<='$week'
	    
	    $query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->result_array();
	    }
	    
	    return null;
	    
    }
    
    function get_punc_months($StuffId) {
	    $sql = "
select DATE_FORMAT(M.rkDate,'%Y-%m') month from ck1_rksheet S
LEFT JOIN ck1_rkmain M  ON M.Id=S.Mid  where S.StuffId=$StuffId group by DATE_FORMAT(M.rkDate,'%Y-%m') order by month desc limit 3;";
		$query = $this->db->query($sql);
	    if($query->num_rows() > 0){
		    return $query->result_array();
	    }
	    return null;
    }
    
    function cg_punctuality_stuff($StuffId, $afterDate, $checkMonth='') {
	    //采购交货准时率统计
		$condition = '';
		if ($StuffId != '') {
			$condition.= " AND S.StuffId=$StuffId  ";
		}
		if ($afterDate != '') {
			$condition.= "  AND DATE_FORMAT(M.rkDate,'%Y-%m-%d')>='$afterDate'  ";
		}
		if ($checkMonth != '') {
			$condition.= "  AND DATE_FORMAT(M.rkDate,'%Y-%m')='$checkMonth'  ";
		}
	    
		$sql = " SELECT SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty   
		FROM ck1_rksheet S
		LEFT JOIN ck1_rkmain M  ON M.Id=S.Mid 
		LEFT JOIN cg1_stocksheet G  ON G.StockId=S.StockId  
		LEFT JOIN cg1_stockmain GM ON GM.Id=G.Mid 
		LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN currencydata D ON D.Id=C.Currency
		WHERE   1  $condition";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$NumsRow = $query->row_array();
		    $P_Qty=$NumsRow["Qty"];
		    if ($P_Qty>0){
				$P_OverQty=$NumsRow["OverQty"];
				$Punc_Value=($P_Qty-$P_OverQty)/$P_Qty*100;
				
				return round($Punc_Value);
		    }
		}
		
		
		return null;
    }
    
    function cg_punctuality($checkMonth='',$CompanyId='',$week='',  $BuyerId='') {
	    //采购交货准时率统计
		$condition = '';
		if ($checkMonth != '') {
			$condition.= "  AND DATE_FORMAT(M.rkDate,'%Y-%m')='$checkMonth'  ";
		}
		if ($CompanyId != '') {
			$condition.= " AND M.CompanyId='$CompanyId'  ";
		}
		if ($week!='') {
			$condition.= " AND  YEARWEEK(G.DeliveryDate,1)='$week'   ";
		}
		
		 if ($BuyerId!='') {
		    $condition .= " and GM.BuyerId=$BuyerId ";
	    }
	    
		
		$sql = " SELECT SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty   
		FROM ck1_rksheet S
		LEFT JOIN ck1_rkmain M  ON M.Id=S.Mid 
		LEFT JOIN cg1_stocksheet G  ON G.StockId=S.StockId  
		LEFT JOIN cg1_stockmain GM ON GM.Id=G.Mid 
		LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN currencydata D ON D.Id=C.Currency
		WHERE   1  $condition";
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$NumsRow = $query->row_array();
		    $P_Qty=$NumsRow["Qty"];
		    if ($P_Qty>0){
				$P_OverQty=$NumsRow["OverQty"];
				$Punc_Value=($P_Qty-$P_OverQty)/$P_Qty*100;
				
				return round($Punc_Value);
		    }
		}
		
		
		return null;
    }
    
    function get_child_stuff($mStockId, $stockid='') {
	    $this->load->model('StuffDataModel');
	    $dataArray = array();
	      $subSql = "select AL.StockId,AL.StuffId,AL.OrderQty,AL.Relation,
			K.tStockQty,AL.StockQty,A.Picture,A.StuffCname,P.Forshort
			from cg1_stuffcombox AL 
			LEFT JOIN stuffdata A ON A.StuffId=AL.StuffId
			LEFT JOIN stufftype ST ON ST.TypeId=A.TypeId
			LEFT JOIN stuffmaintype MT ON MT.Id=ST.mainType
			LEFT JOIN base_mposition MP ON MP.Id=ST.Position 
			LEFT JOIN ck9_stocksheet K ON K.StuffId=AL.StuffId
left join cg1_stocksheet G on G.StockId=AL.mStockId
left join trade_object P on G.CompanyId=P.CompanyId
			where AL.mStockId=?;";
		    $query = $this->db->query($subSql, $mStockId);
		    $nums = $query->num_rows();
		    if ($nums > 0) {
			    $rs = $query->result();
			    foreach ($rs as $row) {
				    $StockId = $row->StockId;
				    
				    $bg = '';
				    if ($stockid == $StockId) {
					    $bg = '#fffcbb';
				    }
				    
				    $StuffId = $row->StuffId;
				    $OrderQty = $row->OrderQty;
				    $Relation = $row->Relation;
				    $Forshort = $row->Forshort;
				    $img =  $this->StuffDataModel->get_stuff_icon($StuffId);
				    $dataArray[]=array(
					    'newload'=>'1',
					    'url'=>$img,
					    'StuffId'=>$StuffId,
					    'StockId'=>$StockId.'',
					    'bg_color'=>$bg,
					    'lockImg'=>'child_stuff',
					    'Picture'=>$row->Picture,
					    'tag'=>'child',
					    'title'=>$StuffId.'-'.$row->StuffCname,
					    'col1Img'=>'scdj_11',
					    'col1'=> number_format($OrderQty)."($Relation)",
					    'col2Img'=>'wh_tstock',
					    'col2'=>number_format($row->tStockQty),
					    'col3Img'=>'',
					    'col4'=>$Forshort
				    );
						    
			    }
		    }

			return $dataArray;
    }
    
    public function get_parent_info($stockid) {
	    
	    $sql = "
	    select AL.mStockId,AL.Relation,G.StuffId,G.OrderQty,(G.FactualQty+G.AddQty) realQty,G.Date,A.Picture,A.StuffCname,
G.CompanyId,P.Forshort 
 from cg1_stuffcombox AL
left join cg1_stocksheet G on G.StockId=AL.mStockId
LEFT JOIN stuffdata A ON A.StuffId=G.StuffId
left join trade_object P on G.CompanyId=P.CompanyId
where AL.StockId=?;";
	    $query= $this->db->query($sql, $stockid);
	    
	    $dataArray = array();
	    if ($query->num_rows() >0) {
		    $this->load->model('StuffDataModel');
		    
		    $row = $query->row();
		    $mStockId = $row->mStockId;
		    $mStuffId = $row->StuffId;
		    $OrderQty = $row->OrderQty;
		    $Relation = $row->Relation;
		    $Date = $row->Date;
		    $Forshort = $row->Forshort;
		    $img =  $this->StuffDataModel->get_stuff_icon($mStuffId);
		    $dataArray[]=array(
			    'newload'=>'1',
			    'open'=>'1',
			    'url'=>$img,
			    'Id'=>$mStockId.'',
			    'type'=>$stockid,
			    'StockId'=>$mStockId.'',
			    'StuffId'=>$mStuffId,
			    'Picture'=>$row->Picture,
			    'method'=>'child_stuffs',
			    'tag'=>'mum',
			    'showArrow'=>'1',
			    'title'=>$mStuffId.'-'.$row->StuffCname,
			    'col1Img'=>'scdj_11',
			    'col1'=> number_format($OrderQty)."($Relation)",
			    'col2Img'=>'',
			    'col3Img'=>'',
			    'col4'=>$Date
		    );
		    
		    $children = $this->get_child_stuff($mStockId, $stockid);
		    
		    $dataArray = array_merge($dataArray, $children);
		   		    
		    
	    }
	    
	    
	    return $dataArray;
	    
    }
    
	/*
		 $subSql = "select AL.StockId,AL.StuffId,AL.OrderQty,AL.Relation,
			K.tStockQty,AL.StockQty,A.Picture,A.StuffCname,P.Forshort
			from cg1_stuffcombox AL 
			LEFT JOIN stuffdata A ON A.StuffId=AL.StuffId
			LEFT JOIN stufftype ST ON ST.TypeId=A.TypeId
			LEFT JOIN stuffmaintype MT ON MT.Id=ST.mainType
			LEFT JOIN base_mposition MP ON MP.Id=ST.Position 
			LEFT JOIN ck9_stocksheet K ON K.StuffId=AL.StuffId
left join cg1_stocksheet G on G.StockId=AL.mStockId
left join trade_object P on G.CompanyId=P.CompanyId
			where AL.mStockId=?;";
		    $query = $this->db->query($subSql, $mStockId);
		    $nums = $query->num_rows();
		    if ($nums > 0) {
			    $rs = $query->result();
			    foreach ($rs as $row) {
				    $StockId = $row->StockId;
				    
				    $bg = '';
				    if ($stockid == $StockId) {
					    $bg = '#fffcbb';
				    }
				    $StuffId = $row->StuffId;
				    $OrderQty = $row->OrderQty;
				    $Relation = $row->Relation;
				    $Forshort = $row->Forshort;
				    $img =  $this->StuffDataModel->get_stuff_icon($StuffId);
				    $dataArray[]=array(
					    'newload'=>'1',
					    'url'=>$img,
					    'StockId'=>$StockId.'',
					    'Picture'=>$row->Picture,
					    'StuffId'=>$StuffId,
					    'tag'=>'child',
					    'bg_color'=>$bg,
					    'title'=>$StuffId.'-'.$row->StuffCname,
					    'col1Img'=>'scdj_11',
					    'col1'=> number_format($OrderQty)."($Relation)",
					    'col2Img'=>'wh_tstock',
					    'col2'=>number_format($row->tStockQty),
					    'col3Img'=>'',
					    'col4'=>$Forshort
				    );
						    
			    }
		    }

	*/
	public function waitcg_usebuyer ($BuyerId) {
		$sql = "SELECT sum((S.FactualQty+S.AddQty)*S.Price*E.Rate) Amount,count(*) nums
			FROM cg1_stocksheet S 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.BuyerId=?  and S.blSign=1 and  S.Mid=0 and T.mainType!=getSysConfig(103) and (S.FactualQty>0 OR S.AddQty>0) 
			
			AND  ( A.DevelopState=0 OR (A.DevelopState=1  AND  EXISTS (SELECT StuffId FROM stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate=0)))
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)) 
			
			 ORDER BY S.BuyerId,S.CompanyId";
			
	    return $this->db->query($sql,array($BuyerId));
	}
	
	
	function cg_reset_add($params) {
		
		
// 		return -3;
		$this->db->trans_start();
		$miniids = element('idList', $params, '');
		$oldId = element('oldId', $params, '');
		$addQty = element('addQty', $params, ''); 
		
		$time = $this->DateTime;
		$oper = $this->LoginNumber;
		if ($miniids != '' && $addQty!='' && $oldId!='') {
			
			
			$checkSemi = "
			SELECT COUNT(1) AS Counts FROM  cg1_semifinished M inner join cg1_stocksheet S on S.StockId=M.mStockId WHERE S.Id in ($oldId,$miniids) ";
			$query = $this->db->query($checkSemi);
			if ($query->num_rows() > 0) {
				$cts = $query->row()->Counts;
				if ($cts > 0) {
					return -3;
				}
				
			}
			
			$sqlAddedQty = "select sum(FactualQty+AddQty) as AddQty from cg1_stocksheet where Id in ($miniids)  ";
			$query = $this->db->query($sqlAddedQty);
			if ($query->num_rows() > 0) {
				$addQty = $query->row()->AddQty;
				
				
				$sqlMini = "update cg1_stocksheet set StockQty=OrderQty,FactualQty=0,AddQty=0,modified='$time',modifier='$oper' where Id in ($miniids) ";
				$this->db->query($sqlMini);
				$sqlMini = "update cg1_stocksheet set  AddQty=(AddQty+$addQty),modified='$time',modifier='$oper' where Id=$oldId";
				
				$this->db->query($sqlMini);
				
			}
			
			
			
			
			
			
			
		}
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		} else {
			return 1;
		}
		
		
	}
	
	public function waitcg_usebuyer_row ($BuyerId) {
		
		/*
			
			LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			 S.Mid=0  $condition   
and  MT.blSign=1  and CG.Id is null 
and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4   

LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN bps SB ON SB.StuffId=A.StuffId 
	           LEFT JOIN trade_object SE ON SE.CompanyId=SB.CompanyId  AND  SE.ObjectSign IN (1,3) 

			
		LEFT JOIN stuffdevelop DP on DP.StuffId=A.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.Mid=0  $condition   
and  MT.blSign=1  and CG.Id is null 
and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4    
 AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)
		*/
		
		
		$sql = "SELECT ((S.FactualQty+S.AddQty)*S.Price*E.Rate) Amount,S.StockId 
			FROM cg1_stocksheet S 
			LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.BuyerId=?  and  MT.blSign=1 and CG.Id is null     and  S.Mid=0  and (S.FactualQty>0 OR S.AddQty>0)  and S.CompanyId!=2270   AND S.CompanyId NOT IN (getSysConfig(106))
			 AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)
			-- and (I.Locks=0 or I.Locks is null)
			AND  ( A.DevelopState=0 OR (A.DevelopState=1  AND  EXISTS (SELECT StuffId FROM stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate=0)))
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)) 
			
			 ";
			
		if ('10882' == $BuyerId) {
			
		$sql = "SELECT sum((S.FactualQty+S.AddQty)*S.Price*E.Rate) amt,count(*) as cts 
			FROM cg1_stocksheet S 
			LEFT JOIN yw1_scsheet CG ON CG.mStockId = S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN stuffmaintype MT ON MT.Id=T.mainType 
			LEFT JOIN staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN currencydata E ON E.Id=P.Currency 
			WHERE S.BuyerId=?  and  MT.blSign=1 and CG.Id is null     and  S.Mid=0  and (S.FactualQty>0 OR S.AddQty>0)  and S.CompanyId!=2270   AND S.CompanyId NOT IN (getSysConfig(106))
			 AND NOT EXISTS(SELECT OP.StuffId FROM stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM ck1_rksheet R WHERE R.StockId=S.StockId)
			-- and (I.Locks=0 or I.Locks is null)
			AND  ( A.DevelopState=0 OR (A.DevelopState=1  AND  EXISTS (SELECT StuffId FROM stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate=0)))
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)) 
			
			 ";
			 $query= $this->db->query($sql,array($BuyerId));
			 
			 return $query->row_array();
			}
	    $query= $this->db->query($sql,array($BuyerId));
	    $nums = 0;
	    $amount = 0;
	    if ($query->num_rows()>0) {
		    $rs = $query->result();
		    
		    foreach($rs as $row) {
			    $lockSign = 0;
			    
			    $StockId = $row->StockId;
			    
			    $lockSign =  $this->get_stockid_lock($StockId);
			    
			    if ($lockSign == 0 ) {
				    $nums ++;
				    $amount+=$row->Amount;
			    }
			    
		    }
	    }
	    
	    return array('amt'=>$amount, 'cts'=>$nums);
	}
	
	function get_stockid_lock($StockId) {
		
		$sql = "select getStockIdLock($StockId) as Sign;";
		 $query= $this->db->query($sql);
		if ($query->num_rows()>0) {
		    return $query->row()->Sign;
	    }
	    return 0;
		
	}
	
	public function all_cgmain() {
		$TotalQty=$TotalAmount=$TotalOverQty=$TotalOverCount=0;
		
		$query= $this->db->query("SELECT YEARWEEK(CURDATE(),1) AS week");
	    $rows=$query->row(0);
        $curWeeks =  $rows->week;
		
        $BuyerQty=$BuyerAmount=$BuyerOverCount=$BuyerCurCount=$BuyerOverAmount=$BuyerCurAmount=
        $TotalOverAmount = $TotalOverCount =
        $TotalWeekAmount = $TotalWeekCount = 0;
        $ReviewQty=0;
        
        // AND P.ObjectSign IN (1,3)  AND M.CompanyId NOT IN (getSysConfig(106)) 
		$sql = "SELECT M.BuyerId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
		               (A.Qty-A.rkQty) AS Qty,((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount  
					     FROM (
							  SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
					          FROM cg1_stocksheet S 
					          LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					          LEFT JOIN ck1_rksheet R ON R.StockId=S.StockId
					          WHERE  S.Mid>0 AND  S.rkSign>0 AND M.CompanyId NOT IN (getSysConfig(106)) GROUP BY S.StockId
						)A 
						LEFT JOIN cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN staffmain N ON N.Number=M.BuyerId 
						LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN currencydata D ON D.Id=P.Currency  
						WHERE A.Qty>A.rkQty  AND M.BuyerId>0 ORDER BY BuyerId";
											
		$query = $this->db->query($sql);
		$personsArr = array();
		if ($query->num_rows()>0) {
			$myRow = $query->row_array();
	    	$oldBuyerId=$myRow["BuyerId"];
			$BuyerName=$myRow["Name"];
			
			foreach ($query->result_array() as $myRow) {
				
		        $BuyerId=$myRow["BuyerId"];
		        
		        
		        
		        if ($BuyerId!=$oldBuyerId){
			      	$val3 = $BuyerAmount-$BuyerOverAmount-$BuyerCurAmount;
					$val2 = $BuyerCurAmount;
					$val1 = $BuyerOverAmount;
			        
			        $BuyerQty = number_format($BuyerQty);
			        $BuyerAmount = number_format($BuyerAmount);
			        
			        $buyerWaitCgAmount = $buyerWaitCgCount = 0;
			        $waitQuery= $this->waitcg_usebuyer_row($oldBuyerId);
			        $buyerWaitCgAmount = $waitQuery['amt'];
			        $buyerWaitCgAmount = number_format($buyerWaitCgAmount);
			        $buyerWaitCgCount = $waitQuery['cts'];

			        $BuyerOverAmount = number_format($BuyerOverAmount);
			        $BuyerCurAmount = number_format($BuyerCurAmount);
			    
			    	$personsArr[]=array(
				    					"buyer"=>"$oldBuyerId",
			    						"name"=>"$BuyerName",
			    						"amount"=>"¥$BuyerAmount",
			    						"allqty"=>"$BuyerQty".'pcs',
			    						"value1"=>
			    							array(array("¥$buyerWaitCgAmount","#3b3e41",'13'),array("($buyerWaitCgCount)","#727171","11"))
											,
			    						"value2"=>
			    							
			    							array(array("¥$BuyerOverAmount","#ff0000",'13'),array("($BuyerOverCount)","#727171","11"))
			    							,
			    						"value3"=>
			    							
			    							array(array("¥$BuyerCurAmount","#3b3e41",'13'),array("($BuyerCurCount)","#727171","11"))
			    							,
			    						'values'=>array("$val1","$val2","$val3"),
			    						
			    						);
			    						
					$oldBuyerId = $BuyerId;
					$BuyerName=$myRow["Name"];
			    	    
			        $BuyerOverAmount =$BuyerOverCount = 0;
			        $BuyerCurAmount =$BuyerCurCount = 0;
			        
			        $BuyerAmount = $BuyerQty = 0;
			    } 
			    
			     
				    
				    
			    $eachQty = $myRow["Qty"];
			    $eachAmount = $myRow["Amount"];
			    $Weeks=$myRow["Weeks"];
			    if ($Weeks != "") {
				     if ($Weeks < $curWeeks) {
				    $BuyerOverAmount += $eachAmount;
				    $TotalOverAmount += $eachAmount;
				    $TotalOverCount ++;
				    $BuyerOverCount ++;
			    	} else if ($Weeks == $curWeeks) {
				    $BuyerCurAmount += $eachAmount;
				    $TotalWeekAmount += $eachAmount;
				    $TotalWeekCount ++;
				    $BuyerCurCount ++;
			    	}
			    }
			    
			    $TotalQty += $eachQty;
			    $TotalAmount += $eachAmount;
			   
			    $BuyerAmount += $eachAmount;
			    $BuyerQty += $eachQty;
			}
			
			$val3 = $BuyerAmount-$BuyerOverAmount-$BuyerCurAmount;
			$val2 = $BuyerCurAmount;
			$val1 = $BuyerOverAmount;
			        
			
		    $BuyerQty = number_format($BuyerQty);
		    $BuyerAmount = number_format($BuyerAmount);
		        
		    $buyerWaitCgAmount = $buyerWaitCgCount = 0;

	        
	         $waitQuery= $this->waitcg_usebuyer_row($oldBuyerId);
			        $buyerWaitCgAmount = $waitQuery['amt'];
			        $buyerWaitCgAmount = number_format($buyerWaitCgAmount);
			        $buyerWaitCgCount = $waitQuery['cts'];
			        
	        
	        $BuyerOverAmount = number_format($BuyerOverAmount);
	        $BuyerCurAmount = number_format($BuyerCurAmount);
	    
	    	$personsArr[]=array(
		    					"buyer"=>"$oldBuyerId",
	    						"name"=>"$BuyerName",
	    						"amount"=>"¥$BuyerAmount",
	    						"allqty"=>"$BuyerQty".'pcs',
	    						"value1"=>
	    							array(array("¥$buyerWaitCgAmount","#3b3e41",'13'),array("($buyerWaitCgCount)","#727171","11"))
									,
	    						"value2"=>
	    							
	    							array(array("¥$BuyerOverAmount","#ff0000",'13'),array("($BuyerOverCount)","#727171","11"))
	    							,
	    						"value3"=>
	    							
	    							array(array("¥$BuyerCurAmount","#3b3e41",'13'),array("($BuyerCurCount)","#727171","11"))
	    							,
	    						'values'=>array("$val1","$val2","$val3"),
	    						
	    						);

	    						

			
			
			
		}
		
		
		$val2 = round($TotalWeekAmount / ($TotalAmount > 0 ? $TotalAmount : 1) * 100,1);
		$val3 = round($TotalOverAmount / ($TotalAmount > 0 ? $TotalAmount : 1) * 100,1);
		$val1 = 100 - $val2 - $val3;
		$val4 = ($val3 + $val2/2 )/100;
		$TotalAmount = number_format($TotalAmount);
		$TotalQty = number_format($TotalQty);
		$TotalWeekAmount = number_format($TotalWeekAmount);
		if ($TotalWeekCount > 0) {
			$TotalWeekAmount = $TotalWeekAmount . "($TotalWeekCount)";
			
		}
		
		$TotalOverAmount = number_format($TotalOverAmount);
		if ($TotalOverCount > 0) {
			$TotalOverAmount = $TotalOverAmount . "($TotalOverCount)";
			
		}
		$headArr = array("values"=>array("$val1","$val2","$val3","$val4"),
						  "lbls"=>array("¥$TotalWeekAmount","¥$TotalOverAmount","¥$TotalAmount","$TotalQty".'pcs'));
		
		
		return array("persons"=>$personsArr,'head'=>$headArr) ;
	}
	
    function __construct()
    {
        parent::__construct();
    }
    
   public function kd_stuffs_in_company($com=-1) {
	    
		 $DataPublic = $this->DataPublic;
		 $this->load->model('stuffdataModel');
		 $baseUrl =$this->stuffdataModel->get_picture_path();
		 //$jsonArray = array();
		 $myCompanyId =$com;
		 $SearchRows = " and S.CompanyId='$myCompanyId' ";
		 
		 $wareNameInfoDict = array("48(1A)"=>"1A","48(3A)"=>"3A","48(2B)"=>"2B","48(3B)"=>"3B",'47(1)'=>"47(1)");
		 
		$nowWeek = date("W");
		/*
			SELECT '0' AS Sign,S.StuffId,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,A.TypeId ,U.Name AS UnitName
		FROM cg1_stocksheet S
		LEFT JOIN stuffdata A ON A.StuffId=S.StuffId 
		LEFT JOIN stuffunit U ON U.Id=A.Unit
		LEFT JOIN  stuffproperty  OP  ON OP.StuffId=A.StuffId AND OP.Property=2	
		WHERE 1  $SearchRows   and (S.Mid>0  OR  (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) 
		and NOT EXISTS(SELECT T.Property FROM stuffproperty T WHERE  T.StuffId=S.StuffId AND T.Property=9)
		GROUP BY S.StuffId  
		 UNION ALL
		    SELECT  '1' AS Sign,A.StuffId,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,A.TypeId ,U.Name AS UnitName
			FROM cg1_stocksheet S 
			LEFT JOIN cg1_stockmain M ON M.Id=S.Mid
		    INNER JOIN cg1_stuffcombox G ON G.mStockId=S.StockId 
			LEFT JOIN stuffdata A ON A.StuffId=G.StuffId
			LEFT JOIN stuffunit U ON U.Id=A.Unit 
			LEFT JOIN  stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2	
			WHERE 1 and S.rkSign>0 and (S.Mid>0  OR  (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) $SearchRows 
			GROUP BY G.StuffId  
		ORDER BY StuffId DESC
		*/
		$sql = "
           SELECT '0' AS Sign,S.StuffId,A.StuffCname,A.Picture,C.Name as WareName,U.Decimals,
                  MIN(S.DeliveryWeek) AS DeliveryWeek 
				FROM cg1_stocksheet S
			 -- 	LEFT JOIN cg1_stockmain M ON M.Id=S.Mid
				LEFT JOIN stuffdata A ON A.StuffId=S.StuffId 
				LEFT JOIN stufftype TP ON TP.TypeId=A.TypeId 
				left join  base_mposition C ON C.Id=A.SendFloor
				LEFT JOIN  stuffunit U ON U.Id=A.Unit 
				WHERE 1 AND  S.rkSign>0 AND   S.Mid>0  AND S.CompanyId='$myCompanyId' AND S.StuffId>0  
				        AND not exists ( select G.mStockId  from cg1_stuffcombox  G WHERE G.mStockId = S.StockId) 
                GROUP BY S.StuffId   
		 UNION ALL
		    SELECT  '1' AS Sign,A.StuffId,A.StuffCname,A.Picture,C.Name as WareName,U.Decimals,
		             MIN(S.DeliveryWeek) AS DeliveryWeek  
			FROM cg1_stocksheet S 
			INNER JOIN cg1_stuffcombox G ON G.mStockId=S.StockId 
			LEFT JOIN  cg1_stockmain M ON M.Id=S.Mid
			LEFT JOIN  stuffdata A ON A.StuffId=G.StuffId
			LEFT JOIN  stufftype TP ON TP.TypeId=A.TypeId 
		    LEFT JOIN  base_mposition C ON C.Id=A.SendFloor
		    LEFT JOIN  stuffunit U ON U.Id=A.Unit  
			WHERE 1 AND S.rkSign>0 AND  S.Mid>0  and M.CompanyId='$myCompanyId' AND A.StuffId>0 
			GROUP BY G.StuffId  
		ORDER BY DeliveryWeek,StuffId DESC";
		
		$query = $this->db->query($sql,$com);
		
					
		$stuffProviderList = array(); $SearchRowsA="";
		if ($query->num_rows() > 0) {
		
		    foreach ($query->result_array() as $sqlRow) {
		
		  	$StuffId = $sqlRow['StuffId'];
		    $Decimals = $sqlRow['Decimals'];
		   // $myCompanyId = $sqlRow['CompanyId'];
		    
			//已购总数
			  
	         $Sign=  $sqlRow["Sign"];
	         $cgTempSql = "";
	         if ($Sign==1)  {
		         $cgTempSql = "SELECT SUM(S.OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty ,MAX(G.DeliveryDate) as DeliveryDate  
			        FROM cg1_stocksheet G
			        LEFT JOIN  cg1_stockmain M ON M.Id=G.Mid 
			        INNER JOIN cg1_stuffcombox S ON S.mStockId=G.StockId 
			        LEFT JOIN  stuffdata A ON A.StuffId=S.StuffId 
			        WHERE  M.CompanyId='$myCompanyId'  and S.StuffId='$StuffId' and G.Mid>0 ";
	               $SearchRowsA=$SearchRows;
	               $SearchRowsA = '';
	         } else {
		         $cgTempSql = "SELECT SUM(S.OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty,MAX(S.DeliveryDate) as DeliveryDate   
			        FROM cg1_stocksheet S 
			        LEFT JOIN cg1_stockmain M ON M.Id=S.Mid  
			        LEFT JOIN stuffdata A ON A.StuffId=S.StuffId 
			        WHERE  S.StuffId='$StuffId' and S.Mid>0 and M.CompanyId='$myCompanyId' ";
	             $SearchRowsA=$SearchRows;
	         }
			// $DeliveryDate = $sqlRow["DeliveryDate"];
			$cgTemp=$this->db->query($cgTempSql);
			$cgQty = $odQty = 0;
			if ($cgTemp->num_rows() > 0) {
				$cgTempRow = $cgTemp->row_array();
				$cgQty = $cgTempRow['Qty'];
				$odQty = $cgTempRow['odQty'];
				$DeliveryDate = $cgTempRow['DeliveryDate'];
			}
			//已收货总数
			
			
			$rkTemp=$this->db->query("SELECT SUM(R.Qty) AS Qty FROM ck1_rksheet R 
			LEFT JOIN cg1_stocksheet S ON S.StockId=R.StockId
			WHERE R.StuffId='$StuffId' and R.Type=1 $SearchRowsA");
			$rkQty = 0;
			if ($rkTemp->num_rows() > 0) {
				$rkTempRow = $rkTemp->row_array();
				$rkQty = $rkTempRow['Qty'];
			}
			
			//待入库数量
			$rkTemp2=$this->db->query("SELECT IFNULL(SUM(C.Qty),0) AS Qty 
			FROM qc_cjtj C
			LEFT JOIN gys_shsheet G ON G.Id=C.Sid 
			LEFT JOIN gys_shmain S ON S.Id=G.Mid 
			WHERE C.StuffId='$StuffId' AND C.Estate=1 $SearchRowsA");
			$rkQty2 = 0;
			if ($rkTemp2->num_rows() > 0) {
				$rkTempRow2 = $rkTemp2->row_array();
				$rkQty2 = $rkTempRow2['Qty'];
			}
	
			
			//待送货数量
			$shSql=$this->db->query("SELECT SUM(G.Qty) AS Qty FROM gys_shsheet G
										   LEFT JOIN gys_shmain S ON S.Id=G.Mid
										   WHERE 1 AND G.SendSign=0 AND G.Estate>0 AND G.StuffId='$StuffId' $SearchRowsA ");
			$shQty = 0;
			if ($shSql->num_rows() > 0) {
				$shSqlRow = $shSql->row_array();
				$shQty = $shSqlRow['Qty'];
			}
			
			// $noQty=$cgQty-$rkQty-$shQty;
/*
			if ($this->LoginNumber == 11965) {
			
			}
*/
			$noQty=round($cgQty-$rkQty-$rkQty2-$shQty,$Decimals);
			
		    //退货的总数量
			$thSql=$this->db->query("SELECT SUM( S.Qty ) AS thQty  FROM ck2_thmain M  
										   LEFT JOIN ck2_thsheet S ON S.Mid = M.Id
										   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ");
			$thQty = 0;
			if ($thSql->num_rows() > 0) {
				$thSqlRow = $thSql->row_array();
				$thQty = $thSqlRow['thQty']==""?0:$thSqlRow['thQty'];
			}
		    //补货的数量
			$bcSql=$this->db->query("SELECT SUM( S.Qty ) AS bcQty  FROM ck3_bcmain M 
										   LEFT JOIN ck3_bcsheet S ON S.Mid = M.Id
										   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ");
		    $bcQty = 0;
			if ($bcSql->num_rows() > 0) {
				$bcSqlRow = $bcSql->row_array();
				$bcQty = $bcSqlRow['bcQty']==""?0:$bcSqlRow['bcQty'];
			}
			
			$bcshSql=$this->db->query("SELECT SUM( S.Qty ) AS Qty FROM gys_shmain M
								LEFT JOIN gys_shsheet S ON S.Mid = M.Id
								WHERE 1 AND M.CompanyId = '$myCompanyId' AND S.Estate>0 AND S.Locks=1 AND S.StuffId='$StuffId' AND (S.StockId='-1' or S.SendSign='1')");
			$bcshQty = 0;
			if ($bcshSql->num_rows() > 0) {
				$bcshSqlRow = $bcshSql->row_array();
				$bcshQty = $bcshSqlRow['Qty']==""?0:$bcshSqlRow['Qty'];
			}
			// $webQty=$thQty-$bcQty-$bcshQty;
		$webQty=round($thQty-$bcQty-$bcshQty,$Decimals); //未补数量
		//$webQty=$thQty.'|'.$bcQty.'|'.$bcshQty; //未补数量
		 if($noQty>0 || $webQty>0 || $this->LoginNumber==11965) {
		  $DeliveryWeek=$sqlRow['DeliveryWeek'];
		  //$weekStr = date('W',strtotime($DeliveryDate));
		  $weekStr = $DeliveryWeek>0?substr($DeliveryWeek, 4):0;
		  $bgcolor = $nowWeek > $weekStr ? "#FF0000":"#000000";
		    $Picture = $sqlRow['Picture'];
		  $others = array('edit'=>array("$noQty","0","$webQty"),
		  				  'Picture'=>"$Picture",
		  				  'url' =>"$baseUrl"."$StuffId"."_s.jpg",
		  				  'weeks'=>array("text"=>"$weekStr","bgcolor"=>"$bgcolor") 
		  				  );
		  				
		  				  //if ($Picture)
		  				  //$baseUrl
		  				  
			   $StuffCname = $sqlRow['StuffCname'];
			   $WareName = $sqlRow['WareName'];
			   $WareName = element("$WareName",$wareNameInfoDict,$sqlRow['WareName']);
			  // $StockId = $sqlRow['StockId'];
			  // $noQty = number_format($noQty);
			 
	// 		   if (date())
			   
/*
			   
			   if ($this->LoginNumber == 11965) {

				$StuffCname = "$cgQty-$rkQty-$rkQty2-$shQty";
			}
			
*/
			
		  $stuffProviderList[] = array("ContentTxt"=>"$noQty",
								   "FiledName"    =>"$StuffId-$StuffCname",
								   "FieldVal"       =>"$StuffId",
								   "require" =>"0",
								   "info"    =>"$WareName",
								   'others'   =>$others
								   
								  
								   //'$StockId'=>"$StockId"
								   ); 
		  }
   	  }
	}
		return $stuffProviderList;		
	//$jsonArray = array('Stuffs'=>$stuffProviderList);
    }
   
   public function cg_process($StockId,$StuffId) {
	   //
	   //采购订单状态显示  传入参数:StockId,StuffId

//1.检查开发状态
$ProcessArray=array();   $interval_d = '';   $interval_qc = '';
	 $this->load->library('dateHandler');
	 
	 $qtyAll = 0;$orinalQty = 0;
$L_xdTime="";
if ($StockId>0 && $StuffId>0){
         			//2.检查锁定状态
			$L_Locks=1;$L_unLockDate="";
			$lockResult=$this->db->query("SELECT TIMESTAMPDIFF(DAY,IF(L.LockDate='0000-00-00 ',S.Date,L.LockDate),IF(L.Locks=1,L.Date,CURDATE())) AS Days,L.Locks,L.LockDate,L.Date  
			FROM cg1_lockstock L
			LEFT JOIN yw1_ordersheet S ON S.POrderId=LEFT(L.StockId,12)   
			WHERE L.StockId=?",$StockId);
			// 
			if($lockResult->num_rows()>0){
				   $lockRow = $lockResult->row_array();
			       $L_Badge=$lockRow["Days"].'d';
			       $L_Locks=$lockRow["Locks"];
			       $L_unLockDate=$L_Locks==1?$lockRow["LockDate"]:"";
			       $L_Color=$L_Locks==1?2:1;
			       $L_Over=$L_Badge>3?1:0;
			       $ProcessArray[]=array("Title"=>"锁","Color"=>"$L_Color","Value"=>"","Badge"=>"$L_Badge","Over"=>"$L_Over");
			       
			       
			       }
			       {
			//3.采购时间
			$L_pos=count($ProcessArray);
			
			
			
			
			
			
			$cgsheetResult=$this->db->query("SELECT S.Mid,(S.AddQty+S.FactualQty) AS Qty,
			YEARWEEK(S.DeliveryDate,1) AS Weeks,
			S.OrderQty,
			YEARWEEK(CURDATE(),1) AS cWeeks,
			TIMESTAMPDIFF(DAY,IF(S.Mid>0,M.Date,S.ywOrderDTime),CURDATE()) AS Days,
			IF(S.Mid=0,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,CURDATE()),0) AS Hours,
			IF(S.Mid>0,M.Date,S.ywOrderDTime) AS xdDate  
			FROM cg1_stocksheet S
			LEFT JOIN cg1_stockmain M ON M.Id=S.Mid
			WHERE S.StockId=? ",$StockId);
			
			
			
			
			
			if($cgsheetResult->num_rows()>0){
			$cgsheetRow = $cgsheetResult->row_array();
			     $Mid=$cgsheetRow["Mid"];
			     $orinalQty = $cgsheetRow["OrderQty"];
			     $L_xdDate=$cgsheetRow["xdDate"];
			     $qtyAll = $cgsheetRow["Qty"];
			     $L_dWeeks=$cgsheetRow["Weeks"];
			     $L_cWeeks=$cgsheetRow["cWeeks"];
			     $L_Badge=$cgsheetRow["Days"].'d';
			     if ($Mid==0){ //未下采购单
			          $L_Over=$cgsheetRow["Hours"]>4 && $L_Locks==1?1:0;
			          
/*
			          if (count($ProcessArray) > 0 ) {
				          $ProcessArray[]=array("Title"=>"单","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			       $ProcessArray[]=array("Title"=>"到","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			       $ProcessArray[]=array("Title"=>"检","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			       $ProcessArray[]=array("Title"=>"入","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			          }
			          
*/
	
				        $ProcessArray[]=array("Title"=>"采","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"$L_Over");
				      $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");

			
			     }
			     else{ //已下采购单
			          $L_Qty=$cgsheetRow["Qty"]; //采购数量 
				     //检查送货单
				     
				      $dateResult=$this->db->query("  SELECT MAX(M.Date) AS shDate,MIN(M.created) AS kdDate,MAX(S.shDate) as arDate
			         ,MAX(B.created) as qcDate
			          FROM gys_shsheet S 
			LEFT JOIN gys_shmain M ON M.Id=S.Mid
			LEFT JOIN qc_badrecord B ON B.shMid=M.Id 
			WHERE S.StockId=? ",$StockId);
			 if($dateResult->num_rows()>0){
				     $gyssheetRow=$dateResult->row_array();
				      $new_shdDate = $gyssheetRow["kdDate"];
			         $new_arDate  = $gyssheetRow["arDate"];
			         $new_qcDate = $gyssheetRow["qcDate"];
			         
			      
			        if ($new_arDate && strlen($new_arDate)>0) {
				        $interval_d = (strtotime($new_arDate)-strtotime($new_shdDate))/3600;
				        if ($interval_d > 24) {
					        $interval_d = intval($interval_d/24).'d';
				        } else if ($interval_d > 1){
					        $interval_d = intval($interval_d).'h';
				        } else {
					        $interval_d =  round($interval_d,1).'h';
				        }
			        }
			         
			       
			      
			        if ($new_qcDate && strlen($new_qcDate)>0) {
				        $interval_qc = (strtotime($new_qcDate)-strtotime($new_arDate))/3600;
				        if ($interval_qc > 24) {
					        $interval_qc = intval($interval_qc/24).'d';
				         } else if ($interval_qc > 1){
					        $interval_qc = intval($interval_qc).'h';
				        } else {
					        $interval_qc = round($interval_qc,1).'h';
				        }
			        }
			         
				 }
				 
				 $L_rkQty = 0;
				    //检查入库记录
			         $rksheetResult=$this->db->query("SELECT SUM(S.Qty) AS rkQty,MAX(M.Date) AS rkDate,YEARWEEK(MAX(M.Date),1) AS rkWeeks 
			                                                                FROM ck1_rksheet S 
																			LEFT JOIN ck1_rkmain M ON M.Id=S.Mid
																			WHERE S.StockId=? ",$StockId);
					 if($rksheetResult->num_rows()>0){
					 $rksheetRow = $rksheetResult->row_array();
					          $L_rkQty=$rksheetRow["rkQty"];
					          
					          }
				     
			         $gyssheetResult=$this->db->query("  SELECT SUM(S.Qty) AS shQty,MAX(M.Date) AS shDate,SUM(IF(S.Estate<>1,S.Qty,0)) shQty2,SUM(IF(S.Estate=0,S.Qty,0)) shQty3
			          FROM gys_shsheet S 
			LEFT JOIN gys_shmain M ON M.Id=S.Mid
			WHERE S.StockId=? ",$StockId);
			         if($gyssheetResult->num_rows()>0){
			         $gyssheetRow=$gyssheetResult->row_array();
			         
			        
			         
			                   $L_shQty=$gyssheetRow["shQty"];
			                 if ($L_shQty>0){
				                 $L_Color=$L_Qty<=$L_shQty?2:1;
			                     
			                    // $testBg = ''; local people nice local colitem
			                
			                   $ProcessArray[]=array("Title"=>"单","Color"=>"$L_Color","Value"=>"$L_shQty","Badge"=>"$interval_d","Over"=>"");
			                     
			                      //检查送货到达确认
			                      $L_shQty2=$gyssheetRow["shQty2"];
			                      if ($L_shQty2>0){
				                         $L_Color=$L_Qty<=$L_shQty2?2:1;
				                         $ProcessArray[]=array("Title"=>"到","Color"=>"$L_Color","Value"=>"$L_shQty2","Badge"=>"","Over"=>"");
			                      }
			                      else{
				                         $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
			                      }
			                      
			                      //检查品检记录
			                      $L_shQty3=$gyssheetRow["shQty3"];
			                      if ($L_shQty3>0){
				                         $L_Color=$L_Qty==$L_shQty3?2:1;
				                         $ProcessArray[]=array("Title"=>"检","Color"=>"$L_Color","Value"=>"$L_shQty3","Badge"=>"$interval_qc","Over"=>"");
			                      }
			                       else{
				                         $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"$interval_qc","Over"=>"");
			                      }
			               }
			                else{
					            $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					            $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					            $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
			                }
			         }
			           
			         $L_Over=$L_cWeeks>$L_dWeeks?1:0; 
			         $L_Color=$L_Locks==1?1:0;
			         //检查入库记录
			       {
					          if ($L_rkQty>0){
					                 $L_Color=$L_Qty<=$L_rkQty?2:$L_Color;
						             $ProcessArray[]=array("Title"=>"入","Color"=>"$L_Color","Value"=>"$L_rkQty","Badge"=>"","Over"=>"");
						              $L_Badge=$L_Qty==$L_rkQty?$this->datehandler->geDifferDateTimeNum($L_xdDate,$rksheetRow["rkDate"],2).'d':$L_Badge;
						              if ($L_Qty==$L_rkQty){
							              $L_Over=$rksheetRow["rkWeeks"]>$L_dWeeks?1:0;
						              }
					          }
					          else{
						            $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					          }
					          // i 
					 }
					 $L_Color = '1';
				 	 $cgdateArray=array(); 
				 	   if ($L_Qty<=$L_rkQty) {
				                     $L_Color = "2";
				                     }
					 $qtyallVal = $qtyAll > 0 ? $qtyAll : '';
					 // qty all val qty all qty all all 
					 $fontcolor = $orinalQty < $qtyallVal ? '#FF0000' : '';
					 $cgdateArray[]=array("Title"=>"采","Color"=>"$L_Color","Value"=>"$qtyallVal","Badge"=>"$L_Badge","Over"=>"$L_Over",'font_color'=>"$fontcolor");
					  array_splice($ProcessArray,$L_pos,0,$cgdateArray);
					  						
			     }
			}
}
	
	   
   }
   
   return $ProcessArray;
   }


 public function cg_process_1($StockId,$StuffId) {
	   //
	   //采购订单状态显示  传入参数:StockId,StuffId

//1.检查开发状态
$ProcessArray=array();   $interval_d = '';   $interval_qc = '';
	 $this->load->library('dateHandler');
	 $AddRemark = $addOper = $addTime = "";
	 $qtyAll = 0;$orinalQty = 0;
$L_xdTime="";$L_Qty = "";
if ($StockId>0 && $StuffId>0){
         			//2.检查锁定状态
			$L_Locks=1;$L_unLockDate="";
			$lockResult=$this->db->query("SELECT TIMESTAMPDIFF(DAY,IF(L.LockDate='0000-00-00 ',S.Date,L.LockDate),IF(L.Locks=1,L.Date,CURDATE())) AS Days,L.Locks,L.LockDate,L.Date  
			FROM cg1_lockstock L
			LEFT JOIN yw1_ordersheet S ON S.POrderId=LEFT(L.StockId,12)   
			WHERE L.StockId=?",$StockId);
			// 
			if($lockResult->num_rows()>0){
				   $lockRow = $lockResult->row_array();
			       $L_Badge=$lockRow["Days"].'d';
			       $L_Locks=$lockRow["Locks"];
			       $L_unLockDate=$L_Locks==1?$lockRow["LockDate"]:"";
			       $L_Color=$L_Locks==1?2:1;
			       $L_Over=$L_Badge>3?1:0;
			       $ProcessArray[]=array("Title"=>"锁","Color"=>"$L_Color","Value"=>"","Badge"=>"$L_Badge","Over"=>"$L_Over");
			       
			       
			       }
			       {
			//3.采购时间
			$L_pos=count($ProcessArray);
			
			
			
			
			
			
			$cgsheetResult=$this->db->query("SELECT S.Mid,(S.AddQty+S.FactualQty) AS Qty,
			YEARWEEK(S.DeliveryDate,1) AS Weeks,
			S.OrderQty,
			YEARWEEK(CURDATE(),1) AS cWeeks,
			TIMESTAMPDIFF(DAY,IF(S.Mid>0,M.Date,S.ywOrderDTime),CURDATE()) AS Days,
			IF(S.Mid=0,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,CURDATE()),0) AS Hours,
			IF(S.Mid>0,M.Date,S.ywOrderDTime) AS xdDate  ,
			S.AddRemark ,SM.Name,date_format(S.ywOrderDTime,'%Y-%m-%d') addtime
			FROM cg1_stocksheet S
			LEFT JOIN cg1_stockmain M ON M.Id=S.Mid
			LEFT JOIN staffmain SM ON SM.Number=S.BuyerId
			WHERE S.StockId=? ",$StockId);
			
			
			
			
			
			if($cgsheetResult->num_rows()>0){
			$cgsheetRow = $cgsheetResult->row_array();
			     $Mid=$cgsheetRow["Mid"];
			     $orinalQty = $cgsheetRow["OrderQty"];
			     
			     $AddRemark = $cgsheetRow["AddRemark"];
			     $addTime = $cgsheetRow["addtime"];
			     $addOper = $cgsheetRow["Name"];
			     
			     $L_xdDate=$cgsheetRow["xdDate"];
			     $qtyAll = $cgsheetRow["Qty"];
			     $L_dWeeks=$cgsheetRow["Weeks"];
			     $L_cWeeks=$cgsheetRow["cWeeks"];
			     $L_Badge=$cgsheetRow["Days"].'d';
			     if ($Mid==0){ //未下采购单
			          $L_Over=$cgsheetRow["Hours"]>4 && $L_Locks==1?1:0;
			          
/*
			          if (count($ProcessArray) > 0 ) {
				          $ProcessArray[]=array("Title"=>"单","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			       $ProcessArray[]=array("Title"=>"到","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			       $ProcessArray[]=array("Title"=>"检","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			       $ProcessArray[]=array("Title"=>"入","Color"=>"","Value"=>"","Badge"=>"","Over"=>"$L_Over");
			          }
			          
*/
	
				        $ProcessArray[]=array("Title"=>"采","Color"=>"0","Value"=>"$orinalQty","Badge"=>"","Over"=>"$L_Over");
				      $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");

			
			     }
			     else{ //已下采购单
			          $L_Qty=$cgsheetRow["Qty"]; //采购数量 
				     //检查送货单
				     
				      $dateResult=$this->db->query("  SELECT MAX(M.Date) AS shDate,MIN(M.created) AS kdDate,MAX(S.shDate) as arDate
			         ,MAX(B.created) as qcDate
			          FROM gys_shsheet S 
			LEFT JOIN gys_shmain M ON M.Id=S.Mid
			LEFT JOIN qc_badrecord B ON B.shMid=M.Id 
			WHERE S.StockId=? ",$StockId);
			 if($dateResult->num_rows()>0){
				     $gyssheetRow=$dateResult->row_array();
				      $new_shdDate = $gyssheetRow["kdDate"];
			         $new_arDate  = $gyssheetRow["arDate"];
			         $new_qcDate = $gyssheetRow["qcDate"];
			         
			      
			        if ($new_arDate && strlen($new_arDate)>0) {
				        $interval_d = (strtotime($new_arDate)-strtotime($new_shdDate))/3600;
				        if ($interval_d > 24) {
					        $interval_d = intval($interval_d/24).'d';
				        } else if ($interval_d > 1){
					        $interval_d = intval($interval_d).'h';
				        } else {
					        $interval_d =  round($interval_d,1).'h';
				        }
			        }
			         
			       
			      
			        if ($new_qcDate && strlen($new_qcDate)>0) {
				        $interval_qc = (strtotime($new_qcDate)-strtotime($new_arDate))/3600;
				        if ($interval_qc > 24) {
					        $interval_qc = intval($interval_qc/24).'d';
				         } else if ($interval_qc > 1){
					        $interval_qc = intval($interval_qc).'h';
				        } else {
					        $interval_qc = round($interval_qc,1).'h';
				        }
			        }
			         
				 }
				 $L_rkQty = 0;
				  //检查入库记录
			         $rksheetResult=$this->db->query("SELECT SUM(S.Qty) AS rkQty,MAX(M.Date) AS rkDate,YEARWEEK(MAX(M.Date),1) AS rkWeeks 
			                                                                FROM ck1_rksheet S 
																			LEFT JOIN ck1_rkmain M ON M.Id=S.Mid
																			WHERE S.StockId=? ",$StockId);
					 if($rksheetResult->num_rows()>0){
					 $rksheetRow = $rksheetResult->row_array();
					          $L_rkQty=$rksheetRow["rkQty"];
					          
					          }
				     
				     
			         $gyssheetResult=$this->db->query("  SELECT SUM(S.Qty) AS shQty,MAX(M.Date) AS shDate,SUM(IF(S.Estate<>1,S.Qty,0)) shQty2,SUM(IF(S.Estate=0,S.Qty,0)) shQty3
			          FROM gys_shsheet S 
			LEFT JOIN gys_shmain M ON M.Id=S.Mid
			WHERE S.StockId=? ",$StockId);
			         if($gyssheetResult->num_rows()>0){
			         $gyssheetRow=$gyssheetResult->row_array();
			         
			        
			         
			                   $L_shQty=$gyssheetRow["shQty"];
			                 if ($L_shQty>0){
				                 $L_Color=$L_Qty<=$L_shQty?2:1;
			                     
			                    $ProcessArray[]=array("Title"=>"单","Color"=>"$L_Color","Value"=>"$L_shQty","Badge"=>"$interval_d","Over"=>"");
			                     
			                      //检查送货到达确认
			                      $L_shQty2=$gyssheetRow["shQty2"];
			                      if ($L_shQty2>0){
				                         $L_Color=$L_Qty<=$L_shQty2?2:1;
				                         $ProcessArray[]=array("Title"=>"到","Color"=>"$L_Color","Value"=>"$L_shQty2","Badge"=>"","Over"=>"");
			                      }
			                      else{
				                         $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
			                      }
			                      
			                      //检查品检记录
			                      $L_shQty3=$gyssheetRow["shQty3"];
			                      if ($L_shQty3>0){
				                         $L_Color=$L_Qty<=$L_shQty3?2:1;
				                         $ProcessArray[]=array("Title"=>"检","Color"=>"$L_Color","Value"=>"$L_shQty3","Badge"=>"$interval_qc","Over"=>"");
			                      }
			                       else{
				                         $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"$interval_qc","Over"=>"");
			                      }
			               }
			                else{
					            $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					            $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					            $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
			                }
			         }
			           
			         $L_Over=$L_cWeeks>$L_dWeeks?1:0; 
			         $L_Color=$L_Locks==1?1:0;
			         {
				
					          if ($L_rkQty>0){
					                 $L_Color=$L_Qty<=$L_rkQty?2:$L_Color;
						             $ProcessArray[]=array("Title"=>"入","Color"=>"$L_Color","Value"=>"$L_rkQty","Badge"=>"","Over"=>"");
						              $L_Badge=$L_Qty==$L_rkQty?$this->datehandler->geDifferDateTimeNum($L_xdDate,$rksheetRow["rkDate"],2).'d':$L_Badge;
						              if ($L_Qty==$L_rkQty){
							              $L_Over=$rksheetRow["rkWeeks"]>$L_dWeeks?1:0;
						              }
					          }
					          else{
						            $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					          }
					          // i 
					 }
				 	 $cgdateArray=array(); 
				 	 
					 $qtyallVal = $qtyAll > 0 ? $qtyAll : '';
					 $L_Color = "1";
  if ($L_Qty<=$L_rkQty) {
				                     $L_Color = "2";
				                     }
			                     
					 $fontcolor = $orinalQty < $qtyallVal ? '4' : $L_Color;
					 
					 $addedShownDict = array("no"=>"no");
					 if ($fontcolor=='4') {
						 
						 $checkName = $this->db->query(" select M.Name 
						 from cg1_stocksheet_log 
						 L left join staffmain M on M.Number=L.Operator
						 where L.Opcode=3 and L.StockId=$StockId order by L.Id desc limit 1 ");
						 
						 if ($checkName->num_rows() > 0) {
							 $checkNameRow = $checkName->row_array();
							 $addOper = $checkNameRow["Name"];
						 }
						 $AddRemark = $AddRemark == "" ? "因订单拆分产生的增购。":$AddRemark;
						 
						 $addedShownDict = array("txt"=>"增购原因：\n". $AddRemark,'oper'=>$addOper,'time'=>$addTime);
					 }
					 $cgdateArray[]=array("Title"=>"采","Color"=>"$fontcolor","Value"=>"$qtyallVal","Badge"=>"$L_Badge","Over"=>"",'font_color'=>"",'added'=>$addedShownDict);
					  array_splice($ProcessArray,$L_pos,0,$cgdateArray);
					  						
			     }
			}
}
	
	   
   }
   
   return $ProcessArray;
   }


   public function cg_process_2($StockId,$StuffId, $Decimals='') {
	   //
	   //采购订单状态显示  传入参数:StockId,StuffId

//1.检查开发状态
$ProcessArray=array();   $interval_d = '';   $interval_qc = '';
	 $this->load->library('dateHandler');
	 $AddRemark = $addOper = $addTime = "";
	 $qtyAll = 0;$orinalQty = 0;
$L_xdTime="";$L_Qty = "";
if ($StockId>0 && $StuffId>0){
	
	if ($Decimals=='') {
		$this->load->model('StuffDataModel');
		$StuffIdRow = $this->StuffDataModel->get_records($StuffId);
		$Decimals = element('Decimals', $StuffIdRow, 0);
	}
			
	
	
	
			$cgmainResult=$this->db->query("SELECT Id FROM cg1_stocksheet WHERE StuffId='$StuffId'  AND Mid>0 LIMIT 1");
			          if($cgmainResult->num_rows()<=0){
							$developResult=$this->db->query("SELECT D.GroupId,D.Estate,D.finishdate,TIMESTAMPDIFF(DAY,D.Date,IF(D.Estate=0,D.finishdate,CURDATE())) AS Days,IF(YEARWEEK(Targetdate,1)<YEARWEEK(IF(D.Estate=0,D.finishdate,CURDATE()),1),1,0) AS Over FROM stuffdevelop  D WHERE D.StuffId='$StuffId'");
							if($developResult->num_rows()>0){
								$developRow = $developResult->row_array();
							       switch($developRow["GroupId"]){
								       case 102:  $L_Value="图";  break;
								       case 502:  $L_Value="B";       break;
								       case 503:  $L_Value="C";       break;
								        default:  $L_Value="A";       break;
							       }
							       $L_Color=$developRow["Estate"]==0?2:1;
							       $L_Badge=$developRow["Days"];
							       $L_Over=$developRow["Over"];
							        $ProcessArray[]=array("Title"=>"开","Color"=>"$L_Color","Value"=>''.round($L_Value, $Decimals),"Badge"=>"$L_Badge","Over"=>"$L_Over");
							       $L_xdTime=$developRow["Estate"]==0?date("m/d H:i",strtotime($developRow["finishdate"])):$L_xdTime;
							}
					 }
         			//2.检查锁定状态
			$L_Locks=1;$L_unLockDate="";
			$lockResult=$this->db->query("SELECT TIMESTAMPDIFF(DAY,IF(L.LockDate='0000-00-00 ',S.Date,L.LockDate),IF(L.Locks=1,L.Date,CURDATE())) AS Days,L.Locks,L.LockDate,L.Date  
			FROM cg1_lockstock L
			LEFT JOIN yw1_ordersheet S ON S.POrderId=LEFT(L.StockId,12)   
			WHERE L.StockId=?",$StockId);
			// 
			if($lockResult->num_rows()>0){
				   $lockRow = $lockResult->row_array();
			       $L_Badge=$lockRow["Days"].'d';
			       $L_Locks=$lockRow["Locks"];
			       $L_unLockDate=$L_Locks==1?$lockRow["LockDate"]:"";
			       $L_Color=$L_Locks==1?2:1;
			       $L_Over=$L_Badge>3?1:0;
			       $ProcessArray[]=array("Title"=>"锁","Color"=>"$L_Color","Value"=>"","Badge"=>"$L_Badge","Over"=>"$L_Over");
			       
			       
			       }
			       {
			//3.采购时间
			$L_pos=count($ProcessArray);
			
			
			
			
			
			
			$cgsheetResult=$this->db->query("SELECT S.Mid,(S.AddQty+S.FactualQty) AS Qty,
			YEARWEEK(S.DeliveryDate,1) AS Weeks,
			S.OrderQty,
			YEARWEEK(CURDATE(),1) AS cWeeks,
			TIMESTAMPDIFF(DAY,IF(S.Mid>0,M.Date,S.ywOrderDTime),CURDATE()) AS Days,
			IF(S.Mid=0,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,CURDATE()),0) AS Hours,
			IF(S.Mid>0,M.Date,S.ywOrderDTime) AS xdDate  ,
			S.AddRemark ,SM.Name,date_format(S.ywOrderDTime,'%Y-%m-%d') addtime
			FROM cg1_stocksheet S
			LEFT JOIN cg1_stockmain M ON M.Id=S.Mid
			LEFT JOIN staffmain SM ON SM.Number=S.BuyerId
			WHERE S.StockId=? ",$StockId);
			
			
			
			
			
			if($cgsheetResult->num_rows()>0){
			$cgsheetRow = $cgsheetResult->row_array();
			     $Mid=$cgsheetRow["Mid"];
			     $orinalQty = $cgsheetRow["OrderQty"];
			     
			     $AddRemark = $cgsheetRow["AddRemark"];
			     $addTime = $cgsheetRow["addtime"];
			     $addOper = $cgsheetRow["Name"];
			     
			     $L_xdDate=$cgsheetRow["xdDate"];
			     $qtyAll = $cgsheetRow["Qty"];
			     $L_dWeeks=$cgsheetRow["Weeks"];
			     $L_cWeeks=$cgsheetRow["cWeeks"];
			     $L_Badge=$cgsheetRow["Days"].'d';
			     if ($Mid==0){ //未下采购单
			          $L_Over=$cgsheetRow["Hours"]>4 && $L_Locks==1?1:0;
			          

				        $ProcessArray[]=array("Title"=>"采","Color"=>"0","Value"=>"".round($orinalQty, $Decimals),"Badge"=>"","Over"=>"$L_Over");
				      $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					  $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");

			
			     }
			     else{ //已下采购单
			          $L_Qty=$cgsheetRow["Qty"]; //采购数量 
				     //检查送货单
				     
				      $dateResult=$this->db->query("  SELECT MAX(M.Date) AS shDate,MIN(M.created) AS kdDate,MAX(S.shDate) as arDate
			         ,MAX(B.created) as qcDate
			          FROM gys_shsheet S 
			LEFT JOIN gys_shmain M ON M.Id=S.Mid
			LEFT JOIN qc_badrecord B ON B.shMid=M.Id 
			WHERE S.StockId=? ",$StockId);
			 if($dateResult->num_rows()>0){
				     $gyssheetRow=$dateResult->row_array();
				      $new_shdDate = $gyssheetRow["kdDate"];
			         $new_arDate  = $gyssheetRow["arDate"];
			         $new_qcDate = $gyssheetRow["qcDate"];
			         
			      
			        if ($new_arDate && strlen($new_arDate)>0) {
				        $interval_d = (strtotime($new_arDate)-strtotime($new_shdDate))/3600;
				        if ($interval_d > 24) {
					        $interval_d = intval($interval_d/24).'d';
				        } else if ($interval_d > 1){
					        $interval_d = intval($interval_d).'h';
				        } else {
					        $interval_d =  round($interval_d,1).'h';
				        }
			        }
			         
			       
			      
			        if ($new_qcDate && strlen($new_qcDate)>0) {
				        $interval_qc = (strtotime($new_qcDate)-strtotime($new_arDate))/3600;
				        if ($interval_qc > 24) {
					        $interval_qc = intval($interval_qc/24).'d';
				         } else if ($interval_qc > 1){
					        $interval_qc = intval($interval_qc).'h';
				        } else {
					        $interval_qc = round($interval_qc,1).'h';
				        }
			        }
			         
				 }
				 $L_rkQty = 0;
				  //检查入库记录
			         $rksheetResult=$this->db->query("SELECT SUM(S.Qty) AS rkQty,MAX(M.Date) AS rkDate,YEARWEEK(MAX(M.Date),1) AS rkWeeks 
			                                                                FROM ck1_rksheet S 
																			LEFT JOIN ck1_rkmain M ON M.Id=S.Mid
																			WHERE S.StockId=? ",$StockId);
					 if($rksheetResult->num_rows()>0){
					 $rksheetRow = $rksheetResult->row_array();
					          $L_rkQty=$rksheetRow["rkQty"];
					          
					          }
				     
				     
			         $gyssheetResult=$this->db->query("  SELECT SUM(S.Qty) AS shQty,MAX(M.Date) AS shDate,SUM(IF(S.Estate<>1,S.Qty,0)) shQty2,SUM(IF(S.Estate=0,S.Qty,0)) shQty3
			          FROM gys_shsheet S 
			LEFT JOIN gys_shmain M ON M.Id=S.Mid
			WHERE S.StockId=? ",$StockId);
			         if($gyssheetResult->num_rows()>0){
			         $gyssheetRow=$gyssheetResult->row_array();
			         
			        
			         
			                   $L_shQty=$gyssheetRow["shQty"];
			                 if ($L_shQty>0){
				                 $L_Color=$L_Qty<=$L_shQty?2:1;
			                     
			                    $ProcessArray[]=array("Title"=>"单","Color"=>"$L_Color","Value"=>"".round($L_shQty, $Decimals),"Badge"=>"$interval_d","Over"=>"");
			                     
			                      //检查送货到达确认
			                      $L_shQty2=$gyssheetRow["shQty2"];
			                      if ($L_shQty2>0){
				                         $L_Color=$L_Qty<=$L_shQty2?2:1;
				                         $ProcessArray[]=array("Title"=>"到","Color"=>"$L_Color","Value"=>"".round($L_shQty2, $Decimals),"Badge"=>"","Over"=>"");
			                      }
			                      else{
				                         $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
			                      }
			                      
			                      //检查品检记录
			                      $L_shQty3=$gyssheetRow["shQty3"];
			                      if ($L_shQty3>0){
				                         $L_Color=$L_Qty<=$L_shQty3?2:1;
				                         $ProcessArray[]=array("Title"=>"检","Color"=>"$L_Color","Value"=>"".round($L_shQty3, $Decimals),"Badge"=>"$interval_qc","Over"=>"");
			                      }
			                       else{
				                         $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"$interval_qc","Over"=>"");
			                      }
			               }
			                else{
					            $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					            $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					            $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
			                }
			         }
			           
			         $L_Over=$L_cWeeks>$L_dWeeks?1:0; 
			         $L_Color=$L_Locks==1?1:0;
			         {
				
					          if ($L_rkQty>0){
					                 $L_Color=$L_Qty<=$L_rkQty?2:$L_Color;
						             $ProcessArray[]=array("Title"=>"入","Color"=>"$L_Color","Value"=>"".round($L_rkQty, $Decimals),"Badge"=>"","Over"=>"");
						              $L_Badge=$L_Qty==$L_rkQty?$this->datehandler->geDifferDateTimeNum($L_xdDate,$rksheetRow["rkDate"],2).'d':$L_Badge;
						              if ($L_Qty==$L_rkQty){
							              $L_Over=$rksheetRow["rkWeeks"]>$L_dWeeks?1:0;
						              }
					          }
					          else{
						            $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
					          }
					          // i 
					 }
				 	 $cgdateArray=array(); 
				 	 
					 $qtyallVal = $qtyAll > 0 ? $qtyAll : '';
					 $L_Color = "1";
  if ($L_Qty<=$L_rkQty) {
				                     $L_Color = "2";
				                     }
			                     
					 $fontcolor = $orinalQty < $qtyallVal ? '4' : $L_Color;
					 
					 $addedShownDict = array("no"=>"no");
					 if ($fontcolor=='4') {
						 
						 $checkName = $this->db->query(" select M.Name 
						 from cg1_stocksheet_log 
						 L left join staffmain M on M.Number=L.Operator
						 where L.Opcode=3 and L.StockId=$StockId order by L.Id desc limit 1 ");
						 
						 if ($checkName->num_rows() > 0) {
							 $checkNameRow = $checkName->row_array();
							 $addOper = $checkNameRow["Name"];
						 }
						 $AddRemark = $AddRemark == "" ? "因订单数量减少产生的增购。":$AddRemark;
						 
						 $addedShownDict = array("txt"=>"增购原因：\n". $AddRemark,'oper'=>$addOper,'time'=>$addTime);
					 }
					 $cgdateArray[]=array("Title"=>"采","Color"=>"$fontcolor","Value"=>"".round($qtyallVal, $Decimals),"Badge"=>"$L_Badge","Over"=>"",'font_color'=>"",'added'=>$addedShownDict);
					  array_splice($ProcessArray,$L_pos,0,$cgdateArray);
					  						
			     }
			}
}
	
	   
   }
   
   return array('l_xdtime'=>$L_xdTime, 'process'=>$ProcessArray);
   }

   
   //外发备料
   public function get_wfbl_all() {
	   
	   //$DataPublic = $this->DataPublic;
	   $DataOu = $this->DataOu;
	   $sql = "select sum(S.Qty) Qty,
	   count(1) Nums,
	   sum(IF(YEARWEEK(S.Date,1)<YEARWEEK(CURDATE(),1),S.Qty,0)) AS OverQty 
	   from ( 
	   		SELECT (OG.AddQty+OG.FactualQty) AS Qty,
	   		OM.Date
	   		FROM  fits_sheet A 
	   		LEFT JOIN  cg1_stocksheet G ON A.StockId=G.StockId 
	   		LEFT JOIN  yw1_ordersheet Y ON Y.POrderId=A.POrderId 
	   		LEFT JOIN  $DataOu.cg1_stocksheet OG ON OG.StockId=Y.OrderNumber 
	   		LEFT JOIN  $DataOu.cg1_stockmain OM ON OM.Id=OG.Mid 
	   		LEFT JOIN  $DataOu.stuffdata OD ON OD.StuffId=OG.StuffId   
	   		LEFT JOIN  stuffdata D  ON D.StuffId=G.StuffId 
	   		where 1  AND (G.AddQty+G.FactualQty)>0  and Y.Estate>0 and G.Mid=0
	   		AND NOT EXISTS
	   			(SELECT E.Type FROM  yw2_orderexpress E 
	   			WHERE E.POrderId=Y.POrderId AND  E.Type='2')
	   		GROUP BY A.StockId
			) S";
	   
	   return $this->db->query($sql);
	   
   }
   
   public function get_types() {
	   
	   $sql = "
SELECT  S.TypeId,S.TypeName,SUM(IFNULL(M.Estate,0)) AS Nums  
                 FROM producttype S  
                 LEFT JOIN staffgroup G  ON G.scType=S.TypeId  
                 LEFT JOIN staffmain M ON M.GroupId=G.GroupId AND M.Estate=1  AND M.cSign='3' 
      WHERE S.Estate=1 AND S.TypeId<>'8064' 
      AND NOT EXISTS(SELECT Number FROM kqqjsheet K WHERE K.Number=M.Number  AND K.StartDate<=CURRENT_DATE AND K.EndDate>=CURRENT_DATE)  
       GROUP BY S.TypeId ORDER BY S.SortId";
       return $this->db->query($sql);
   }
   
  public function kl_limit_days(){
	 return  date('w')>=4?14:7;
}
   
   public function kltj_date($eDate) {
	   /*
		   SELECT SUM(Qty) AS Qty FROM sc5_kltj  WHERE Date='$eDate'
	   */
	   $eDate = $eDate==""?date('Y-m-d'):$eDate;
	   $sql = "SELECT SUM(Qty) AS Qty FROM sc5_kltj  WHERE Date=?";
	   return $this->db->query($sql,$eDate);
   }
   
   public function check_cg_locks($StockId='-1') {
   	$sql = "SELECT Locks FROM cg1_lockstock WHERE StockId=? AND Locks=? LIMIT 1";
   	return $this->db->query($sql,array($StockId,0));
   }
   
   public function get_llqty($StockId,$mStuffId) {
	   $sql = "SELECT IFNULL(SUM(S.Qty),0) AS llQty FROM ck5_klsheet  K LEFT JOIN  ck5_llsheet S ON S.Id=K.Sid  WHERE  K.StockId=? AND S.StuffId=?";
	   return $this->db->query($sql,array($StockId,$mStuffId));
   }
   //开料备料 开料加工  
   public function get_klbl_kljg() {
	   
	    $curDate=date("Y-m-d");
	    $addDays = $this->kl_limit_days();
        $nextWeekDate=date("Y-m-d",strtotime("$curDate  +$addDays  day"));
        $sql = "SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek,YEARWEEK('$curDate',1) AS CurWeek";
        $query = $this->db->query($sql);
        $SearchRows = '';
        $nextWeek = 0;
        $curWeek = 0;
        if ($query->num_rows() > 0) {
	        
	        $row = $query->row();
	        
	       $nextWeek= $row->NextWeek;
	       $curWeek = $row->CurWeek;
        $SearchRows=" AND  S.Weeks<='$nextWeek'  AND S.Weeks>0 ";
        }
        
        $klblCounts = array();
        $klCounts = array();
         $klblQty=array(); $klblOverQty=array();$klQtys=array(); $klOverQtys=array();
        $query = $this->get_types();
        $TypeIdArray = array();$TypeNameArray=array();
        if ($query->num_rows()>0) {
	        foreach($query->result() as $row) {
		        $TypeId = $row->TypeId;
		        $TypeName = $row->TypeName;
		        array_push($TypeIdArray, $TypeId);
				$TypeNameArray[$TypeId]=$TypeName; 
				
			    $klblQtys[$TypeId]=0; 
			    $klblCounts[$TypeId] = 0;
			    $klCounts[$TypeId] = 0;
		        $klblOverQtys[$TypeId]=0; 
		        $klQtys[$TypeId]=0; 
		        $klOverQtys[$TypeId]=0;
	        }
	        
        }
        
	   //$sql select k.* typeid ck.stock
	   $sql = "SELECT K.*,P.TypeId,CK.tStockQty,D.Unit FROM (
			         SELECT S.POrderId,S.ProductId,S.Qty,S.NewSign,S.Weeks,A.StuffId,A.mStuffId,G.StockId,
			         ROUND((G.AddQty+G.FactualQty)*(IFNULL(substring_index(A.Relation,'/',1),A.Relation)/IFNULL(substring_index(A.Relation,'/',-1),1)),1) AS OrderQty,(G.AddQty+G.FactualQty) as cutQty,IFNULL(SUM(T.Qty),0) as cutedQty   
			        FROM   (SELECT Y.POrderId,Y.Qty,Y.ProductId,Y.NewSign,YEARWEEK(OG.DeliveryDate,1)  AS Weeks
			                       FROM  yw1_ordersheet Y
			                      LEFT JOIN cg1_stocksheet OG  ON Y.OrderNumber=OG.StockId  
			                       WHERE Y.Estate=1 AND Y.scFrom>0  AND Y.NewSign=1 
			                       AND EXISTS (SELECT S.POrderId FROM  slice_sheet S WHERE S.POrderId=Y.POrderId)
			                        AND NOT EXISTS (SELECT E.POrderId FROM  yw2_orderexpress E WHERE E.POrderId=Y.POrderId AND E.Type=2)
			                   ) S   
			        LEFT JOIN  slice_sheet A ON A.POrderId=S.POrderId  
			        LEFT JOIN  cut_data C ON C.Id=A.CutId 
			        LEFT JOIN  sc5_kltj T ON T.StockId=A.StockId 
			        LEFT JOIN  cg1_stocksheet G ON G.StockId=A.StockId 
			        LEFT JOIN  slice_drawing P ON  P.StuffId=A.StuffId AND P.CutId=A.CutId 
			       WHERE P.Picture IS NOT NULL  $SearchRows  GROUP BY A.StockId,A.CutId 
		      )K 
	       LEFT JOIN  productdata P ON  P.ProductId=K.ProductId  
	       LEFT JOIN  ck9_stocksheet CK ON CK.StuffId=K.mStuffId 
	       LEFT JOIN  stuffdata D ON D.StuffId=K.mStuffId  
		   WHERE K.cutQty>K.cutedQty  GROUP BY StockId ORDER BY POrderId,TypeId;";
		    $query = $this->db->query($sql);
		    
		    foreach ($query->result_array() as $AbleKlRow) {
			      $StockId=$AbleKlRow["StockId"];
			      $TypeId=$AbleKlRow["TypeId"];
			      $cutQty=$AbleKlRow["cutQty"];
			      $cutedQty=$AbleKlRow["cutedQty"];
			      $OrderQty=$AbleKlRow["OrderQty"];
			      $tStockQty=$AbleKlRow["tStockQty"];
			      $POrderId=$AbleKlRow["POrderId"];
			      $Weeks=$AbleKlRow["Weeks"];
			      
			      $lockQuery = $this->check_cg_locks($StockId);
			      if ($lockQuery->num_rows()>0) {
				      continue;
			      }
			      $NewSign=$AbleKlRow["NewSign"];
			      //检查是否已备料
			     $mStuffId=$AbleKlRow["mStuffId"];
			     
			     $llQuery = $this->get_llqty($StockId,$mStuffId);
			     $llQty = 0;
			     if ($llQuery->num_rows()>0) {
				     $llRow = $llQuery->row();
				     $llQty = $llRow->llQty;
				     
			     }
			     $sb = $OrderQty-$llQty;
				 if ($sb >= -0.2 && $sb <= 0.2){ //可开料
					$klQty=$cutQty-$cutedQty;
					// if ($TypeId=="8058") echo "$StockId :  $klQty <br>";
					$klQtys[$TypeId]+=($klQty);
					$klCounts[$TypeId] ++;
					if($Weeks<$curWeek){
						$klOverQtys[$TypeId]+=$klQty;
					}
				}
				{
					     //可备料 
						 if ($sb != 0 && $tStockQty>=($OrderQty-$llQty) && $OrderQty-$llQty>=0.2){
						      $klQty=$cutQty-$cutedQty;
						      //if ($LoginNumber==10868) echo $POrderId;
						      if($Weeks<$curWeek){
						              $klblOverQtys[$TypeId]+=$klQty;
						     }
						     $klblQtys[$TypeId]+=$klQty;
						     $klblCounts[$TypeId]++;
	                        
						 }//end if 可备料
				}//end else
	    }//end while
	    
	    /*
		    
	    */
	    $rowHeight = 45; $sWidth = 0.5;
	    $counts = count($TypeIdArray);
	   $RLColor = "#86898A";
	     $dataArray=array();
	 $kldataArray=array();
	  for($i=0;$i<$counts;$i++){
                   $TypeId=$TypeIdArray[$i];
                   $TypeName=$TypeNameArray[$TypeId];  
                   $tempCount = $klblCounts[$TypeId];
                   $klblOverQty=$klblOverQtys[$TypeId]==0?"":number_format($klblOverQtys[$TypeId]);
                   $klblQty=$klblQtys[$TypeId]==""?0:number_format($klblQtys[$TypeId]);
	                $sWidth=0.5;
                      $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2120|$TypeId",
			             "onTap"=>array("Title"=>"开料备料/$TypeName",
			             				"Value"=>"1",
			             				"Tag"=>"Slice",
			             				"Args"=>""),
			             "RowSet"=>array("Separator"=>"$sWidth",
			             				 "Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"$TypeName",
			             				"Align"=>"L"),
			             "Col_B"=>array("Title"=>"$klblOverQty",
			             				"Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$klblQty" . "",
			             				"Align"=>"R",
			             				"RLText"=>"($tempCount)",
			             				"RLColor"=>"$RLColor")
			          ); 
			          
			       $klOverQty=$klOverQtys[$TypeId]==0?"":number_format($klOverQtys[$TypeId]);
                   $klQty=$klQtys[$TypeId]==""?0:number_format($klQtys[$TypeId]);
                   
                   $tempCount =$klCounts[$TypeId];
			          $kldataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2121|$TypeId",
			             "onTap"=>array("Title"=>"开料加工/$TypeName",
			             				"Value"=>"1",
			             				"Tag"=>"Slice",
			             				"Args"=>""),
			             "RowSet"=>array("Separator"=>"$sWidth",
			             				 "Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"$TypeName",
			             				"Align"=>"L"),
			             "Col_B"=>array("Title"=>"$klOverQty",
			             				"Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$klQty" . "",
			             				"Align"=>"R",
						 				"RLText"=>"($tempCount)",
						 				"RLColor"=>"$RLColor")
			          );           
       }
  
     
      
   //5天平均生产数量
   $yDate=date("Y-m-d",strtotime("-1 day"));
   $k=0;$n=0;$TotalcutQty=0;
	do{
		   $eDate=date("Y-m-d",strtotime("$yDate  -$n   day"));
		    //判断当天是否有登记生产数量
		    $kltj_query = $this->kltj_date($eDate);
		    $kltj_qty = 0;
		    if ($kltj_query->num_rows() > 0) {
			    $kltj_row = $kltj_query->row();
			    $kltj_qty = $kltj_row->Qty;
			    if ($kltj_qty > 0) {
				   $k++;$TotalcutQty+= $kltj_qty;
			    }
		    }
		   
	   $n++; if ($n>30) break;
	}while($k<5);
	$avgKlQty=number_format(round($TotalcutQty/5));
	
   //当天开料数量
   $kltj_query = $this->kltj_date('');
   $kltjQty = 0;
    if ($kltj_query->num_rows() > 0) {
			  $kltj_row = $kltj_query->row();
			  $kltjQty =  $kltj_row->Qty; 
	}
	
		$this->load->library('dateHandler');
		$worktimes = $this->datehandler->get_worktimes();
   $workTimes = $worktimes[0];
      
    $kldataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2122",
			             "onTap"=>array("Title"=>"今日开料","Value"=>"1","Tag"=>"Slice","Args"=>""),
			             "RowSet"=>array("Separator"=>"2","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"今日开料","Align"=>"L","TopRight"=>"$workTimes"),
			             "Col_B"=>array("Title"=>"$avgKlQty"),
			             "Col_C"=>array("Title"=>"$kltjQty" . "pcs","Align"=>"R")
			          );           
			          return array($dataArray,$kldataArray);

   }
	 
}