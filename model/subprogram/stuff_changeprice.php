<?php 
$StockId=$myRow["StockId"];
$CheckCgQty="&nbsp;";$newPrice=0;
$ChangeResult=mysql_query("SELECT  A.LowQty,A.HighQty,A.Price 
 FROM $DataIn.stuff_cgprice  C 
LEFT JOIN $DataPublic.stuff_cgadjust A ON A.Id=C.adjustId
WHERE StuffId=$StuffId",$link_id);
if($ChangeRow=mysql_fetch_array($ChangeResult)){
            $CheckCgQtyResult=mysql_fetch_array(mysql_query("SELECT  SUM(AddQty+FactualQty) AS Qty FROM $DataIn.cg1_stocksheet WHERE StuffId=$StuffId AND Mid=0 AND (FactualQty>0 OR AddQty>0) ",$link_id));
            $CheckCgQty=$CheckCgQtyResult["Qty"];
             do{
                     $CheckLowQty=$ChangeRow["LowQty"];
                     $CheckHighQty=$ChangeRow["HighQty"];
                     $CheckPrice=$ChangeRow["Price"];
                      if($CheckCgQty>$CheckLowQty && $CheckCgQty<=$CheckHighQty){
                               $newPrice=$CheckPrice;break;
                           }
                  }while($ChangeRow=mysql_fetch_array($ChangeResult));
             $CheckCgQty="<a href='stuff_cgprice_read.php?fromStuffId=$StuffId' target='_blank'>$CheckCgQtyResult[Qty]</a>";
     }
if($newPrice>0 && $Price!=$newPrice){
              $UpdateSql="UPDATE  $DataIn.cg1_stocksheet SET Price=$newPrice, Estate=1, AddRemark=CONCAT(AddRemark,'应达到一定采购数量 $CheckLowQty 变更采购单价') WHERE StockId=$StockId";
               $UpdateResult=@mysql_query($UpdateSql);
          }
?>