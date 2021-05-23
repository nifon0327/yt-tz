<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

   $CheckTaxRow  = mysql_fetch_array(mysql_query("SELECT InvoiceTax FROM $DataIn.providersheet WHERE CompanyId = '$CompanyId'",$link_id));
   
   $InvoiceTax = $CheckTaxRow["InvoiceTax"]==""?0.00:$CheckTaxRow["InvoiceTax"];
   
   echo $InvoiceTax;
?>