<?php 
include "../basic/parameter.inc";
$Amount=0;
if ($Ids!=''){
	$checkSql=mysql_query("SELECT SUM(Qty*Price) AS Amount FROM nonbom6_cgsheet WHERE Mid IN($Ids)",$link_id);
	if ($checkRow = mysql_fetch_array($checkSql)) {
			$Amount=round($checkRow["Amount"],2);
	} 
}
echo $Amount;
?>
