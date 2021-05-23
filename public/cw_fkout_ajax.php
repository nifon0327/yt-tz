<?php 
include "../basic/parameter.inc";
$Amount=0;
if ($Ids!=''){
	$checkSql=mysql_query("SELECT SUM(Amount) AS Amount FROM cw1_fkoutsheet WHERE Id IN($Ids)",$link_id);
	if ($checkRow = mysql_fetch_array($checkSql)) {
			$Amount=round($checkRow["Amount"],2);
	} 
}
echo $Amount;
?>
