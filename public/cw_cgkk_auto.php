<?php
$KKDate=date("Y-m-d");
        //非BOM配件模具类，下单数量达到一定数量时，做供应商扣款操作
     $ChecknonBomResult=mysql_query("SELECT   B.CompanyId,D.GetQty,D.GoodsId,D.Price,D.GoodsName  FROM $DataPublic.nonbom4_goodsdata  D  
     LEFT JOIN $DataPublic.nonbom4_bomcompany B  ON B.GoodsId=D.GoodsId
     LEFT JOIN $DataIn.cw15_gyskksheet  G ON G.GoodsId=D.GoodsId  AND G.GoodsId>0
     WHERE  B.cSign=$Login_cSign AND D.GetQty>0 AND G.Id IS  NULL ",$link_id);
     while($ChecknonBomRow=mysql_fetch_array($ChecknonBomResult)){
            $nonBomCompanyId=$ChecknonBomRow["CompanyId"];
            $nonBomGetQty=$ChecknonBomRow["GetQty"];
            $nonBomGoodsId=$ChecknonBomRow["GoodsId"];
            $nonBomPrice=$ChecknonBomRow["Price"];
            $nonBomGoodsName=$ChecknonBomRow["GoodsName"];
          $CheckDieResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty  FROM ( 
           SELECT Y.Qty FROM $DataIn.cut_die  D   
           LEFT JOIN $DataIn.yw1_ordersheet  Y ON Y.ProductId=D.ProductId WHERE D.GoodsId=$nonBomGoodsId
 )A WHERE 1",$link_id));
           $ProductOrderQty=$CheckDieResult["Qty"];
           if($ProductOrderQty>=$nonBomGetQty){
               $MaxValues=1;
               $CheckMaxBillNumber=mysql_query("SELECT  BillNumber  FROM $DataIn.cw15_gyskkmain WHERE 1  ORDER BY Date DESC LIMIT 0,100",$link_id);
               while($CheckMaxRow=mysql_fetch_array($CheckMaxBillNumber)){
                       $nowBillNumber=$CheckMaxRow["BillNumber"];
                        $nowBillTemp=substr($nowBillNumber,11);
                       if($nowBillTemp>$MaxValues)$MaxValues=$nowBillTemp;
               }
            $MaxValues=$MaxValues+1;
            $MaxBillNumber="Debit note ".$MaxValues;
              $inRecode="INSERT INTO $DataIn.cw15_gyskkmain (Id,BillNumber,CompanyId,Date,TotalAmount,BillFile,Picture,Remark, Estate,Locks,Operator) VALUES (NULL,'$MaxBillNumber','$nonBomCompanyId','$KKDate','0','1','0','因模具$nonBomGoodsName 的下单数量:$ProductOrderQty 达到款项收货条件:$nonBomGetQty 做的扣款','1','0','$Login_P_Number')";
              $inAction=mysql_query($inRecode);
              $Mid=mysql_insert_id();
              if($inAction && mysql_affected_rows()>0){
	                $addRecodes="INSERT INTO $DataIn.cw15_gyskksheet (Id, Mid, PurchaseID, StockId, StuffId, StuffName,Qty, Price, Amount,Remark,GoodsId,Kid) VALUES (NULL,'$Mid','0','0','0','$nonBomGoodsName','1','$nonBomPrice','$nonBomPrice','因模具$nonBomGoodsName 的下单数量:$ProductOrderQty 达到款项收货条件:$nonBomGetQty 做的扣款','$nonBomGoodsId','0')";
	              $addAction=mysql_query($addRecodes);
                    if($addAction){
                             $UpdateSql="UPDATE  $DataIn.cw15_gyskkmain SET TotalAmount='$nonBomPrice'  WHERE Id=$Mid"; $UpdateResult=@mysql_query($UpdateSql);
                          }
                     }
              }
       }
?>