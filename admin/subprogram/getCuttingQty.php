<?php    //统计刀模工序完成数量,ProductId,StuffId,Cutrelation

$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(Y.Qty) AS scQty  
FROM $DataIn.cut_bom A 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.ProductId=A.ProductId 
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
where 1 AND A.Diecut!='' AND A.Cutrelation>0  AND T.mainType=5 AND  A.ProductId='$ProductId' AND A.StuffId='$StuffId' AND A.Diecut='$Diecut' GROUP BY A.ProductId",$link_id));

$scQty=$CheckscQty["scQty"];  
$cutQty=$scQty*$Cutrelation;  //所有刀模工序的总量
/*
$CheckwcQty=mysql_fetch_array(mysql_query("SELECT SUM(Y.Qty) AS wcQty  
FROM $DataIn.pands A 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.ProductId=A.ProductId 
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
where 1 AND A.Diecut!='' AND A.Cutrelation>0  AND T.mainType=5 AND Y.Estate=0  AND  A.ProductId='$ProductId' AND A.StuffId='$StuffId'  GROUP BY A.ProductId",$link_id));
$wcQty=$CheckwcQty["wcQty"];  
$cutedQty=$wcQty*$Cutrelation;  //已完成刀模工序的总量（即已出货的订单统计数量）
*/

$CheckwcQty=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS wcQty  
FROM $DataIn.sc_cuttj S where 1 AND  S.ProductId='$ProductId' AND S.StuffId='$StuffId' AND  S.Diecut='$Diecut' GROUP BY S.StuffId",$link_id));

$wcQty=$CheckwcQty["wcQty"];  
$cutedQty=$wcQty*$Cutrelation;  //已完成刀模工序的总量（登记数量）

$nocutQty=$cutQty-$cutedQty;
?>