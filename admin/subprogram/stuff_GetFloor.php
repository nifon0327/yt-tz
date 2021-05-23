<?php   
//$DataIn.base_mposition 电信---yang 20120801
//二合一已更新
$pResult = mysql_query("SELECT Remark FROM $DataIn.base_mposition WHERE Id=$SendFloor ORDER BY Id LIMIT 1",$link_id);
if($pRow = mysql_fetch_array($pResult)){
	$SendFloor=$pRow["Remark"];
	}
?>