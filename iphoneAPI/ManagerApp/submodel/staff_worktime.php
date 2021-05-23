<?php
//默认公司上班时间
$defaultState=1;
$weeks=date("w");
$defaultState=($weeks==0 || $weeks==6)?4:$defaultState;
$sbTime=date("Y-m-d") . " 08:00:00"; $xbTime=date("Y-m-d") . " 17:30:00";
$checkHoliday=mysql_query("SELECT * FROM $DataPublic.kqholiday  K  WHERE K.Date=CURDATE() OR NOW()<'$sbTime' OR NOW()>'$xbTime' LIMIT 1",$link_id);
$defaultState=mysql_num_rows($checkHoliday)==1?4:$defaultState;

?>