<?php   
//EWEN 2012-10-05 已备料订单数
/*
$Result213=mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS blQty FROM (
	SELECT 
	S.POrderId,S.ProductId,S.Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,
	SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
	SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,SUM(IFNULL(L.llEstate,0)) AS llEstate 
	FROM d7.yw1_ordermain M
	LEFT JOIN d7.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN d7.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN d7.ck9_stocksheet K ON K.StuffId=G.StuffId
	LEFT JOIN d7.stuffdata D ON D.StuffId=G.StuffId 
	LEFT JOIN d7.stufftype ST ON ST.TypeId=D.TypeId
	LEFT JOIN (
				 SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate  FROM d7.yw1_ordersheet S 
				 LEFT JOIN d7.cg1_stocksheet G ON S.POrderId=G.POrderId
				 LEFT JOIN d7.ck5_llsheet L ON G.StockId=L.StockId 
				 WHERE S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
			 ) L ON L.StockId=G.StockId
	WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  
	              AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
	GROUP BY S.POrderId 
	) A 
WHERE A.K1>=A.K2 AND A.blQty=A.llQty ",$link_id));//AND A.llEstate=0 
*/
$Result213=mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS blQty FROM (
          SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty FROM (      
             SELECT 
						S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty 
                        FROM $DataIn.yw1_ordermain M
						INNER JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
						INNER JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                        INNER JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
                         LEFT JOIN $DataIn.stuffproperty T  ON T.StuffId=G.StuffId AND  T.Property='8' 
                        WHERE 1 AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND T.StuffId IS NULL 
                        GROUP BY G.StockId 
               )S0 GROUP BY S0.POrderId 
)A WHERE A.blQty=A.llQty   AND EXISTS (
  SELECT ST.mainType 
       FROM $DataIn.cg1_stocksheet G 
       LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
       LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
       WHERE G.POrderId=A.POrderId AND ST.mainType=3)
",$link_id));

$iPhone_C213=$Result213["blQty"];
$temp_C213=$Result213["blQty"]==""?0:round($Result213["blQty"]/1000,0);
$tmpTitle="<font color='red'>$temp_C213"."k</font>";
?>