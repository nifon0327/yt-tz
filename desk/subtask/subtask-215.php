<?php   
//已送料配件 yang  全检
$Q_Result215=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS shQty
FROM $DataIn.gys_shsheet S 
LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0 
WHERE 1 AND S.Estate=2  AND D.CheckSign=1 AND GL.StockId IS NULL",$link_id));
 //LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=SUBSTRING(S.StockId,1,12) AND E.Type=2  
$Q_Qty=$Q_Result215["shQty"]==""?0:round($Q_Result215["shQty"]/1000,0);
$iPhone_Qty215=$Q_Result215["shQty"];
$tmpTitle="<font color='red'>$Q_Qty"."k</font>";
?>