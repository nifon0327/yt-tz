<?php 
//电信-EWEN
include "../model/modelhead.php";
$mySql="SELECT SUM(S.Price*K.oStockQty) AS Amount,S.Estate 
FROM $DataIn.stuffdata S
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE oStockQty>0 GROUP BY S.Estate";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Estate=$myRow["Estate"];
		$Amount=$myRow["Amount"];
		echo $Estate."-".$Amount."<br>";
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>