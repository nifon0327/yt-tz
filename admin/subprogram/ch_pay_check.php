<?php 
	//未收货款检查
	$checkPayment=mysql_query("SELECT A.PayMode,B.Name AS PayTerm,B.Keys FROM $DataIn.trade_object A
	         LEFT JOIN $DataPublic.clientpaymode B ON B.Id=A.PayMode
			WHERE A.CompanyId='$CompanyId' LIMIT 1",$link_id);
     if($checkPaymentRow = mysql_fetch_array($checkPayment)){
            $PayMode=$checkPaymentRow["PayMode"];
            $PayKeys=$checkPaymentRow["Keys"]*1;
            $PayTerm=$checkPaymentRow["PayTerm"];
     }
     switch($PayMode){
	     case 1://出货前100%付款
	     case 11:
	         $SearchDays="";
	        break;
	    case 2://出货后收到B/L15天付款
	    case 9://出货后7天付款
	    case 12:
	    case 13:
	    case 15:
	    case 16:
	    case 17:
	    case 18:
	       $PayKeys+=10; //延长10天。英姿要求 2016/07/20
	         $SearchDays=" AND  TIMESTAMPDIFF(DAY,M.Date,Now())>$PayKeys ";
	        break;
	    case  3://出货后付款,每月10号上上月货款须到帐
	         $curDate=date("Y-m-d");
	         $day=date("d");
	         $month=$day>10?date("Y-m",strtotime( "-2 month")):date("Y-m",strtotime( "-3 month"));
	         $SearchDays=" AND  DATE_FORMAT(M.Date,'%Y-%m')<='$month' ";
	        break;
	  case 4:// 出货后付款,每月10号上月货款须到帐
	         $curDate=date("Y-m-d");
	         $day=date("d");
	         $month=$day>10?date("Y-m",strtotime( "-1 month")):date("Y-m",strtotime( "-2 month"));
	         $SearchDays=" AND  DATE_FORMAT(M.Date,'%Y-%m')<='$month' ";
	       break;
	  case 5://出货后30天付款
	         $month=date("Y-m",strtotime( "-2 month"));
	         $SearchDays=" AND  DATE_FORMAT(M.Date,'%Y-%m')<='$month' ";
	       break;
	  case 6://出货后付款,2000USD一结
	  case 7://出货后付款,20000USD一结
	      break;
	  case 8:// 出货后6个月付款
	          $month=date("Y-m",strtotime( "-7 month"));
	          $SearchDays=" AND  DATE_FORMAT(M.Date,'%Y-%m')<='$month' ";
	      break;
	      
     }

$PaySign=1;	
switch($funFrom){
	case "ch_shippinglist": 
			$ShipSql="SELECT SUM(Amount) AS Amount FROM (
				        SELECT SUM( S.Price*S.Qty*M.Sign) AS Amount 
			            FROM $DataIn.ch1_shipmain M
			            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
			            WHERE M.Estate =0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' $SearchDays
			          UNION 
			             SELECT IFNULL(SUM(-Amount),0) Amount  FROM $DataIn.cw6_advancesreceived 
			             WHERE CompanyId='$CompanyId' AND Mid='0'   
		            )A ";
		           // echo $ShipSql;
	break;
	case "cw_orderin":
	      $ShipSql="SELECT SUM(Amount) AS Amount FROM (
				        SELECT SUM( S.Price*S.Qty*M.Sign) AS Amount 
			            FROM $DataIn.ch1_shipmain M
			            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
			            WHERE M.Id='$Id' $SearchDays
			          UNION 
			             SELECT IFNULL(SUM(-Amount),0) Amount FROM $DataIn.cw6_advancesreceived 
			             WHERE CompanyId='$CompanyId' AND Mid='0'   
		            )A ";
	break;
}
$ShipResult = mysql_query($ShipSql,$link_id);
if($ShipRow = mysql_fetch_array($ShipResult)) {
     $noPayAmount=$ShipRow["Amount"]*1;
      switch($PayMode){
          case 6:
          case 7:
              if ($noPayAmount>=$PayKeys) $PaySign=0;
              break;
         default:
            if ($noPayAmount>0) $PaySign=0;
            break;
      }
}

?>