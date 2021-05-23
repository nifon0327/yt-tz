<?php 
//检查是否订单中最后一个需备料的配件
//include "../../basic/parameter.inc";
$LastBgColor="";$LastBlSign=0;

if ($FromPageName=="sh"){
	   $isLastStockSql = "SELECT COUNT(*) AS Counts,SUM(IF(K.tStockQty+IFNULL(S.Qty,0)>=G.OrderQty,1,0)) AS Nums,SUM(IF(D.StuffId='$StuffId',1,0)) AS lastSign 
										   FROM $DataIn.yw1_ordersheet Y 
										   LEFT JOIN $DataIn.cg1_stocksheet G  ON Y.POrderId=G.POrderId 
										   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
										   LEFT JOIN $DataIn.gys_shsheet S ON S.StockId=G.StockId AND S.Estate>0 
										   WHERE Y.Estate>0 AND G.POrderId = '$POrderId'
										   AND T.mainType in (1,0)
										   AND K.tStockQty < G.OrderQty";
						$isLastStockResult = mysql_query($isLastStockSql);
						if(mysql_num_rows($isLastStockResult) == 1)
						{
							$lastStockRow = mysql_fetch_assoc($isLastStockResult);
							if($lastStockRow["lastSign"]>0 && $lastStockRow["Nums"]==$lastStockRow["Counts"])
							{
							    $LastBlSign=$lastStockRow["Nums"];
								$LastBgColor =$LastBlSign>1?"#CCFFFF":"#C3FF64";//B0FF8E#CFFFA0
							}
				    }
}
else{
		 $checkOrderEstate=mysql_fetch_array(mysql_query("SELECT Estate FROM $DataIn.yw1_ordersheet WHERE POrderId='$POrderId' AND Estate>0",$link_id));
		 if ($checkOrderEstate["Estate"]>0){	
						$isLastStockSql = "SELECT G.StockId, D.StuffId
										   FROM $DataIn.cg1_stocksheet G  
										   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
										   WHERE G.POrderId = '$POrderId'
										   AND T.mainType in (1,0)
										   AND K.tStockQty < G.OrderQty";
						$isLastStockResult = mysql_query($isLastStockSql);
						if(mysql_num_rows($isLastStockResult) == 1)
						{
							$lastStockRow = mysql_fetch_assoc($isLastStockResult);
							$lastStuffId = $lastStockRow["StuffId"];
							if($lastStuffId == $StuffId)
							{
							    $LastBlSign=1;
								$LastBgColor = "#C3FF64";//B0FF8E#CFFFA0
							}
				    }
		     }
}
?>