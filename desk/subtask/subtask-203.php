<?php   
/*
功能：统计未
*/
$Today=date("Y-m-d");
$Result203=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS unFormalNumber
FROM $DataPublic.staffmain M
WHERE 1 AND M.Estate=1 AND M.FormalSign=2  AND M.cSign='$Login_cSign' AND datediff(DATE_ADD(M.ComeIn,INTERVAL 4 MONTH),'$Today')<='10'",$link_id));
/*echo "SELECT COUNT(*) AS unFormalNumber
FROM $DataPublic.staffmain M
WHERE 1 AND M.Estate=1 AND M.FormalSign=2 AND M.cSign='$Login_cSign' AND datediff(DATE_ADD(M.ComeIn,INTERVAL 2 MONTH),'$Today')<='10'";*/
$temp_C203=$Result203['unFormalNumber']==""?"0人":$Result203['unFormalNumber']."人";
$tmpTitle="<font color='red'>$temp_C203</font>";
?> 