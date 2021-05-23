<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TempDate=date("Ym");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
   $CheckMaxResult=mysql_fetch_array(mysql_query("SELECT Max(BillNumber)  AS MaxBillNumber  FROM  $DataIn.nonbom8_bf WHERE BillNumber LIKE '$TempDate%'",$link_id));
$MaxBillNumber=$CheckMaxResult["MaxBillNumber"];
if($MaxBillNumber=="")$MaxBillNumber=$TempDate."0001";
else $MaxBillNumber=$MaxBillNumber+1;
 $CheckQtyResult=mysql_fetch_array(mysql_query("SELECT  GoodsId, Qty ,PdNumber FROM $DataIn.nonbom8_pd WHERE Id=$Id",$link_id));
 $GoodsId=$CheckQtyResult["GoodsId"];
 $Qty=$CheckQtyResult["Qty"];
 $PdNumber=$CheckQtyResult["PdNumber"];
if($Qty>0){
                $IN_Sql="INSERT INTO $DataIn.nonbom8_bf(Id,BillNumber,GoodsId,Qty,Remark,bfNumber,Picture,Estate,Locks,Date,Operator)
                VALUES(NULL,'$MaxBillNumber','$GoodsId','$Qty','盘点后报废','$PdNumber','','1','0','$DateTime','$Operator')";
                $IN_recode=@mysql_query($IN_Sql);
                $BfId=mysql_insert_id();
                 if($IN_recode && mysql_affected_rows()>0){
                        $IN_Sql1="INSERT INTO $DataIn.nonbom8_bffixed  SELECT  NULL,'$BfId',GoodsId,BarCode  FROM $DataIn.nonbom8_pdfixed WHERE PdId=$Id"; 
                        $IN_recode1=@mysql_query($IN_Sql1);

                        $UpdateSql=" UPDATE  $DataPublic.nonbom5_goodsstock  SET lStockQty=lStockQty-$Qty WHERE GoodsId=$GoodsId AND lStockQty>=$Qty";
                        $UpdateResult=@mysql_query($UpdateSql);

                         $UpdateSql1=" UPDATE  $DataIn.nonbom8_pd  SET Estate=1 WHERE Id=$Id";
                         $UpdateResult1=@mysql_query($UpdateSql1);
                }
}













?>