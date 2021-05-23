<?php   
//配件图档初审记录
$Result200=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Qty
FROM $DataIn.stuffdata S 
LEFT JOIN $DataIn.stuffverify V ON V.Mid=S.Id WHERE 1 AND V.Mid IS NULL AND (S.Gstate=2 or S.Gstate=6)",$link_id));
$temp_C200=$Result200["Qty"]==""?0:$Result200["Qty"];
$tmpTitle="<font color='red'>".$temp_C200."</font>";
?>