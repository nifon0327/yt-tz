<?php 
//$DataIn.base_mposition 
//二合一已更新
/*
$pResult = mysql_query("SELECT Name,Remark FROM $DataIn.base_mposition WHERE Id=$SendFloor ORDER BY Id LIMIT 1",$link_id);
if($pRow = mysql_fetch_array($pResult)){
	$SendFloor=$pRow["Name"];
}
*/

if ($SYS_FloorArray==""){
   $pResult = mysql_query("SELECT Id,Name,Remark FROM $DataIn.base_mposition WHERE 1 ORDER BY Id",$link_id);
   while($pRow = mysql_fetch_array($pResult)){
        $Floor_Id=$pRow["Id"];
	    $SYS_FloorArray["$Floor_Id"]=$pRow["Name"];
   }
}

if ($SendFloor>0){
   $SendFloor=$SYS_FloorArray["$SendFloor"];	
}

?>