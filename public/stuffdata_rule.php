<?php 
//$DataIn.电信---yang 20120801
include "../basic/parameter.inc";
$TypeId=$_GET["TypeId"];
$NameRule="";
$result = mysql_query("SELECT T.NameRule,T.Position,M.CheckSign,T.BuyerId,T.SeatId  FROM $DataIn.stufftype T 
LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position
LEFT JOIN $DataIn.wms_seat W ON W.ZoneName=T.SeatId
WHERE  T.TypeId='$TypeId' LIMIT 1",$link_id);
if($NameResult=mysql_fetch_array($result)){
   $NameRule=$NameResult["NameRule"];
   $Position=$NameResult["Position"];
   $CheckSign=$NameResult["CheckSign"];
   $BuyerId=$NameResult["BuyerId"];
   $SeatIds = $NameResult["SeatId"];
   if ($Position>0) $NameRule.="|" . $Position . "|" . $CheckSign . "|" . $BuyerId . "|" . $SeatIds;
  }
 echo $NameRule;
?>