<?php   
//检讨报告
$sum_Result229=mysql_fetch_array(mysql_query("SELECT count(*) AS Qty FROM $DataIn.errorcasedata WHERE Estate=1",$link_id));
$sumQty=$sum_Result229["Qty"];
$tmpTitle="<font color='red'>$sumQty</font>";
?>