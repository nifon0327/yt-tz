<?php 
//$DataIn.电信---yang 20120801
$checkExpress=mysql_query("SELECT Type FROM $DataIn.cg2_orderexpress WHERE StockId='$StockId' ORDER BY Id",$link_id);

if($checkExpress && ($checkExpressRow = mysql_fetch_array($checkExpress))){
	$theDefaultColor="#FFA6D2";//加急采购单
	}
//if ($TypeId=='9104') $theDefaultColor="#FFFF00";  //客户退款 
?>