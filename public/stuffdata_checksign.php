<?php 
//$DataIn.电信---yang 20120801
include "../basic/parameter.inc";
$FloorId=$_GET["FloorId"];

$CheckSign="";
$result = mysql_query("SELECT CheckSign FROM $DataIn.base_mposition WHERE Id='$FloorId' ",$link_id);
if($row=mysql_fetch_array($result)){
   $CheckSign=$row["CheckSign"];
}
echo $CheckSign;
?>