<?php
if($TypeId==8059 && $TypeId!=""){
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$CheckResult=mysql_query("SELECT SUM(IFNULL(G.OrderQty,0)) AS  OrderQty ,SUM(IFNULL(G.StockQty,0)) AS StockQty,K.tStockQty ,SUM(IFNULL(L.Qty,0)) AS llQty,G.StuffId,G.StockId
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=G.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
WHERE G.POrderId='$POrderId' AND T.mainType<2  GROUP BY G.StockId",$link_id);
if($CheckRow=mysql_fetch_array($CheckResult)){
   do{
            $thisStuffId=$CheckRow["StuffId"];
            $thisStockId=$CheckRow["StockId"];
            $thisOrderQty=$CheckRow["OrderQty"];
            $thisStockQty=$CheckRow["StockQty"];
            $thistStockQty=$CheckRow["tStockQty"];
            $thisllQty=$CheckRow["llQty"];
            $thisQty=$thisOrderQty-$thisllQty;
            if($thisllQty<$thisOrderQty && $thistStockQty>$thisQty){
                        $blinRecode="INSERT INTO $DataIn.yw9_blmain (Id,Estate,Locks,Date,Operator) VALUES (NULL,'1','0','$DateTime','$Operator')";
                        $blinAction=@mysql_query($blinRecode);
                        $Pid=mysql_insert_id();
                        $llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','0','$thisStockId','$thisStuffId','$thisQty','0','1')";
                        $llInAction=@mysql_query($llInSql);
                        if($llInAction&& mysql_affected_rows()>0){
                                           $UpdateSql="UPDATE $DataIn.yw1_ordersheet SET scFrom=0,Estate=2 WHERE POrderId=$POrderId";
                                           $UpdateResult=@mysql_query($UpdateSql);
                                    }         
                            $UpdateSql="UPDATE $DataIn.yw1_ordersheet SET scFrom=0,Estate=2 WHERE POrderId=$POrderId";
                            $UpdateResult=@mysql_query($UpdateSql);
                    }
             if($thisllQty==$thisOrderQty){
                            $UpdateSql="UPDATE $DataIn.yw1_ordersheet SET scFrom=0,Estate=2 WHERE POrderId=$POrderId";
                            $UpdateResult=@mysql_query($UpdateSql);
                        }
          }while($CheckRow=mysql_fetch_array($CheckResult));
      }
}
?>