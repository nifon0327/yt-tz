<?php 

$searString = trim($info[0]);
$searType = $info[1];
$searConditions = " and StuffCname like '".$searString."%' ";
if ((int)$searType > 0) {
	$searConditions.= " and TypeId=$searType "; 
}

$sqlNames       = mysql_query("select StuffCname from $DataIn.stuffdata 
									where 1 $searConditions order by Id desc  limit 250 
									");

$NameList = array();

while ($sqlNameRow = mysql_fetch_assoc($sqlNames)) {
	

	$StuffCname        = $sqlNameRow["StuffCname"];
	$NameList[] = array("headImage"=>"",
							   "title"    =>"$StuffCname",
							   "Id"       =>"$StuffCname",
							   "CellType" =>"2",
							   "selected" =>"0"
							   ); 
									
	
}

$jsonArray = array(
					 "NewName"=>$NameList
					 );
 
?>