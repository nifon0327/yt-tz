<?php    //统计刀模工序完成数量,ProductId,StuffId,Diecut

$CheckwcQty=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Relation) AS wcQty  
FROM $DataIn.sc5_cuttj S where 1 AND  S.POrderId='$POrderId' AND S.ProductId='$ProductId' AND S.StuffId='$StuffId' AND  S.Diecut='$Diecut'  GROUP BY S.StuffId",$link_id));
$cutedQty=$CheckwcQty["wcQty"]; 
  
?>