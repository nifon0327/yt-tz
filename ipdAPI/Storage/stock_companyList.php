<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$ClientResult= mysql_query("SELECT Forshort,CompanyId FROM ( 
									SELECT M.CompanyId,C.Forshort,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 ,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty
									FROM  (SELECT OrderNumber,POrderId,ProductId FROM $DataIn.yw1_ordersheet WHERE  Estate>0) S 
									LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
									LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
									LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
									LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
									LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
									LEFT JOIN ( 
										SELECT L.StockId,SUM(L.Qty) AS Qty 
										FROM $DataIn.yw1_ordersheet S 
										LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId 
										LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
										WHERE 1 AND S.Estate>0 AND L.StockId IS NOT NULL  GROUP BY L.StockId
										) L ON L.StockId=G.StockId 
									WHERE 1  AND ST.mainType<2 GROUP BY S.POrderId) A 
								WHERE A.K1>=A.K2 
								AND A.blQty!=A.llQty 
								Group by A.CompanyId" ,$link_id);
									
	$companyList = array();
	while($clientRow = mysql_fetch_assoc($ClientResult))
	{
		$forShort = $clientRow["Forshort"];
		$companyId = $clientRow["CompanyId"];
		
		$companyList[] = array("$forShort", "$companyId");
	}
	
	echo json_encode($companyList);
	
?>