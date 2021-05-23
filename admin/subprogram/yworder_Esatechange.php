<?php   
//电信---yang 20120801
$UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
			SET Y.scFrom=0 
			WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty AND Y.scFrom='2' AND Y.Estate>0";
$UpdateResult = mysql_query($UpdateSql);			
?>