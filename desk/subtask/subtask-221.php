<?php   
//未下单禁用配件 zhongxq-2012/11/08

$Result221=mysql_fetch_array(mysql_query("SELECT  count(*) as nums
FROM $DataIn.stuffdata S 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId  
LEFT JOIN (
			           SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,G.StuffId 
			           FROM $DataIn.ch1_shipmain M 
		               LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
		               LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                      WHERE 1 GROUP BY G.StuffId ORDER BY M.Date DESC
					) E ON E.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffdisablenot  SD ON SD.StuffId=S.StuffId
WHERE 1  and S.Estate=1 AND T.mainType<=1  AND E.Months>11 AND E.Months IS NOT NULL  AND SD.StuffId IS NULL",$link_id));

$temp_C221=$Result221["nums"]==""?0:$Result221["nums"];
$tmpTitle="<font color='red'>" .$temp_C221."</font>";
?>