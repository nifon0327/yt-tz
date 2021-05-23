<?php   
$mySql="SELECT * FROM (
  SELECT 
  S.Id,S.POrderId,M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.ProductId,S.Qty,S.Price,
  S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,U.Name AS Unit,PI.PI,PI.Leadtime AS DeliveryDate,
  SUM(if(K.tStockQty>=(G.OrderQty- IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
  SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,CD.PreChar,PI.Remark AS PIRemark 
  FROM $DataIn.yw1_ordermain M
  LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
  LEFT JOIN $DataPublic.currencydata  CD ON CD.Id=C.Currency
  LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
  LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
  LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
  LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
  LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
  LEFT JOIN (
             SELECT L.StockId,SUM(L.Qty) AS Qty FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
             WHERE 1  AND S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
         ) L ON L.StockId=G.StockId
  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
  LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
  WHERE 1 and  S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  $SearchRows  GROUP BY S.POrderId 
) A 
WHERE A.K1>=A.K2  AND A.blQty=A.llQty  ORDER BY A.OrderDate ASC,A.Id DESC";
?>