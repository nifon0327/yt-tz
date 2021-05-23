<?php
header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=$Id.pdf"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
$ShipSign=$CheckSign["Sign"];
$ShipType=$CheckSign["ShipType"];	

$FromFunPage='_tooldpdf';
if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note		
	  include "ch_creditnote_topdf.php";
	}
else{
	  //include "ch_shippinglist_toinvoice.php";
	  include "ch0_shippinglistOut_toinvoice.php";
	  
	}

?>