<?php
 if($StuffCname!=""){
   $checkOldNameResult = mysql_fetch_array(mysql_query("SELECT StuffId,StuffCname FROM $DataIn.stuffdata_old  WHERE  StuffCname ='$StuffCname'",$link_id));
   $oldStuffCname = $checkOldNameResult["StuffCname"];
   $oldStuffId = $checkOldNameResult["StuffId"];
   if($oldStuffId>0){
	  $StuffCname.="<span class='blueB'>(". $oldStuffId.")</span>";
   }

}
?>