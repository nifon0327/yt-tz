<?php
$CheckComboxSql=mysql_query("SELECT G.Id AS Mid ,M.StuffId,M.StockId,M.StockQty,(G.FactualQty+G.AddQty) AS AddStockQty   
FROM  $DataIn.cg1_stuffcombox   M   
LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId =M.mStockId
WHERE    M.mStockId=$StockId",$link_id);
while($CheckComboxRow = mysql_fetch_array($CheckComboxSql)){
         $GMid =  $CheckComboxRow["Mid"]==""?0:$CheckComboxRow["Mid"];
         $ComboxStuffId =  $CheckComboxRow["StuffId"];
         $ComboxStockId =  $CheckComboxRow["StockId"];
         if($GMid==0){
               $UpdateComboxSql ="DELETE  FROM $DataIn.cg1_stuffcombox   WHERE mStockId =$StockId AND StuffId = $ComboxStuffId";
               $ComStockQty=$CheckComboxRow["StockQty"];
           }
        else{
             $UpdateComboxSql = "UPDATE   $DataIn.cg1_stuffcombox  M   
              LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId =M.mStockId
              SET  M.OrderQty=M.Relation*G.OrderQty,M.StockQty=M.Relation*G.StockQty,M.FactualQty=M.Relation*(G.FactualQty+G.AddQty),M.Date = NOW()
               WHERE  G.StuffId=$StuffId AND G.StockId=$StockId";
               $ComStockQty=$CheckComboxRow["AddStockQty"];
               }
              $UpdateComboxResult = @mysql_query($UpdateComboxSql);
            if($UpdateComboxResult ){
               $Log.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;母配件($StuffId)对应的子配件($ComboxStuffId)更新成功. </br>";
               
               //删除子配件领料记录
              /* $uptStockQtyStr="";
               $CheckllQty = mysql_fetch_array(mysql_query("SELECT StuffId,SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE  StockId='$ComboxStockId' group by StockId",$link_id));
	           $ComllQty=$CheckllQty["llQty"];
	            if ($ComllQty!=0){
						 //删除领料记录
						 $delSql="DELETE FROM $DataIn.ck5_llsheet WHERE StockId='$ComboxStockId'";
					     $delResult=mysql_query($delSql);	
						 if($delResult){
						       $Log.="配件需求单( $ComboxStockId )的领料记录已成功删除.<br>";
						       $uptStockQtyStr=",tStockQty=tStockQty+$ComllQty ";
						}
						else{
							 $Log.="<div class=redB>配件需求单( $ComboxStockId )的领料记录删除失败.</div><br>";
						}
			   }*/

               //更新子配件订单库存
               //$UpdateoStockQtySql="UPDATE $DataIn.ck9_stocksheet SET oStockQty=oStockQty+$ComStockQty $uptStockQtyStr WHERE StuffId='$ComboxStuffId'";
               //$UpdateoStockQtyResult = @mysql_query($UpdateoStockQtySql);
           }
           else{
                 $Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;母配件($StuffId)对应的子配件($ComboxStuffId)更新失败. $UpdateComboxSql </div></br>";
                 }
}
?>