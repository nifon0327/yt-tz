<?php 
//订单利润
$CheckProfitSign=0;
switch($ReadProfitSign){
	case "SAVE":
	   $inRecode="INSERT INTO $DataIn.yw1_orderprofit (Id,POrderId,Profit,Percent,Date,Operator) VALUES(NULL,'$POrderId','$profitRMB2','$profitRMB2PC',CURDATE(),'$Login_P_Number') ON DUPLICATE KEY  UPDATE Profit='$profitRMB2',Percent='$profitRMB2PC' ";
	    $inAction=@mysql_query($inRecode);
	   break;
	default:
	   $checkPorfitResult=mysql_query("SELECT Profit,Percent FROM $DataIn.yw1_orderprofit  WHERE POrderId='$POrderId' LIMIT 1",$link_id);
	   if($checkPorfitRow = mysql_fetch_array($checkPorfitResult)){
	           $profitRMB2=$checkPorfitRow["Profit"];
	           $profitRMB2PC=$checkPorfitRow["Percent"];
	           $CheckProfitSign=1;
	   }
	   else{
		      $profitRMB2=0;
		      $profitRMB2PC=0;
	   }
	  break;
}
?>