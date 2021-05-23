<?php 
//ewen 2013-03-07
include "../basic/parameter.inc";
$TypeId=$_GET["TypeId"];
$result = mysql_query("SELECT mainType,GetSign,NameRule ,Remark FROM $DataPublic.nonbom2_subtype WHERE Id='$TypeId'",$link_id);
if($NameResult=mysql_fetch_array($result)){
   $NameRule=$NameResult["NameRule"];
   $mainType=$NameResult["mainType"];
   $GetSign=$NameResult["GetSign"];
   $Remark=$NameResult["Remark"];
  }
 echo $NameRule."|".$mainType."|".$GetSign."|".$Remark;
?>