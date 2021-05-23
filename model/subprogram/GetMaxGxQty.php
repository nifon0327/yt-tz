<?php
$MaxQty = $OrderQty;
$CheckMaxGxQtyResult=mysql_query("SELECT PD.BassLoss
FROM $DataIn.cg1_processsheet B 
LEFT JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
WHERE B.StockId='$StockId'",$link_id);
while($CheckMaxGxQtyRow = mysql_fetch_array($CheckMaxGxQtyResult)){	
	$MaxBassLoss  =  $CheckMaxGxQtyRow["BassLoss"];
	$MaxQty += floor($OrderQty * $MaxBassLoss);
}

?>