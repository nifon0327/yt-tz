<?php 
//µçÐÅ-EWEN
include "../basic/parameter.inc";
$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.staffmain ORDER BY Number DESC",$link_id));
$tempMaxnumber=$checkNumRow["Number"];
echo "|$tempMaxnumber|";

?>

