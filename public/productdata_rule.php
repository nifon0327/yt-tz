<?php 
//$DataIn.电信---yang 20120801
include "../basic/parameter.inc";
$TypeId=$_GET["TypeId"];
$result = mysql_query("SELECT NameRule FROM $DataIn.producttype WHERE  TypeId='$TypeId'",$link_id);
if($NameResult=mysql_fetch_array($result)){
   $NameRule=$NameResult["NameRule"];
  }
 echo $NameRule;
?>