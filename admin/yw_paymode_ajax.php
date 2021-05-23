<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
 $Paymentterm="";
if ($CompanyId!=""){
	  $checkPayment=mysql_query("SELECT B.eName FROM $DataIn.trade_object A LEFT JOIN $DataPublic.clientpaymode B ON B.Id=A.PayMode WHERE A.CompanyId='$CompanyId' LIMIT 1",$link_id);
     if($checkPaymentRow = mysql_fetch_array($checkPayment)){
           $Paymentterm=$checkPaymentRow["eName"];
     }
}
echo $Paymentterm;
?>